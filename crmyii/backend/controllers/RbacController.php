<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/9/29
 * Time: 10:51
 */

namespace backend\controllers;
use Yii;

use yii\web\Controller;

class RbacController extends Controller
{
    public function actionInit(){
        // 这个是我们上节课添加的authManager组件，组件的调用方式没忘吧？
        $auth=Yii::$app->authManager;
        $blogIndex=$auth->createPermission('/blog/index');
        $blogIndex->description='博客列表';
        $auth->add($blogIndex);
        //创建一个角色'博客管理',并为该角色分配 '/blog/index'的权限
        $blogManage=$auth->createRole('博客管理');
        $auth->add($blogManage);
        $auth->addChild($blogManage,$blogIndex);
        $auth->assign($blogManage,1);

    }
    // 添加权限
    public function actionInit2 ()
    {
        $auth = Yii::$app->authManager;

        // 添加权限, 注意斜杠不要反了
        $blogView = $auth->createPermission('/blog/view');
        $auth->add($blogView);
        $blogCreate = $auth->createPermission('/blog/create');
        $auth->add($blogCreate);
        $blogUpdate = $auth->createPermission('/blog/update');
        $auth->add($blogUpdate);
        $blogDelete = $auth->createPermission('/blog/delete');
        $auth->add($blogDelete);

        // 分配给我们已经添加过的"博客管理"权限
        $blogManage = $auth->getRole('博客管理');
        $auth->addChild($blogManage, $blogView);
        $auth->addChild($blogManage, $blogCreate);
        $auth->addChild($blogManage, $blogUpdate);
        $auth->addChild($blogManage, $blogDelete);
    }
}