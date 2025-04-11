<?php

namespace App\Repositories;

use App\Models\WorkshopAd;

class WorkshopAdRepository
{
    /**
     * إنشاء إعلان ورشة.
     */
    public function create(array $data): WorkshopAd
    {
        return WorkshopAd::create($data);
    }

    /**
     * البحث عن إعلان ورشة باستخدام ID.
     */
    public function find(int $id): ?WorkshopAd
    {
        return WorkshopAd::find($id);
    }

    /**
     * تحديث إعلان ورشة.
     */
    public function update(WorkshopAd $workshopAd, array $data): bool
    {
        return $workshopAd->update($data);
    }

    /**
     * حذف إعلان ورشة.
     */
    public function delete(WorkshopAd $workshopAd): bool
    {
        return $workshopAd->delete();
    }
}
