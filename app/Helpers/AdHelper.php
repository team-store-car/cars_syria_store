<?php
namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class AdHelper
{
    public static function hasReachedDailyLimit(Model $business, string $relation, int $limit = 3): bool
    {
        return $business->$relation()->count() >= $limit;
    }
}



