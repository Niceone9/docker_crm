<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AuditAction */

$this->title = 'Update Audit Action: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Audit Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="audit-action-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
