<?php

declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace Plugin\Joygoldmisson\Service;

use Plugin\Joygoldmisson\Model\FunleisureUserAccess;
use Plugin\Joygoldmisson\Model\FunleisureUserAccount;
use Hashids\Hashids;
use Plugin\Joygoldmisson\Model\FunleisureUser;
use App\Service\IService;



use Mine\Jwt\Factory;
use Mine\Jwt\JwtInterface;
use Mine\JwtAuth\Event\UserLoginEvent;
use Mine\JwtAuth\Interfaces\CheckTokenInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\UnencryptedToken;

use App\Exception\BusinessException;
use App\Exception\JwtInBlackException;

/**
 * 用户表服务类
 */
final  class FunleisureUserService extends IService implements CheckTokenInterface
{


    /**
     * @var string jwt场景
     */
    private string $jwt = 'qupai';

    public function __construct(

        protected readonly Factory $jwtFactory,
        protected readonly EventDispatcherInterface $dispatcher
    ) {}

    public function getUserInfo($params)
    {
        $user = FunleisureUser::query()->where('user_id', $params['user_id'])->first();
        return $user;
    }
    public function doLogin($params)
    {
        $user = FunleisureUser::query()->where('phone', $params['phone'])->first();
        if (!$user) {
            //注册一个
            $hashids = new Hashids('vate96', 6, 'abcdefghijklmnopqrstuvwxyz1234567890');
            $usermodel = new FunleisureUser();
            $usermodel->phone = $params['phone'];
            $usermodel->password = md5('111111');
            $usermodel->avatar = "1111";
            $usermodel->save();
            $usermodel->invitation_code = $hashids->encode($usermodel->user_id);
            $usermodel->update();
            //user表
            if ($params['invitation_code'] == '888888') {
                $accessModel =  new FunleisureUserAccess();
                $accessModel->user_id = $usermodel->user_id;
                $accessModel->p_id = 0;
                $accessModel->pp_id = 0;
                $accessModel->ppp_id = 0;
                $accessModel->save();
            } else {
                $invitationUser = FunleisureUser::query()->where('invitation_code', $params['invitation_code'])->first();
                if ($invitationUser) {
                    $accessModel = FunleisureUserAccess::query()->where('user_id', $invitationUser->user_id)->first();
                    if (!$accessModel) {
                        $createAccess = new FunleisureUserAccess();
                        $createAccess->user_id = $usermodel->user_id;
                        $createAccess->p_id = 0;
                        $createAccess->pp_id = 0;
                        $createAccess->ppp_id = 0;
                        $createAccess->save();
                    } else {
                        $createAccess = new FunleisureUserAccess();
                        $createAccess->user_id = $usermodel->user_id;
                        $createAccess->p_id = $accessModel->user_id;
                        $createAccess->pp_id = $accessModel->p_id;
                        $createAccess->ppp_id = $accessModel->pp_id;
                        $createAccess->save();
                    }
                } else {
                    $createAccess = new FunleisureUserAccess();
                    $createAccess->user_id = $usermodel->user_id;
                    $createAccess->p_id = 0;
                    $createAccess->pp_id = 0;
                    $createAccess->ppp_id = 0;
                    $createAccess->save();
                }
            }
            $info =  ['id' => $usermodel->user_id];
        } else {
            $info =   ['id' => $user->user_id];
        }
        $hashids = new Hashids('vate96', 6, 'abcdefghijklmnopqrstuvwxyz1234567890');
        $uid = $hashids->encode($info['id']);
        $jwt = $this->getJwt();
        return [
            'access_token' => $jwt->builderAccessToken((string) $uid)->toString(),
            'refresh_token' => $jwt->builderRefreshToken((string) $uid)->toString(),
            'expire_at' => (int) $jwt->getConfig('ttl', 0),
        ];
    }

    public function checkJwt(UnencryptedToken $token): void
    {
        $this->getJwt()->hasBlackList($token) && throw new JwtInBlackException();
    }

    public function logout(UnencryptedToken $token): void
    {
        $this->getJwt()->addBlackList($token);
    }

    public function getJwt(): JwtInterface
    {
        return $this->jwtFactory->get($this->jwt);
    }

    /**
     * @return array<string,int|string>
     */
    public function refreshToken(UnencryptedToken $token): array
    {
        return value(static function (JwtInterface $jwt) use ($token) {
            $jwt->addBlackList($token);
            return [
                'access_token' => $jwt->builderAccessToken($token->claims()->get(RegisteredClaims::ID))->toString(),
                'refresh_token' => $jwt->builderRefreshToken($token->claims()->get(RegisteredClaims::ID))->toString(),
                'expire_at' => (int) $jwt->getConfig('ttl', 0),
            ];
        }, $this->getJwt());
    }



    public function doRechage($params)
    {
        //目前只支持支付宝
        $userAcount = FunleisureUserAccount::query()->where('created_by', $params['user_id'])->first();
        $userAcounts = new FunleisureUserAccount();
        if (!$userAcount) {
            $userAcounts->created_by = $params['user_id'];
            $userAcounts->before_balance = 0;
            $userAcounts->after_balance = $params['amount'];
            $userAcounts->price = $params['amount'];
            $userAcounts->reason = '用户充值';
            $userAcounts->code = '0001';
            $userAcounts->type = 1; //1 + 2 -
            $userAcounts->save();
        } else {
            $userAcounts->created_by = $params['user_id'];
            $userAcounts->before_balance = $userAcount->after_balance;
            $userAcounts->after_balance = $params['amount'] + $userAcount->after_balance;
            $userAcounts->price = $params['amount'];
            $userAcounts->reason = '用户充值';
            $userAcounts->code = '0001';
            $userAcounts->type = 1; //1 + 2 -
            $userAcounts->save();
        }
        return ['id' => $userAcounts->user_account_id];
    }
}
