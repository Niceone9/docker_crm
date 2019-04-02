<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\QudaoToAdvertiserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qudao To Advertisers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qudao-to-advertiser-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Qudao To Advertiser', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'advertiser_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
