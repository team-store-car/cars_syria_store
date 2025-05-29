<?php

namespace Tests\Unit;

use App\Filters\CarFilter;
use App\Models\Car;
use Tests\TestCase;

class CarFilterTest extends TestCase
{
    protected $query;

    protected function setUp(): void
    {
        parent::setUp();
        $this->query = Car::query();
    }

    public function test_name_filter()
    {
        $filters = ['name' => 'BMW'];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringContainsString('"name" like ?', $query->toSql());
        $this->assertEquals(['%BMW%'], $query->getBindings());
    }

    public function test_brand_filter()
    {
        $filters = ['brand' => 'Toyota'];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringContainsString('"brand" like ?', $query->toSql());
        $this->assertEquals(['%Toyota%'], $query->getBindings());
    }

    public function test_category_id_filter()
    {
        $filters = ['category_id' => 1];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringContainsString('"category_id" = ?', $query->toSql());
        $this->assertEquals([1], $query->getBindings());
    }

    public function test_year_filter()
    {
        $filters = ['year' => 2020];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringContainsString('"year" = ?', $query->toSql());
        $this->assertEquals([2020], $query->getBindings());
    }

    public function test_mileage_filter()
    {
        $filters = ['mileage' => ['min' => 10000, 'max' => 50000]];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringContainsString('"mileage" between ? and ?', $query->toSql());
        $this->assertEquals([10000, 50000], $query->getBindings());
    }

    public function test_fuel_type_filter()
    {
        $filters = ['fuel_type' => 'Electric'];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringContainsString('"fuel_type" = ?', $query->toSql());
        $this->assertEquals(['Electric'], $query->getBindings());
    }

    public function test_horsepower_filter()
    {
        $filters = ['horsepower' => ['min' => 100, 'max' => 300]];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringContainsString('"horsepower" between ? and ?', $query->toSql());
        $this->assertEquals([100, 300], $query->getBindings());
    }

    public function test_seats_filter()
    {
        $filters = ['seats' => 5];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringContainsString('"seats" = ?', $query->toSql());
        $this->assertEquals([5], $query->getBindings());
    }

    public function test_is_featured_filter()
    {
        $filters = ['is_featured' => true];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringContainsString('"is_featured" = ?', $query->toSql());
        $this->assertEquals([1], $query->getBindings());
    }

    public function test_null_filter_ignored()
    {
        $filters = ['name' => null];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertStringNotContainsString('name', $query->toSql());
    }

    public function test_non_existent_filter_ignored()
    {
        $filters = ['invalid_filter' => 'value'];
        $filter = new CarFilter($this->query, $filters);
        $query = $filter->apply();

        $this->assertEquals(Car::query()->toSql(), $query->toSql());
    }
}