<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\debug\models\search\Log;
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
 * @property integer $mht_id
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'ctime', 'submituser',  'state', 'contract_id', 'prlin_id', 'server_type', 'qudao', 'avid','cu_ad_id','cu_ad_product','mt_payment_type','dk_date','dk_zhouqi','mht_id'], 'integer'],
            [[ 'ctime', 'submituser', 'a_users'], 'required'],
            [['note','promote_url','appname','a_password','appid','ip','promote_url','pingbidiyu','tel','advertiser_name','url','xingzhi','tags'], 'string']
            //[['appname', 'a_users', 'a_password', 'appid'], 'string', 'max' => 100],
           // [['ip', 'promote_url'], 'string', 'max' => 200],
           // [['pingbidiyu'], 'string', 'max' => 250],
           // [['tel'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'appname' => 'Appname',
            'ip' => 'Ip',
            'promote_url' => 'Promote Url',
            'note' => 'Note',
            'ctime' => 'Ctime',
            'submituser' => 'Submituser',
            'audit_1' => 'Audit 1',
            'audit_2' => 'Audit 2',
            'state' => 'State',
            'a_users' => 'A Users',
            'a_password' => 'A Password',
            'fandian' => 'Fandian',
            'pingbidiyu' => 'Pingbidiyu',
            'tel' => 'Tel',
            'contract_id' => 'Contract ID',
            'endtime' => 'Endtime',
            'appid' => 'Appid',
            'prlin_id' => 'Prlin ID',
            'server_type' => 'Server Type',
            'qudao' => 'Qudao',
            'avid' => 'Avid',
        ];
    }
    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }
    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'avid'])->select('id,advertiser,business,customer_type,submituser');
    }

    //关联数据
    public function getRencontract(){
        return $this->hasOne(ContractRelevance::className(),['contract_id'=>'id']);
    }
    //关联数据
    public function getAccountusers(){
        return $this->hasOne(AccountUsers::className(),['account_id'=>'id']);
    }
    //关联数据
    public function getPrlin(){
        return $this->hasOne(ProductLine::className(),['id'=>'prlin_id']);
    }

    //渠道对应
    public function getQudao0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'qudao'])->select('id,advertiser');
    }

    public function getMht(){
        return $this->hasOne(JdContract::className(),['id'=>'mht_id'])->select('id,title,rebates_proportion,dl_fandian');
    }

    public function getYuming(){
        return $this->hasMany(Yuming::className(),['account_id'=>'id'])->select('account_id,true_url,status,is_ultimate_shenhe');
    }

    public static function account_contract_list($htid){
        return self::find()->select(['id','a_users','a_password','appname','cu_ad_id'])->where(['contract_id'=>$htid])->asArray()->all();
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

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_account.appname' ,$Search_str],['like','subt.name',$Search_str],['like','jd_account.a_users' ,$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>','jd_account.ctime',$start],['<','jd_account.ctime',$end]];
        }else
        {
            $search_data=['and',['>','jd_account.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        //产品线条件
        $pr_id=Yii::$app->request->post('pr_id');

        //公司名称单独搜索
        $search_adname=Yii::$app->request->post('search_adname');

        //如果有二级域名搜索
        $search_yuming=Yii::$app->request->post('serach_yuming');
        if($search_yuming)
        {
            $yuming_account_id_list=Yuming::find()->select('account_id')->where(['like','true_url',$search_yuming])->asArray()->column();


            if(count($yuming_account_id_list)<1)
            {
                $yuming_account_id_list=0;
            }

        }




        //查询自己的组
        $query=self::find()
            ->select('jd_account.*,subt.name as submitname,u.name as marketname,cuad.advertiser as guanggaozhu,cuadrele.product as product,prlin.name as prlin')
            ->joinWith(['advertiser0 ad','submituser0 subt'])
            ->leftJoin('jd_contract con','con.id=jd_account.contract_id')
            ->leftJoin('user_backend u','u.id=con.market')
            ->leftJoin('customer_advertiser cuad','jd_account.cu_ad_id=cuad.id')
            ->leftJoin('customer_advertiser_releveance cuadrele','jd_account.cu_ad_product=cuadrele.id')
            ->leftJoin('jd_contract_relevance conre','conre.contract_id=con.id')
            ->leftJoin('jd_product_line prlin','prlin.id=conre.product_line')
            ->andFilterWhere(['jd_account.contract_id'=>$id])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['like','ad.advertiser',$search_adname])
            ->andFilterWhere(['jd_account.id'=>$yuming_account_id_list])

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


    //账户列表  如果传入公司id则只显示这个合同下的账户
    public static function account_list_m($id){
        if($id=='All')
        {
            $id=null;
        }
        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_account.appname' ,$Search_str],['like','subt.name',$Search_str],['like','jd_account.a_users' ,$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>','jd_account.ctime',$start],['<','jd_account.ctime',$end]];
        }else
        {
            $search_data=['and',['>','jd_account.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        //产品线条件
        $pr_id=Yii::$app->request->post('pr_id');

        //公司名称单独搜索
        $search_adname=Yii::$app->request->post('search_adname');



        //查询自己的组
        $query=self::find()
            ->select('jd_account.*,subt.name as submitname,u.name as marketname,cuad.advertiser as guanggaozhu,cuadrele.product as product')
            ->joinWith(['advertiser0 ad','submituser0 subt'])
            ->leftJoin('jd_contract con','con.id=jd_account.contract_id')
            ->leftJoin('user_backend u','u.id=con.market')
            ->leftJoin('customer_advertiser cuad','jd_account.cu_ad_id=cuad.id')
            ->leftJoin('customer_advertiser_releveance cuadrele','jd_account.cu_ad_product=cuadrele.id')
            ->andFilterWhere(['jd_account.mht_id'=>$id])
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
            ->select('jd_account.*,u.name as marketname,cuad.advertiser as guanggaozhu,cuadrele.product as product')
            ->with(['advertiser0','submituser0','accountusers','prlin','qudao0','mht'=>function($query){
                $query->select("id,title,product_line,rebates_proportion,dl_fandian,advertiser,iszuofei,state")
                    ->with(['prlin'=>function($query){
                        $query->select("id,name,fk_type");
                    }])->with(['advertiser0'=>function($query){
                        $query->select("id,advertiser as madname");
                    }]);
            }])
            ->leftJoin('jd_contract con','con.id=jd_account.contract_id')
            ->leftJoin('user_backend u','u.id=con.market')
            ->leftJoin('customer_advertiser cuad','jd_account.cu_ad_id=cuad.id')
            ->leftJoin('customer_advertiser_releveance cuadrele','jd_account.cu_ad_product=cuadrele.id')
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
            if(\Yii::$app->params['yushan_synchronizing']=='true') {
                //如果是临时账户改为正式账户 改了用户名的
                if ($postdata['state'] === '0') {

                    $url = Yii::$app->params['yushancom']."sem/createhu";


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
                        "sem"=>'3355'
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
                    $url = Yii::$app->params['yushancom']."sem/setzhxxcrm";
                    //$url = "http://www.yushanapp.com/sem/setzhxxcrm";
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


    //获取账户的总消耗和总续费
    public static function account_money_list($start,$end){
        $startstime=strtotime($start);
        $endstime=strtotime($end ."+1 day");


        //公司筛选或者账户条件
        $searchstr=Yii::$app->request->post('searchstr');
        if($searchstr!='')
        {
            $serachwhere=" and (a.a_users like '%$searchstr%' or b.a_user like '%$searchstr%'  or gl.alias like '%$searchstr%' or a.advertiser like '%$searchstr%' or b.advertiser like '%$searchstr%')";
        }

        $sql='
        select a.a_users,a.pay,a.avid,b.pay as tui,(COALESCE(a.pay,0)-COALESCE(b.pay,0)) as xufei,c.advertiser,a.ctime,a.endtime from (
select b.a_users,b.avid,sum(show_money) as pay,min(b.ctime) as ctime, max(b.endtime) as endtime from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.is_ultimate_shenhe=1 and a.payment_type in(1,2) and payment_time>='.$startstime.' and payment_time<'.$endstime.'  group by b.a_users,b.avid
) a left join 
(
select b.a_users,b.avid,sum(show_money) as pay from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.is_ultimate_shenhe=1 and a.payment_type in(15) and payment_time>='.$startstime.' and payment_time<'.$endstime.'  group by b.a_users,b.avid
) b 
on a.a_users=b.a_users left join jd_customer c on c.id=a.avid
        ';


        $sql2="select a.a_user,sum(a.cost) as cost,c.advertiser from jd_account_cost_real a left join jd_contract b on b.id=a.contract_id left join jd_customer c on c.id=b.advertiser where date>='$start' and date<='$end' and a.istuikuan=0 group by a_user,b.advertiser,c.advertiser";

        //没有有关联账户的
       // $sql3="select a.a_users,a.xufei,b.cost,c.advertiser,c.id,gl.alias from ($sql) a left join  ($sql2) b on  a.a_users=b.a_user left join  jd_customer c on a.avid=c.id left join jd_account gl on a.a_users=gl.a_users where gl.alias isnull $serachwhere";

        $sql3="select 
         case when a.a_users is not null then a.a_users else b.a_user end as a_users ,
         case when a.advertiser is not null then a.advertiser else b.advertiser end as advertiser ,
         min(a.ctime) as ctime, max(a.endtime) as endtime,
         a.xufei,b.cost,gl.alias from ($sql) a full  join  ($sql2) b on  a.a_users=b.a_user left join   jd_account gl on a.a_users=gl.a_users  where  gl.alias isnull  $serachwhere group by a.a_users,b.a_user,a.xufei,b.cost,gl.alias,a.advertiser,b.advertiser";


        //有关联账户的
        $sql4="select 
         gl.alias as a_users ,
         case when a.advertiser is not null then a.advertiser else b.advertiser end as advertiser ,
         min(gl.ctime) as ctime, max(gl.endtime) as endtime,
         sum(a.xufei) as xufei,sum(b.cost) as cost,gl.alias from ($sql) a full  join  ($sql2) b on  a.a_users=b.a_user left join   jd_account gl on a.a_users=gl.a_users  where  gl.alias notnull  $serachwhere group by gl.alias,a.advertiser,b.advertiser";

        /*
        echo $sql4;
        $list=\Yii::$app->db->createCommand($sql4)->queryAll();
        var_dump($list);exit;
*/

        $list=\Yii::$app->db->createCommand($sql3)->queryAll();

        $list2=\Yii::$app->db->createCommand($sql4)->queryAll();


        $accountcostlist=ArrayHelper::merge($list,$list2);

        $pre_page=Yii::$app->request->post('pre_page');
        $dataProvider=new ArrayDataProvider([
            'allModels'=>$accountcostlist,
            'pagination'=>[
                'pageSize'=>$pre_page,
            ],
            'sort'=>[
                'attributes'=>['cost']
            ]
        ]);



        $ar['page-count']=$dataProvider->count;
        $ar['totalCount']=$dataProvider->totalCount;
        $ar['data']=$dataProvider->getModels();
        return $ar;

    }


//获取账户的总消耗和总续费
    public static function account_money_info($start,$end){
        $startstime=strtotime($start);
        $endstime=strtotime($end ."+1 day");


        //公司筛选或者账户条件
        $account=Yii::$app->request->post('account');
        $advertiser=Yii::$app->request->post('advertiser');
        if($account!='')
        {
            $serachwhere=" and (a.a_users = '$account' or b.a_user = '$account' or gl.alias = '$account') and  (a.advertiser = '$advertiser' or b.advertiser = '$advertiser')";
        }
        $sql='
        select a.a_users,a.pay,a.avid,b.pay as tui,(COALESCE(a.pay,0)-COALESCE(b.pay,0)) as xufei,c.advertiser from (
select b.a_users,b.avid,sum(show_money) as pay,min(b.ctime) as ctime, max(b.endtime) as endtime from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.is_ultimate_shenhe=1 and a.payment_type in(1,2) and payment_time>='.$startstime.' and payment_time<'.$endstime.'  group by b.a_users,b.avid
) a left join 
(
select b.a_users,b.avid,sum(show_money) as pay from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.is_ultimate_shenhe=1 and a.payment_type in(15) and payment_time>='.$startstime.' and payment_time<'.$endstime.'  group by b.a_users,b.avid
) b 
on a.a_users=b.a_users left join jd_customer c on c.id=a.avid
        ';


        $sql2="select a.a_user,sum(a.cost) as cost,c.advertiser from jd_account_cost_real a left join jd_contract b on b.id=a.contract_id left join jd_customer c on c.id=b.advertiser where date>='$start' and date<='$end' and a.istuikuan=0 group by a_user,b.advertiser,c.advertiser";

        //没有有关联账户的
        // $sql3="select a.a_users,a.xufei,b.cost,c.advertiser,c.id,gl.alias from ($sql) a left join  ($sql2) b on  a.a_users=b.a_user left join  jd_customer c on a.avid=c.id left join jd_account gl on a.a_users=gl.a_users where gl.alias isnull $serachwhere";

        $sql3="select 
         case when a.a_users is not null then a.a_users else b.a_user end as a_users ,
         case when a.advertiser is not null then a.advertiser else b.advertiser end as advertiser ,
         min(gl.ctime) as ctime, max(gl.endtime) as endtime,
         a.xufei,b.cost,gl.alias,a.avid from ($sql) a full  join  ($sql2) b on  a.a_users=b.a_user left join   jd_account gl on a.a_users=gl.a_users  where 1!=2 $serachwhere group by a.a_users,b.a_user,a.xufei,b.cost,gl.alias,a.advertiser,b.advertiser,a.avid";



        $list=\Yii::$app->db->createCommand($sql3)->queryAll();



        return $list;

    }


    public static function account_money_info_day($start,$end){
        $startstime=strtotime($start);
        $endstime=strtotime($end ."+1 day");


        //公司筛选或者账户条件
        $account=Yii::$app->request->post('account');
        $advertiser=Yii::$app->request->post('advertiser');
        if($account!='')
        {
            $serachwhere=" and (a.a_users = '$account' or b.a_user = '$account' or gl.alias = '$account') and  (a.advertiser = '$advertiser' or b.advertiser = '$advertiser')";
        }
        $sql='
        select a.pay,a.avid,b.pay as tui,(COALESCE(a.pay,0)-COALESCE(b.pay,0)) as xufei,c.advertiser,a.date from (
select b.avid,sum(show_money) as pay,to_char(to_timestamp(payment_time),\'YYYY-MM-DD\') as date from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.is_ultimate_shenhe=1 and a.payment_type in(1,2) and payment_time>='.$startstime.' and payment_time<'.$endstime.' and b.avid='.$advertiser.' and b.a_users=\''.$account.'\'  group by date,b.avid
) a left join 
(
select b.avid,sum(show_money) as pay,to_char(to_timestamp(payment_time),\'YYYY-MM-DD\') as date from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.is_ultimate_shenhe=1 and a.payment_type in(15) and payment_time>='.$startstime.' and payment_time<'.$endstime.' and b.avid='.$advertiser.' and b.a_users=\''.$account.'\' group by date,b.avid
) b 
on a.date=b.date left join jd_customer c on c.id=a.avid
        ';


        $sql2="select a.a_user,sum(a.cost) as cost,c.advertiser,a.date from jd_account_cost_real a left join jd_contract b on b.id=a.contract_id left join jd_customer c on c.id=b.advertiser where date>='$start' and date<='$end' and c.id=$advertiser and a.a_user='$account' and a.istuikuan=0 group by a_user,b.advertiser,c.advertiser,a.date";

        //没有有关联账户的
        // $sql3="select a.a_users,a.xufei,b.cost,c.advertiser,c.id,gl.alias from ($sql) a left join  ($sql2) b on  a.a_users=b.a_user left join  jd_customer c on a.avid=c.id left join jd_account gl on a.a_users=gl.a_users where gl.alias isnull $serachwhere";

        $list1=\Yii::$app->db->createCommand($sql)->queryAll();
        $list2=\Yii::$app->db->createCommand($sql2)->queryAll();

        $data=[];
        foreach ($list1 as $k=>$v)
        {

            $data[$v['date']]['cost']=0;
            $data[$v['date']]['xufei']=$v['xufei'];
            $data[$v['date']]['date']=substr($v['date'],5,10);
        }
        foreach ($list2 as $k=>$v)
        {
            if(empty($data[$v['date']]['xufei'])){
                $data[$v['date']]['xufei']=0;
            }
            $data[$v['date']]['cost']=$v['cost'];
            $data[$v['date']]['date']=substr($v['date'],5,10);
        }

        $cdata=[];
        $i=0;
        foreach ($data as $key=>$val)
        {

            $cdata[$i]=$val;
            $i++;
        }

        ArrayHelper::multisort($cdata, ['cost'], [SORT_DESC]);
        $maxcost=$cdata[0]['cost'];
        ArrayHelper::multisort($cdata, ['cost'], [SORT_ASC]);
        $mincost=$cdata[0]['cost'];

        ArrayHelper::multisort($cdata, ['date'], [SORT_ASC]);


        return array('data'=>$cdata,'max'=>$maxcost,'min'=>$mincost);

    }


    //导入消耗
    public static function importxiaohao($file='')
    {
        $fileName=dirname(__Dir__.'../').'/web/'.$file;

        //$fileName='Uploads/3.xls';
        if($fileName=='')
        {
            return false;
        }

        $data = \moonland\phpexcel\Excel::import($fileName, [
            'setFirstRecordAsKeys' => false, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel.
            'setIndexSheetByName' => false, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric.
            //  'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
        ]);




        $level=@\Yii::$app->hjd->getmaxdim($data);


        if($level==2)
        {
            foreach ($data as $key=>$val)
            {
                if($key!=1 and $val['B']!=''){

                    if(self::dataxiaohao($val['A'],$val['B'],$val['C'])==false){
                        //$err[]=$val['A'].'-'.$val['B'];
                        $err[]=$val['B'];
                    }
                }

            }
        }
        if($level==3)
        {
            foreach ($data as $key=>$val)
            {

                foreach ($val as $k=>$v){
                    if($k!=1 and $v['B']!=''){

                        if(self::dataxiaohao($v['A'],$v['B'],$v['C'])==false){
                            //$err[]=$v['A'].'-'.$v['B'];
                            $err[]=$v['B'];
                        }
                    }
                }

            }
        }


        $dataru['code']="200";
        $dataru['msg']="上传成功";
        $dataru['error_account_list']=$err;
        //删除临时文件
        @unlink($file);



        return $dataru;


    }

    //消耗入库操作
    public static function dataxiaohao($data,$account,$xiaohao){
        $logfile= dirname(__Dir__.'../').'/runtime/logs/hjd.log';
        error_log(date("Y-m-d H:i:s").' 执行方法开始'.PHP_EOL,3,$logfile);
        //日期修改
        $account = trim($account);
        if($xiaohao=="" || $xiaohao == "-"){
            $xiaohao = 0;
        }

        if(strpos($data,'/'))
        {
        }else
        {
            $data=explode('-',$data);
            $data='20'.$data[2].'-'.$data[0].'-'.$data[1];
        }

        $datas=strtotime($data);

      //  error_log($datas.'--'.$account.'--'.$xiaohao.PHP_EOL,3,$logfile);

        // echo $datas;exit;
        //查询此账户此日期所属的合同
        $accountone=self::find()->select('b.advertiser')->join('a left join','jd_contract b','a.contract_id=b.id')->andWhere("a_users = '$account' and a.ctime<$datas and a.endtime>$datas")->asArray()->one();

        if($accountone){
            //先查询  如果有就更改如果没有就添加
            $select=AccountCost::find()->where(['date'=>date("Y-m-d",$datas),'account_name'=>$account])->one();
            if($select){

                $select->account_name=$account;
                if($xiaohao=='' or $xiaohao=='-'){
                    $select->cost=0;
                    //   $select->left_cost=0;
                }else
                {
                    $select->cost=$xiaohao;
                    // $select->left_cost=$xiaohao;
                }
                $select->av_id=$accountone['advertiser'];
                $select->date=date("Y-m-d",$datas);
                if($select->save())
                {
                    return true;
                }else
                {

                    return false;
                }
            }else{

                $newaccountcost =new AccountCost();
                $newaccountcost->account_name=$account;

                $newaccountcost->date=date("Y-m-d",$datas);
                if($xiaohao=='' or $xiaohao=='-'){
                    $newaccountcost->cost=0;
                    $newaccountcost->left_cost=0;
                }else
                {
                    $newaccountcost->cost=$xiaohao;
                    $newaccountcost->left_cost=$xiaohao;
                }
                $newaccountcost->av_id=$accountone['advertiser'];

                if($newaccountcost->save())
                {
                    //error_log('添加成功',3,$logfile);
                    return true;
                }else
                {
                    //error_log('添加失败',3,$logfile);
                    if($xiaohao=='')
                        //var_dump($newaccountcost->errors);exit;
                        return false;
                }
            }

        }else
        {
            if($xiaohao==0){
                return true;
            }else
            {
                return false;
            }

        }
        error_log(date("Y-m-d H:i:s").' 执行方法结束'.PHP_EOL,3,$logfile);

    }


    public static function copyaccount($accountid,$mhtid){
        //查询复制账户信息
        $account_info=Account::findOne($accountid);

        //查看账户有没有媒介合同 如果没有就修改 有就复制 新建

        if(!$account_info->mht_id)
        {
            $account_info->mht_id=$mhtid;
            $account_info->save();

            return $account_info;
            exit;
        }
        //复制这些账户
        $new_account=new Account();
        //$new_account=clone $account_info;

        foreach ($account_info as $key=>$val)
        {
            if($key!='id'){
                $new_account->$key=$val;
            }
        }
        //$new_account->id=null;
        $new_account->mht_id=$mhtid;
        $new_account->ctime=time();
        $new_account->endtime=4092599349;
        if($new_account->save())
        {

            //修改账户结束时间
            Account::updateAll(['endtime'=>time()],"a_users='".$account_info['a_users']."' and endtime=4092599349 and prlin_id=".$account_info['prlin_id']." and id !=".$new_account->id);
            //修改账户所属域名

            Yuming::updateAll(['account_id'=>$new_account->id],['account_id'=>$accountid]);

            return $new_account;
        }else
        {
            return $new_account->errors;
        }

        //$account_info->endtime=time();
        //$account_info->save();


    }

    public static function account_history($id){
        $one=Account::findOne($id);
        $list=Account::find()
            ->select('a.*,b.fandian,b.xj_fandian,mht.title')
            ->join('a left join','jd_contract_relevance b','a.contract_id=b.contract_id')
            ->leftJoin('jd_contract mht','mht.id=a.mht_id')
            ->where(['a_users'=>$one->a_users,'avid'=>$one->avid])
            ->orderBy('endtime desc')
            ->asArray()
            ->all();

        foreach ($list as $key=>$val)
        {
            //每个账户的消耗是续费减退款

            $xf=RenewHuikuan::find()->where(['account'=>$val['id'],'advertiser'=>$one->avid,'is_ultimate_shenhe'=>'1','payment_type'=>[1,2]])->andWhere("ctime >= ".$val['ctime']." and ctime <=".$val['endtime'])->sum('show_money');
            $tk=RenewHuikuan::find()->where(['account'=>$val['id'],'advertiser'=>$one->avid,'is_ultimate_shenhe'=>'1','payment_type'=>[15]])->andWhere("ctime >= ".$val['ctime']." and ctime <=".$val['endtime'])->sum('show_money');

            $xf=$xf?$xf:0;
            $tk=$tk?$tk:0;


            $list[$key]['xf_cost']=round($xf-$tk,2);

        }

        return $list;

    }

    public static function yushansem($account_name){
        $url = Yii::$app->params['yushancom'].'sem/getSemByAccountName';
        $yushan_data = Yii::$app->hjd->hjd_post_curl($url,['account_name'=>$account_name]);
        return $yushan_data->data;
    }





    //查询公司总消耗
    public static function ad_cost_total($id){
       $list=self::ad_cost_list($id);
       $sum=0;
       foreach ($list as $value)
       {
           $sum+=($value['total_cost']+$value['balance_cost']);
       }
       return $sum;

    }

    public static function ad_cost_list($id='',$date='2019-01-01'){
        if($id!='')
        {
            $where=' and xf.advertiser='.$id;
        }

        $sql='select xf.advertiser,xf.prlin_id,xf.money as xf,COALESCE(tk.money,0) as tk,(COALESCE(xf.money,0)-COALESCE(tk.money,0)) as pay,COALESCE(a_cost.cost,0) as cost,a_cost.mindate,a_cost.maxdate,COALESCE(b_cost.balance_cost,0) as balance_cost,COALESCE(xf.money,0)-COALESCE(tk.money,0)-(COALESCE(a_cost.cost,0) + COALESCE(b_cost.balance_cost,0)) as money,a_cost.account_num,COALESCE(a_cost_date.cost,0) as cost_day,ad.advertiser adname,pr.name from (
select a.advertiser,sum(show_money) as money,b.prlin_id from jd_renew_huikuan a left join jd_account b on a.account=b.id  where is_ultimate_shenhe=1 and payment_type in(1,2) GROUP BY a.advertiser,b.prlin_id
) xf left join 
(select a.advertiser,sum(show_money) as money,b.prlin_id from jd_renew_huikuan a left join jd_account b on a.account=b.id  where is_ultimate_shenhe=1 and payment_type in(15) GROUP BY a.advertiser,b.prlin_id) 
tk on xf.advertiser=tk.advertiser and xf.prlin_id=tk.prlin_id
left join (
select a.av_id,sum(a.cost) as cost,b.prlin_id,min(a.date) as mindate,max(a.date) as maxdate,count(DISTINCT account_name) as account_num from jd_account_cost a left join (select distinct a_users,avid,prlin_id from jd_account) b on a.account_name=b.a_users and a.av_id=b.avid GROUP BY av_id,b.prlin_id
) a_cost on xf.advertiser=a_cost.av_id and xf.prlin_id=a_cost.prlin_id
left join (
select a.av_id,sum(a.cost) as cost,b.prlin_id,min(a.date) as mindate,max(a.date) as maxdate,count(DISTINCT account_name) as account_num from jd_account_cost a left join (select distinct a_users,avid,prlin_id from jd_account) b on a.account_name=b.a_users and a.av_id=b.avid and date=\''.$date.'\' GROUP BY av_id,b.prlin_id
) a_cost_date on xf.advertiser=a_cost_date.av_id and xf.prlin_id=a_cost_date.prlin_id
left join (
select a.avid,sum(a.balance_cost) as balance_cost,a.prlin_id from jd_account a GROUP BY avid,a.prlin_id
) b_cost on xf.advertiser=b_cost.avid and xf.prlin_id=b_cost.prlin_id 
left join jd_customer ad on xf.advertiser=ad.id
left join jd_product_line pr on xf.prlin_id=pr.id 
where pr.fk_type!=1 '.$where;

        $list=Yii::$app->db->createCommand($sql)->queryAll();
        return $list;

    }

    public static function prlin_account_max_up($id,$pr_lin,$value){
        $sql='select * from (
select xf.advertiser,xf.prlin_id,xf.money as xf,COALESCE(tk.money,0) as tk,(COALESCE(xf.money,0)-COALESCE(tk.money,0)) as pay,xf.account from (
select a.advertiser,sum(show_money) as money,b.prlin_id,a.account from jd_renew_huikuan a left join jd_account b on a.account=b.id  where is_ultimate_shenhe=1 and payment_type in(1,2) GROUP BY a.advertiser,b.prlin_id,a.account
) xf left join 
(select a.advertiser,sum(show_money) as money,b.prlin_id,a.account from jd_renew_huikuan a left join jd_account b on a.account=b.id  where is_ultimate_shenhe=1 and payment_type in(15) GROUP BY a.advertiser,b.prlin_id,a.account) 
tk on xf.advertiser=tk.advertiser and xf.prlin_id=tk.prlin_id and xf.account =tk.account
left join jd_product_line pr on xf.prlin_id=pr.id 
where xf.advertiser='.$id.' and xf.prlin_id='.$pr_lin.' and pr.fk_type!=1) allaccount order by pay desc limit 1';

        $list=Yii::$app->db->createCommand($sql)->queryOne();
        return Account::updateAllCounters(['balance_cost'=>$value],['id'=>$list['account']]);

    }

}