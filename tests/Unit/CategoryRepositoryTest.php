<?php

// Unit Tests for Repository

namespace Tests\Unit;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryRepository();
    }

    public function test_can_create_category()
    {
        $data = [
            'name' => 'Luxury',
            'description' => 'Luxury Vehicles',
            'category_type' => 'car'
        ];

        $category = $this->repository->create($data);

        $this->assertDatabaseHas('categories', ['name' => 'Luxury']);
        $this->assertInstanceOf(Category::class, $category);
    }
}
