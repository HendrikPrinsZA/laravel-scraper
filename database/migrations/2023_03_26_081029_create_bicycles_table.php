<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bicycles', function (Blueprint $table) {
            $table->id();
            $table->string('object_number', 32)->unique();
            $table->string('type', 32);
            $table->string('sub_type', 32);
            $table->string('brand', 32);
            $table->string('color', 32);
            $table->text('description');
            $table->string('city', 32);
            $table->string('storage_location', 64);
            $table->dateTime('registered_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bicycles');
    }
};
