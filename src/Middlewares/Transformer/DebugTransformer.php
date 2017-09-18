<?php

namespace Apitte\Core\Middlewares\Transformer;

use Apitte\Core\Exception\Logical\InvalidStateException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Tracy\Debugger;
use function GuzzleHttp\Psr7\stream_for;

class DebugTransformer implements ITransformer
{

	/** @var int */
	private $maxDepth;

	/** @var int */
	private $maxLength;

	/**
	 * @param int $maxDepth
	 * @param int $maxLength
	 */
	public function __construct($maxDepth = 10, $maxLength = 1500)
	{
		$this->maxDepth = $maxDepth;
		$this->maxLength = $maxLength;
	}

	/**
	 * @param ApiResponse $response
	 * @param array $options
	 * @return ApiResponse
	 */
	public function encode(ApiResponse $response, array $options = [])
	{
		Debugger::$maxDepth = $this->maxDepth;
		Debugger::$maxLength = $this->maxLength;

		$tmp = clone $response;

		if (!$response->hasData()) {
			$response = $response->withHeader('Content-Type', 'text/html')
				->withBody(stream_for())
				->writeBody(Debugger::dump($tmp, TRUE), TRUE);
		} else {
			$response = $response->writeBody(Debugger::dump($tmp, TRUE), TRUE);
		}

		return $response;
	}

	/**
	 * @param ApiRequest $request
	 * @param array $options
	 * @return void
	 */
	public function decode(ApiRequest $request, array $options = [])
	{
		throw new InvalidStateException('No decode mode');
	}

}
