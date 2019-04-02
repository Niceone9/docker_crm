<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\AuthListCrm */

$this->title = 'Create Auth List Crm';
$this->params['breadcrumbs'][] = ['label' => 'Auth List Crms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-list-crm-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
