<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\JdContract */

$this->title = 'Create Jd Contract';
$this->params['breadcrumbs'][] = ['label' => 'Jd Contracts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jd-contract-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
