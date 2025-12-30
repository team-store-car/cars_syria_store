<?php

namespace App\Filters;

use App\Models\CarOffer;
use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;

class CarFilter
{
    /**
     * The query builder instance.
     *
     * @var Builder
     */
    protected Builder $query;

    /**
     * The filters to apply.
     *
     * @var array
     */
    protected array $filters;

    /**
     * Create a new CarFilter instance.
     *
     * @param Builder $query
     * @param array $filters
     */
    public function __construct(Builder $query, array $filters)
    {
        $this->query = $query;
        $this->filters = $filters;
    }

    /**
     * Apply the filters to the query.
     *
     * @return Builder
     */
    public function apply(): Builder
    {
        foreach ($this->filters as $key => $value) {
            if (method_exists($this, $key) && !is_null($value)) {
                $this->$key($value);
            }
        }

        return $this->query;
    }

    /**
     * Filter by car name.
     *
     * @param string $value
     * @return void
     */
    protected function name($value)
    {
        $this->query->where('name', 'like', "%{$value}%");
    }

    /**
     * Filter by car brand.
     *
     * @param string $value
     * @return void
     */
    protected function brand($value)
    {
        $this->query->where('brand', 'like', "%{$value}%");
    }

    /**
     * Filter by category ID.
     *
     * @param int $value
     * @return void
     */
    protected function category_id($value)
    {
        $this->query->where('category_id', $value);
    }

    /**
     * Filter by car model.
     *
     * @param string $value
     * @return void
     */
    protected function model($value)
    {
        $this->query->where('model', 'like', "%{$value}%");
    }

    /**
     * Filter by car year.
     *
     * @param int $value
     * @return void
     */
    protected function year($value)
    {
        $this->query->where('year', $value);
    }

    /**
     * Filter by country of manufacture.
     *
     * @param string $value
     * @return void
     */
    protected function country_of_manufacture($value)
    {
        $this->query->where('country_of_manufacture', 'like', "%{$value}%");
    }

    /**
     * Filter by car condition.
     *
     * @param string $value
     * @return void
     */
    protected function condition($value)
    {
        $this->query->where('condition', $value);
    }

    /**
     * Filter by mileage range.
     *
     * @param array{min: int, max: int} $value
     * @return void
     */
    protected function mileage($value)
    {
        if (is_array($value) && isset($value['min'], $value['max'])) {
            $this->query->whereBetween('mileage', [$value['min'], $value['max']]);
        }
    }

    /**
     * Filter by fuel type.
     *
     * @param string $value
     * @return void
     */
    protected function fuel_type($value)
    {
        $this->query->where('fuel_type', $value);
    }

    /**
     * Filter by transmission type.
     *
     * @param string $value
     * @return void
     */
    protected function transmission($value)
    {
        $this->query->where('transmission', $value);
    }

    /**
     * Filter by horsepower range.
     *
     * @param array{min: int, max: int} $value
     * @return void
     */
    protected function horsepower($value)
    {
        if (is_array($value) && isset($value['min'], $value['max'])) {
            $this->query->whereBetween('horsepower', [$value['min'], $value['max']]);
        }
    }

    /**
     * Filter by number of seats.
     *
     * @param int $value
     * @return void
     */
    protected function seats($value)
    {
        $this->query->where('seats', $value);
    }

    /**
     * Filter by car color.
     *
     * @param string $value
     * @return void
     */
    protected function color($value)
    {
        $this->query->where('color', 'like', "%{$value}%");
    }

    /**
     * Filter by featured status.
     *
     * @param bool $value
     * @return void
     */
    protected function is_featured($value)
    {
        $this->query->where('is_featured', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    /**
     * Filter by user ID.
     *
     * @param int $value
     * @return void
     */
    protected function user_id($value)
    {
        $this->query->where('user_id', $value);
    }

    /**
     * Filter by store ID.
     *
     * @param int $value
     * @return void
     */
    protected function store_id($value)
    {
        $store = Store::findOrFail($value);
        $user_id = $store->user_id;
        $this->user_id($user_id);
    }
    /**
     * Filter by offer price range.
     *
     * @param array{min: float, max: float} $value
     * @return void
     */
    protected function price($value)
    {
        if (is_array($value)) {
            $this->query->whereIn('cars.id', function ($query) use ($value) {
                $query->select('car_id')
                    ->from('car_offers');

                if (isset($value['min']) && isset($value['max'])) {
                    $query->whereBetween('price', [$value['min'], $value['max']]);
                } elseif (isset($value['min'])) {
                    $query->where('price', '>=', $value['min']);
                } elseif (isset($value['max'])) {
                    $query->where('price', '<=', $value['max']);
                }
            });
        }
    }

    /**
     * Filter by offer type.
     *
     * @param string $value
     * @return void
     */
    protected function offer_type($value)
    {
        $this->query->whereIn('cars.id', function ($query) use ($value) {
            $query->select('car_id')
                ->from('car_offers')
                ->where('offer_type', $value);
        });
    }

    /**
     * Filter by price unit.
     *
     * @param string $value
     * @return void
     */
    protected function price_unit($value)
    {
        $this->query->whereIn('cars.id', function ($query) use ($value) {
            $query->select('car_id')
                ->from('car_offers')
                ->where('price_unit', $value);
        });
    }

    /**
     * Filter by offer location.
     *
     * @param string $value
     * @return void
     */
    protected function location($value)
    {
        $this->query->whereIn('cars.id', function ($query) use ($value) {
            $query->select('car_id')
                ->from('car_offers')
                ->where('location', 'like', "%{$value}%");
        });
    }

    /**
     * Filter by pricing period.
     *
     * @param string $value
     * @return void
     */
    protected function pricing_period($value)
    {
        $this->query->whereIn('cars.id', function ($query) use ($value) {
            $query->select('car_id')
                ->from('car_offers')
                ->where('pricing_period', $value);
        });
    }

    /**
     * Filter by offer availability.
     *
     * @param bool $value
     * @return void
     */
    protected function is_available($value)
    {
        $this->query->whereIn('cars.id', function ($query) use ($value) {
            $query->select('car_id')
                ->from('car_offers')
                ->where('is_available', filter_var($value, FILTER_VALIDATE_BOOLEAN));
        });
    }

    /**
     * Filter by additional features.
     *
     * @param string $value
     * @return void
     */
    protected function additional_features($value)
    {
        $this->query->whereIn('cars.id', function ($query) use ($value) {
            $query->select('car_id')
                ->from('car_offers')
                ->where('additional_features', 'like', "%{$value}%");
        });
    }

    /**
     * Filter by whether the car has an offer.
     *
     * @param string $value
     * @return void
     */
    protected function has_offer($value)
    {
        if ($value === 'yes') {
            $this->query->whereHas('offer');
        } elseif ($value === 'no') {
            $this->query->whereDoesntHave('offer');
        }
        // If value is anything else (or 'all'), no filter is applied
    }
}
