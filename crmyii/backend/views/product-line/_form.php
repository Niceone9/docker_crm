<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductLine */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-line-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'audit_1')->textInput() ?>

    <?= $form->field($model, 'audit_2')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php
//    $b=array(0=>"父类");
    $a=\backend\models\ProductLine::find()->select('name')->where(['parent_id'=>0])->indexBy('id')->column()

    ?>
    <?= $form->field($model, 'parent_id')->dropDownList($a,['prompt' => '请选择']) ?>

    <?= $form->field($model, 'yushan_type')->textInput() ?>

    <?= $form->field($model, 'fk_type')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
