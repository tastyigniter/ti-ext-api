<?php

namespace Igniter\Api\Models;

use Laravel\Sanctum\HasApiTokens;

/**
 * Users Model
 */
class ApiUsers extends \Admin\Models\Users_model
{
    use HasApiTokens;
    
    public function viaRequest(){
	    
    }

}