<?php

namespace Igniter\Api\Classes;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @deprecated
 */
class TransformerAbstract extends JsonResource
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * Include resources without needing it to be requested.
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @return void
     */
    public function __construct($resource = null)
    {
        $this->resource = $resource;
    }

    /**
     * Returns all available includes.
     *
     * @return array
     */
    public function getAvailableIncludes()
    {
        return $this->availableIncludes;
    }

    /**
     * Returns all default includes.
     *
     * @return array
     */
    public function getDefaultIncludes()
    {
        return $this->defaultIncludes;
    }

    public function getIncludes()
    {
        $includes = $this->getDefaultIncludes();

        foreach ($this->getAvailableIncludes() as $include) {
            if ($this->isRequestedInclude($include)) {
                $includes[] = $include;
            }
        }

        return $includes;
    }

    public function toArray($request)
    {
        return parent::toArray($request);
    }

    protected function isRequestedInclude($include)
    {
        $requested = $_GET['include'] ?? '';

        $includes = is_string($requested) ? explode(',', $requested) : $requested;

        return in_array($include, $includes);
    }
}
