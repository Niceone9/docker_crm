<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserBackendSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-backend-index">

<!--   b-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加新用户', ['signup'], ['class' => 'btn btn-success']) ?>
    </p>
<?=\yii\helpers\Url::toRoute('/UserBackend/index')?>
<form action="<?=\yii\helpers\Url::toRoute('');?>" method="get">
<input type="text" name="UserBackendSearch[id]" class="input"><input type="submit" class="btn btm-info">
</form>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           //['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute'=>'头像',
                'format'=>[
                        'image',
                        [
                                'width'=>'50',
                                'height'=>'50'
                        ]
                ],
                'value'=>function($data){
                    return $data->image;
                },
            ],
            'name',
            'username',
            'email',
            'phone',
            //'password_hash',
            //'password_reset_token',
            // 'email:email',
            // 'status',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
