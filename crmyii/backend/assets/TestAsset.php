<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/12/28
 * Time: 17:24
 */

namespace backend\assets;


use yii\web\AssetBundle;

class TestAsset extends AssetBundle
{
    public $basePath="@webroot";
    public $baseUrl="@web";
    public $css=[
       'css/site_test.css'
    ];
}