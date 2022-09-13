<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\CubeController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {

    // Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    //     return $request->user();
    // });

    Route::post('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken($request->token_name);

        return ['token' => $token->plainTextToken];
    });



    Route::group(['prefix' => 'user'], function () {
        Route::get('/me', function (Request $request) {
            return $request->user();
        })->middleware('auth:api');
        Route::post('/login', [UserController::class, 'login']);
        Route::post('/register', [UserController::class, 'register']);
        Route::post('/logout', [UserController::class, 'logout']);
    });

    Route::group(['prefix' => 'organizations'], function () {
        Route::get('/', [OrganizationController::class, 'index'])->middleware('can:viewAny,App\Models\Organization');

        Route::group(['prefix' => '{organization}'], function () {
            Route::get('/', [OrganizationController::class, 'show'])->middleware('can:view,organization');
            Route::group(['prefix' => 'cubes'], function () {
                Route::get('/', [CubeController::class, 'index'])->middleware('can:viewAny,App\Models\Cube');
                Route::get('/{cube}', [CubeController::class, 'show'])->middleware('can:view,cube');
                Route::post('/', [CubeController::class, 'create'])->middleware('can:create,App\Models\Cube');
                Route::put('/{cube}', [CubeController::class, 'update'])->middleware('can:update,cube');
                Route::delete('/{cube}', [CubeController::class, 'destroy'])->middleware('can:delete,cube');
            });
        });
        Route::post('/', [OrganizationController::class, 'create'])->middleware('can:create,App\Models\Organization');
        Route::put('/{organization}', [OrganizationController::class, 'update'])->middleware('can:update,organization');
        Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->middleware('can:delete,organization');
    });
});
