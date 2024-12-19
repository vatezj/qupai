<?php

declare(strict_types=1);

namespace Plugin\Joygoldmisson\Model;

use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * @property int $verify_id 步骤主键
 * @property int $join_id 任务ID
 * @property int $verify_type 步骤类型
 * @property string $verify_desc 步骤名称
 * @property string $verify_pic 步骤图片
 * @property string $verify_text 步骤文字
 * @property int $updated_by 更新人
 * @property int $created_by 创建人
 * @property string $created_time 创建时间
 * @property string $updated_time 更新时间
 */
class FunleisureTaskVerify extends MineModel
{
    protected string $primaryKey = 'verify_id';

    protected ?string $connection = 'qu';
    public bool $timestamps = false;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'funleisure_task_verify';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['verify_id', 'join_id', 'verify_type', 'verify_desc', 'verify_pic', 'verify_text', 'updated_by', 'created_by', 'created_time', 'updated_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['verify_id' => 'integer', 'join_id' => 'integer', 'verify_type' => 'integer', 'updated_by' => 'integer', 'created_by' => 'integer'];


    /**
     * 定义 attend 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function attend() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\Plugin\Joygoldmisson\Model\FunleisureTaskJoin::class, 'join_id', 'join_id');
    }

    /**
     * 定义 user 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function user() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\Plugin\Joygoldmisson\Model\FunleisureUser::class, 'user_id', 'created_by');
    }
}
