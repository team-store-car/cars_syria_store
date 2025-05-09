<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CarFilter
{
    protected Builder $query;
    protected array $filters;

    public function __construct(Builder $query, array $filters)
    {
        $this->query = $query;
        $this->filters = $filters;
    }

    public function apply(): Builder
    {
        foreach ($this->filters as $key => $value) {
            if (method_exists($this, $key) && !is_null($value)) {
                $this->$key($value);
            }
        }

        return $this->query;
    }

    // فلاتر لكل خاصية
    protected function name($value)
    {
        $this->query->where('name', 'like', "%{$value}%");
    }

    protected function brand($value)
    {
        $this->query->where('brand', 'like', "%{$value}%");
    }

    protected function category_id($value)
    {
        $this->query->where('category_id', $value);
    }

    protected function model($value)
    {
        $this->query->where('model', 'like', "%{$value}%");
    }

    protected function year($value)
    {
        $this->query->where('year', $value);
    }

    protected function country_of_manufacture($value)
    {
        $this->query->where('country_of_manufacture', 'like', "%{$value}%");
    }

    protected function condition($value)
    {
        $this->query->where('condition', $value);
    }

    protected function mileage($value)
    {
        if (is_array($value) && isset($value['min'], $value['max'])) {
            $this->query->whereBetween('mileage', [$value['min'], $value['max']]);
        }
    }

    protected function fuel_type($value)
    {
        $this->query->where('fuel_type', $value);
    }

    protected function transmission($value)
    {
        $this->query->where('transmission', $value);
    }

    protected function horsepower($value)
    {
        if (is_array($value) && isset($value['min'], $value['max'])) {
            $this->query->whereBetween('horsepower', [$value['min'], $value['max']]);
        }
    }

    protected function seats($value)
    {
        $this->query->where('seats', $value);
    }

    protected function color($value)
    {
        $this->query->where('color', 'like', "%{$value}%");
    }

    protected function is_featured($value)
    {
        $this->query->where('is_featured', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    protected function user_id($value)
    {
        $this->query->where('user_id', $value);
    }
}