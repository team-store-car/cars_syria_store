<?php

namespace App\Repositories;

use App\Filters\WorkshopAdFilter;
use App\Models\WorkshopAd;
use Illuminate\Database\Eloquent\ModelNotFoundException; // أضف هذا
use Illuminate\Pagination\LengthAwarePaginator;

class WorkshopAdRepository
{

        /**
     * Get all workshop ads with pagination and filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function all(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = WorkshopAd::query()->with([
            'workshop:id,name',
            'images',
        ]);
        $filter = new WorkshopAdFilter($query, $filters);
        return $filter->apply()->paginate($perPage);
    }

    public function find(int $id): ?WorkshopAd
    {
        // يمكنك استخدام findOrFail إذا كنت تفضل رمي استثناء مباشرة هنا
        // return WorkshopAd::findOrFail($id);
        return WorkshopAd::find($id);
    }

    public function create(array $data): WorkshopAd
    {
        return WorkshopAd::create($data);
    }

    // دالة لتحديث إعلان موجود
    public function update(WorkshopAd $workshopAd, array $data): bool // غيّرنا نوع الإرجاع لـ bool
    {
        return $workshopAd->update($data);
    }

    // دالة لحذف إعلان موجود
    public function delete(WorkshopAd $workshopAd): bool
    {
        return $workshopAd->delete();
    }
}
