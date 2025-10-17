<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseSeeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        echo "🌱 Seeding users...\n";
        
        // Create a test user
        Capsule::table('users')->insert([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'is_verified' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        echo "  ✓ Created test user (testuser / password123)\n";
        
        // You can call other seeders here
        // $this->call(UsersTableSeeder::class);
    }
    
    /**
     * Call another seeder class
     */
    protected function call($seederClass)
    {
        $seederFile = __DIR__ . '/' . $seederClass . '.php';
        
        if (file_exists($seederFile)) {
            require_once $seederFile;
            $seeder = new $seederClass;
            $seeder->run();
        }
    }
}
