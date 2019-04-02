<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/21
 * Time: 15:37
 */

namespace backend\models;


use yii\base\Model;
use yii\web\UploadedFile;
class Uptouxiang extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false,'checkExtensionByMimeType' => false, 'extensions' => 'png,jpg,jpeg'],
        ];
    }

    public function upload($path)
    {
        //检查目录没有则创建
        $path=$path.date("Ymd");

        $dir=$path;
        if(!file_exists($dir))
        {
            mkdir($dir,0777,true);
        }
        if ($this->validate()) {

            $suijishu=time().rand(1000,9999);

            $this->imageFile->saveAs($path.'/' . $suijishu . '.'  . $this->imageFile->extension);
            //$this->imageFile->saveAs('Uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return $path.'/' . $suijishu . '.'  . $this->imageFile->extension;
        } else {
            return false;
            //var_dump($this->errors);exit;
        }
    }
}