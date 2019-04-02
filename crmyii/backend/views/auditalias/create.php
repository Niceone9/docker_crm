<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Auditalias */

$this->title = 'Create Auditalias';
$this->params['breadcrumbs'][] = ['label' => 'Auditaliases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auditalias-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
