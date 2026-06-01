<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateFacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update users that have the old faculty code to the new one
        $old = 'FCSIT';
        $new = 'FSKTM';

        DB::table('users')
            ->where('faculty', $old)
            ->update(['faculty' => $new]);
    }
}
