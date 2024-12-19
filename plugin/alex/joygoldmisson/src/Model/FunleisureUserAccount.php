<?php

declare(strict_types=1);

namespace Plugin\Joygoldmisson\Model;

use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * @property int $user_account_id 用户账户记录ID
 * @property int $code 变动code
 * @property int $type 变动分类
 * @property string $price 变动金额
 * @property string $before_balance 变动前余额
 * @property string $after_balance 变动后余额
 * @property string $reason 变动内容
 * @property int $created_by 创建人
 * @property string $created_time 创建时间
 */
class FunleisureUserAccount extends MineModel
{
    protected string $primaryKey = 'user_account_id';
    public bool $timestamps = true;
    protected ?string $connection = 'qu';
    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'funleisure_user_account';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['updated_time','user_account_id', 'code', 'type', 'price', 'before_balance', 'after_balance', 'reason', 'created_by', 'created_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['user_account_id' => 'integer', 'code' => 'integer', 'type' => 'integer', 'price' => 'decimal:2', 'before_balance' => 'decimal:2', 'after_balance' => 'decimal:2', 'created_by' => 'integer'];

    /**
     * 定义 user 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function user() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\Plugin\Joygoldmisson\Model\FunleisureUser::class, 'user_id', 'created_by');
    }

}
