<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\File;
use yii\rest\ActiveController;
use yii\web\UploadedFile;

class FileController extends ActiveController
{
    public $modelClass='backend\models\File';

    public function actionShow_files($yid,$type){
        $list=File::files($yid,$type);

        foreach ($list as $key=>$value)
        {
            $list[$key]['suffix']=\Yii::$app->hjd->getFileSuffix($value['file']);
            //$list[$key]['file']=$_SERVER['HTTP_HOST'].$value['file'];
            $list[$key]['file']=$value['file'];
        }
        $data['code']=200;
        $data['data']=$list;
        return $data;

    }

    public function actionAddfile(){

        $model = new File();

        if (\Yii::$app->request->isPost) {

            $model->load(\Yii::$app->request->post());
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');

            switch ($model->type)
            {
                case 4:
                    $path="Uploads/xufei/";
                    break;
                case 3:
                    $path="Uploads/huikuan/";
                    break;
                case 3664:
                    $path="Uploads/linshi/";
                    break;
                default:
                    $path="Uploads/yiicrmimg/";
                    break;
            }
            $type=$model->type;
            $yid=$model->yid;

            if ($ru=$model->upload($path)) {


                if($model->type!=3664){
                // 文件上传成功
                foreach ($ru as $k=>$v)
                {
                    $model2 = new File();
                    $model2->type=$type;
                    $model2->yid=$yid;
                    $model2->file=$v;
                    if(!$model2->save())
                    {
                     
                        return array('code'=>'500','msg'=>'上传失败');
                    }
                }
                }
                $data['status']='success';
                $data['code']='200';
                $data['mes']='上传成功';
                $data['files']=$ru;
                return $data;
            }

        }
    }

    public function actionDelete_file($id){
        $file=File::findOne($id);

        if(File::deleteAll(['id'=>$id]))
        {
            @unlink('./'.$file->file);
            return array('code'=>'200','msg'=>'删除成功');
        }else
        {
            return array('code'=>'200','msg'=>'删除失败');
        }
    }
}