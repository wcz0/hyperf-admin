<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use Hyperf\HttpServer\Contract\ResponseInterface;

class RoleController extends Controller
{
    /**
     * @param array $data
     * @param string $message
     * @param int $code
     * @param int $status
     *
     */
    public function success(array $data = [], string $message = 'success', int $code = 200, int $status = 0)
    {
        return $this->response->json([
            'status' => 0,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * @param string $message
     * @param int $code
     * @param int $status
     * @param array $data
     *
     */
    public function fail(string $message = 'fail', int $code = 500, int $status = 1, array $data = [])
    {
        return $this->response->json([
            'status' => 1,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}
