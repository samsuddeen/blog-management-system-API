<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // public function run(): void
    // {
    //     // \App\Models\User::factory(10)->create();

    //     // \App\Models\User::factory()->create([
    //     //     'name' => 'Test User',
    //     //     'email' => 'test@example.com',
    //     // ]);
    //     $this->call(UserSeeder::class);
    // }

    public function run(): void
    {
        $permissions = [
            'post-create',
            'post-edit',
            'post-delete',
            'category-manage',
            'user-manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin = Role::create(['name' => 'admin']);
        $editor = Role::create(['name' => 'editor']);

        $admin->syncPermissions($permissions);
        $editor->syncPermissions(['post-create', 'post-edit']);

        // $this->call(PostSeeder::class);
    }



}
