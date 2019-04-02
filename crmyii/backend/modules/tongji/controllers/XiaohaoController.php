<?php

namespace backend\modules\tongji\controllers;

use backend\models\BackMoney;
use backend\models\JdAccountConsumption;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\MbackMoney;
use backend\models\RenewHuikuan;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use Yii;
/**
 * Default controller for the `crm` module
 */
class XiaohaoController extends ActiveController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public $modelClass='backend\models\JdContract';
    public function behaviors()
    {

        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];

        return $behaviors;
    }

    //根据时间返回期间新建的消耗数量
    public function actionXiaohao_date_sum($state_time,$end_time){
        //时间条件
       // $start=strtotime($state_time);
       // $end=strtotime($end_time . "+1 day");

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['c.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
             $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['c.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','c.id',0];
        }


        $search_data=['and',['>=','date',$state_time],['<=','date',$end_time]];

        $sum=JdAccountConsumption::find()->where($search_data)->join('a left join','jd_account b','a.account_id=b.id')->leftJoin('jd_contract c','b.contract_id=c.id')->andWhere($userswhere)->sum('baidu_cost_total');
       if(!$sum){
           $sum=0;
       }
        return array("code"=>'200',"sum"=>$sum);
    }

    //按照日期返回回款总额
    public function actionMoney_huikuan_sum($state_time,$end_time){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();

            $userswhere=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhere=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','a.id',0];
        }


        //->andWhere($userswhere)
        //时间条件
        $start=strtotime($state_time);
        $end=strtotime($end_time);
        $search_data=['and',['>=','b_time',$start],['<=','b_time',$end]];
        $sum=BackMoney::find()->where($search_data)->andWhere(['a.is_ultimate_shenhe'=>'1'])->join('a left join','jd_customer ad','a.advertiser=ad.id')->sum('b_money');
        if(!$sum){
            $sum=0;
        }
        return array("code"=>'200',"sum"=>$sum);
    }

    public function actionMoney_fukuan_sum($state_time,$end_time){
        //时间条件
        //$start=strtotime($state_time);
        $end=strtotime($end_time." +1 day");
        $end=date("Y-m-d",$end);
        $search_data=['and',['>=','last_time',$state_time],['<=','last_time',$end_time]];


        //print_r($search_data);exit;
        $sum=MbackMoney::find()->where($search_data)->andWhere(['is_ultimate_shenhe'=>'1','type'=>'1'])->sum('b_money');

        if(!$sum){
            $sum=0;
        }
        return array("code"=>'200',"sum"=>$sum);
    }

    public function actionMoney_diankuan_sum($state_time,$end_time){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        /*bbbbbb*/
        //时间条件
        $start=strtotime($state_time);
        $end=strtotime($end_time);


        $dk_sm = Yii::$app->db->createCommand('select sum(a.yue) as zyue from (SELECT id,advertiser,submituser,yu_e,huikuan,huikuan+bukuan-margin_money-yu_e-kehu_bukuan as yue FROM jd_customer) a where a.yue<0')
            ->queryOne();
        return array("code"=>'200',"sum"=>$dk_sm[zyue]);
    }

    public function actionMoney_bukuan_sum($state_time,$end_time){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
             $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','jd_renew_huikuan.id',0];
        }

        //时间条件
        $start=strtotime($state_time);
        $end=strtotime($end_time);

        $sum=RenewHuikuan::find()->joinWith(['advertiser0 ad','contract ht'])->andWhere(['jd_renew_huikuan.payment_type'=>[3,311],'jd_renew_huikuan.is_ultimate_shenhe'=>1])->andWhere($userswhere)->sum('jd_renew_huikuan.money');
        return array("code"=>'200',"sum"=>$sum);
    }


    public function actionMoney_kehubukuan_sum($state_time,$end_time){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','jd_renew_huikuan.id',0];
        }

        //时间条件
        $start=strtotime($state_time);
        $end=strtotime($end_time);

        $sum=RenewHuikuan::find()->joinWith(['advertiser0 ad','contract ht'])->andWhere(['jd_renew_huikuan.payment_type'=>31])->andWhere(['<>','jd_renew_huikuan.audit','2'])->andWhere($userswhere)->sum('jd_renew_huikuan.money');
        return array("code"=>'200',"sum"=>$sum);
    }


    public function actionFind_market_week_counsumption_statistics($datetype){


          //  $account_counsumption=M("AccountConsumption");
            //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
            if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
            {
                $uid=Yii::$app->user->getId();
                $userswhere=['or',['c.market'=>$uid]];
            }elseif (Yii::$app->hjd->list_type()==2)
            {
                 $uid=Yii::$app->hjd->asbumen();


                $userswhere=['or',['c.market'=>$uid]];

            }elseif (Yii::$app->hjd->list_type()==3)
            {
                $userswhere=['>','c.id',0];
            }



            if ($datetype == 'day') {
                //最近七天
                $j7=\Yii::$app->hjd->date_daye_j7();
                $list=JdAccountConsumption::find()->select('date,sum(baidu_cost_total) as consumption')->join('a left join','jd_account b','a.account_id=b.id')->leftJoin('jd_contract c','b.contract_id=c.id')->andWhere($userswhere)->where(" starttime>='$j7[start]'  and starttime<'$j7[end]' ")->groupBy('date')->orderBy("date asc")->asArray()->all();

            } elseif ($datetype == 'week') {
                //最近四周
                $zhouar =\Yii::$app->hjd->teodate_week(4, 'Monday');//本周开始时间和结束时间

                foreach ($zhouar as $key=>$val)
                {
                    $time_start = strtotime($val['start']);
                    $time_end = strtotime($val['end']);
                    $Consumption=JdAccountConsumption::find()->join('a left join','jd_account b','a.account_id=b.id')->leftJoin('jd_contract c','b.contract_id=c.id')->andWhere($userswhere)->where("starttime>='$time_start'  and starttime<'$time_end' ")->sum('baidu_cost_total');
                    $list[$key]['date']=$val['start'];
                    $list[$key]['consumption']=$Consumption?$Consumption:0;
                }

            } elseif ($datetype == 'month') {
                //最近12个月
                $yuear = \Yii::$app->hjd->teodate_month_12(12);//本月开始时间和结束时间

                foreach ($yuear as $key=>$val)
                {
                    $time_start = strtotime($val['start']);
                    $time_end = strtotime($val['end']);
                    $Consumption=JdAccountConsumption::find()->join('a left join','jd_account b','a.account_id=b.id')->leftJoin('jd_contract c','b.contract_id=c.id')->andWhere($userswhere)->where(" starttime>='$time_start'  and starttime<'$time_end'")->sum('baidu_cost_total');
                    $list[$key]['date']=$val['start'];
                    $list[$key]['consumption']=$Consumption?$Consumption:0;
                }

            }
            /*
            $time_start=strtotime(date("Y-m-d")."-7 day");
            $time_end=strtotime(date("Y-m-d"));

            $sum=$account_counsumption->field('date,sum(baidu_cost_total) as baidu_cost_total')->where("$where and starttime>='$time_start'  and starttime<'$time_end' ")->group("date")->order("date asc")->select();

            if(!$sum){$sum="0";}
           */
            $data['code'] = 200;
            $data['counsumption'] = $list;


        return $data;
    }


    //根据时间返回期间新建的消耗数量
    public function actionXiaohao_date_list($state_time,$end_time){
        //时间条件
        // $start=strtotime($state_time);
        // $end=strtotime($end_time . "+1 day");

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['c.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
             $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['c.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','c.id',0];
        }



        $search_data=['and',['>=','a.date',$state_time],['<=','a.date',$end_time]];

        $sum=JdAccountConsumption::find()->select(['a.*','b.a_users','cu.advertiser'])->join('a left join','jd_account b','a.account_id=b.id')->leftJoin('jd_contract c','b.contract_id=c.id')->leftJoin('jd_customer cu','cu.id=c.advertiser')->where($search_data)->andWhere($userswhere)->asArray()->all();
        return $sum;

    }


    //欠款公司最高的20条，并取得她的近三次回款记录
    public function actionDiankuan_compare(){

        //$dk_sm=$customer->field('id,advertiser,yu_e,huikuan,huikuan-yu_e as yue')->order("yue asc")->select();

        $dk_sm = Yii::$app->db->createCommand('select a.* from (SELECT id,advertiser,submituser,yu_e,huikuan,huikuan+bukuan-margin_money-yu_e-kehu_bukuan as yue FROM jd_customer where customer_type in(1,2)) a where a.yue<-10 order by a.yue asc')
            ->queryAll();
        //公司逾期金额
        $date=Yii::$app->request->get('date');
        if (!$date){
            $date = date('Y-m-d');
        }
        
        $yuqi_sm = Yii::$app->db->createCommand('select advertiser as id ,sum(xf_qiane) as money from jd_renew_huikuan where payment_type in (1,2) and xf_qiane > 0 and is_ultimate_shenhe=1 and pay_day < \''.$date.'\' group by advertiser')
            ->queryAll();
        
        foreach ($yuqi_sm as $key=>$val){
            $yuqi_company[$val['id']] = $val;
        }

        foreach ($dk_sm as $key=>$val)
        {
            $zuijinhk=BackMoney::find()->where("advertiser=$val[id] and audit!=2")->select('b_time,b_money')->orderBy("b_time desc")->limit('5')->asArray()->all();

            foreach ($zuijinhk as $k=>$v)
            {
                $zuijinhk[$k]['payment_time']=date("Y-m-d",$v['b_time']);
                $zuijinhk[$k]['money']=number_format($v['b_money'],2);
                //number_format
            }
            $yuqi_money = $yuqi_company[$val['id']]['money'];
            $dk_sm[$key]['yuqi'] = number_format($yuqi_money,2);
            $dk_sm[$key]['huikuan_record']=$zuijinhk;
            $dk_sm[$key]['yu_e']=number_format($val['yu_e'],2);
            $dk_sm[$key]['huikuan']=number_format($val['huikuan'],2);
            $dk_sm[$key]['yue']=number_format($val['yue'],2);
            //公司负责销售
            //$u=users_info($val['submituser']);
            //$dk_sm[$key]['market']=$u['name'];
            $avinfo=JdCustomer::kehu_info_sm($val[id]);
            //var_dump($avinfo);exit;
            $dk_sm[$key]['market']=$avinfo['submituser0']['name'];
            $dk_sm[$key]['ae']=$avinfo['business0']['name'];
            //查询合同下面有没有 按消耗结算
            $jscontract=JdContract::find()->where("advertiser=$val[id] and is_jstype=1")->count();


            if($jscontract > 0)
            {
                $dk_sm[$key]['advertiser']=$dk_sm[$key]['advertiser']."(-)";
            }

        }

        $data['code'] = 200;
        $data['diankuan_huikuan_record'] = $dk_sm;
      return $data;
    }

    //打款与回款折线图

    public function actionDakuanhuikuantu($yyyy){
        $sql='select sum(b_money) as money,to_char(last_time,\'YYYY-MM\') as date from jd_mback_money where type=1 and is_ultimate_shenhe=1 GROUP BY date';
        $sql2="select * from ($sql) t where date like '%{$yyyy}%' order by date asc ";
        $dk_sm = Yii::$app->db->createCommand($sql2)
            ->queryAll();
        $zong=0;
        foreach ($dk_sm as $key=>$val)
        {
            $zong+=$val['money'];
            $dk_sm[$key]['zong']=$zong;
        }



        $sql='select round(sum(b_money),2) as money,to_char(to_timestamp(b_time),\'YYYY-MM\') as date from jd_back_money where is_ultimate_shenhe=1 GROUP BY date';
        $sql2="select * from ($sql) t where date like '%{$yyyy}%'  order by date asc ";
        $hk_sm = Yii::$app->db->createCommand($sql2)
            ->queryAll();
        $zong=0;
        foreach ($hk_sm as $key=>$val)
        {
            //获取截止到当前月的总额
            $zong+=$val['money'];
            $hk_sm[$key]['zong']=round($zong,2);
        }

        $a='';
        for ($i=0;$i<=11;$i++)
        {
            if($i+1<10)
            {
                $a='0';
            }
            if(empty($hk_sm[$i]))
            {
                $hk_sm[$i]=array(
                   'money'=>0,
                    'date'=>$yyyy."-".$a.($i+1),
                    'zong'=>$hk_sm[$i-1]['zong']
                );
            }

            if(empty($dk_sm[$i]))
            {
                $dk_sm[$i]=array(
                    'money'=>0,
                    'date'=>$yyyy."-".$a.($i+1),
                    'zong'=>$dk_sm[$i-1]['zong']
                );
            }
        }

        return array('code'=>'200','dakuan'=>$dk_sm,'huikuan'=>$hk_sm);


    }

    //消耗折线图
    public function actionXiaohaotj($start,$end){

//         $sql="
// select to_char(date,'MM-DD') as date,sum(cost) as cost,sum(money) as money from (
// SELECT A
// 	.*,
// 	b.fandian,
// CASE
	
// 	WHEN b.fandian = 0 THEN
// 	A.COST ELSE round( A.COST / (( b.fandian + 100 ) / 100 ), 2 ) 
// 	END AS money 
// FROM
// 	jd_account_cost_real
// A LEFT JOIN jd_contract_relevance b ON A.contract_id = b.contract_id where istuikuan=0
// ) t where date>='{$start}' and date <='{$end}' GROUP BY date order by date asc 
// ";

        $pr_lin=Yii::$app->request->get('pr_lin');

        if($pr_lin!='')
        {
            $prlin_where=" and cr.product_line={$pr_lin}";
        }

        $customer_type=Yii::$app->request->get('customer_type');
        if($customer_type!='')
        {
            $customer_type_where=" and ad.customer_type=".$customer_type;
        }


        $sql = "select date,sum(cost) as cost,sum(money) as money from (select t.*,cr.fandian,cr.xj_fandian,round(cost/(100+cr.fandian)*(100-cr.xj_fandian),2) as money from (select ac.date,ac.id,ac.cost,ac.av_id,max(a.id) account_id from jd_account_cost ac
        left join jd_account a on ac.account_name = a.a_users and ac.av_id = a.avid
         and a.ctime < EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd') + interval '1 day')
         and a.endtime > EXTRACT(EPOCH FROM to_timestamp(ac.date::text,'yyyy-MM-dd'))
         where ac.cost > 0 and ac.date >= '{$start}' and ac.date <='{$end}'   group by ac.id,account_name,av_id) t 
        left join jd_account ja on t.account_id = ja.id
        left join jd_contract_relevance cr on ja.contract_id = cr.contract_id
        left join jd_customer ad on ad.id=ja.avid 
         where cr.product_line!=0 $prlin_where $customer_type_where) t1 group by date order by date asc";
        $hk_sm = Yii::$app->db->createCommand($sql)
            ->queryAll();

        return $hk_sm;
    }

    //日期，产品线消耗 或者金额
    public function actionXiaohaotjprlin($date){
        if(!$date)
        {
            return array('code'=>'500','msg'=>'缺少参数');
        }
        $sql='
select date,sum(cost) as cost,sum(money) as money,name from (
SELECT A
	.*,
	b.fandian,
	pl.name,
CASE
	
	WHEN b.fandian = 0 THEN
	A.COST ELSE round( A.COST / (( b.fandian + 100 ) / 100 ), 2 ) 
	END AS money 
FROM
	jd_account_cost_real
A LEFT JOIN jd_contract_relevance b ON A.contract_id = b.contract_id
left join jd_product_line pl on b.product_line=pl.id where istuikuan=0
) t where date=\''.$date.'\' GROUP BY date,name order by date desc 
';

        $hk_sm = Yii::$app->db->createCommand($sql)
            ->queryAll();

        return $hk_sm;
    }


    public function actionXiaohaozhou(){
        $date=Yii::$app->hjd->day_7d();
        $ace=[];


        $prlin=Yii::$app->request->post('prlin');
        if(is_array($prlin))
        {
            $prin=implode(',',$prlin);
            $prwhere=" and conre.product_line in($prin)";
        }

        foreach ($date as $key=>$value)
        {

           $sql="SELECT SUM
	( A.COST ) AS costmoney,
	(a.cost/((conre.fandian+100)/100)) as xjmoney,
	A.DATE,
	C.advertiser,
	ROW_NUMBER () OVER ( ORDER BY sum(cost) DESC ) rank,
	csumcoust.zcost
FROM
	jd_account_cost_real
	A LEFT JOIN jd_contract b ON A.contract_id = b.
	ID LEFT JOIN jd_customer C ON C.ID = b.advertiser 
	left join jd_contract_relevance conre on b.id=conre.contract_id
	left join (
	  select c.id,sum(a.cost) as zcost from jd_account_cost_real a left join jd_contract b ON A.contract_id = b.id left join jd_customer c on c.id=b.advertiser where a.istuikuan=0 and c.id!=0 $prwhere  GROUP  by c.id
	) csumcoust on c.id=csumcoust.id 
	where date='{$value[start]}' $prwhere
GROUP BY
	C.advertiser,
	A.DATE ,
	xjmoney,
	zcost
	order by costmoney desc 
";

            $hk_sm = Yii::$app->db->createCommand($sql)
                ->queryAll();


           // $ace[$v['advertiser']][$key]['date']=$value['start'];

            foreach ($hk_sm as $k=>$v)
            {

                $ace[$v['advertiser']][$key]=array(
                  'cost'=>$v['costmoney'],
                  'smoney'=>round($v['xjmoney'],2),
                  'rank'=>$v['rank'],
                  'date'=>$v['date'],
                  'zcost'=>$v['zcost'],
                );
            }
        }

        foreach ($date as $k=>$v)
        {


            foreach ($ace as $key=>$val)
            {
                if($v['start']!=$val[$k]['date'])
                {
                    $ace[$key][$k]=array(
                        'date'=>$v['start'],
                        'smoney'=>0,
                        'rank'=>0,
                        'cost'=>0,
                        'zcost'=>$ace[$key][-1]
                    );
                }

            }

        }



        $aox=[];
        foreach ($ace as $key=>$val)
        {

            $aox[]=array(
                'adver'=>$key,

                'data'=>$val
            );
        }

        return array('code'=>'200','data'=>$aox);

    }


    public function actionXiaohaozhou_smoney(){
        $date=Yii::$app->hjd->day_7d();
        $ace=[];
        $prlin=Yii::$app->request->post('prlin');
        if(is_array($prlin))
        {
            $prin=implode(',',$prlin);
            $prwhere=" and conre.product_line in($prin)";
        }

        foreach ($date as $key=>$value)
        {

            $sql="SELECT 
	(a.cost/((conre.fandian+100)/100)) as costmoney,
	A.DATE,
	C.advertiser,
	ROW_NUMBER () OVER ( ORDER BY sum((a.cost/((conre.fandian+100)/100))) DESC ) rank,
	csumcoust.zcost
FROM
	jd_account_cost_real
	A LEFT JOIN jd_contract b ON A.contract_id = b.
	ID LEFT JOIN jd_customer C ON C.ID = b.advertiser 
	left join jd_contract_relevance conre on b.id=conre.contract_id
	left join (
	  select c.id,sum((a.cost/((conre.fandian+100)/100))) as zcost from jd_account_cost_real a left join jd_contract b ON A.contract_id = b.id left join jd_customer c on c.id=b.advertiser left join jd_contract_relevance conre on b.id=conre.contract_id where istuikuan=0 c.id!=0 $prwhere  GROUP  by c.id
	) csumcoust on C.id=csumcoust.id 
	where date='{$value[start]}' $prwhere
GROUP BY
	C.advertiser,
	A.DATE ,
	costmoney,
	zcost
	order by costmoney desc
";


            $hk_sm = Yii::$app->db->createCommand($sql)
                ->queryAll();
            // $ace[$v['advertiser']][$key]['date']=$value['start'];

            foreach ($hk_sm as $k=>$v)
            {

                $ace[$v['advertiser']][$key]=array(
                    'cost'=>$v['costmoney'],
                    'smoney'=>round($v['xjmoney'],2),
                    'rank'=>$v['rank'],
                    'date'=>$v['date'],
                    'zcost'=>$v['zcost']
                );
            }
        }

        foreach ($date as $k=>$v)
        {


            foreach ($ace as $key=>$val)
            {
                if($v['start']!=$val[$k]['date'])
                {
                    $ace[$key][$k]=array(
                        'date'=>$v['start'],
                        'smoney'=>0,
                        'rank'=>0,
                        'cost'=>0,
                        'zcost'=>$ace[$key][-1]
                    );
                }

            }

        }

        $aox=[];
        foreach ($ace as $key=>$val)
        {
            $aox[]=array(
                'adver'=>$key,
                'data'=>$val
            );
        }

        return array('code'=>'200','data'=>$aox);

    }


    public function actionXufeizhou(){
        $date=Yii::$app->hjd->day_7d();
        $ace=[];


        $prlin=Yii::$app->request->post('prlin');
        if(is_array($prlin))
        {
            $prin=implode(',',$prlin);
            $prwhere=" and conre.product_line in($prin)";
        }

        foreach ($date as $key=>$value)
        {
            $value[start] = strtotime($value[start]);
            $sql="SELECT SUM
	( A.show_money ) AS costmoney,
	a.money as xjmoney,

	to_char(to_timestamp(a.payment_time),'YYYY-MM-DD') date,
	C.advertiser,
	ROW_NUMBER () OVER ( ORDER BY sum(a.show_money) DESC ) rank,
	csumcoust.zcost
FROM
	jd_renew_huikuan
	A LEFT JOIN jd_contract b ON A.xf_contractid = b.
	ID LEFT JOIN jd_customer C ON C.ID = b.advertiser 
	left join jd_contract_relevance conre on b.id=conre.contract_id
	left join (
	  select c.id,sum(a.show_money) as zcost from jd_renew_huikuan a left join jd_contract b ON A.xf_contractid = b.id left join jd_customer c on c.id=b.advertiser where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 $prwhere  GROUP  by c.id
	) csumcoust on c.id=csumcoust.id 
	where a.payment_time='{$value[start]}' and a.payment_type in(1,2) and a.is_ultimate_shenhe=1 $prwhere
GROUP BY
	C.advertiser,
	date,
	xjmoney,
	zcost
	order by costmoney desc 
";

            $hk_sm = Yii::$app->db->createCommand($sql)
                ->queryAll();


            // $ace[$v['advertiser']][$key]['date']=$value['start'];

            foreach ($hk_sm as $k=>$v)
            {

                $ace[$v['advertiser']][$key]=array(
                    'cost'=>$v['costmoney'],
                    'smoney'=>round($v['xjmoney'],2),
                    'rank'=>$v['rank'],
                    'date'=>$v['date'],
                    'zcost'=>$v['zcost'],
                );
            }
        }

        foreach ($date as $k=>$v)
        {


            foreach ($ace as $key=>$val)
            {
                if($v['start']!=$val[$k]['date'])
                {
                    $ace[$key][$k]=array(
                        'date'=>$v['start'],
                        'smoney'=>0,
                        'rank'=>0,
                        'cost'=>0,
                        'zcost'=>$ace[$key][-1]
                    );
                }

            }

        }



        $aox=[];
        foreach ($ace as $key=>$val)
        {

            $aox[]=array(
                'adver'=>$key,

                'data'=>$val
            );
        }

        return array('code'=>'200','data'=>$aox);

    }

    public function actionXufei_money_zhou(){
        $date=Yii::$app->hjd->day_7d();
        $ace=[];


        $prlin=Yii::$app->request->post('prlin');
        if(is_array($prlin))
        {
            $prin=implode(',',$prlin);
            $prwhere=" and conre.product_line in($prin)";
        }

        foreach ($date as $key=>$value)
        {
            $value[start] = strtotime($value[start]);
            $sql="SELECT SUM
	( A.money ) AS costmoney,
	a.money as xjmoney,

	to_char(to_timestamp(a.payment_time),'YYYY-MM-DD') date,
	C.advertiser,
	ROW_NUMBER () OVER ( ORDER BY sum(a.money) DESC ) rank,
	csumcoust.zcost
FROM
	jd_renew_huikuan
	A LEFT JOIN jd_contract b ON A.xf_contractid = b.
	ID LEFT JOIN jd_customer C ON C.ID = b.advertiser 
	left join jd_contract_relevance conre on b.id=conre.contract_id
	left join (
	  select c.id,sum(a.money) as zcost from jd_renew_huikuan a left join jd_contract b ON A.xf_contractid = b.id left join jd_customer c on c.id=b.advertiser where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 $prwhere  GROUP  by c.id
	) csumcoust on c.id=csumcoust.id 
	where a.payment_time='{$value[start]}' and a.payment_type in(1,2) and a.is_ultimate_shenhe=1 $prwhere
GROUP BY
	C.advertiser,
	date,
	xjmoney,
	zcost
	order by costmoney desc 
";

            $hk_sm = Yii::$app->db->createCommand($sql)
                ->queryAll();


            // $ace[$v['advertiser']][$key]['date']=$value['start'];

            foreach ($hk_sm as $k=>$v)
            {

                $ace[$v['advertiser']][$key]=array(
                    'cost'=>$v['costmoney'],
                    'smoney'=>round($v['xjmoney'],2),
                    'rank'=>$v['rank'],
                    'date'=>$v['date'],
                    'zcost'=>$v['zcost'],
                );
            }
        }

        foreach ($date as $k=>$v)
        {


            foreach ($ace as $key=>$val)
            {
                if($v['start']!=$val[$k]['date'])
                {
                    $ace[$key][$k]=array(
                        'date'=>$v['start'],
                        'smoney'=>0,
                        'rank'=>0,
                        'cost'=>0,
                        'zcost'=>$ace[$key][-1]
                    );
                }

            }

        }





        $aox=[];
        foreach ($ace as $key=>$val)
        {

            $aox[]=array(
                'adver'=>$key,

                'data'=>$val
            );
        }

        return array('code'=>'200','data'=>$aox);

    }



    //按照产品线统计续费
    public function actionProductlinrenewtj($start,$end){
        //$start = strtotime($start);
        $end = strtotime($end."+1 day");
        $end=date("Y-m-d",$end);

        $list=RenewHuikuan::find()
            ->select('p.name,sum(a.money) as money')
            ->join('a left join','jd_contract b','a.xf_contractid=b.id')
            ->leftJoin('jd_contract_relevance c','c.contract_id=b.id')
            ->leftJoin('jd_product_line p','p.id=c.product_line')
            ->where(['a.payment_type'=>array(1,2),'a.is_ultimate_shenhe'=>1])
            ->andWhere(['and',['>=','a.last_time',$start],['<','a.last_time',$end]])
            ->groupBy('p.name')
            ->asArray()
            ->all();

        $list2=RenewHuikuan::find()
            ->select('p.name,sum(a.money) as money')
            ->join('a left join','jd_contract b','a.xf_contractid=b.id')
            ->leftJoin('jd_contract_relevance c','c.contract_id=b.id')
            ->leftJoin('jd_product_line p','p.id=c.product_line')
            ->where(['a.payment_type'=>array(15),'a.is_ultimate_shenhe'=>1])
            ->andWhere(['and',['>=','a.last_time',$start],['<','a.last_time',$end]])
            ->groupBy('p.name')
            ->asArray()
            ->all();

        $sum=0;

        foreach ($list as $key=>$value)
        {
            foreach ($list2 as $k=>$v)
            {
                if($value['name']==$v['name'])
                {
                    $list[$key]['money']-=$v['money'];


                }

            }

            $sum+=$list[$key]['money'];
        }




        return array('code'=>'200','data'=>$list,'sum'=>$sum);
    }

    //按照产品线统计续费
    public function actionProductlinadtj($start,$end){
        $start = strtotime($start);
        $end = strtotime($end."+1 day");
        $list=RenewHuikuan::find()
            ->select('b.advertiser,b.id,sum(a.money) as money')
            ->join('a left join','jd_customer b','a.advertiser=b.id')
            ->where(['a.payment_type'=>array(1,2),'a.is_ultimate_shenhe'=>1])
            ->andWhere(['and',['>=','a.payment_time',$start],['<','a.payment_time',$end]])
            ->limit(10)
            ->groupBy('b.advertiser,b.id')
            ->orderBy('money desc')
            ->asArray()
            ->all();

        $idlist=ArrayHelper::getColumn($list,'id');

        $list2=RenewHuikuan::find()
            ->select('b.advertiser,b.id,sum(a.money) as money')
            ->join('a left join','jd_customer b','a.advertiser=b.id')
            ->where(['a.payment_type'=>array(1,2),'a.is_ultimate_shenhe'=>1])
            ->andWhere(['and',['>=','a.payment_time',$start],['<','a.payment_time',$end]])
            ->andWhere(['<>','b.id',$idlist])
            ->groupBy('b.advertiser,b.id')
            ->sum('money');
        array_push($list,['advertiser'=>'其他','money'=>$list2]);

        return array('code'=>'200','data'=>$list);
    }


    //按照产品线统计消耗 和金额 时间段
    //日期，产品线消耗 或者金额
    public function actionXiaohaomoneytjprlin($start,$end){
        $prlin=Yii::$app->request->post('prlin');
        if($prlin)
        {
            $prin=implode(',',$prlin);
            $prwhere=" and pl.id in($prin)";
        }
        if(!$start)
        {
            return array('code'=>'500','msg'=>'缺少参数');
        }
        $sql="
select date,sum(cost) as cost,sum(money) as money from (
SELECT A
	.*,
	b.fandian,
	pl.name,
CASE
	
	WHEN b.fandian = 0 THEN
	A.COST ELSE round( A.COST / (( b.fandian + 100 ) / 100 ), 2 ) 
	END AS money 
FROM
	jd_account_cost_real
A LEFT JOIN jd_contract_relevance b ON A.contract_id = b.contract_id
left join jd_product_line pl on b.product_line=pl.id where b.istuikuan=0 {$prwhere}
) t where date>='$start' and date <='$end'  GROUP BY date order by date asc 
";

        $sumsql="
select sum(cost) as cost,sum(money) as money from (
SELECT A
	.*,
	b.fandian,
	pl.name,
CASE
	
	WHEN b.fandian = 0 THEN
	A.COST ELSE round( A.COST / (( b.fandian + 100 ) / 100 ), 2 ) 
	END AS money 
FROM
	jd_account_cost_real
A LEFT JOIN jd_contract_relevance b ON A.contract_id = b.contract_id
left join jd_product_line pl on b.product_line=pl.id  where b.istuikuan=0 {$prwhere}
) t where date>='$start' and date <='$end' GROUP BY date order by date asc 
";

        $hk_sm = Yii::$app->db->createCommand($sql)
            ->queryAll();
        $hk_sm_z = Yii::$app->db->createCommand($sql)
            ->queryOne();


        return array('hk_sm'=>$hk_sm,'hk_sm_all'=>$hk_sm_z);
    }

    //日期，产品线消耗 或者金额
    public function actionRenewmoneytjprlin($start,$end){
        $start = strtotime($start);
        $end = strtotime($end."+1 day");
        $prlin=Yii::$app->request->post('prlin');
        $prlin=Yii::$app->request->getBodyParam('prlin');

        if($prlin)
        {
            $prin=implode(',',$prlin);
            $prwhere=" where pl.id in($prin)";
        }
        if(!$start)
        {
            return array('code'=>'500','msg'=>'缺少参数');
        }
        $sql="
select to_char(to_timestamp(payment_time),'YYYY-MM-DD') as date,sum(show_money) as cost,sum(money) as money from (
SELECT A
	.*,
	b.fandian,
	pl.name
    
FROM
	jd_renew_huikuan
A LEFT JOIN jd_contract_relevance b ON A.xf_contractid = b.contract_id
left join jd_product_line pl on b.product_line=pl.id {$prwhere}
) t where payment_type in(1,2) and is_ultimate_shenhe=1 and payment_time>='$start' and payment_time <'$end' GROUP BY payment_time order by payment_time asc 
";

        $sumsql="
select to_char(to_timestamp(payment_time),'YYYY-MM-DD') as date,sum(show_money) as cost,sum(money) as money from (
SELECT A
	.*,
	b.fandian,
	pl.name
    
FROM
	jd_renew_huikuan
A LEFT JOIN jd_contract_relevance b ON A.xf_contractid = b.contract_id
left join jd_product_line pl on b.product_line=pl.id {$prwhere}
) t where payment_type in(1,2) and is_ultimate_shenhe=1 and payment_time>='$start' and payment_time <'$end' GROUP BY payment_time order by payment_time asc 
";
        $hk_sm = Yii::$app->db->createCommand($sql)
            ->queryAll();
        $hk_sm_z = Yii::$app->db->createCommand($sql)
            ->queryOne();


        return array('hk_sm'=>$hk_sm,'hk_sm_all'=>$hk_sm_z);
    }



    //查看产品线消耗占比  账户比  与 现金
    public function actionXiaohaoprzhanbi($start,$end)
    {
        $prlin=Yii::$app->request->post('prlin');
        if($prlin)
        {
            $prin=implode(',',$prlin);
            $prwhere=" and pl.id in($prin)";
        }
        if(!$start)
        {
            return array('code'=>'500','msg'=>'缺少参数');
        }
        $sumsql="
select sum(cost) as cost,sum(money) as money,name from (
SELECT A
	.*,
	b.fandian,
	pl.name,
CASE
	
	WHEN b.fandian = 0 THEN
	A.COST ELSE round( A.COST / (( b.fandian + 100 ) / 100 ), 2 ) 
	END AS money 
FROM
	jd_account_cost_real
A LEFT JOIN jd_contract_relevance b ON A.contract_id = b.contract_id
left join jd_product_line pl on b.product_line=pl.id where b.istuikuan=0 {$prwhere}
) t where date>='$start' and date <='$end' GROUP BY name
";

        $hk_sm = Yii::$app->db->createCommand($sumsql)
            ->queryAll();

        return $hk_sm;
    }

    //查看续费 产品线占比

    public function actionRenewproduct($start,$end){

        $prlin=Yii::$app->request->post('prlin');

        if(!$start)
        {
            return array('code'=>'500','msg'=>'缺少参数');
        }
        $start = strtotime($start);
        $end = strtotime($end." +1 day");

        $andwhere='a.payment_time>='.$start.' and a.payment_time <'.$end;
        $moneylist=(new Query())->from('jd_renew_huikuan')
            ->select(['pr.name','pr.id','sum(a.money) as money','sum(a.show_money) as cost'])
            ->where([
                'a.is_ultimate_shenhe'=>'1',
                'a.payment_type'=>[1,2]
            ])
            ->andWhere($andwhere)
            ->join('a left join','jd_contract_relevance c','c.contract_id=a.xf_contractid')
            ->leftJoin('jd_product_line pr','pr.id=c.product_line')
            ->andFilterWhere(['pr.id'=>$prlin])
            ->groupBy(['pr.name','pr.id'])->all();

        $tuikuanlist=(new Query())->from('jd_mrenew_huikuan')
            ->select(['pr.name','pr.id','sum(a.money) as money','sum(a.show_money) as cost'])
            ->where([
                'a.is_ultimate_shenhe'=>'1',
                'a.payment_type'=>[15]
            ])
            ->andWhere($andwhere)
            ->join('a left join','jd_contract_relevance c','c.contract_id=a.xf_contractid')
            ->leftJoin('jd_product_line pr','pr.id=c.product_line')
            ->andFilterWhere(['pr.id'=>$prlin])
            ->groupBy(['pr.name','pr.id'])
            ->all();

        foreach ($moneylist as $key=>$value)
        {
            foreach ($tuikuanlist as $tkey =>$kval)
            {
                if($value['id']==$kval['id'])
                {
                    $moneylist[$key]['money']-=$kval['money']?$kval['money']:0;
                    $moneylist[$key]['cost']-=$kval['cost']?$kval['cost']:0;
                }
            }

        }
        return $moneylist;
    }

}
