<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;
use backend\models\Audit;
use backend\models\Flow;
use backend\models\MbackMoney;
use backend\models\MrenewHuikuan;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use backend\components\Quanxian;

class AuditController extends ActiveController
{
    public $modelClass='backend\modls\Audit';
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
        //$behaviors['myBehavior']=[
        //    'class'=>Quanxian::className(),
        //];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        return $behaviors;
    }




    public function actionShenhe(){
        echo 232;
    }

    public function actionShehe_history($id,$table){
        return Audit::find()->where(['yid'=>$id,'tablea'=>$table])->with('users0')->orderBy('id asc')->asArray()->all();
    }

    //父子流程对应关系
    public function actionFlow_fz_show($fu_flow,$id){
        $list=Flow::find()
            ->select('a.fu_flow,a.zi_flow,a.fu_audit,b.type,b.fuid,b.ziid,b.ctime')
            ->join('a left join','flow_gl b','a.id=b.type')
            ->where(['a.fu_flow'=>$fu_flow,'b.fuid'=>$id,'a.is_true'=>1])
            ->asArray()
            ->all();



        foreach ($list as $key=>$value)
        {
            if($value['zi_flow']=='dakuan')
            {
                $zistate=MbackMoney::findOne($value['ziid']);
                if($zistate['audit']=='2')
                {
                    $list[$key]['state']='2';
                }else
                {
                    $list[$key]['state']='0';
                }
                if($zistate['is_ultimate_shenhe']=='1')
                {
                    $list[$key]['state']='1';
                }
            }

            if($value['zi_flow']=='meijie_tuikuan')
            {
                $zistate=MrenewHuikuan::findOne($value['ziid']);
                if($zistate['audit']=='2')
                {
                    $list[$key]['state']='2';
                }else
                {
                    $list[$key]['state']='0';
                }
                if($zistate['is_ultimate_shenhe']=='1')
                {
                    $list[$key]['state']='1';
                }
            }

        }
        return $list;
    }

    public function actionShenhe_users(){
        $routing=\Yii::$app->request->post('routing');
        $auth=\Yii::$app->getUrlManager()->rules;

        echo "<pre>";
        var_dump($auth);
        exit;
        foreach ($auth as $key=>$value)
        {
            var_dump($value->name)."<br>";
        }
        echo $routing;

        exit;
    }
}