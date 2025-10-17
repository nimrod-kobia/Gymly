# Database Migrations Guide

This project uses **Laravel's Illuminate Database** component for database migrations.

## 📁 Structure

```
database/
├── migrations/     # Migration files
└── seeds/         # Seeder files

scripts/
├── bootstrap.php  # Database connection setup
├── migrate.php    # Run migrations
├── rollback.php   # Rollback migrations
└── seed.php       # Run seeders
```

## 🚀 Quick Start

### Run All Migrations
```bash
php scripts/migrate.php
```

### Rollback Last Migration
```bash
php scripts/rollback.php
```

### Rollback Multiple Steps
```bash
php scripts/rollback.php --step=2
```

### Run Seeders
```bash
php scripts/seed.php                    # Run DatabaseSeeder
php scripts/seed.php UsersTableSeeder   # Run specific seeder
```

## ✍️ Creating New Migrations

### 1. Create Migration File

Create a new file in `database/migrations/` with this naming convention:
```
YYYY_MM_DD_HHMMSS_description.php
```

Example: `2024_10_17_120000_create_workouts_table.php`

### 2. Migration Template

```php
<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkoutsTable
{
    public function up()
    {
        if (!Capsule::schema()->hasTable('workouts')) {
            Capsule::schema()->create('workouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('duration'); // in minutes
                $table->integer('calories_burned')->nullable();
                $table->timestamps();
                
                $table->index('user_id');
                $table->index('created_at');
            });
            
            echo "    ✓ Created workouts table\n";
        } else {
            echo "    ⏭️  Workouts table already exists\n";
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('workouts');
        echo "    ✓ Dropped workouts table\n";
    }
}
```

### 3. Class Naming Convention

The class name should be PascalCase version of your migration description:
- `create_users_table` → `CreateUsersTable`
- `add_phone_to_users` → `AddPhoneToUsers`
- `create_workouts_table` → `CreateWorkoutsTable`

## 📊 Common Column Types

```php
// Primary key
$table->id();                           // Auto-incrementing BIGINT

// Strings
$table->string('name');                 // VARCHAR(255)
$table->string('email', 100);           // VARCHAR(100)
$table->text('description');            // TEXT

// Numbers
$table->integer('age');                 // INTEGER
$table->bigInteger('views');            // BIGINT
$table->decimal('price', 8, 2);         // DECIMAL(8,2)
$table->float('rating');                // FLOAT

// Booleans
$table->boolean('is_active');           // BOOLEAN

// Dates & Times
$table->date('birth_date');             // DATE
$table->time('start_time');             // TIME
$table->dateTime('published_at');       // DATETIME
$table->timestamp('verified_at');       // TIMESTAMP
$table->timestamps();                   // created_at & updated_at

// Foreign Keys
$table->foreignId('user_id')
    ->constrained()
    ->onDelete('cascade');

// JSON
$table->json('metadata');               // JSON

// Nullable columns
$table->string('middle_name')->nullable();

// Default values
$table->boolean('is_active')->default(true);
$table->integer('status')->default(0);

// Indexes
$table->index('email');
$table->unique('username');
$table->index(['user_id', 'created_at']);
```

## 🌱 Creating Seeders

Create a file in `database/seeds/`:

```php
<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class UsersTableSeeder
{
    public function run()
    {
        echo "🌱 Seeding users table...\n";
        
        $users = [
            [
                'full_name' => 'John Doe',
                'username' => 'johndoe',
                'email' => 'john@example.com',
                'password_hash' => password_hash('password', PASSWORD_DEFAULT),
                'is_verified' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'full_name' => 'Jane Smith',
                'username' => 'janesmith',
                'email' => 'jane@example.com',
                'password_hash' => password_hash('password', PASSWORD_DEFAULT),
                'is_verified' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        
        Capsule::table('users')->insert($users);
        
        echo "  ✓ Inserted " . count($users) . " users\n";
    }
}
```

## 🔄 Migration Workflow

### Development Workflow
1. Create migration file
2. Write `up()` and `down()` methods
3. Run `php scripts/migrate.php`
4. Test your changes
5. If needed, rollback with `php scripts/rollback.php`
6. Adjust migration and run again

### Production Deployment
1. Commit migrations to git
2. Pull changes on production
3. Run `php scripts/migrate.php`
4. Never rollback in production (create new migration instead)

## ⚠️ Important Notes

- **Always** include table existence checks in `up()` method
- **Never** modify existing migrations that have been run in production
- **Always** test rollbacks locally before deploying
- **Use transactions** for complex migrations (Capsule handles this automatically)
- **Create a backup** before running migrations in production

## 🛠️ Troubleshooting

### Migration fails with "Class not found"
- Check your class name matches the file name convention
- Ensure class name is PascalCase

### "Table already exists" error
- Add table existence check: `if (!Capsule::schema()->hasTable('table_name'))`

### Database connection fails
- Check `.env` file for correct database credentials
- Ensure Neon database is not paused
- Verify network connectivity

## 📚 Resources

- [Laravel Schema Builder](https://laravel.com/docs/migrations#tables)
- [Available Column Types](https://laravel.com/docs/migrations#available-column-types)
- [Illuminate Database GitHub](https://github.com/illuminate/database)
