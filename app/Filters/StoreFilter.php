<?php

namespace App\Filters;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;

class StoreFilter
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
     * Create a new StoreFilter instance.
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
     * Filter by store name.
     *
     * @param string $value
     * @return void
     */
    protected function name($value)
    {
        $this->query->where('name', 'like', "%{$value}%");
    }

    /**
     * Filter by store description.
     *
     * @param string $value
     * @return void
     */
    protected function description($value)
    {
        $this->query->where('description', 'like', "%{$value}%");
    }

    /**
     * Filter by store address.
     *
     * @param string $value
     * @return void
     */
    protected function address($value)
    {
        $this->query->where('address', 'like', "%{$value}%");
    }

    /**
     * Filter by store phone.
     *
     * @param string $value
     * @return void
     */
    protected function phone($value)
    {
        $this->query->where('phone', 'like', "%{$value}%");
    }

    /**
     * Filter by store email.
     *
     * @param string $value
     * @return void
     */
    protected function email($value)
    {
        $this->query->where('email', 'like', "%{$value}%");
    }

    /**
     * Filter by store website.
     *
     * @param string $value
     * @return void
     */
    protected function website($value)
    {
        $this->query->where('website', 'like', "%{$value}%");
    }

    /**
     * Filter by store status.
     *
     * @param string $value
     * @return void
     */
    protected function status($value)
    {
        $this->query->where('status', $value);
    }
    
    /**
     * Filter by user ID (owner).
     *
     * @param int $value
     * @return void
     */
    protected function user_id($value)
    {
        $this->query->where('user_id', $value);
    }
}
