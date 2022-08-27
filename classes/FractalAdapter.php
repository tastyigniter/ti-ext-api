<?php

namespace Igniter\Api\Classes;

use Dingo\Api\Http\Request;
use Dingo\Api\Transformer\Adapter\Fractal;
use Dingo\Api\Transformer\Binding;
use Illuminate\Contracts\Pagination\Paginator as IlluminatePaginator;

class FractalAdapter extends Fractal
{
    /**
     * Transform a response with a transformer.
     *
     * @param mixed $response
     * @param \League\Fractal\TransformerAbstract|object $transformer
     * @param \Dingo\Api\Transformer\Binding $binding
     * @param \Dingo\Api\Http\Request $request
     *
     * @return array
     */
    public function transform($response, $transformer, Binding $binding, Request $request)
    {
        $this->parseFractalIncludes($request);

        if ($sparseFieldsets = request('fields', false))
            $this->getFractal()->parseFieldsets($sparseFieldsets);

        $resource = $this->createResource($response, $transformer, $parameters = $binding->getParameters());

        if ($response instanceof IlluminatePaginator) {
            $paginator = $this->createPaginatorAdapter($response);

            $resource->setPaginator($paginator);
        }

        if ($this->shouldEagerLoad($response)) {
            $eagerLoads = $this->mergeEagerLoads($transformer, $this->fractal->getRequestedIncludes());

            $response->load($eagerLoads);
        }

        foreach ($binding->getMeta() as $key => $value) {
            $resource->setMetaValue($key, $value);
        }

        $binding->fireCallback($resource, $this->fractal);

        $identifier = isset($parameters['identifier']) ? $parameters['identifier'] : null;

        return $this->fractal->createData($resource, $identifier)->toArray();
    }
}
