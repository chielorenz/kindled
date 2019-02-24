<?php

namespace App\Service;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Wrapper for Mercury 
 * @link https://mercury.postlight.com/web-parser/
 */
class Mercury
{
	/**
	 * @var string
	 */
	private $key;

    /**
     * @var Guzzle
     */
    private $guzzle;

    /**
     * @param string Mercure api key
     * @param Guzzle
     */
    public function __construct(string $key, Guzzle $guzzle)
    {
    	$this->key = $key;
    	$this->guzzle = $guzzle;
    }

    /**
     * Parse an url and get the Mercury analized array
     * 
     * @param string  $url
     * @return array  Mercury object
     */
    public function parse(string $url) : array 
    {	
    	$res = $this->guzzle->request(
			'get',
			'https://kindled.mobi/mercury',
			[
                'headers' => ['x-api-key' => $this->key, 'Content-Type' => 'application/json'],
                'query' => [ 'url' => $url],
    	    ]
    	);

        return json_decode($res->getBody()->getContents(), true);
    }
}