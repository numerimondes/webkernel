<?php

namespace Webkernel\Core\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use DB;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Yassine El Moumen',
            'email' => 'info@numerimondes.com',
            'password' => 'cHeval2*troie',
        ]);

     //   $this->call([
     //       LanguagesSeeder::class,
     //   ]);

        $populateFilePath = __DIR__ . '/database/language_translations/populate.sql';
        $populateDirFilePath = '/database/language_translations';

        if (file_exists($populateFilePath)) {
            DB::unprepared(
                file_get_contents($populateFilePath)
            );
        } else {
            echo "Translations Seeder Populator : No file to populate translations.\nIt must be in $populateDirFilePath\n";
        }

    }
}
