<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomerbSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customerbs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customerb-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Customerb', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'advertiser',
            'industry',
            'website',
            'appname',
            // 'submituser',
            // 'ctime:datetime',
            // 'city',
            // 'r_time:datetime',
            // 'is_claim',
            // 'audit',
            // 'audit_u',
            // 'site',
            // 'type',
            // 'ytime:datetime',
            // 'is_xu',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
