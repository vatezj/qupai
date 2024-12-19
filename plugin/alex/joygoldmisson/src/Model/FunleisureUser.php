<?php

declare(strict_types=1);

namespace Plugin\Joygoldmisson\Model;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * @property int $user_id 用户ID
 * @property string $nickname 昵称
 * @property string $password 用户密码
 * @property string $phone 手机号
 * @property string $avatar 用户头像
 * @property int $user_status 用户状态
 * @property int $integral 用户积分
 * @property string $balance 用户余额
 * @property string $invitation_code 邀请码
 * @property string $created_time 创建时间
 * @property string $updated_time 更新时间
 */
class FunleisureUser extends MineModel
{
    use SoftDeletes;
    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
    protected string $primaryKey = 'user_id';

    protected ?string $connection = 'qu';
    public bool $timestamps = true;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'funleisure_user';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['user_id', 'nickname', 'password', 'phone', 'avatar', 'user_status', 'integral', 'balance', 'invitation_code', 'created_time', 'updated_time','deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['user_id' => 'integer', 'user_status' => 'integer', 'integral' => 'integer', 'balance' => 'decimal:2'];
}
