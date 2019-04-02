<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BlogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '接口文档';
$this->params['breadcrumbs'][] = $this->title;
backend\assets\TestAsset::register($this);

?>

<div class="blog-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增接口文档', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            'title',

            //'content:ntext',
            [

                'attribute'=>'created_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            // 'views',
            // 'is_delete',
            // 'update_at',
            [

                 'class' => 'yii\grid\ActionColumn'],

            [
                'attribute'=>'demourl',
                'headerOptions'=>['width'=>'100']
            ],
        ],
    ]); ?>
</div>
