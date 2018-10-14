<?php namespace Igniter\Api\Database\Migrations;

use Igniter\Api\Models\Resource;
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

        Resource::unguard();
        Resource::create([
            'name' => 'Dummy',
            'description' => 'Description of this API resource',
            'model' => 'Igniter\Api\Models\Resource',
            'controller' => 'Igniter\Api\Resources\Dummy',
            'transformer' => 'Igniter\Api\Resources\Transformers\DummyTransformer',
            'meta' => [
                'actions' => ['index', 'show']
            ],
            'is_custom' => TRUE,
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('igniter_api_resources');
    }
}