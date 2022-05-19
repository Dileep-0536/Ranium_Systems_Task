<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

      /**
     * Gloabel Validation Errors -
     * It will get the object of validation errors and return in JSON
     */

    public function validationErrors($validationErrors) {
        return response()->json(["status" => "error", "validation_errors" => $validationErrors]);
    }
}
