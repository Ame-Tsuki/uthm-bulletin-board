<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InsertAdminUser extends Migration
{
    public function up()
    {
        DB::table('users')->insert([
            'uthm_id' => 'ADMIN001',
            'name' => 'Admin User',
            'email' => 'admin@uthm.edu.my',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_verified' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        DB::table('users')->where('email', 'admin@uthm.edu.my')->delete();
    }
}