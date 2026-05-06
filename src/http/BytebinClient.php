<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\http;

use pocketmine\utils\Internet;
use pocketmine\utils\InternetException;
use function basename;
use function curl_close;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function explode;
use function json_decode;
use function ltrim;
use function str_ends_with;
use function stripos;
use function substr;
use function trim;
use const CURLINFO_HEADER_SIZE;
use const CURLINFO_HTTP_CODE;
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
			if(isset($json['key']) && is_string($json['key']) && $json['key'] !== ''){
				return new Content($json['key']);
			}
			if(isset($json['url']) && is_string($json['url'])){
				$key = basename($json['url']);
				if($key !== '') return new Content($key);
			}
		}catch(\Throwable){}

		throw new InternetException("Bytebin did not return a content key. HTTP=$httpCode Body=" . substr($body, 0, 200));
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
		$response = Internet::simpleCurl($this->url . ltrim($id, '/'), 10, ["User-Agent: {$this->userAgent}"]);
		return json_decode($response->getBody(), true, flags: JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);
	}
}
