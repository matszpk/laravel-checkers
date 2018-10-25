<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
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
        //
        User::create([
            'name' => 'admin',
            'email' => env('ADMIN_EMAIL'),
            'email_verified_at' => new DateTime(),   // now
            'password' => encrypt('admin'),
            'role' => 'ADMIN'
        ]);
    }
}
