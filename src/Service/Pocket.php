<?php

namespace App\Service;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * This class is a wrapper of the Pocket api. It lets you authenticate and retrive the list 
 * of articles.
 */
class Pocket
{
	const REQUEST_TOKEN = 'requestToken';
	const ACCESS_TOKEN = 'accessToken';
	
	/**
	 * @var string
	 */
	private $key;

	/**
	 * @var Guzzle
	 */
	private $guzzle;

	/**
	 * @param string  $key  Pocket consumer key
	 * @param Guzzle
	 */
	public function __construct($key, Guzzle $guzzle)
	{
		$this->key = $key;
		$this->guzzle = $guzzle;
	}

	/**
	 * Get a request token from Pocket api, store it via the 'persist' method and return the url 
	 * where to redirect the user to ask for authorization 
	 *
	 * @param string  $redirect  The url to redirect to after the Pocket authentication
	 * @return string  Url to redirect to
	 */
	public function authorize($redirect) : string
	{
     	$requestToken = $this->getRequestToken($redirect);
     	$this->persist($this::REQUEST_TOKEN, $requestToken);
        return $this->getRedirectUrl($requestToken, $redirect);
	}

	/**
	 * Get an access token from Pocket api and store it via the 'persist' method
	 */
	public function authorized() 
	{
		$requestToken = $this->get($this::REQUEST_TOKEN);
		$accessToken = $this->getAccessToken($requestToken);
		$this->persist($this::ACCESS_TOKEN, $accessToken);	
	}
	
	/**
	 * Get Pocket articles via the access token
	 * 
	 * @return array  List of Pocket articles
	 */
	public function articles() : array
	{
		$accessToken = $this->get($this::ACCESS_TOKEN);
		return $this->getArticles($accessToken);
	}

	/**
	 * Delete Pocket tokens
	 */
	public function logout()
	{
		$this->persist(Pocket::REQUEST_TOKEN, null);
        $this->persist(Pocket::ACCESS_TOKEN, null);
	}

	/**
	 * Get Pocket articles
	 * 
	 * @param  string  $accessToken
	 * @return array
	 */
	private function getArticles($accessToken) : array
	{
		try {
			$res = $this->guzzle->request(
				'post',
				'https://getpocket.com/v3/get',
				[
	                'headers' => $this->getHeaders(),
	                'form_params' => [
	                    'consumer_key' => $this->key,
	                    'access_token' => $accessToken,
	                    'state' => 'all',
	                    'sort' => 'newest',
	                    'contentType' => 'article',
	                    'count' => 60,
	                ],
        	    ]
        	);
		}  catch (GuzzleException $e) {
            throw new \Exception(implode(',', $e->getResponse()->getHeaders()['X-Error']));
        }

        $body = json_decode($res->getBody(), true);
        return $body['list'];
	}

	/**
	 * Get request token via the Pocket api
	 * 
	 * @return string  Pocket request token
	 */
	private function getRequestToken($redirect) : string
	{
		try {
            $res = $this->guzzle->request(
            	'post', 
            	'https://getpocket.com/v3/oauth/request', 
            	[
            		'headers' => $this->getHeaders(),
                	'form_params' => [
                    	'consumer_key' => $this->key,
                    	'redirect_uri' => $redirect,
                	],
            	]
            );
        } catch (\GuzzleException $e) {
            throw new \Exception(implode(',', $e->getResponse()->getHeaders()['X-Error']));
        }

        return substr($res->getBody(), 5);
	}

	/**
	 * Get access token via the Pocket api
	 * 
	 * @return string  Pocket acces token
	 */
	private function getAccessToken($requestToken) : string
	{
		try {
            $res = $this->guzzle->request(
            	'post', 
            	'https://getpocket.com/v3/oauth/authorize', 
            	[
            		'headers' => $this->getHeaders(),
                	'form_params' => [
                    	'consumer_key' => $this->key,
                    	'code' => $requestToken,
                	],
            	]
            );
        } catch (\GuzzleException $e) {
            throw new \Exception(implode(',', $e->getResponse()->getHeaders()['X-Error']));
        }

        $chunks = explode('&', $res->getBody());
        $accessToken = explode('=', $chunks[0])[1];

        return $accessToken;
	}

	/**
	 * Get the url to redirect the user to ask for Pocket authorization
	 * 
	 * @param  string  $requestToken
	 * @return string  url
	 */
	private function getRedirectUrl($requestToken, $redirect) : string
	{
		return 'https://getpocket.com/auth/authorize?request_token=' . $requestToken .'&redirect_uri=' . $redirect;
	}

	/**
	 * Persist a data with a key-value structure
	 * 
	 * @param string  $key  Position of the value
	 * @param string  $value  Value to store
	 */
	private function persist($key, $value)
	{
	   $session = new Session();
       $session->set($key, $value);
	} 

	/**
	 * Get a value from a key-value structure
	 * 
	 * @param  string $key
	 * @return  string
	 */
	private function get($key) : string
	{
	   $session = new Session();
       return $session->get($key);
	} 

	/**
	 * Get defalt headers for the HTTP request
	 * 
	 * @return array  Needed  headers
	 */
	private function getHeaders() : array
	{
		return [
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Accept' => 'application/x-www-form-urlencoded',
        ];
	}
}