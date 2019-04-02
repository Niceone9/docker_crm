<?php

namespace backend\models;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * This is the model class for table "jd_back_money".
 *
 * @property integer $id
 * @property integer $b_company
 * @property integer $advertiser
 * @property string $b_money
 * @property integer $b_time
 * @property string $note
 * @property integer $belongs
 * @property integer $ctime
 * @property integer $submituser
 * @property string $appname
 * @property integer $audit_1
 * @property integer $audit_2
 * @property integer $susers1
 * @property integer $susers2
 * @property string $huikuanren
 */
class BackMoney extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_back_money';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['b_company', 'advertiser', 'b_money', 'b_time', 'ctime', 'submituser'], 'required'],
            [['b_company', 'advertiser', 'b_time', 'belongs', 'ctime', 'submituser', 'audit', 'audit_u', 'audit_count'], 'integer'],
            [['b_money'], 'number'],
            [['note'], 'string'],
            [['appname'], 'string', 'max' => 100],
            [['huikuanren'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'b_company' => 'B Company',
            'advertiser' => 'Advertiser',
            'b_money' => 'B Money',
            'b_time' => 'B Time',
            'note' => 'Note',
            'belongs' => 'Belongs',
            'ctime' => 'Ctime',
            'submituser' => 'Submituser',
            'appname' => 'Appname',
            'huikuanren' => 'Huikuanren',
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
    //关联主体公司

    public function getAgenCompany(){
        return $this->hasOne(AgentCompany::className(),['id'=>'b_company']);
    }


    public static function back_money_list($id='',$shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhere=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];
            if(Yii::$app->request->post('qudao')=='1')
            {
                $uid=Yii::$app->user->getId();
                $avidin=QudaoToAdvertiser::find()->select('advertiser_id')->where(['user_id'=>$uid])->column();
                $userswhere=['or',['ad.id'=>$avidin]];
            }

        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhere=['or',['ad.business'=>$uid],['ad.id'=>$adidlist],['jd_back_money.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_back_money.id',0];
        }



        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_back_money.appname' ,$Search_str],['like','jd_back_money.huikuanren' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));

        $end=strtotime(Yii::$app->request->post('end_date'). " +1 day");
        if($start and $end)
        {
            //$search_data=['and',['>=','jd_back_money.b_time',$start],['<=','jd_back_money.b_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_back_money.b_time', $start], ['<', 'jd_back_money.b_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_back_money.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_back_money.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_back_money.b_time',1]];
        }
        //审核条件
        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_back_money.audit=1 and jd_back_money.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_back_money.audit=0 or jd_back_money.audit=1 and jd_back_money.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_back_money.audit=2";
        }else
        {
            $shenhewhere="jd_back_money.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_ultimate_shenhe");

        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser as adname,jd_back_money.*,subt.name as submitname')
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['is_ultimate_shenhe'=>$is_accomplish])->andFilterWhere(['jd_contract.advertiser'=>$id])->andFilterWhere($searchwhere)->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','advertiser0 ad'])
            ->asArray();
        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;
        //总额
        $query_count=self::find()->andFilterWhere($userswhere)->andFilterWhere(['jd_back_money.audit'=>$shenhe,'is_ultimate_shenhe'=>$is_accomplish])->andFilterWhere(['jd_contract.advertiser'=>$id])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','advertiser0 ad'])->sum('jd_back_money.b_money');


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

        //总回款
        $ar['money_sum']=$query_count;

        return $ar;
    }

    //新增公司回款
    public static function add_backmoney(){
        $model=new BackMoney();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->b_time=strtotime($model->b_time);
        $model->ctime=time();
        if($model->b_money<0)
        {
            return array('code'=>500,'msg'=>'不能输入负数');
        }
        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }

    }

    //回款详情
    public static function backmonet_info($id){
        return self::find()->where(['jd_back_money.id'=>$id])->with(['submituser0','audituname','agenCompany'])->joinWith(['advertiser0 b'=>function($query){
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

    //审核操作
    public static function backmoney_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $backmoney_info=self::findOne($id);//回款详情
        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'backmoney'])->one();
        //如果审核通过 并且是最后一级审核
        if($audit=='1' and $audit_type==$renew_shenhe['audit_count'])
        {
            //修改客户金额 公司回款加 公司未分配余额加
            RenewHuikuan::money_change($backmoney_info['advertiser'],'','6',$backmoney_info['b_money']);
            //修改为最终审核
            $backmoney_info->is_ultimate_shenhe=1;
            $backmoney_info->save();

            //修改公司发票回款状态
            Invoice::up_invoice_state($backmoney_info['advertiser']);


        }

    }


    //分配款操作

    public static function fp_huikuanru(){

        $postdata=Yii::$app->request->post();
        $submitusers=Yii::$app->user->id;

        if($postdata['ht_id'])
        {
            foreach ($postdata['ht_id'] as $key=>$val)
            {
                //如果分配款等于0则跳过本次循环 执行下一次循环
                if ($postdata['pmoney'][$key]==0)
                {
                    continue;
                }

                //合同信息
                $htinfo=JdContract::findOne($postdata['ht_id'][$key]);
                //媒体合同信息
                //$mhtinfo=JdContract::findOne($htinfo['mht_id']);
                $model=new RenewHuikuan();
                //判断分配款类型   1 分配到回款  2 分配到保证金 3 分配到客户补款（负数）

                if($postdata['backmoney_type']=='')
                {
                    $postdata['backmoney_type']=1;
                }
                if($postdata['backmoney_type']=='1')
                {
                    $model->is_huikuan=1;
                    $model->backmoney_yue=$postdata['pmoney'][$key];
                    $model->note='协议分配款 由CRM系统生成 ';
                    $model->cu_ad_id=$postdata['cu_ad_id'];
                    $model->is_ultimate_shenhe=1;
                    $model->audit=1;
                    $model->audit_count=2;
                    $model->audit_u=$submitusers;

                    if($postdata['from_tuikuan']=='1')
                    {
                        $model->note='系统生成转入款，因为与做了退款抵扣';
                        $model->is_tk_deduction=1;
                    }
                }
                elseif($postdata['backmoney_type']=='2')
                {
                    $model->payment_type=4;
                    $model->backmoney_yue=$postdata['pmoney'][$key];
                    $model->note='协议分配保证金 由CRM系统生成 ';
                    $model->cu_ad_id=$postdata['cu_ad_id'];
                    $model->is_ultimate_shenhe=1;
                    $model->audit=1;
                    $model->audit_count=2;
                }elseif($postdata['backmoney_type']=='3')
                {
                    $model->payment_type=31;
                    $model->xf_qiane=$postdata['pmoney'][$key];
                    $model->note=$postdata['note'];
                    $model->cu_ad_id=$postdata['cu_ad_id'];
                    $model->is_ultimate_shenhe=1;
                    $model->audit=1;
                    $model->audit_count=2;
                }
                $model->xf_contractid=$htinfo['id'];
                $model->advertiser=$htinfo['advertiser'];
                $model->payment_time=time();
                $model->ctime=time();
                $model->money=$postdata['pmoney'][$key];




                $model->submituser=$submitusers;

                $model->ht_state=$htinfo['contract_state'];




                if($model->save())
                {
                    if($postdata['backmoney_type']=='1') {
                        //回款成功修改客户和合同回款总额
                            RenewHuikuan::money_change($htinfo['advertiser'], $htinfo['id'], '4', $postdata['pmoney'][$key]);
                    }
                    elseif($postdata['backmoney_type']=='2')
                    {
                        //分配保证金成功后协议保证金加
                        RenewHuikuan::money_change($htinfo['advertiser'], $htinfo['id'], '4-2', $postdata['pmoney'][$key]);
                    }
                    elseif($postdata['backmoney_type']=='3')
                    {
                        //生成一笔分配款 并给合同增加回款
                        $model_hk=new RenewHuikuan();
                        $model_hk->is_huikuan=1;
                        $model_hk->backmoney_yue=$postdata['pmoney'][$key];
                        $model_hk->note='协议分配款 由CRM系统生成 为抵消客户补款';
                        $model_hk->cu_ad_id=$postdata['cu_ad_id'];
                        $model_hk->is_ultimate_shenhe=1;
                        $model_hk->audit=1;
                        $model_hk->audit_count=2;
                        $model_hk->audit_u=$submitusers;
                        $model_hk->xf_contractid=$htinfo['id'];
                        $model_hk->advertiser=$htinfo['advertiser'];
                        $model_hk->payment_time=time();
                        $model_hk->ctime=time();
                        $model_hk->money=$postdata['pmoney'][$key];
                        $model_hk->submituser=$submitusers;
                        $model_hk->ht_state=$htinfo['contract_state'];
                        $model_hk->save();




                        RenewHuikuan::money_change($htinfo['advertiser'], $htinfo['id'], '4', $postdata['pmoney'][$key]);


                        $model->submituser=$submitusers;

                        $model->ht_state=$htinfo['contract_state'];
                        //分配保证金成功后协议保证金加
                        RenewHuikuan::money_change($htinfo['advertiser'], $htinfo['id'], '4-3', $postdata['pmoney'][$key]);
                    }
                    //减少公司未分配余额
                    if($postdata['from_tuikuan']=='1')
                    {
                        JdCustomer::updateAllCounters(['tuikuan_undistributed_yu_e'=>-$postdata['pmoney'][$key]],['id'=>$htinfo['advertiser']]);
                    }else {
                        RenewHuikuan::money_change_jian($htinfo['advertiser'], $htinfo['id'], '4', $postdata['pmoney'][$key]);
                    }
                    //回款对应
                    //--RenewHuikuan::renew_huikuan($htinfo['id'],$model->cu_ad_id);

                }
            }


        }

    }


}
