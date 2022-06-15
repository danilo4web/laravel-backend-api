<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('is_active')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('admins')->insert(
            [
                'name' => "BNB Admin",
                'email' => "admin@bnb.com",
                'email_verified_at' => now(),
                'password' => '$2y$10$AYLvOEHeXZ5VwkX8Lm93JebW3LJ9yzLpg4bc4k8qhKs8mwxQEhXp2',
                'is_active' => 1
            ]
        );
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
