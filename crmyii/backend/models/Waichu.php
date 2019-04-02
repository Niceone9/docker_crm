<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "jd_waichu".
 *
 * @property integer $id
 * @property string $bumen
 * @property string $zhiwu
 * @property string $shiyou
 * @property integer $starttime
 * @property integer $endtime
 * @property integer $submituser
 * @property integer $audit_1
 * @property integer $audit_2
 * @property integer $ctime
 * @property string $gongsi
 * @property string $lianxiren
 * @property string $tel
 * @property string $dizhi
 * @property integer $susers1
 * @property integer $susers2
 */
class Waichu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_waichu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shiyou', 'starttime', 'endtime', 'submituser', 'ctime'], 'required'],
            [['shiyou'], 'string'],
            [['starttime', 'endtime', 'submituser', 'audit', 'audit_count', 'ctime', 'audit_u'], 'integer'],
            [['bumen', 'zhiwu', 'lianxiren', 'tel'], 'string', 'max' => 50],
            [['gongsi'], 'string', 'max' => 100],
            [['dizhi'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bumen' => 'Bumen',
            'zhiwu' => 'Zhiwu',
            'shiyou' => 'Shiyou',
            'starttime' => 'Starttime',
            'endtime' => 'Endtime',
            'submituser' => 'Submituser',
            'audit_1' => 'Audit 1',
            'audit_2' => 'Audit 2',
            'ctime' => 'Ctime',
            'gongsi' => 'Gongsi',
            'lianxiren' => 'Lianxiren',
            'tel' => 'Tel',
            'dizhi' => 'Dizhi',
            'susers1' => 'Susers1',
            'susers2' => 'Susers2',
        ];
    }


    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }



    //关联审核人
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }



    public static function waichu_list($shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();


            $userswhere=['or',['jd_waichu.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $userswhere=['or',['jd_waichu.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_waichu.id',0];
        }



        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>','jd_waichu.ctime',$start],['<','jd_waichu.ctime',$end]];
        }else
        {
            $search_data=['and',['>','jd_waichu.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_waichu.audit=1 and jd_waichu.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_waichu.audit=0 or jd_waichu.audit=1 and jd_waichu.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_waichu.audit=2";
        }else
        {
            $shenhewhere="jd_waichu.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");

        //收支类型
        $f_type=Yii::$app->request->post("f_type");

        //查询自己的组
        $query=self::find()->select('jd_waichu.*,subt.name as submitname')->andWhere($shenhewhere)->andFilterWhere($userswhere)->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt'])->asArray();
        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;
        //总额
        //  $query_count=self::find()->andFilterWhere($userswhere)->andFilterWhere(['jd_invoice.audit'=>$shenhe,'is_accomplish'=>$is_accomplish])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','advertiser0 ad'])->sum('jd_back_money.b_money');


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

    public static function add_waichu(){
        $model=new Waichu();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->ctime=time();
        $model->starttime=strtotime($model->starttime);
        $model->endtime=strtotime($model->endtime);
        if($model->save())
        {

            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }

    }

    public static function waichu_info($id){
        //具体数据
        $data1=self::find()->where(['id'=>$id])->With(['submituser0','audituname'])->asArray()->one();
        $data['code']='200';
        $data['data']=$data1;
        return $data;
    }


}
