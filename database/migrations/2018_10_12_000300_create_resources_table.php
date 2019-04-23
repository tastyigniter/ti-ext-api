<?php namespace Igniter\Api\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class CreateResourcesTable extends Migration
{
    public function up()
    {
        Schema::create('igniter_api_resources', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('endpoint');
            $table->string('model');
            $table->string('controller');
            $table->string('transformer');
            $table->string('description')->nullable();
            $table->text('meta')->nullable();
            $table->boolean('is_custom')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('igniter_api_resources');
    }
}