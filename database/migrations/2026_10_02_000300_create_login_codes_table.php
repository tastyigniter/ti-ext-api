<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orderpoint_login_codes') && !Schema::hasTable('igniter_api_login_codes')) {
            Schema::rename('orderpoint_login_codes', 'igniter_api_login_codes');

            return;
        }

        if (Schema::hasTable('igniter_api_login_codes')) {
            return;
        }

        Schema::create('igniter_api_login_codes', function(Blueprint $table): void {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('code_hash', 64);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->unique('code_hash');
            $table->index(['user_id', 'used_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('igniter_api_login_codes');
    }
};
