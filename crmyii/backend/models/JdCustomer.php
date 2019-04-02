<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "jd_customer".
 *
 * @property integer $id
 * @property integer $type
 * @property string $advertiser
 * @property string $product_line
 * @property string $industry
 * @property string $website
 * @property integer $isapp
 * @property string $appname
 * @property string $tax_identification
 * @property string $ticket_address
 * @property string $open_account
 * @property string $account
 * @property integer $submituser
 * @property integer $ctime
 * @property string $kp_tel
 * @property string $city
 * @property string $yu_e
 * @property string $huikuan
 * @property string $bukuan
 * @property string $site
 * @property string $post_site
 * @property integer $business
 * @property integer $customer_type
 * @property integer $yushan_id
 * @property string $undistributed_yu_e
 * @property string $dakuan
 * @property string $myu_e
 * @property string $dakuan_undistributed_yu_e
 * @property string $fapiao
 * @property integer $r_time
 * @property integer $is_claim
 * @property integer $audit
 * @property integer $audit_u
 *
 * @property UserBackend $submituser0
 */
class JdCustomer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'advertiser'], 'required'],
            [['type', 'isapp', 'submituser', 'ctime', 'business', 'customer_type', 'yushan_id', 'r_time', 'is_claim', 'audit', 'audit_u'], 'integer'],
            [['yu_e', 'huikuan', 'bukuan', 'undistributed_yu_e', 'dakuan', 'myu_e', 'dakuan_undistributed_yu_e', 'fapiao','tuikuan_undistributed_yu_e'], 'number'],
            [['advertiser', 'appname', 'kp_tel'], 'string', 'max' => 50],
            [['product_line'], 'string', 'max' => 200],
            [['industry'], 'string', 'max' => 20],
            [['website', 'tax_identification', 'ticket_address', 'open_account', 'account', 'city','true_advertiser'], 'string', 'max' => 100],
            [['site', 'post_site'], 'string', 'max' => 255],
            [['submituser'], 'exist', 'skipOnError' => true, 'targetClass' => UserBackend::className(), 'targetAttribute' => ['submituser' => 'id']],
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
            'advertiser' => 'Advertiser',
            'product_line' => 'Product Line',
            'industry' => 'Industry',
            'website' => 'Website',
            'isapp' => 'Isapp',
            'appname' => 'Appname',
            'tax_identification' => 'Tax Identification',
            'ticket_address' => 'Ticket Address',
            'open_account' => 'Open Account',
            'account' => 'Account',
            'submituser' => 'Submituser',
            'ctime' => 'Ctime',
            'kp_tel' => 'Kp Tel',
            'city' => 'City',
            'yu_e' => 'Yu E',
            'huikuan' => 'Huikuan',
            'bukuan' => 'Bukuan',
            'site' => 'Site',
            'post_site' => 'Post Site',
            'business' => 'Business',
            'customer_type' => 'Customer Type',
            'yushan_id' => 'Yushan ID',
            'undistributed_yu_e' => 'Undistributed Yu E',
            'dakuan' => 'Dakuan',
            'myu_e' => 'Myu E',
            'dakuan_undistributed_yu_e' => 'Dakuan Undistributed Yu E',
            'fapiao' => 'Fapiao',
            'r_time' => 'R Time',
            'is_claim' => 'Is Claim',
            'audit' => 'Audit',
            'audit_u' => 'Audit U',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubmituser0()
    {
        return $this->hasOne(UserBackend::className(), ['id' => 'submituser'])->select('id,name');
    }
    public function getBusiness0()
    {
        return $this->hasOne(UserBackend::className(), ['id' => 'business'])->select('id,name');
    }

    //审核人的关联数据
    public function getAuditu(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('id,name');
    }

    //联系人关联数据
    public function getContact(){
        return $this->hasMany(JdContactList::className(),['customer_id'=>'id']);
    }

    //联系人关联数据
    public function getContract0(){
        return $this->hasMany(JdContract::className(),['advertiser'=>'id'])->select("advertiser");
    }
    //联系人关联数据
    public function getContractnew0(){
        return $this->hasMany(ContractNew::className(),['advertiser'=>'id'])->select("advertiser");
    }
    //广告主列表
    public function getGuanggaozhu(){
        return $this->hasMany(CustomerAdvertiser::className(),['customer_id'=>'id'])->select("advertiser,customer_id");
    }

    //联系人关联数据
    public function getContractaccounts(){
        return $this->hasMany(ContractAccounts::className(),['advertiser'=>'id'])->select("advertiser");
    }

    //销售只看自己的列表
    public static function market_list($user=''){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['jd_customer.business'=>$uid],['jd_customer.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['jd_customer.business'=>$uid],['jd_customer.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','jd_customer.id',0];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');
        /*$searchwhere= ['like','advertiser', $Search_str];
        switch ($Search_type) {
            case 'advertiser':
                $searchwhere = ['like','advertiser', $Search_str];
                break;
            case 'appname':
                $searchwhere = ['like','appname' ,$Search_str];
                break;
            case 'contact':
                $searchwhere = ['or',['like','name',$Search_str],['like','tel',$Search_str]];
                break;
            case 'marektorbussiness':
                $user_idin=UserBackend::find()->select('id')->where(['like','name',$Search_str]);
                $searchwhere = ['or',['in','submituser',$user_idin],['in','business',$user_idin]];
                break;
        }*/
        $searchwhere = ['or',['like','jd_customer.advertiser', $Search_str],['like','jd_customer.appname' ,$Search_str],['like','subt.name',$Search_str],['like','bus.name',$Search_str]];

        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>','jd_customer.ctime',$start],['<','jd_customer.ctime',$end]];
        }else
        {
            $search_data=['and',['>','jd_customer.ctime',1]];
        }


        //欠款公司条件
        $ad_qiankuan=Yii::$app->request->post('qiankuan');
        if($ad_qiankuan=='1')
        {
            $qiankuan_where='(jd_customer.huikuan+jd_customer.bukuan-jd_customer.yu_e-jd_customer.margin_money-jd_customer.kehu_bukuan) <0';
        }else{
            $qiankuan_where='jd_customer.id>0';
        }


        //客户质量度搜索
        $kehuzhiliangdu=Yii::$app->request->post('liveness');

        //查询自己的组
        $query=self::find()
            ->select('jd_customer.*,subt.name as market,bus.name as bussname')
            ->with(['contact','contract0','contractnew0','guanggaozhu','contractaccounts'])
            ->joinWith(['submituser0 subt','business0 bus'])
            ->where(['customer_type'=>array(1,2)])
            ->andFilterWhere($userswhere)
            ->andFilterWhere($searchwhere)
            ->andWhere($qiankuan_where)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['liveness'=>$kehuzhiliangdu])
            ->asArray();


        $provider = new ActiveDataProvider([
            'query' => $query,
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

    //修改商务或者销售
    public static function  upmarket($custmoerarray,$users,$type){
        //把字符串改为数组
        $custmoerarray=explode(',',$custmoerarray);



        $tr = Yii::$app->db->beginTransaction();
        try {
            //修改公司所属提交人或者商务
            self::updateAll([$type=>$users],['in','id',$custmoerarray]);
            //如果是修改销售
            if($type=='submituser'){
                //修改合同的状态
                JdContract::updateAll(['market'=>$users,'submituser'=>$users],['in','advertiser',$custmoerarray]);
                //修改合同的状态
                ContractNew::updateAll(['market'=>$users,'submituser'=>$users],['in','advertiser',$custmoerarray]);
                //修改续费回款表的销售字段
                RenewHuikuan::updateAll(['market'=>$users],['in','advertiser',$custmoerarray]);

            }

            $tr->commit();
        } catch (Exception $e) {
            $tr->rollBack();
        }



        return 'save success';

    }

    //
    public function extraFields()
    {
        return ['submituser0','business0','contact'];
    }

    //获取自己或树下孩子 的客户
    public static function mykehu($id){
        $userswhere=['or',['submituser'=>$id],['business'=>$id]];
        return  self::find()->select(['advertiser','id'])->where($userswhere)->all();
    }

    //获取客户简要信息
    public static function kehu_info_sm($id){
        return self::find()->with(['submituser0','business0'])->where(['id'=>$id])->asArray()->one();
    }

    //获取渠道列表
    public static function qudaolist(){
        return self::find()->select(['id','advertiser'])->where(['customer_type'=>2])->asArray()->all();
    }




    //媒介客户列表
    //销售只看自己的列表
    public static function meijie_market_list($user=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['jd_customer.business'=>$uid],['jd_customer.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
             $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['jd_customer.business'=>$uid],['jd_customer.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','jd_customer.id',0];
        }

        //筛选条件
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','jd_customer.advertiser', $Search_str],['like','jd_customer.appname' ,$Search_str],['like','subt.name',$Search_str],['like','bus.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>','jd_customer.ctime',$start],['<','jd_customer.ctime',$end]];
        }else
        {
            $search_data=['and',['>','jd_customer.ctime',1]];
        }
        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');


        //查询自己的组
       // $query=self::find()->select('jd_customer.*,subt.name as market,bus.name as bussname')->filterWhere($userswhere)->andWhere('jd_customer.customer_type=3')->with(['contact','contract0'])->joinWith(['submituser0 subt','business0 bus'])->asArray();
               $query=self::find()
                   ->select('jd_customer.*,subt.name as market,bus.name as bussname')
                   ->filterWhere($userswhere)
                   ->andWhere('jd_customer.customer_type=3')
                   ->andFilterWhere($searchwhere)
                   ->andFilterWhere($search_data)
                   ->with(['contact','contract0','contractnew0'])
                   ->joinWith(['submituser0 subt','business0 bus'])
                   ->asArray();

        //$a=clone $query;
        //echo $a->createCommand()->sql;exit;
        $provider = new ActiveDataProvider([
            'query' => $query,
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

    //获取这个公司的产品线
    public static function customer_prlin($id){
        $sql="select product_line from jd_contract_relevance  where advertiser='$id'";
        $sql2="select * from jd_product_line where id in($sql)";

        $lrlist=parent::findBySql($sql2)->asArray()->all();
        return $lrlist;
    }

    //新增合同排名
    public static function contract_add_rank($id,$strat,$end){
        //如果有时间筛选条件
        if($strat!='default' && $end!='default')
        {
            $start = strtotime($strat);
            $end = strtotime($end);
            $end=strtotime("+1 days",$end);
            $andwhere=" a.ctime >=$start and a.ctime<$end";
        }else
        {
            $andwhere=" a.id!=0";
        }
        if($id!='default')
        {
            $andwhere=" b.id=$id";
        }

        $list=(new Query())->from('jd_contract')
            ->select(['b.name','b.id','b.image','count (a.id) as amount','Row_Number() OVER (ORDER BY count (a.id) desc) rank'])
            ->join('a left join','jd_customer c','a.advertiser=c.id')
            ->leftJoin('user_backend b','b.id=c.submituser')
            ->Where($andwhere)
            ->andWhere([
                'c.customer_type'=>[1,2],
                'a.audit'=>[1,3],
                'a.is_copy'=>'0'
            ])
            ->groupBy(['b.name','b.image','b.id'])
            ->orderBy(['amount'=>SORT_DESC])
            ->all();
        return $list;
    }



    //销售毛利排名
    public static function markey_maoli_rank($id,$strat,$end){
        //如果有时间筛选条件
        if($strat!='default' && $end!='default')
        {
            $andwhere=" and a.payment_time >=$strat and a.payment_time<$end";
        }else
        {
            $andwhere=" a.id!=0";
        }
        if($id!='default')
        {
            $andwhere2=" where quan.submituser=$id";
        }
        $sql="SELECT
	lirun.advertiser,
	(lirun.money - lirun.xf_cost) AS liruns,
	lirun.adname,
	lirun.companyname,
	lirun.submituser,
	coalesce(bukuan.bukuan,0) as bukuan,
	coalesce(tuikuan.tuikuan,0) as tuikuan,
	coalesce(tuikuan.tuikmoney,0) as tuikuan_money,
	coalesce(tuikuan.tuikmoney-tuikuan.tuikuan,0) as tuikuan_lirun
FROM
	(
		SELECT
			A .advertiser,
			SUM (A .money) AS money,
			SUM (A .xf_cost) AS xf_cost,
			cu.advertiser AS adname,
			cu.submituser,
			dlgs.companyname
		FROM
			jd_renew_huikuan A
		LEFT JOIN jd_customer cu ON cu. ID = A .advertiser
		LEFT JOIN jd_contract ht ON ht. ID = A .xf_contractid
		LEFT JOIN jd_agent_company dlgs ON dlgs. ID = ht.agent_company
		WHERE
			A .is_ultimate_shenhe = 1
			and xf_cost >0
		AND A .payment_type IN (1, 2)
	   ".$andwhere."
		GROUP BY
			A .advertiser,
			adname,
			companyname,
			
			cu.submituser
	) lirun left join (select sum(a.money) as tuikmoney,sum(a.xf_cost) as tuikuan,a.advertiser from jd_renew_huikuan a left join jd_contract ht on ht.id=a.xf_contractid where a.payment_type='15' and a.is_ultimate_shenhe=1 ".$andwhere." GROUP BY a.advertiser) tuikuan on lirun.advertiser=tuikuan.advertiser left join (select sum(a.xf_cost) as bukuan,a.advertiser from jd_renew_huikuan a left join jd_contract ht on ht.id=a.xf_contractid where a.payment_type='3' and a.is_ultimate_shenhe=1 ".$andwhere." GROUP BY a.advertiser) bukuan on lirun.advertiser=bukuan.advertiser";

        $sqlone='select quan.submituser,uu.name,uu.image,sum(quan.liruns-quan.bukuan-quan.tuikuan_lirun) as lirun,Row_Number() OVER (ORDER BY sum(quan.liruns-quan.bukuan-quan.tuikuan_lirun) desc) rank from ('.$sql.')  quan left join user_backend uu on uu.id=quan.submituser '.$andwhere2.' group by uu.name,uu.image,quan.submituser order by lirun desc';
        $lrlist=parent::findBySql($sqlone)->asArray()->all();
        return $lrlist;
    }

    //新增客户列表
    public static function contract_add_list($strat,$end){
        //如果有时间筛选条件
        if($strat!='default' && $end!='default')
        {
            $start = strtotime($strat);
            $end = strtotime($end);
            $end=strtotime("+1 days",$end);
            $andwhere=" a.ctime >=$start and a.ctime<$end";
        }else
        {
            $andwhere=" a.id!=0";
        }
        $list=(new Query())->from('jd_contract')
            ->select(['b.name','b.id as uid','c.advertiser'])
            ->join('a left join','jd_customer c','a.advertiser=c.id')
            ->leftJoin('user_backend b','b.id=c.submituser')
            ->Where($andwhere)
            ->andWhere([
                'c.customer_type'=>[1,2],
                'a.audit'=>[1,3],
                'a.is_copy'=>'0'
            ])
            ->all();
        $list2=(new Query())->from('jd_contract')
            ->select(['b.id','count(b.id) as numm'])
            ->join('a left join','jd_customer c','a.advertiser=c.id')
            ->leftJoin('user_backend b','b.id=c.submituser')
            ->Where($andwhere)
            ->andWhere([
                'c.customer_type'=>[1,2],
                'a.audit'=>[1,3],
                'a.is_copy'=>'0'
            ])
            ->groupBy('b.id')
            ->all();
        foreach ($list as $key=>$val)
        {
            foreach ($list2 as $k=>$v)
            {
                if($val['uid']==$v['id'])
                {
                    $list[$key]['numm']=$v[numm];
                }
            }
        }
        return $list;
    }

    //销售新增合同详情列表
    public static function contract_market_add_list($id,$strat,$end)
    {
        //如果有时间筛选条件
        if ($strat != 'default' && $end != 'default') {
            $start = strtotime($strat);
            $end = strtotime($end);
            $end = strtotime("+1 days", $end);
            $andwhere = " a.ctime >=$start and a.ctime<$end";
        } else {
            $andwhere = " a.id!=0";
        }

        $list=(new Query())->from('jd_contract')->select([
            'a.id',
            'a.contract_no',
            'a.appname',
            'b.advertiser',
            'c.name'
        ])->join('a left join','jd_customer b','a.advertiser=b.id')
            ->leftJoin('jd_contract_relevance cc','a.id=cc.contract_id')
            ->leftJoin('jd_product_line c','c.id=cc.product_line')
            ->Where($andwhere)
            ->andWhere(['b.submituser'=>$id])
            ->all();
        //销售名字
        $name=UserBackend::findOne($id);

        return $list;
        exit;
    }


    //销售个人毛利列表
    public static function markey_maoli_list($id,$strat,$end){
        //如果有时间筛选条件
        if($strat!='default' && $end!='default')
        {
            $andwhere=" and a.payment_time >=$strat and a.payment_time<$end";
        }else
        {
            $andwhere=" a.id!=0";
        }
        $sql="SELECT
	lirun.advertiser,
	(lirun.money - lirun.xf_cost) AS liruns,
	lirun.adname,
	lirun.companyname,
	lirun.submituser,
	coalesce(lirun.bukuan,0) as bukuan,
	coalesce(tuikuan.tuikuan,0) as tuikuan
FROM
	(
		SELECT
			A .advertiser,
			SUM (A .money) AS money,
			SUM (A .xf_cost) AS xf_cost,
			cu.advertiser AS adname,
			cu.submituser,
			cu.bukuan,
			dlgs.companyname
		FROM
			jd_renew_huikuan A
		LEFT JOIN jd_customer cu ON cu. ID = A .advertiser
		LEFT JOIN jd_contract ht ON ht. ID = A .xf_contractid
		LEFT JOIN jd_agent_company dlgs ON dlgs. ID = ht.agent_company
		WHERE
			A .is_ultimate_shenhe = 1
			and xf_cost >0
		AND A .payment_type IN (1, 2)
	   ".$andwhere."
		GROUP BY
			A .advertiser,
			adname,
			companyname,
			cu.bukuan,
			cu.submituser
	) lirun left join (select sum(a.xf_cost) as tuikuan,a.advertiser from jd_renew_huikuan a left join jd_contract ht on ht.id=a.xf_contractid where a.payment_type='15' and a.is_ultimate_shenhe=1 ".$andwhere."  GROUP BY a.advertiser) tuikuan on lirun.advertiser=tuikuan.advertiser";

        $sqlone='select quan.submituser,quan.advertiser,quan.adname,uu.name,sum(quan.liruns-quan.bukuan-quan.tuikuan) as lirun from ('.$sql.')  quan left join user_backend uu on uu.id=quan.submituser where quan.submituser='.$id.' group by quan.submituser,quan,advertiser,quan.adname,uu.name order by lirun desc';
        $lrlist=parent::findBySql($sqlone)->asArray()->all();
        return $lrlist;
    }


}
