<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\ProductLine */

$this->title = 'Create Product Line';
$this->params['breadcrumbs'][] = ['label' => 'Product Lines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-line-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
