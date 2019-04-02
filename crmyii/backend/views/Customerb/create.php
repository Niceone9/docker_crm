<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Customerb */

$this->title = 'Create Customerb';
$this->params['breadcrumbs'][] = ['label' => 'Customerbs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customerb-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
