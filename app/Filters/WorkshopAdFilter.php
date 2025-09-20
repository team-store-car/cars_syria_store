<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class WorkshopAdFilter
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
     * Create a new WorkshopAdFilter instance.
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
     * Filter by workshop ad title.
     *
     * @param string $value
     * @return void
     */
    protected function title($value)
    {
        $this->query->where('title', 'like', "%{$value}%");
    }

    /**
     * Filter by workshop ad description.
     *
     * @param string $value
     * @return void
     */
    protected function description($value)
    {
        $this->query->where('description', 'like', "%{$value}%");
    }

    /**
     * Filter by workshop ad price range.
     *
     * @param array{min: float, max: float} $value
     * @return void
     */
    protected function price($value)
    {
        if (is_array($value) && isset($value['min'], $value['max'])) {
            $this->query->whereBetween('price', [$value['min'], $value['max']]);
        }
    }

    /**
     * Filter by workshop ID.
     *
     * @param int $value
     * @return void
     */
    protected function workshop_id($value)
    {
        $this->query->where('workshop_id', $value);
    }
}
