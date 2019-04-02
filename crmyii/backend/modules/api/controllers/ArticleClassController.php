<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;

use backend\models\ArticleClass;
use backend\models\AuthAssignment;
use backend\models\Department;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use backend\components\Quanxian;

use backend\models\ProductLine;
use yii\rest\ActiveController;

class ArticleClassController extends ActiveController
{
    public $modelClass='backend\models\ArticleClass';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                //HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['myBehavior']=[
            'class'=>Quanxian::className(),
        ];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        return $behaviors;
    }


    //产品线列表
    public function actionBumenlist(){
        //获取常规二级数组
        $cateInfo = $this->actionGetInfo();

        //echo "<pre>";
        //var_dump($cateInfo);exit;
        //处理常规数组生成树形数组
        $tree1 = array();
        $cateTree = $this->actionCate($cateInfo, $tree1);


        return $cateTree;
       // $list=Department::bumen_list();

    }



    public function actionGetInfo()
    {
        $cateInfo = ArticleClass::bumen_list();
        // var_dump($cateInfo);
        return $cateInfo;
    }



    public function actionCate(&$info, $child, $pid = 0)
    {

        $child = array();
        if(!empty($info)){//当$info中的子类还没有被移光的时候
            foreach ($info as $k => &$v) {
                if($v['parent_id'] == $pid){//判断是否存在子类pid和返回的父类id相等的
                    $v['child'] = $this->actionCate($info, $child, $v['id']);//每次递归参数为当前的父类的id
                    $child[] = $v;//将$info中的键值移动到$child当中
                    //unset($info[$k]);//每次移动过去之后删除$info中当前的值
                }
            }
        }
        return $child;//返回生成的树形数组


    }

    //根据部门id获取部门下面的所有人员
    public function actionBumenusers($id){
        //获取部门下面的-递归
        $inid=\Yii::$app->hjd->article_classtree($id);
        //只获取这个部门的

        $list=ArticleClass::bumenuserslist($inid);
        return $list;
    }


    //新增产品线
    public function  actionAdd($id){

        $data['code']=200;
        return $data;

    }


    //新增公司回款返回
    public function actionAddru(){

        if($datadat=ArticleClass::add_ru()){
            $data['code']='200';
            $data['data']=$datadat;
            return  $data;
        }else
        {
            $data['code']='200';
            $data['msg']='提交失败';
        }
    }




    //删除部门
    public function actionDeletebumen($id){

        /*
        //查询此部门下有没有用户 如果有不可删除
        $u=$this->actionBumenusers($id);

        if(count($u)>0){
            return array('code'=>'500','msg'=>'此部门下还有用户，所以无法删除此部门');
        }else
        {
            if(ArticleClass::deleteAll(['id'=>$id]))
            {
                return array('code'=>'200','msg'=>'删除成功');
            }else
            {
                return array('code'=>'500','msg'=>'删除失败');
            }

        }

        exit;
        */
        if(ArticleClass::deleteAll(['id'=>$id]))
        {
            return array('code'=>'200','msg'=>'删除成功');
        }else
        {
            return array('code'=>'500','msg'=>'删除失败');
        }

    }




    public function actionUpbumen($id){
        $model=ArticleClass::findOne($id);

        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');

        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'修改成功','data'=>$model->errors);
        }
    }




}