<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Flow */

$this->title = 'Create Flow';
$this->params['breadcrumbs'][] = ['label' => 'Flows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flow-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
