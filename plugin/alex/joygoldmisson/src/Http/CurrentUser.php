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

namespace  Plugin\Joygoldmisson\Http;


use Lcobucci\JWT\Token\RegisteredClaims;
use Mine\Jwt\Traits\RequestScopedTokenTrait;
use Plugin\Joygoldmisson\Service\FunleisureUserService;
use Plugin\Joygoldmisson\Service\HashidsHelper;

final class CurrentUser
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly FunleisureUserService $userService
    ) {}

    public function user(): ?FunleisureUserService
    {
        return $this->userService->getUserInfo(['id' => $this->id()]);
    }

    public function refresh(): array
    {
        return $this->userService->refreshToken($this->getToken());
    }

    public function encodeid()
    {
        $uid = $this->getToken()->claims()->get(RegisteredClaims::ID);
        return  $uid;
    }
    public function id()
    {
        $uid = HashidsHelper::decode($this->getToken()->claims()->get(RegisteredClaims::ID));
        return  $uid[0];
    }
}
