<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CubeController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SiloFileController;
use App\Http\Controllers\SiloFolderController;
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
        Route::get('/me', [UserController::class, 'me'])->middleware('auth:api');
        Route::post('/login', [UserController::class, 'login']);
        Route::post('/register', [UserController::class, 'register']);
        Route::post('/logout', [UserController::class, 'logout']);
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [CategoryController::class, 'index'])->middleware('auth:api');
        Route::post('/', [CategoryController::class, 'store'])->middleware('auth:api');
    });

    Route::group(['prefix' => 'organizations'], function () {
        Route::get('/', [OrganizationController::class, 'index'])->middleware('can:viewAny,App\Models\Organization');

        Route::group(['prefix' => '{organization}'], function () {
            Route::get('/', [OrganizationController::class, 'show'])->middleware('can:view,organization');
            Route::group(['prefix' => 'cubes'], function () {
                Route::get('/', [CubeController::class, 'index'])->middleware('can:viewAny,App\Models\Cube');
                Route::get('/{cube}', [CubeController::class, 'show'])->middleware('can:view,cube');
                Route::post('/', [CubeController::class, 'store'])->middleware('can:create,App\Models\Cube');
                Route::put('/{cube}', [CubeController::class, 'update'])->middleware('can:update,cube');
                Route::delete('/{cube}', [CubeController::class, 'destroy'])->middleware('can:delete,cube');
            });

            Route::group(['prefix' => 'folders'], function () {
                Route::get('/', [SiloFolderController::class, 'index'])->middleware('can:viewAny,App\Models\SiloFolder');
                Route::post('/', [SiloFolderController::class, 'store'])->middleware('can:create,App\Models\SiloFolder');
                Route::put('/{folder}', [SiloFolderController::class, 'update'])->middleware('can:update,folder');
                Route::delete('/{folder}', [SiloFolderController::class, 'destroy'])->middleware('can:delete,folder');

                Route::get('/{folder}/files', [SiloFileController::class, 'index'])->middleware('can:viewAny,App\Models\SiloFile');
                Route::get('/{folder}/files/attributes', [SiloFileController::class, 'showMultipleAttributes']);
                Route::get('/{folder}/files/{file}', [SiloFileController::class, 'show'])->middleware('can:view,file');
                Route::get('/{folder}/files/{file}/attributes', [SiloFileController::class, 'showAttributes'])->middleware('can:view,file');
                Route::post('/{folder}/files', [SiloFileController::class, 'store'])->middleware('can:create,App\Models\SiloFile');
                Route::put('/{folder}/files/{file}', [SiloFileController::class, 'update'])->middleware('can:update,file');
                Route::delete('/{folder}/files/{file}', [SiloFileController::class, 'destroy'])->middleware('can:delete,file');
                Route::get('/{folder}/files/{file}/download', [SiloFileController::class, 'download'])->middleware(['can:view,file']);

                Route::get('/{folder}', [SiloFolderController::class, 'show'])->middleware('can:view,folder');
            });
        });
        Route::post('/', [OrganizationController::class, 'create'])->middleware('can:create,App\Models\Organization');
        Route::put('/{organization}', [OrganizationController::class, 'update'])->middleware('can:update,organization');
        Route::delete('/{organization}', [OrganizationController::class, 'destroy'])->middleware('can:delete,organization');
    });
});
