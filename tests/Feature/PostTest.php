<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $editor;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'create-post']);
        Permission::create(['name' => 'edit-post']);
        Permission::create(['name' => 'delete-post']);
        Permission::create(['name' => 'create-category']);
        Permission::create(['name' => 'delete-category']);
        Permission::create(['name' => 'export-categories']);
        Permission::create(['name' => 'import-categories']);
        Permission::create(['name' => 'export-posts']);

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'create-post',
            'edit-post',
            'delete-post',
            'create-category',
            'delete-category',
            'export-categories',
            'import-categories',
            'export-posts',
        ]);

        $editorRole = Role::create(['name' => 'editor']);
        $editorRole->givePermissionTo(['create-post', 'edit-post']);

        // Create test users and assign roles
        $this->admin = User::factory()->create()->assignRole('admin');
        $this->editor = User::factory()->create()->assignRole('editor');
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function can_register_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'editor',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token'])
                 ->assertJsonFragment(['email' => 'test@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /** @test */
    public function can_login_user()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ])->assignRole('admin');

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token'])
                 ->assertJsonFragment(['email' => $user->email]);
    }

    /** @test */
    public function can_list_posts()
    {
        Post::factory()->count(3)->create([
            'category_id' => $this->category->id,
            'author_id' => $this->admin->id,
        ]);

        Sanctum::actingAs($this->admin);
        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'body', 'category', 'author']
                     ]
                 ]);
    }

    /** @test */
    public function can_create_post()
    {
        Sanctum::actingAs($this->editor);
        $response = $this->postJson('/api/posts', [
            'title' => 'Test Post',
            'body' => 'This is a test post.',
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => ['id', 'title', 'body', 'category', 'author']
                 ])
                 ->assertJsonFragment(['title' => 'Test Post']);
        $this->assertDatabaseHas('posts', ['title' => 'Test Post', 'author_id' => $this->editor->id]);
    }

    /** @test */
    public function can_update_post_by_author()
    {
        $post = Post::factory()->create([
            'author_id' => $this->editor->id,
            'category_id' => $this->category->id,
        ]);

        Sanctum::actingAs($this->editor);
        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated Post',
            'body' => 'Updated body.',
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Post']);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Post',
            'body' => 'Updated body.',
        ]);
    }

    /** @test */
    public function cannot_update_post_by_non_author()
    {
        $post = Post::factory()->create([
            'author_id' => $this->admin->id,
            'category_id' => $this->category->id,
        ]);

        Sanctum::actingAs($this->editor);
        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated Post',
            'body' => 'Updated body.',
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $post->title,
        ]);
    }

    /** @test */
    public function can_delete_post_by_admin()
    {
        $post = Post::factory()->create([
            'author_id' => $this->editor->id,
            'category_id' => $this->category->id,
        ]);

        Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /** @test */
    public function can_list_public_posts()
    {
        Post::factory()->count(2)->create([
            'category_id' => $this->category->id,
            'author_id' => $this->admin->id,
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/public/posts');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'body', 'category', 'author']
                     ]
                 ]);
    }

    /** @test */
    public function can_create_category()
    {
        Sanctum::actingAs($this->admin);
        $response = $this->postJson('/api/categories', [
            'name' => 'Tech',
            'slug' => 'tech',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Tech', 'slug' => 'tech']);
        $this->assertDatabaseHas('categories', ['slug' => 'tech']);
    }

    /** @test */
    public function cannot_create_category_without_permission()
    {
        Sanctum::actingAs($this->editor);
        $response = $this->postJson('/api/categories', [
            'name' => 'Tech',
            'slug' => 'tech',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('categories', ['slug' => 'tech']);
    }

    /** @test */
    public function cannot_delete_category_with_posts()
    {
        $category = Category::factory()->create();
        Post::factory()->create([
            'category_id' => $category->id,
            'author_id' => $this->admin->id,
        ]);

        Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /** @test */
    public function can_export_categories()
    {
        Category::factory()->count(5)->create();

        Sanctum::actingAs($this->admin);
        Excel::fake();

        $response = $this->getJson('/api/categories/export');

        $response->assertStatus(200);
        Excel::assertDownloaded('categories.xlsx');
    }

    /** @test */
    public function can_import_categories()
    {
        Sanctum::actingAs($this->admin);
        Excel::fake();

        $file = UploadedFile::fake()->create('categories.xlsx', 100);

        $response = $this->postJson('/api/categories/import', [
            'file' => $file,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Categories imported successfully']);
    }

    /** @test */
    public function can_export_posts()
    {
        Post::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'author_id' => $this->admin->id,
        ]);

        Sanctum::actingAs($this->admin);
        Excel::fake();

        $response = $this->getJson('/api/posts/export');

        $response->assertStatus(200);
        Excel::assertDownloaded('posts.xlsx');
    }

    /** @test */
    public function cannot_create_post_with_invalid_data()
    {
        Sanctum::actingAs($this->editor);
        $response = $this->postJson('/api/posts', [
            'title' => '',
            'body' => 'This is a test post.',
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title']);
        $this->assertDatabaseMissing('posts', ['body' => 'This is a test post.']);
    }
}
