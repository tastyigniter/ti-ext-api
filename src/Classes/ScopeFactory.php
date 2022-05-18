<?php

namespace Igniter\Api\Classes;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\ScopeFactory as FractalScopeFactory;

class ScopeFactory extends FractalScopeFactory
{
    public function createScopeFor(Manager $manager, ResourceInterface $resource, $scopeIdentifier = null)
    {
        return new Scope($manager, $resource, $scopeIdentifier);
    }
}
