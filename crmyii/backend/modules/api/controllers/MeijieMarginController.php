<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\AgentCompany;
use backend\models\JdContactList;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\MbackMoney;
use backend\models\MrenewHuikuan;
use yii\base\Exception;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class MeijieMarginController extends ActiveController
{
    public $modelClass='backend\models\MbackMoney';

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


    //媒介打款列表
    public function actionList(){
        $data['code']=200;
        $data['data']=MbackMoney::margin_list();
        return $data;
    }

    //媒介客户申请的保证金列表
    public function actionDa_list(){
        return MbackMoney::margin_m_list();

    }

    //媒介打保证金列表
    public function actionTui_list(){
        return MbackMoney::margin_tui_list();

    }

    //添加公司打款
    public function actionAdd($id){
        //代理公司
        $data['AgentCompany']=AgentCompany::find()->all();
        //媒介合同list
        $data['m_contract_list']=JdContract::find()->where(['advertiser'=>$id,'iszuofei'=>0])->all();
        //查询此公司所有打款读出开票信息。
        $data['dakuan_piao_list']=MbackMoney::find()->select('kaihuhang,shoukuanren,zhanghu')->where("advertiser=$id and zhanghu!=''")->groupBy('kaihuhang,shoukuanren,zhanghu')->all();
        return $data;
    }
    //媒介保证金列表 根据媒介合同显示
    public function actionMrenew_list(){
        $mhtid=Yii::$app->request->post('mhtid');

        $list=MrenewHuikuan::find()->select('a.*,b.advertiser as adname,b.appname as adappname')->join('a left join','jd_customer b','a.advertiser=b.id')->where(['a.mht_id'=>$mhtid,'a.payment_type'=>[41]])->andWhere(['>','a.xf_qiane',0])->asArray()->all();
        return $list;
    }

    public function actionAddru(){
        $postdata=\Yii::$app->request->post();

        //如果是实时付款 检查续费欠额和打款金额进行对比
        $renew_sum=0;
        if($postdata['ftype']==1)
        {
            $renew_sum=MrenewHuikuan::find()->select('money')->where(['id'=>$postdata['renew_idin']])->sum('money');
            //比较两个高精度的数值
            $c = bccomp($postdata['b_money'],$renew_sum, 2);
            if($c==-1)
            {
                $data['msg']="本次打款金额为".$postdata['b_money'].",不足以平掉选中的保证金(总计：{$renew_sum})。请重新提交！";
                $data['code']="500";
                return $data;
                exit;
            }
        }


        $tr1 = Yii::$app->db->beginTransaction();
        try {

           $a= MbackMoney::adddabaozhengjin($renew_sum);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'添加成功','data'=>$a);
    }

    //打款详情
    public function actionDkinfo($id){
        $data['data']=MbackMoney::margin_dakuaninfo($id);
        $data['code']=200;
        return $data;
    }

    //退款到总账户  直开 一审
    public function actionShenhe1($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin-dakuan',1,'jd_mback_money');
            //审核续费后续
            MbackMoney::margin_shenhe($id,1);
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }


    //退款到总账户  直开 二审
    public function actionShenhe2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin-dakuan',2,'jd_mback_money');
            //审核续费后续
            MbackMoney::margin_shenhe($id,2);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }

    //退款到总账户  直开 二审
    public function actionShenhe3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin-dakuan',3,'jd_mback_money');
            //审核续费后续
            MbackMoney::margin_shenhe($id,3);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }
    //退款到总账户  直开 二审
    public function actionShenhe4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin-dakuan',4,'jd_mback_money');
            //审核续费后续
            MbackMoney::margin_shenhe($id,4);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }
    //退款到总账户  直开 二审
    public function actionShenhe5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'margin-dakuan',5,'jd_mback_money');
            //审核续费后续
            MbackMoney::margin_shenhe($id,5);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }


    //分配打款
    public function actionFpdakuan($id){
        //合同id
        $contractid=Yii::$app->request->get('contractid');


        //读出公司和公司可分配余额
        $data['costomer']=JdCustomer::find()->where("id=$id")->select('id,advertiser,dakuan_undistributed_yu_e')->asArray()->one();
        $data['contract_list']=JdContract::find()->select('jd_contract.*,b.name')->where(['advertiser'=>$id,'is_meijie'=>1])->andWhere('iszuofei!=1')->andFilterWhere(['jd_contract.id'=>$contractid])->leftJoin('jd_product_line b','b.id=jd_contract.product_line')->asArray()->all();
        $data['code']="200";
        return $data;
    }

    //分配打款返回
    public function actionFpdakuanru()
    {
        $postdata=Yii::$app->request->post();
        //客户信息
        $kehuinfo=JdCustomer::kehu_info_sm($postdata['advertiser']);
        //所有添加的分配款总和
        foreach ($postdata['pmoney'] as $key=>$val)
        {
            $money+=$postdata['pmoney'][$key];
        }

        //高精度对比
        $c = bccomp($money,$kehuinfo['dakuan_undistributed_yu_e'], 2);
        if($c==1)
        {
            return array("code"=>"500","msg"=>"可用分配打款余额不足  ");
            exit;
        }


        //执行事务
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            MbackMoney::fp_dakuanru();
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }


        $data['code']="200";
        $data['msg']="分配打款成功";
        return $data;

    }


    //退保证金
    public function actionTuimargin($id)
    {

        $one=MrenewHuikuan::findOne($id);
        if($one->margin_istui==1)
        {
            return array('code'=>'500','msg'=>'已经退过了！');
        }

        if(MrenewHuikuan::find()->where(['xf_id'=>$id,'payment_type'=>42,'audit'=>array(0,1)])->count()>0)
        {
            return array('code'=>'500','msg'=>'已经提交过申请了！');
        }

        $tr1=Yii::$app->db->beginTransaction();
        try{
            MrenewHuikuan::tuimargin($id);
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
            $this->global_audit($id,'meiti-margin-tuikuan',1,'jd_mrenew_huikuan');
            //审核续费后续
            MbackMoney::tui_margin_shenhe($id,1);
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
            $this->global_audit($id,'meiti-margin-tuikuan',2,'jd_mrenew_huikuan');
            //审核续费后续
            MbackMoney::tui_margin_shenhe($id,2);
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
            $this->global_audit($id,'meiti-margin-tuikuan',3,'jd_mrenew_huikuan');
            //审核续费后续
            MbackMoney::tui_margin_shenhe($id,3);
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
            $this->global_audit($id,'meiti-margin-tuikuan',4,'jd_mrenew_huikuan');
            //审核续费后续
            MbackMoney::tui_margin_shenhe($id,4);
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
            $this->global_audit($id,'meiti-margin-tuikuan',5,'jd_mrenew_huikuan');
            //审核续费后续
            MbackMoney::tui_margin_shenhe($id,5);
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
        $data['data']=MrenewHuikuan::tui_margin_info($id);
        return $data;
    }


    //媒体保证金
    public function actionMargin_mt_sum(){


        $sum=JdCustomer::find()
            ->where(['customer_type'=>3])
            ->sum('margin_meiti');
        return array('code'=>'200','sum'=>$sum);

    }
    //客户保证金
    public function actionMargin_kh_sum(){


        $sum=JdCustomer::find()
            ->where(['customer_type'=>[1,2]])
            ->andWhere('margin_money >0')
            ->sum('margin_money');
        return array('code'=>'200','sum'=>$sum);

    }

    //垫付客户保证金
    public function actionMargin_kh_d_sum(){


        $sum=JdCustomer::find()
            ->where(['customer_type'=>[1,2]])
            ->andWhere('margin_yue <0')
            ->sum('margin_yue');
        return array('code'=>'200','sum'=>-$sum);

    }

    public function actionMargin_meijie_list(){
            //获取要显示的客户
            $data['list']=JdContract::meijie_margin_list();
            $data['code']=200;
            return $data;
    }

    public function actionMargin_kehu_list(){
        //获取要显示的客户
        $data['list']=JdContract::margin_contract_list();
        $data['code']=200;
        return $data;
    }

}

