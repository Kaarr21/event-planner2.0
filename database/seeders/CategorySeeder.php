<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Conference',
            'Workshop',
            'Party',
            'Wedding',
            'Seminar',
            'Meeting',
            'Webinar',
            'Concert',
            'Other',
        ];

        foreach ($categories as $category) {
            \App\Models\Category::firstOrCreate(['name' => $category, 'user_id' => null]);
        }
    }
}
