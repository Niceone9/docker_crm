<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/24
 * Time: 10:02
 */

namespace backend\modules\api\controllers;


use backend\models\Account;
use backend\models\AgentCompany;
use backend\models\AuditAction;
use backend\models\BackMoney;
use backend\models\ContractNew;
use backend\models\ContractRelevance;
use backend\models\ContractRelevanceNew;
use backend\models\Customerb;
use backend\models\JdCustomer;
use backend\models\QdDakuan;
use backend\models\RenewHuikuan;
use backend\models\Yuming;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use backend\models\JdContract as Contract;
use yii\web\NotFoundHttpException;
use backend\components\Quanxian;

use yii\data\ActiveDataProvider;


class ContractController extends ActiveController
{
    public $modelClass='backend\models\JdContract';

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
        $data['data']=Contract::contractinfonomt($id);
        return $data;
    }

    //查看合同相关账户
    public function actionContract_account($id){
        $data['code']='200';
        $data['data']=Contract::contact_account($id);
        return $data;
    }



    //一级审核
    public function actionShenhe1($id){
        $tr = \Yii::$app->db->beginTransaction();
        try {
            //写入续费
            $this->global_audit($id,'contract',1,'jd_contract');
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
            $this->global_audit($id,'contract',2,'jd_contract');
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
        Contract::contract_shenhe($id,3);
        return  $this->global_audit($id,'contract',3,'jd_contract');
    }

    //四级审核
    public function actionShenhe4($id){
        Contract::contract_shenhe($id,4);
        return  $this->global_audit($id,'contract',4,'jd_contract');
    }

    //五级审核
    public function actionShenhe5($id){
        Contract::contract_shenhe($id,5);
        return  $this->global_audit($id,'contract',5,'jd_contract');
    }


    //新增协议  公司id,合同id,
    /*
    public function actionAddContract($id){
        $data['code']='200';
        $htinfo=ContractNew::findOne($id);
        $data['data']['contract_info']=$htinfo;
        $data['data']['advertiser']=JdCustomer::find()->select(['id','advertiser','customer_type'])->where(['id'=>$htinfo->advertiser])->one();
        $data['data']['meijie_list']=Contract::find()->select(['a.id','a.title','a.product_line','b.fandian'])->join("a left join","contract_relevance_new b","a.product_line=b.product_line")->where(['a.is_meijie'=>1,'a.iszuofei'=>0,'a.is_ultimate_shenhe'=>1,'b.contract_id'=>$id])->asArray()->all();


        return $data;
    }*/



    public function actionAddContract($id){
        $data['code']='200';
        $myid=\Yii::$app->user->id;

        $data['customer']=JdCustomer::kehu_info_sm($id);
        $data['daili']=AgentCompany::find()->all();
        $data['meijielist']=Contract::find()->select(['id','title','product_line'])->where(['is_meijie'=>1,'iszuofei'=>0])->all();

        return $data;
    }

    //合同数量查询
    public function actionContract_num(){
        $today = strtotime(date('Y-m-d', time()));//获取当天0点
        $uid=\Yii::$app->user->id;
        $max=Contract::find()->where("submituser=$uid and ctime>$today")->count();

        $num=$max+1;
        
        if($num<10)
        {
            $num="0".$num;
        }

        echo $num;
    }

    //新增合同返回
    public function actionAddru(){

        $tr = \Yii::$app->db->beginTransaction();
        try {
            //写入续费
            $insstate=Contract::add_countract_ru();
            if($insstate['code']=='500')
            {
                return $insstate;
            }
            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'提交成功','data'=>1);


    }


    //历史记录
    public function actionHistory($contract_id){

        $ht_info= Contract::contractinfonomt($contract_id);

        $where="id!=0";
        //筛选条件
        $getdata=\Yii::$app->request->get();
        //续费
        if($getdata['type']=='renew')
        {
            $where="payment_type in (1,2)";
        }
        if($getdata['type']=='bukuan')
        {
            $where="payment_type in (3)";
        }
        if($getdata['type']=='tuikuan')
        {
            $where="payment_type in (14,15)";
        }
        if($getdata['type']=='zhuankuan')
        {
            $where="payment_type in (16)";
        }
        if($getdata['type']=='huikuan')
        {
            $where="is_huikuan =1";
        }
        if($getdata['type']=='baozhengjin')
        {
            $where="payment_type in (4)";
        }
        if($getdata['type']=='kehubaokuan')
        {
            $where="payment_type in (31)";
        }
        if($getdata['type']=='dabaozhengjin')
        {
            $where="payment_type in (41)";
        }

        //时间条件
        $time_start=$getdata['time_start'];
        $time_end=$getdata['time_end'];
        if($time_start!="" and $time_end!="")
        {
            $time_start=strtotime($time_start);
            //$time_start=strtotime("-1 days",$time_start);
            $time_end=strtotime($time_end ."+1 day");
            //$time_end=strtotime("+1 days",$time_end);
            $where.=" and payment_time >= $time_start and payment_time < $time_end";
            //$fp_where.=" and ctime >= $time_start and ctime <= $time_end";

        }
//
//        $list2=QdDakuan::find()
//            ->select(['b_money as money','b_money as xf_qiane','id','b_time as payment_time','ctime','b_money as show_money','contract_id as xf_contractid','ctime as payment_type'])
//            ->with(['submituser0'])
//            ->where(['contract_id'=>$contract_id])
//            ->andWhere($where)
//            ->asArray();

        $list=RenewHuikuan::find()
           // ->select(['xf_qiane','xf_contractid','id','payment_time','ctime','money','show_money','payment_type'])
           // ->union($list2)
            ->with(['account0','submituser0','guanggaozhu'])
            ->where(['jd_renew_huikuan.xf_contractid'=>$contract_id])
            ->andWhere($where)
            ->asArray();

       // $ar['sum']=$list->sum('money')+$list2->sum('money');
        $provider = new ActiveDataProvider([
            'query' => $list,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'payment_time' => SORT_DESC,
                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);




      //  var_dump($provider->getModels());exit;


        foreach ($provider->getModels() as $key=>$val)
        {
            if($val['payment_type']==1)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //续费预付
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"续费 付款".($val['money']),"yue"=>$yue-=$val['money'],"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'充值',"submitusers"=>$val['submituser0']['name'],"money"=>"-".round($val['money'],2),"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],'is_xiadan'=>$val['is_xiadan']);
            }elseif($val['payment_type']==2)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //续费垫付
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"续费 垫款".($val['money']),"yue"=>$yue-=$val['money'],"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'充值',"submitusers"=>$val['submituser0']['name'],"money"=>"-".round($val['money'],2),"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],'is_xiadan'=>$val['is_xiadan']);
            }elseif($val['payment_type']==3)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'bukuan'])->one();
                ////续费补款
                $history_xf[]=array("kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"续费 补款".($val['money']),"yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'补款',"submitusers"=>$val['submituser0']['name'],"money"=>round($val['money'],2),"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);

            }elseif($val['payment_type']==311)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'bukuanbi'])->one();
                ////续费补款
                $history_xf[]=array("kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"续费 补款".($val['money']),"yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'补账户币',"submitusers"=>$val['submituser0']['name'],"money"=>round($val['money'],2),"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);

            }elseif($val['payment_type']==14)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'refund_kehu'])->one();
                //续费 退款
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"退款到客户","yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'退款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".round($val['money'],2),"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }elseif($val['payment_type']==15)
            {

                //判断媒介合同状态
//                $contract_mhttype=Contract::contractinfo($val['xf_contractid']);
//                if($contract_mhttype['mht']['state']=='1')
//                {
//                    $audit_name='refund_account_z';
//                }else
//                {
//                    $audit_name='refund_account_p';
//                }
//                //需要几审
//                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>$audit_name])->one();
                //续费 转款
                $history_xf[]=array("kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"退款到总账户","yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'退款',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }
            elseif($val['payment_type']==16)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //合同 转款
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"转款到资金池（未分配余额）".($val['money']),"yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'转款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }
            elseif($val['payment_type']==161)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //合同 转款
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"保证金转出到（未分配余额）".($val['money']),"yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'转出保证金',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }
            elseif($val['payment_type']==4)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'baozhengjin'])->one();
                //合同 保证金
                $history_xf[]=array("kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"分配款到保证金".($val['money']),"yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'分配保证金',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }
            elseif($val['payment_type']==41)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //合同 保证金
                $history_xf[]=array("kyue"=>0,"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"打保证金".($val['money']),"yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'打保证金',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }
            elseif($val['payment_type']==31)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'baozhengjin'])->one();
                //合同 客户补款
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'客户补款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }
            elseif($val['payment_type']==20)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //合同 转款
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"退款转款到资金池（退款未分配余额）".($val['money']),"yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'转出退款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }
            //分配款
            if($val['is_huikuan']==1)
            {
                $history_xf[]=array("kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"分配款".($val['money']),"yue"=>$yue-=0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'分配款',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"audit_shencount"=>2,"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }


        }
        // 获取分页和排序数据
        $ar['data'] = $history_xf;
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();
        $ar['contract_info']=$ht_info;
        return $ar;
    }

    //历史记录
    public function actionHistory_ad($adid){

        $ad_info=JdCustomer::findOne($adid);

        $where="id!=0";
        //筛选条件
        $getdata=\Yii::$app->request->get();
        //续费
        if($getdata['type']=='renew')
        {
            $where="payment_type in (1,2)";
        }
        if($getdata['type']=='bukuan')
        {
            $where="payment_type in (3)";
        }
        if($getdata['type']=='tuikuan')
        {
            $where="payment_type in (14,15)";
        }
        if($getdata['type']=='zhuankuan')
        {
            $where="payment_type in (16)";
        }
        if($getdata['type']=='huikuan')
        {
            $where="is_huikuan =1";
        }
        if($getdata['type']=='baozhengjin')
        {
            $where="payment_type in (4)";
        }
        if($getdata['type']=='kehubaokuan')
        {
            $where="payment_type in (31)";
        }
        if($getdata['type']=='dabaozhengjin')
        {
            $where="payment_type in (41)";
        }
        if($getdata['type']=='zhuankuanbzj')
        {
            $where="payment_type in (161)";
        }
        if($getdata['type']=='adhuikuan')
        {
            $where="payment_type >10000";
        }
        if($getdata['type']!='adhuikuan' && $getdata['type']!='')
        {

        }

        //时间条件
        $time_start=$getdata['time_start'];
        $time_end=$getdata['time_end'];
        if($time_start!="" and $time_end!="")
        {
            $time_start=strtotime($time_start);
            //$time_start=strtotime("-1 days",$time_start);
            $time_end=strtotime($time_end ."+1 day");
            //$time_end=strtotime("+1 days",$time_end);
            $where.=" and payment_time >= $time_start and payment_time < $time_end";
            //  $fp_where.=" and ctime >= $time_start and ctime <= $time_end";

            $hwhere.="and b_time >= $time_start and b_time < $time_end";
        }



        $list2=BackMoney::find()
            ->select(['b_money as money','b_money as xf_qiane','id','b_time as payment_time','ctime','b_money as show_money','ctime as payment_type'])
            ->with(['submituser0'])
            ->where(['advertiser'=>$adid,'is_ultimate_shenhe'=>1])
            ->andWhere('id!=0 '.$hwhere)
            ->asArray()
            ->all();







        $list=RenewHuikuan::find()
            // ->select(['xf_qiane','xf_contractid','id','payment_time','ctime','money','show_money','payment_type'])
            // ->union($list2)
            ->with(['account0','submituser0','guanggaozhu','contractproduct'])
            ->where(['jd_renew_huikuan.advertiser'=>$adid,'is_ultimate_shenhe'=>1])

            ->andWhere($where)
            ->andWhere('money > 0')
            ->asArray()
            ->all();

        if(!is_array($list2))
        {
            $list2=[];
        }
        if(!is_array($list))
        {
            $list=[];
        }
        $list=array_merge($list2,$list);







        /*
        $provider = new ActiveDataProvider([
            'query' => $list,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'payment_time' => SORT_DESC,
                    'id' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        */
        //$provider->getModels()



        //  var_dump($provider->getModels());exit;


        foreach ($list as $key=>$val)
        {
            if($val['payment_type']==1)
            {
                //续费预付
                $history_xf[]=array("project"=>"3","kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"续费 付款".($val['money']),"type"=>'续费',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }elseif($val['payment_type']==2)
            {
                //续费垫付
                $history_xf[]=array("project"=>"3","kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"续费 垫款".($val['money']),"type"=>'续费',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }elseif($val['payment_type']==3)
            {
                ////续费补款
                $history_xf[]=array("project"=>"1","kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"续费 补款".($val['money']),"type"=>'补款',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);

            }elseif($val['payment_type']==311)
            {
                ////续费补款
                $history_xf[]=array("project"=>"2","kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"续费 补款".($val['money']),"type"=>'补账户币',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);

            }elseif($val['payment_type']==14)
            {
                //续费 退款
                $history_xf[]=array("project"=>"1","kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"退款到客户","type"=>'退款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }elseif($val['payment_type']==15)
            {

                //续费 退款到总账户
                $history_xf[]=array("project"=>"3","kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"退款到总账户","type"=>'退款',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }
            elseif($val['payment_type']==16)
            {

                //合同 转款
                $history_xf[]=array("project"=>"2","kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"转款到资金池（未分配余额）".($val['money']),"type"=>'转款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }
            elseif($val['payment_type']==161)
            {

                //合同 转款
                $history_xf[]=array("project"=>"2","kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"保证金转出到（未分配余额）".($val['money']),"type"=>'转出保证金',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }
            elseif($val['payment_type']==4)
            {
                //合同 保证金
                $history_xf[]=array("project"=>"2","kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"分配款到保证金".($val['money']),"type"=>'分配保证金',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }
            elseif($val['payment_type']==41)
            {
                //合同 申请保证金
                $history_xf[]=array("project"=>"3","kyue"=>0,"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"打保证金".($val['money']),"type"=>'打保证金',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }
            elseif($val['payment_type']==42)
            {
                //合同 退保证金
                $history_xf[]=array("project"=>"3","kyue"=>0,"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"退保证金".($val['money']),"type"=>'退保证金',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }
            elseif($val['payment_type']==31)
            {
                //合同 客户补款
                $history_xf[]=array("project"=>"2","kyue"=>0,"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"type"=>'客户补款',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }
            //分配款
            if($val['is_huikuan']==1)
            {
                $history_xf[]=array("project"=>"2","kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"分配款".($val['money']),"type"=>'分配款',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu'],"product"=>$val['contractproduct']);
            }

            //回款
            if($val['payment_type']>10000)
            {
                $history_xf[]=array("project"=>"1","kyue"=>$val['money'],"xf_contractid"=>0,"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"公司回款".($val['money']),"type"=>'公司回款',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion'],'show_money'=>$val['show_money'],"guanggaozhu"=>$val['guanggaozhu']);
            }


        }
       // ArrayHelper::multisort($history_xf, 'date', SORT_DESC);
        if($getdata['order']=='asc' || $getdata['order']=='')
        {
            ArrayHelper::multisort($history_xf, ['date','id'], [SORT_DESC,SORT_DESC]);

        }else
        {
            ArrayHelper::multisort($history_xf, ['date','id'], [SORT_ASC,SORT_ASC]);

        }


        if(is_array($history_xf))
        {
            return $history_xf;
        }else
        {
            return array();
        }


    }


    //转款页面
    public function actionZhuankuan($contract_id){
        $contract_info=Contract::contractinfonomt($contract_id);
        $yue=$contract_info['huikuan']+$contract_info['bukuan']-$contract_info['yu_e'];
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

        $info=Contract::contractinfonomt($id);

        $ren_contract=ContractRelevance::find()->where(['contract_id'=>$id])->one();
        //$yue=$info['huikuan']-$info['margin_money']+$info['bukuan']-$info['yu_e'];
        $yue=$info['huikuan']+$info['bukuan']-$info['yu_e'];
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
                Contract::updateAllCounters(['huikuan'=>-$money],['id'=>$id]);
                //公司未分配余额增加
                JdCustomer::updateAllCounters(['undistributed_yu_e'=>$money],['id'=>$info['advertiser']]);

                $model=new RenewHuikuan();
                $model->advertiser=$info['advertiser'];
                $model->product_line=$ren_contract['product_line'];
                $model->type=$info['type'];
                $model->money=$money;
                $model->payment_type=16;
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

    public function actionContract_meijie_list(){
      return  Contract::find()->select(['id','title','product_line'])->where(['is_meijie'=>1,'iszuofei'=>0])->all();
    }

    public function actionCopy_account($contract_id){
        $postdata=\Yii::$app->request->post();
        $ii=0;
        //如果是复制的合同 并且复制账户啦
        if(count($postdata['account_list']) > 0 && is_array($postdata['account_list']))
        {
            foreach ($postdata['account_list'] as $key=>$val)
            {
                //查询复制账户信息
                $account_info=Account::findOne($val);

                //复制这些账户
                $new_account=new Account();

                foreach ($account_info as $key=>$val)
                {
                    if($key!='id' and $key!='contract_id'){
                        $new_account->$key=$val;
                    }
                }

                $new_account->contract_id=$contract_id;
                $new_account->ctime=time();
                $new_account->endtime=4092599349;
                if($new_account->save())
                {
                   // print_r($new_account);
                    $ii++;
                }else
                {
                    return $new_account->errors;
                }
                //修改账户结束时间
                Account::updateAll(['endtime'=>time()],"a_users='".$account_info['a_users']."' and endtime=4092599349 and id !=".$new_account->id);
                Yuming::updateAll(['account_id'=>$new_account->id],['account_id'=>$account_info->id]);
                //$account_info->endtime=time();
                //$account_info->save();
            }
        }

        return array('code'=>'200','msg'=>"成功复制 $ii 个账户");

    }



}