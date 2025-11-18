<?php

namespace Ntech\ActivityLogPackage\Http\Controllers\ActivityLog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ntech\ActivityLogPackage\Services\ActivityLogService;

class ActivityLogController extends Controller
{

    public function __construct(private readonly ActivityLogService $activityLogRepository)
    {
    }

    public function index()
    {

        $causers = $this->activityLogRepository->getCausersList();
        $actions = $this->activityLogRepository->getActionsList();
        $logs = $this->activityLogRepository->getAll();

        return response(compact('causers','actions','logs'));
    }
    public function show($id)
    {

        $log = $this->activityLogRepository->get($id);

        return response(compact('log'));
    }
}
