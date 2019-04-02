<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Customerb */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Customerbs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customerb-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'advertiser',
            'industry',
            'website',
            'appname',
            'submituser',
            'ctime:datetime',
            'city',
            'r_time:datetime',
            'is_claim',
            'audit',
            'audit_u',
            'site',
            'type',
            'ytime:datetime',
            'is_xu',
        ],
    ]) ?>

</div>
