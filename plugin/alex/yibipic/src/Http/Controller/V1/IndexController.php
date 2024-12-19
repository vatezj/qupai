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

namespace Plugin\Yibipic\Http\Controller\V1;


use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;

use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Post;

#[HyperfServer(name: 'http')]
 class IndexController extends AbstractController
{
    #[Post(
        path: '/yibipic/v1/login',
        operationId: 'yibipicLogin',
        summary: '艺壁之选',
        tags: ['艺壁之选'],
    )]
    public function login(): Result
    {
        return $this->success();
    }
}
