<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017/7/4
 * Time: 17:02
 */

namespace backend\modules\tongji\controllers;



use backend\models\MrenewHuikuan;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\Response;
use Yii;


class MrenewController extends ActiveController
{
    public $modelClass='backend\models\MrenewHuikuan';

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

    //媒体流水趋势图
    public function actionMedia_running($month){

        $data=MrenewHuikuan::pr_linglist();


        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;
        /*
        $month=hjd::teodate_month_12($month);
        foreach ($month as $key=>$val)
        {
            $data[$key]['startTime']=$val['start'];
            /*
            $val['start']=strtotime($val['start']);
            $val['end']=strtotime($val['end']);
            */
           // $data[$key]['money']=MrenewHuikuan::prlin_liushui($val['start'],$val['end']);
        //}


    }

    //媒体产品线占比  开始时间 结束时间
    public function actionMedia_bing($start,$end){
        $data=MrenewHuikuan::prlin_liushui($start,$end);

        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;

    }
    //获取时间范围内有消耗的账户数
    public function actionXiaohao_accounts($start,$end){
        $sql = "select count(*) num from (select DISTINCT(account_name) from jd_account_cost where date >= '{$start}' and date <= '{$end}' and cost >0) t";
        $account_count = Yii::$app->db->createCommand($sql)
            ->queryOne();
        return $account_count;
    }
    //获取时间范围内日消耗的极值和平均值
    public function actionXiaohao_dailyAvg($start,$end){
        $sql = "select total_cost/num as d_avg,m_cost from (
select sum(d_cost) as total_cost ,count(*) as num,max(d_cost) as m_cost from (
select sum(money) as d_cost,date from (
select t.*,ac.a_users,cr.fandian,cr.xj_fandian,round(t.cost/(cr.fandian +100)*(100-cr.xj_fandian),2) as money  from (
select a.date,a.id,a.cost,a.av_id,max(b.id) account_id from jd_account_cost a left join jd_account  b on a.account_name=b.a_users and b.ctime < EXTRACT(epoch from to_timestamp(a.date::text,'yyyy-MM-dd') + interval '1 day') and b.endtime > EXTRACT(epoch from to_timestamp(a.date::text,'yyyy-MM-dd')) and a.av_id=b.avid 
 where a.cost>0 and  a.date>='{$start}' and a.date <='{$end}' GROUP BY a.id,a.av_id,a.date,a.cost order by date asc, id asc ) t 
 left join jd_account ac on t.account_id=ac.id 
 left join jd_contract_relevance cr on cr.contract_id=ac.contract_id ) ls GROUP BY date ) ls2 ) ls3 ";
        $daily_cost = Yii::$app->db->createCommand($sql)
            ->queryOne();
        return $daily_cost;
    }
    //获取时间范围内账户消耗各个量级的比例
    public function actionXiaohao_accountPercent($start,$end){
        $sql = "select * from (select count(*) num,date,'>=50k' as name from jd_account_cost where date >= '{$start}' and date <= '{$end}' and cost >=50000 group by date 
        UNION 
        select count(*) num,date,'10k-50k' as name from jd_account_cost where date >= '{$start}' and date <= '{$end}' and cost >=10000 and cost < 50000 group by date
        UNION
        select count(*) num,date,'5k-10k' as name from jd_account_cost where date >= '{$start}' and date <= '{$end}' and cost >=5000 and cost < 10000 group by date
        UNION
        select count(*) num,date,'1k-5k' as name from jd_account_cost where date >= '{$start}' and date <= '{$end}' and cost >=1000 and cost < 5000 group by date
        UNION
        select count(*) num,date,'<1k' as name from jd_account_cost where date >= '{$start}' and date <= '{$end}' and  cost < 1000 group by date) t order by date";
        $daily_count = Yii::$app->db->createCommand($sql)
            ->queryAll();
        return $daily_count;
    }
    //获取时间范围内公司消耗的top量级变化
    public function actionXiaohao_topCompany($start,$end){
        $sql = "select t.*,c.advertiser from (select sum(cost) d_cost,date,av_id from jd_account_cost where date >= '{$start}' and date <= '{$end}' group by date,av_id) t,jd_customer c  where t.av_id=c.id and  t.d_cost > 50000 order by t.date,d_cost";
        $company_cost = Yii::$app->db->createCommand($sql)
            ->queryAll();
        return $company_cost;
    }

    public function actionXiaohao_topAccount($start,$end){
        $sql = "select cost,date,account_name from jd_account_cost where date >= '{$start}' and date <= '{$end}' and cost > 50000 order by date";
        $account_cost = Yii::$app->db->createCommand($sql)
            ->queryAll();
        return $account_cost;
    }



}