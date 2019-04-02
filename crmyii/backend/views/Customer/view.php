<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\JdCustomer */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Jd Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jd-customer-view">

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
            'type',
            'advertiser',
            'product_line',
            'industry',
            'website',
            'isapp',
            'appname',
            'tax_identification',
            'ticket_address',
            'open_account',
            'account',
            'submituser',
            'ctime:datetime',
            'kp_tel',
            'city',
            'yu_e',
            'huikuan',
            'bukuan',
            'site',
            'post_site',
            'business',
            'customer_type',
            'yushan_id',
            'undistributed_yu_e',
            'dakuan',
            'myu_e',
            'dakuan_undistributed_yu_e',
            'fapiao',
            'r_time:datetime',
            'is_claim',
            'audit',
            'audit_u',
        ],
    ]) ?>

</div>
