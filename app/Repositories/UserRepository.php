<?php

namespace App\Repositories;

use App\Filters\UserFilter;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role;

class UserRepository
{
    /**
     * Get all users with pagination and filters.
     *
     * @param array $filters Filters to apply.
     * @param int $perPage Number of users per page.
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->with(['roles']);
        $filter = new UserFilter($query, $filters);
        return $filter->apply()->paginate($perPage);
    }

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return User
     */
    public function findById($id)
    {
        return User::findOrFail($id);
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        return User::create($data);
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    /**
     * Delete a user by their ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $user = $this->findById($id);
        $user->delete();
        return true;
    }

    /**
     * Assign a role to a user.
     *
     * @param User $user
     * @param string $role
     * @return void
     */
    public function assignRole(User $user, string $role)
    {
        $user->assignRole($role);
    }

    /**
     * Remove a role from a user.
     *
     * @param User $user
     * @param string $role
     * @return void
     */
    public function removeRole(User $user, string $role)
    {
        $user->removeRole($role);
    }
}
