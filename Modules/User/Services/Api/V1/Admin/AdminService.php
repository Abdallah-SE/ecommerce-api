<?php

namespace Modules\User\Services\Api\V1\Admin;

use Modules\User\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Services\BaseService;
use Modules\Core\Exceptions\ExceptionFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Repositories\Api\V1\Interfaces\AdminRepositoryInterface;

class AdminService extends BaseService
{
    public const DEFAULT_PER_PAGE = 15;
    public const MAX_PER_PAGE = 100;

    protected AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Paginate admin users.
     */
    public function paginateAdmins(int $perPage, int $page): LengthAwarePaginator
    {
        $perPage = ($perPage < 1 || $perPage > self::MAX_PER_PAGE) ? self::DEFAULT_PER_PAGE : $perPage;
        $page = max($page, 1);

        return $this->adminRepository->paginate($perPage, $page);
    }

    /**
     * Create a new admin user.
     */
    public function createAdmin(array $data): Admin
    {
        return $this->createOrFail('Admin', $data, $this->adminRepository, function($data) {
            $data['password'] = Hash::make($data['password']);
            return $this->adminRepository->create($data);
        });
    }

    /**
     * Find an admin by ID or throw exception if not found.
     */
    public function findAdminOrFail(int $id): Admin
    {
        return $this->findOrFail('Admin', $id, $this->adminRepository);
    }

    /**
     * Update an admin user.
     */
    public function updateAdmin(int $id, array $data): Admin
    {
        return $this->updateOrFail('Admin', $id, $data, $this->adminRepository, function($id, $data) {
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            return $this->adminRepository->update($id, $data);
        });
    }

    /**
     * Delete an admin user or throw exception if not found.
     */
    public function deleteAdminOrFail(int $id): bool
    {
        return $this->deleteOrFail('Admin', $id, $this->adminRepository);
    }
}