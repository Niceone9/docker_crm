<?php

namespace backend\models;

use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "audit".
 *
 * @property integer $id
 * @property integer $yid
 * @property integer $type
 * @property string $tablea
 * @property integer $users
 * @property string $note
 * @property integer $catated_at
 * @property integer $updated_at
 * @property integer $audit_count
 */
class Audit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'audit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['yid', 'type', 'users', 'catated_at', 'updated_at','audit_count'], 'integer'],
            [['note'], 'string'],
            [['tablea'], 'string', 'max' => 255],
        ];
    }

    public function getUsers0(){
        return $this->hasOne(UserBackend::className(),['id'=>'users']);
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'yid' => 'Yid',
            'type' => 'Type',
            'tablea' => 'Table',
            'users' => 'Users',
            'note' => 'Note',
            'catated_at' => 'Catated At',
            'updated_at' => 'Updated At',
        ];
    }

    //根据操作名称返回需要几级审核
    public static function audit_count($action_name){
        return self::find()->where(["action_name"=>$action_name])->one();
    }

    //审核默认操作
    public static function audit_auto($id,$action,$type,$table,$state='1',$note=''){
        //查询是否重复审核
        $rows=(new Query())->select("id,audit_count")->from("audit")->where(['yid'=>$id,'tablea'=>$action])->orderBy('id desc')->one();
        if($table!='jd_contract'){
            if($rows && $type<=$rows['audit_count'])
            {
                throw new NotFoundHttpException('不可重复审核');
            }
        }
        if($note!='')
        {
            $note_str=",note=concat('$note',note)";
        }

        $uid=Yii::$app->user->id;
        $sql1="update $table set audit=$state,audit_u=$uid,audit_count=$type $note_str where id=$id";

        $a=Yii::$app->db->createCommand($sql1)
            ->queryAll();

        $audit_db=new Audit();
        $audit_db->yid=$id;
        $audit_db->type=$state;
        $audit_db->tablea=$action;
        $audit_db->users=Yii::$app->user->id;
        $audit_db->catated_at=time();
        $audit_db->updated_at=time();
        $audit_db->audit_count=$type;
        $audit_db->note='系统自动审核';
        if($audit_db->validate()){
            $audit_db->save();

        }else
        {
            throw new NotFoundHttpException('写入审核历史记录失败');
        }

    }

}
