<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
{
    //seed users table
    \App\Models\User::factory(2)->create();
    //seed articles table
    \App\Models\Article::factory(50)->create();
}}
