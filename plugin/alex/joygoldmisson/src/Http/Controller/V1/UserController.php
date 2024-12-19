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


#[HyperfServer(name: 'http')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly FunleisureUserService $funleisureUserService,
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
    #[ResultResponse(new Result())]
    public function info(RequestInterface $request): Result
    {
        return $this->success($this->funleisureUserService->getUserInfo(['user_id' => $request->input('id')]));
    }
}
