<?php

declare(strict_types=1);

namespace Plugin\Joygoldmisson\Model;

use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * @property int $id 用户关系表
 * @property int $pp_id 父父级别
 * @property int $ppp_id 父父级
 * @property int $p_id 父级
 * @property int $user_id 创建人
 * @property string $created_time 创建时间
 */
class FunleisureUserAccess extends MineModel
{

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';

    protected ?string $connection = 'qu';
    public bool $timestamps = true;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'funleisure_user_access';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'pp_id', 'ppp_id', 'p_id', 'user_id', 'created_time','updated_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'pp_id' => 'integer', 'ppp_id' => 'integer', 'p_id' => 'integer', 'user_id' => 'integer'];

    /**
     * 定义 user 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function user() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\Plugin\Joygoldmisson\Model\FunleisureUser::class, 'user_id', 'user_id');
    }

    /**
     * 定义 puser 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function puser() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\Plugin\Joygoldmisson\Model\FunleisureUser::class, 'user_id', 'p_id');
    }

    /**
     * 定义 ppuser 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function ppuser() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\Plugin\Joygoldmisson\Model\FunleisureUser::class, 'user_id', 'pp_id');
    }

    /**
     * 定义 pppuser 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function pppuser() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\App\FunLeisure\Model\FunleisureUser::class, 'user_id', 'ppp_id');
    }




}
