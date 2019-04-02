<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\JdContractSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="jd-contract-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'advertiser') ?>

    <?= $form->field($model, 'contract_no') ?>

    <?= $form->field($model, 'agent_company') ?>

    <?= $form->field($model, 'product_line') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'contract_start') ?>

    <?php // echo $form->field($model, 'contract_end') ?>

    <?php // echo $form->field($model, 'rebates_proportion') ?>

    <?php // echo $form->field($model, 'show_money') ?>

    <?php // echo $form->field($model, 'contract_money') ?>

    <?php // echo $form->field($model, 'payment_type') ?>

    <?php // echo $form->field($model, 'payment_time') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'state') ?>

    <?php // echo $form->field($model, 'the_archive') ?>

    <?php // echo $form->field($model, 'audit_1') ?>

    <?php // echo $form->field($model, 'audit_2') ?>

    <?php // echo $form->field($model, 'submituser') ?>

    <?php // echo $form->field($model, 'ctime') ?>

    <?php // echo $form->field($model, 'fk_money') ?>

    <?php // echo $form->field($model, 'margin') ?>

    <?php // echo $form->field($model, 'isxufei') ?>

    <?php // echo $form->field($model, 'xf_hetonghao') ?>

    <?php // echo $form->field($model, 'appname') ?>

    <?php // echo $form->field($model, 'account') ?>

    <?php // echo $form->field($model, 'isguidang') ?>

    <?php // echo $form->field($model, 'users2') ?>

    <?php // echo $form->field($model, 'susers1') ?>

    <?php // echo $form->field($model, 'susers2') ?>

    <?php // echo $form->field($model, 'xf_contractid') ?>

    <?php // echo $form->field($model, 'market') ?>

    <?php // echo $form->field($model, 'yu_e') ?>

    <?php // echo $form->field($model, 'huikuan') ?>

    <?php // echo $form->field($model, 'bukuan') ?>

    <?php // echo $form->field($model, 'invoice') ?>

    <?php // echo $form->field($model, 'iszuofei') ?>

    <?php // echo $form->field($model, 'is_meijie') ?>

    <?php // echo $form->field($model, 'parent_id') ?>

    <?php // echo $form->field($model, 'mht_id') ?>

    <?php // echo $form->field($model, 'dl_fandian') ?>

    <?php // echo $form->field($model, 'contract_state') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'market1') ?>

    <?php // echo $form->field($model, 'dakuan') ?>

    <?php // echo $form->field($model, 'zhouqi') ?>

    <?php // echo $form->field($model, 'is_copy') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
