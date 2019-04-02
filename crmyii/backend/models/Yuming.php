<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "yuming".
 *
 * @property integer $id
 * @property integer $note
 * @property integer $ctime
 * @property string $last_time
 * @property string $true_url
 * @property integer $account_id
 * @property integer $submitusers
 * @property integer $yidu
 */
class Yuming extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yuming';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ctime', 'account_id', 'submitusers', 'yidu','audit','audit_count'], 'integer'],
            [['last_time'], 'safe'],
            [['true_url','note','ip'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'note' => 'Note',
            'ctime' => 'Ctime',
            'last_time' => 'Last Time',
            'true_url' => 'True Url',
            'account_id' => 'Account ID',
            'submitusers' => 'Submitusers',
            'yidu' => 'Yidu',
        ];
    }

    public static function add(){

        //需求是数组
        $note=Yii::$app->request->post('note');

        foreach ($note as $key=>$val)
        {
            $model=new Yuming();
            $model->load(Yii::$app->getRequest()->getBodyParams(),'');
            $model->ctime=time();
            $model->last_time=date("Y-m-d H:i:s");
            $model->note=$val;

            $model->save();
        }


//            //如果成功 发送邮件
//            $mail= Yii::$app->mailer->compose();
//            $mail->setTo('2881486189@qq.com');
//            $mail->setSubject("尊敬的审核人员，您有新的域名解析需求！");
////$mail->setTextBody('zheshisha ');   //发布纯文字文本
//            $mail->setHtmlBody("内容：".$model->note."<br><span style='color: #ff0000'>请速到任务系统查看</span>");    //发布可以带html标签的文本
//            $mail->send();

            return $model;


    }

    public static function addy(){
        $model=new QudaoDemand();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->ctime=time();
        $model->last_time=date("Y-m-d H:i:s");
        $model->qiniu_key=$model->zip_link;
        $model->zip_link=Yii::$app->params['qn_erjiyu'].$model->zip_link;

        if($model->save())
        {

            //如果成功 发送邮件
//            $mail= Yii::$app->mailer->compose();
//            $mail->setTo('2881486189@qq.com');
//            $mail->setSubject("尊敬的审核人员，您有新的落地页需求！");
////$mail->setTextBody('zheshisha ');   //发布纯文字文本
//            $mail->setHtmlBody("内容：".$model->note."<br><span style='color: #ff0000'>请速到任务系统查看</span>");    //发布可以带html标签的文本
//            $mail->send();

            return $model;


        }else
        {
            throw new \Exception('新增失败');
        }

    }

    public static function account_yuminglist($account_id){
        return Yuming::find()
            ->where(['account_id'=>$account_id])
            ->orderBy('id desc')
            ->asArray()
            ->all();
    }

}
