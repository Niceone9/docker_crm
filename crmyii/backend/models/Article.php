<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $views
 * @property integer $is_delete
 * @property integer $created_at
 * @property integer $updated_at
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }




    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['views', 'is_delete', 'created_at', 'updated_at','fuid','submituser'], 'integer'],
            [['title'], 'string', 'max' => 100],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'views' => 'Views',
            'is_delete' => 'Is Delete',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }



    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }

    //关联数据
    public function getFile(){
        return $this->hasOne(File::className(),['yid'=>'id']);
    }


    public static function article_list($shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();


            $userswhere=['or',['article.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $userswhere=['or',['article.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','article.id',0];
        }

        $fuid=Yii::$app->request->post('fuid');

        $inid=\Yii::$app->hjd->article_classtree($fuid);

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
            $search_data=['and',['>','article.created_at',$start],['<','article.created_at',$end]];
        }else
        {
            $search_data=['and',['>','article.created_at',1]];
        }

        /*
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
  */
        //查询自己的组
        $query=self::find()->select('article.*,subt.name as submitname,file.file')
            ->andFilterWhere($userswhere)
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','file file'])
            ->andFilterWhere(['fuid'=>$inid])
            ->asArray();
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

                    'created_at' => SORT_DESC,
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

    public static function add_article(){
        $model=new Article();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->created_at=time();
        $model->updated_at=time();
        if($model->save())
        {

            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }

    }

    public static function article_info($id){
        //具体数据
        $data1=self::find()->where(['id'=>$id])->With(['submituser0','audituname','file'])->asArray()->one();
        $data['code']='200';
        $data['data']=$data1;
        return $data;
    }

}
