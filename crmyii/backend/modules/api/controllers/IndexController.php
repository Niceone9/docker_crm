<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/9
 * Time: 17:15
 */
namespace backend\modules\api\controllers;
use backend\models\AuditAction;
use backend\models\AuthAssignment;
use backend\models\BackMoney;
use backend\models\Beikuan;
use backend\models\ContractAccounts;
use backend\models\ContractNew;
use backend\models\Customerb;
use backend\models\Holiday;
use backend\models\Invoice;
use backend\models\JdContract;
use backend\models\MbackMoney;
use backend\models\MrenewHuikuan;
use backend\models\QdDakuan;
use backend\models\RenewHuikuan;
use backend\models\Uptouxiang;
use backend\models\UserBackend;
use backend\models\Waichu;
use backend\models\Zhuankuan;
use Codeception\PHPUnit\Constraint\Page;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\UploadedFile;

use backend\components\Quanxian;

class IndexController extends ActiveController
{
    public $modelClass='backend\modules\api\models\UserBackend';
    public $serializer=[
      'class'=>'yii\rest\Serializer',
      'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                ///HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],

        ];
        $behaviors['myBehavior']=[
            'class'=>Quanxian::className(),
        ];
        $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;

        return $behaviors;
    }


    //获取待办事项
    public function actionDaiban(){

        //获取用户拥有的访问function权限
        $rowes=\Yii::$app->hjd->get_roles();
        //查询公司需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'contract_new'])->one();
        //合同审核 api/contract-new/shenhe1
        $contract_daiban=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/contract-new/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=ContractNew::contract_list('All',"and contract_new.audit=1 and contract_new.audit_count=$i-1",'/api/contract-new/contractlist');
                    $row=$list['totalCount'];
                }else{
                    $list=ContractNew::contract_list('All',"and contract_new.audit=0 and contract_new.audit_count=$i",'/api/contract-new/contractlist');
                    $row=$list['totalCount'];

                }

                $contract_daiban+=$row;

            }
        }



        //查询协议需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'contract'])->one();
        //合同审核 api/contract-new/shenhe1
        $xieyi_daiban=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/contract/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=JdContract::contract_list('All',"and jd_contract.is_meijie=0 and  jd_contract.audit=1 and jd_contract.audit_count=$i-1",'/api/contract/contractlist');
                    $row=$list['totalCount'];

                }else{
                    $list=JdContract::contract_list('All',"and jd_contract.is_meijie=0 and jd_contract.audit=0 and jd_contract.audit_count=$i",'/api/contract/contractlist');
                    $row=$list['totalCount'];

                }

                $xieyi_daiban+=$row;

            }
        }





        //查询续费需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'renew'])->one();

        $renew_daiban=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/renew-huikuan/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=RenewHuikuan::xufei_list(''," and jd_renew_huikuan.payment_type in(1,2) and jd_renew_huikuan.audit=1 and jd_renew_huikuan.audit_count=$i-1",'/api/renew-huikuan/indexlist');
                    $row=$list['totalCount'];

                }else{
                    $list=RenewHuikuan::xufei_list(''," and jd_renew_huikuan.payment_type in(1,2) and jd_renew_huikuan.audit=0 and jd_renew_huikuan.audit_count=$i",'/api/renew-huikuan/indexlist');
                    $row=$list['totalCount'];
                }

                $renew_daiban+=$row;

            }
        }


        //查询回款需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'backmoney'])->one();

        $huikuan_daiban=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/back-money/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=BackMoney::back_money_list(''," and  jd_back_money.audit=1 and jd_back_money.audit_count=$i-1",'/api/back-money/list');
                    $row=$list['totalCount'];
                }else{
                    $list=BackMoney::back_money_list(''," and  jd_back_money.audit=0 and jd_back_money.audit_count=$i",'/api/back-money/list');
                    $row=$list['totalCount'];
                }

                $huikuan_daiban+=$row;

            }
        }



        //查询退款到客户需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'refund_kehu'])->one();

        $refund_kehu_daiban=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/refund-money/shenhe'.$i.'_tuikuan_kehu',$rowes))
            {
                if($i>1)
                {
                    $list=RenewHuikuan::tuikuan_list('','14',''," and jd_renew_huikuan.audit=1 and jd_renew_huikuan.audit_count=$i-1",'/api/refund-money/list_kehu');

                    $row=$list['totalCount'];
                }else{
                    $list=RenewHuikuan::tuikuan_list('','14',''," and jd_renew_huikuan.audit=0 and jd_renew_huikuan.audit_count=$i",'/api/refund-money/list_kehu');

                    $row=$list['totalCount'];

                }

                $refund_kehu_daiban+=$row;

            }
        }

        //查询退款到总账户拼框套壳需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'refund_account_p'])->one();

        $refund_account_p=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/refund-money/shenhe'.$i.'_tuikuan_account_p',$rowes))
            {
                if($i>1)
                {
                    $list=RenewHuikuan::tuikuan_list('','15',''," and mht.state in(1,2) and jd_renew_huikuan.audit=1 and jd_renew_huikuan.audit_count=$i-1",'/api/refund-money/list_zongzhnaghu');
                    $row=$list['totalCount'];

                }else{
                    $list=RenewHuikuan::tuikuan_list('','15',''," and mht.state in(1,2) and jd_renew_huikuan.audit=0 and jd_renew_huikuan.audit_count=$i",'/api/refund-money/list_zongzhnaghu');
                    $row=$list['totalCount'];
                }
                $refund_account_p+=$row;

            }
        }

        //查询退款到总账户直开需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'refund_account_z'])->one();

        $refund_account_z=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/refund-money/shenhe'.$i.'_tuikuan_account_z',$rowes))
            {
                if($i>1)
                {
                    $list=RenewHuikuan::tuikuan_list('','15',''," and mht.state in(1) and jd_renew_huikuan.audit=1 and jd_renew_huikuan.audit_count=$i-1",'/api/refund-money/list_zongzhnaghu');
                    $row=$list['totalCount'];
                }else{
                    $list=RenewHuikuan::tuikuan_list('','15',''," and mht.state in(1) and jd_renew_huikuan.audit=0 and jd_renew_huikuan.audit_count=$i",'/api/refund-money/list_zongzhnaghu');
                    $row=$list['totalCount'];
                }
                $refund_account_z+=$row;

            }
        }


        //查询补款需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'bukuan'])->one();

        $bukuan_daiban=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/bukuan/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=RenewHuikuan::bukuan_list('',"and jd_renew_huikuan.payment_type=3 and jd_renew_huikuan.audit=1 and jd_renew_huikuan.audit_count=$i-1",'/api/bukuan/list');
                    $row=$list['totalCount'];
                }else{
                    $list=RenewHuikuan::bukuan_list('',"and jd_renew_huikuan.payment_type=3 and jd_renew_huikuan.audit=0 and jd_renew_huikuan.audit_count=$i",'/api/bukuan/list');
                    $row=$list['totalCount'];
                }

                $bukuan_daiban+=$row;

            }
        }

        //查询媒介打款需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'meijie-dakuan'])->one();

        $meijie_dakuan=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/mback-money/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=MbackMoney::dakuan_list("and jd_mback_money.type=1 and jd_mback_money.audit=1 and jd_mback_money.audit_count=$i-1",'/api/mback-money/list');
                    $row=$list['totalCount'];
                }else{
                    $list=MbackMoney::dakuan_list("and jd_mback_money.type=1 and jd_mback_money.audit=0 and jd_mback_money.audit_count=$i",'/api/mback-money/list');
                    $row=$list['totalCount'];
                }


                $meijie_dakuan+=$row;

            }
        }


        //查询媒介回款需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'meijie-huikuan'])->one();

        $meijie_huikuan=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/meijie-huikuan/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=MbackMoney::back_money_list("and jd_mback_money.type=2 and jd_mback_money.audit=1 and jd_mback_money.audit_count=$i-1",'/api/meijie-huikuan/list');
                    $row=$list['totalCount'];
                }else{
                    $list=MbackMoney::back_money_list("and jd_mback_money.type=2 and jd_mback_money.audit=0 and jd_mback_money.audit_count=$i",'/api/meijie-huikuan/list');
                    $row=$list['totalCount'];
                }


                $meijie_huikuan+=$row;

            }
        }


        //查询媒介补款需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'meijie-bukuan'])->one();

        $meijie_bukuan=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/meijie-bukuan/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=Beikuan::bukuan_list("and beikuan.audit=1 and beikuan.audit_count=$i-1",'/api/meijie-bukuan/list');
                    $row=$list['totalCount'];
                }else{
                    $list=Beikuan::bukuan_list("and beikuan.audit=0 and beikuan.audit_count=$i",'/api/meijie-bukuan/list');
                    $row=$list['totalCount'];

                }


                $meijie_bukuan+=$row;

            }
        }



        //查询客保需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'kebao'])->one();

        $kebao=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/customerb/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=Customerb::market_list('','and customerb.audit=1 and customerb.audit_count=$i-1','/api/customerb/market_kebao_list');
                    $row=$list['totalCount'];
                }else{
                    $list=Customerb::market_list('','and customerb.audit=0 and customerb.audit_count=$i','/api/customerb/market_kebao_list');
                    $row=$list['totalCount'];
                }


                $kebao+=$row;

            }
        }


        //查询外出需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'waichu'])->one();

        $waichu=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/waichu/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=Waichu::waichu_list(" and jd_waichu.audit=1 and jd_waichu.audit_count=$i-1",'/api/waichu/list');
                    $row=$list['totalCount'];
                }else{
                    $list=Waichu::waichu_list(" and jd_waichu.audit=0 and jd_waichu.audit_count=$i",'/api/waichu/list');
                    $row=$list['totalCount'];
                }


                $waichu+=$row;

            }
        }

        //查询请假需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'qingjia'])->one();

        $qingjia=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/qingjia/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=Holiday::qingjia_list("and jd_holiday.audit=1 and jd_holiday.audit_count=$i-1",'/api/qingjia/list');
                    $row=$list['totalCount'];
                }else{
                    $list=Holiday::qingjia_list("and jd_holiday.audit=0 and jd_holiday.audit_count=$i",'/api/qingjia/list');
                    $row=$list['totalCount'];
                }
                $qingjia+=$row;

            }
        }

        //查询请假需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'qd_dakuan'])->one();

        $qddakuan=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/qd-dakuan/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=QdDakuan::qd_dakuan_list("and jd_qd_dakuan.audit=1 and jd_qd_dakuan.audit_count=$i-1",'/api/qd-dakuan/list');
                    $row=$list['totalCount'];
                }else{
                    $list=QdDakuan::qd_dakuan_list("and jd_qd_dakuan.audit=0 and jd_qd_dakuan.audit_count=$i",'/api/qd-dakuan/list');
                    $row=$list['totalCount'];
                }
                $qddakuan+=$row;

            }
        }

        //查询发票需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'Invoice'])->one();

        $fapiao=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/invoice/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=Invoice::fp_list("and jd_invoice.audit=1 and jd_invoice.audit_count=$i-1",'/api/invoice/list');
                    $row=$list['totalCount'];
                }else{
                    $list=Invoice::fp_list("and jd_invoice.audit=0 and jd_invoice.audit_count=$i",'/api/invoice/list');
                    $row=$list['totalCount'];
                }
                $fapiao+=$row;

            }
        }

        //查询转款需要几级审核
        //查询发票需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'zhuankuan'])->one();

        $zhuankuan=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/zhuankuan/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=Zhuankuan::zhuankuan_list("and zhuankuan.audit=1 and zhuankuan.audit_count=$i-1",'/api/zhuankuan/list');
                    $row=$list['totalCount'];
                }else{
                    $list=Zhuankuan::zhuankuan_list("and zhuankuan.audit=0 and zhuankuan.audit_count=$i",'/api/zhuankuan/list');
                    $row=$list['totalCount'];
                }
                $zhuankuan+=$row;



            }
        }

        //查询媒介合同需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'meijie_contract'])->one();

        $meijiecontract=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/meijie-contract/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=JdContract::meijie_contract_list('All'," and jd_contract.is_meijie=1 and  jd_contract.audit=1 and jd_contract.audit_count=$i-1",'/api/meijie-contract/contractlist');
                    $row=$list['totalCount'];
                }else{
                    $list=JdContract::meijie_contract_list('All'," and jd_contract.is_meijie=1 and  jd_contract.audit=0 and jd_contract.audit_count=$i",'/api/meijie-contract/contractlist');
                    $row=$list['totalCount'];
                }
                $meijiecontract+=$row;
            }
        }

        //查询备款需要几级审核

        //查询媒介合同需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'beikuan'])->one();

        $beikuan=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/beikuan/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=Beikuan::beikuan_list(" and   beikuan.audit=1 and beikuan.audit_count=$i-1",'/api/beikuan/list');
                    $row=$list['totalCount'];
                }else{
                    $list=Beikuan::beikuan_list(" and   beikuan.audit=0 and beikuan.audit_count=$i",'/api/beikuan/list');
                    $row=$list['totalCount'];
                }
                $beikuan+=$row;
            }
        }

        //下单审核
        $shenhe=AuditAction::find()->where(['action_name'=>'xiadan'])->one();

        $xiadan_daiban=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/renew-huikuan/xiadan-shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=RenewHuikuan::xufei_list(''," and jd_renew_huikuan.payment_type in(1,2) and jd_renew_huikuan.audit=1 and jd_renew_huikuan.audit_count=$i-1",'/api/renew-huikuan/indexlist','1');
                    $row=$list['totalCount'];

                }else{
                    $list=RenewHuikuan::xufei_list(''," and jd_renew_huikuan.payment_type in(1,2) and jd_renew_huikuan.audit=0 and jd_renew_huikuan.audit_count=$i",'/api/renew-huikuan/indexlist','1');
                    $row=$list['totalCount'];
                }

                $xiadan_daiban+=$row;

            }
        }

        //媒介退款
        $shenhe=AuditAction::find()->where(['action_name'=>'meijie_tuikuan'])->one();

        $meijie_tuikuan=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/meijie-tuikuan/shenhe'.$i,$rowes))
            {

                /*
                if($i>1)
                {
                    $list=Zhuankuan::zhuankuan_list("and zhuankuan.audit=1 and zhuankuan.audit_count=$i-1",'/api/zhuankuan/list');
                    $row=$list['totalCount'];
                }else{
                    $list=Zhuankuan::zhuankuan_list("and zhuankuan.audit=0 and zhuankuan.audit_count=$i",'/api/zhuankuan/list');
                    $row=$list['totalCount'];
                }*/

                if($i>1)
                {
                    $list=MrenewHuikuan::meijietuikuan_list('',"and a.audit=1 and a.audit_count=$i-1",'/api/meijie-tuikuan/indexlist');
                    $row=$list['totalCount'];
                }else{
                    $list=MrenewHuikuan::meijietuikuan_list('',"and a.audit=0 and a.audit_count=$i",'/api/meijie-tuikuan/indexlist');
                    $row=$list['totalCount'];
                }
                $meijie_tuikuan+=$row;

            }
        }

        //结算单审核
        $shenhe=AuditAction::find()->where(['action_name'=>'contract_accounts'])->one();
        $accounts_daiban=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/contract-accounts/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=ContractAccounts::contract_list('All',"  and contract_accounts.audit=1 and contract_accounts.audit_count=$i-1",'/api/contract-accounts/contractlist');
                    $row=$list['totalCount'];

                }else{
                    $list=ContractAccounts::contract_list('All',"  and contract_accounts.audit=0 and contract_accounts.audit_count=$i",'/api/contract-accounts/contractlist');
                    $row=$list['totalCount'];
                }

                $accounts_daiban+=$row;

            }
        }


        //罚款单审核
        $shenhe=AuditAction::find()->where(['action_name'=>'meijie_fakuan'])->one();
        $fakuan_daiban=0;
        for ($i=1;$i<=$shenhe['audit_count'];$i++){
            if(in_array('/api/meijie-fakuan/shenhe'.$i,$rowes))
            {
                if($i>1)
                {
                    $list=MrenewHuikuan::fakuan_list("and jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.audit_count=$i-1",'/api/meijie-fakuan/list');
                    $row=$list['totalCount'];

                }else{
                    $list=MrenewHuikuan::fakuan_list("and jd_mrenew_huikuan.audit=0 and jd_mrenew_huikuan.audit_count=$i",'/api/meijie-fakuan/list');
                    $row=$list['totalCount'];
                }

                $fakuan_daiban+=$row;

            }
        }

        $data['contract']=$contract_daiban;
        $data['xieyi_daiban']=$xieyi_daiban;
        $data['renew_daiban']=$renew_daiban;
        $data['huikuan_daiban']=$huikuan_daiban;
        $data['refund_kehu_daiban']=$refund_kehu_daiban;
        $data['refund_account_p']=$refund_account_p;
        $data['refund_account_z']=$refund_account_z;
        $data['bukuan_daiban']=$bukuan_daiban;
        $data['meijie_dakuan']=$meijie_dakuan;
        $data['meijie_huikuan']=$meijie_huikuan;
        $data['meijie_bukuan']=$meijie_bukuan;
        $data['kebao']=$kebao;
        $data['waichu']=$waichu;
        $data['qingjia']=$qingjia;
        $data['qddakuan']=$qddakuan;
        $data['fapiao']=$fapiao;
        $data['zhuankuan']=$zhuankuan;
        $data['meijiecontract']=$meijiecontract;
        $data['beikuan']=$beikuan;
        $data['xiadan']=$xiadan_daiban;
        $data['meijie_tuikuan']=$meijie_tuikuan;
        $data['contract_accpunts']=$accounts_daiban;
        $data['meijie_fakuan']=$fakuan_daiban;
        $sum=0;
        foreach ($data as $k=>$v)
        {
            $sum+=$v;
        }

        return array('daiban'=>$data,'sum'=>$sum);

    }




}