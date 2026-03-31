<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_provider_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->string('plate_number')->unique();
            $table->integer('year');
            $table->integer('seats');
            $table->decimal('price_per_day', 10, 2);
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cars');
    }
};