<?php

declare(strict_types=1);

namespace Plugin\Joygoldmisson\Model;

use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * @property int $task_id 项目ID
 * @property int $task_status 任务状态
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
 * @property int $created_by 创建人
 * @property string $created_time 创建时间
 * @property int $updated_by 更新人
 * @property string $updated_time 更新时间
 */
class FunleisureTask extends MineModel
{
    protected string $primaryKey = 'task_id';
    public bool $timestamps = true;

    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'funleisure_task';


    protected ?string $connection = 'qu';


    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['task_id','task_step','task_verify', 'task_status', 'task_category', 'task_desc', 'task_name', 'task_tag', 'task_price', 'task_amount', 'task_limited_time', 'task_limited_area', 'task_limited_frequency', 'is_android', 'is_ios', 'created_by', 'created_time', 'updated_by', 'updated_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['task_id' => 'integer', 'task_status' => 'integer', 'task_category' => 'integer', 'task_price' => 'decimal:2', 'task_amount' => 'integer', 'task_limited_time' => 'integer', 'task_limited_area' => 'integer', 'task_limited_frequency' => 'integer', 'is_android' => 'integer', 'is_ios' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer'];

    /**
     * 定义 category 关联
     * @return \Hyperf\Database\Model\Relations\hasOne
     */
    public function category() : \Hyperf\Database\Model\Relations\hasOne
    {
        return $this->hasOne(\Plugin\Joygoldmisson\Model\FunleisureTaskCategory::class, 'category_id', 'task_category');
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
