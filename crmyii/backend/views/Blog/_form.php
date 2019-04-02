<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="blog-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'demourl')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'content')->widget(\yii\redactor\widgets\Redactor::className()) ?>


    <?= $form->field($model, 'is_delete')->textInput() ?>
    <?=$form->field($model,'fenlei')->checkboxList(\common\models\Blog::bumenlist())?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
