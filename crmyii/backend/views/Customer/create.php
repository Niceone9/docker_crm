<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\JdCustomer */

$this->title = 'Create Jd Customer';
$this->params['breadcrumbs'][] = ['label' => 'Jd Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jd-customer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
