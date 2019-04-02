<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductLineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Lines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-line-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Product Line', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

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
                },
                'filter'=>\backend\models\ProductLine::find()->select(['name'])->where(['parent_id'=>'0'])->indexBy('id')->column()
            ],
            //'parent_id',
            // 'yushan_type',
            // 'fk_type',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
