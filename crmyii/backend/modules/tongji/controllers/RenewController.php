<?php
namespace backend\modules\tongji\controllers;
use backend\models\RenewHuikuan;
use yii\rest\ActiveController;
use Yii;
use yii\web\Response;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


class RenewController extends ActiveController
{
    public $modelClass='backend\models\RenewHuikuan';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                //HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
       // $behaviors['myBehavior']=[
       //     'class'=>Quanxian::className(),
       // ];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        return $behaviors;
    }
    public function actionIndex1()
    {
        return ['111','2222'];
    }

    public function actionDkrenewlist(){
     $posts = Yii::$app->db->createCommand('SELECT * from (SELECT t.*,c.advertiser,c.appname,((hk_time-xf_time)/3600/24) rg FROM 	(
		SELECT
			y.avid,
			y.money,
			hk.money hk,
			hk.payment_time hk_time,
			xf.money xf_money,
			xf.payment_time xf_time
		FROM
			jd_yihuikuanxufei y,
			jd_renew_huikuan hk,
			jd_renew_huikuan xf
		WHERE
			y.xf_id = xf. ID
		AND y.hk_id = hk. ID
	) t,jd_customer c where  t.avid = c.id) t1 order by rg desc;
')
            ->queryAll();
        /*
        $model=RenewHuikuan::find()
            ->where('xf_qiane>0 and ')
            ->all();
        */
        return $posts;
    }

    //客户续费排名 参数 数量,开始时间，结束时间,主体
    public function actionRenew_rank($number,$start,$end,$ourMain){

        if($start!='default' && $end!='default') {
            $start = strtotime($start);
            $end = strtotime($end."+1 day");
        }

        $rank_list=RenewHuikuan::xufei_rank($number,$start,$end,$ourMain);

        $return_data['code']=200;
        $return_data['data']=$rank_list;
        return $return_data;

    }

    //客户续费月份趋势图
    public function actionRenew_trend($month,$ourMain,$cuid=''){
        $yyyy=Yii::$app->request->post('year');
        $month=Yii::$app->hjd->teodate_month_12($month,$yyyy);

        foreach ($month as $key=>$val)
        {
            $data[$key]['startTime']=$val['start'];
            $val['start']=strtotime($val['start']);
            $val['end']=strtotime($val['end']);
            $data[$key]['money']=RenewHuikuan::xufei_timedate($val['start'],$val['end'],$ourMain,$cuid);
        }

        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;
    }



    /*----------------------------------------------------------利润---------------------------------------------------------------------------------------------*/

    //客户利润 参数 数量,开始时间，结束时间，主体
    public function actionRenew_profits($number,$start,$end,$ourMain,$cuid='',$prlin=''){
        if($start!='default' && $end!='default') {
            $start = strtotime($start);
            $end = strtotime($end."+1 day");
        }

        $profits_list=RenewHuikuan::xufei_profits($number,$start,$end,$ourMain,$cuid,$prlin);

        $return_data['code']=200;
        $return_data['data']=$profits_list;
        return $return_data;

    }


    //客户已回款利润
    public function actionRenew_true_profits($number,$start,$end,$ourMain,$cuid='',$prlin=''){
        if($start!='default' && $end!='default') {
            $start = strtotime($start);
            $end = strtotime($end." +1 day");
        }
        $profits_list_true=RenewHuikuan::xufei_true_profits($number,$start,$end,$ourMain,$cuid,$prlin);


        $return_data['code']=200;
        $return_data['data']=$profits_list_true;
        return $return_data;
    }

    //客户续费月份趋势图 已回款
    public function actionLirun_trend($month,$ourMain,$cuid='',$prlin=''){

        $yyyy=Yii::$app->request->post('year');

        $month=Yii::$app->hjd->teodate_month_12($month,$yyyy);
        foreach ($month as $key=>$val)
        {
            $data[$key]['startTime']=$val['start'];
            $val['start']=strtotime($val['start']);
            $val['end']=strtotime($val['end']."+1 day");
            $data[$key]['money']=RenewHuikuan::lirun_timedate($val['start'],$val['end'],$ourMain,$cuid,$prlin);
            if($cuid!='')
            {
                $data[$key]['xf_money']=RenewHuikuan::xufei_timedate($val['start'],$val['end'],$ourMain,$cuid,$prlin);
            }
            $data[$key]['month']=date('m',$val['start']);
        }

        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;
    }

    //客户续费月份趋势图 已回款
    public function actionLirun_true_trend($month,$ourMain,$cuid='',$prlin=''){
        $yyyy=Yii::$app->request->post('year');

        $month=Yii::$app->hjd->teodate_month_12($month,$yyyy);


        foreach ($month as $key=>$val)
        {


            $data[$key]['startTime']=$val['start'];
            $val['start']=strtotime($val['start']);
            $val['end']=strtotime($val['end']." +1 day");
            $data[$key]['money']=RenewHuikuan::lirun_true_timedate($val['start'],$val['end'],$ourMain,$cuid,$prlin);
            if($cuid!='')
            {
                $data[$key]['xf_money']=RenewHuikuan::xufei_timedate($val['start'],$val['end'],$ourMain,$cuid,$prlin);
            }
            $data[$key]['month']=date('m',$val['start']);
        }
        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;
    }

    public function actionRenew_statistical($start,$end){
        if($start!='default' && $end!='default') {
            $start = strtotime($start);
            $end = strtotime($end);
        }
        $data['max']=RenewHuikuan::renew_tongji($start,$end,'desc');
        $data['min']=RenewHuikuan::renew_tongji($start,$end,'asc');
        $data['average']=RenewHuikuan::renew_tongji_average($start,$end);
        var_dump($data);
        exit;
    }

    //公司续费12个月
    public function actionRenewcustomerrenewmoney($id){

    }
}
