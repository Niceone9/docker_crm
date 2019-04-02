<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\AgentCompany;


use backend\models\Article;
use backend\models\JdCustomer;
use backend\models\Piaotype;
use backend\models\Waichu;
use yii\rest\ActiveController;


use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


use Yii;
use backend\components\Quanxian;

class ArticleController extends ActiveController
{
    public $modelClass='backend\models\Article';

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
        $behaviors['myBehavior']=[
            'class'=>Quanxian::className(),
        ];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
        return $behaviors;
    }

    //发票列表
    public function actionList(){
        return Article::article_list();
    }
    //新增发票
    public function  actionAdd($id){

        $data['code']=200;
        return $data;

    }


    //新增公司回款返回
    public function actionAddru(){

         if($datadat=Article::add_article()){
            $data['code']='200';
            $data['data']=$datadat;
            return  $data;
         }else
         {
             $data['code']='200';
             $data['msg']='提交失败';
         }
    }




    //回款详情
    public function actionArticle_info($id){
        $data['code']='200';
        $data['data']=Article::waichu_info($id);
        return $data;
    }

    //一级审核
    public function actionShenhe1($id){
        //写入历史记录
        $this->global_audit($id,'waichu',1,'jd_waichu');

        return array('code'=>200,'mgs'=>'审核成功');
    }

    //二级审核
    public function actionShenhe2($id){
            //写入历史记录
        $this->global_audit($id,'waichu',2,'jd_waichu');

        return array('code'=>200,'mgs'=>'审核成功');

    }

    //二级审核
    public function actionShenhe3($id){
        //写入历史记录
        $this->global_audit($id,'waichu',3,'jd_waichu');

        return array('code'=>200,'mgs'=>'审核成功');

    }

    //二级审核
    public function actionShenhe4($id){
        //写入历史记录
        $this->global_audit($id,'waichu',4,'jd_waichu');

        return array('code'=>200,'mgs'=>'审核成功');

    }

    //二级审核
    public function actionShenhe5($id){
        //写入历史记录
        $this->global_audit($id,'waichu',5,'jd_waichu');

        return array('code'=>200,'mgs'=>'审核成功');

    }

    public function actionDeletea($id){
        Article::deleteAll(['id'=>$id]);
        return array('code'=>'200','msg'=>'提交成功');
    }


    //

}