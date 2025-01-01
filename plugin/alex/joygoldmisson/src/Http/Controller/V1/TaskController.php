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
use Plugin\Joygoldmisson\Http\CurrentUser;
use Plugin\Joygoldmisson\Service\FunleisureTaskService;
use Hyperf\Swagger\Annotation\JsonContent;
use OpenApi\Attributes\RequestBody;

#[HyperfServer(name: 'http')]
class TaskController extends AbstractController
{
    public function __construct(
        private readonly FunleisureUserService $userService,
        private readonly FunleisureTaskService $taskService,
        private readonly CurrentUser $user
    ) {}
    #[Get(
        path: '/joygoldmisson/v1/task/list',
        summary: '任务列表',
        tags: ['趣金派'],
    )]
    #[OA\QueryParameter(name: 'token', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzQ2ODA4MTYsIm5iZiI6MTczNDY4MDgxNiwiZXhwIjoxNzM0Njg0NDE2LCJqdGkiOiI0VzEifQ.72UMh41ZmivO7aZth4uGgPSEnjjk0b88kxcEhC8EZNI')]
    #[OA\QueryParameter(name: 'page', example: 1)]
    #[OA\QueryParameter(name: 'size', example: 10)]
    #[ResultResponse(new Result())]
    #[Middleware(ApiTokenMiddleware::class, 100)]
    #[Middleware(AuthMiddleware::class, 99)]
    public function list(RequestInterface $request): Result
    {
        $params = $request->all();
        return $this->success($this->taskService->getTaskList($params));
    }



    #[Get(
        path: '/joygoldmisson/v1/task/info',
        summary: '任务详情',
        tags: ['趣金派'],
    )]
    #[OA\QueryParameter(name: 'token', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzQ2ODA4MTYsIm5iZiI6MTczNDY4MDgxNiwiZXhwIjoxNzM0Njg0NDE2LCJqdGkiOiI0VzEifQ.72UMh41ZmivO7aZth4uGgPSEnjjk0b88kxcEhC8EZNI')]
    #[OA\QueryParameter(name: 'id', description: '任务id', example: 'goj')]
    #[ResultResponse(new Result())]
    #[Middleware(ApiTokenMiddleware::class, 100)]
    #[Middleware(AuthMiddleware::class, 99)]
    public function info(RequestInterface $request): Result
    {
        $params = $request->all();
        return $this->success($this->taskService->getTaskInfo($params['id']));
    }



    #[Post(
        path: '/joygoldmisson/v1/task/publish',
        summary: '发布任务',
        tags: ['趣金派'],
    )]
    #[OA\QueryParameter(name: 'token', example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MzQ2ODA4MTYsIm5iZiI6MTczNDY4MDgxNiwiZXhwIjoxNzM0Njg0NDE2LCJqdGkiOiI0VzEifQ.72UMh41ZmivO7aZth4uGgPSEnjjk0b88kxcEhC8EZNI')]
    /**
     * @property int $task_category 任务分类
     * @property string $task_desc 活动说明
     * @property string $task_name 项目名称
     * @property string $task_tag 任务标签
     * @property string $task_price 任务单价
     * @property int $task_amount 任务数量
     * @property int $task_limited_time 限制时间
     * @property int $task_limited_area 限制地区
     * @property int $task_limited_frequency 限制次数
     * @property int $is_android 是否支持安卓
     * @property int $is_ios 是否支持ios
     */
    #[OA\RequestBody(content: new OA\JsonContent(properties: [
        new OA\Property(property: 'task_category', title: '任务分类', rules: 'required', example: '1'),
        new OA\Property(property: 'task_desc', title: '活动说明', rules: 'required', example: '活动说明'),
        new OA\Property(property: 'task_name', title: '项目名称', rules: 'required', example: '项目名称'),
        new OA\Property(property: 'task_tag', title: '任务标签', rules: 'required', example: '任务标签'),
        new OA\Property(property: 'task_price', title: '任务价格', rules: 'required', example: '2'),
        new OA\Property(property: 'task_num', title: '任务数量', rules: 'required', example: '40'),
        new OA\Property(property: 'task_limited_time', title: '限制时间', rules: 'required', example: '1'),
        new OA\Property(property: 'task_limited_area', title: '限制地区', rules: 'required', example: '0'),
        new OA\Property(property: 'task_limited_frequency', title: '限制次数', rules: 'required', example: '1'),
        new OA\Property(property: 'is_android', title: '是否支持安卓', rules: 'required', example: '1'),
        new OA\Property(property: 'is_ios', title: '是否支持ios', rules: 'required', example: '1'),
        new OA\Property(property: 'task_step', title: '任务步骤', rules: 'required', example: '项目名称'),
        new OA\Property(property: 'task_verify', title: '任务验证', rules: 'required', example: '项目名称'),
    ]))]
    #[ResultResponse(new Result())]
    #[Middleware(ApiTokenMiddleware::class, 100)]
    #[Middleware(AuthMiddleware::class, 99)]
    public function publish(RequestInterface $request): Result
    {
        $params = $request->all();
        return $this->success($this->taskService->addTask($params));
        // return $this->success($params);
    }




    #[Post(
        path: '/joygoldmisson/v1/task/join',
        summary: '参加任务',
        tags: ['趣金派'],
        security: [['Bearer' => [], 'ApiKey' => []]],
    )]
    /**
     * @property int $task_category 任务分类
     * @property string $task_desc 活动说明
     * @property string $task_name 项目名称
     * @property string $task_tag 任务标签
     * @property string $task_price 任务单价
     * @property int $task_amount 任务数量
     * @property int $task_limited_time 限制时间
     * @property int $task_limited_area 限制地区
     * @property int $task_limited_frequency 限制次数
     * @property int $is_android 是否支持安卓
     * @property int $is_ios 是否支持ios
     */
    #[OA\RequestBody(content: new OA\JsonContent(properties: [

        new OA\Property(property: 'task_id', title: '任务id', rules: 'required', example: '78'),
    ]))]
    
    #[ResultResponse(new Result())]
    #[Middleware(ApiTokenMiddleware::class, 100)]
    #[Middleware(AuthMiddleware::class, 99)]
    public function join(RequestInterface $request): Result
    {
        $params = $request->all();
        return $this->success($this->taskService->joinTask($params));
        // return $this->success($params);
    }



    #[Post(
        path: '/joygoldmisson/v1/task/submit',
        summary: '提交任务',
        tags: ['趣金派'],
        security: [['Bearer' => [], 'ApiKey' => []]],
    )]
    #[OA\RequestBody(content: new OA\JsonContent(properties: [

        new OA\Property(property: 'task_id', title: '任务id', rules: 'required', example: '78'),
        new OA\Property(property: 'joininfo', title: '提交内容', rules: 'required', example: '78'),
    ]))]
    
    #[ResultResponse(new Result())]
    #[Middleware(ApiTokenMiddleware::class, 100)]
    #[Middleware(AuthMiddleware::class, 99)]
    public function submit(RequestInterface $request): Result
    {
        $params = $request->all();
        return $this->success($this->taskService->submitTask($params));
    }



    
}
