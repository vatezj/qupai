<?php

declare(strict_types=1);

namespace Plugin\Joygoldmisson\Model;

use Hyperf\DbConnection\Model\Model as MineModel;

/**
 * @property int $category_id 分类ID
 * @property string $category_code 分类code
 * @property string $category_name 分类名称
 * @property int $created_by 创建人
 * @property string $created_time 创建时间
 * @property int $updated_by 更新人
 * @property string $updated_time 更新时间
 */
class FunleisureTaskCategory extends MineModel
{
    protected string $primaryKey = 'category_id';
    public bool $timestamps = false;


    protected ?string $connection = 'qu';
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'funleisure_task_category';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['category_id', 'category_code', 'category_name', 'created_by', 'created_time', 'updated_by', 'updated_time','desc','icon','min_price'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['category_id' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer'];
}
