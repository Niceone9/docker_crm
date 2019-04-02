<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "jd_mrenew_huikuan".
 *
 * @property integer $id
 * @property integer $advertiser
 * @property integer $product_line
 * @property integer $type
 * @property integer $contract_start
 * @property integer $contract_end
 * @property string $rebates_proportion
 * @property double $show_money
 * @property double $money
 * @property integer $payment_type
 * @property integer $payment_time
 * @property string $note
 * @property integer $audit_1
 * @property integer $audit_2
 * @property integer $submituser
 * @property integer $ctime
 * @property string $appname
 * @property integer $users2
 * @property integer $susers1
 * @property integer $susers2
 * @property integer $xf_contractid
 * @property integer $is_huikuan
 * @property integer $account
 * @property integer $market
 * @property integer $audit_3
 * @property integer $susers3
 * @property integer $audit_4
 * @property integer $susers4
 * @property string $huikuanren
 * @property string $xf_cost
 * @property string $xf_qiane
 * @property integer $is_accomplish
 * @property integer $accomplish_users
 * @property string $backmoney_yue
 * @property integer $select id_id
 * @property string $dakuan_yue
 * @property integer $xf_id
 * @property string $shishifu_onid
 * @property integer $ht_state
 * @property integer $is_ultimate_shenhe
 */
class MrenewHuikuan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_mrenew_huikuan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser', 'money', 'payment_time', 'submituser', 'ctime', 'xf_contractid'], 'required'],
            [['advertiser', 'product_line', 'type', 'contract_start', 'contract_end', 'payment_type', 'payment_time', 'audit_u', 'audit', 'submituser', 'ctime', 'audit_u', 'audit_count', 'xf_contractid', 'is_huikuan', 'account', 'market', 'is_accomplish', 'accomplish_users', 'mht_id', 'xf_id', 'ht_state','margin_istui'], 'integer'],
            [['show_money', 'money', 'xf_cost', 'xf_qiane', 'backmoney_yue', 'dakuan_yue'], 'number'],
            [['note'], 'string'],
            [['rebates_proportion'], 'string', 'max' => 50],
            [['appname'], 'string', 'max' => 100],
            [['huikuanren'], 'string', 'max' => 255],
            ['shishifu_onid','default', 'value'=>0]
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
            'product_line' => 'Product Line',
            'type' => 'Type',
            'contract_start' => 'Contract Start',
            'contract_end' => 'Contract End',
            'rebates_proportion' => 'Rebates Proportion',
            'show_money' => 'Show Money',
            'money' => 'Money',
            'payment_type' => 'Payment Type',
            'payment_time' => 'Payment Time',
            'note' => 'Note',
            'submituser' => 'Submituser',
            'ctime' => 'Ctime',
            'appname' => 'Appname',
            'users2' => 'Users2',
            'xf_contractid' => 'Xf Contractid',
            'is_huikuan' => 'Is Huikuan',
            'account' => 'Account',
            'market' => 'Market',
            'huikuanren' => 'Huikuanren',
            'xf_cost' => 'Xf Cost',
            'xf_qiane' => 'Xf Qiane',
            'is_accomplish' => 'Is Accomplish',
            'accomplish_users' => 'Accomplish Users',
            'backmoney_yue' => 'Backmoney Yue',
            'mht_id' => 'Mht ID',
            'dakuan_yue' => 'Dakuan Yue',
            'xf_id' => 'Xf ID',
            'shishifu_onid' => 'Shishifu Onid',
            'ht_state' => 'Ht State',
        ];
    }

    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }
    //关联数据
    public function getMarket0(){
        return $this->hasOne(UserBackend::className(),['id'=>'market'])->select('id,name');
    }

    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'advertiser'])->select('id,advertiser,customer_type');
    }

    //关联账户
    public function getAccount0(){
        return $this->hasOne(Account::className(),['id'=>'account'])->select('id,a_users,state,a_password,promote_url,mt_payment_type,dk_date,dk_zhouqi');
    }
    //关联审核人
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }
    //关联数据
    public function getContract(){
        return $this->hasOne(JdContract::className(),['id'=>'mht_id'])->select('id,mht_id');
    }


    //增加公司和合同的续费  公司id 合同id  付款类型  金额  增加或减少
    public static function money_change($advertisers_id,$contract_id,$payment_type,$value,$type=''){
        if($type=='jian')
        {
            $value=-$value;
        }
        //媒介续费
        if($payment_type=='1' or $payment_type=='2')
        {

            JdCustomer::updateAllCounters(['myu_e'=>$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['yu_e'=>$value],['id'=>$contract_id]);
        }
        //给媒介打款
        if($payment_type=='4')
        {
            JdCustomer::updateAllCounters(['dakuan'=>$value],['id'=>$advertisers_id]);
            JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e'=>$value],['id'=>$advertisers_id]);
        }
        //媒介回款
        if($payment_type=='41')
        {
            JdCustomer::updateAllCounters(['huikuan'=>$value],['id'=>$advertisers_id]);
            JdCustomer::updateAllCounters(['undistributed_yu_e'=>$value],['id'=>$advertisers_id]);
        }
        //媒介退打款
        if($payment_type=='411')
        {
            JdCustomer::updateAllCounters(['dakuan'=>$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['dakuan'=>$value],['id'=>$contract_id]);
        }
        //媒介分配打款
        if($payment_type=='5')
        {
            JdContract::updateAllCounters(['dakuan'=>$value],['id'=>$contract_id]);
        }
        //客户提了打保证金-保证金 -客户给咱们的保证金
        if($payment_type=='5-2')
        {

            JdContract::updateAllCounters(['margin_money'=>$value],['id'=>$contract_id]);
            JdCustomer::updateAllCounters(['margin_money'=>$value],['id'=>$advertisers_id]);
        }
        //媒介分配打款-保证金
        if($payment_type=='5-2-1')
        {

            JdContract::updateAllCounters(['margin_meiti'=>$value],['id'=>$contract_id]);
            JdCustomer::updateAllCounters(['margin_meiti'=>$value],['id'=>$advertisers_id]);
        }


        //媒介补款
        if($payment_type=='3')
        {
            JdCustomer::updateAllCounters(['bukuan'=>$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['bukuan'=>$value],['id'=>$contract_id]);
        }
        //媒介罚款
        if($payment_type=='19')
        {
            JdCustomer::updateAllCounters(['fakuan'=>$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['fakuan'=>$value],['id'=>$contract_id]);
        }
    }
    //重跑备款
    public static function beikuan_renewdy(){
        $list=MrenewRelevance::find()->where(['type'=>'2'])->all();
        foreach ($list as $key=>$val)
        {
            Beikuan::updateAllCounters(['xf_qiane'=>-$val['fk_money']],['id'=>$val['xf_id']]);

        }

        $list2=BeikuanAccountToRenew::find()->all();
        foreach ($list2 as $key=>$val)
        {
            MrenewHuikuan::updateAllCounters(['xf_qiane'=>-$val['fk_money']],['id'=>$val['xf_id']]);

        }

    }

    //设置备款账户id
    public static function zhuankuan_updy(){
        $list=Zhuankuan::find()->where(['is_ultimate_shenhe'=>'1'])->asArray()->all();

        foreach ($list as $key=>$val)
        {
            //查询媒介退款信息
            $mrenewino=MrenewHuikuan::find()->where(['xf_id'=>$val['tk_id']])->one();

            $mrexfufeiinfo=MrenewHuikuan::find()->where(['xf_id'=>$val['xf_id']])->one();
            MrenewHuikuan::updateAll(['shishifu_onid'=>$mrexfufeiinfo['id']],['id'=>$mrenewino['id']]);

        }
    }




    //媒介续费对应打款
    public static function mrenew_huikuan($mhtid=''){
        //不跑跟备款账户绑定的续费 因为他会跟备款对应 而不是打款
        $l=\backend\models\BeikuanAccountToRenew::find()->select('xf_id')->column();
        $l=implode(',',$l);

        //循环续费欠额大于0的数据 并且对应该续费合同的回款
        $list=self::find()->select('id')->where("(payment_type=1 or payment_type=2 or payment_type=17 or payment_type=16) and xf_qiane>0 and audit!=2 and id not in($l) ")->andFilterWhere(['mht_id'=>$mhtid])->orderBy("payment_time asc")->asArray()->all();

        if($list){
            foreach ($list as $key=>$val)
            {
                self::mrenew_auto_huikuan($val['id']);
            }
        }

    }




    public static function mrenew_auto_huikuan($xf_id){
        //续费信息
        $xufei_info=self::findOne($xf_id);

        //此合同实时付对应的续费优先
        //此合同回款列表
        //$backmoney=self::find()->where("(payment_type=5 or payment_type=15)  and mht_id='$xufei_info[mht_id]' and audit!=2  and dakuan_yue >0 and shishifu_onid='$xf_id'")->orderBy('payment_time asc')->asArray()->all();
        $backmoney=self::find()->where("(payment_type=5 or payment_type=15)  and mht_id='$xufei_info[mht_id]' and audit!=2  and dakuan_yue >0 and shishifu_onid='$xf_id'")->orderBy('payment_time asc')->asArray()->all();

        if(!$backmoney)
        {
            //此合同回款列表e
            $backmoney=self::find()->where("(payment_type=5)  and mht_id='$xufei_info[mht_id]' and audit!=2  and dakuan_yue >0  and shishifu_onid =0")->orderBy("payment_time asc")->all();
        }


        foreach ($backmoney as $key=>$value)
        {
            $xufei_info=self::findOne($xf_id);
            //如果续费欠额为0则跳出本次循环
            if($xufei_info['xf_qiane']==0)
            {
                break ;
            }
            $yixufeihuikuan_date=new Myihuikuanxufei();
            $yixufeihuikuan_date->mt_fandian=$xufei_info['rebates_proportion'];
            $yixufeihuikuan_date->dl_fandian=0;
            $yixufeihuikuan_date->mht_id=$xufei_info[mht_id];
            $yixufeihuikuan_date->xf_fandian=0;
            $yixufeihuikuan_date->gr_fandian=0;
            $yixufeihuikuan_date->xs_fandian=0;
            $yixufeihuikuan_date->avid=$xufei_info['advertiser'];
            //销售
            $yixufeihuikuan_date->xsid=$xufei_info['market'];
            $yixufeihuikuan_date->xf_id=$xf_id;
            //$yixufeihuikuan_date->xs_tc=0;
            //$yixufeihuikuan_date->ht_id=$xufei_info[xf_contractid];



            //如果打款金额 大于 续费欠额 （余额大于续费金额）停止循环并返回续费欠额为0
            if($value['dakuan_yue']-$xufei_info['xf_qiane']>0) {
                //设置打款余额=打款金额-续费欠额
                self::updateAll(['dakuan_yue'=>$value['dakuan_yue']-$xufei_info['xf_qiane']],['id'=>$value['id']]);
                //增加已回款续费记录
                $yixufeihuikuan_date->hk_id = $value['id'];
                //谁大取谁
                if ($value['payment_time'] >= $xufei_info['payment_time']) {
                    $yixufeihuikuan_date->time = $value['payment_time'];
                } else
                {
                    $yixufeihuikuan_date->time = $xufei_info['payment_time'];
                }
                $yixufeihuikuan_date->money=$xufei_info['xf_qiane'];
                $yixufeihuikuan_date->shifu_money=$xufei_info['xf_qiane'];
                $yixufeihuikuan_date->save();
                //修改续费欠额
                self::updateAll(['xf_qiane'=>0],['id'=>$xf_id]);
                return ;
            }else{
                //如果回款余额 小于 续费金额  （此回款余额不足以抹平此续费）则继续执行循环 并把此回款的余额为0
                //增加已回款续费记录
                $yixufeihuikuan_date->hk_id=$value['id'];
                //谁大取谁
                if ($value['payment_time'] >= $xufei_info['payment_time']) {
                    $yixufeihuikuan_date->time = $value['payment_time'];
                } else
                {
                    $yixufeihuikuan_date->time = $xufei_info['payment_time'];
                }

                $yixufeihuikuan_date->money=$value['dakuan_yue'];
                $yixufeihuikuan_date->shifu_money=$value['dakuan_yue'];
                $yixufeihuikuan_date->save();
                //设置回款余额
                self::updateAll(['dakuan_yue'=>0],['id'=>$value[id]]);
                //修改续费欠额
                self::updateAll(['xf_qiane'=>$xufei_info['xf_qiane']-$value['dakuan_yue']],['id'=>$xf_id]);
            }
        }
    }

    //媒介退打款
    public static function tuikuan_list(){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
             $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','jd_mrenew_huikuan.id',0];
        }

        //筛选条件
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_mrenew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");

        if($start and $end)
        {
            //$search_data=['and',['>=','jd_mrenew_huikuan.payment_time',$start],['<','jd_mrenew_huikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.payment_time', $start], ['<', 'jd_mrenew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_mrenew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_mrenew_huikuan.payment_time',1]];
        }


        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=0 or jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_mrenew_huikuan.id>0";
        }



        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");




        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_mrenew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users')->where("jd_mrenew_huikuan.payment_type in(17)")->andWhere($shenhewhere)->andFilterWhere($userswhere)->andFilterWhere(['is_accomplish'=>$is_accomplish])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac'])->leftJoin('jd_contract ht','ht.id=jd_mrenew_huikuan.mht_id')->leftJoin("jd_product_line prlin","prlin.id=ht.product_line")->asArray();

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
    //退款审核
    public static function tuikuan_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $renew_info=self::findOne($id);//退款详情


        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie-refund-money'])->one();


        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {


            //修改媒介合同和媒介公司续费值
            self::money_change($renew_info['advertiser'],$renew_info['xf_contractid'],411,$renew_info['money'],'jian');
            //重跑媒介续费对应
            //--self::mrenew_huikuan($renew_info['xf_contractid']);




            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            $renew_info->save();
        }
    }

    //退款详情
    public static function tuikuaninfo($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','account0','advertiser0','audituname'])->asArray()->one();
        $contarat_info=JdContract::meijie_contractinfo($data['xf_contractid']);
        $data['contractinfo']=$contarat_info;
        return $data;
    }

    //媒介退打款
    public static function fakuan_list($shenhewhere2='',$role=''){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_mrenew_huikuan.id',0];
        }


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

//
//        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
//        {
//            $uid=Yii::$app->user->getId();
//            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];
//        }elseif (Yii::$app->hjd->list_type()==2)
//        {
//            $uid=Yii::$app->hjd->asbumen();
//
//
//            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];
//
//        }elseif (Yii::$app->hjd->list_type()==3)
//        {
//            $userswhere=['>','jd_mrenew_huikuan.id',0];
//        }

        //筛选条件
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_mrenew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");

        if($start and $end)
        {
           // $search_data=['and',['>=','jd_mrenew_huikuan.payment_time',$start],['<','jd_mrenew_huikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.payment_time', $start], ['<', 'jd_mrenew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_mrenew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_mrenew_huikuan.payment_time',1]];
        }


        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=0 or jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_mrenew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");




        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_mrenew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,ht.title')
            ->where("jd_mrenew_huikuan.payment_type in(19)")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['is_accomplish'=>$is_accomplish])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac'])
            ->leftJoin('jd_contract ht','ht.id=jd_mrenew_huikuan.mht_id')
            ->leftJoin("jd_product_line prlin","prlin.id=ht.product_line")
            ->asArray();

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
    //退款审核
    public static function fakuan_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $renew_info=self::findOne($id);//退款详情


        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie_fakuan'])->one();


        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {


            //修改媒介合同和媒介公司续费值
            self::money_change($renew_info['advertiser'],$renew_info['xf_contractid'],19,$renew_info['money']);
            //重跑媒介续费对应
            //--self::mrenew_huikuan($renew_info['xf_contractid']);




            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            $renew_info->save();
        }
    }

    //退款详情
    public static function fakuaninfo($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','account0','advertiser0','audituname'])->asArray()->one();
        $contarat_info=JdContract::meijie_contractinfo($data['xf_contractid']);
        $data['contractinfo']=$contarat_info;
        return $data;
    }






    //媒介续费列表
    public static function xufei_list($id=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
             $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','a.id',0];
        }


        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','mhtad.advertiser', $Search_str],['like','a.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");

        if($start and $end)
        {
           // $search_data=['and',['>=','a.payment_time',$start],['<','jd_mrenew_huikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'a.payment_time', $start], ['<', 'a.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'a.last_time', date("Y-m-d H:i:s",$start)], ['<', 'a.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','a.payment_time',1]];
        }

        $dk_date=Yii::$app->request->post('dk_date');

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="a.audit=1 and a.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="a.audit=0 or a.audit=1 and a.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="a.audit=2";
        }else
        {
            $shenhewhere="a.id>0";
        }
        //账户状态
        $account=Yii::$app->request->post("account_state");

        //下单条件
        if(Yii::$app->request->post('xiadan')==1)
        {
            $ordeid=RenewOrder::find()->select('xf_id')->column();
        }else
        {
            $ordeid=null;
        }

        //是否只显示垫款
        $is_diankuan=Yii::$app->request->post("diankuan");
        if($is_diankuan!='' && $is_diankuan=='1')
        {
            $payment_type="2";
        }else
        {
            $payment_type="1,2";
        }
        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");

        //是否有续费欠额
        //完成状态
        $is_qiane=Yii::$app->request->post("is_qiane");
        if($is_qiane=='1')
        {
            $qianewhere="a.xf_qiane >0";
        }if($is_qiane=='0')
        {
            $qianewhere="a.xf_qiane =0";
        }else
        {
            $qianewhere="a.id >0";
        }

        //产品线条件
        $pr_lin_id=Yii::$app->request->post("pr_lin_id");

        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser as adname,mhtad.advertiser as mhtadname,a.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,ac.mt_payment_type,ac.dk_date,ac.dk_zhouqi ')
            ->where('xf_qiane > 0 and a.payment_type in('.$payment_type.')')
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andWhere($qianewhere)
            ->andFilterWhere(['ac.state'=>$account,'is_accomplish'=>$is_accomplish,'prlin.id'=>$pr_lin_id,'dk_date'=>$dk_date])
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.id'=>$ordeid])
            ->andFilterWhere($searchwhere)->andFilterWhere($search_data)

            ->join("a left join",'jd_customer ad','ad.id=a.advertiser')
            ->leftJoin('user_backend subt','subt.id=a.submituser')
            ->leftJoin('jd_account ac','a.account=ac.id')
            ->leftJoin('jd_contract cont','cont.id=a.mht_id')
            ->leftJoin('jd_customer mhtad','mhtad.id=cont.advertiser')
            ->leftJoin("jd_product_line prlin","prlin.id=cont.product_line")
            ->leftJoin("user_backend markets","markets.id=a.market")
            ->asArray()
            ->all();


        $query2=Beikuan::find()
            ->select('ad.advertiser as adname,mhtad.advertiser as mhtadname,a.*,subt.name as submitname,subt.name as marketname,prlin.name as prlina,ac.a_users,ac.mt_payment_type,ac.dk_date,ac.dk_zhouqi ')
            ->where('xf_qiane > 0 and a.payment_type in('.$payment_type.')')
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andWhere($qianewhere)
            ->andFilterWhere(['ac.state'=>$account,'is_accomplish'=>$is_accomplish,'prlin.id'=>$pr_lin_id,'dk_date'=>$dk_date])
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.id'=>$ordeid])
            ->andFilterWhere($searchwhere)->andFilterWhere($search_data)
            ->join("a left join",'jd_customer ad','ad.id=a.advertiser')
            ->leftJoin('user_backend subt','subt.id=a.submituser')
            ->leftJoin('jd_account ac','a.account=ac.id')
            ->leftJoin('jd_contract cont','cont.id=a.mht_id')
            ->leftJoin('jd_customer mhtad','mhtad.id=cont.advertiser')
            ->leftJoin("jd_product_line prlin","prlin.id=cont.product_line")
            //->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac','contract cont'])
            ->asArray()
            ->all();

        foreach ($query2 as $key=>$val)
        {
            $query2[$key]['payment_type']='0507'; //如果是备款就单独设置一个付款方式
        }

        if(!is_array($query))
        {
            $query=[];
        }
        if(!is_array($query2))
        {
            $query2=[];
        }

        $query=array_merge($query,$query2);


        foreach ($query as $k=>$v)
        {
            $ar['qiane_sum']+=$v['xf_qiane'];
        }

        $provider =new ArrayDataProvider(
            [
                'allModels'=>$query,
            ]
        );

       // ArrayHelper::multisort($list, ['ctime'], [ SORT_DESC]);


        //$ar['qiane_sum']=$query->sum('jd_mrenew_huikuan.xf_qiane');
        //echo $a->createCommand()->sql;exit;
        /*
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
        */


        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();

        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();
        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();


        return $ar;
    }
    //续费详情

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function renewinfo($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','account0','advertiser0','audituname'])->asArray()->one();

        $contarat_info=JdContract::contractinfo_i($data['account']);
        //实付金额计算公式 2017年5月27日14:27:39

        //$dengyu=$data['show_money']/(1+$contarat_info['mht']['rebates_proportion']/100)*(1-$contarat_info['mht']['dl_fandian']/100);
        //$data['jisuangongshi']=$data['show_money']."/(1+".($contarat_info['mht']['rebates_proportion']/100).")*(1-".($contarat_info['mht']['dl_fandian']/100).")=".round($dengyu,2);
        $data['contractinfo']=$contarat_info;
        //查看续费详情
        if($data['pici']!='')
        {
            $pilist=self::find()->select(['id','money','show_money','payment_time','xf_cost'])->where("pici='$data[pici]'")->orderBy('id asc')->asArray()->all();
            $picimoney_sum=0;
            $picishowmoney_sum=0;
            foreach ($pilist as $pv)
            {
                $picimoney_sum+=$pv['money'];
                $picishowmoney_sum+=$pv['show_money'];
            }
            $data['picilist']=$pilist;
            $data['picimoney_sum']=$picimoney_sum;
            $data['picishowmoney_sum']=$picishowmoney_sum;
        }


        return $data;
    }


    public static function meitituikuaninfo($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','account0','advertiser0','audituname'])->asArray()->one();

        $contarat_info=JdContract::contractinfo_i($data['account']);
        //实付金额计算公式 2017年5月27日14:27:39

        //$dengyu=$data['show_money']/(1+$contarat_info['mht']['rebates_proportion']/100)*(1-$contarat_info['mht']['dl_fandian']/100);
        //$data['jisuangongshi']=$data['show_money']."/(1+".($contarat_info['mht']['rebates_proportion']/100).")*(1-".($contarat_info['mht']['dl_fandian']/100).")=".round($dengyu,2);
        $data['contractinfo']=$contarat_info;


        return $data;
    }




//补款列表
    public static function bukuan_list($id='',$shenhewhere2='',$role=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_mrenew_huikuan.id',0];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_mrenew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'). " +1 day");

        if($start and $end)
        {
            //$search_data=['and',['>=','jd_mrenew_huikuan.payment_time',$start],['<','jd_mrenew_huikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.payment_time', $start], ['<', 'jd_mrenew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_mrenew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_mrenew_huikuan.payment_time',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');
        if($shenhe=='1')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=0 or jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_mrenew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;

        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_mrenew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina')->where("jd_mrenew_huikuan.payment_type in(3)")->andFilterWhere($userswhere)->andWhere($shenhewhere)->andFilterWhere(['jd_contract.advertiser'=>$id])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','market0 markets','advertiser0 ad'])->leftJoin('jd_contract ht','ht.id=jd_mrenew_huikuan.mht_id')->leftJoin("jd_product_line prlin","prlin.id=ht.product_line")->asArray();

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


    //补款详情
    public static function bukuan_info($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname'])->asArray()->one();

        $contarat_info=JdContract::contractinfonomt($data['xf_contractid']);

        $data['contractinfo']=$contarat_info;

        return $data;
    }


    //新增补款
    public static function add_bukuan()
    {
        $model = new MrenewHuikuan();
        $postdata = \Yii::$app->request->post();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        $model->payment_time = strtotime($model->payment_time);
        $model->ctime = time();
        $model->payment_type=3;

        $model->dakuan_yue = $postdata['money'];
        if ($model->save()) {
            return $model;
        }else{
            return $model->errors;
        }
    }

    //补款审核操作
    public static function bukuan_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $renew_info=self::findOne($id);//退款详情

        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie-bukuan'])->one();


        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {
            //修改公司 合同补款值 （增）
            self::money_change($renew_info['advertiser'],$renew_info['xf_contractid'],3,$renew_info['money']);
            //--self::mrenew_huikuan($renew_info['xf_contractid']);

            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            $renew_info->save();
        }
    }


    //媒体按照产品线统计 流水
    public static function prlin_liushui($startTime,$endTime)
    {
        if($startTime!='default' && $endTime!='default') {
            $start = strtotime($startTime);
            $end = strtotime($endTime ." +1 day");

            $andwhere='a.payment_time>='.$start.' and a.payment_time <'.$end;
        }else
        {
            $andwhere='a.id !=0';
        }
        $moneylist=(new Query())->from('jd_mrenew_huikuan')
            ->select(['pr.name','pr.id','sum(xf_cost) as money'])
            ->where([
                'a.is_ultimate_shenhe'=>'1',
                'a.payment_type'=>[1,2]
            ])
            ->andWhere($andwhere)
            ->join('a left join','jd_contract c','c.id=a.mht_id')
            ->leftJoin('jd_product_line pr','pr.id=c.product_line')
            ->groupBy(['pr.name','pr.id'])->all();

        $tuikuanlist=(new Query())->from('jd_mrenew_huikuan')
            ->select(['pr.name','pr.id','sum(xf_cost) as money'])
            ->where([
                'a.is_ultimate_shenhe'=>'1',
                'a.payment_type'=>[15]
            ])
            ->andWhere($andwhere)
            ->join('a left join','jd_contract c','c.id=a.mht_id')
            ->leftJoin('jd_product_line pr','pr.id=c.product_line')
            ->groupBy(['pr.name','pr.id'])
            ->all();

        foreach ($moneylist as $key=>$value)
        {
            foreach ($tuikuanlist as $tkey =>$kval)
            {
                if($value['id']==$kval['id'])
                {
                    $moneylist[$key]['money']-=$kval['money'];
                }
            }

        }
        return $moneylist;
    }


    public static function pr_linglist(){

        //接受数据
        $prlinid=Yii::$app->request->get('prlinid');
        if($prlinid!='')
        {
            $where2=' and c.product_line='.$prlinid;
        }
        $year='';
        //接受数据
        $year=Yii::$app->request->get('year');
        $where="a.date like '%$year%'";




        $sql="SELECT to_char(to_timestamp(h.payment_time),'YYYY-MM') date,sum(h.xf_cost) as money,c.product_line,pl.name from jd_mrenew_huikuan h,jd_contract c,jd_product_line pl where h.mht_id = c.id and pl.id=c.product_line and h.is_ultimate_shenhe=1 and h.payment_type in(1,2) $where2  group by date,c.product_line,pl.name order by c.product_line";


        $sql2="SELECT to_char(to_timestamp(h.payment_time),'YYYY-MM') date,sum(h.xf_cost) as money,c.product_line,pl.name from jd_mrenew_huikuan h,jd_contract c,jd_product_line pl where h.mht_id = c.id and pl.id=c.product_line and h.is_ultimate_shenhe=1 and h.payment_type in(15) $where2  group by date,c.product_line,pl.name order by c.product_line";


        $prlin=Yii::$app->db->createCommand("select (sum(COALESCE(a.money,0))-sum(COALESCE(b.money,0))) as money,a.date from ($sql) a ,($sql2) b where  a.date=b.date and a.product_line=b.product_line and $where group by a.date order by a.date asc")->queryAll();

        $prlin=ArrayHelper::map($prlin,'date','money');


        $data=[];

        for ($i=1;$i<=12;$i++)
        {
            if($i<10)
            {
                $a='0'.$i;
            }else
            {
                $a=$i;
            }

            $data[$i-1]['date']=$year.'-'.$a;

            $data[$i-1]['money']=$prlin[$year.'-'.$a]?$prlin[$year.'-'.$a]:0;

        }

        return $data;


        /*
        //循环产品线
        foreach ($prlin as $key=>$value)
        {
            //循环月份12
            foreach ($month as $k=>$v)
            {

                $v['start']=strtotime($v['start']);
                $money=0;
                //续费利润
                foreach ($lirun as $k3=>$v3)
                {

                    foreach ($tuikuan as $k4=>$v4)
                    {
                        if($v3['date']==date("Y-m",$v['start']) && $v3['product_line']==$value['product_line']){
                            if($v3['date']==$v4['date'] && $v3['product_line']==$v4['product_line'])
                            {
                                $money=$v3['sum']-$v4['sum'];
                            }else
                            {
                                $money=$v3['sum'];
                            }

                        }

                    }


                }
                $prlin[$key]['data'][]=[
                    'start'=>date("Y-m-d",$v['start']),
                    'money'=>$money
                ];

            }
        }*/

        return($prlin);

    }


    public static function tuimargin($id){

        //修改保证金为已退款状态
        $one=MrenewHuikuan::findOne($id);
            //print_r($one);exit;
        if($one->xf_id){
            $xfinfo=RenewHuikuan::findOne($one->xf_id);
        }else{
            $xfinfo=$one;
        }

            //生成退款
            $mrenew_model=new MrenewHuikuan();
            $mrenew_model->advertiser=$xfinfo['advertiser'];
            $mrenew_model->submituser=$xfinfo['submituser'];
            $mrenew_model->type=1;//1续费 2 打款
            $mrenew_model->xf_contractid=$xfinfo['xf_contractid'];
            $mrenew_model->market=$xfinfo['market'];
            $mrenew_model->account=$xfinfo['account'];
            $mrenew_model->appname=$xfinfo['appname'];
            //金额改为百度币
            $mrenew_model->money=$xfinfo['money'];
            //获取媒体合同信息

            $meiinfo=JdContract::findOne($one['mht_id']);
            //获取媒介返点算出实付金额

            $mrenew_model->dakuan_yue=$xfinfo['money']; //续费欠额
            $mrenew_model->xf_cost=$xfinfo['money']; //续费实付
            $mrenew_model->money=$xfinfo['money']; //续费实付
            $mrenew_model->rebates_proportion=$meiinfo['rebates_proportion'];//媒介合同返点
            $mrenew_model->mht_id=$meiinfo['id'];//媒介合同id

            $mrenew_model->show_money=$xfinfo['show_money'];
            $mrenew_model->payment_type=42;
            $mrenew_model->payment_time=$xfinfo['payment_time'];
            $mrenew_model->note='生成的退保证金记录';
            $mrenew_model->contract_start=$xfinfo['contract_start'];
            $mrenew_model->contract_end=$xfinfo['contract_end'];
            $mrenew_model->ctime=time();
            $mrenew_model->audit_u=$xfinfo['audit_u'];
            $mrenew_model->audit=0;//1级默认审核通过
            $mrenew_model->audit_count=1;//2级默认审核通过
            $mrenew_model->xf_id=$one['id'];
            $mrenew_model->is_ultimate_shenhe=0;


            if(!$mrenew_model->save())
            {
                print_r($mrenew_model->errors);
                die("出现严重错误，媒介退保证金 记录自动生成失败。请联系CRM 管理员");
            }


    }


    //补款详情
    public static function tui_margin_info($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname'])->asArray()->one();

        $contarat_info=JdContract::contractinfo_mei($data['xf_contractid']);

        $data['contractinfo']=$contarat_info;

        return $data;
    }



    //媒介续费列表
    public static function meijietuikuan_list($id='',$shenhewhere2='',$role=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','a.id',0];
        }


        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','mhtad.advertiser', $Search_str],['like','a.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");

        if($start and $end)
        {
            //$search_data=['and',['>=','a.payment_time',$start],['<','jd_mrenew_huikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'a.payment_time', $start], ['<', 'a.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'a.last_time', date("Y-m-d H:i:s",$start)], ['<', 'a.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','a.payment_time',1]];
        }

        $dk_date=Yii::$app->request->post('dk_date');

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="a.audit=1 and a.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="a.audit=0 or a.audit=1 and a.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="a.audit=2";
        }else
        {
            $shenhewhere="a.id>0";
        }
        $shenhewhere.=$shenhewhere2;
        //账户状态
        $account=Yii::$app->request->post("account_state");

        //下单条件
        if(Yii::$app->request->post('xiadan')==1)
        {
            $ordeid=RenewOrder::find()->select('xf_id')->column();
        }else
        {
            $ordeid=null;
        }


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");

        //是否有续费欠额
        //完成状态
        $is_qiane=Yii::$app->request->post("is_qiane");
        if($is_qiane=='1')
        {
            $qianewhere="a.xf_qiane >0";
        }if($is_qiane=='0')
        {
            $qianewhere="a.xf_qiane =0";
        }else
        {
            $qianewhere="a.id >0";
        }

        //产品线条件
        $pr_lin_id=Yii::$app->request->post("pr_lin_id");

        //只显示退款到现金的
        $tkxjid=RenewHuikuan::find()->select('id')->where(['is_tuixianjin'=>'1'])->column();

        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser as adname,mhtad.advertiser as mhtadname,a.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,ac.mt_payment_type,ac.dk_date,ac.dk_zhouqi ')
            ->where(' a.payment_type in(15)')
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andWhere($qianewhere)
            ->andFilterWhere(['ac.state'=>$account,'is_accomplish'=>$is_accomplish,'prlin.id'=>$pr_lin_id,'dk_date'=>$dk_date])
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.id'=>$ordeid])
            ->andFilterWhere($searchwhere)->andFilterWhere($search_data)
            ->andWhere(['a.xf_id'=>$tkxjid])
            ->join("a left join",'jd_customer ad','ad.id=a.advertiser')
            ->leftJoin('user_backend subt','subt.id=a.submituser')
            ->leftJoin('jd_account ac','a.account=ac.id')
            ->leftJoin('jd_contract cont','cont.id=a.mht_id')
            ->leftJoin('jd_customer mhtad','mhtad.id=cont.advertiser')
            ->leftJoin("jd_product_line prlin","prlin.id=cont.product_line")
            ->leftJoin("user_backend markets","markets.id=a.market")
            ->asArray();






        // ArrayHelper::multisort($list, ['ctime'], [ SORT_DESC]);


     $ar['qiane_sum']=$query->sum('a.dakuan_yue');
        //echo $a->createCommand()->sql;exit;

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


    public static function meijie_tuikuanshenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回

        //默认通过
        if(empty($audit))
        {
            $audit=1;
        }

        $renew_info=self::findOne($id);//退款详情


        //是否做了抵扣如果抵扣的打款 未审核 就不可操作
        $dkttuik=DakuanToTuikuan::find()->where(['tk_id'=>$id])->orderBy('id desc')->one();

        if($dkttuik)
        {
            $dakinfo=MbackMoney::findOne($dkttuik['dk_id']);
            if($dakinfo['audit']!='2' and $dakinfo['is_ultimate_shenhe']!='1')
            {
              //  throw new \Exception('有包含此退款的打款正在进行中,所以暂时不能操作退款审核');
            }


            //如果要驳回此比退款 再如果此退款已经跟打款抵扣过了 并且没有驳回 ，那么这个退款不可以驳回。需要联系管理员
            if($audit=='2' && $dakinfo['audit']!='2')
            {
                throw new \Exception('此退款已经被打款抵消过了,所以不能操作退款驳回 请联系管理员！');
            }
        }






        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie_tuikuan'])->one();


        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {

            //获取媒介合同信息
            $meiinfo=JdContract::contractinfo_mei($renew_info['mht_id']);
            //echo $renew_info['xf_cost'];exit;
            //修改媒介合同和媒介公司续费值  减少
            MrenewHuikuan::money_change($meiinfo['advertiser'],$meiinfo['id'],1,$renew_info['xf_cost'],'jian');


            //公司和合同打款减少
            JdCustomer::updateAllCounters(['dakuan'=>-$renew_info['dakuan_yue']],['id'=>$meiinfo['advertiser']]);
            JdContract::updateAllCounters(['dakuan'=>-$renew_info['dakuan_yue']],['id'=>$meiinfo['id']]);

            //  var_dump($renew_info->);exit;
            //增加公司打款 和 未分配打款
            //MrenewHuikuan::money_change($renew_info['advertiser'],'',4,$renew_info['b_money']);
            //如果是实时付款的话 则 给合同分配款 并平掉实时付勾选中的续费欠额
            /*
            if($renew_info['ftype']==1)
            {

            }*/

            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            //修改备注内容
            if(\Yii::$app->request->post('audit')){
                $renew_info->note=date('Y-m-d').'退现金'.$renew_info->dakuan_yue.'__'.$renew_info->note;
            }
            //$renew_info->dakuan_yue=0;
            //修改打款时间
            //$renew_info->pici=time();
            $renew_info->save();






        }


        //如果是最终审核
        if($audit_type==$renew_shenhe['audit_count'] || $audit=='2'){


            //查询打款关联的父子关联  查备款的父级操作
            $fzflow=Flow::find()->where(['zi_flow'=>'meijie_tuikuan','is_true'=>1])->asArray()->all();
            foreach ($fzflow as $key=>$val)
            {
                if($val['fu_flow']=='tuikuan')
                {

                    //如果父流程等于退款 则 走退款的的审核流程  子最终审核 为父的审核
                    //查询关联对应父id
                    $fzdy=FlowGl::find()->where(['type'=>$val['id'],'ziid'=>$renew_info['id']])->one();

                    // BeikuanController::
                    //如果子流程为未审核状态才执行
                    $zituikuan=RenewHuikuan::findOne($fzdy['fuid']);

                    if($fzdy && $zituikuan->is_ultimate_shenhe!='1'){
                        Audit::audit_auto($fzdy['fuid'],'refund_account_z',$val['fu_audit'],'jd_renew_huikuan',$audit);
                        RenewHuikuan::account_shenhe($fzdy['fuid'],$val['fu_audit'],'refund_account_z',$audit);
                    }
                }
            }



        }
    }



}
