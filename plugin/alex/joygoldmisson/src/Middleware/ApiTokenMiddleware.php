<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Plugin\Joygoldmisson\Middleware;

use Mine\Jwt\JwtInterface;
use Mine\JwtAuth\Middleware\AbstractTokenMiddleware;
use Psr\Http\Message\ServerRequestInterface;

use Hyperf\Collection\Arr;
use Hyperf\Stringable\Str;


final class ApiTokenMiddleware extends AbstractTokenMiddleware
{
    public function getJwt(): JwtInterface
    {
        // 指定场景为 上一步新建的场景名称
        return $this->jwtFactory->get('qupai');
    }
    protected function getToken(ServerRequestInterface $request): string
    {
     
        if ($request->hasHeader('Authorization')) {
            return Str::replace('Bearer ', '', $request->getHeaderLine('Authorization'));
        }
        if ($request->hasHeader('token')) {
            return $request->getHeader('token');
        }
        if (Arr::has($request->getQueryParams(), 'token')) {
            return $request->getQueryParams()['token'];
        }
        return '';
    }
}