<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


 /**
 *     security={
 *         {"BearerAuth": {}}
 *     },
 *     @OA\Info(
 *         title="Certif",
 *         description="Api de l'appli PENC",
 *         version="1.0.0"
 *     ),
 *     @OA\SecurityScheme(
 *         securityScheme="BearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT"
 *     ),
 *     consumes={"application/json"},
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
}
