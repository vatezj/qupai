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

namespace Plugin\Joygoldmisson\Http\Controller\V1;

use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation as OA;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\HttpServer\Contract\RequestInterface;
use Mine\Swagger\Attributes\ResultResponse;
use Plugin\Joygoldmisson\Service\FunleisureUserService;
use Hyperf\HttpServer\Annotation\Middleware;
use Plugin\Joygoldmisson\Middleware\AuthMiddleware;
use Plugin\Joygoldmisson\Middleware\ApiTokenMiddleware;
use function Hyperf\Support\env;




use Plugin\Joygoldmisson\Http\CurrentUser;

#[HyperfServer(name: 'http')]
class UserController extends AbstractController
{




    public function __construct(
        private readonly FunleisureUserService $funleisureUserService,
        private readonly CurrentUser $user
    ) {}

    #[Post(
        operationId: 'joygoldmissonLogin',
        path: '/joygoldmisson/v1/user/login',
        summary: '登陆',
        tags: ['趣金派'],
    )]
    #[OA\RequestBody(content: new OA\JsonContent(
        title: '登录请求参数',
        required: ['phone', 'code'],
        example: '{"phone":"15607357724","code":"123456"}'
    ))]
    #[ResultResponse(new Result())]
    public function login(RequestInterface $request): Result
    {
        return $this->success($this->funleisureUserService->doLogin($request->all()));
    }
    #[Get(
        path: '/joygoldmisson/v1/getCodeImg',
        summary: '获取图形验证码',
        tags: ['趣金派'],
    )]
    #[ResultResponse(new Result())]
    public function getCode(): Result
    {
        return $this->success(\Ella123\HyperfCaptcha\captcha_create());
    }
    #[Get(
        path: '/joygoldmisson/v1/user/info',
        summary: '趣金派',
        tags: ['趣金派'],
    )]

    #[OA\QueryParameter(name: 'id')]
    #[OA\QueryParameter(name: 'token')]
    #[ResultResponse(new Result())]


    #[Middleware(ApiTokenMiddleware::class, 100)]
    #[Middleware(AuthMiddleware::class, 99)]
    public function info(RequestInterface $request): Result
    {

        // return $this->success($this->user->id());
        return $this->success($this->funleisureUserService->getUserInfo(['user_id' => $this->user->id()]));
    }




    #[Post(
        path: '/joygoldmisson/v1/user/rechage',
        summary: '用户充值',
        tags: ['趣金派'],
    )]
    #[OA\QueryParameter(name: 'token', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzQ2ODA4MTYsIm5iZiI6MTczNDY4MDgxNiwiZXhwIjoxNzM0Njg0NDE2LCJqdGkiOiI0VzEifQ.72UMh41ZmivO7aZth4uGgPSEnjjk0b88kxcEhC8EZNI')]
   
    #[OA\RequestBody(content: new OA\JsonContent(
        title: '充值请求参数',
        required: ['amount', 'type'],
        example: '{"amount":"10000","type":"1"}'
    ))]
    #[ResultResponse(new Result())]

    #[Middleware(ApiTokenMiddleware::class, 100)]
    #[Middleware(AuthMiddleware::class, 99)]
    public function rechage(RequestInterface $request): Result
    {
        return $this->success($this->funleisureUserService->rechage($request->all(), $this->user->id()));
    }
}
