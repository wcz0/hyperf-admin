<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Admin;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authenticate implements MiddlewareInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Admin::permission()->authIntercept($request)) {
            return Admin::response()
                ->additional(['code' => 401])
                ->doNotDisplayToast()
                ->fail('Unauthorized');
        }

        Admin::permission()->checkUserStatus();

        return $handler->handle($request);
    }
}
