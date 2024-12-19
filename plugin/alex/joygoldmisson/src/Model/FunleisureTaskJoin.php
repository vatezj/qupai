<?php

declare(strict_types=1);

namespace Plugin\Joygoldmisson\Model;
use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * @property int $join_id 参加ID
 * @property int $task_id 任务id
 * @property string $join_time 参加时间
 * @property string $join_detail 参加详情
 * @property int $join_status 参加状态
 * @property string $rejection 驳回理由
 * @property int $created_by 创建人
 * @property string $created_time 创建时间
 * @property string $updated_by 更新人
 * @property string $updated_time 更新时间
 * @property string $store_id 商铺id
 */
class FunleisureTaskJoin extends MineModel
{
    protected string $primaryKey = 'join_id';
    public bool $timestamps = true;


    protected ?string $connection = 'qu';

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'funleisure_task_join';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['join_id', 'task_id', 'store_id','join_detail', 'join_status', 'rejection', 'created_by', 'created_time', 'updated_by', 'updated_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['store_id'=>'integer',   'join_id' => 'integer', 'task_id' => 'integer', 'join_status' => 'integer', 'created_by' => 'integer'];

    /**
     * 定义 task 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function task() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\Plugin\Joygoldmisson\Model\FunleisureTask::class, 'task_id', 'task_id');
    }

    /**
     * 定义 user 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function store() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\Plugin\Joygoldmisson\Model\FunleisureUser::class, 'user_id', 'store_id');
    }





    /**
     * 定义 user 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function user() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\App\FunLeisure\Model\FunleisureUser::class, 'user_id', 'created_by');
    }




}
