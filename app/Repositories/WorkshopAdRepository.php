<?php
namespace App\Repositories;

use App\Models\WorkshopAd;

class WorkshopAdRepository
{
    public function create(array $data): WorkshopAd
    {
        return WorkshopAd::create($data);
    }
}
