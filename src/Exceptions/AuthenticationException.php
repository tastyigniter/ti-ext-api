<?php

namespace Igniter\Api\Exceptions;

use Illuminate\Auth\AuthenticationException as Exception;
use Illuminate\Contracts\Support\Responsable;

class AuthenticationException extends Exception implements Responsable
{
    public function toResponse($request)
    {
        return response()->json(['message' => $this->getMessage()], 401);
    }
}
