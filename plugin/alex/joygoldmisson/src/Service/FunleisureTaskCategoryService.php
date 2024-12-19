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


use Plugin\Joygoldmisson\Model\FunleisureTaskCategory;
use App\Service\IService;

/**
 * 任务分类服务类
 */
class FunleisureTaskCategoryService extends IService
{

    public function __construct()
    {

    }

    public  function getCategoryList()
    {
         return  FunleisureTaskCategory::query()->select('desc','icon','category_name','category_id','min_price')->get();
    }
}