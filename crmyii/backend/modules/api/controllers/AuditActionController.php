<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/20
 * Time: 16:12
 */

namespace backend\modules\api\controllers;


use backend\models\AuditAction;
use backend\models\AuditAlias;
use backend\models\AuthItem;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

class AuditActionController extends ActiveController
{
    public $modelClass='backend\models\AuditAction';
  //  private $rolepermis=[];



    public function actionH(){

        $manager=\Yii::$app->getAuthManager();

        //获取所有角色
        $roles=AuthItem::findAll(['type'=>1]);
        $rolepermis=[];
        //获取觉得所用的所有权限及路由
        foreach ($roles as $val)
        {

            $ar=$manager->getPermissionsByRole($val['name']);

            $rolepermis[$val['name']]=$ar;
        }

        //审核列表
        $auditlist=AuditAction::find()->asArray()->all();

        //var_dump($auditlist);
        foreach ($auditlist as $key=>$val)
        {
            //查询是否有审核别名
            $bieming=AuditAlias::find()->where(['action_name'=>$val['action_name']])->all();
            if($bieming)
            {
                $auditlist[$key]['audit_alias']=$bieming;
            }

            switch ($val['action_name'])
            {
                case 'contract_new':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/contract-new/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'contract':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/contract/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'renew':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/renew-huikuan/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'backmoney':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/back-money/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'refund_kehu':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/refund-money/shenhe'.$i.'_tuikuan_kehu',$rolepermis);
                    }
                    break;
                case 'refund_account_p':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/refund-money/shenhe'.$i.'_tuikuan_account_p',$rolepermis);
                    }
                    break;
                case 'refund_account_z':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/refund-money/shenhe'.$i.'_tuikuan_account_z',$rolepermis);
                    }
                    break;
                case 'bukuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/bukuan/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'meijie-dakuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/mback-money/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'meijie-huikuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/meijie-huikuan/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'meijie-bukuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/meijie-bukuan/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'kebao':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/customerb/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'waichu':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/waichu/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'qingjia':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/qingjia/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'qd_dakuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/qd-dakuan/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'Invoice':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/invoice/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'zhuankuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/zhuankuan/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'meijie_contract':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/meijie-contract/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'baozhengjin':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/margin/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'margin-dakuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/meijie-margin/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'meiti-margin-tuikuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/meijie-margin/shenhetui'.$i,$rolepermis);
                    }
                    break;
                case 'margin_tuikaun':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/margin/shenhetui'.$i,$rolepermis);
                    }
                    break;
                case 'bukuanbi':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/bukuan/bishenhe'.$i,$rolepermis);
                    }
                    break;
                case 'beikuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/beikuan/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'xiadan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/renew-huikuan/xiadan-shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'meijie_tuikuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/meijie-tuikuan/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'contract_accounts':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/contract-accounts/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'meijie_fakuan':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/meijie-fakuan/shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'beikuan_account_tuikuan':

                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/beikuan/tuikuan-shenhe'.$i,$rolepermis);
                    }
                    break;
                case 'gaizhang':
                    for($i=1;$i<=$val['audit_count'];$i++)
                    {
                        $auditlist[$key]['shenhe'][$i]=$this->actionShenheren('/api/gaizhang/shenhe'.$i,$rolepermis);
                    }
                    break;

            }
        }
       return $auditlist;
        exit;


    }

    public function actionShenheren($lurou,$rolepermis)
    {

        foreach ($rolepermis as $key=>$val)
        {
            $ar=ArrayHelper::getColumn($val,'name');
            if(in_array($lurou,$ar))
            {

                $ren[]=$key;
            }

        }

        return $ren;

        exit;
        var_dump($this->rolepermis);exit;

        //获取所有角色
        $roles=AuthItem::findAll(['type'=>1]);

        //获取觉得所用的所有权限及路由
        $ren=[];
        foreach ($roles as $val)
        {
           //$ar=$manager->getPermissionsByRole($val['name']);

           //$ar=ArrayHelper::getColumn($ar,'name');
           /*if(in_array($lurou,$ar))
           {
               $ren[]=$val['name'];
           }*/

          //  $rolepermis[$val['name']]=$ar;
        }

      return $ren;
    }

    /*
     *
     *     public function actionShenheren($lurou)
    {
        $manager=\Yii::$app->getAuthManager();

        //获取所有角色
        $roles=AuthItem::findAll(['type'=>1]);
        $rolepermis=[];
        //获取觉得所用的所有权限及路由
        foreach ($roles as $val)
        {

            $ar=$manager->getPermissionsByRole($val['name']);

            $rolepermis[$val['name']]=$ar;
        }
    }
     * */

}

