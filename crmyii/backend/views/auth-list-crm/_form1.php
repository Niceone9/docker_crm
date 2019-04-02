<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model backend\models\AuthListCrm */
/* @var $form yii\widgets\ActiveForm */
?>



<div class="auth-list-crm-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=$form->field($model, 'route')->widget(Select2::classname(), [
    'data' => (new \yii\db\Query())->from("auth_item")->select('name')->where(['type'=>2])->indexBy('name')->column(),
    'options' => ['placeholder' => '请选择 ...'],]);

    ?>


    <?=$form->field($model, 'role')->widget(Select2::classname(), [
        'data' => (new \yii\db\Query())->from("auth_item")->select('name')->where(['type'=>1])->indexBy('name')->column(),
        'options' => ['placeholder' => '请选择 ...'],]);

    ?>

    <?= $form->field($model, 'type')->dropDownList(['1'=>'只看自己','2'=>'看自己组','3'=>'看全部']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

