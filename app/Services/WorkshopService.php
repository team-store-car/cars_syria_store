<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workshop;
use App\Repositories\WorkshopRepository;
use Illuminate\Support\Facades\Auth;

class WorkshopService
{
    protected WorkshopRepository $workshopRepo;

    public function __construct(WorkshopRepository $workshopRepo)
    {
        $this->workshopRepo = $workshopRepo;
    }

    public function getAllWorkshops()
    {
        return $this->workshopRepo->all();
    }

    public function createWorkshop(array $data, User $user): Workshop
    {
        $data['user_id'] = $user->id;
        $workshop = $this->workshopRepo->create($data);
        if ($user->hasRole('user')) {
            $user->removeRole('user');
        }
        $user->assignRole('workshop');

        return $workshop;
    }

    public function updateWorkshop(Workshop $workshop, array $data, User $user): Workshop
    {
        if ($workshop->user_id !== $user->id && !(Auth::user()->hasRole('admin'))) {
            abort(403, 'غير مصرح بتعديل هذه الورشة');
        }

        return $this->workshopRepo->update($workshop, $data);
    }

    public function deleteWorkshop(Workshop $workshop, User $user): void
    {
        if ($workshop->user_id !== $user->id  && !(Auth::user()->hasRole('admin'))) {
            abort(403, 'غير مصرح بحذف هذه الورشة');
        }
        $owner = $workshop->user;
                if($owner->hasRole('workshop')){
            $owner->removeRole('workshop');
        }
        $owner->assignRole('user');

        $this->workshopRepo->delete($workshop);
    }
}
