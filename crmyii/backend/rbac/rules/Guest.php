<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/16
 * Time: 9:29
 */

namespace backend\rbac\rules;

use yii\rbac\Rule;
use Yii;
use yii\web\User;

class Guest extends Rule
{
    public $name='customerb';
    /**
     * @param string|integer $user 用户 ID.
     * @param Item $item 该规则相关的角色或者权限
     * @param array $params 传给 ManagerInterface::checkAccess() 的参数
     * @return boolean 代表该规则相关的角色或者权限是否被允许
     */
    public function execute($user, $item, $params)
    {
        return true;
        $username = Yii::$app->user->identity->username;
        $auth=Yii::$app->getAuthManager();
        var_dump($params);exit;
        var_dump($auth->getRolesByUser(2));exit;

    }
}