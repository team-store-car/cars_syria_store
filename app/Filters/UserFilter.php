<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilter
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
     * Create a new UserFilter instance.
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
     * Filter by user name.
     *
     * @param string $value
     * @return void
     */
    protected function name($value)
    {
        $this->query->where('name', 'like', "%{$value}%");
    }

    /**
     * Filter by user email.
     *
     * @param string $value
     * @return void
     */
    protected function email($value)
    {
        $this->query->where('email', 'like', "%{$value}%");
    }

    /**
     * Filter by user phone.
     *
     * @param string $value
     * @return void
     */
    protected function phone($value)
    {
        $this->query->where('phone', 'like', "%{$value}%");
    }

    /**
     * Filter by user role (Spatie role).
     *
     * @param string $value
     * @return void
     */
    protected function role($value)
    {
        $this->query->whereHas('roles', function (Builder $query) use ($value) {
            $query->where('name', $value);
        });
    }
}

