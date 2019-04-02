<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "contract_accounts".
 *
 * @property integer $id
 * @property integer $advertiser
 * @property integer $audit
 * @property integer $audit_count
 * @property integer $submituser
 * @property integer $ctime
 * @property integer $audit_u
 * @property integer $is_ultimate_shenhe
 * @property string $note
 * @property integer $payment_type
 * @property integer $zhouqi
 * @property integer $contract_start
 * @property integer $contract_end
 * @property integer $iszuofei
 * @property integer $isguidang
 * @property string $last_time
 * @property integer $cu_ad_id
 */
class ContractAccounts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract_accounts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser', 'submituser', 'ctime','money'], 'required'],
            [['advertiser', 'audit', 'audit_count', 'submituser', 'ctime', 'audit_u', 'is_ultimate_shenhe', 'payment_type', 'zhouqi', 'contract_start', 'contract_end', 'iszuofei', 'isguidang', 'cu_ad_id'], 'integer'],
            [['note'], 'string'],
            [['last_time'], 'safe'],
            [['money','show_money'], 'number'],
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
            'audit' => 'Audit',
            'audit_count' => 'Audit Count',
            'submituser' => 'Submituser',
            'ctime' => 'Ctime',
            'audit_u' => 'Audit U',
            'is_ultimate_shenhe' => '是否是最终审核 0不是 1是',
            'note' => 'Note',
            'payment_type' => 'Payment Type',
            'zhouqi' => '回款周期',
            'contract_start' => 'Contract Start',
            'contract_end' => 'Contract End',
            'iszuofei' => '是否作废',
            'isguidang' => 'Isguidang',
            'last_time' => '最后审核时间',
            'cu_ad_id' => '客户下 广告主id',
        ];
    }



    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }


    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'advertiser'])->select('id,advertiser,business,customer_type,true_advertiser');
    }

    //关联数据
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }
    //关联合同信息
    //关联数据
    public function getRencontract(){
        return $this->hasMany(ContractRelevanceNew::className(),['contract_id'=>'id']);
    }
    //关联数据
    public function getMarket0(){
        return $this->hasOne(UserBackend::className(),['id'=>'market'])->select('id,name');
    }

    /*
        public function getDailigongsi(){
        return $this->hasOne(ProductLine::className(),['id'=>'product_line']);
    }*/


    //销售只看自己的列表
    public static function contract_list($avid,$shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['contract_accounts.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.business'=>$uid],['contract_accounts.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','contract_accounts.id',0];
        }

        if($avid!='All')
        {
            $id=$avid;
        }else
        {
            $id=null;
        }

        $customer_type=Yii::$app->request->post('customer_type'); //1客户，2媒体
        if($customer_type=='1' or $customer_type=='')
        {
            $customerwhere=[1,2];
        }else
        {
            $customerwhere=[3];
        }



        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            //$search_data=['and',['>','contract_accounts.ctime',$start],['<','contract_accounts.ctime',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'contract_accounts.ctime', $start], ['<', 'contract_accounts.ctime', $end]];
            }else{
                $search_data = ['and', ['>=', 'contract_accounts.last_time', date("Y-m-d H:i:s",$start)], ['<', 'contract_accounts.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','contract_accounts.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="contract_accounts.audit=1 and contract_accounts.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="contract_accounts.audit=0 or contract_accounts.audit=1 and contract_accounts.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="contract_accounts.audit=2";
        }else
        {
            $shenhewhere="contract_accounts.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //归档条件
        $isguidang=Yii::$app->request->post('isguidang');




        //查询自己的组
        $query=self::find()->select('contract_accounts.*,subt.name as submitname,cuad.advertiser as guanggaozhu')
            ->joinWith(['advertiser0 ad','submituser0 subt'])
            ->leftJoin('customer_advertiser cuad','contract_accounts.cu_ad_id=cuad.id')
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['contract_accounts.advertiser'=>$id,'contract_accounts.isguidang'=>$isguidang,'ad.customer_type'=>$customerwhere])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->distinct()
            ->asArray();
//         $a=clone $query;
//           echo $a->createCommand()->sql;exit;
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

    //修改合同字段
    public static function updataContract($id,$field,$sava_data){
        $data=self::findOne($id);

        $data->$field=$sava_data;

        if($data->update())
        {
            return true;
        }else
        {
            var_dump($data->errors);exit;
        }
    }

    //查看合同详情
    public static function contractinfo($id){

        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname'])->asArray()->one();




        return $data;
    }





    //新增合同返回
    public static function add_countract_ru(){
        $postdata=\Yii::$app->request->post();


        $hetong=new ContractAccounts();


        $hetong->load(\Yii::$app->getRequest()->getBodyParams(),'');

        //$hetong->contract_start=strtotime($hetong->contract_start);
        //$hetong->contract_end=strtotime($hetong->contract_end);
        //$hetong->payment_time=strtotime($hetong->payment_time);
        $hetong->ctime=time();
        $hetong->contract_start=strtotime($hetong->contract_start);
        $hetong->contract_end=strtotime($hetong->contract_end);


        if($hetong->save())
        {

            return $hetong;

        }else{

            return array("code"=>'500',"msg"=>"提交合同失败","error"=>$hetong->errors);
        }
    }


    //合同审核操作
    public static function contract_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $contract=self::findOne($id);//合同详情
        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'contract_accounts'])->one();
        //如果审核通过 并且是最后一级审核

        if($audit!='2' and $audit_type==$renew_shenhe['audit_count'])
        {
            //修改为最终审核
            $contract->is_ultimate_shenhe=1;
            if(!$contract->save()){
                var_dump($contract->errors);
                exit;

            }

        }
    }






    //根据时间段获取合同列表
    public static function contract_data_list($start,$end){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['contract_accounts.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['contract_accounts.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','contract_accounts.id',0];
        }

        //时间条件
        $start=strtotime($start);
        $end=strtotime($end . "+1 day");



        $search_data=['and',['>','contract_accounts.ctime',$start],['<','jd_contract.ctime',$end]];

        return self::find()->where($search_data)->with(['advertiser0','market0'])->andWhere($userswhere)->all();


    }

    //相同合同号数量
    public static function contract_no_num($contract_no){
        $num=ContractNew::find()->where("contract_no like '{$contract_no}%'")->count('id');
        if($num<10 && $num!='')
        {
            $num='0'.$num;
        }
        return $contract_no.'-'.$num;

    }


}
