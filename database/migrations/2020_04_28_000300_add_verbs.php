<?php namespace Igniter\Api\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerbs extends Migration
{
    public function up()
    {
	    
        Schema::table('igniter_api_resources', function (Blueprint $table) {
            $table->text('verbs');
        });

        DB::table('igniter_api_resources')
            ->where('verbs', '')
            ->update(['verbs' => json_encode(['index', 'store', 'show', 'update', 'destroy'])]);
    }

    public function down()
    {
    }
}