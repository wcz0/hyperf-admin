<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Controller\Admin\AuthController;
use App\Controller\Admin\IndexController;
use App\Controller\Admin\UserController;
use App\Middleware\Authenticate;
use App\Middleware\Permission;
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});

Router::addGroup('/admin-api', function () {
    Router::get('/login', [AuthController::class, 'loginPage']);
    Router::post('login', [AuthController::class, 'login']);
    Router::get('logout', [AuthController::class, 'logout']);
    Router::post('register', [AuthController::class, 'register']);
    Router::post('_setting', [IndexController::class, 'saveSetting']);
    Router::get('_setting', [IndexController::class, 'getSetting']);
    Router::get('no-content', [IndexController::class, 'noContent']);
    Router::get('_download_export', [IndexController::class, 'downloadExport']);

    Router::addGroup('/', function () {
        Router::post('upload_image', [IndexController::class, 'imageUpload']);
        Router::post('upload_file', [IndexController::class, 'fileUpload']);
        Router::get('menus', [IndexController::class, 'getMenus']);
        Router::get('current-user', [AuthController::class, 'currentUser']);
        Router::get('upload_rich', [AuthController::class, 'users']);
        // Router::get(route: 'user_setting', [UserController::class, 'getUserSetting']);
        Router::get('user_setting', [UserController::class, 'getUserSetting']);
        Router::put('user_setting', [UserController::class, 'putUserSetting']);

        Router::addGroup('', function () {
            Router::get('admin_users', [UserController::class, 'index']);
            Router::post('admin_users', [UserController::class, 'store']);
            Router::get('admin_users/{id}', [UserController::class, 'show']);
            Router::put('admin_users/{id}', [UserController::class, 'update']);
            Router::delete('admin_users/{id}', [UserController::class, 'destroy']);
        }, [
            'middleware' => [
                Permission::class
            ]
        ]);
    }, [
        'middleware' => [
            Authenticate::class
        ]
    ]);
});