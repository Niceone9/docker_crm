<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/9
 * Time: 17:15
 */
namespace backend\modules\api\controllers;
use backend\models\AuthAssignment;
use backend\models\Uptouxiang;
use backend\models\UserBackend;
use Codeception\PHPUnit\Constraint\Page;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\UploadedFile;

class UserController extends ActiveController
{
    public $modelClass='backend\modules\api\models\UserBackend';
    public $serializer=[
      'class'=>'yii\rest\Serializer',
      'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                ///HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        return $behaviors;
    }
    public function actionH()
    {
        foreach (UserBackend::find()->each(10) as $customers) {
            // $customers 是 10 个或更少的客户对象的数组
            echo $customers->name.'<br>';
            var_dump($customers);
        }
        exit;
    }
    public function actionSearch(){
        $a=UserBackend::findOne(2);
        var_dump($this->checkAccess('/admin/menu/index',$a));
        echo 1;
        exit;
    }

    //获取用户是否有权限
    public function actionGet_auth($actionid){

        $array['code']=200;
        $array['if_permission']=$this->checkAccess($actionid);
        return $array;
    }

    //检查用户所有权限
    public static function actionGet_uesr_auth(){
        //当前登录用户的id
        $user=Yii::$app->getUser();
        $userId=$user->id;
        // 获取当前用户已经分配过的路由权限
        // 写的比较简单，有过基础的可自行完善，比如解决"*"的问题，看不懂的该行注释自行忽略
        $routes = [];
        $manager=Yii::$app->getAuthManager();
        foreach ($manager->getPermissionsByUser($userId) as $name => $value) {
            if ($name[0] === '/') {
                $routes[] = $name;
            }
        }

        return $routes;
    }

    //修改用户信息
    public static function actionUpusersinfo($uid){
        $users=UserBackend::findOne($uid);
        $postdata=\Yii::$app->request->post();
        $users->load(\Yii::$app->getRequest()->getBodyParams(),'');




        if($users->save())
        {
            //带部门就修改部门
            if($postdata['bumen']){
                self::actionUserss_to_juese($users->id,$postdata['bumen'],$postdata['role']);
            }
            return  array('code'=>'200','msg'=>'修改成功','data'=>$users);
        }else
        {
            return  array('code'=>'500','msg'=>'修改失败','data'=>$users->errors);
        }
    }

    //修改密码
    public static function actionUppassword($uid){
        $users=UserBackend::findOne($uid);

        $password=\Yii::$app->request->post('password');

        if(\Yii::$app->security->validatePassword($password, $users->password_hash))
        {
            $newpassword=\Yii::$app->request->post('newpassword');
            $users->password_hash=\Yii::$app->security->generatePasswordHash($newpassword);
            if($users->save())
            {
                return  array('code'=>'200','msg'=>'修改成功','data'=>$users);
            }else
            {
                return  array('code'=>'500','msg'=>'修改失败','data'=>$users->errors);
            }

        }else{
            return  array('code'=>'500','msg'=>'原始密码错误');

        }

    }

    //修改头像
    public static function actionUptouxiang($uid){
        $model = new Uptouxiang();

        if (\Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($touxiang=$model->upload('Uploads/touxiang/')) {
                $users=UserBackend::findOne($uid);
                $users->image=$touxiang;
                if($users->save())
                {
                    return  array('code'=>'200','msg'=>'修改成功','data'=>$users);
                }else
                {
                    return  array('code'=>'500','msg'=>'修改失败','data'=>$users->errors);
                }
                // 文件上传成功
                return;
            }else
            {
                var_dump($model->errors);exit;
            }
        }

    }

    //删除用户
    public function actionUser_delete($id){
        if(UserBackend::updateAll(['is_delete'=>1],['id'=>$id]))
        {
            return array('code'=>'200','msg'=>'删除成功');
        }else
        {
            return array('code'=>'500','msg'=>'删除失败');
        }
    }

    //新增用户
    public function actionAddusers(){

        // 实例化一个表单模型，这个表单模型我们还没有创建，等一下后面再创建
        $model = new \backend\models\SignupForm();
        // 下面这一段if是我们刚刚分析的第二个小问题的实现，下面让我具体的给你描述一下这几个方法的含义吧
        // $model->load() 方法，实质是把post过来的数据赋值给model的属性
        // $model->signup() 方法, 是我们要实现的具体的添加用户操作
       // $model->load(\Yii::$app->request->post());

        $postdata=\Yii::$app->request->post();


        if ($model->load(\Yii::$app->request->getBodyParams(),'')) {
            if($user=$model->signup())
            {
                // 添加完用户之后，我们跳回到index操作即列表页
                $this->actionUsers_to_bumen($user->id,$postdata['bumen'],$postdata['role']);
            }else
            {
                return $model->errors;
            }

           return array("code"=>"200","data"=>$user);
        }else
        {
            return array("code"=>"200","data"=>$model->errors);

        }

    }

    public function actionUserslist(){
        return UserBackend::userslist();
    }

    public function  actionUsersinfo($id){
        $info=UserBackend::find()->select('user_backend.*,b.item_name,c.name as bumen')->leftjoin('auth_assignment b','b.user_id=user_backend.id')->leftjoin('department c','b.department=c.id')->where(['user_backend.id'=>$id])->asArray()->one();
        $roles=$this->actionRoles();
        //查询用户拥有的角色
        $routes = [];
        $manager=\Yii::$app->getAuthManager();
        foreach ($manager->getRolesByUser($id) as $name => $value) {
                if($name!='guest'){
                    $routes[] = $name;
                }
        }
        foreach ($roles as $key=>$val)
        {
           $juese[$key]['name']=$val;
           $juese[$key]['seleceed']=0;
        }

        foreach ($routes as $k=>$v)
        {

                $key=array_search($v,$roles);
            $juese[$key]['seleceed']=1;

        }

        $data['info']=$info;
        $data['roles']=$juese;


        return $data;

    }

    public function actionRoles(){
        $auth=\Yii::$app->getAuthManager();
        $roles=$auth->getRoles();
        $a=array();
        $i=0;
        foreach ($roles as $key=>$val)
        {
            $a[$i]=$key;
            $i++;
        }

        return $a;
    }

    public static function actionUserss_to_bumen(){
        $postdata=\Yii::$app->request->post();

        foreach ($postdata['users'] as $key=>$val){

            self::actionUsers_to_bumen($val,$postdata['bumen'],$postdata['role']);
        }
        return array('code'=>'200','msg'=>'分配成功');
    }

    public static function actionUserss_to_juese($user,$bumen,$juese){
        AuthAssignment::deleteAll(['user_id'=>$user]);

        foreach ($juese as $key=>$val){

            $auth_assignment= new AuthAssignment();
            $auth_assignment->item_name=$val;
            $auth_assignment->user_id=$user;
            $auth_assignment->department=$bumen;
            $auth_assignment->created_at=time();
            if(!$auth_assignment->save())
            {
                return false;
            }
        }

        return true;

    }


    //给用户分配部门
    public static function actionUsers_to_bumen($user_id,$bumen,$role){

        //查询用户如果有就修改没有就新增
        AuthAssignment::deleteAll(['user_id'=>$user_id]);

        $auth_assignment= new AuthAssignment();
        $auth_assignment->item_name=$role;
        $auth_assignment->user_id=$user_id;
        $auth_assignment->department=$bumen;
        $auth_assignment->created_at=time();
        if($auth_assignment->save())
        {
            return array('code'=>'200','data'=>$auth_assignment);
        }else
        {
           // echo AuthAssignment::find()->createCommand()->getRawSql();
           // var_dump($auth_assignment->errors);exit;
            //var_dump($auth_assignment->errors);exit;
            return array('code'=>'500','data'=>$auth_assignment->errors);
        }
        /*
        $On=AuthAssignment::find()->where(['user_id'=>$user_id])->one();
        if($On!=''){

            $On->item_name=$role;
            $On->user_id=$user_id;
            $On->department=$bumen;
            $On->created_at=time();
            if($On->save())
            {
                return array('code'=>'200','data'=>$On);
            }else
            {
                return array('code'=>'500','data'=>$On->errors);
            }
        }else
        {
            $auth_assignment= new AuthAssignment();
            $auth_assignment->item_name=$role;
            $auth_assignment->user_id=$user_id;
            $auth_assignment->department=$bumen;
            $auth_assignment->created_at=time();
            if($auth_assignment->save())
            {
                return array('code'=>'200','data'=>$auth_assignment);
            }else
            {
                return array('code'=>'500','data'=>$auth_assignment->errors);
            }
        }
*/
    }


    //获取待办事项
    public function actionDaiban(){

        $rowes=\Yii::$app->hjd->get_roles();

       var_dump($rowes);
        exit;

    }


}