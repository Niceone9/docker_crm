<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "jd_file".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $yid
 * @property string $file
 */
class File extends \yii\db\ActiveRecord
{
    public $imageFiles;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'yid'], 'required'],
            [['type', 'yid'], 'integer'],
            [['file'], 'string', 'max' => 250],
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpg,txt,xls,xlsx,pdf,doc,docx,ppt,zip,rar,pptx,jpeg', 'checkExtensionByMimeType' => false,'maxFiles' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'yid' => 'Yid',
            'file' => 'File',
        ];
    }
    //返回文件列表
    public static function files($yid,$type){
        return self::find()->where(['yid'=>$yid,'type'=>$type])->asArray()->all();
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

            foreach ($this->imageFiles  as $key=> $file) {

                $suijishu=time().rand(1000,9999).$this->type.$this->yid;
                $a=$file->saveAs($path.'/' . $suijishu . '.' . $file->extension);
                $array[$key]=$path.'/' . $suijishu . '.' . $file->extension;

            }

            return $array;
        } else {

           return $this->errors;
            exit;
        }
    }

    public static function export_excel($header,$data=''){
        set_time_limit(0);
        \moonland\phpexcel\Excel::export([
           'models'=>[['张三','18','1'],['李四','20','1'],['王五','22','1']],
           'fileName'=>time(),
           'columns' => ['column1','column2','column3'], //without header working, because the header will be get label from attribute label.
	       'headers' => ['column1' => '姓名','column2' => '年龄', 'column3' => '婚否'],

        ]);

        //$str=ord('A');
        //echo $str;
        exit;


    }

}
