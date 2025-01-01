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

namespace  Plugin\Joygoldmisson\Service;


use Plugin\Joygoldmisson\Model\FunleisureTask;
use Plugin\Joygoldmisson\Model\FunleisureTaskJoin;
use Plugin\Joygoldmisson\Model\FunleisureUserAccount;
use App\Service\IService;
use phpDocumentor\Reflection\Types\This;
use Plugin\Joygoldmisson\Http\Common\TaskCode;

use function Hyperf\Support\retry;
use Plugin\Joygoldmisson\Http\CurrentUser;

/**
 * 任务表服务类
 */
class FunleisureTaskService extends IService
{


    public function __construct(private readonly CurrentUser $user) {}


    public function getTaskList($params)
    {
        $list = FunleisureTask::query()->with(['user' => function ($query) {
            return $query->select('avatar', 'nickname', 'user_id');
        }])->with(['category' => function ($query) {
            return $query->select('category_id', 'category_name', 'category_code');
        }])->paginate((int)$params['size'] | 10);
        return $list;
    }

    public function getTaskInfo($id)
    {
        $id = HashidsHelper::decode($id)[0];
        $list = FunleisureTask::query()->with(['user' => function ($query) {
            return $query->select('avatar', 'nickname', 'user_id');
        }])->with(['category' => function ($query) {
            return $query->select('category_id', 'category_name', 'category_code');
        }])->find($id);

        $findJoin = FunleisureTaskJoin::where([
            ['task_id', '=', $id],
            ['created_by', '=', $this->user->id()]
        ])->first();
        unset($list['user']['user_id']);
        return ['info' => $list, 'joinInfo' => $findJoin];
    }


    public function verifyTask($params): array
    {
        $data = FunleisureTaskJoin::query()->where('join_id', $params['join_id'])->first();
        if ($data->join_status == 2) {
            return ['code' => 20001, 'msg' => '任务已经完成'];
        }
        if (!$data->task) {
            return ['code' => 20001, 'msg' => '任务已经失效'];
        }
        if ($data->task->task_status != 1) {
            return ['code' => 20001, 'msg' => '任务已经禁用'];
        }
        if (!$data->user) {
            return ['code' => 20001, 'msg' => '提交用户不存在'];
        }
        if ($data->user->user_status != 1) {
            return ['code' => 20001, 'msg' => '提交用户已被禁用'];
        }

        if ($params['type'] == 1) {
            $data->join_status = 2;
        }
        if ($params['type'] == 2) {
            $data->join_status = 3;
            $data->rejection = $params['remake'];
        }
        $data->save();
        if ($params['type'] == 1) {
            //todo 给用户加钱
            $userAcount = FunleisureUserAccount::query()->where('created_by', $data->created_by)->first();
            $userAcounts = new FunleisureUserAccount();
            $userAcounts->created_by = $data->created_by;
            $userAcounts->reason = '完成任务' . $params['join_id'];
            $userAcounts->code = '20000';
            $userAcounts->type = 1; //1 + 2 -
            $userAcounts->price = $data->task->task_price;
            if (!$userAcount) {
                $userAcounts->before_balance = 0;
                $userAcounts->after_balance = $data->task->task_price;
            } else {
                $userAcounts->before_balance = $userAcount->after_balance;
                $userAcounts->after_balance = $data->task->task_price + $userAcount->after_balance;
            }
            $userAcounts->save();
            return ['id' => $userAcounts->user_account_id];
        }
        return ['code' => 10000, 'msg' => '提交成功'];
    }

    public function joinTask($params)
    {
        //查看任务情况
        $taskData = FunleisureTask::query()->where('task_id', $params['task_id'])->first();
        if ($taskData) {
            //查询是否参加
            $isJoin = FunleisureTaskJoin::query()->where([
                ['task_id', '=', $params['task_id']],
                ['created_by', '=', $this->user->id()]
            ])->first();
            if( $isJoin){
                return ['code' => TaskCode::SUCCESS, 'msg' => '已经参加该任务'];
            }
            //查询是否已经满员
            $allNum = $taskData->task_amount;
            $num = FunleisureTaskJoin::query()->where('task_id', $params['task_id'])->count();
            if ($num < $allNum) {
                //进行参加
                $newJoin = new FunleisureTaskJoin();
                $newJoin->task_id = $params['task_id'];
                $newJoin->created_by = $this->user->id();
                $newJoin->store_id = $taskData['created_by'];
                $newJoin->save();
                return ['code' => 20000, 'msg' => '参加成功，尽快提交任务'];
            }
            return ['code' => 10002, 'msg' => '任务已经参加完成'];
        } else {
            return ['code' => 10001, 'msg' => '任务不存在或已删除'];
        }
    }


    public function submitTask($params)
    {

        //查看任务情况
        $taskData = FunleisureTask::query()->where('task_id', $params['task_id'])->first();
        if ($taskData) {
            //查询是否参加
            $isJoin = FunleisureTaskJoin::query()->where([
                ['task_id', '=', $params['task_id']],
                ['created_by', '=', $this->user->id()]
            ])->first();
            if(!$isJoin){
                return ['code' => 30001, 'msg' => '请先报名后再进行操作'];
            }
            $isJoin->join_detail = json_encode($params['verify_data']);
            $isJoin->join_status = 2;
            $isJoin->save();
            return ['code' => 20001, 'msg' => '提交成功，请等待审核'];
        } else {
            return ['code' => 10001, 'msg' => '任务不存在或已删除'];
        }
    }


    //发布任务
    public function addTask($params)
    {
        //查询余额z

        $userAccount = FunleisureUserAccount::query()->orderByDesc('created_time')->where('created_by',  $this->user->id())->first();
        if (!$userAccount) {
            // return false;
            return [
                'code' => 10001,
                'msg' => '用户不存在'
            ];
        } else {

            if ($userAccount->after_balance < ((float)$params['task_price'] * (int)$params['task_num'])) {
                return [
                    'code' => 10001,
                    'msg' => '余额不足'
                ];
            }
        }

        // return  $userAccount;
        $taskModel = new FunleisureTask();
        $taskModel->is_android = $params['is_android'];
        $taskModel->is_ios = $params['is_ios'];
        $taskModel->task_name = $params['task_name'];
        $taskModel->task_desc = $params['task_desc'];
        $taskModel->task_tag = $params['task_tag'];
        $taskModel->task_category = $params['task_category'];
        $taskModel->task_price = $params['task_price'];
        $taskModel->task_step = json_encode($params['task_step']);
        $taskModel->task_verify = json_encode($params['task_verify']);
        $taskModel->task_amount = $params['task_num'];
        $taskModel->task_limited_frequency = $params['task_limited_frequency'];
        $taskModel->task_limited_area = $params['task_limited_area'];
        $taskModel->task_limited_time = $params['task_limited_time'];
        $taskModel->created_by = $this->user->id();
        $id = $taskModel->save();
        //扣去余额
        $userAcounts = new FunleisureUserAccount();
        $userAcounts->created_by = $this->user->id();
        $userAcounts->before_balance = $userAccount->after_balance;
        $userAcounts->after_balance = $userAccount->after_balance - $params['task_price'] * $params['task_num'];
        $userAcounts->price = $params['task_price'] * $params['task_num'];
        $userAcounts->reason = '用户发布任务';
        $userAcounts->code = '20001';
        $userAcounts->type = 1; //1 + 2 -
        $userAcounts->save();
        return [
            'id' => $taskModel->task_id,
            'msg' => '添加成功'
        ];
    }


    public function adopt($params)
    {
        $ids = $params['ids'];
        $ref = FunleisureTaskJoin::query()
            ->whereIn('join_id', $ids)
            ->update(['join_status' => 3]);
        if ($ref) {
            //todo 加钱
            $this->addMoeny($ids);
        }
        return  $ref;
    }

    public function  reject($params)
    {
        $ids = $params['ids'];
        $ref = FunleisureTaskJoin::query()
            ->whereIn('join_id', $ids)
            ->update(['join_status' => 4, 'rejection' => $params['reason']]);

        return  $ref;
    }

    public function addMoeny($ids)
    {
        //目前只支持支付宝
        $arr = FunleisureTaskJoin::query()
            ->with(['task'])
            ->whereIn('join_id', $ids)->get();

        foreach ($arr as $k => $v) {
            $userAcount = FunleisureUserAccount::query()->where('created_by', $v['created_by'])->first();
            $userAcounts = new FunleisureUserAccount();
            $price = $v['task']['task_price'];
            if (!$userAcount) {
                $userAcounts->created_by = $v['created_by'];
                $userAcounts->before_balance = 0;
                $userAcounts->after_balance =  $price;
                $userAcounts->price =  $price;
                $userAcounts->reason = '用户任务';
                $userAcounts->code = $v['join_id'];
                $userAcounts->type = 1; //1 + 2 -
                $userAcounts->save();
            } else {
                $userAcounts->created_by = $v['created_by'];
                $userAcounts->before_balance = $userAcount->after_balance;
                $userAcounts->after_balance =  $price + $userAcount->after_balance;
                $userAcounts->price =  $price;
                $userAcounts->reason = '用户任务';
                $userAcounts->code = $v['join_id'];
                $userAcounts->type = 1; //1 + 2 -
                $userAcounts->save();
            }
            // return ['id'=>$userAcounts->user_account_id];
        }
    }


    public function  getJoinList($task_id)
    {
        $joinInfo = FunleisureTaskJoin::query()
            ->with([
                'user' => function ($query) {
                    return $query->select('avatar', 'nickname', 'user_id');
                }
            ])
            ->where([
                ['task_id', '=', $task_id],
                ['join_status', '=', 2]
            ])
            ->get();
        return $joinInfo;
    }


    //商家稿件
    public function getAudit($type, $user_id)
    {
        if ($type == 2) {
            $joinList = FunleisureTask::query()
                ->with([
                    'user' => function ($query) {
                        return $query->select('avatar', 'nickname', 'user_id');
                    }
                ])
                ->where([
                    ['created_by', '=', $user_id],
                    ['task_status', '=', '1']
                ])
                ->get();
            if ($joinList) {
                foreach ($joinList as $key => $value) {
                    $count = FunleisureTaskJoin::query()
                        ->where([
                            ['task_id', '=', $value['task_id']],
                            ['join_status', '=', 2]
                        ])
                        ->count();

                    $joinList[$key]['count'] = $count;
                    if ($count == 0) {
                        unset($joinList[$key]);
                    } else {
                        $joinCount = FunleisureTaskJoin::query()
                            ->where([
                                ['task_id', '=', $value['task_id']],
                                ['join_status', 'in', [1, 2, 3]]
                            ])
                            ->count();
                        $joinList[$key]['joinCount'] =  $joinCount;
                        $joinInfo = FunleisureTaskJoin::query()
                            ->with([
                                'user' => function ($query) {
                                    return $query->select('avatar', 'nickname', 'user_id');
                                }
                            ])
                            ->where([
                                ['task_id', '=', $value['task_id']],
                                ['join_status', '=', 2]
                            ])
                            ->get();
                        $joinList[$key]['joinInfo'] =  $joinInfo;
                    }
                }
            }
            return ['data' => $joinList];
        } else {
            $status = $type;
            $joinList = FunleisureTask::query()
                ->with(['user' => function ($query) {
                    return $query->select('avatar', 'nickname', 'user_id');
                }])
                ->with(['category' => function ($query) {
                    return $query->select('category_id', 'category_name', 'category_code');
                }])
                ->where([
                    ['task_status', '=', $status],
                    ['created_by', '=', $user_id]
                ])
                ->paginate(20);
            return $joinList;
        }
    }
}
