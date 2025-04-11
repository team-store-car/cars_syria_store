<?php
namespace App\Repositories;

use App\Models\Workshop;

class WorkshopRepository
{
    public function create(array $data): Workshop
    {
        return Workshop::create($data);
    }

    public function update(Workshop $workshop, array $data): Workshop
    {
        $workshop->update($data);
        return $workshop;
    }

    public function delete(Workshop $workshop): void
    {
        $workshop->delete();
    }
}
