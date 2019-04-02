<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/13
 * Time: 11:16
 */

namespace backend\modules\api\controllers;

use backend\models\ContractRelevance;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\MrenewHuikuan;
use backend\models\RenewHuikuan;
use backend\models\RenewOrder;
use yii\db\Exception;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use backend\components\Quanxian;
use Yii;



class MarginController extends ActiveController
{
    public $modelClass='backend\models\RenewHuikuan';
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

    public function actionList_margin_m(){
        return  RenewHuikuan::margin_m_list();
    }


    public function actionAdd($contract_id){
        //要续费的合同信息
        $data['code']='200';
        $data['data']=array(
            "contract"=>JdContract::contractinfonomt($contract_id),
        );

        return $data;
    }

    //给媒体打保证金
    public function actionAddMarginToMedia(){
        $tr=\Yii::$app->db->beginTransaction();
        try{
            //写入打保证金
            //写入续费
            $insert=RenewHuikuan::add_margin_to_media();
            if(isset($insert['code']))
            {
                return $insert;exit;
            }
            $tr->commit();
        }catch (Exception $e){
            $tr->rollBack();
            return $e->getMessage();
        }

        return array('code'=>'200','msg'=>'提交保证金成功','data'=>$insert);
    }


    //一级审核
    public function actionShenhe1($id){


        $tr1 = Yii::$app->db->beginTransaction();
        try {
                //写入历史记录
                $this->global_audit($id,'baozhengjin',1,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::margin_m_shenhe($id,1);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费审核成功');






    }
    //二级审核
    public function actionShenhe2($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {

                //写入历史记录
                $this->global_audit($id,'baozhengjin',2,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::margin_m_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    //二级审核
    public function actionShenhe3($id){


        $tr1 = Yii::$app->db->beginTransaction();
        try {

                //写入历史记录
                $this->global_audit($id,'baozhengjin',3,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::margin_m_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    //二级审核
    public function actionShenhe4($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {

                //写入历史记录
                $this->global_audit($id,'baozhengjin',4,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::margin_m_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    //二级审核
    public function actionShenhe5($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {

                //写入历史记录
                $this->global_audit($id,'baozhengjin',5,'jd_renew_huikuan');
                //审核续费后续
                RenewHuikuan::margin_m_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'续费二级审核成功');

    }

    public function actionInfo($id){
        $data['code']='200';
        $data['data']=RenewHuikuan::margin_da_info($id);
        return $data;
    }



    //退保证金
    public function actionTuimargin($id)
    {
        $one=RenewHuikuan::findOne($id);
        if($one->margin_istui==1)
        {
            return array('code'=>'500','msg'=>'已经退过了！');
        }

        if($one->is_ultimate_shenhe==0)
        {
            return array('code'=>'500','msg'=>'该比保证金未审核通过，不能提交退款申请！');
        }

        if(RenewHuikuan::find()->where(['relevance_id'=>$id,'payment_type'=>42])->count()>0)
        {
            return array('code'=>'500','msg'=>'已经提交过申请了！');
        }

        //查询媒体是否退了这笔钱
        $mmargin=MrenewHuikuan::find()->where(['xf_id'=>$id])->one();

        $mmargintuiinfo=MrenewHuikuan::find()->where(['xf_id'=>$mmargin['id']])->one();

        if($mmargintuiinfo['is_ultimate_shenhe']==0)
        {
            return array('code'=>'500','msg'=>'媒体还未退此比保证金！');
        }

        $tr1=Yii::$app->db->beginTransaction();
        try{
            RenewHuikuan::tuimargin($id);
            $tr1->commit();
        }catch (Exception $e){

            $tr1->rollBack();
            return $e->getMessage();
        }

        return array('code'=>'200','msg'=>'申请成功');


    }


    //退款到总账户  直开 一审
    public function actionShenhetui1($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin_tuikaun',1,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::tui_margin_shenhe($id,1);
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }


    //退款到总账户  直开 二审
    public function actionShenhetui2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin_tuikaun',2,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::tui_margin_shenhe($id,2);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }

    //退款到总账户  直开 二审
    public function actionShenhetui3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin_tuikaun',3,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::tui_margin_shenhe($id,3);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }
    //退款到总账户  直开 二审
    public function actionShenhetui4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin_tuikaun',4,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::tui_margin_shenhe($id,4);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }
    //退款到总账户  直开 二审
    public function actionShenhetui5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin_tuikaun',5,'jd_renew_huikuan');
            //审核续费后续
            RenewHuikuan::tui_margin_shenhe($id,5);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }

    //补款详情
    public function actionTui_info($id){
        $data['code']='200';
        $data['data']=RenewHuikuan::margin_da_info($id);
        return $data;
    }


    //媒介打保证金列表
    public function actionTui_list(){
        return RenewHuikuan::margin_tui_list();

    }


    public function actionTuiinfo($id){
        $data['code']='200';
        $data['data']=RenewHuikuan::margin_da_info($id);
        return $data;
    }

    //转款页面
    public function actionZhuankuan($contract_id){
        $contract_info=JdContract::contractinfonomt($contract_id);
        $yue=$contract_info['margin_yue'];
        if($yue<=0)
        {
            return array("code"=>'500',"msg"=>"该合同的余额不足，不可以操作转款");
        }else
        {
            $data['code']="200";
            $data['yue']=$yue;
            $data['contractinfo']=$contract_info;
            return $data;
        }
    }

    public function actionZhuankuanru(){
        $postdata=\Yii::$app->request->post();
        $money=$postdata['money'];
        $id=$postdata['id'];

        $info=JdContract::contractinfonomt($id);
        $contractren=ContractRelevance::find()->where(['contract_id'=>$id])->one();
        //$yue=$info['huikuan']-$info['margin_money']+$info['bukuan']-$info['yu_e'];
        $yue=$info['margin_yue'];
        //比较两个高精度的数值
        $c = bccomp($money,$yue, 2);

        if($c==1)
        {
            return array("code"=>'500',"msg"=>"转款金额不可大于 $yue");
        }else
        {
            //执行事务
            $tr = \Yii::$app->db->beginTransaction();
            try {
                //合同回款减少
                JdContract::updateAllCounters(['margin_yue'=>-$money,'margin_money'=>-$money],['id'=>$id]);

                //公司未分配余额增加
                JdCustomer::updateAllCounters(['undistributed_yu_e'=>$money,'margin_yue'=>-$money,'margin_money'=>-$money],['id'=>$info['advertiser']]);


                $model=new RenewHuikuan();

                $model->advertiser=$info['advertiser'];
                $model->product_line=$contractren['product_line'];

                $model->type=$info['type'];
                $model->money=$money;
                $model->payment_type=161;
                $model->payment_time=time();
                $model->submituser=\Yii::$app->user->getId();

                $model->xf_contractid=$info['id'];
                $model->is_huikuan=0;
                $model->market=$info['market'];
                //$model->xf_qiane=0;
                $model->audit=1;
                $model->audit_count=2;
                $model->audit_u=\Yii::$app->user->getId();
                $model->is_ultimate_shenhe=1;
                $model->ctime=time();

                $model->xf_qiane=$money;
                $model->cu_ad_id=$postdata['cu_ad_id'];

                $model->note='转出保证金-crm操作';

                if(!$model->save())
                {
                    return array("code"=>500,"data"=>$model->errors,"msg"=>'转款失败');
                }

                //--RenewHuikuan::renew_huikuan($info['id']);

                $tr->commit(); //提交数据

            } catch (Exception $e) {
                //回滚
                $tr->rollBack();
                return $e->getMessage(); //返回自定义异常信息
            }

            return array('code'=>200,'mgs'=>'提交成功','data'=>1);



        }
    }

    public function actionUpmarginmht($id,$mhtid){
        if(RenewHuikuan::updateAll(['mht_id'=>$mhtid],['id'=>$id]))
        {
            return array('code'=>'200','msg'=>'修改成功');
        }else
        {
            return array('code'=>'500','msg'=>'修改失败');
        }
        
    }

}