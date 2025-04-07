<?php
namespace App\Repositories;

use App\Models\ShopAd;

class ShopAdRepository
{
    public function create(array $data): ShopAd
    {
        return ShopAd::create($data);
    }
}
