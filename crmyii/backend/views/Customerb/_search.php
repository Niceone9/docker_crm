<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CustomerbSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customerb-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'advertiser') ?>

    <?= $form->field($model, 'industry') ?>

    <?= $form->field($model, 'website') ?>

    <?= $form->field($model, 'appname') ?>

    <?php // echo $form->field($model, 'submituser') ?>

    <?php // echo $form->field($model, 'ctime') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'r_time') ?>

    <?php // echo $form->field($model, 'is_claim') ?>

    <?php // echo $form->field($model, 'audit') ?>

    <?php // echo $form->field($model, 'audit_u') ?>

    <?php // echo $form->field($model, 'site') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'ytime') ?>

    <?php // echo $form->field($model, 'is_xu') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
