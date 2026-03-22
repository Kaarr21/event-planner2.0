<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('banner_image_path')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('venue_type')->default('physical'); // physical, online
            $table->string('online_link')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('visibility')->default('draft'); // draft, published, private
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_frequency')->nullable();
            $table->integer('recurrence_interval')->default(1);
            $table->string('recurrence_unit')->nullable(); // day, week, month, year
            $table->dateTime('recurrence_end_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'banner_image_path',
                'start_at',
                'end_at',
                'timezone',
                'venue_type',
                'online_link',
                'capacity',
                'visibility',
                'is_recurring',
                'recurrence_frequency',
                'recurrence_interval',
                'recurrence_unit',
                'recurrence_end_at',
            ]);
        });
    }
};
