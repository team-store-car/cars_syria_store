<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    /**
     * Create a new UserController instance.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a paginated listing of users with filters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->query();
        $perPage = $request->query('per_page', 15);
        $users = $this->userService->getAllUsers($filters, (int) $perPage);
        return response()->json(new UserCollection($users), 200);
    }

    /**
     * Store a newly created user.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());
        return response()->json(new UserResource($user), 201);
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        // return dd($id);
        $user = $this->userService->getUserById($id);
        return response()->json(new UserResource($user), 200);
    }

    /**
     * Update the specified user.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        
        $updatedUser = $this->userService->updateUser($user, $request->validated());
        return response()->json(new UserResource($updatedUser), 200);
    }

    /**
     * Delete the specified user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->userService->deleteUser($id);
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    /**
     * Assign a role to the specified user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $this->userService->assignUserRole($user, $request->role);
        return response()->json(['message' => 'Role assigned successfully'], 200);
    }

    /**
     * Remove a role from the specified user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function removeRole(Request $request, User $user): JsonResponse
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $this->userService->removeUserRole($user, $request->role);
        return response()->json(['message' => 'Role removed successfully'], 200);
    }
}
