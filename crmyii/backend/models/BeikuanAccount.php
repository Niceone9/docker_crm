<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "jd_account".
 *
 * @property integer $id
 * @property integer $type
 * @property string $appname
 * @property string $ip
 * @property string $promote_url
 * @property string $note
 * @property integer $ctime
 * @property integer $submituser
 * @property integer $audit_1
 * @property integer $audit_2
 * @property integer $state
 * @property string $a_users
 * @property string $a_password
 * @property string $fandian
 * @property string $pingbidiyu
 * @property string $tel
 * @property integer $contract_id
 * @property string $endtime
 * @property string $appid
 * @property integer $prlin_id
 * @property integer $server_type
 * @property integer $qudao
 * @property integer $avid
 */
class BeikuanAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'beikuan_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'ctime', 'submituser',  'state', 'contract_id', 'prlin_id', 'server_type', 'qudao', 'avid','yu_e','mt_payment_type','dk_date','dk_zhouqi'], 'integer'],
            [['ctime', 'submituser', 'a_users'], 'required'],
            [['note','promote_url','appname','a_password','appid','promote_url','tel'], 'string']
            //[['appname', 'a_users', 'a_password', 'appid'], 'string', 'max' => 100],
           // [['ip', 'promote_url'], 'string', 'max' => 200],
           // [['pingbidiyu'], 'string', 'max' => 250],
           // [['tel'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */

    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }
    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'avid'])->select('id,advertiser,business,customer_type');
    }


    //关联数据
    public function getPrlin(){
        return $this->hasOne(ProductLine::className(),['id'=>'prlin_id']);
    }

    //渠道对应
    public function getQudao0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'qudao'])->select('id,advertiser');
    }


    public static function account_contract_list($htid){
        return self::find()->select(['id','a_users','a_password','appname','yu_e'])->where(['contract_id'=>$htid])->asArray()->all();
    }

    public static function meijie_account_contract_list($htid){
        $contid=JdContract::find()->select('id')->where(["mht_id"=>$htid])->indexBy('id')->column();
        return self::find()->select(['id','a_users','a_password','appname'])->where(['contract_id'=>$contid])->asArray()->all();
    }



    //账户列表  如果传入公司id则只显示这个合同下的账户
    public static function account_list($id){
        if($id=='All')
        {
            $id=null;
        }
        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','subt.name',$Search_str],['like','beikuan_account.a_users' ,$Search_str],['like','con.title',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
           // $search_data=['and',['>','beikuan_account.ctime',$start],['<','beikuan_account.ctime',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'beikuan_account.ctime', $start], ['<', 'beikuan_account.ctime', $end]];
            }else{
                $search_data = ['and', ['>=', 'beikuan_account.last_time', date("Y-m-d H:i:s",$start)], ['<', 'beikuan_account.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','beikuan_account.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        //产品线条件
        $pr_id=Yii::$app->request->post('pr_id');

        //公司名称单独搜索
        $search_adname=Yii::$app->request->post('search_adname');

        //公司id 单独搜索
        $advertiser_id=Yii::$app->request->post('advertiserid');

        //金额x1+返点/1-代理返点

        //查询自己的组
        $query=self::find()
            ->select('beikuan_account.*,subt.name as submitname,u.name as marketname,con.title,prlin.name,beikuan_account.yu_e * ( 1+cast(con.rebates_proportion as numeric)  / 100 ) / ( 1-cast(con.dl_fandian as numeric) / 100 ) as show_money ')
            ->joinWith(['advertiser0 ad','submituser0 subt'])
            ->leftJoin('jd_contract con','con.id=beikuan_account.contract_id')
            ->leftJoin('user_backend u','u.id=con.market')
            ->leftJoin('jd_product_line prlin','con.product_line=prlin.id')
            ->andFilterWhere(['beikuan_account.contract_id'=>$id,'beikuan_account.avid'=>$advertiser_id])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['like','ad.advertiser',$search_adname])
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

    public static function account_info($id){
        return self::find()
            ->select('jd_account.*,u.name as marketname')
            ->with(['advertiser0','submituser0','accountusers','prlin','qudao0'])
            ->leftJoin('jd_contract con','con.id=jd_account.contract_id')
            ->leftJoin('user_backend u','u.id=con.market')

            ->where(['jd_account.id'=>$id])
            ->asArray()
            ->one();
    }


    //修改账户信息
    public static function updataaccount($id){
        $model=self::findOne($id);

        $model->load(Yii::$app->getRequest()->getBodyParams(),'');

        $postdata=Yii::$app->request->post();
        $prlin=ProductLine::findOne($postdata['prlin_id']);

        //$isjiugaixin= $postdata['isjiugaixin'];

        if($model->save())
        {
            //修改账户的时候 判断这个账户之前是否被添加过 如果有并且不等于当前id数据 则变更之前的账户有效期为当前时间

            self::updateAll(['endtime'=>time()],"a_users='$postdata[a_users]' and endtime=4092599349 and id !=".$model->id);

            //产品线
            $prlin=ProductLine::findOne($postdata['prlin_id']);

            //渠道
            $qudao=JdCustomer::findOne($postdata['qudao']);
            /*
            if(\Yii::$app->params['yushan_synchronizing']=='true') {
                //如果是临时账户改为正式账户 改了用户名的
                if ($postdata['state'] === '0') {

                    $url = "http://sem.yushanapp.com/sem/createhu";


                    $post_data = array(
                        "name" => $postdata['appname'],
                        "account_type" => $prlin['yushan_type'],
                        "account_name" => $postdata['a_users'],
                        "account_password" => $postdata['a_password'],
                        "account_appid" => 'no token',
                        "account_status" => '1',
                        "fd_rate" => $postdata['fandian'] / 100,
                        "view_type" => '120001',
                        "server_type" => $postdata['server_type'],
                        "qudao_id" => $qudao['yushan_id'],
                    );
                    //账户所属公司不等于原账户公司

                    $yushan_data = Yii::$app->hjd->hjd_post_curl($url, $post_data);
                    $yushan_id = $yushan_data->data->appid;
                    if ($yushan_id != '') {
                        //$Refund->where('id='.$id)->setField('appid',$yushan_id);
                        self::updateAll(['appid' => $yushan_id], ['id' => $id]);

                    } else {
                        return array("code" => "500", "msg" => "同步羽扇失败", "data" => $yushan_data);
                    }

                } elseif (empty($postdata['state'])) {

                    $url = "http://sem.yushanapp.com/sem/setzhxxcrm";
                    $post_data = array(
                        "name" => $postdata['appname'],
                        "type" => $prlin['yushan_type'],
                        "account_name" => $postdata['a_users'],
                        "account_password" => $postdata['a_password'],
                        "account_appid" => 'notoken',
                        "account_status" => '1',
                        "fd_rate" => $postdata['fandian'] / 100,
                        "view_type" => '120001',
                        "fwlx" => $postdata['server_type'],
                        "qudao_id" => $qudao['yushan_id'],
                        "appid" => $postdata['appid']
                    );

                    $yushan_data = Yii::$app->hjd->hjd_post_curl($url, $post_data);
                    $yushan_id = $yushan_data->data;

                    if ($yushan_id != 'ok') {
                        return array("code" => "500", "msg" => "同步羽扇失败", "data" => $yushan_data);
                    }
                }
            }
            */
            return array("code"=>"200","msg"=>"修改成功","data"=>self::account_info($model->id));
        }else
        {
            return array("code"=>"500","msg"=>"修改失败");
        }

    }

    //根据合同ID返回未到期的账户列表
    public static function contract_account($id){
        return self::find()->where(['contract_id'=>$id,'endtime'=>'4092599349'])->all();
    }



}