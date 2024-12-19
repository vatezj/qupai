<?php

namespace Plugin\Joygoldmisson\Middleware;

use Hashids\Hashids;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCode;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeader('auth')[0] ?? null;
        if ($token && user()->check($token)) {
            $hashids = new Hashids('vate96', 6, 'abcdefghijklmnopqrstuvwxyz1234567890');
            $info = user()->getUserInfo($token);
            $requestWithId =  $request->withParsedBody(array_merge(
                $request->getParsedBody(),
                ['id'=>$info['id']],
                ['user_id' =>  $hashids->decode($info['id'])[0]]
            ));
            return $handler->handle($requestWithId);
        } else {
            return throw new NormalStatusException($request, MineCode::RESOURCE_STOP);
        }

    }

}