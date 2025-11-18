<?php

namespace App\Services\Demand;

use App\Exceptions\AdapterException;
use App\Interfaces\DemandServiceAdapter;
use Illuminate\Contracts\Container\BindingResolutionException;

readonly class DemandHandlerService
{

    private function __construct(
        private string               $driver,
        private DemandServiceAdapter $adapter,
    ) {}

    /**
     * @throws BindingResolutionException
     */
    public static function use(
        string $driver,
    ): static {
        return new static(
            driver: $driver,
            adapter: app()->make($driver),
        );
    }

    /**
     * @throws AdapterException
     */
    public function __call($method, $args)
    {
        if (! method_exists($this->adapter, $method)) {
            throw new AdapterException(
                message: "Method [$method] not found in [$this->driver]."
            );
        }

        return $this->adapter->$method(...$args);
    }
}
