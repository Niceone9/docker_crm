<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\AgentCompany;
use backend\models\Beikuan;
use backend\models\JdContactList;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\MbackMoney;
use backend\models\MrenewHuikuan;
use backend\models\MrenewRelevance;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class MbackMoneyController extends ActiveController
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
        $data['data']=MbackMoney::dakuan_list();
        return $data;
    }

    //添加公司打款
    public function actionAdd($id){
        //代理公司
        $data['AgentCompany']=AgentCompany::find()->all();
        //媒介合同list
        $data['m_contract_list']=JdContract::find()->where(['advertiser'=>$id])->all();
        //查询此公司所有打款读出开票信息。
        $data['dakuan_piao_list']=MbackMoney::find()->select('kaihuhang,shoukuanren,zhanghu')->where("advertiser=$id and zhanghu!=''")->groupBy('kaihuhang,shoukuanren,zhanghu')->all();
        return $data;
    }

    //媒介续费列表 根据媒介合同显示
    public function actionMrenew_list(){
        $mhtid=Yii::$app->request->post('mhtid');
        $dk_date=Yii::$app->request->post('dk_date');//打款日期
        $meiti_payment_type=Yii::$app->request->post('meiti_payment_type');//媒体付款类型

        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");

        if($start and $end)
        {
            $search_data=['and',['>=','a.payment_time',$start],['<','a.payment_time',$end]];
        }else
        {
            $search_data=['and',['>','a.payment_time',1]];
        }

        $mrelexf_id=MrenewRelevance::find()->select('xf_id')->asArray()->column();

        $mrelexf_idin=implode(',',$mrelexf_id);

        $list=MrenewHuikuan::find()
            ->select('a.*,b.a_users,b.mt_payment_type,b.dk_date,b.dk_zhouqi')
            ->join('a left join','jd_account b','a.account=b.id')
            ->where(['a.mht_id'=>$mhtid,'payment_type'=>[1,2],'is_ultimate_shenhe'=>'1'])
            ->andWhere(['>','a.xf_qiane',0])
            ->andFilterWhere(['b.dk_date'=>$dk_date,'b.mt_payment_type'=>$meiti_payment_type])
            ->andFilterWhere($search_data)
            //->andWhere("a.id not in ($mrelexf_idin)")
            ->orderBy('a.payment_time asc')
            ->asArray()
            ->all();



        //备款列表
        $list2=Beikuan::find()
            ->select('a.*,b.a_users,b.mt_payment_type,b.dk_date,b.dk_zhouqi,b.yu_e')
            ->join('a left join','beikuan_account b','a.account=b.id')
            ->where(['a.xf_contractid'=>$mhtid,'payment_type'=>[2],'is_ultimate_shenhe'=>'1'])
            ->andWhere(['>','a.xf_qiane',0])
           // ->andFilterWhere(['b.dk_date'=>$dk_date,'b.mt_payment_type'=>$meiti_payment_type])
            ->andFilterWhere($search_data)
            //->andWhere("a.id not in ($mrelexf_idin)")
            ->orderBy('a.payment_time asc')
            ->asArray()
            ->all();

        if(!is_array($list))
        {
            $list=[];
        }
        if(!is_array($list2))
        {
            $list2=[];
        }
        return array_merge($list,$list2);
    }

    //媒介退款列表
    public function actionMtuikuan_list(){
        $id=Yii::$app->request->get('id');

        $search_data=['and',['>','a.payment_time',1]];

        //媒介退款列表 打款余额大于0的list
        $list=MrenewHuikuan::find()
            ->select("a.dakuan_yue,a.payment_time,a.id,b.a_users,b.mt_payment_type,b.dk_date,b.dk_zhouqi,a.rebates_proportion")
            ->join('a left join','jd_account b','a.account=b.id')
            ->join('left join','jd_contract c','a.mht_id=c.id')
            ->where(['c.advertiser'=>$id,'a.payment_type'=>[15],'a.audit'=>1])
            ->andWhere(['>','a.dakuan_yue',0])
            ->andWhere(['<>','a.is_ultimate_shenhe',1])
            ->andFilterWhere($search_data)
            ->orderBy('a.payment_time asc')
            ->asArray()
            ->all();
        foreach ($list as $key=>$value)
        {
            $list[$key]['type']='1';
        }

        $bzj_tk=MrenewHuikuan::find()
            ->select('a.dakuan_yue,a.payment_time,a.id,b.a_users,b.mt_payment_type,b.dk_date,b.dk_zhouqi,a.rebates_proportion')
            ->join('a left join','jd_account b','a.account=b.id')
            ->join('left join','jd_contract c','a.mht_id=c.id')
            ->where(['c.advertiser'=>$id,'a.payment_type'=>[42],'a.audit'=>1])
            ->andWhere(['>','a.dakuan_yue',0])
            ->andWhere(['<>','a.is_ultimate_shenhe','1'])
            ->andFilterWhere($search_data)
            ->orderBy('a.payment_time asc')
            ->asArray()
            ->all();
        foreach ($bzj_tk as $key=>$value)
        {
            $bzj_tk[$key]['type']='2';
        }
        $bukuan=Beikuan::find()
            ->select('a.dakuan_yue,a.payment_time,a.id,a.rebates_proportion,b.a_users,b.mt_payment_type,b.dk_date,b.dk_zhouqi')
            ->join('a left join','beikuan_account b','a.account=b.id')
            ->join('left join','jd_contract c','a.mht_id=c.id')
            ->where(['c.advertiser'=>$id,'a.payment_type'=>[3],'a.audit'=>1])
            ->andWhere(['>','a.dakuan_yue',0])
            ->andWhere(['<>','a.is_ultimate_shenhe','1'])
            ->andFilterWhere($search_data)
            ->andWhere('a.account isnull')
            ->orderBy('a.payment_time asc')
            ->asArray()
            ->all();
        foreach ($bukuan as $key=>$value)
        {
            $bukuan[$key]['type']='3';
        }
        $bk_ac_tuikuang=Beikuan::find()
            ->select('a.dakuan_yue,a.payment_time,a.id,a.rebates_proportion,b.a_users,b.mt_payment_type,b.dk_date,b.dk_zhouqi')
            ->join('a left join','beikuan_account b','a.account=b.id')
            ->join('left join','jd_contract c','a.mht_id=c.id')
            ->where(['c.advertiser'=>$id,'a.payment_type'=>[155],'a.audit'=>1])
            ->andWhere(['>','a.dakuan_yue',0])
            ->andWhere(['<>','a.is_ultimate_shenhe','1'])
            ->andFilterWhere($search_data)
            ->orderBy('a.payment_time asc')
            ->asArray()
            ->all();
        foreach ($bk_ac_tuikuang as $key=>$value)
        {
            $bk_ac_tuikuang[$key]['type']='4';
        }
//        var_dump($list);
//        var_dump($bzj_tk);
//        var_dump($bukuan);
//        var_dump($bk_ac_tuikuang);
//
//
//        exit;

        //合并数组
        $list=ArrayHelper::merge($list,$bzj_tk,$bukuan,$bk_ac_tuikuang);


        return $list;
    }


    public function actionAddru(){
        $postdata=\Yii::$app->request->post();

        //如果是实时付款 检查续费欠额和打款金额进行对比
        /*
        if($postdata['ftype']==1)
        {
            $renew_sum=MrenewHuikuan::find()->select('xf_qiane')->where(['id'=>$postdata['renew_idin']])->sum('xf_qiane');
            //比较两个高精度的数值
            $c = bccomp($postdata['b_money'],$renew_sum, 2);
            if($c==-1)
            {
                $data['msg']="本次打款金额为".$postdata['b_money'].",不足以平掉选中的续费(总计：{$renew_sum})。请重新提交！";
                $data['code']="500";
                return $data;
                exit;
            }
        }*/
        $renew_sum=MrenewHuikuan::find()->select('xf_qiane')->where(['id'=>$postdata['renew_idin']])->sum('xf_qiane');

        $beikuan_sum=Beikuan::find()->select('xf_qiane')->where(['id'=>$postdata['renew_idin']])->sum('xf_qiane');

        $renew_sum+=$beikuan_sum;

        //比较两个高精度的数值
        /*
        $c = bccomp($postdata['b_money'],$renew_sum, 2);
        if($c==-1)
        {
            $data['msg']="本次打款金额为".$postdata['b_money'].",不足以平掉选中的续费(总计：{$renew_sum})。请重新提交！";
            $data['code']="500";
            return $data;
            exit;
        }*/

        $tr1 = Yii::$app->db->beginTransaction();
        try {

           $a= MbackMoney::adddakuan();

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
        $data['data']=MbackMoney::dakuaninfo($id);
        $data['code']=200;
        return $data;
    }

    //退款到总账户  直开 一审
    public function actionShenhe1($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'meijie-dakuan',1,'jd_mback_money');
            //审核续费后续
            MbackMoney::dakuan_shenhe($id,1);
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
            $this->global_audit($id,'meijie-dakuan',2,'jd_mback_money');
            //审核续费后续
            MbackMoney::dakuan_shenhe($id,2);
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
            $this->global_audit($id,'meijie-dakuan',3,'jd_mback_money');
            //审核续费后续
            MbackMoney::dakuan_shenhe($id,3);
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
            $this->global_audit($id,'meijie-dakuan',4,'jd_mback_money');
            //审核续费后续
            MbackMoney::dakuan_shenhe($id,4);
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
            $this->global_audit($id,'meijie-dakuan',5,'jd_mback_money');
            //审核续费后续
            MbackMoney::dakuan_shenhe($id,5);
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

    //媒介应付款列表
    public function actionYfk_list(){

        $data['code']=200;
        $data['data']=MrenewHuikuan::xufei_list();


        return $data;
    }
}

