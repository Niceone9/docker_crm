<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FlowSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flows';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flow-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Flow', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'fu_flow',
            'zi_flow',
            'fu_audit',
            'is_true',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
