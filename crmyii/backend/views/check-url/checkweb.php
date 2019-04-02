<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Category */

$this->title = 'Check WebPage';
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-create">
    <div class="row">
        <div class="col-md-5">
            <iframe src="<?=$list['url']?>" width="380" height="600"></iframe>
        </div>
        <div class="col-md-7">
            <form name="form" action="/check-url/update?id=<?=$list['id']?>" method="post">
            <input type="hidden" name="id" value="<?=$list['id']?>">
            <h2>
                链接：<a href="<?=$list['url']?>" target="_blank"><?=$list['url']?></a><br>
            </h2>
            <h2> 账户：<?=$list['ip']?></h2>

            <h3>违规项</h3>
            <div class="checkbox">

                <label>
                    <input type="checkbox" value="1" name="is_weigui[]">
                    无LOGO或者版权
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="2" name="is_weigui[]">
                    LOGO过小
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="3" name="is_weigui[]">
                    全屏下载
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="4" name="is_weigui[]">
                    域名外跳转
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="5" name="is_weigui[]">
                    涉黄涉暴及诱导
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="6" name="is_weigui[]">
                    含有二维码,微信号,电话或者地址
                </label>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="7" name="is_weigui[]">
                    LOGO版权错误
                </label>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div>
                <br>
                若无违规项 直接点击Submit 即可, 如左侧显示不清可点击链接查看整体页面。<br>
                当前进度：已检查 <?=$ycount?> 剩余 <?=$count?>
            </div>
        </div>
    </div>

</div>
