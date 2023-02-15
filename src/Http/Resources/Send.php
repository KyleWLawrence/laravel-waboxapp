<?php

namespace KyleWLawrence\WaboxApp\Http\Resources\Core;

use KyleWLawrence\WaboxApp\Http\Exceptions\MissingParametersException;
use KyleWLawrence\WaboxApp\Http\Exceptions\ResponseException;
use KyleWLawrence\WaboxApp\Http\Resources\ResourceAbstract;

class Send extends ResourceAbstract
{
    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes()
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'chat' => '',
        ]);
    }

    /**
     * Send a Chat
     *
     * @param  array  $params
     * @return \stdClass | null
     *
     * @throws ResponseException
     * @throws \Exception
     * @throws \KyleWLawrence\WaboxApp\Http\Exceptions\AuthException
     * @throws \KyleWLawrence\WaboxApp\Http\Exceptions\ApiResponseException
     */
    public function chat(array $params)
    {
        $mandatory = ['to', 'text'];
        if (! $this->hasKeys($params, $mandatory)) {
            throw new MissingParametersException(__METHOD__, $mandatory);
        }

        $route = $this->getRoute(__FUNCTION__, $params);

        return $this->client->post(
            $route,
            [$this->objectName => $params]
        );
    }
}
