<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    /**
     * Create a new UserService instance.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users with pagination and filters.
     *
     * @param array $filters Filters to apply.
     * @param int $perPage Number of users per page.
     * @return LengthAwarePaginator
     */
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getAll($filters, $perPage);
    }

    /**
     * Get a user by their ID.
     *
     * @param int $id
     * @return User
     */
    public function getUserById($id)
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Create a new user and assign a role if provided.
     *
     * @param array $data User data including optional role.
     * @return User
     */
    public function createUser(array $data)
    {
        $this->authorizeAdminAction();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->userRepository->create($data);

        if (isset($data['role'])) {
            $this->userRepository->assignRole($user, $data['role']);
        }

        return $user;
    }

    /**
     * Update an existing user and their role if provided.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data)
    {
        $this->authorizeUserAction($user);

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $updatedUser = $this->userRepository->update($user, $data);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $updatedUser;
    }

    /**
     * Delete a user by their ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser($id)
    {
        $user = $this->getUserById($id);
        $this->authorizeUserAction($user);
        return $this->userRepository->delete($id);
    }

    /**
     * Assign a role to a user.
     *
     * @param User $user
     * @param string $role
     * @return void
     */
    public function assignUserRole(User $user, string $role)
    {
        $this->authorizeAdminAction();
        $this->userRepository->assignRole($user, $role);
    }

    /**
     * Remove a role from a user.
     *
     * @param User $user
     * @param string $role
     * @return void
     */
    public function removeUserRole(User $user, string $role)
    {
        $this->authorizeAdminAction();
        $this->userRepository->removeRole($user, $role);
    }

    /**
     * Authorize actions for admins or the user themselves.
     *
     * @param User $user
     * @throws AuthorizationException
     */
    protected function authorizeUserAction(User $user): void
    {
        $authUser = Auth::user();
        if ($user->id !== $authUser->id && !$authUser->hasRole('admin')) {
            throw new AuthorizationException('غير مصرح لك بإجراء هذا الإجراء على هذا المستخدم.');
        }
    }

    /**
     * Authorize actions restricted to admins only.
     *
     * @throws AuthorizationException
     */
    protected function authorizeAdminAction(): void
    {
        $authUser = Auth::user();
        if (!$authUser->hasRole('admin')) {
            throw new AuthorizationException('يجب أن تكون مديرًا لإجراء هذا الإجراء.');
        }
    }
}

