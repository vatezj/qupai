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

namespace  Plugin\Joygoldmisson\Middleware;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use Hyperf\Collection\Arr;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;
use Mine\Access\Attribute\Permission;
use Mine\Support\Traits\ParserRouterTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthMiddleware implements MiddlewareInterface
{
    use ParserRouterTrait;



    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // dd($request);
        var_dump(123);
        $this->check($request->getAttribute(Dispatched::class));
        // $request->user_id =  1;
        return $handler->handle($request);
    }

    private function check(Dispatched $dispatched): bool
    {
        $parseResult = $this->parse($dispatched->handler->callback);
        if (! $parseResult) {
            return true;
        }     
        return true;
    }


}
