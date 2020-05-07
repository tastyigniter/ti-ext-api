<?php namespace Igniter\Api\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerbs extends Migration
{
    public function up()
    {
        DB::table('igniter_api_resources')
            ->where('meta', '[]')
            ->update(['meta' => json_encode(['verbs' => ['index', 'store', 'show', 'update', 'destroy']])]);
    }

    public function down()
    {
    }
}