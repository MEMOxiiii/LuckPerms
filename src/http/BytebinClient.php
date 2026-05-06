<?php

declare(strict_types=1);

namespace jasonw4331\LuckPerms\http;

use pocketmine\utils\Internet;
use pocketmine\utils\InternetException;
use pocketmine\utils\InternetRequestResult;
use function array_key_exists;
use function array_reverse;
use function basename;
use function json_decode;
use function ltrim;
use function str_ends_with;
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
		$headers = [
			"User-Agent: {$userAgent}",
			"Content-Type: {$contentType}",
			"Content-Encoding: gzip",
		];

		$err = null;
		$response = Internet::postURL($this->url . 'post', $buffer, 10, $headers, $err);
		if($response === null){
			throw new InternetException('Bytebin POST request failed: ' . (string) $err);
		}

		$key = $this->extractContentKey($response);
		if($key === null){
			throw new InternetException('Bytebin did not return a content key in the Location header');
		}
		return new Content($key);
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

	private function extractContentKey(InternetRequestResult $response) : ?string{
		foreach(array_reverse($response->getHeaders()) as $headerGroup){
			if(array_key_exists('location', $headerGroup)){
				$location = $headerGroup['location'];
				$location = basename($location);
				if($location !== ''){
					return $location;
				}
			}
		}
		// Fallback: bytebin may return the key in the JSON body as {"key":"..."}
		try{
			$body = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
			if(isset($body['key']) && is_string($body['key']) && $body['key'] !== ''){
				return $body['key'];
			}
		}catch(\Throwable){}

		return null;
	}
}
