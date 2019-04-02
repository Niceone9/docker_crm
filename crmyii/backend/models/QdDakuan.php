<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "jd_qd_dakuan".
 *
 * @property integer $id
 * @property integer $b_company
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
 * @property integer $type
 * @property string $zhanghu
 * @property string $shoukuanren
 * @property string $kaihuhang
 * @property integer $pici
 * @property integer $contract_id
 * @property integer $advertiser
 */
class QdDakuan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_qd_dakuan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['b_company', 'b_money', 'b_time', 'ctime', 'submituser'], 'required'],
            [['b_company', 'b_time', 'belongs', 'ctime', 'submituser',  'type', 'pici', 'contract_id', 'advertiser'], 'integer'],
            [['b_money','shuidian','sj_money'], 'number'],
            [['note'], 'string'],
            [['appname'], 'string', 'max' => 100],
            [['huikuanren', 'zhanghu', 'shoukuanren', 'kaihuhang'], 'string', 'max' => 255],
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
            'b_money' => 'B Money',
            'b_time' => 'B Time',
            'note' => 'Note',
            'belongs' => 'Belongs',
            'ctime' => 'Ctime',
            'submituser' => 'Submituser',
            'appname' => 'Appname',

            'huikuanren' => 'Huikuanren',
            'type' => 'Type',
            'zhanghu' => 'Zhanghu',
            'shoukuanren' => 'Shoukuanren',
            'kaihuhang' => 'Kaihuhang',
            'pici' => 'Pici',
            'contract_id' => 'Contract ID',
            'advertiser' => 'Advertiser',
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

    //关联打款主题
    public function getBcompany(){
        return $this->hasOne(AgentCompany::className(),['id'=>'b_company'])->select('id,companyname');
    }


    //媒介回款列表
    public static function qd_dakuan_list($shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['jd_qd_dakuan.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['jd_qd_dakuan.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_qd_dakuan.id',0];
        }



        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_qd_dakuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));

        if($start and $end)
        {
            //$search_data=['and',['>=','jd_qd_dakuan.b_time',$start],['<=','jd_qd_dakuan.b_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_qd_dakuan.b_time', $start], ['<', 'jd_qd_dakuan.b_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_qd_dakuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_qd_dakuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_qd_dakuan.b_time',1]];
        }
        //审核条件
        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_qd_dakuan.audit=1 and jd_qd_dakuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_qd_dakuan.audit=0 or jd_qd_dakuan.audit=1 and jd_qd_dakuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_qd_dakuan.audit=2";
        }else
        {
            $shenhewhere="jd_qd_dakuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");

        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_qd_dakuan.*,subt.name as submitname')->andWhere($shenhewhere)->andFilterWhere($userswhere)->andFilterWhere(['is_accomplish'=>$is_accomplish])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','advertiser0 ad'])->asArray();
        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;
        $ar['sum']=$query->sum('jd_qd_dakuan.b_money');
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
    public static function add_qddakuan(){
        $model=new QdDakuan();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->b_time=strtotime($model->b_time);
        $model->ctime=time();
        $model->type=2;
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

    public static function qdakuan_info($id){
        return self::find()->where(['jd_qd_dakuan.id'=>$id])->with(['submituser0','audituname','bcompany'])->joinWith(['advertiser0 b'=>function($query){
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

    public static function qddakuan_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $backmoney_info=self::findOne($id);//回款详情
        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'qd_dakuan'])->one();
        //如果审核通过 并且是最后一级审核
        if($audit=='1' and $audit_type==$renew_shenhe['audit_count'])
        {
            JdCustomer::updateAllCounters(['qd_money'=>$backmoney_info->b_money],['id'=>$backmoney_info->advertiser]);
            JdContract::updateAllCounters(['qd_money'=>$backmoney_info->b_money],['id'=>$backmoney_info->contract_id]);
            //修改为最终审核
            $backmoney_info->is_ultimate_shenhe=1;
            $backmoney_info->save();
        }
    }

}
