<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CarPermissionHelper
{
    public static function canCreateCar(User $user, string $relation, int $limit = 3): bool
    {
        // مستخدمو الورش لا يمكنهم إنشاء سيارات
        if ($user->role === 'workshop') {
            return false;
        }

        // مديرو المتاجر ليس لديهم حد
        if ($user->role === 'shop_manager') {
            return true;
        }

        if ($user->role === 'admin') {
            return true;
        }

        // المستخدمون العاديون مقيدون بثلاث سيارات
        return $user->$relation()->count() < $limit;
    }
}