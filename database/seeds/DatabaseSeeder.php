<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseSeeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        echo "🌱 Seeding database...\n\n";
        
        // Create admin user
        echo "  Creating admin user...\n";
        Capsule::table('users')->insert([
            'full_name' => 'System Admin',
            'username' => 'admin',
            'email' => 'admin@gymly.com',
            'password_hash' => password_hash('Admin@123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'is_verified' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        echo "  ✓ Admin user created (username: admin, password: Admin@123)\n\n";
        
        // Create test user
        echo "  Creating test user...\n";
        Capsule::table('users')->insert([
            'full_name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@gymly.com',
            'password_hash' => password_hash('Test@123', PASSWORD_DEFAULT),
            'role' => 'user',
            'is_verified' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        echo "  ✓ Test user created (username: testuser, password: Test@123)\n";
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
