<?php

namespace backend\models;

use Yii;
use yii\db\Query;
use yii\web\IdentityInterface;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "user_backend".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserBackend extends \yii\db\ActiveRecord implements  IdentityInterface
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_backend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'name','auth_key', 'password_hash', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at','istrue'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            //[['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name'=>'姓名',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'phone'=>'shouji',
        ];
    }



    /**
     * @inheritdoc
     * 根据user_backend表的主键（id）获取用户
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     * 根据access_token获取用户，我们暂时先不实现，我们在文章 http://www.manks.top/yii2-restful-api.html 有过实现，如果你感兴趣的话可以先看看
     */

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }


    /**
     * @inheritdoc
     * 用以标识 Yii::$app->user->id 的返回值
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     * 获取auth_key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     * 验证auth_key
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    /**
     * 为model的password_hash字段生成密码的hash值
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }


    /**
     * 生成 "remember me" 认证key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getRoles(){
        return $this->hasMany(AuthAssignment::className(),['user_id'=>'id']);
    }

    /**
     * 根据user_backend表的username获取用户
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username,'is_delete'=>'0']);
    }
    /**
     * 验证密码的准确性
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }



    //根据角色获取用户
    public static function roler_users($roler=''){
        if($roler!='')
        {
            $where=" and item_name='$roler'";
        }
        $rows=(new \yii\db\Query())
            ->select(['a.*','b.name'])
            ->from('auth_assignment a')
            ->leftJoin('user_backend b','a.user_id=b.id')
            ->filterWhere(['item_name'=>$roler])
            ->all();
        return $rows;

    }

    public static function userslist(){
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['user_backend.id'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['user_backend.id'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','user_backend.id',0];
        }

        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','user_backend.name', $Search_str],['like','b.item_name', $Search_str],['like','c.name', $Search_str]];

        $isdelete=Yii::$app->request->post('is_delete');
        //查询自己的组
        $query=self::find()->select('user_backend.*,b.item_name,c.name as bumen')->leftjoin('auth_assignment b','b.user_id=user_backend.id')->leftjoin('department c','b.department=c.id')->andFilterWhere($userswhere)->andFilterWhere($searchwhere)->andFilterWhere(['is_delete'=>$isdelete])->asArray();

        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
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


    //根据appid读取销售信息
    public static function appid_to_markey($appid){

        $account=Account::find()->where(['appid'=>$appid,'endtime'=>'4092599349'])->one();
        if(!$account){
            $data['code']='500';
            $data['mes']='找不到这个appid相关的账户';
        }else
        {
            $submitQuery=(new Query())->select(['submituser'])->where(['id'=>$account->avid])->from('jd_customer');
            $query=(new Query())->where(['id'=>$submitQuery])->from('user_backend');
            $data['data']=$query->one();
            $data['code']=200;

        }
        return $data;

    }

}
