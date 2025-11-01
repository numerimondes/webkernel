<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apt_timezones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->string('offset', 10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'name']);
        });

        $timezones = [
            // Europe
            ['name' => 'Europe/Paris', 'display_name' => 'Paris (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/London', 'display_name' => 'Londres (UTC+0)', 'offset' => '+00:00'],
            ['name' => 'Europe/Berlin', 'display_name' => 'Berlin (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Rome', 'display_name' => 'Rome (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Madrid', 'display_name' => 'Madrid (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Amsterdam', 'display_name' => 'Amsterdam (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Zurich', 'display_name' => 'Zurich (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Vienna', 'display_name' => 'Vienne (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Stockholm', 'display_name' => 'Stockholm (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Oslo', 'display_name' => 'Oslo (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Copenhagen', 'display_name' => 'Copenhague (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Helsinki', 'display_name' => 'Helsinki (UTC+2)', 'offset' => '+02:00'],
            ['name' => 'Europe/Warsaw', 'display_name' => 'Varsovie (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Prague', 'display_name' => 'Prague (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Budapest', 'display_name' => 'Budapest (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Europe/Bucharest', 'display_name' => 'Bucarest (UTC+2)', 'offset' => '+02:00'],
            ['name' => 'Europe/Sofia', 'display_name' => 'Sofia (UTC+2)', 'offset' => '+02:00'],
            ['name' => 'Europe/Athens', 'display_name' => 'Athènes (UTC+2)', 'offset' => '+02:00'],
            ['name' => 'Europe/Istanbul', 'display_name' => 'Istanbul (UTC+3)', 'offset' => '+03:00'],
            ['name' => 'Europe/Moscow', 'display_name' => 'Moscou (UTC+3)', 'offset' => '+03:00'],

            // North America
            ['name' => 'America/New_York', 'display_name' => 'New York (UTC-5)', 'offset' => '-05:00'],
            ['name' => 'America/Chicago', 'display_name' => 'Chicago (UTC-6)', 'offset' => '-06:00'],
            ['name' => 'America/Denver', 'display_name' => 'Denver (UTC-7)', 'offset' => '-07:00'],
            ['name' => 'America/Los_Angeles', 'display_name' => 'Los Angeles (UTC-8)', 'offset' => '-08:00'],
            ['name' => 'America/Toronto', 'display_name' => 'Toronto (UTC-5)', 'offset' => '-05:00'],
            ['name' => 'America/Vancouver', 'display_name' => 'Vancouver (UTC-8)', 'offset' => '-08:00'],
            ['name' => 'America/Montreal', 'display_name' => 'Montréal (UTC-5)', 'offset' => '-05:00'],

            // South America
            ['name' => 'America/Sao_Paulo', 'display_name' => 'São Paulo (UTC-3)', 'offset' => '-03:00'],
            ['name' => 'America/Buenos_Aires', 'display_name' => 'Buenos Aires (UTC-3)', 'offset' => '-03:00'],
            ['name' => 'America/Lima', 'display_name' => 'Lima (UTC-5)', 'offset' => '-05:00'],
            ['name' => 'America/Bogota', 'display_name' => 'Bogotá (UTC-5)', 'offset' => '-05:00'],
            ['name' => 'America/Mexico_City', 'display_name' => 'Mexico (UTC-6)', 'offset' => '-06:00'],

            // Asia
            ['name' => 'Asia/Tokyo', 'display_name' => 'Tokyo (UTC+9)', 'offset' => '+09:00'],
            ['name' => 'Asia/Shanghai', 'display_name' => 'Shanghai (UTC+8)', 'offset' => '+08:00'],
            ['name' => 'Asia/Hong_Kong', 'display_name' => 'Hong Kong (UTC+8)', 'offset' => '+08:00'],
            ['name' => 'Asia/Singapore', 'display_name' => 'Singapour (UTC+8)', 'offset' => '+08:00'],
            ['name' => 'Asia/Seoul', 'display_name' => 'Séoul (UTC+9)', 'offset' => '+09:00'],
            ['name' => 'Asia/Bangkok', 'display_name' => 'Bangkok (UTC+7)', 'offset' => '+07:00'],
            ['name' => 'Asia/Jakarta', 'display_name' => 'Jakarta (UTC+7)', 'offset' => '+07:00'],
            ['name' => 'Asia/Kolkata', 'display_name' => 'Mumbai/Delhi (UTC+5:30)', 'offset' => '+05:30'],
            ['name' => 'Asia/Dubai', 'display_name' => 'Dubaï (UTC+4)', 'offset' => '+04:00'],
            ['name' => 'Asia/Tehran', 'display_name' => 'Téhéran (UTC+3:30)', 'offset' => '+03:30'],

            // Africa
            ['name' => 'Africa/Cairo', 'display_name' => 'Le Caire (UTC+2)', 'offset' => '+02:00'],
            ['name' => 'Africa/Johannesburg', 'display_name' => 'Johannesburg (UTC+2)', 'offset' => '+02:00'],
            ['name' => 'Africa/Lagos', 'display_name' => 'Lagos (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Africa/Casablanca', 'display_name' => 'Casablanca (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Africa/Algiers', 'display_name' => 'Alger (UTC+1)', 'offset' => '+01:00'],
            ['name' => 'Africa/Tunis', 'display_name' => 'Tunis (UTC+1)', 'offset' => '+01:00'],

            // Oceania
            ['name' => 'Australia/Sydney', 'display_name' => 'Sydney (UTC+10)', 'offset' => '+10:00'],
            ['name' => 'Australia/Melbourne', 'display_name' => 'Melbourne (UTC+10)', 'offset' => '+10:00'],
            ['name' => 'Australia/Perth', 'display_name' => 'Perth (UTC+8)', 'offset' => '+08:00'],
            ['name' => 'Pacific/Auckland', 'display_name' => 'Auckland (UTC+12)', 'offset' => '+12:00'],

            // UTC default
            ['name' => 'UTC', 'display_name' => 'UTC (UTC+0)', 'offset' => '+00:00'],
        ];

        foreach ($timezones as $timezone) {
            DB::table('apt_timezones')->insert([
                'name' => $timezone['name'],
                'display_name' => $timezone['display_name'],
                'offset' => $timezone['offset'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('apt_timezones');
    }
};
