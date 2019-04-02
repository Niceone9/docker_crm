<?php

namespace backend\models;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * This is the model class for table "jd_account_cost".
 *
 * @property integer $id
 * @property string $account_name
 * @property string $date
 * @property string $cost
 * @property string $left_cost
 * @property integer $av_id
 */
class AccountCost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_account_cost';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_name', 'date'], 'required'],
            [['date'], 'safe'],
           // [['cost', 'left_cost'], 'number'],
            [['av_id'], 'integer'],
            [['account_name'], 'string', 'max' => 255],
            ['cost','default','value'=>0],
            ['left_cost','default','value'=>0],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_name' => 'Account Name',
            'date' => 'Date',
            'cost' => 'Cost',
            'left_cost' => 'Left Cost',
            'av_id' => 'Av ID',
        ];
    }

    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'av_id'])->select('id,advertiser');
    }
    public function getAccount0(){
        return $this->hasMany(Account::className(),['a_users'=>'account_name'])->select('a_users,mht_id,contract_id');
    }



    public static function cost_list(){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();

            $userswhere=['or',['av_id.id'=>$adidlist]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhere=['or',['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','jd_account_cost.id',0];
        }



        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_account_cost.account_name',$Search_str],['like','market.name',$Search_str]];
        //日期筛选条件
        $start=Yii::$app->request->post('start_date');
        $end=Yii::$app->request->post('end_date');

        if($start and $end)
        {
            $search_data=['and',['>=','jd_account_cost.date',$start],['<=','jd_account_cost.date',$end]];
        }else
        {
            $search_data=['and',['<>','jd_account_cost.id','0']];
        }




        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_account_cost.*,market.name,ad.customer_type')
            ->andFilterWhere($userswhere)
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['advertiser0 ad'])
            ->leftJoin('user_backend market','market.id=ad.submituser')
            ->asArray();

        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;
        //总额
        $query_count=$query->sum('jd_account_cost.cost');

        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [

                    'date' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);

        $datalist=$provider->getModels();

        foreach ($datalist as $key=>$val)
        {
            $time=strtotime($val['date']);
            $info=Account::find()
                ->select('b.fandian,b.xj_fandian,pr.name,mht.rebates_proportion as mfandian,mht.dl_fandian,product_name.product')
                ->where(['a.a_users'=>$val['account_name']])
                ->andWhere("a.ctime <$time and a.endtime >$time ")
                ->join('a left join','jd_contract_relevance b','a.contract_id=b.contract_id')
                ->leftJoin('jd_product_line pr','pr.id=b.product_line')
                ->leftJoin('jd_contract mht','mht.id=a.mht_id')
                ->leftJoin('customer_advertiser_releveance product_name','product_name.id=a.cu_ad_product')
                ->orderBy('endtime desc')
                ->asArray()
                ->one();


            $datalist[$key]['fandian']=$info['fandian'];
            $datalist[$key]['xj_fandian']=$info['xj_fandian'];
            $datalist[$key]['prname']=$info['name'];
            $datalist[$key]['mfandian']=$info['mfandian'];
            $datalist[$key]['mdlfandian']=$info['dl_fandian'];
            $datalist[$key]['product']=$info['product'];


        }
        // 获取分页和排序数据
        $ar['data'] = $datalist;
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();

        //总回款
        $ar['money_sum']=$query_count;

        return $ar;
    }


    public static function cost_list2(){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();

            $userswhere=['or',['av_id.id'=>$adidlist]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhere=['or',['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','jd_account_cost_real.id',0];
        }



        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_account_cost_real.a_user', $Search_str]];
        //日期筛选条件
        $start=Yii::$app->request->post('start_date');
        $end=Yii::$app->request->post('end_date');

        if($start and $end)
        {
            $search_data=['and',['>=','jd_account_cost_real.date',$start],['<=','jd_account_cost_real.date',$end]];
        }else
        {
            $search_data=['and',['<>','jd_account_cost_real.id','0']];
        }




        //查询自己的组
        $query=AccountCostReal::find()
            ->select('ad.advertiser as adname,ad.customer_type,jd_account_cost_real.*,mht.rebates_proportion as mfandian')
            ->joinWith(['contractgl gl','contractprlin','market'])
            ->leftJoin('jd_contract mht','mht.id=jd_contract.mht_id')
            ->andFilterWhere($userswhere)
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andWhere(['istuikuan'=>0])
            ->joinWith(['advertiser0 ad'])
            ->asArray();

        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;
        //总额
        $query_count=$query->sum('jd_account_cost_real.cost');

        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [

                    'date' => SORT_DESC,
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

        //总回款
        $ar['money_sum']=$query_count;

        return $ar;
    }

    public static function account_cost_date($start,$end){
//       $list=self::find()
//            ->select('sum(a.cost) as cost,b.advertiser,c.name,a.account_name')
//            ->join('a left join','jd_customer b','a.av_id=b.id')
//            ->leftJoin('user_backend c','c.id=b.submituser')
//            ->where("date >= '$start' and date <='$end'")
//            ->groupBy('a.account_name,b.advertiser,c.name')
//            ->asArray()
//            ->all();


        //客户属性 1 直客 2渠道
        $customer_type=Yii::$app->request->get('customer_type');
        $pr_lin=Yii::$app->request->get('pr_lin');

        if($customer_type!='')
        {
            $customer_type_where="where customer_type={$customer_type}";
        }

        if($pr_lin!='')
        {
            $prlin_where=" where cr.product_line={$pr_lin}";
        }


        $sql="select sum(cost) as cost_bi,sum(money) as cost,advertiser,customer_type,name,account_name from (select t.*,cr.fandian,cr.xj_fandian,round(cost/(100+cr.fandian)*(100-cr.xj_fandian),2) as money,customer.advertiser,users.name,customer.customer_type from (select ac.date,ac.id,ac.cost,ac.av_id,max(a.id) account_id,ac.account_name from jd_account_cost ac
        left join jd_account a on ac.account_name = a.a_users and ac.av_id = a.avid
         and a.ctime < EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd') + interval '1 day')
         and a.endtime > EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd'))
         where ac.cost > 0 and ac.date >= '{$start}' and ac.date <='{$end}'   group by ac.id,account_name,av_id) t 
        left join jd_account ja on t.account_id = ja.id
        left join jd_contract_relevance cr on ja.contract_id = cr.contract_id 
        left join jd_customer customer on customer.id=t.av_id
		left join user_backend users on users.id =customer.submituser
		$prlin_where
        )
         t1 {$customer_type_where} GROUP BY advertiser,customer_type,name,account_name";
        $list=\Yii::$app->db->createCommand($sql)->queryAll();






       $data=[];
       foreach ($list as $key=>$val)
        {
            $data[$val['account_name']]=$val;
        }
       return $data;
    }

    public static function account_ave_cost_date($start,$end){
//        $list=self::find()
//
//            ->select('sum(a.cost) as cost,a.account_name,b.advertiser,c.name,a.date')
//            ->join('a left join','jd_customer b','a.av_id=b.id')
//            ->leftJoin('user_backend c','c.id=b.submituser')
//            ->where("date >= '$start' and date <='$end'")
//            ->groupBy('a.account_name,a.date,b.advertiser,c.name')
//            ->asArray()
//            ->all();



        //客户属性 1 直客 2渠道
        $customer_type=Yii::$app->request->get('customer_type');
        $pr_lin=Yii::$app->request->get('pr_lin');

        if($customer_type!='')
        {
            $customer_type_where="where customer_type={$customer_type}";
        }

        if($pr_lin!='')
        {
            $prlin_where=" where cr.product_line={$pr_lin}";
        }


        $sql="select sum(cost) as cost_bi,sum(money) as cost,advertiser,customer_type,name,account_name,date from (select t.*,cr.fandian,cr.xj_fandian,round(cost/(100+cr.fandian)*(100-cr.xj_fandian),2) as money,customer.advertiser,users.name,customer.customer_type from (select ac.date,ac.id,ac.cost,ac.av_id,max(a.id) account_id,ac.account_name from jd_account_cost ac
        left join jd_account a on ac.account_name = a.a_users and ac.av_id = a.avid
         and a.ctime < EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd') + interval '1 day')
         and a.endtime > EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd'))
         where ac.cost > 0 and ac.date >= '{$start}' and ac.date <='{$end}'   group by ac.id,account_name,av_id) t 
        left join jd_account ja on t.account_id = ja.id
        left join jd_contract_relevance cr on ja.contract_id = cr.contract_id 
        left join jd_customer customer on customer.id=t.av_id
		left join user_backend users on users.id =customer.submituser
		$prlin_where
        )
         t1 {$customer_type_where} GROUP BY advertiser,customer_type,name,account_name,date";
        $list=\Yii::$app->db->createCommand($sql)->queryAll();


        $data=[];
        $lenght=[];
        foreach ($list as $key=>$val)
        {
            $data[$val['account_name']]['cost']+=$val['cost'];
            $data[$val['account_name']]['advertiser']=$val['advertiser'];
            $data[$val['account_name']]['name']=$val['name'];

            $lenght[$val['account_name']]+=1;
        }

        $avg=[];
        foreach ($data as $key =>$val)
        {
            $avg[$key]['cost']=$data[$key]['cost']/$lenght[$key];
            $avg[$key]['advertiser']=$val['advertiser'];
            $avg[$key]['name']=$val['name'];
        }


        return $avg;
    }

    public static function account_cost_date_all($start,$end){
//        $list=self::find()
//            ->where("date >= '$start' and date <='$end'")
//            ->sum('cost');
//        return $list;

        //客户属性 1 直客 2渠道
        $customer_type=Yii::$app->request->get('customer_type');
        $pr_lin=Yii::$app->request->get('pr_lin');
//        $list=self::find()
//            ->select('sum(a.cost) as cost,b.advertiser,b.customer_type,c.name')
//            ->join('a left join','jd_customer b','a.av_id=b.id')
//            ->leftJoin('user_backend c','c.id=b.submituser')
//            ->where("a.date >= '$start' and a.date <='$end'")
//            ->andFilterWhere(['b.customer_type'=>$customer_type])
//            ->groupBy('b.advertiser,b.customer_type,c.name')
//            ->asArray()
//            ->all();


        if($customer_type!='')
        {
            $customer_type_where="where customer_type={$customer_type}";
        }

        if($pr_lin!='')
        {
            $prlin_where=" where cr.product_line={$pr_lin}";
        }


        $sql="select sum(cost) as cost_bi,sum(money) as cost from (select t.*,cr.fandian,cr.xj_fandian,round(cost/(100+cr.fandian)*(100-cr.xj_fandian),2) as money,customer.advertiser,users.name,customer.customer_type from (select ac.date,ac.id,ac.cost,ac.av_id,max(a.id) account_id from jd_account_cost ac
        left join jd_account a on ac.account_name = a.a_users and ac.av_id = a.avid
         and a.ctime < EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd') + interval '1 day')
         and a.endtime > EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd'))
         where ac.cost > 0 and ac.date >= '{$start}' and ac.date <='{$end}'   group by ac.id,account_name,av_id) t 
        left join jd_account ja on t.account_id = ja.id
        left join jd_contract_relevance cr on ja.contract_id = cr.contract_id 
        left join jd_customer customer on customer.id=t.av_id
		left join user_backend users on users.id =customer.submituser
		$prlin_where
        )
         t1 {$customer_type_where} ";
        $list=\Yii::$app->db->createCommand($sql)->queryAll();




        return $list[0]['cost'];



    }

    public static function account_ave_cost_date_all($start,$end){
        exit;
//        $list=self::find()
//            ->select('sum(cost) as cost,date')
//            ->where("date >= '$start' and date <='$end'")
//            ->groupBy('date')
//            ->asArray()
//            ->all();


        //客户属性 1 直客 2渠道
        $customer_type=Yii::$app->request->get('customer_type');
        $pr_lin=Yii::$app->request->get('pr_lin');

        if($customer_type!='')
        {
            $customer_type_where="where customer_type={$customer_type}";
        }

        if($pr_lin!='')
        {
            $prlin_where=" where cr.product_line={$pr_lin}";
        }


        $sql="select sum(cost) as cost_bi,sum(money) as cost from (select t.*,cr.fandian,cr.xj_fandian,round(cost/(100+cr.fandian)*(100-cr.xj_fandian),2) as money,customer.advertiser,users.name,customer.customer_type from (select ac.date,ac.id,ac.cost,ac.av_id,max(a.id) account_id,ac.account_name from jd_account_cost ac
        left join jd_account a on ac.account_name = a.a_users and ac.av_id = a.avid
         and a.ctime < EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd') + interval '1 day')
         and a.endtime > EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd'))
         where ac.cost > 0 and ac.date >= '{$start}' and ac.date <='{$end}'   group by ac.id,account_name,av_id) t 
        left join jd_account ja on t.account_id = ja.id
        left join jd_contract_relevance cr on ja.contract_id = cr.contract_id 
        left join jd_customer customer on customer.id=t.av_id
		left join user_backend users on users.id =customer.submituser
		$prlin_where
        )
         t1 {$customer_type_where} ";
        $list=\Yii::$app->db->createCommand($sql)->queryAll();



        $data=0;
        $lenght=0;

        foreach ($list as $key=>$val)
        {

            $data+=$val['cost'];
            $lenght+=1;
        }
        $avg=$data/$lenght;
        return $avg;
    }


    public static function account_cost_date_ad($start,$end){
        //客户属性 1 直客 2渠道
        $customer_type=Yii::$app->request->get('customer_type');
        $pr_lin=Yii::$app->request->get('pr_lin');
//        $list=self::find()
//            ->select('sum(a.cost) as cost,b.advertiser,b.customer_type,c.name')
//            ->join('a left join','jd_customer b','a.av_id=b.id')
//            ->leftJoin('user_backend c','c.id=b.submituser')
//            ->where("a.date >= '$start' and a.date <='$end'")
//            ->andFilterWhere(['b.customer_type'=>$customer_type])
//            ->groupBy('b.advertiser,b.customer_type,c.name')
//            ->asArray()
//            ->all();


        if($customer_type!='')
        {
            $customer_type_where="where customer_type={$customer_type}";
        }

        if($pr_lin!='')
        {
            $prlin_where=" where cr.product_line={$pr_lin}";
        }


        $sql="select sum(cost) as cost_bi,sum(money) as cost,advertiser,customer_type,name from (select t.*,cr.fandian,cr.xj_fandian,round(cost/(100+cr.fandian)*(100-cr.xj_fandian),2) as money,customer.advertiser,users.name,customer.customer_type from (select ac.date,ac.id,ac.cost,ac.av_id,max(a.id) account_id from jd_account_cost ac
        left join jd_account a on ac.account_name = a.a_users and ac.av_id = a.avid
         and a.ctime < EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd') + interval '1 day')
         and a.endtime > EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd'))
         where ac.cost > 0 and ac.date >= '{$start}' and ac.date <='{$end}'   group by ac.id,account_name,av_id) t 
        left join jd_account ja on t.account_id = ja.id
        left join jd_contract_relevance cr on ja.contract_id = cr.contract_id 
        left join jd_customer customer on customer.id=t.av_id
		left join user_backend users on users.id =customer.submituser
		$prlin_where
        )
         t1 {$customer_type_where} GROUP BY advertiser,customer_type,name";
        $list=\Yii::$app->db->createCommand($sql)->queryAll();


        $format_data=[];
        foreach ($list as $key=>$value){
            $format_data[$value['advertiser']]['cost']=$value['cost'];
            $format_data[$value['advertiser']]['customer_type']=$value['customer_type'];
            $format_data[$value['advertiser']]['name']=$value['name'];
        }

        return $format_data;
    }

    public static function account_ave_cost_date_ad($start,$end){

        //客户属性 1 直客 2渠道
        $customer_type=Yii::$app->request->get('customer_type');
        $pr_lin=Yii::$app->request->get('pr_lin');
//        $list=self::find()
//            ->select('sum(a.cost) as cost,b.advertiser,b.customer_type,c.name,a.date')
//            ->join('a left join','jd_customer b','a.av_id=b.id')
//            ->leftJoin('user_backend c','c.id=b.submituser')
//            ->leftJoin('jd_account ac',"a.account_name = ac.a_users and a.avid=ac.av_id and  ac.ctime < EXTRACT ( EPOCH FROM to_timestamp( a.DATE :: TEXT, 'yyyy-MM-dd' ) + INTERVAL '1 day' ) ') and  ac.endtime > EXTRACT (EPOCH FROM to_timestamp( a.DATE :: TEXT, 'yyyy-MM-dd' )")
//            ->leftJoin('jd_contract_relevance cr','cr.contract_id=ac.contract_id')
//            ->where("date >= '$start' and date <='$end'")
//            ->andFilterWhere(['b.customer_type'=>$customer_type])
//            ->groupBy('b.advertiser,a.date,b.customer_type,c.name')
//            ->asArray()
//            ->all();

        if($customer_type!='')
        {
            $customer_type_where="where customer_type={$customer_type}";
        }

        if($pr_lin!='')
        {
            $prlin_where=" where cr.product_line={$pr_lin}";
        }


        $sql="select sum(cost) as cost_bi,sum(money) as cost,advertiser,customer_type,name,date from (select t.*,cr.fandian,cr.xj_fandian,round(cost/(100+cr.fandian)*(100-cr.xj_fandian),2) as money,customer.advertiser,users.name,customer.customer_type from (select ac.date,ac.id,ac.cost,ac.av_id,max(a.id) account_id from jd_account_cost ac
        left join jd_account a on ac.account_name = a.a_users and ac.av_id = a.avid
         and a.ctime < EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd') + interval '1 day')
         and a.endtime > EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd'))
         where ac.cost > 0 and ac.date >= '{$start}' and ac.date <='{$end}'   group by ac.id,account_name,av_id) t 
        left join jd_account ja on t.account_id = ja.id
        left join jd_contract_relevance cr on ja.contract_id = cr.contract_id 
        left join jd_customer customer on customer.id=t.av_id
		left join user_backend users on users.id =customer.submituser
		$prlin_where
        )
         t1 {$customer_type_where} GROUP BY advertiser,customer_type,name,date";
        $list=\Yii::$app->db->createCommand($sql)->queryAll();

        $data=[];
        $lenght=[];
        foreach ($list as $key=>$val)
        {
            $data[$val['advertiser']]['cost']+=$val['cost'];
            $data[$val['advertiser']]['customer_type']=$val['customer_type'];
            $data[$val['advertiser']]['name']=$val['name'];
            $lenght[$val['advertiser']]+=1;
        }

        $avg=[];
        foreach ($data as $key =>$val)
        {
            $avg[$key]['cost']=$data[$key]['cost']/$lenght[$key];
            $avg[$key]['customer_type']=$val['customer_type'];
            $avg[$key]['name']=$val['name'];
        }


        return $avg;
    }


}
