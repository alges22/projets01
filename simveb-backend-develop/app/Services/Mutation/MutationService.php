<?php

namespace App\Services\Mutation;

use App\Models\Mutation;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use App\Traits\CrudRepositoryTrait;
use App\Traits\UploadFile;

class MutationService
{

    use CrudRepositoryTrait;
    use UploadFile;

    public function __construct()
    {
        $this->initRepository(Mutation::class);
    }

}
