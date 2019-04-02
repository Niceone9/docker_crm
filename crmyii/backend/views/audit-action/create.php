<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\AuditAction */

$this->title = 'Create Audit Action';
$this->params['breadcrumbs'][] = ['label' => 'Audit Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-action-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
