<?php

namespace KyleWLawrence\WaboxApp\Services;

use Illuminate\Support\Facades\Log;
use Log;

class NullService
{
    /**
     * @var bool
     */
    private $logCalls;

    public function __construct(bool $logCalls = false)
    {
        $this->logCalls = $logCalls;
    }

    public function __call($name, $arguments)
    {
        if ($this->logCalls) {
            Log::debug('Called WaboxApp facade method: '.$name.' with:', $arguments);

            return new self;
        }

        return $this;
    }
}
