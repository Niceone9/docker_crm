<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\QudaoToAdvertiser */

$this->title = 'Update Qudao To Advertiser: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Qudao To Advertisers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qudao-to-advertiser-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
