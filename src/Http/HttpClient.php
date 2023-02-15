<?php

namespace KyleWLawrence\WaboxApp\Http;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use KyleWLawrence\WaboxApp\Http\Exceptions\MissingParametersException;
use KyleWLawrence\WaboxApp\Http\Middleware\RetryHandler;
use KyleWLawrence\WaboxApp\Http\Resources\Send;

/**
 * Client class, base level access
 *
 * @method Send send()
 */
class HttpClient
{
    const VERSION = '1.0.0';

    /**
     * @var array
     */
    private array $headers = [];

    /**
     * @var string
     */
    protected string $apiBasePath;

    /**
     * @var Debug
     */
    protected Debug $debug;

    /**
     * @var \GuzzleHttp\Client
     */
    public \GuzzleHttp\Client $guzzle;

    /**
     * @param  string  $token
     * @param  string|int  $uid
     * @param  string  $url
     * @param  \GuzzleHttp\Client  $guzzle
     */
    public function __construct(
        protected string $token,
        protected string|int $uid,
        protected string $apiUrl = 'www.waboxapp.com/',
        $scheme = 'https',
        $guzzle = null
    ) {
        if (is_null($guzzle)) {
            $handler = HandlerStack::create();
            $handler->push(new RetryHandler(['retry_if' => function ($retries, $request, $response, $e) {
                return $e instanceof RequestException && strpos($e->getMessage(), 'ssl') !== false;
            }]), 'retry_handler');
            $this->guzzle = new \GuzzleHttp\Client(compact('handler'));
        } else {
            $this->guzzle = $guzzle;
        }

        $this->url = $url;

        $this->debug = new Debug();
    }

    /**
     * @return array
     */
    public static function getValidSubResources()
    {
        return [
            'send' => Send::class,
        ];
    }

    public function setToken(string $token): object
    {
        $this->token = $token;

        return $this;
    }

     public function setUid(string|int $uid): object
     {
         $this->uid = $uid;

         return $this;
     }

    public function getToken(): string
    {
        return $this->token;
    }

     public function getUid(): string|int
     {
         return $this->uid;
     }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param  string  $key The name of the header to set
     * @param  string  $value The value to set in the header
     * @return HttpClient
     *
     * @internal param array $headers
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Return the user agent string
     *
     * @return string
     */
    public function getUserAgent()
    {
        return 'WaboxApp API PHP '.self::VERSION;
    }

    /**
     * Returns the generated api URL
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    public function getApiBasePath()
    {
        return $this->apiBasePath;
    }

    /**
     * Set debug information as an object
     *
     * @param  mixed  $lastRequestHeaders
     * @param  mixed  $lastRequestBody
     * @param  mixed  $lastResponseCode
     * @param  string  $lastResponseHeaders
     * @param  mixed  $lastResponseError
     */
    public function setDebug(
        $lastRequestHeaders,
        $lastRequestBody,
        $lastResponseCode,
        $lastResponseHeaders,
        $lastResponseError
    ) {
        $this->debug->lastRequestHeaders = $lastRequestHeaders;
        $this->debug->lastRequestBody = $lastRequestBody;
        $this->debug->lastResponseCode = $lastResponseCode;
        $this->debug->lastResponseHeaders = $lastResponseHeaders;
        $this->debug->lastResponseError = $lastResponseError;
    }

    /**
     * Returns debug information in an object
     *
     * @return Debug
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * This is a helper method to do a get request.
     *
     * @param    $endpoint
     * @param  array  $queryParams
     * @return \stdClass | null
     *
     * @throws \KyleWLawrence\WaboxApp\Http\Exceptions\AuthException
     * @throws \KyleWLawrence\WaboxApp\Http\Exceptions\ApiResponseException
     */
    public function get($endpoint, $queryParams = [])
    {
        $sideloads = $this->getSideload($queryParams);

        if (is_array($sideloads)) {
            $queryParams['include'] = implode(',', $sideloads);
            unset($queryParams['sideload']);
        }

        $response = Http::send(
            $this,
            $endpoint,
            ['queryParams' => $queryParams]
        );

        return $response;
    }

    /**
     * This is a helper method to do a post request.
     *
     * @param    $endpoint
     * @param  array  $postData
     * @param  array  $options
     * @return null|\stdClass
     *
     * @throws \KyleWLawrence\WaboxApp\Http\Exceptions\AuthException
     * @throws \KyleWLawrence\WaboxApp\Http\Exceptions\ApiResponseException
     */
    public function post($endpoint, $postData = [], $options = [])
    {
        $extraOptions = array_merge($options, [
            'postFields' => $postData,
            'method' => 'POST',
        ]);

        $response = Http::send(
            $this,
            $endpoint,
            $extraOptions
        );

        return $response;
    }

    /**
     * Check that all parameters have been supplied
     *
     * @param  array  $params
     * @param  array  $mandatory
     * @return bool
     */
    public function hasKeys(array $params, array $mandatory)
    {
        for ($i = 0; $i < count($mandatory); $i++) {
            if (! array_key_exists($mandatory[$i], $params)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check that any parameter has been supplied
     *
     * @param  array  $params
     * @param  array  $mandatory
     * @return bool
     */
    public function hasAnyKey(array $params, array $mandatory)
    {
        for ($i = 0; $i < count($mandatory); $i++) {
            if (array_key_exists($mandatory[$i], $params)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Send a Chat
     *
     * @param  array  $params
     * @return \stdClass | null
     *
     * @throws ResponseException
     * @throws \Exception
     * @throws \KyleWLawrence\WaboxApp\Http\Exceptions\ApiResponseException
     * @throws \KyleWLawrence\WaboxApp\Http\Exceptions\MissingParametersException
     */
    public function sendChat(array $params)
    {
        $this->apiBasePath = '';
        $mandatory = ['to', 'text'];
        if (! $this->hasKeys($params, $mandatory)) {
            throw new MissingParametersException(__METHOD__, $mandatory);
        }

        $route = '';

        return $this->client->post(
            $route,
            $params
        );
    }
}
