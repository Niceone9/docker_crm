<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\Account;
use backend\models\AccountCost;
use backend\models\AccountCostReal;
use backend\models\Beikuan;
use backend\models\BeikuanAccount;
use backend\models\BeikuanAccountToRenew;
use backend\models\BeikuanAccountToRenewAccount;
use backend\models\ContractRelevance;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\MrenewHuikuan;
use backend\models\MrenewRelevance;
use backend\models\ProductLine;
use backend\models\RenewHuikuan;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use backend\components\Quanxian;
use Yii;

class BeikuanAccountController extends ActiveController
{
    public $modelClass='backend\models\Account';

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

    //账户列表
    public function  actionAccount_list($id){
        $data['list']=BeikuanAccount::account_list($id);
        $data['code']=200;
        return $data;
    }

    //账户详情页面
    public function actionAccount_info($id){
        $data['data']=BeikuanAccount::account_info($id);
        $data['qudaolist']=JdCustomer::qudaolist();
        $data['code']=200;
        return $data;
    }

    //新增账户 接受合同id
    public function actionAccount_add($contract_id)
    {
        //产品线
        $data['code']='200';
        $data['contract_info']=JdContract::contractinfonomt($contract_id);
        $data['qudao_list']=JdCustomer::qudaolist();
        return $data;
    }

    //新增账户返回
    public function actionAccount_addru(){
        //账户
        $postdata=\Yii::$app->request->post();
        //获取相同账户
        $acc_copy=BeikuanAccount::find()
            ->select('a.*,b.advertiser as advertiser')
            ->join('a left join','jd_contract b','a.contract_id=b.id')
            ->where("a.a_users ='".$postdata['a_users']."' and a.endtime='4092599349'")
            ->orderBy("id desc")
            ->asArray()
            ->one();



        $model=new BeikuanAccount();
        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');

        if($model->ctime==''){
            $model->ctime=time();
        }else
        {
            $model->ctime = strtotime($model->ctime);
        }

        //格式化账户名称
        $model->a_users=trim($model->a_users);//删除左右两侧空格
        $model->a_users=str_replace(' ','',$model->a_users);




        //产品线详情
        $contract_id=$postdata['contract_id'];
        $contractin=ContractRelevance::find()->where("contract_id=$contract_id")->one();
        $prlin=ProductLine::findOne($contractin->product_line);
        $model->prlin_id=$contractin->product_line;

        /*
       if(\Yii::$app->params['yushan_synchronizing']=='true') {


           $url = "http://sem.yushanapp.com/sem/createhu";
           // $url ="http://192.168.199.171:5281/sem/createhu";
           $post_data = array(
               "name" => $postdata['appname'],
               "account_type" => $prlin['yushan_type'],
               "account_name" => $postdata['a_users'],
               "account_password" => $postdata['a_password'],
               "account_appid" => 'no token',
               "account_status" => '1',
               "fd_rate" => $postdata['fandian'] / 100,
               "view_type" => '120001',
               "server_type" => $postdata['server_type']
               // "qudao_id" => '',
           );


           //如果是正式账户
           if ($postdata['state'] == 0) {
               //账户所属公司不等于原账户公司
               if ($acc_copy['advertiser'] != $postdata['avid']) {

                   $yushan_data = \Yii::$app->hjd->hjd_post_curl($url, $post_data);
                   $yushan_id = $yushan_data->data->appid;
                   if ($yushan_id != '') {
                       $model->appid = $yushan_id;
                   } else {
                       $data['code'] = '500';
                       $data['msg'] = '新增账户羽扇平台数据同步失败';
                       $data['data'] = $yushan_data;
                       $data['crmdata'] = $post_data;
                       return $data;
                   }
               } else {
                   //如果新增账户所属公司是原账户的公司
                   $model->appid = $acc_copy['appid'];
               }
           }
       }
        */
        //如果之前有这个账户就结束掉
        if($acc_copy['id'])
        {
            BeikuanAccount::updateAll(['endtime'=>time()],['id'=>$acc_copy['id']]);
            //并且给新账户赋值旧账户的 媒体付款类型 时间 周期
            $model->mt_payment_type=$acc_copy['mt_payment_type'];
            $model->dk_date=$acc_copy['dk_date'];
            $model->dk_zhouqi=$acc_copy['dk_zhouqi'];
        }




        if($model->save())
        {


            $data['code']='200';
            $data['msg']='添加成功';
            $data['data']=$model;
        }else
        {
            $data['code']='500';
            $data['msg']='添加失败';
            $data['data']=$model->errors;
        }

        return $data;

    }

    //修改账户信息
    public function actionUpdateaccount($id){

         return BeikuanAccount::updataaccount($id);

    }

    //查询账户是否创建过  修改
    public function actionCheckaccount_up($a_users,$thisid){
        //添加账户的时候 判断这个账户之前是否被添加过
        $is_j=BeikuanAccount::find()->where("a_users ='$a_users' and endtime='4092599349' and id !=$thisid")->count();
        return $is_j;
    }
    //查询账户是否创建过 创建
    public function actionCheckaccount_add($a_users){
        //添加账户的时候 判断这个账户之前是否被添加过
        $is_j=BeikuanAccount::find()->where("a_users ='$a_users'")->count();
        return $is_j;
    }



    //check account is create
    public function actionCheck_date_add($date){
        $account_list=AccountCost::find()->where(['date'=>$date])->asArray()->all();

        $account_lista=ArrayHelper::getColumn($account_list,'account_name');

        //$accounst_
        $acl=Account::find()
            ->select('a_users')
            ->where(['endtime'=>'4092599349'])
            ->andWhere(['not in','a_users',$account_lista])
            ->asArray()
            ->all();
        $acl=ArrayHelper::getColumn($acl,'a_users');
        if(count($acl)>0){
            $acl="'".implode('\',\'',$acl)."'";
        }

        $sql="
select * from (
    select *,b.xufei-c.tuikuan-a.acxiaohao as yue from (
        select a_user,sum(cost) as acxiaohao from jd_account_cost_real where istuikuan=0 and a_user in(
        ".$acl."
        ) GROUP BY a_user
    ) a
    left join (
        select sum(a.no_cost_xf) as xufei,b.a_users from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 GROUP BY b.a_users
    ) b on a.a_user=b.a_users
    left join (
        select sum(a.show_money) as tuikuan,b.a_users from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.payment_type in(15) and a.is_ultimate_shenhe=1 GROUP BY b.a_users
    ) c on a.a_user=c.a_users
) c where yue >0
        
        ";


        $posts = \Yii::$app->db->createCommand($sql)
            ->queryAll();
        return $posts;
        exit;
    }



    public function actionAd_account_gl($id){
        $data=BeikuanAccount::find()->select('alias')->where(['avid'=>$id])->andWhere('alias notnull')->groupBy('alias')->asArray()->all();
        return array('code'=>'200','data'=>$data);
    }

    public function actionAccount_upalias(){
        $accountid=\Yii::$app->request->post('account_list');
        $alias=\Yii::$app->request->post('alias');
        if(empty($accountid) or empty($alias))
        {
            return false;
        }
        if(BeikuanAccount::updateAll(['alias'=>$alias],['id'=>$accountid]))
        {
            return array('code'=>'200','msg'=>'修改成功');
        }else
        {
            return array('code'=>'500','msg'=>'修改失败');
        }


    }

    //结束账户
    public function actionAccount_end($id){

        if(BeikuanAccount::updateAll(['endtime'=>time()],['id'=>$id]))
        {
            return array('cdoe'=>'200','msg'=>'操作成功');
        }else
        {
            return array('cdoe'=>'500','msg'=>'操作失败');
        }

    }


    //修改账户的媒体付款方式，打款时间，打款周期
    public function actionAccount_upkuan($accountid,$type,$dk_date='',$dk_zhouqi=''){
        $account=BeikuanAccount::findOne($accountid);
        $account->mt_payment_type=$type;

        if($dk_date){
            $account->dk_date=$dk_date;
        }
        if($dk_zhouqi)
        {
            $account->dk_zhouqi=$dk_zhouqi;
        }



        if($account->save()
        ){
            return array('code'=>'200','msg'=>'修改账户成功','data'=>BeikuanAccount::findOne($accountid));
        }else
        {
            var_dump($account->errors);exit;
            return array('code'=>'500','msg'=>'修改账户失败');
        }
    }

    //返回该媒介合同下的所有有余额的备款账户
    public function actionBeikuan_account($id){
        $list=BeikuanAccount::find()
            //->where("yu_e >0 ")
            ->where(['state'=>'0'])
            ->andWhere(['contract_id'=>$id])
            ->asArray()
            ->all();

        $account=\Yii::$app->request->post('account');
        foreach ($list as $key=>$val) {
            $c = BeikuanAccountToRenewAccount::find()
                ->where([
                    'account' => $account,
                    'bk_account_id' => $val['id']
                ])
                ->count();

            if ($c > 0)
            {
                $list[$key]['selected']=1;
            }else
            {
                $list[$key]['selected']=0;
            }
        }
        return $list;
    }


    //查询续费账户有没有和备款账户做关联 如果有 则默认选择

    public function actionBeikuan_account_quey(){
        $postdata=\Yii::$app->request->post();


        return BeikuanAccountToRenewAccount::find()
            ->select('bk_account_id')
            ->where(['account'=>$postdata['account']])
            ->count();


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


       //$l=\backend\models\BeikuanAccountToRenew::find()->select('xf_id')->column();
      //  $l=implode(',',$l);

     //   $mrelexf_id=MrenewRelevance::find()->select('xf_id')->asArray()->column();

     //   $mrelexf_idin=implode(',',$mrelexf_id);

        $list=MrenewHuikuan::find()
            ->select('a.*,b.a_users,b.mt_payment_type,b.dk_date,b.dk_zhouqi')
            ->join('a left join','jd_account b','a.account=b.id')
            ->where(['a.mht_id'=>$mhtid,'payment_type'=>[1,2]])
            ->andWhere(['>','a.xf_qiane',0])
            ->andFilterWhere(['b.dk_date'=>$dk_date,'b.mt_payment_type'=>$meiti_payment_type])
            ->andFilterWhere($search_data)
            //->andWhere("a.id not in ($mrelexf_idin)")
            //->andWhere("a.id not in ($l)")
            ->orderBy('a.payment_time asc')
            ->asArray()
            ->all();

        foreach ($list as $key=>$value)
        {
            $list[$key]['selected']=false;
        }

        return $list;
    }

    //备款账户和续费做绑定
    public function actionBeikuan_account_renew_binding(){
        $xf_id=\Yii::$app->request->post('xf_id');
        $fkmoney=Yii::$app->request->post('fk_money');
        $beikuan_account_id=\Yii::$app->request->post('beikuan_account_id');
        foreach ($xf_id as $key=>$val)
        {
            //读取续费详情
            $xf_info=MrenewHuikuan::findOne($val);

            //判断账户余额是否足以抵消续费续费
            $bk_ac_info=BeikuanAccount::findOne($beikuan_account_id);
            if($bk_ac_info->yu_e < $xf_info->xf_cost)
            {
                // throw new \Exception('不足以抵消！');
            }
            //插入数据
            $ba_t_ren= new BeikuanAccountToRenew();
            $ba_t_ren->bk_account_id=$beikuan_account_id;
            $ba_t_ren->xf_id=$xf_info->id;
            $ba_t_ren->fk_money=$fkmoney[$key];
            $ba_t_ren->save();




            //如果备款账户选择了一直关联
            $beiluan_account_gl=Yii::$app->request->post('beikuan_account_gl');
            if($beiluan_account_gl=='1')
            {
                //读取账户名字
                $a_users=Account::findOne($xf_info->account);

                //如果数据库里已经关联过这个账户就不新建啦
                $bglcont=BeikuanAccountToRenewAccount::find()->where(['bk_account_id'=>$beikuan_account_id,'account'=>$a_users])->count();
                if($bglcont==0){

                    $ba_gl=new BeikuanAccountToRenewAccount();
                    $ba_gl->bk_account_id=$beikuan_account_id;
                    $ba_gl->account=$a_users->a_users;
                    $ba_gl->save();
                }
            }

            //续费前额减
            MrenewHuikuan::updateAllCounters(['xf_qiane'=>-$fkmoney[$key]],['id'=>$val]);

            //备款账户减续费金额

            BeikuanAccount::updateAllCounters(['yu_e'=>-$fkmoney[$key]],['id'=>$beikuan_account_id]);
            JdContract::updateAllCounters(['beikuan_yue'=>-$fkmoney[$key]],['id'=>$bk_ac_info->contract_id]);
            JdCustomer::updateAllCounters(['beikuan_yue'=>-$fkmoney[$key]],['id'=>$bk_ac_info->avid]);
        }

        return array('code'=>'200','msg'=>'绑定成功');

    }


    public function actionBeikuanAccountStatus($id,$status){
        $beikuanaccount_info=BeikuanAccount::findOne($id);
        if($beikuanaccount_info->yu_e != '0')
        {
            return array('code'=>'500','msg'=>'账户有余额不可结束!');
        }

        if($status!=1 && $status!=0)
        {
            throw new Exception('参数错误!');
        }

        if(BeikuanAccount::updateAll(['state'=>$status],['id'=>$id]))
        {
            return array('code'=>'200','msg'=>'修改账户状态成功');
        }else
        {
            return array('code'=>'500','msg'=>'修改账户状态失败');
        }
    }

}