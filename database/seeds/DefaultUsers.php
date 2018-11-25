<?php

use Illuminate\Database\Seeder;
use App\User;

class DefaultUsers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $strongPassword = str_random(12);
        echo "---------------------------------------------\n";
        echo "We use strong password for admin\n";
        echo "---------------------------------------------\n";
        echo "\n";
        echo "ADMIN PASSWORD: " . $strongPassword . "\n";
        //
        User::create([
            'name' => 'admin',
            'email' => env('ADMIN_EMAIL'),
            'email_verified_at' => now(),   // now
            'password' => bcrypt($strongPassword),
            'role' => 'ADMIN'
        ]);
    }
}
