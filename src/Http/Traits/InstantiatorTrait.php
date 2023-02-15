<?php

namespace KyleWLawrence\WaboxApp\Http\Traits\Utility;

use KyleWLawrence\WaboxApp\Http\HttpClient;

/**
 * The Instantiator trait which has the magic methods for instantiating Resources
 */
trait InstantiatorTrait
{
    /**
     * Generic method to object getter. Since all objects are protected, this method
     * exposes a getter function with the same name as the protected variable, for example
     * $client->tickets can be referenced by $client->tickets()
     *
     * @param $name
     * @param $arguments
     * @return ChainedParametersTrait
     *
     * @throws \Exception
     */
    public function __call($name)
    {
        if ((array_key_exists($name, $validSubResources = $this::getValidSubResources()))) {
            $className = $validSubResources[$name];
            $client = ($this instanceof HttpClient) ? $this : $this->client;
            $class = new $className($client);
        } else {
            throw new \Exception("No method called $name available in ".__CLASS__);
        }

        return $class;
    }
}
