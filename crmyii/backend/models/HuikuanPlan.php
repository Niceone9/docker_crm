<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "huikuan_plan".
 *
 * @property integer $id
 * @property integer $advertiser
 * @property integer $submituser
 * @property integer $ctime
 * @property string $note
 * @property integer $receivable_day
 * @property string $last_time
 * @property string $money
 */
class HuikuanPlan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'huikuan_plan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser', 'submituser', 'ctime'], 'required'],
            [['advertiser', 'submituser', 'ctime', 'receivable_day'], 'integer'],
            [['note'], 'string'],
            [['last_time'], 'safe'],
            [['money'], 'number'],
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
            'submituser' => 'Submituser',
            'ctime' => 'Ctime',
            'note' => 'Note',
            'receivable_day' => '回款日',
            'last_time' => '最后审核时间',
            'money' => 'Money',
        ];
    }

    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }
    //关联数据
    public function getAd(){
        return $this->hasOne(JdCustomer::className(),['id'=>'advertiser'])->select('id,advertiser');
    }

    public static function getCustomer(){

    }

    public static function createPlan(){
        $model=new HuikuanPlan();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->ctime=time();
        $model->receivable_day = strtotime($model->receivable_day);
        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }
    }

    public static function upPlan($id){
        $model=HuikuanPlan::findOne($id);

        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->receivable_day = strtotime($model->receivable_day);
        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'修改失败','data'=>$model->errors);
        }
    }


    public static function getPlan($start_date='',$end_date='',$role=''){

        //获取用户拥有的访问function权限
        $rowes=\Yii::$app->hjd->get_roles();
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部


        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','huikuan_plan.id',0];
        }




        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','subt.name',$Search_str],['like','ad.advertiser',$Search_str]];
        //日期筛选条件
        if($start_date=='' && $end_date==''){
            $start=strtotime(Yii::$app->request->post('start_date'));
            $end=strtotime(Yii::$app->request->post('end_date'));
        }else{
            $start=strtotime($start_date);
            $end=strtotime($end_date);
        }
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>=','huikuan_plan.receivable_day',$start],['<','huikuan_plan.receivable_day',$end]];

        }else
        {
            $search_data=['and',['>','huikuan_plan.ctime',1]];
        }
        //查看单个公司
        $advertiser_id=Yii::$app->request->post('advertiser_id');



        //查询自己的组
        $query=self::find()
            ->select('huikuan_plan.receivable_day,huikuan_plan.advertiser,huikuan_plan.receivable_day,sum(money) as money,ad.advertiser as adname')
            ->andFilterWhere($userswhere)
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['ad.id'=>$advertiser_id])
            ->leftJoin('jd_customer ad','ad.id=huikuan_plan.advertiser')
            ->groupBy('huikuan_plan.receivable_day,huikuan_plan.receivable_day,ad.advertiser,huikuan_plan.advertiser')
            ->asArray();


        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [

                    'receivable_day' => SORT_ASC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        $return_data=$provider->getModels();

        foreach ($return_data as $key =>$val){
            $cha=self::getAdPlanTureHuikuanCost($val['advertiser'],$start,$end);
            $return_data[$key]['cha']=$val['money']-$cha;
            $return_data[$key]['huikuan']=$return_data[$key]['cha']<0 ?0:$return_data[$key]['cha'];
        }
        // 获取分页和排序数据
        $ar['data'] = $return_data;
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();

        return $ar;
    }
    public static function getPlanAd($role=''){

        //获取用户拥有的访问function权限
        $rowes=\Yii::$app->hjd->get_roles();
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部


        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','huikuan_plan.id',0];
        }



        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','subt.name',$Search_str],['like','ad.advertiser',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>=','huikuan_plan.receivable_day',$start],['<','huikuan_plan.receivable_day',$end]];

        }else
        {
            $search_data=['and',['>','huikuan_plan.ctime',1]];
        }


        //公司条件
        $avid=Yii::$app->request->post('avid');

        //查询自己的组
        $query=self::find()
            ->select('huikuan_plan.advertiser,sum(money) as money,ad.advertiser as adname,u.name')
            ->andFilterWhere($userswhere)
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['huikuan_plan.advertiser'=>$avid])
            ->leftJoin('jd_customer ad','ad.id=huikuan_plan.advertiser')
            ->leftJoin('user_backend u','u.id=ad.submituser')
            ->groupBy('ad.advertiser,huikuan_plan.advertiser,u.name')
            ->asArray();


        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [

                    'money' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        $return_data=$provider->getModels();


        foreach ($return_data as $key =>$val){
            $cha=self::getAdPlanTureHuikuanCost($val['advertiser'],$start,$end);
            $return_data[$key]['money']=round($return_data[$key]['money'],2);
            $return_data[$key]['cha']=$val['money']-$cha;
            $return_data[$key]['huikuan']=$return_data[$key]['cha']<0 ?0:round($return_data[$key]['cha'],2);
            $return_data[$key]['ratio']=round($return_data[$key]['huikuan']/($val['money']>0?$val['money']:1)*100,2);
            if($avid)
            {
                $back_money=BackMoney::find()->where(['advertiser'=>$avid])->orderBy('b_time desc')->one();
                if($back_money['b_time']>0)
                {
                    $return_data[$key]['huikuan_date']=date('Y-m-d',$back_money['b_time']);
                }else{
                    $return_data[$key]['huikuan_date']=0;
                }

            }
        }
        // 获取分页和排序数据
        $ar['data'] = $return_data;
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();

        return $ar;
    }
    public static function getPlanAdList(){
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);


        $month_one=strtotime(date("Y-m-01",$start));


        if($start and $end)
        {
            $search_data=['and',['>=','huikuan_plan.receivable_day',$start],['<','huikuan_plan.receivable_day',$end]];
            $search_dataBK=['and',['>=','jd_back_money.b_time',$month_one],['<','jd_back_money.b_time',$end]];
        }else
        {
            $search_data=['and',['>','huikuan_plan.ctime',1]];
            $search_dataBK=['and',['>=','jd_back_money.b_time',1]];
        }
        //查看单个公司
        $advertiser_id=Yii::$app->request->post('advertiser_id');



        //查询自己的组
        $query=self::find()
            ->select('id,receivable_day,advertiser,money')
            ->andFilterWhere($search_data)
            ->andFilterWhere(['advertiser'=>$advertiser_id])
            ->orderBy('receivable_day asc,ctime asc')
            ->asArray()
            ->all();

        $huikuan=BackMoney::find()
            ->where(['jd_back_money.advertiser'=>$advertiser_id,'is_ultimate_shenhe'=>'1'])
            ->andFilterWhere($search_dataBK)
            ->sum('b_money');

        foreach ($query as $key=>$val){
            $query[$key]['huikuan']=0;
            if(count($huikuan)<1){
                continue;
            }
            if($val['money']<$huikuan){
                $query[$key]['huikuan']=$val['money'];
                $huikuan-=$val['money'];
            }else{
                $query[$key]['huikuan']=$huikuan;
                $huikuan=0;

            }

        }

        return $query;


    }
    public static function getAdPlanTureHuikuanCost($avid,$start_date,$end_date,$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];

            //查询销售负责所有客户
            $uid=Yii::$app->user->getId();
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhereBK=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];

            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhereBK=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','huikuan_plan.id',0];
            $userswhereBK=['>','jd_back_money.id',0];
        }

        //日期筛选条件
        $start=$start_date;
        $month_one=strtotime(date("Y-m-01",$start));

        $end_plan=$end_date;

        $end=strtotime(date('Y-m-d', strtotime(date('Y-m-d',$month_one)." +1 month -1 day")));

        if($start and $end)
        {
            $search_data=['and',['>=','huikuan_plan.receivable_day',$month_one],['<','huikuan_plan.receivable_day',$end_plan]];
            $search_dataBK=['and',['>=','jd_back_money.b_time',$month_one],['<','jd_back_money.b_time',$end]];

        }else
        {
            $search_data=['and',['>=','huikuan_plan.ctime',1]];
            $search_dataBK=['and',['>=','jd_back_money.b_time',1]];
        }


        //查询自己的组
        $plan=self::find()
            ->select('sum(huikuan_plan.money) as money,ad.advertiser')
            ->where(['huikuan_plan.advertiser'=>$avid])
            ->andFilterWhere($userswhere)
            ->andFilterWhere($search_data)
            ->leftJoin('jd_customer ad','ad.id=huikuan_plan.advertiser')
            ->groupBy('ad.advertiser')
            ->indexBy('advertiser')
            ->asArray()
            ->one();


        $huikuan=BackMoney::find()
            ->select('sum(jd_back_money.b_money) as money,ad.advertiser')
            ->where(['jd_back_money.advertiser'=>$avid,'jd_back_money.is_ultimate_shenhe'=>'1'])
            ->andFilterWhere($userswhereBK)
            ->andFilterWhere($search_dataBK)
            ->leftJoin('jd_customer ad','ad.id=jd_back_money.advertiser')
            ->groupBy('ad.advertiser')
            ->indexBy('advertiser')
            ->asArray()
            ->one();


        return $plan['money']-$huikuan['money'];
    }

    public static function getPlanGroup($role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部


        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','huikuan_plan.id',0];
        }



        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        // $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>=','huikuan_plan.receivable_day',$start],['<=','huikuan_plan.receivable_day',$end]];

        }else
        {
            $search_data=['and',['>','huikuan_plan.ctime',1]];
        }



        //查询自己的组
        $query=self::find()
            ->select(["sum(huikuan_plan.money) as money","to_char(to_timestamp(receivable_day),'yyyy-MM-dd') as receivable_day"])
            ->andFilterWhere($userswhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt'])
            ->groupBy('receivable_day')
            ->orderBy('receivable_day asc')
            ->indexBy('receivable_day')
            ->asArray()
            ->all();

        foreach ($query as $key=>$value)
        {
            $chalist=self::getPlan($value['receivable_day'],$value['receivable_day']);
            //$chalist=self::getPlan('2018-12-01','2019-01-31');

            $weihuan=0;
            foreach ($chalist['data'] as $k=>$v)
            {
                $weihuan+=($v['money']-$v['huikuan'])<0?0:($v['money']-$v['huikuan']);
            }

            $query[$key]['weihuan']=$weihuan;
            $query[$key]['jindu']=100-($weihuan/$value['money'])*100;

            //$query[$key]['cha']=$value['money']-$cha;
            //$query[$key]['huikuan']=$query[$key]['cha']<0 ?0:$query[$key]['cha'];
        }

        return $query;
    }

    public static function GetPlanCost($role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];

            //查询销售负责所有客户
            $uid=Yii::$app->user->getId();
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhereBK=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];

            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhereBK=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','huikuan_plan.id',0];
            $userswhereBK=['>','jd_back_money.id',0];
        }

        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        //$end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>=','huikuan_plan.receivable_day',$start],['<=','huikuan_plan.receivable_day',$end]];
            $search_dataBK=['and',['>=','jd_back_money.b_time',$start],['<=','jd_back_money.b_time',$end]];

        }else
        {
            $search_data=['and',['>=','huikuan_plan.ctime',1]];
            $search_dataBK=['and',['>=','jd_back_money.b_time',1]];
        }


        //查询自己的组
        $plan=self::find()
            ->andFilterWhere($userswhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','ad ad'])
            ->sum('huikuan_plan.money');

        $huikuan=BackMoney::find()
            ->andFilterWhere($userswhereBK)
            ->andFilterWhere($search_dataBK)
            ->andWhere(['is_ultimate_shenhe'=>'1'])
            ->joinWith(['submituser0 subt','advertiser0 ad'])
            ->sum('jd_back_money.b_money');



        $jihuaad=self::find()
            ->select('ad.id')
            ->andFilterWhere($userswhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','ad ad'])
            ->asArray()
            ->all();

        $jihuaadarr=[];
        foreach ($jihuaad as $key=>$val){
            $jihuaadarr[$key]=$val['id'];
        }


        $adhuikuan=BackMoney::find()
            ->andFilterWhere($userswhereBK)
            ->andFilterWhere($search_dataBK)
            ->andWhere(['jd_back_money.is_ultimate_shenhe'=>'1','jd_back_money.advertiser'=>$jihuaadarr])
            ->joinWith(['submituser0 subt','advertiser0 ad'])
            ->sum('jd_back_money.b_money');

        return [
            'plan'=>$plan?$plan:0,
            'huikuan'=>$huikuan?$huikuan:0,
            'planhuikuantoad'=>$adhuikuan?$adhuikuan:0
        ];

    }


    public static function GetOverdueAd($role=''){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];

            //查询销售负责所有客户
            $uid=Yii::$app->user->getId();
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhereBK=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $userswhere=['or',['huikuan_plan.submituser'=>$uid]];

            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhereBK=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','huikuan_plan.id',0];
            $userswhereBK=['>','jd_back_money.id',0];
        }

        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        //$end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>=','huikuan_plan.receivable_day',$start],['<=','huikuan_plan.receivable_day',$end]];
            $search_dataBK=['and',['>=','jd_back_money.b_time',$start],['<=','jd_back_money.b_time',$end]];

        }else
        {
            $search_data=['and',['>=','huikuan_plan.ctime',1]];
            $search_dataBK=['and',['>=','jd_back_money.b_time',1]];
        }


        //查询自己的组
        $plan=self::find()
            ->select('sum(huikuan_plan.money) as money,ad.advertiser,u.name')
            ->andFilterWhere($userswhere)
            ->andFilterWhere($search_data)
            ->leftJoin('jd_customer ad','ad.id=huikuan_plan.advertiser')
            ->leftJoin('user_backend u','u.id=ad.submituser')
            ->groupBy('ad.advertiser,u.name')
            ->indexBy('advertiser')
            ->asArray()
            ->all();


        $huikuan=BackMoney::find()
            ->select('sum(jd_back_money.b_money) as money,ad.advertiser')
            ->andFilterWhere($userswhereBK)
            ->andFilterWhere($search_dataBK)
            ->andWhere(['jd_back_money.is_ultimate_shenhe'=>'1'])
            ->leftJoin('jd_customer ad','ad.id=jd_back_money.advertiser')
            ->groupBy('ad.advertiser')
            ->indexBy('advertiser')
            ->asArray()
            ->all();

        $overdue_ad=[];

        foreach ($plan as $key=>$val){
            if(isset($huikuan[$key])){
                if(($val['money']-$huikuan[$key]['money'])>0){
                    array_push($overdue_ad,['ad_name'=>$key,'plan'=>$val['money'],'repay'=>$huikuan[$key]['money'],'uname'=>$val['name']]);
                }
            }else{

                array_push($overdue_ad,['ad_name'=>$key,'plan'=>$val['money'],'repay'=>0,'uname'=>$val['name']]);
            }

        }

        return $overdue_ad;
    }
}
