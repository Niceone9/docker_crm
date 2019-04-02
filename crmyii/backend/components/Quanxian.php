<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/9/29
 * Time: 11:29
 */
namespace backend\components;

use backend\models\Audit;

use common\models\Category;
use Yii;
use yii\base\ActionFilter;
use mdm\admin\components\Helper;
use yii\db\Exception;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class Quanxian extends ActionFilter
{
    public $list_type=3;
    public function beforeAction($action)
    {

        //当前路由
        $actionID=$action->getUniqueId();

         $actionID="/".$actionID;
        $user=\Yii::$app->getUser();
        //判断是否有权限
        if (Helper::checkRoute($actionID, \Yii::$app->getRequest()->get(), $user)) {

            //echo $actionID;exit;
            return true;
        }else
        {
            throw new NotFoundHttpException('无权限访问');
        }




    }

    public function hjd(){
        //执行事务
        Yii::$app->db->transaction(function(){
           $a=new Category();
           $a->name='张三'.time();
           $a->save();
            $a=new Category();
            $a->name='李四';

            $a->save();
        });
    }
    //关联id,页面，几级审核
    public function global_audit($id,$action,$type,$table){
        $rows=(new Query())->select("id,audit_count")->from("audit")->where(['yid'=>$id,'tablea'=>$action])->orderBy('id desc')->one();

        if($table!='jd_contract'){
           if($rows && $type<=$rows['audit_count'])
           {
               throw new NotFoundHttpException('不可重复审核');
           }
        }


        //如果该条已经是最终状态已完成则不可继续操作审核
        $one=(new Query())->select("id,is_ultimate_shenhe")->from($table)->where(['id'=>$id])->one();
        if($one['is_ultimate_shenhe']=='1')
        {
            throw new NotFoundHttpException('此条状态标记为已完成，不可继续操作（is_ultimate_shenhe=1）'.$id);
        }


        $tr = Yii::$app->db->beginTransaction();

        try {

            $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
            $auditu=\Yii::$app->request->post('auditu'); //审核人员
            $note=\Yii::$app->request->post('note');//审核说明
            $sql1="update $table set audit=$audit,audit_u=$auditu,audit_count=$type,last_time=now() where id=$id";

            $a=Yii::$app->db->createCommand($sql1)
                ->queryAll();

            $audit_db=new Audit();
            $audit_db->yid=$id;
            $audit_db->type=$audit;
            $audit_db->tablea=$action;
            $audit_db->users=$auditu;
            $audit_db->catated_at=time();
            $audit_db->updated_at=time();
            $audit_db->audit_count=$type;
            $audit_db->note=$note;
            if($audit_db->validate()){
                $audit_db->save();
            }else
            {
                throw new NotFoundHttpException('写入审核历史记录失败');
            }
            $tr->commit();

        } catch (Exception $e) {

            $tr->rollBack();

            $data['code']=500;
            $data['mes']='审核失败';
            throw new NotFoundHttpException('审核失败');
            return $data;
        }
        $data['code']=200;
        $data['mes']='审核成功';
        return $data;

        /*
        Yii::$app->db->transaction(function($db){

            $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
            $auditu=\Yii::$app->request->post('auditu'); //审核人员
            $note=\Yii::$app->request->post('note');//审核说明

            //修改本表当前审核状态和审核人员
            $sql1="update $table set audit=$audit,audit_u=$auditu where id=$id";
            $db->createCommand($sql1)->execute();
            $audit_db=new Audit();
            $audit_db->aasd=13123;
            $audit_db->save();
        });*/


    }

}