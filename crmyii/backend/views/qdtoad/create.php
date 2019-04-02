<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\QudaoToAdvertiser */

$this->title = 'Create Qudao To Advertiser';
$this->params['breadcrumbs'][] = ['label' => 'Qudao To Advertisers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qudao-to-advertiser-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
