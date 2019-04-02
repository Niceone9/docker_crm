<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\JdContractSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Jd Contracts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jd-contract-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Jd Contract', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'advertiser',
            'contract_no',
            'agent_company',
            'product_line',
            // 'type',
            // 'contract_start',
            // 'contract_end',
            // 'rebates_proportion',
            // 'show_money',
            // 'contract_money',
            // 'payment_type',
            // 'payment_time:datetime',
            // 'note:ntext',
            // 'state',
            // 'the_archive',
            // 'audit_1',
            // 'audit_2',
            // 'submituser',
            // 'ctime:datetime',
            // 'fk_money',
            // 'margin',
            // 'isxufei',
            // 'xf_hetonghao',
            // 'appname',
            // 'account',
            // 'isguidang',
            // 'users2',
            // 'susers1',
            // 'susers2',
            // 'xf_contractid',
            // 'market',
            // 'yu_e',
            // 'huikuan',
            // 'bukuan',
            // 'invoice',
            // 'iszuofei',
            // 'is_meijie',
            // 'parent_id',
            // 'mht_id',
            // 'dl_fandian',
            // 'contract_state',
            // 'title',
            // 'market1',
            // 'dakuan',
            // 'zhouqi',
            // 'is_copy',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
