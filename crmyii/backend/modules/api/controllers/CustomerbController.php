<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/12
 * Time: 17:24
 */
namespace backend\modules\api\controllers;
use backend\models\Customerb;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class CustomerbController extends ActiveController
{
    public $modelClass = 'backend\models\Customerb';
    //页面显示分页信息
    public $serializer=[
        'class'=>'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    //
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['myBehavior']=[
            'class'=>Quanxian::className(),
        ];
        return $behaviors;
    }


    //判断 网站和APP名称是否冲突
    public function actionIs_conflict($weburl='',$appname='',$advertiser='',$true_advertiser=''){
        $data['count']=Customerb::find()
            ->where(['and',['advertiser'=>$advertiser,'true_advertiser'=>$true_advertiser]])
           // ->orFilterWhere(['or',['website'=>$weburl],['appname'=>$appname]])
            ->count(1)
        ;

        $data['code']=200;
        return $data;
        //$array['']
    }


    //返回 销售自己的客户  参数 销售id
    public function actionMarket_kebao_list($id=''){

        if($id!=''){
        //获取要显示的客户
        $submitusers=Yii::$app->hjd->assignments($id); //根据角色递归获取旗下包含的用户 id
            $data['list']=Customerb::market_list($submitusers);
        }else
        {
            $data['list']=Customerb::market_list();
        }

        $data['code']=200;
        return $data;
    }

    public function actionRenling($id,$usersid){
        $data=Customerb::renling($id,$usersid);

        if($data=='success')
        {
            $data_ren['code']='200';
            $data_ren['mes']='认领成功';
        }
        elseif($data=='error-isme')
        {
            $data_ren['code']='500';
            $data_ren['mes']='不可认领自己的客户';
        }
        elseif($data=='error-count')
        {
            $data_ren['code']='500';
            $data_ren['mes']='客保数量超出限制';
        }elseif($data=='error-defeated')
        {
            $data_ren['code']='500';
            $data_ren['mes']='认领失败';
        }
        return $data_ren;
    }

    //一级审核
    public function actionShenhe1($id){
        //写入历史记录
        $this->global_audit($id,'kebao',1,'jd_customerb');

        return array('code'=>200,'mgs'=>'审核成功');
    }

    //二级审核
    public function actionShenhe2($id){
        //写入历史记录
        $this->global_audit($id,'kebao',2,'jd_customerb');

        return array('code'=>200,'mgs'=>'审核成功');

    }

    //二级审核
    public function actionShenhe3($id){
        //写入历史记录
        $this->global_audit($id,'kebao',3,'jd_customerb');

        return array('code'=>200,'mgs'=>'审核成功');

    }

    //二级审核
    public function actionShenhe4($id){
        //写入历史记录
        $this->global_audit($id,'kebao',4,'jd_customerb');

        return array('code'=>200,'mgs'=>'审核成功');

    }

    //二级审核
    public function actionShenhe5($id){
        //写入历史记录
        $this->global_audit($id,'kebao',5,'jd_customerb');

        return array('code'=>200,'mgs'=>'审核成功');

    }


}