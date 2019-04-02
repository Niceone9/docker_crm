<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/12
 * Time: 16:10
 */
namespace backend\modules\api\controllers;

use backend\models\Account;
use backend\models\JdCustomer;
use backend\modules\api\models\LoginForm;
use Yii;
use backend\models\UserBackend;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class PublicController extends \yii\rest\ActiveController
{
    public $modelClass = 'backend\modules\api\models\UserBackend';


    //判断用户名和密码是否正确
    public function actionLogin()
    {

        $mode=new LoginForm();
        if($mode->load(\Yii::$app->getRequest()->getBodyParams(),'') && $us=$mode->login())
        {


            //用户信息
            $us->image=$us->image;
            $arr=$us;

            //获取所有权限

            $userId=$us->id;
            $routes = [];
            $manager=Yii::$app->getAuthManager();
            foreach ($manager->getPermissionsByUser($userId) as $name => $value) {
                if ($name[0] === '/') {
                    $routes[] = $name;
                }
            }
            //获取用户角色
            $rule=$manager->getAssignments($us->id);

            $array['code']=200;
            $array['data']=$arr;
            $array['auth']=$routes;
            $array['roles']=$rule;
            return $array;
        }else
        {
            $array['code']=500;
            $array['mes']='登录失败';
            return $array;
        }

    }

    //根据条件获去角色下面的用户
    public function actionRoler_users($role_name=''){
        if($role_name!='')
        {
            $idin= Yii::$app->hjd->role_name_users($role_name);
            return UserBackend::find()->select('id,name')->where(['id'=>$idin])->andWhere(['is_delete'=>0])->all();
        }
        return UserBackend::roler_users($role_name);

    }

    public function actionHjd()
    {
       
        return Account::importxiaohao('1');
    }


    //导入消耗
    public  function actionTest($file='')
    {



        $fileName='Uploads/3.xls';
        if($fileName=='')
        {
            return false;
        }




        $data = \moonland\phpexcel\Excel::import($fileName, [
            'setFirstRecordAsKeys' => false, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel.
            'setIndexSheetByName' => false, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric.
            //  'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
        ]);



        var_dump($data);exit;

        $level=@\Yii::$app->hjd->getmaxdim($data);



        if($level==2)
        {
            foreach ($data as $key=>$val)
            {
                if($key!=1 and $val['B']!=''){

                    if(self::dataxiaohao($val['A'],$val['B'],$val['C'])==false){
                        $err[]=$val['A'].'-'.$val['B'];
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
                            $err[]=$v['A'].'-'.$v['B'];
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


    //node 测试
    public static function actionNoderev($id){
    //    sleep(1);
        $list=JdCustomer::find()
            ->where(['submituser'=>$id])
            ->all();


        if($list)
        {
            return $list;
        }else
        {
            return ['msg'=>'没数据'];
        }

    }



    //node 测试
    public function actionNoderev2(){

        $list=UserBackend::find()->limit(30)->all();
        foreach ($list as $key=>$val)
        {
          self::actionNoderev($val['id']);
        }

        return $list;

    }

    //测试链接七牛test
    public function actionQiniu(){
        //require 'path_to_sdk/vendor/autoload.php';
        $app= Yii::getAlias('@app');

        require "$app/vendor/qiniu/php-sdk/autoload.php";
        // 初始化签权对象
        $auth = new Auth(Yii::$app->params['qn_accessKey'], Yii::$app->params['qn_secretKey']);
        $bucket = 'moshi-yushan-public-bucket';
        // 生成上传Token
        $token = $auth->uploadToken($bucket);

        echo $token;
        exit;
// 构建 UploadManager 对象
        $uploadMgr = new UploadManager();


        $a=$uploadMgr->putFile($token,'hjdtest',"$app/web/uploads/file/20170825/_s4inkaz1n-pQ03mzpacoRfTjV2Meoaz.jpg");
        var_dump($a);
        exit;
    }
    //获取上传token

    public function actionGetuptoken(){
        $app= Yii::getAlias('@vendor');
        require "$app/qiniu/php-sdk/autoload.php";
        // 初始化签权对象
        $auth = new Auth(Yii::$app->params['qn_accessKey'],Yii::$app->params['qn_secretKey']);
        //$bucket = 'moshi-yushan-public-bucket';
        $bucket = Yii::$app->params['qn_bucket'];
        // 生成上传Token
        $token = $auth->uploadToken($bucket);
        return $token;
    }

    //删除文件
    public function actionDeleteqiniu($key){
        $app= Yii::getAlias('@vendor');
        require "$app/qiniu/php-sdk/autoload.php";
        $accessKey = Yii::$app->params['qn_accessKey'];
        $secretKey =Yii::$app->params['qn_secretKey'];
        $bucket = Yii::$app->params['qn_bucket'];
        $key = $key;
        $auth = new Auth($accessKey, $secretKey);

        $config=new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
        $err = $bucketManager->delete($bucket, $key);
        if ($err) {
            print_r($err);
        }else
        {
            return array('code'=>'200','msg'=>'删除成功');
        }
    }

    //删除文件
    public static function deleteqiniu($key){
        $app= Yii::getAlias('@vendor');
        require "$app/qiniu/php-sdk/autoload.php";
        $accessKey = Yii::$app->params['qn_accessKey'];
        $secretKey =Yii::$app->params['qn_secretKey'];
        $bucket = Yii::$app->params['qn_bucket'];
        $key = $key;
        $auth = new Auth($accessKey, $secretKey);

        $config=new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
        $err = $bucketManager->delete($bucket, $key);
        if ($err) {
            print_r($err);
        }else
        {
            return array('code'=>'200','msg'=>'删除成功');
        }
    }


    public function actionUserlist(){
        $list=UserBackend::find()->select(['name','username','phone','email'])->where(['is_delete'=>'0'])->asArray()->all();
        return $list;
    }

    public function actionHh(){
        phpinfo();
        echo Yii::getVersion();
        exit;
    }



}