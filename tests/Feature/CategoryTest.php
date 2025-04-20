<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_category()
    {
        $data = [
            'name' => 'SUV',
            'description' => 'Sport Utility Vehicle',
            'category_type' => 'car'
        ];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'SUV']);
    }

    public function test_cannot_create_category_with_invalid_data()
    {
        $response = $this->postJson('/api/categories', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'category_type']);
    }

    public function test_can_update_category()
    {
        $category = Category::factory()->create();
        $data = ['name' => 'Updated Name'];

        $response = $this->putJson("/api/categories/{$category->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Updated successfully']);
    }

    public function test_cannot_update_non_existent_category()
    {
        $response = $this->putJson('/api/categories/999', ['name' => 'Test']);

        $response->assertStatus(404);
    }

    public function test_can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Deleted successfully']);
    }
}
