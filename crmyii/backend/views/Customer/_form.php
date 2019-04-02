<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\JdCustomer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="jd-customer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'advertiser')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_line')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'industry')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'isapp')->textInput() ?>

    <?= $form->field($model, 'appname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tax_identification')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ticket_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'open_account')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'account')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'submituser')->textInput() ?>

    <?= $form->field($model, 'ctime')->textInput() ?>

    <?= $form->field($model, 'kp_tel')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'yu_e')->textInput() ?>

    <?= $form->field($model, 'huikuan')->textInput() ?>

    <?= $form->field($model, 'bukuan')->textInput() ?>

    <?= $form->field($model, 'site')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'post_site')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'business')->textInput() ?>

    <?= $form->field($model, 'customer_type')->textInput() ?>

    <?= $form->field($model, 'yushan_id')->textInput() ?>

    <?= $form->field($model, 'undistributed_yu_e')->textInput() ?>

    <?= $form->field($model, 'dakuan')->textInput() ?>

    <?= $form->field($model, 'myu_e')->textInput() ?>

    <?= $form->field($model, 'dakuan_undistributed_yu_e')->textInput() ?>

    <?= $form->field($model, 'fapiao')->textInput() ?>

    <?= $form->field($model, 'r_time')->textInput() ?>

    <?= $form->field($model, 'is_claim')->textInput() ?>

    <?= $form->field($model, 'audit')->textInput() ?>

    <?= $form->field($model, 'audit_u')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
