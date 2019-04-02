<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/24
 * Time: 10:02
 */

namespace backend\modules\api\controllers;



use backend\models\AgentCompany;


use backend\models\ContractNew;
use backend\models\ContractRelevanceNew;
use backend\models\CustomerAdvertiserReleveance;
use backend\models\JdCustomer;
use backend\models\ProductLine;

use yii\db\Query;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use backend\models\ContractAccounts as Contract;
use backend\components\Quanxian;



class ContractAccountsController extends ActiveController
{
    public $modelClass='backend\models\ContractAccounts';

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

    //根据客户ID获取客户合同列表
    public function actionContractlist($id){
        //获取要显示的客户
        $data['list']=Contract::contract_list($id);
        $data['code']=200;
        return $data;
    }

    public function actionGuidang($id){
        if($contract=Contract::updataContract($id,'isguidang','1'))
        {
            $data['code']='200';
            $data['mes']='操作归档成功';
        }else
        {
            $data['code']='500';
            $data['mes']='归档失败';
        }
        return $data;

    }
    public function actionZuofei($id){
        if($contract=Contract::updataContract($id,'iszuofei','1'))
        {
            $data['code']='200';
            $data['mes']='操作作废合同成功';
        }else
        {
            $data['code']='500';
            $data['mes']='归档失败';
        }
        return $data;
    }
    public function actionJieshu($id){
        if($contract=Contract::updataContract($id,'iszuofei','2'))
        {
            $data['code']='200';
            $data['mes']='操作结束合同成功';
        }else
        {
            $data['code']='500';
            $data['mes']='归档失败';
        }
        return $data;
    }

    public function actionYanqi($id,$date){
        $date=strtotime($date);
        if($contract=Contract::updataContract($id,'contract_end',$date))
        {
            Contract::updataContract($id,'iszuofei','0');
            $data['code']='200';
            $data['mes']='操作延期合同成功';
        }else
        {
            $data['code']='500';
            $data['mes']='延期失败';
        }
        return $data;
    }

    //合同详情
    public function actionContract_info($id){
        $data['code']='200';
        $data['data']=Contract::contractinfo($id);
        return $data;
    }





    //一级审核
    public function actionShenhe1($id){

        $tr = \Yii::$app->db->beginTransaction();
        try {

            $this->global_audit($id,'contract_new',1,'contract_accounts');

            Contract::contract_shenhe($id,1);
            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }

        return array('code'=>"200",'msg'=>'审核成功');

    }

    //二级审核
    public function actionShenhe2($id){
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $this->global_audit($id,'contract_new',2,'contract_accounts');
            Contract::contract_shenhe($id,2);
            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }

        return array('code'=>"200",'msg'=>'审核成功');
    }

    //预留审核
    //三级审核
    public function actionShenhe3($id){
        $this->global_audit($id,'contract_new',3,'contract_accounts');
        Contract::contract_shenhe($id,3);
        return array('code'=>"200",'msg'=>'审核成功');
    }

    //四级审核
    public function actionShenhe4($id){
        $this->global_audit($id,'contract_new',4,'contract_accounts');
        Contract::contract_shenhe($id,4);
        return array('code'=>"200",'msg'=>'审核成功');
    }

    //五级审核
    public function actionShenhe5($id){
        $this->global_audit($id,'contract_new',5,'contract_accounts');
        Contract::contract_shenhe($id,5);
        return array('code'=>"200",'msg'=>'审核成功');
    }


    //新增合同
    public function actionAddContract($id){
        $data['code']='200';
        $myid=\Yii::$app->user->id;

        $data['customer']=JdCustomer::kehu_info_sm($id);
        $data['daili']=AgentCompany::find()->all();
        $data['prlist']=ProductLine::pr_list();

        return $data;
    }

    //合同数量查询
    public function actionContract_num(){
        $today = strtotime(date('Y-m-d', time()));//获取当天0点
        $uid=\Yii::$app->user->id;
        $max=ContractNew::find()->where("submituser=$uid and ctime>$today")->count();

        $num=$max+1;
        
        if($num<10)
        {
            $num="0".$num;
        }

        echo $num;
    }

    //新增合同返回
    public function actionAddru(){
        /*
        $postdata=\Yii::$app->request->post();
        $commod=(new Query())->createCommand()->insert('contract_new',[
            'advertiser'=>'361',
            'contract_no'=>'123',
            'agent_company'=>'123',
            'submituser'=>'17',
            'ctime'=>time(),
            'payment_type'=>2
        ])->execute();
        var_dump($commod->sql);
        */

        $tr = \Yii::$app->db->beginTransaction();
        try {
            //写入
            $insstate=Contract::add_countract_ru();
            if($insstate->id){
                $data= array('code'=>200,'mgs'=>'提交成功','data'=>$insstate);
            }elseif($insstate['code']=='500')
            {
                $data= $insstate;
            }
            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }

        return $data;
    }


    //根据广告主查询产品线
    public function actionAdvertiser_to_product($adid){
        $data['data']=CustomerAdvertiserReleveance::find()->where(['advertiser'=>$adid])->asArray()->all();
        $data['code']='200';
        return $data;
    }

    //是否有权限查看合同附件
    public function actionIsshowcontractfile(){
        /*
         * 这是一个空方法，为了权限而设定
         * */
    }

    //

    public function actionContract_no_num($no){
        return ContractNew::contract_no_num($no);

    }

}