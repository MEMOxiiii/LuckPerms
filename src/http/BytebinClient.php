<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\http;

use pocketmine\utils\InternetException;
use function assert;
use function basename;
use function curl_close;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function explode;
use function gzdecode;
use function is_array;
use function is_string;
use function json_decode;
use function ltrim;
use function str_ends_with;
use function stripos;
use function strlen;
use function substr;
use function trim;
use const CURLINFO_HEADER_SIZE;
use const CURLINFO_HTTP_CODE;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HEADER;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_TIMEOUT;
use const JSON_OBJECT_AS_ARRAY;
use const JSON_THROW_ON_ERROR;

class BytebinClient extends AbstractHttpClient{

	private string $url;

	public function __construct(string $url, private string $userAgent){
		$this->url = str_ends_with($url, "/") ? $url : $url . '/';
	}

	public function getUrl() : string{
		return $this->url;
	}

	public function getUserAgent() : string{
		return $this->userAgent;
	}

	/**
	 * POSTs GZIP compressed content to bytebin.
	 *
	 * @param string      $buffer         the compressed content
	 * @param string      $contentType    the type of the content
	 * @param string|null $extraUserAgent extra string to append to the user agent
	 * @return Content the key of the resultant content
	 */
	public function postContent(string $buffer, string $contentType, ?string $extraUserAgent = null) : Content {
		$userAgent = $this->userAgent . ($extraUserAgent !== null ? "/$extraUserAgent" : "");

		// Use curl directly to avoid PocketMine's Internet class quirks
		// (duplicate User-Agent header, FOLLOWLOCATION on POST, etc.)
		$ch = curl_init($this->url . 'post');
		if($ch === false){
			throw new InternetException('Failed to initialise cURL');
		}
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $buffer);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"User-Agent: $userAgent",
			"Content-Type: $contentType",
			"Content-Encoding: gzip",
		]);

		$raw = curl_exec($ch);
		if($raw === false){
			$err = curl_error($ch);
			curl_close($ch);
			throw new InternetException('Bytebin POST request failed: ' . $err);
		}
		$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		assert(is_string($raw));
		$rawHeaders = substr($raw, 0, $headerSize);
		$body = substr($raw, $headerSize);
		curl_close($ch);

		// Parse Location header from raw response headers
		foreach(explode("\r\n", $rawHeaders) as $line){
			if(stripos($line, 'location:') === 0){
				$key = basename(trim(substr($line, 9)));
				if($key !== '') return new Content($key);
			}
		}

		// Fallback: bytebin may return {"key":"..."} or {"url":"..."} in body
		try{
			$json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
			if(is_array($json)){
				if(isset($json['key']) && is_string($json['key']) && $json['key'] !== ''){
					return new Content($json['key']);
				}
				if(isset($json['url']) && is_string($json['url'])){
					$key = basename($json['url']);
					if($key !== '') return new Content($key);
				}
			}
		}catch(\Throwable){}

		throw new InternetException("Bytebin did not return a content key. HTTP=$httpCode Body=" . substr($body, 0, 200));
	}

	/**
	 * PATCHes (updates in-place) existing bytebin content at the given key.
	 * This keeps the same URL/key so the editor session is refreshed without
	 * generating a new link.
	 *
	 * @param string      $key            the existing content key
	 * @param string      $buffer         the GZIP-compressed new content
	 * @param string      $contentType    the MIME type
	 * @param string|null $extraUserAgent extra string to append to the user agent
	 */
	public function patchContent(string $key, string $buffer, string $contentType, ?string $extraUserAgent = null) : void{
		$userAgent = $this->userAgent . ($extraUserAgent !== null ? "/$extraUserAgent" : "");

		$ch = curl_init($this->url . ltrim($key, '/'));
		if($ch === false){
			throw new InternetException('Failed to initialise cURL');
		}
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $buffer);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"User-Agent: $userAgent",
			"Content-Type: $contentType",
			"Content-Encoding: gzip",
		]);

		$raw = curl_exec($ch);
		if($raw === false){
			$err = curl_error($ch);
			curl_close($ch);
			throw new InternetException('Bytebin PATCH request failed: ' . $err);
		}
		$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// 2xx = success; anything else is an error
		if($httpCode < 200 || $httpCode >= 300){
			throw new InternetException("Bytebin PATCH returned unexpected HTTP $httpCode for key $key");
		}
	}

	/**
	 * GETs json content from bytebin
	 *
	 * @param string $id the id of the content
	 *
	 * @return mixed[] the data
	 * @throws \JsonException
	 */
	public function getJsonContent(string $id) : array {
		$ch = curl_init($this->url . ltrim($id, '/'));
		if($ch === false) throw new InternetException('Failed to initialise cURL');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: {$this->userAgent}"]);
		$raw = curl_exec($ch);
		if($raw === false){
			$err = curl_error($ch);
			curl_close($ch);
			throw new InternetException('Bytebin GET failed: ' . $err);
		}
		curl_close($ch);
		assert(is_string($raw));
		// auto-decompress gzip (bytebin stores content as-is when uploaded with Content-Encoding: gzip)
		if(strlen($raw) > 2 && $raw[0] === "\x1f" && $raw[1] === "\x8b"){
			$decompressed = @gzdecode($raw);
			if($decompressed !== false) $raw = $decompressed;
		}
		return json_decode($raw, true, 512, JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);
	}
}
