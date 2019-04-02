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
use backend\models\Beikuan;
use backend\models\ContractRelevance;
use backend\models\Customerb;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\MrenewHuikuan;
use backend\models\RenewHuikuan;
use backend\models\Zhuankuan;
use yii\data\ArrayDataProvider;
use yii\db\Exception;
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


class MeijieContractController extends ActiveController
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
        $data['list']=Contract::meijie_contract_list($id);
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

    //合同详情
    public function actionContract_info($id){
        $data['code']='200';
        $data['data']=Contract::meijie_contractinfo($id);
        return $data;
    }





    //新增合同
    public function actionAddContract($id){
        $data['code']='200';
        $data['customer']=JdCustomer::kehu_info_sm($id);
        $data['daili']=AgentCompany::find()->all();
      //  $data['meijielist']=Contract::find()->select(['id','title','product_line'])->where(['is_meijie'=>1,'iszuofei'=>0])->all();

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

        $model = new Contract();
        //接收数据并处理
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        $model->contract_start=strtotime($model->contract_start);
        $model->contract_end=strtotime($model->contract_end);
        // $model->payment_time=strtotime($model->payment_time);
        $model->ctime=time();
        $model->audit=0;
        $model->audit_count=1;
        $model->audit_u=$model->submituser;
        $model->is_ultimate_shenhe=0;
        if($model->save())
        {
            return array('code'=>200,'mgs'=>'提交成功','data'=>$model);
        }else
        {
            return array('code'=>500,'mgs'=>'提交失败','data'=>$model->errors);
        }




    }


    //历史记录
    public function actionHistory($contract_id){

        $ht_info= Contract::meijie_contractinfo($contract_id);

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

        //时间条件
        $time_start=$getdata['time_start'];
        $time_end=$getdata['time_end'];
        if($time_start!="" and $time_end!="")
        {
            $time_start=strtotime($time_start);
            //$time_start=strtotime("-1 days",$time_start);
            $time_end=strtotime($time_end."+1 day");
            //$time_end=strtotime("+1 days",$time_end);
            $where.=" and payment_time >= $time_start and payment_time < $time_end";
            //  $fp_where.=" and ctime >= $time_start and ctime <= $time_end";

        }

        $list=MrenewHuikuan::find()
            ->with(['account0','submituser0'])
            ->where(['jd_mrenew_huikuan.mht_id'=>$contract_id])
            ->andWhere($where)
            ->andWhere('payment_type !=19')
            ->orderBy('payment_time desc,id desc')
            ->asArray()
            ->all();

        $list2=Beikuan::find()
            ->with(['account0','submituser0'])
            ->where(['beikuan.mht_id'=>$contract_id])
            ->andWhere($where)
            ->orderBy('payment_time desc,id desc')
            ->asArray()
            ->all();

        foreach ($list2 as $key=>$val)
        {
            $list2[$key]['payment_type2']=$val['payment_type'];
            $list2[$key]['payment_type']='0507'; //如果是备款就单独设置一个付款方式

        }

        if(!is_array($list))
        {
            $list=[];
        }
        if(!is_array($list2))
        {
            $list2=[];
        }

        $list=array_merge($list,$list2);



       // $ar['sum']=$list->sum('xf_cost');
        /*
        $provider = new ActiveDataProvider([
            'query' => $list,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'payment_time' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        */

        ArrayHelper::multisort($list, ['payment_time','id'], [ SORT_DESC,SORT_DESC]);


        $provider =new ArrayDataProvider(
          [
              'allModels'=>$list,

          ]
        );



        foreach ($provider->getModels() as $key=>$val)
        {
            if($val['payment_type']==1)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //续费预付
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['xf_id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>$val['money'],"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'充值',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['xf_cost'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
            }elseif($val['payment_type']==2)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //续费垫付
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['xf_id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>$val['money'],"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'充值',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['xf_cost'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
            }elseif($val['payment_type']==5)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'dakuan'])->one();
                ////打款
                $history_xf[]=array("kyue"=>$val['dakuan_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'打款',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);

            }elseif($val['payment_type']==17)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'refund_kehu'])->one();
                ////退打款
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'退打款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
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
                $history_xf[]=array("kyue"=>$val['dakuan_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['xf_id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>$val['money'],"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'退充值',"submitusers"=>$val['submituser0']['name'],"money"=>"+".$val['xf_cost'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
            }
            elseif($val['payment_type']==16)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //合同 转款
                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'转款',"submitusers"=>$val['submituser0']['name'],"money"=>$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
            }
            elseif($val['payment_type']==3)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //合同 媒介补款
                $history_xf[]=array("kyue"=>$val['dakuan_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'补款',"submitusers"=>$val['submituser0']['name'],"money"=>"+".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
            }elseif($val['payment_type']==41)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //合同 媒介补款
                $history_xf[]=array("kyue"=>$val['dakuan_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'打保证金',"submitusers"=>$val['submituser0']['name'],"money"=>"+".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
            }elseif($val['payment_type']==42)
            {
                //需要几审
                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                //合同 媒介补款
                $history_xf[]=array("kyue"=>$val['dakuan_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'退保证金',"submitusers"=>$val['submituser0']['name'],"money"=>"+".$val['money'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
            }elseif($val['payment_type']=='0507')
            {
                if($val['payment_type2']=='1' or $val['payment_type2']=='2')
                {
                    //需要几审
                    $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
                    //续费垫付
                    $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>$val['money'],"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'备款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['xf_cost'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
                }elseif ($val['payment_type2']=='3')
                {
                    //需要几审
                    $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'meijie-bukuan'])->one();
                    //续费垫付
                    $history_xf[]=array("kyue"=>$val['dakuan_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>$val['money'],"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'补款',"submitusers"=>$val['submituser0']['name'],"money"=>"+".$val['xf_cost'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
                }elseif ($val['payment_type2']=='155')
                {
                    //需要几审
                    $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'beikuan_account_tuikuan'])->one();
                    //续费垫付
                    $history_xf[]=array("kyue"=>$val['dakuan_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>$val['money'],"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'备款账户退款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['xf_cost'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
                }
//                //需要几审
//                $audit_jishen=AuditAction::find()->select('audit_count')->where(['action_name'=>'renew'])->one();
//                //续费垫付
//                $history_xf[]=array("kyue"=>$val['xf_qiane'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['xf_id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>"备款 百度币".($val['money']),"show_money"=>$val['money'],"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'备款',"submitusers"=>$val['submituser0']['name'],"money"=>"-".$val['xf_cost'],"audit_shencount"=>$audit_jishen['audit_count'],"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
               }
            //分配款
            if($val['is_huikuan']==1)
            {
                $history_xf[]=array("kyue"=>$val['backmoney_yue'],"xf_contractid"=>$val['xf_contractid'],"id"=>$val['id'],"date"=>date("Y-m-d",$val['payment_time']),"ctime"=>date("Y-m-d",$val['ctime']),"mes"=>$val['note'],"show_money"=>0,"audit"=>$val['audit'],"audit_count"=>$val['audit_count'],"type"=>'分配款',"submitusers"=>$val['submituser0']['name'],"money"=>"+".$val['money'],"audit_shencount"=>2,"is_ultimate_shenhe"=>$val['is_ultimate_shenhe'],"account"=>$val['account0'],"fandian"=>$val['rebates_proportion']);
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


    //转款页面
    public function actionZhuankuan($contract_id){
        $contract_info=Contract::meijie_contractinfo($contract_id);
        $yue=$contract_info['dakuan']+$contract_info['bukuan']-$contract_info['yu_e'];
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

        $info=Contract::meijie_contractinfo($id);
        $yue=$info['dakuan']+$info['bukuan']-$info['yu_e'];
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
                Contract::updateAllCounters(['dakuan'=>-$money],['id'=>$id]);
                //公司未分配余额增加
                JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e'=>$money],['id'=>$info['advertiser']]);

                $model=new MrenewHuikuan();
                $model->advertiser=$info['advertiser'];
                $model->product_line=$info['product_line'];
                $model->type=$info['type'];
                $model->money=$money;
                $model->payment_type=16;
                $model->payment_time=time();
                $model->submituser=\Yii::$app->user->getId();
                $model->xf_contractid=$info['id'];
                $model->is_huikuan=0;
                $model->market=$info['submituser'];
                //$model->xf_qiane=0;
                $model->audit=1;
                $model->audit_count=2;
                $model->audit_u=\Yii::$app->user->getId();
                $model->is_ultimate_shenhe=1;
                $model->ctime=time();
                $model->xf_qiane=$money;
                $model->mht_id=$id;
                if(!$model->save())
                {
                    return array("code"=>500,"data"=>$model->errors,"msg"=>'转款失败');
                }

                //--MrenewHuikuan::mrenew_huikuan($info['id']);

                $tr->commit(); //提交数据

            } catch (Exception $e) {
                //回滚
                $tr->rollBack();
                return $e->getMessage(); //返回自定义异常信息
            }

            return array('code'=>200,'mgs'=>'提交成功','data'=>1);



        }
    }



    //一级审核
    public function actionShenhe1($id){

        $tr = \Yii::$app->db->beginTransaction();
        try {
            //写入续费
            $this->global_audit($id,'meijie_contract',1,'jd_contract');
            Contract::mcontract_shenhe($id,1);
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
            $this->global_audit($id,'meijie_contract',2,'jd_contract');
            Contract::mcontract_shenhe($id,2);
            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }

        return array('code'=>"200",'msg'=>'审核成功');
    }


    public function actionShenhe3($id){
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $this->global_audit($id,'meijie_contract',3,'jd_contract');
            Contract::mcontract_shenhe($id,3);
            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }

        return array('code'=>"200",'msg'=>'审核成功');
    }

    //预留审核


    public function actionShenhe4($id){
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $this->global_audit($id,'meijie_contract',4,'jd_contract');
            Contract::mcontract_shenhe($id,4);
            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }

        return array('code'=>"200",'msg'=>'审核成功');
    }

    public function actionShenhe5($id){
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $this->global_audit($id,'meijie_contract',5,'jd_contract');
            Contract::mcontract_shenhe($id,5);
            $tr->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }

        return array('code'=>"200",'msg'=>'审核成功');
    }


    //批量复制账户
    public function actionCopyaccount(){
        $postdata=\Yii::$app->request->post();
        $ii=0;
        //如果是复制的合同 并且复制账户啦
        if(count($postdata['account_list']) > 0 && is_array($postdata['account_list']))
        {
            foreach ($postdata['account_list'] as $key=>$val){
                $info=Account::copyaccount($val,$postdata['mhtid']);
                if($ii->id){
                    $ii++;
                }
            }


        }

        return array('code'=>'200','msg'=>'修改成功');
    }


    //修改续费 退款 补账户币 账户所属的媒介合同
    public function actionUpdateaccountrenew($id,$account,$mhtid){
        $tr = \Yii::$app->db->beginTransaction();
        try {
        //媒介合同详情
        $mhtinfo=JdContract::findOne($mhtid);



        //复制账户到新的媒介合同
        $newaccount=Account::copyaccount($account,$mhtid);


        //如果是转款
        if(\Yii::$app->request->get('iszhuankuan')=='1')
        {
            //转款
            $renew_info=Zhuankuan::findOne($id);

            //先查出账户id 属于 account   还是 to_account  取得update 字段名称
            if($renew_info->account==$account)
            {
                $renew_info->account=$newaccount->id;
            }elseif($renew_info->to_account=$account)
            {
                $renew_info->to_account=$newaccount->id;
            }
            if(!$renew_info->save()){
                throw new Exception('修改失败请检查代码!');
            }else
            {
                //如果是续费是下单的话就修改同批次的实付金额及账户id
//                if($renew_info->pici!='')
//                {
//                    RenewHuikuan::updateAll(['account'=>$newaccount->id,'xf_cost'=>$renew_info->show_money/$fandian*$dlfandian],['pici'=>$renew_info->pici]);
//                }
            }
        }else
        {
            $fandian=($mhtinfo['rebates_proportion']+100)/100;//媒体返点
            $dlfandian=(100-$mhtinfo['dl_fandian'])/100;//代理返点


            //查出所属原账户所有的续费
            $renew_list=RenewHuikuan::find()->where(['account'=>$account,'is_ultimate_shenhe'=>'0','payment_type'=>[1,2,15]])->asArray()->all();

            foreach ($renew_list as $k=>$v)
            {
                //续费 退款 补账户币详情
                $renew_info=RenewHuikuan::findOne($v['id']);

                $renew_info->account=$newaccount->id;
                if(!isset($renew_info->show_money))
                {
                    throw new  \Exception('没有账户币?');
                }
                $renew_info->xf_cost=$renew_info->show_money/$fandian*$dlfandian;//续费成本

                if(!$renew_info->save()){
                    throw new Exception('修改失败请检查代码!');
                }
                //如果是续费是下单的话就修改同批次的实付金额及账户id
                if($renew_info->pici!='')
                {
                    RenewHuikuan::updateAll(['account'=>$newaccount->id,'xf_cost'=>$renew_info->show_money/$fandian*$dlfandian],['pici'=>$renew_info->pici]);
                }
            }


        }
            $tr->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>'200','msg'=>'修改媒介合同成功');
//
//        if($renew_info->save())
//        {
//            //如果是续费是下单的话就修改同批次的实付金额及账户id
//            if($renew_info->pici!='')
//            {
//                RenewHuikuan::updateAll(['account'=>$newaccount->id,'xf_cost'=>$renew_info->show_money/$fandian*$dlfandian],['pici'=>$renew_info->pici]);
//            }
//
//            return array('code'=>'200','msg'=>'修改媒介合同成功');
//        }else
//        {
//            var_dump($renew_info->errors);
//            return array('code'=>'500','msg'=>'修改失败');
//        }

    }

    //修改转款的账户信息
    public function actionUpdatezhuankuan($id,$account,$mhtid){
        //媒介合同详情
        $mhtinfo=JdContract::findOne($mhtid);
        //复制账户到新的媒介合同
        $newaccount=Account::copyaccount($account,$mhtid);

        if($renew_info->save())
        {
            return array('code'=>'200','msg'=>'修改媒介合同成功');
        }else
        {
            var_dump($renew_info->errors);
            return array('code'=>'500','msg'=>'修改失败');
        }



    }

}