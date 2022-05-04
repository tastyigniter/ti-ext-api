<?php

namespace Igniter\Api\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakePrimaryKeyBigintAllTables extends Migration
{
    public function up()
    {
        Schema::table('igniter_api_resources', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true)->change();
        });
    }

    public function down()
    {
    }
}
