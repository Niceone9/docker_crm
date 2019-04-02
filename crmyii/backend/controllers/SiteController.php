<?php
namespace backend\controllers;

use backend\models\UserBackend;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','hjd'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index','hjd'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        // 判断用户是访客还是认证用户
        // isGuest为真表示访客，isGuest非真表示认证用户，认证过的用户表示已经登录了，这里跳转到主页面
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        // 实例化登录模型 common\models\LoginForm
        $model = new LoginForm();
        // 接收表单数据并调用LoginForm的login方法
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
            // 非post直接渲染登录表单
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionHjd()
    {
        $db = new yii\db\Connection([
            'dsn' => 'pgsql:host=rds455ekt1422z8sh7e2o.pg.rds.aliyuncs.com;port=3432;dbname=crm',
            'username' => 'rdspg',
            'password' => 'anmeng',
            'tablePrefix' => 'jd_',
            'charset' => 'utf8',
        ]);

        $a = $db->createCommand('select * from jd_users')->queryAll();
        //创建用户
        $passwor=Yii::$app->security->generatePasswordHash('123456');
        foreach ($a as $key => $val)
        {
            $users= new UserBackend();
            $users->id=$val['id'];
            $users->username=$val['users'];
            $users->auth_key=Yii::$app->security->generateRandomString();
            $users->password_hash=$passwor;
            $users->created_at=$val['ctime'];
            $users->updated_at=$val['ctime'];
            $users->email=$val['email'];
            $users->name=$val['name'];
            $users->phone=$val['phone'];
            $users->jobtime=$val['jobtime'];
            $users->intime=$val['intime'];
            $users->istrue=$val['istrue'];
            $users->nianjia=$val['nianjia'];
            $users->njuptime=$val['njuptime'];
            $users->is_delete=$val['is_delete'];
            $users->image=$val['image'];

            if($users->save())
            {

            }else
            {
                var_dump($users->getErrors());
            }


        }

    }
}
