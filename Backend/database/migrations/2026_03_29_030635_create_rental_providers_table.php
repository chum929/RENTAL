<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rental_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->text('address');
            $table->string('phone');
            $table->string('photo')->nullable();
            $table->enum('status', ['pending','approved','rejected','inactive'])
                  ->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rental_providers');
    }
};