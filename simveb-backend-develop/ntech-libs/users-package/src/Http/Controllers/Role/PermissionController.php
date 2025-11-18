<?php
namespace Ntech\UserPackage\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use Ntech\UserPackage\Repositories\RoleRepository;

class PermissionController extends Controller
{
    public function __construct(
        private readonly RoleRepository $roleRepository
    )
    {
    }

    public function index()
    {
        return response([
            'modules' => $this->roleRepository->getModules(),
        ]);
    }
}
