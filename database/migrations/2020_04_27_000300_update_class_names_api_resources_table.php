<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('igniter_api_resources')
            ->where('controller', 'Igniter\Local\Resources\Menus')
            ->orWhere('controller', 'Igniter\Local\ApiResources\Menus')
            ->update(['controller' => \Igniter\Api\ApiResources\Menus::class]);

        DB::table('igniter_api_resources')
            ->where('transformer', 'Igniter\Local\Resources\Transformers\MenuTransformer')
            ->orWhere('transformer', 'Igniter\Local\ApiResources\Transformers\MenuTransformer')
            ->update(['transformer' => \Igniter\Api\ApiResources\Transformers\MenuTransformer::class]);

        DB::table('igniter_api_resources')
            ->where('controller', 'Igniter\Local\Resources\Categories')
            ->orWhere('controller', 'Igniter\Local\ApiResources\Categories')
            ->update(['controller' => \Igniter\Api\ApiResources\Categories::class]);

        DB::table('igniter_api_resources')
            ->where('transformer', 'Igniter\Local\Resources\Transformers\CategoryTransformer')
            ->orWhere('transformer', 'Igniter\Local\ApiResources\Transformers\CategoryTransformer')
            ->update(['transformer' => \Igniter\Api\ApiResources\Transformers\CategoryTransformer::class]);

        DB::table('igniter_api_resources')
            ->where('controller', 'Igniter\Local\Resources\Locations')
            ->orWhere('controller', 'Igniter\Local\ApiResources\Locations')
            ->update(['controller' => \Igniter\Api\ApiResources\Locations::class]);

        DB::table('igniter_api_resources')
            ->where('transformer', 'Igniter\Local\Resources\Transformers\LocationTransformer')
            ->orWhere('transformer', 'Igniter\Local\ApiResources\Transformers\LocationTransformer')
            ->update(['transformer' => \Igniter\Api\ApiResources\Transformers\LocationTransformer::class]);
    }

    public function down(): void {}
};
