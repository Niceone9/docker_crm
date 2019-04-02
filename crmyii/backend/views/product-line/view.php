<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductLine */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Product Lines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-line-view">

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
            'name',
            'title',
            [
                'attribute'=>'parent_id',
                'value'=>function($model){
                    $one=\backend\models\ProductLine::findOne($model->parent_id);
                    if($model->parent_id=='0')
                    {
                        $one['name']='父类';
                    }

                    return $one['name'];
                }
            ],
            'yushan_type',
            'fk_type',
        ],
    ]) ?>

</div>
