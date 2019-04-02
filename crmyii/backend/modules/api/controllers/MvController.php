<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/29
 * Time: 16:13
 */

namespace backend\modules\api\controllers;
use backend\models\Customerb;
use backend\models\JdCustomer;
use yii\db\Connection;
use yii\rest\ActiveController;
set_time_limit(0);
class MvController extends  ActiveController
{
    public $modelClass='backend\models\Holiday';

    //导入数据 旧CRM 迁移到 新CRM
    public function actionDatamv(){
        $db = new Connection([
            'dsn' => 'pgsql:host=rds455ekt1422z8sh7e2o.pg.rds.aliyuncs.com;port=3432;dbname=crm',
            'username' => 'rdspg',
            'password' => 'anmeng',
            'charset' => 'utf8',
        ]);


        $db2 = new Connection([
            'dsn' => 'pgsql:host=rds455ekt1422z8sh7e2o.pg.rds.aliyuncs.com;port=3432;dbname=jw-crm',
            'username' => 'rdspg',
            'password' => 'anmeng',
            'charset' => 'utf8',
        ]);
        /*
        $db2 = new Connection([
            'dsn' => 'pgsql:host=localhost;port=5432;dbname=crmdata',
            'username' => 'postgres',
            'password' => '123456',
            'charset' => 'utf8',
        ]);*/

        //迁移续费
        $xufei = $db->createCommand('SELECT * FROM jd_renew_huikuan where payment_type in(14,15)')->queryAll();

        foreach ($xufei as $key=>$val)
        {
            $audit_type='renew';
            if($val['payment_type']=='3')
            {
                $audit_type='bukuan';
            }
            if($val['payment_type']=='14')
            {
                $audit_type='refund_kehu';
            }
            if($val['payment_type']=='15')
            {
                $kh= $hetong = $db->createCommand('SELECT * FROM jd_contract where id='.$val['xf_contractid'])->queryOne();
                $mht=$hetong = $db->createCommand('SELECT * FROM jd_contract where id='.$kh['mht_id'])->queryOne();
                if($mht['state']=='1')
                {
                    $audit_type='refund_account_z';
                }elseif($mht['state']=='2' or $mht['state']=='3')
                {
                    $audit_type='refund_account_p';
                }
                //判断媒体合同状态

            }

            //从新库查审核时间如果有就记录
            $as=$db2->createCommand("select * from audit where yid=$val[id] and 'table'='$audit_type' and catated_at >1512489600")->queryOne();
            if($as <=0){

                //如果是退款到客户
                if($val['payment_type']=='14')
                {
                    //先删除之前的审核记录
                    $db2->createCommand()->delete('audit',"'table'='$audit_type' and yid=$val[id]")->execute();
                    //删除之前的退款记录
                    $db2->createCommand()->delete('jd_renew_huikuan',"id=$val[id]")->execute();
                    if($val['audit_3']!='0')
                    {
                        //设置审核当前审核状态
                        $val['audit']=$val['audit_3'];
                        $val['audit_count']=2;
                        $val['audit_u']=$val['susers3'];
                        //新增审核状态
                        $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                        $db2->createCommand()->insert('audit', ['type'=>$val['audit_3'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers3'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                    }else
                    {
                        //设置审核当前审核状态
                        $val['audit']=$val['audit_2'];
                        $val['audit_count']=1;
                        $val['audit_u']=$val['susers2'];
                        //新增审核状态
                        $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                    }
                    if($val['audit_2']=='1')
                    {
                        $val['is_ultimate_shenhe']=1;
                    }

                    unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['susers3'],$val['susers4'],$val['market1'],$val['audit_3'],$val['audit_4']);
                    $db2->createCommand()->insert('jd_renew_huikuan', $val)->execute();

                }

                //如果是退款到总账户
                if($val['payment_type']=='15')
                {
                    //先删除之前的审核记录
                    $db2->createCommand()->delete('audit',"\"table\"='$audit_type' and yid=$val[id]")->execute();
                    //删除之前的退款记录
                    $db2->createCommand()->delete('jd_renew_huikuan',"id=$val[id]")->execute();
                    if($val['audit_3']!='0')
                    {
                        //设置审核当前审核状态
                        $val['audit']=$val['audit_3'];
                        $val['audit_count']=2;
                        $val['audit_u']=$val['susers3'];
                        //新增审核状态
                        $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                        $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                    }elseif ($val['audit_1']!='0')
                    {
                        //设置审核当前审核状态
                        $val['audit']=$val['audit_1'];
                        $val['audit_count']=1;
                        $val['audit_u']=$val['susers1'];
                        //新增审核状态
                        $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                    }
                    if($val['audit_3']=='1')
                    {
                        $val['is_ultimate_shenhe']=1;
                    }

                    unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['susers3'],$val['susers4'],$val['market1'],$val['audit_3'],$val['audit_4']);
                    $db2->createCommand()->insert('jd_renew_huikuan', $val)->execute();
                }
            }


            /*



             */

        }

        /*


             //迁移客保

                    $kebao = $db->createCommand('SELECT * FROM jd_customerb')->queryAll();


                    foreach ($kebao as $key=>$val)
                    {

                        $db2->createCommand()->insert('customerb', $val)->execute();
                    }


             //var_dump($posts);

             //迁移客户

             $kehu = $db->createCommand('SELECT * FROM jd_customer ')->queryAll();

             foreach ($kehu as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_customer', $val)->execute();
             }










             //迁移合同
             $hetong = $db->createCommand('SELECT * FROM jd_contract ')->queryAll();

             foreach ($hetong as $key=>$val)
             {
                 //设置审核当前审核状态

                 if($val['audit_2']!='0')
                 {
                      $val['audit']=$val['audit_2'];
                     $val['audit_count']=2;
                     $val['audit_u']=$val['susers2'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'contract','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>'contract','users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                 }else
                 {
                     $val['audit']=$val['audit_1'];
                     $val['audit_count']=1;
                     $val['audit_u']=$val['susers1'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'contract','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                 }
                 if($val['audit_2']=='1' or $val['audit_2']=='3')
                 {
                     $val['is_ultimate_shenhe']=1;
                 }
                 //修改合同负责销售
                 $kh= $hetong = $db->createCommand('SELECT * FROM jd_customer where id='.$val['advertiser'])->queryOne();
                 $val['market']=$kh['submituser'];
                 unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['market1']);
                 $db2->createCommand()->insert('jd_contract', $val)->execute();
                 //新增审核状态

             }



             //迁移续费
             $xufei = $db->createCommand('SELECT * FROM jd_renew_huikuan ')->queryAll();

             foreach ($xufei as $key=>$val)
             {
                 $audit_type='renew';
                 if($val['payment_type']=='3')
                 {
                     $audit_type='bukuan';
                 }
                  if($val['payment_type']=='14')
                 {
                     $audit_type='refund_kehu';
                 }
                 if($val['payment_type']=='15')
                 {
                     $kh= $hetong = $db->createCommand('SELECT * FROM jd_contract where id='.$val['xf_contractid'])->queryOne();
                     $mht=$hetong = $db->createCommand('SELECT * FROM jd_contract where id='.$kh['mht_id'])->queryOne();
                     if($mht['state']=='1')
                     {
                         $audit_type='refund_account_z';
                     }elseif($mht['state']=='2' or $mht['state']=='3')
                     {
                         $audit_type='refund_account_p';
                     }
                     //判断媒体合同状态

                 }
                 //如果是续费
                 if($val['payment_type']=='1' or $val['payment_type']=='2' or $val['payment_type']=='3' or $val['payment_type']=='16' or $val['is_huikuan']=='1')
                 {

                     if($val['audit_2']!='0')
                     {
                         //设置审核当前审核状态
                         $val['audit']=$val['audit_2'];
                         $val['audit_count']=2;
                         $val['audit_u']=$val['susers2'];
                         //新增审核状态
                         $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                         $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                     }else
                     {
                         //设置审核当前审核状态
                         $val['audit']=$val['audit_1'];
                         $val['audit_count']=1;
                         $val['audit_u']=$val['susers1'];
                         //新增审核状态
                         $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                     }
                     if($val['audit_2']=='1')
                     {
                         $val['is_ultimate_shenhe']=1;
                     }

                     unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['susers3'],$val['susers4'],$val['market1'],$val['audit_3'],$val['audit_4']);
                     $db2->createCommand()->insert('jd_renew_huikuan', $val)->execute();
                 }

                 //如果是退款到客户
                 if($val['payment_type']=='14')
                 {

                     if($val['audit_3']!='0')
                     {
                         //设置审核当前审核状态
                         $val['audit']=$val['audit_3'];
                         $val['audit_count']=2;
                         $val['audit_u']=$val['susers3'];
                         //新增审核状态
                         $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                         $db2->createCommand()->insert('audit', ['type'=>$val['audit_3'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers3'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                     }else
                     {
                         //设置审核当前审核状态
                         $val['audit']=$val['audit_2'];
                         $val['audit_count']=1;
                         $val['audit_u']=$val['susers2'];
                         //新增审核状态
                         $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                     }
                     if($val['audit_2']=='1')
                     {
                         $val['is_ultimate_shenhe']=1;
                     }

                     unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['susers3'],$val['susers4'],$val['market1'],$val['audit_3'],$val['audit_4']);
                     $db2->createCommand()->insert('jd_renew_huikuan', $val)->execute();
                 }


                 //如果是退款到总账户
                 if($val['payment_type']=='15')
                 {

                     if($val['audit_3']!='0')
                     {
                         //设置审核当前审核状态
                         $val['audit']=$val['audit_3'];
                         $val['audit_count']=2;
                         $val['audit_u']=$val['susers3'];
                         //新增审核状态
                         $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                         $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                     }
                     if($val['audit_1']!='0')
                     {
                         //设置审核当前审核状态
                         $val['audit']=$val['audit_1'];
                         $val['audit_count']=1;
                         $val['audit_u']=$val['susers1'];
                         //新增审核状态
                         $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                     }
                     if($val['audit_3']=='1')
                     {
                         $val['is_ultimate_shenhe']=1;
                     }

                     unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['susers3'],$val['susers4'],$val['market1'],$val['audit_3'],$val['audit_4']);
                     $db2->createCommand()->insert('jd_renew_huikuan', $val)->execute();
                 }


             }


             //迁移账户

             $kehu = $db->createCommand('SELECT * FROM jd_account ')->queryAll();

             foreach ($kehu as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_account', $val)->execute();
             }


             //迁移公司回款
             $hetong = $db->createCommand('SELECT * FROM jd_back_money ')->queryAll();

             foreach ($hetong as $key=>$val)
             {
                 //设置审核当前审核状态

                 if($val['audit_2']!='0')
                 {
                     $val['audit']=$val['audit_2'];
                     $val['audit_count']=2;
                     $val['audit_u']=$val['susers2'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'backmoney','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>'backmoney','users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                 }else
                 {
                     $val['audit']=$val['audit_1'];
                     $val['audit_count']=1;
                     $val['audit_u']=$val['susers1'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'backmoney','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                 }
                 if($val['audit_2']=='1')
                 {
                     $val['is_ultimate_shenhe']=1;
                 }

                 unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['market1']);
                 $db2->createCommand()->insert('jd_back_money', $val)->execute();
                 //新增审核状态

             }

             //迁移消耗

             $account_consumption = $db->createCommand('SELECT * FROM jd_account_consumption ')->queryAll();

             foreach ($account_consumption as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_account_consumption', $val)->execute();
             }

             $account_cost = $db->createCommand('SELECT * FROM jd_account_cost ')->queryAll();

             foreach ($account_cost as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_account_cost', $val)->execute();
             }


             $account_users = $db->createCommand('SELECT * FROM jd_account_users ')->queryAll();

             foreach ($account_users as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_account_users', $val)->execute();
             }


             //迁移联系人
             $account_users = $db->createCommand('SELECT * FROM jd_contact_list ')->queryAll();

             foreach ($account_users as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_contact_list', $val)->execute();
             }

             //迁移合同关联表
             $account_users = $db->createCommand('SELECT * FROM jd_contract_relevance ')->queryAll();

             foreach ($account_users as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_contract_relevance', $val)->execute();
             }

             //迁移文件关联
             $account_users = $db->createCommand('SELECT * FROM jd_file ')->queryAll();

             foreach ($account_users as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_file', $val)->execute();
             }



             //迁移请假
             $hetong = $db->createCommand('SELECT * FROM jd_holiday ')->queryAll();

             foreach ($hetong as $key=>$val)
             {
                 //设置审核当前审核状态

                 if($val['audit_2']!='0')
                 {
                     $val['audit']=$val['audit_2'];
                     $val['audit_count']=2;
                     $val['audit_u']=$val['susers2'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'qingjia','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>'qingjia','users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                 }else
                 {
                     $val['audit']=$val['audit_1'];
                     $val['audit_count']=1;
                     $val['audit_u']=$val['susers1'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'qingjia','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                 }
                 if($val['audit_2']=='1')
                 {
                     $val['is_ultimate_shenhe']=1;
                 }

                 unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['market1']);
                 $db2->createCommand()->insert('jd_holiday', $val)->execute();
                 //新增审核状态

             }


             //迁移外出
             $hetong = $db->createCommand('SELECT * FROM jd_waichu ')->queryAll();

             foreach ($hetong as $key=>$val)
             {
                 //设置审核当前审核状态

                 if($val['audit_2']!='0')
                 {
                     $val['audit']=$val['audit_2'];
                     $val['audit_count']=2;
                     $val['audit_u']=$val['susers2'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'waichu','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>'waichu','users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                 }else
                 {
                     $val['audit']=$val['audit_1'];
                     $val['audit_count']=1;
                     $val['audit_u']=$val['susers1'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'waichu','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                 }
                 if($val['audit_2']=='1')
                 {
                     $val['is_ultimate_shenhe']=1;
                 }

                 unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['market1']);
                 $db2->createCommand()->insert('jd_waichu', $val)->execute();
                 //新增审核状态

             }


             //迁移发票
             $hetong = $db->createCommand('SELECT * FROM jd_invoice ')->queryAll();

             foreach ($hetong as $key=>$val)
             {
                 //设置审核当前审核状态

                 if($val['audit_2']!='0')
                 {
                     $val['audit']=$val['audit_2'];
                     $val['audit_count']=2;
                     $val['audit_u']=$val['susers2'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'invoice','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>'invoice','users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                 }else
                 {
                     $val['audit']=$val['audit_1'];
                     $val['audit_count']=1;
                     $val['audit_u']=$val['susers1'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'invoice','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                 }
                 if($val['audit_2']=='1')
                 {
                     $val['is_ultimate_shenhe']=1;
                 }

                 unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['market1']);
                 $db2->createCommand()->insert('jd_invoice', $val)->execute();
                 //新增审核状态

             }



             //迁移媒介收打款
             $hetong = $db->createCommand('SELECT * FROM jd_mback_money ')->queryAll();

             foreach ($hetong as $key=>$val)
             {
                 //设置审核当前审核状态
                 if($val['type']==1)
                 {
                     $audit_type='meijie-dakuan';
                 }
                 if($val['type']==2)
                 {
                     $audit_type='meijie-huikuan';
                 }
                 if($val['audit_2']!='0')
                 {
                     $val['audit']=$val['audit_2'];
                     $val['audit_count']=2;
                     $val['audit_u']=$val['susers2'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                 }else
                 {
                     $val['audit']=$val['audit_1'];
                     $val['audit_count']=1;
                     $val['audit_u']=$val['susers1'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                 }
                 if($val['audit_2']=='1')
                 {
                     $val['is_ultimate_shenhe']=1;
                 }

                 unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['market1']);
                 $db2->createCommand()->insert('jd_mback_money', $val)->execute();
                 //新增审核状态

             }

             //迁移媒介续费汇款
             $hetong = $db->createCommand('SELECT * FROM jd_mrenew_huikuan ')->queryAll();

             foreach ($hetong as $key=>$val)
             {


                     if($val['payment_type']=='3')
                     {
                         $audit_type='meijie-bukuan';
                     }
                     if($val['payment_type']=='17')
                     {
                         $audit_type='meijie-refund-money';
                     }

                     //如果是退款到客户
                     if($val['payment_type']=='17' or $val['payment_type']=='3')
                     {

                         if($val['audit_2']!='0')
                         {
                             //设置审核当前审核状态
                             $val['audit']=$val['audit_2'];
                             $val['audit_count']=2;
                             $val['audit_u']=$val['susers2'];
                             //新增审核状态
                             $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                             $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                         }else
                         {
                             //设置审核当前审核状态
                             $val['audit']=$val['audit_1'];
                             $val['audit_count']=1;
                             $val['audit_u']=$val['susers1'];
                             //新增审核状态
                             $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>$audit_type,'users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                         }
                         if($val['audit_2']=='1')
                         {
                             $val['is_ultimate_shenhe']=1;
                         }

                         unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['susers3'],$val['susers4'],$val['market1'],$val['audit_3'],$val['audit_4']);
                         $db2->createCommand()->insert('jd_mrenew_huikuan', $val)->execute();
                     }else
                     {
                         //不需要录入审核历史记录
                         if($val['audit_2']!='0')
                         {
                             //设置审核当前审核状态
                             $val['audit']=$val['audit_2'];
                             $val['audit_count']=2;
                             $val['audit_u']=0;
                         }else
                         {
                             //设置审核当前审核状态
                             $val['audit']=$val['audit_1'];
                             $val['audit_count']=1;
                             $val['audit_u']=0;

                         }
                         if($val['audit_2']=='1')
                         {
                             $val['is_ultimate_shenhe']=1;
                         }
                         unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['susers3'],$val['susers4'],$val['market1'],$val['audit_3'],$val['audit_4']);
                         $db2->createCommand()->insert('jd_mrenew_huikuan', $val)->execute();
                     }
             }

             //迁移媒介续费关联
             $account_consumption = $db->createCommand('SELECT * FROM jd_mrenew_relevance ')->queryAll();

             foreach ($account_consumption as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_mrenew_relevance', $val)->execute();
             }
             //已汇款续费媒介
             $account_consumption = $db->createCommand('SELECT * FROM jd_myihuikuanxufei ')->queryAll();

             foreach ($account_consumption as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_myihuikuanxufei', $val)->execute();
             }

             //产品线
             $account_consumption = $db->createCommand('SELECT * FROM jd_product_line ')->queryAll();

             foreach ($account_consumption as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_product_line', $val)->execute();
             }



             //给渠道打款
             $hetong = $db->createCommand('SELECT * FROM jd_qd_dakuan ')->queryAll();

             foreach ($hetong as $key=>$val)
             {
                 //设置审核当前审核状态

                 if($val['audit_2']!='0')
                 {
                     $val['audit']=$val['audit_2'];
                     $val['audit_count']=2;
                     $val['audit_u']=$val['susers2'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'qd_dakuan','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_2'],'yid'=>$val['id'],'table'=>'qd_dakuan','users'=>$val['susers2'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>2])->execute();

                 }else
                 {
                     $val['audit']=$val['audit_1'];
                     $val['audit_count']=1;
                     $val['audit_u']=$val['susers1'];
                     //新增审核状态
                     $db2->createCommand()->insert('audit', ['type'=>$val['audit_1'],'yid'=>$val['id'],'table'=>'qd_dakuan','users'=>$val['susers1'],'catated_at'=>$val['ctime'],'updated_at'=>$val['ctime'],'audit_count'=>1])->execute();

                 }
                 if($val['audit_2']=='1')
                 {
                     $val['is_ultimate_shenhe']=1;
                 }

                 unset($val['audit_1'],$val['audit_2'],$val['susers1'],$val['susers2'],$val['market1']);
                 $db2->createCommand()->insert('jd_qd_dakuan', $val)->execute();
                 //新增审核状态

             }


             //下单关联
             $account_consumption = $db->createCommand('SELECT * FROM jd_renew_order ')->queryAll();

             foreach ($account_consumption as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_renew_order', $val)->execute();
             }

             //已汇款续费
             $account_consumption = $db->createCommand('SELECT * FROM jd_yihuikuanxufei ')->queryAll();

             foreach ($account_consumption as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_yihuikuanxufei', $val)->execute();
             }

             //已汇款续费
             $account_consumption = $db->createCommand('SELECT * FROM jd_piaotype ')->queryAll();

             foreach ($account_consumption as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_piaotype', $val)->execute();
             }

             //已汇款续费
             $account_consumption = $db->createCommand('SELECT * FROM jd_agent_company ')->queryAll();

             foreach ($account_consumption as $key=>$val)
             {

                 $db2->createCommand()->insert('jd_agent_company', $val)->execute();
             }
             exit;
     */
    }


}