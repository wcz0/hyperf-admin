<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use Hyperf\HttpServer\Contract\ResponseInterface;

class Controller extends AbstractController
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
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * @param string $message
     * @param array $data
     * @param int $code
     * @param int $status
     *
     */
    public function fail(string $message = 'fail', array $data = [], int $code = 500, int $status = 1)
    {
        return $this->response->json([
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}
