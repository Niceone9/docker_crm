<?php

namespace backend\models;

use backend\modules\api\controllers\RefundMoneyController;
use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "zhuankuan".
 *
 * @property integer $id
 * @property integer $advertiser
 * @property double $money
 * @property double $show_money
 * @property string $rebates_proportion
 * @property string $note
 * @property integer $audit
 * @property integer $audit_u
 * @property integer $submituser
 * @property integer $ctime
 * @property integer $audit_count
 * @property integer $xf_contractid
 * @property integer $account
 * @property integer $to_account
 * @property integer $is_accomplish
 * @property integer $cu_ad_id
 * @property integer $payment_time
 * @property integer $zc_contractid
 */
class Zhuankuan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zhuankuan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser', 'money', 'submituser', 'ctime', 'xf_contractid','zc_contractid'], 'required'],
            [['advertiser', 'audit', 'audit_u', 'submituser', 'ctime', 'audit_count', 'xf_contractid', 'account', 'to_account', 'is_accomplish','is_ultimate_shenhe', 'cu_ad_id','payment_time','tk_id','xf_id'], 'integer'],
            [['money', 'show_money'], 'number'],
            [['note'], 'string'],
            [['rebates_proportion'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'advertiser' => 'Advertiser',
            'money' => 'Money',
            'show_money' => 'Show Money',
            'rebates_proportion' => 'Rebates Proportion',
            'note' => 'Note',
            'audit' => 'Audit',
            'audit_u' => 'Audit U',
            'submituser' => 'Submituser',
            'ctime' => 'Ctime',
            'audit_count' => 'Audit Count',
            'xf_contractid' => 'Xf Contractid',
            'account' => 'Account',
            'to_account' => 'To Account',
            'is_accomplish' => 'Is Accomplish',
            'cu_ad_id' => 'Cu Ad ID',
        ];
    }

    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }


    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'advertiser'])->select('id,advertiser,customer_type');
    }


    //关联审核人
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }

    //转出账户信息
    public function getZczhanghu(){
        return $this->hasOne(Account::className(),['id'=>'account'])->select('id,a_users');
    }
    //转入账户信息
    public function getZrzhanghu(){
        return $this->hasOne(Account::className(),['id'=>'to_account'])->select('id,a_users');
    }



    //媒介回款列表
    public static function zhuankuan_list($shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['zhuankuan.submituser'=>$uid],['ad.business'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['zhuankuan.submituser'=>$uid],['ad.business'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','zhuankuan.id',0];
        }



        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','zr.a_users' ,$Search_str],['like','zc.a_users' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));

        if($start and $end)
        {
            //$search_data=['and',['>=','zhuankuan.payment_time',$start],['<=','zhuankuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'zhuankuan.payment_time', $start], ['<', 'zhuankuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'zhuankuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'zhuankuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','zhuankuan.payment_time',1]];
        }
        //审核条件
        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="zhuankuan.audit=1 and zhuankuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="zhuankuan.audit=0 or zhuankuan.audit=1 and zhuankuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="zhuankuan.audit=2";
        }else
        {
            $shenhewhere="zhuankuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");

        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,zhuankuan.*,subt.name as submitname,zc.a_users as zcaccount,zr.a_users as zraccount ')->andWhere($shenhewhere)->andFilterWhere($userswhere)->andFilterWhere(['is_accomplish'=>$is_accomplish])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','advertiser0 ad','zczhanghu zc','zrzhanghu zr'])->asArray();
        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;
        $ar['sum']=$query->sum('zhuankuan.money');
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [

                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();



        return $ar;
    }



    //新增公司回款
    public static function add_zhuakuan(){
        $model=new Zhuankuan();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->payment_time=time();
        $model->ctime=time();

        if($model->money<0)
        {
            return array('code'=>500,'msg'=>'不能输入负数');
        }

        //跟系统计算账户比的公式进行对比。
       // Yii::$app->hjd->show_money($model->money,$model->xf_contractid,$model->show_money);

        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }
    }

    public static function zhuankuan_info($id){
        return self::find()->where(['zhuankuan.id'=>$id])->with(['submituser0','audituname','zczhanghu','zrzhanghu'])->joinWith(['advertiser0 b'=>function($query){
            $query->select("id,advertiser,customer_type,submituser,business")
                ->with(
                    ['submituser0'=>function($query){
                        $query->select(['id','name']);
                    }])
                ->with(
                    ['business0'=>function($query){
                        $query->select(['id','name']);
                    }]);

        }])->asArray()->one();
    }

    public static function zhuankuan_shenhe($id,$audit_type){

        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $backmoney_info=self::findOne($id);//回款详情

        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'zhuankuan'])->one();

        //如果审核通过 并且是最后一级审核
        if($audit=='1' and $audit_type==$renew_shenhe['audit_count'])
        {
            //读出转出账户详情和转入账户详情




            //查询转出账户详情
            $zc_accountinfo=Account::findOne($backmoney_info->account);
            //查询转入账户详情
            $zr_accountinfo=Account::findOne($backmoney_info->to_account);


            //读出转出账户的合同详情
            $zchtinfo=JdContract::contractinfo($zc_accountinfo->id);
            //读出转入合同详情
            $zrhtinfo=JdContract::contractinfo($zr_accountinfo->id);

            //exit;



            //生成一笔退款
            $tuikuan=RefundMoneyController::actionAddru([
                'advertiser'=>$backmoney_info->advertiser,
                'submituser'=>$backmoney_info->submituser,
                'product_line'=>$zchtinfo['mht']['prlin']['id'],
                'xf_contractid'=>$backmoney_info->zc_contractid,
                'market'=>$zchtinfo['market0']['id'],
                'payment_type'=>15,
                'payment_time'=>time(),
                'money'=>$backmoney_info->money,
                'rebates_proportion'=>$zchtinfo['rencontract']['fandian'],
                'show_money'=>Yii::$app->hjd->show_money($backmoney_info->money,$backmoney_info->zc_contractid),
                //'show_money'=>$backmoney_info->money*((100+$zchtinfo['rencontract']['fandian'])/100),
                'account'=>$backmoney_info->account,
                'note'=>'由转款生成的退款 crm系统操作',
                'cu_ad_id'=>$zc_accountinfo->cu_ad_id
            ]);



            //审核退款
            //查询退款到总账户需要几级审核 先查询是退款到总账户直开还是退款到总账户拼框

            //查询退款需要几级审核
            $renew_shenhe=AuditAction::find()->where(['action_name'=>'refund_account_z'])->one();
            for($i=1;$i<=$renew_shenhe['audit_count'];$i++)
            {
                Audit::audit_auto($tuikuan['data']['id'],'refund_account_z',$i,'jd_renew_huikuan');
                RenewHuikuan::account_shenhe_zhuankuan($tuikuan['data']['id'],$i,'refund_account_z','1');
            }
                /*
            if($zchtinfo['mht']['state']=='1')
            {
            }

            if($zchtinfo['mht']['state']=='2' or $zchtinfo['mht']['state']=='3' )
            {
                //查询退款需要几级审核

                $renew_shenhe=AuditAction::find()->where(['action_name'=>'refund_account_p'])->one();

                for($i=1;$i<=$renew_shenhe['audit_count'];$i++) {
                    Audit::audit_auto($tuikuan['data']['id'], 'refund_account_p', $i, 'jd_renew_huikuan');
                    //审核续费后续
                     $tuikuan['data']['id'];
                    RenewHuikuan::account_shenhe($tuikuan['data']['id'], $i, 'refund_account_p','1');
                }
            }
                */



            //生成续费
            $renew=RenewHuikuan::add_renew([
                'advertiser'=>$backmoney_info->advertiser,
                'submituser'=>$backmoney_info->submituser,
                'xf_contractid'=>$backmoney_info->xf_contractid,
                'market'=>$zrhtinfo['market0']['id'],
                'account'=>$backmoney_info->to_account,
                'rebates_proportion'=>$zrhtinfo['rencontract']['fandian'],
                'money'=>$backmoney_info->money,
                'rebates_proportion'=>$zrhtinfo['rencontract']['fandian'],
                'show_money'=>Yii::$app->hjd->show_money($backmoney_info->money,$backmoney_info->xf_contractid),
                //'show_money'=>$backmoney_info->money*((100+$zrhtinfo['rencontract']['fandian'])/100),
                'payment_type'=>'1',
                'payment_time'=>time(),
                'note'=>'由转款生成的续费 crm系统操作',
                'cu_ad_id'=>$zr_accountinfo->cu_ad_id
            ],'1');

            RenewHuikuan::shengcheng_xf_zhuanru($renew['id']);

            //查询续费需要几级审核
            $renew_shenhe=AuditAction::find()->where(['action_name'=>'renew'])->one();
            for($i=1;$i<=$renew_shenhe['audit_count'];$i++)
            {
                Audit::audit_auto($renew[id],'renew',$i,'jd_renew_huikuan');
                RenewHuikuan::renew_shenhe($renew[id],$i,'1');
            }

            //$this->global_audit($id,'refund_kehu',1,'jd_renew_huikuan');

            //JdCustomer::updateAllCounters(['qd_money'=>$backmoney_info->b_money],['id'=>$backmoney_info->advertiser]);
            //JdContract::updateAllCounters(['qd_money'=>$backmoney_info->b_money],['id'=>$backmoney_info->contract_id]);
            //修改退款的相关联id 保证退款和续费是一一对应
            //RenewHuikuan::updateAll(['relevance_id'=>$renew[id]],['id'=>$tuikuan['data']['id']]);

            //修改媒介退款和续费对应关系
            //查询生成的媒介续费和退款信息
            $mrenewinfo=MrenewHuikuan::find()->where(['xf_id'=>$renew['id']])->one();
            $mtuiinfo=MrenewHuikuan::find()->where(['xf_id'=>$tuikuan['data']['id']])->one();

            MrenewHuikuan::updateAll(['shishifu_onid'=>$mrenewinfo['id']],['id'=>$mtuiinfo['id']]);



            //修改退款id
            $backmoney_info->tk_id=$tuikuan['data']['id'];
            $backmoney_info->xf_id=$renew[id];



            //修改为最终审核
            $backmoney_info->is_ultimate_shenhe=1;
            $backmoney_info->save();

            //执行账户总续费变更
            \Yii::$app->hjd->updateaccountpay($backmoney_info->account);
            \Yii::$app->hjd->updateaccountpay($backmoney_info->to_account);
        }
    }
}
