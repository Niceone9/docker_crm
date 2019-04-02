<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\JdCustomer */

$this->title = 'Update Jd Customer: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Jd Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="jd-customer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
