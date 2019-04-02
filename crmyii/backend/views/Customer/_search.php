<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\JdCustomerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="jd-customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'advertiser') ?>

    <?= $form->field($model, 'product_line') ?>

    <?= $form->field($model, 'industry') ?>

    <?php // echo $form->field($model, 'website') ?>

    <?php // echo $form->field($model, 'isapp') ?>

    <?php // echo $form->field($model, 'appname') ?>

    <?php // echo $form->field($model, 'tax_identification') ?>

    <?php // echo $form->field($model, 'ticket_address') ?>

    <?php // echo $form->field($model, 'open_account') ?>

    <?php // echo $form->field($model, 'account') ?>

    <?php // echo $form->field($model, 'submituser') ?>

    <?php // echo $form->field($model, 'ctime') ?>

    <?php // echo $form->field($model, 'kp_tel') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'yu_e') ?>

    <?php // echo $form->field($model, 'huikuan') ?>

    <?php // echo $form->field($model, 'bukuan') ?>

    <?php // echo $form->field($model, 'site') ?>

    <?php // echo $form->field($model, 'post_site') ?>

    <?php // echo $form->field($model, 'business') ?>

    <?php // echo $form->field($model, 'customer_type') ?>

    <?php // echo $form->field($model, 'yushan_id') ?>

    <?php // echo $form->field($model, 'undistributed_yu_e') ?>

    <?php // echo $form->field($model, 'dakuan') ?>

    <?php // echo $form->field($model, 'myu_e') ?>

    <?php // echo $form->field($model, 'dakuan_undistributed_yu_e') ?>

    <?php // echo $form->field($model, 'fapiao') ?>

    <?php // echo $form->field($model, 'r_time') ?>

    <?php // echo $form->field($model, 'is_claim') ?>

    <?php // echo $form->field($model, 'audit') ?>

    <?php // echo $form->field($model, 'audit_u') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
