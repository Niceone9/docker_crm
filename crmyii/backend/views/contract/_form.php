<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\JdContract */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="jd-contract-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'advertiser')->textInput() ?>

    <?= $form->field($model, 'contract_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'agent_company')->textInput() ?>

    <?= $form->field($model, 'product_line')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'contract_start')->textInput() ?>

    <?= $form->field($model, 'contract_end')->textInput() ?>

    <?= $form->field($model, 'rebates_proportion')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'show_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'contract_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'payment_type')->textInput() ?>

    <?= $form->field($model, 'payment_time')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'state')->textInput() ?>

    <?= $form->field($model, 'the_archive')->textInput() ?>

    <?= $form->field($model, 'audit_1')->textInput() ?>

    <?= $form->field($model, 'audit_2')->textInput() ?>

    <?= $form->field($model, 'submituser')->textInput() ?>

    <?= $form->field($model, 'ctime')->textInput() ?>

    <?= $form->field($model, 'fk_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'margin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'isxufei')->textInput() ?>

    <?= $form->field($model, 'xf_hetonghao')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'appname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'account')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'isguidang')->textInput() ?>

    <?= $form->field($model, 'users2')->textInput() ?>

    <?= $form->field($model, 'susers1')->textInput() ?>

    <?= $form->field($model, 'susers2')->textInput() ?>

    <?= $form->field($model, 'xf_contractid')->textInput() ?>

    <?= $form->field($model, 'market')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'yu_e')->textInput() ?>

    <?= $form->field($model, 'huikuan')->textInput() ?>

    <?= $form->field($model, 'bukuan')->textInput() ?>

    <?= $form->field($model, 'invoice')->textInput() ?>

    <?= $form->field($model, 'iszuofei')->textInput() ?>

    <?= $form->field($model, 'is_meijie')->textInput() ?>

    <?= $form->field($model, 'parent_id')->textInput() ?>

    <?= $form->field($model, 'mht_id')->textInput() ?>

    <?= $form->field($model, 'dl_fandian')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'contract_state')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'market1')->textInput() ?>

    <?= $form->field($model, 'dakuan')->textInput() ?>

    <?= $form->field($model, 'zhouqi')->textInput() ?>

    <?= $form->field($model, 'is_copy')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
