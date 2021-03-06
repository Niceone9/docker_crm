<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Category', ['create'], ['class' => 'btn btn-success']) ?>
        <?=Html::a('创建栏目',['create'],['class'=>'btn btn-success','id'=>'create','data-toggle'=>'modal','data-target'=>'#operate-modal'])?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {delete}',
                'header'=>'操作',
                'buttons'=>[
                    'update'=>function($url,$model,$key){
                        return Html::a('栏目信息',$url,[
                                'title'=>'栏目信息',
                                'class'=>'btn btn-default btn-update',
                                'data-toggle'=>'modal',
                                'data-target'=>'#operate-modal'

                        ]);
                    },
                    'delete'=>function($url,$model,$key){
                        return Html::a('删除栏目',$url,[
                                'title'=>'删除',
                                'class'=>'btn btn-default',
                                'data'=>[
                                        'confirm'=>'确定到删除吗',
                                        'method'=>'post'
                                ]
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
<?php
use yii\bootstrap\Modal;
Modal::begin([
    'id' => 'operate-modal',
    'header' => '<h4 class="modal-title"></h4>',
]);
Modal::end();


?>

<?php
use yii\helpers\Url;
// 异步请求的地址
$requestCreateUrl = Url::toRoute('create');

$js = <<<JS
// 创建操作
$('#create').on('click', function () {
    $('.modal-title').html('创建栏目');
    $.get('{$requestCreateUrl}',
        function (data) {    
            // 弹窗的主题渲染页面
            $('.modal-body').html(data);
        }  
    );
});
JS;
$this->registerJs($js);
?>

<?php
// 更新
$requestUpdateUrl = Url::toRoute('update');
$js = <<<JS
    // 更新操作
    $('.btn-update').on('click', function () {
        
        $('.modal-title').html('栏目信息');
        $.get('{$requestUpdateUrl}', { id: $(this).closest('tr').data('key') },
            function (data) {
                $('.modal-body').html(data);
            }  
        );
       
    });
JS;
$this->registerJs($js);
?>
