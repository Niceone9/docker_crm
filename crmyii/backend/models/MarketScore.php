<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "jd_market_score".
 *
 * @property integer $id
 * @property string $name
 * @property integer $uid
 * @property integer $year
 * @property integer $month
 * @property integer $uptime
 * @property integer $level
 */
class MarketScore extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_market_score';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['uid', 'year', 'month', 'uptime', 'level'], 'integer'],
            [['name'], 'string', 'max' => 20],
        ];
    }


    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'uid'])->select('id,name');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'uid' => 'Uid',
            'year' => 'Year',
            'month' => 'Month',
            'uptime' => 'Uptime',
            'level' => 'Level',
        ];
    }

    public static function pf_list(){
        //筛选条件

        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','market.name',$Search_str]];
        //日期筛选条件
        $year=Yii::$app->request->post('start_date');



        //查询自己的组
        $query=self::find()->select('jd_market_score.*,market.name as submitname')->andFilterWhere(['year'=>$year])->andFilterWhere($searchwhere)->joinWith(['submituser0 market'])->asArray();
        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;


        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
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
}
