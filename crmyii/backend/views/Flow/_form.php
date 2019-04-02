<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Flow */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flow-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fu_flow')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zi_flow')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fu_audit')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_true')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
