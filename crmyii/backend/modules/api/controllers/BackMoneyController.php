<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\AgentCompany;
use backend\models\BackMoney;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\RenewHuikuan;
use yii\rest\ActiveController;


use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;

class BackMoneyController extends ActiveController
{
    public $modelClass='backend\models\BackMoney';

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

    //公司回款列表
    public function actionList(){
        return BackMoney::back_money_list();
    }
    //新增公司回款
    public function  actionAdd($id){

        //客户信息
       $data['customer']=JdCustomer::kehu_info_sm($id);

        //代理公司
        $data['agentCompany']=AgentCompany::find()->all();
        $data['code']=200;
        return $data;

    }


    //新增公司回款返回
    public function actionAddru(){
        $data['code']='200';
        $data['data']=BackMoney::add_backmoney();
        return  $data;
    }

    //回款详情
    public function actionBack_money_info($id){
        $data['code']='200';
        $data['data']=BackMoney::backmonet_info($id);
        return $data;
    }

    //一级审核
    public function actionShenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'backmoney',1,'jd_back_money');
            //审核续费后续
            BackMoney::backmoney_shenhe($id,1);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');
    }

    //二级审核
    public function actionShenhe2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'backmoney',2,'jd_back_money');
            //审核续费后续
            BackMoney::backmoney_shenhe($id,2);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');

    }

    //三级审核
    public function actionShenhe3($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'backmoney',3,'jd_back_money');
            //审核续费后续
            BackMoney::backmoney_shenhe($id,3);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');

    }
    //四级审核
    public function actionShenhe4($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'backmoney',4,'jd_back_money');
            //审核续费后续
            BackMoney::backmoney_shenhe($id,4);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');

    }
    //五级审核
    public function actionShenhe5($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'backmoney',5,'jd_back_money');
            //审核续费后续
            BackMoney::backmoney_shenhe($id,5);

            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'回款审核成功');

    }

    //分配回款  公司id
    public function actionFphuikuan($id){
        //读出公司和公司可分配余额
        $data['costomer']=JdCustomer::find()->where("id=$id")->select('id,advertiser,undistributed_yu_e,tuikuan_undistributed_yu_e')->asArray()->one();

        //建议分配款算法
        /*
         *把此公司所有续费欠额大于0 而且 审核不等于驳回的 续费全部列出来 ，循环加入续费id,如果所循环续费大于未分配余额，则停止循环，按照记录好的续费id group出每个合同的建议分配余额
         * */
        //获取此公司所有欠额大于0的续费

        $renew_list=RenewHuikuan::qiane_renew($id);

        $wfpyue=$data['costomer']['undistributed_yu_e'];

        $money=0;
        foreach ($renew_list as $key=>$value)
        {

            if($value['xf_qiane']<=$wfpyue)
            {
                $jianyi[$value['xf_contractid']]+=$value['xf_qiane'];
                $wfpyue-=$value['xf_qiane'];

            }else
            {
                $jianyi[$value['xf_contractid']]+=$wfpyue;
                $wfpyue-$wfpyue;
                break;
            }

            //$money+=$value['xf_qiane'];
            //$reid.=$value['id'].",";
        }


        //$reid=substr($reid,0,-1);

        $contractid=Yii::$app->request->get('contractid');
        if($contractid!='')
        {
            $where =" and a.id=".$contractid;
        }

        $sql2="select a.* ,conre.money,conre.fandian,prlin.name as product_line from jd_contract a left join jd_contract_relevance conre on conre.contract_id=a.id left join jd_product_line prlin on prlin.id=conre.product_line where a.advertiser=$id and a.iszuofei!=1 and a.audit!=2 $where";

        /*
        if($reid!='')
        {
            //RenewHuikuan::find()->select(['sum(xf_qiane) as xf_qiane','xf_contractid'])->where("id in ($reid)")->groupBy("xf_contractid")->asArray()->all();
           // $sql="select sum(xf_qiane) as xf_qiane,xf_contractid from jd_renew_huikuan where id in($reid) group by xf_contractid";
            $sql2="select a.*,jy.xf_qiane,conre.money,conre.fandian,prlin.name as product_line from jd_contract a left join ($sql) jy on a.id=jy.xf_contractid left join jd_contract_relevance conre on conre.contract_id=a.id left join jd_product_line prlin on prlin.id=conre.product_line where a.advertiser=$id and a.iszuofei!=1 and a.audit!=2 $where";

        }else
        {

            //$contract_list=$contract->table("jd_contract a")->where("a.advertiser=$id and a.iszuofei!=1 and a.audit_1!=2 and a.audit_2!=2 $where")->select();
            $sql2="select a.* ,conre.money,conre.fandian,prlin.name as product_line from jd_contract a left join jd_contract_relevance conre on conre.contract_id=a.id left join jd_product_line prlin on prlin.id=conre.product_line where a.advertiser=$id and a.iszuofei!=1 and a.audit!=2 $where";
        }*/


        $posts = Yii::$app->db->createCommand($sql2)
            ->queryAll();

        foreach ($posts as $key=>$val)
        {
            $posts[$key]['xf_qiane']=$jianyi[$val['id']];
        }

        $data['contract_list']=$posts;
        $data['code']="200";

        return $data;
    }

    //分配回款返回
    public function actionFphuikuanru(){

         $postdata=Yii::$app->request->post();

        //客户信息
         $kehuinfo=JdCustomer::kehu_info_sm($postdata['advertiser']);

         //所有添加的分配款总和
        foreach ($postdata['pmoney'] as $key=>$val)
        {
            $money+=$postdata['pmoney'][$key];
        }
        /*
        if($money>$kehuinfo['undistributed_yu_e'])
        {
            return array("code"=>500,"msg"=>"可用分配款余额不足!");
        }*/

        //高精度对比
        $c = bccomp($money,$kehuinfo['undistributed_yu_e'], 2);
        if(Yii::$app->request->post('from_tuikuan')=='1')
        {
            $c= bccomp($money,$kehuinfo['tuikuan_undistributed_yu_e'], 2);
        }
        if($c==1 && Yii::$app->request->post('backmoney_type')!='3')
        {
            return array("code"=>"500","msg"=>"可用分配款余额不足  ");
            exit;
        }


        //执行事务
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            BackMoney::fp_huikuanru();
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }


        $data['code']="200";
        $data['msg']="分配回款成功";
        return $data;



    }


    public function actionDshenhebackmoney($id){
        $a= BackMoney::find()->where("audit!=2 and is_ultimate_shenhe!=1 and advertiser=$id")->sum('b_money');
        if(!$a){
         $a='';
        }
        return $a;
    }
    public function actionDshenhebumoney($id){
        $a= RenewHuikuan::find()->where("audit!=2 and is_ultimate_shenhe!=1 and advertiser=$id and payment_type=3")->sum('money');
        if(!$a){
            $a='';
        }
        return $a;
    }
}