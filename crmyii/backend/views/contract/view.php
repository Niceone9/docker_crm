<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\JdContract */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Jd Contracts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jd-contract-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'advertiser',
            'contract_no',
            'agent_company',
            'product_line',
            'type',
            'contract_start',
            'contract_end',
            'rebates_proportion',
            'show_money',
            'contract_money',
            'payment_type',
            'payment_time:datetime',
            'note:ntext',
            'state',
            'the_archive',
            'audit_1',
            'audit_2',
            'submituser',
            'ctime:datetime',
            'fk_money',
            'margin',
            'isxufei',
            'xf_hetonghao',
            'appname',
            'account',
            'isguidang',
            'users2',
            'susers1',
            'susers2',
            'xf_contractid',
            'market',
            'yu_e',
            'huikuan',
            'bukuan',
            'invoice',
            'iszuofei',
            'is_meijie',
            'parent_id',
            'mht_id',
            'dl_fandian',
            'contract_state',
            'title',
            'market1',
            'dakuan',
            'zhouqi',
            'is_copy',
        ],
    ]) ?>

</div>
