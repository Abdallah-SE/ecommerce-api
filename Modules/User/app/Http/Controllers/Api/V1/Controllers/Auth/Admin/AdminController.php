<?php
namespace Modules\User\Http\Controllers\Api\V1\Controllers\Auth\Admin;

use Illuminate\Http\Request;
use Modules\User\Exceptions\AdminNotFoundException;
use Modules\User\Services\Api\V1\Admin\AdminService;
use Modules\Core\Http\Controllers\Api\V1\BaseApiController;
use Modules\User\Http\Resources\Api\V1\Admin\AdminResource;
use Modules\User\Http\Requests\Api\V1\Admin\StoreAdminRequest;
use Modules\User\Http\Requests\Api\V1\Admin\UpdateAdminRequest;

class AdminController extends BaseApiController
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('admin')->attempt($credentials)) {
            return $this->respondUnauthorized('Invalid email or password');
        }

        return $this->respondSuccess([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60,
        ], 'Login successful');
    }

    public function dashboard()
    {
        return $this->respondSuccess([
            'message' => 'Welcome Admin',
            'admin' => new AdminResource(auth('admin')->user())
        ]);
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $admins = $this->adminService->paginateAdmins($perPage, $page);

        return $this->respondSuccess(
            AdminResource::collection($admins),
            'Admins list',
            200,
            [
                'current_page' => $admins->currentPage(),
                'per_page' => $admins->perPage(),
                'total' => $admins->total(),
                'last_page' => $admins->lastPage(),
                'first_page_url' => $admins->url(1),
                'last_page_url' => $admins->url($admins->lastPage()),
                'next_page_url' => $admins->nextPageUrl(),
                'prev_page_url' => $admins->previousPageUrl(),
            ]
        );
    }

    public function store(StoreAdminRequest $request)
    {
         $data = $request->validated();

        $admin = $this->adminService->createAdmin($data);

        return $this->respondSuccess(new AdminResource($admin), 'Admin created successfully', 201);
    }

    public function show($id)
    {
        $admin = $this->adminService->findAdminOrFail($id);

        return $this->respondSuccess(new AdminResource($admin));
    }

    public function update(UpdateAdminRequest $request, $id)
    {
         $data = $request->validated();

        $updatedAdmin = $this->adminService->updateAdmin($id, $data);

        if ($updatedAdmin === null) {
            return $this->respondNotFound('Admin not found');
        }

        return $this->respondSuccess(new AdminResource($updatedAdmin), 'Admin updated successfully');
    }

    public function destroy($id)
    {
         $this->adminService->deleteAdminOrFail($id);

        return $this->respondSuccess(null, 'Admin deleted successfully');
    }

    public function logout()
    {
        auth('admin')->logout();

        return $this->respondSuccess(null, 'Successfully logged out');
    }
}
