<?php

namespace KyleWLawrence\WaboxApp\Services;

use BadMethodCallException;
use Config;
use InvalidArgumentException;
use KyleWLawrence\WaboxApp\Http\HttpClient;

class WaboxAppService
{
    public string $uid;

    private string $token;

    public string $url;

    public HttpClient $client;

    /**
     * Get auth parameters from config, fail if any are missing.
     * Instantiate API client and set auth bearer token.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->token = config('waboxapp-laravel.token');
        $this->uid = config('waboxapp-laravel.uid');
        $this->url = config('waboxapp-laravel.url');

        if (! $this->bearer || ! $this->uid) {
            throw new InvalidArgumentException('Please set WABOXAPP_TOKEN && WABOXAPP_UID environment variables.');
        }

        $this->client = new HttpClient($this->token, $this->uid, $this->url);
    }

    /**
     * Pass any method calls onto $this->client
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (is_callable([$this->client, $method])) {
            return call_user_func_array([$this->client, $method], $args);
        } else {
            throw new BadMethodCallException("Method $method does not exist");
        }
    }

    /**
     * Pass any property calls onto $this->client
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this->client, $property)) {
            return $this->client->{$property};
        } else {
            throw new BadMethodCallException("Property $property does not exist");
        }
    }
}
