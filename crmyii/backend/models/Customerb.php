<?php

namespace backend\models;

use Yii;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "customerb".
 *
 * @property integer $id
 * @property string $advertiser
 * @property string $industry
 * @property string $website
 * @property string $appname
 * @property integer $submituser
 * @property integer $ctime
 * @property string $city
 * @property integer $r_time
 * @property integer $is_claim
 * @property integer $audit
 * @property integer $audit_u
 * @property string $site
 * @property integer $type
 * @property integer $ytime
 * @property integer $is_xu
 */
class Customerb extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customerb';
    }

    //页面显示分页信息
    public $serializer=[
        'class'=>'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser'], 'required'],
            //[['advertiser'],'unique','message'=>'这个公司已经存在了'],
            [['submituser', 'ctime', 'r_time', 'is_claim', 'audit', 'audit_u', 'type', 'ytime', 'is_xu'], 'integer'],
            [['advertiser', 'appname','true_advertiser'], 'string', 'max' => 50],
            [['industry'], 'string', 'max' => 20],
            [['website', 'city'], 'string', 'max' => 100],
            [['site','name','qq','weixin','position','tel','email'], 'string', 'max' => 255],
            [['submituser'],'iscount', 'skipOnEmpty' => false, 'skipOnError' => false],
            ['ctime','default', 'value' => time()],
            ['r_time','default', 'value' => time()],
          //  ['email','email']
        ];
    }
    //判断此销售是客户数量是否大于30
    public function iscount($attributs,$params){
      $count= self::find()->where(
            [
                'is_claim'=>'0',
                'ytime'=>'0',
                'submituser'=>$this->submituser
            ]
        )->count();
        if($count>30)
        {
            $this->addError($attributs, "拥有客户数量不得大于30！.".$count);
        }
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'advertiser' => 'Advertiser',
            'industry' => 'Industry',
            'website' => 'Website',
            'appname' => 'Appname',
            'submituser' => 'Submituser',
            'ctime' => 'Ctime',
            'city' => 'City',
            'r_time' => 'R Time',
            'is_claim' => 'Is Claim',
            'audit' => 'Audit',
            'audit_u' => 'Audit U',
            'site' => 'Site',
            'type' => 'Type',
            'ytime' => 'Ytime',
            'is_xu' => 'Is Xu',
        ];
    }

    //查询是否有数据 返回相应数据数量
    public static function wherecount($array){

       return self::find()->where($array)->count();
    }

    public function extraFields()
    {
        return ['submituser0','auditu'];
    }
    //提交人关联数据
    public function getSubmituser0(){
       return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }
    //审核人的关联数据
    public function getAuditu(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('id,name');
    }

    //销售只看自己的列表
    public static function market_list($user='',$shenhewhere2='',$role=''){

        if($user!=''){
            //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
            if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
            {
                $uid=Yii::$app->user->getId();
                $userswhere=['customerb.submituser'=>$uid];
            }elseif (Yii::$app->hjd->list_type($role)==2)
            {
                $uid=Yii::$app->hjd->asbumen();
                $userswhere=['customerb.submituser'=>$uid];

            }elseif (Yii::$app->hjd->list_type($role)==3)
            {
                $userswhere=['>','customerb.id',0];
            }
        }else
        {
            $userswhere=['>','customerb.id',0];
        }


        $Search_str=Yii::$app->request->post('Search_str');
        $searchwhere = ['or',['like','advertiser', $Search_str],['like','appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);

        if($start and $end)
        {
            $search_data=['and',['>','ctime',$start],['<','ctime',$end]];
        }else
        {
            $search_data=['and',['>','ctime',1]];
        }
        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="customerb.audit=1 and customerb.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="customerb.audit=0 or customerb.audit=1 and customerb.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="customerb.audit=2";
        }else
        {
            $shenhewhere="customerb.id>0";
        }

        $shenhewhere.=$shenhewhere2;


        //可认领条件 0不可认领，1 可认领
        $is_claim=Yii::$app->request->post('isrenling');

        //是否成单
        $is_chengdan=Yii::$app->request->post('is_chengdan');
        if($is_chengdan=='1')
        {
            $shenhewhere.=' and ytime >0';
        }
        if($is_chengdan=='0')
        {
            $shenhewhere.=' and ytime =0';
        }
        //查询自己的组
        $query=self::find()->with(['submituser0','auditu'])
            ->andWhere($userswhere)
            ->andWhere($shenhewhere)
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['audit'=>$shenhe])
            ->andFilterWhere(['is_claim'=>$is_claim])
            ->joinWith(['submituser0 subt'])
            ->asArray();
//        $a= clone $query;
//        echo $a->createCommand()->sql;
//        exit;
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
        $models = $provider->getModels();
        // 在当前页获取数据项的数目
        $count = $provider->getCount();
        // 获取所有页面的数据项的总数
        $totalCount = $provider->getTotalCount();

        $ar['list']=$models;
        $ar['count']=$count;
        $ar['totalCount']=$totalCount;

        return $ar;
    }


    //认领客户

    public static function renling($id,$usersid)
    {

        //查询是否已经满了客保数量
        $cu=self::find()->where("is_claim=0 and ytime=0 and submituser=$usersid")->count();

        if($cu>Yii::$app->params['kebao_count'])
        {
            return 'error-count';
        }
        //查询认领的客户是不是自己的
        $info=self::findOne($id);

        if($info['submituser']==$usersid)
        {
            return 'error-isme';
        }



        //正式认领操作
        $info->r_time=time();
        $info->audit=1;
        $info->is_claim=0;
        $info->submituser=$usersid;

        if($info->save()){
            return 'success';
        }else
        {
            return 'error-defeated';
        }



    }


}
