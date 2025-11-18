<?php

namespace App\Http\Controllers\Duplicate;

use App\Http\Controllers\Controller;
use App\Models\PlateDuplicate;

class PlateDuplicateController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(PlateDuplicate $plateDuplicate)
    {
        return response($plateDuplicate->load($plateDuplicate::relations()));
    }

}
