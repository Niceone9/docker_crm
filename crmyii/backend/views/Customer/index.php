<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\JdCustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Jd Customers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jd-customer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Jd Customer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'type',
            'advertiser',
            'product_line',
            'industry',
            // 'website',
            // 'isapp',
            // 'appname',
            // 'tax_identification',
            // 'ticket_address',
            // 'open_account',
            // 'account',
            // 'submituser',
            // 'ctime:datetime',
            // 'kp_tel',
            // 'city',
            // 'yu_e',
            // 'huikuan',
            // 'bukuan',
            // 'site',
            // 'post_site',
            // 'business',
            // 'customer_type',
            // 'yushan_id',
            // 'undistributed_yu_e',
            // 'dakuan',
            // 'myu_e',
            // 'dakuan_undistributed_yu_e',
            // 'fapiao',
            // 'r_time:datetime',
            // 'is_claim',
            // 'audit',
            // 'audit_u',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
