<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/11/22
 * Time: 9:44
 */

namespace backend\modules\api\controllers;


use backend\models\Account;
use backend\models\AccountCost;
use backend\models\AccountCostReal;
use backend\models\ArticleClass;
use backend\models\Audit;
use backend\models\AuditAction;
use backend\models\AuthItem;
use backend\models\BackMoney;
use backend\models\Beikuan;
use backend\models\BeikuanAccount;
use backend\models\BeikuanAccountToRenew;
use backend\models\BeikuanRenewDy;
use backend\models\ContractNew;
use backend\models\ContractRelevance;
use backend\models\CustomerAdvertiser;
use backend\models\CustomerAdvertiserReleveance;
use backend\models\Customerb;
use backend\models\Holiday;
use backend\models\HuikuanPlan;
use backend\models\Invoice;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\MbackMoney;
use backend\models\MrenewHuikuan;
use backend\models\Myihuikuanxufei;
use backend\models\NianjiaLog;
use backend\models\QdDakuan;
use backend\models\RenewHuikuan;
use backend\models\Waichu;
use backend\models\Yihuikuanxufei;
use backend\models\Zhuankuan;
use backend\modules\api\models\UserBackend;
use common\models\User;
use yii\db\Connection;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

ini_set('max_execution_time', '0');
set_time_limit(0);


class LinuxTimeController extends  ActiveController
{
   public $modelClass='backend\models\Holiday';
   //检查合同状态
   public function actionContract_isend(){
       $time=time();
       $contract=JdContract::updateAll(['iszuofei'=>2],"contract_end<$time and iszuofei=0 and is_ultimate_shenhe=1 and contract_end!=0");
       \Yii::warning('本次执行结束合同'.$contract.'条',__METHOD__);
       return array('code'=>'200','msg'=>'本次执行结束合同'.$contract.'条');
   }
    //修改客保客户状态
   public function actionCustomer_isend(){
        $list=Customerb::find()->where(['ytime'=>0])->all();

        $cont=0;
        foreach ($list as $key=>$val)
        {
            if($val['is_xu']=='1')
            {
                $dqdate=strtotime(date("Y-m-d",$val['r_time'])." +180 day");
            }else{
                $dqdate=strtotime(date("Y-m-d",$val['r_time'])." +90 day");
            }

            if($dqdate<time())
            {
               $a= Customerb::updateAll(['is_claim'=>'1'],['id'=>$val['id']]);
                \Yii::warning('修改客保公司id'.$val['id'].'为可认领状态',__METHOD__);
                $cont++;
            }
        }

       return array('code'=>'200','msg'=>'修改到期客户'.$cont.'条');
   }

   //修改合同到期状态
    public function actionContract_state_up(){
        $list=JdContract::find()->select('id')->where("is_meijie=0 and isxufei=0")->all();
        foreach ($list as $key =>$value)
        {
            $this->contract_isstate($value['id']);
        }
        return array('code'=>'200','msg'=>'修改老客状态客户执行成功');
    }

    public function contract_isstate($contract_id){
        $hhhinfo=JdContract::findOne($contract_id);

        if($hhhinfo['contract_state']=='2' or $hhhinfo['contract_state']=='4')
        {
            return true;
        }
        //$hetong=M("RenewHuikuan");
        $count=0;
        //如果不是分配的客户就检查是否要改变合同状态
        if($hhhinfo['contract_state']!='4')
        {
                //合同基本信息
                $contractinfo=ContractRelevance::find()
                    ->select('b.three')
                    ->join('a left join','jd_product_line b','a.product_line=b.id')
                    ->where(['a.contract_id'=>$hhhinfo['id']])
                    ->asArray()
                    ->one();

                //查看续费的产品线是否满一年如果满一年就把所有此产品线的合同状态就改为2（老客户）l
                $contract_time=RenewHuikuan::find()
                    ->select('a.*')
                    ->join('a left join','jd_contract_relevance b','a.xf_contractid=b.contract_id')
                    ->leftJoin('jd_product_line c','c.id=b.product_line')
                    ->where(["c.three"=>$contractinfo[three],"a.advertiser"=>$hhhinfo['advertiser'],"a.payment_type"=>array(1,2)])
                    ->orderBy("a.payment_time asc")
                    ->one();

                $a=date("Y-m-d",$contract_time['payment_time']);//此产品线第一笔续费的开始时间
                $b=date("Y-m-d");
                if(strtotime($b)>strtotime($a."+1 year") && $contract_time!='')
                {

                    //修改合同为老客状态
                    JdContract::updateAll(['contract_state'=>'2'],['id'=>$hhhinfo['id']]);
                    //修改该合同下续费为老客状态
                    RenewHuikuan::updateAll(['ht_state'=>'2'],['xf_contractid'=>$hhhinfo['id'],'payment_type'=>array(1,2,14,15)]);
                    \Yii::warning('修改合同id'.$hhhinfo['id'].'为老客状态',__METHOD__);
                    $count++;

                }

        }

        return $count;

    }


    //年假更新
    public function actionNianjia()
    {
        $userslist = UserBackend::find()->where('is_delete != 1')->all();
        //$userslist = UserBackend::find()->where('is_delete != 1 and id=355')->all();

        foreach ($userslist as $key => $users) {

            //首先先算我是不是正式员工

            if ($users['njuptime'] > time()) {
                return false;
            }

            if(time() <$users['intime'])
            {
                 continue;
            }

            $m = date("m");

            if ($users['istrue'] == 1) {

                //算我的工龄
                $a = date("Y-m-d", $users['jobtime']);//工作时间
                $c = date("Y-m-d", $users['intime']);//入职时间
                $b = date("Y-m-d");

                //入职是否满一年
                if (strtotime($a . "+1 year") < strtotime($b)) {
                    $ca3 = date_diff(date_create($c), date_create($b));//入职时间和现在时间差值
                    $nianjia =  5 + $ca3->y;//多一年加一天


                    //echo $gongling."<br>";
                    $nianjiazhouqi = 12 / round($nianjia, 1);//年假周期  12个月 除以 工龄

                    //1.5/2=0.75  1/0.75


                    $nianjiazhouqi = $nianjiazhouqi / 2; //0.5天的周期

                    //$nianjia=$m/round($nianjiazhouqi,1);
                    $nianjia = $m / $nianjiazhouqi; //实际年假天数等于 当前月除以年假半天周期




                    $nianjia = floor($nianjia);
                    //上次年假


                    $snianjia = $this->actionSnianjia($users['id'],date("m", $users['njuptime']));




                    if($m=='01'){
                        $snianjia=0;
                    }


                    $nianjiazjts=0;

                   // 2-

                    if ($nianjia - $snianjia > 0) {
                        $nianjiazjts = ($nianjia - $snianjia) * 0.5;

                    }



                    /*
                    echo '本次年假'.$nianjia;
                    echo '--<br>';
                    echo '上次年假'.$snianjia;
                    echo '<br>';
                    echo '增加天数'.$nianjiazjts;
                    exit;
                    */
                    //$User = M("Users"); // 实例化User对象
                    if ($nianjiazjts > 0) {
                        // $User->where("id=$uid")->setInc('nianjia',$nianjiazjts); // 年假加0.5
                        UserBackend::updateAllCounters(['nianjia' => $nianjiazjts], ['id' => $users['id']]);
                        $nianjialog= new NianjiaLog();
                        $nianjialog->uid=$users['id'];
                        $nianjialog->note='修改'.$users['name'].'用户id'.$users['id'].'年假+'.$nianjiazjts.'天';
                        $nianjialog->time=date('Y-m-d H:i:s');
                        $nianjialog->save();
                        //\Yii::warning('修改用户id'.$users['id'].'年假+'.$nianjiazjts.'天',__METHOD__);
                    }

                    UserBackend::updateAll(['njuptime' => time()], ['id' => $users['id']]);
                    $md = date("m-d");

                    /*
                    if ($md == '01-01') {

                        $b = strtotime(date("Y-m-d") . " -1 years");

                        $yjjj = floor(1 / $nianjiazhouqi) * 0.5;//1月应有年假

                        //算出去年应有年假
                        if (strtotime($a . "+1 year") < $b) {
                            $ca3 = date_diff(date_create($c), date_create(date("Y-m-d", $b)));//入职时间和现在时间差值

                            $nianjia = 5 + $ca3->y;

                            if ($nianjia < $users['nianjia']) {

                                UserBackend::updateAll(['nianjia' => $nianjia + $yjjj], ['id' => $users['id']]);
                            } else {

                                UserBackend::updateAll(['nianjia' => $users['nianjia'] + $yjjj], ['id' => $users['id']]);
                            }

                        }

                        UserBackend::updateAll(['njuptime' => time()], ['id' => $users['id']]);
                    }
                    */
                } else {
                    //入职时间不满一年
                    $nianjia = 0;

                }

            }
        }
        return array('code'=>'200','msg'=>'年假更新执行成功');
    }

    public function actionSnianjia($id,$m){

        $users = UserBackend::findOne($id);
        //算我的工龄
        $a = date("Y-m-d", $users['jobtime']);//工作时间
        $c = date("Y-m-d", $users['intime']);//入职时间
        $b = date("Y-m-d");
        $ca3 = date_diff(date_create($c), date_create($b));//入职时间和现在时间差值
        $nianjia =  5 + $ca3->y;//多一年加一天


        //echo $gongling."<br>";
        $nianjiazhouqi = 12 / round($nianjia, 1);//年假周期  12个月 除以 工龄
        //1.5/2=0.75  1/0.75


        $nianjiazhouqi = $nianjiazhouqi / 2; //0.5天的周期

        //$nianjia=$m/round($nianjiazhouqi,1);
        $nianjia = $m / $nianjiazhouqi; //实际年假天数等于 当前月除以年假半天周期





        $nianjia = floor($nianjia);

        //如果当前月是入职月则返回0


        return $nianjia;
   }


    //续费驳回
    public function actionBohui_renew($id){

        //查这笔续费
        $renewinfo=RenewHuikuan::findOne($id);
        //账户信息
        $accountinfo=Account::findOne($renewinfo->account);

        //普通续费驳回
        if($renewinfo['audit']=='2')
        {
            return array('code'=>'500','msg'=>'程序已终止，原因：这笔续费已经驳回了,请勿进行这样的操作。');
        }

        //如果不是续费就报错！
        if($renewinfo['payment_type'] !='1' and  $renewinfo['payment_type'] !='2')
        {
            return array('code'=>'500','msg'=>'程序已终止，原因：这不是一笔续费,擦亮眼睛，请勿进行这样的操作。');
        }
        
        //1.将续费改为驳回状态
        //2.将续费欠额改为金额
        RenewHuikuan::updateAll(['audit'=>'2','is_ultimate_shenhe'=>'0','xf_qiane'=>$renewinfo['money'],'last_time'=>date('Y-m-d').' 00:00:00'],['id'=>$id]);

        //3.将公司续费和合同续费减少
        JdCustomer::updateAllCounters(['yu_e'=>-$renewinfo['money']],['id'=>$renewinfo['advertiser']]);//更新公司续费值
        JdContract::updateAllCounters(['yu_e'=>-$renewinfo['money']],['id'=>$renewinfo['xf_contractid']]);//更新合同续费值
        //4.根据续费类型读出已续费回款进行回滚操作
        if($renewinfo['payment_type']==3)
        {
            $yihuikuanxufei=Yihuikuanxufei::find()->where(['hk_id'=>$id])->all();
            foreach ($yihuikuanxufei as $key=>$val)
            {
                //回款回滚

                RenewHuikuan::updateAllCounters(['backmoney_yue'=>$val['money']],['id'=>$val['hk_id']]);
                //续费回滚

                RenewHuikuan::updateAllCounters(['xf_qiane'=>$val['money']],['id'=>$val['xf_id']]);
                //删除已回款续费记录
                RenewHuikuan::deleteAll(['id'=>$val['id']]);
                //M("Yihuikuanxufei")->delete($val[id]);
            }
        }else
        {

            $yihuikuanxufei=Yihuikuanxufei::find()->where(['xf_id'=>$id])->all();
            foreach ($yihuikuanxufei as $key=>$val)
            {
                //回款回滚

                RenewHuikuan::updateAllCounters(['backmoney_yue'=>$val['money']],['id'=>$val['hk_id']]);
                //续费回滚

                RenewHuikuan::updateAll(['xf_qiane'=>$renewinfo['money']],['id'=>$val['xf_id']]);
                //删除已回款续费记录

                Yihuikuanxufei::deleteAll(['id'=>$val['id']]);
            }


            //判断回款是否与续费一一对应 如果是 则删除此比分配款 is_tk_deduction
            $tk_deduction=RenewHuikuan::find()->where(['relevance_id'=>$id,'is_huikuan'=>'1','is_tk_deduction'=>'1'])->one();
            if($tk_deduction)
            {
                //合同回款减少
                JdContract::updateAllCounters(['huikuan'=>-$tk_deduction['money']],['id'=>$tk_deduction['xf_contractid']]);
                //公司未分配余额减少
                JdCustomer::updateAllCounters(['tuikuan_undistributed_yu_e'=>$tk_deduction['money']],['id'=>$tk_deduction['advertiser']]);
                self::delete($tk_deduction['id']);
            }
        }
        //重跑媒介回款对应关系
        RenewHuikuan::renew_huikuan($renewinfo->xf_contractid);




//媒介处理
//1.删除媒介续费
        //$mrennew=M("MrenewHuikuan");
//查出续费生成的媒介续费记录
        $mrenewinfo=MrenewHuikuan::find()->where("xf_id=$id")->one();
//获取媒体合同信息
        $meiinfo=JdContract::findOne($accountinfo->mht_id);
//3.将媒介公司续费和合同续费减少
       // $update1=$advertisers->where("id=$meiinfo[mht][advertiser]")->setDec('myu_e', $renewinfo['xf_cost']);//更新公司续费值
       // $update2=$contract->where("id=$meiinfo[mht][id]")->setDec('yu_e', $renewinfo['xf_cost']);//更新合同续费值

        //2018年5月8日17:44:42  因为更新了备款功能所以续费驳回功能需要改动 o(╥﹏╥)o
        //查询是否是关联了备款的续费 如果是就不走媒介驳回的步骤了
        $is_beikuan=BeikuanAccountToRenew::find()->where(['xf_id'=>$mrenewinfo['id']])->count();
        if($is_beikuan==0)
        {

            JdCustomer::updateAllCounters(['myu_e'=>-$renewinfo['xf_cost']],['id'=>$meiinfo['advertiser']]);//更新公司续费值
            JdContract::updateAllCounters(['yu_e'=>-$renewinfo['xf_cost']],['id'=>$meiinfo['id']]);//更新合同续费值
            $myihuikuanxufei=Myihuikuanxufei::find()->where("xf_id=$mrenewinfo[id]")->all();
            foreach ($myihuikuanxufei as $key=>$val)
            {
                //回款回滚
                MrenewHuikuan::updateAllCounters(['dakuan_yue'=>$val['money']],['id'=>$val['hk_id']]);
                //续费回滚
                MrenewHuikuan::updateAll(['xf_qiane'=>$mrenewinfo['xf_cost']],['id'=>$val['xf_id']]);
                //删除已回款续费记录
                Myihuikuanxufei::deleteAll(['id'=>$val['id']]);
            }
            //处理消耗 不会
            MrenewHuikuan::deleteAll(['id'=>$mrenewinfo['id']]);
        }else
        {
            JdCustomer::updateAllCounters(['myu_e'=>-$renewinfo['xf_cost']],['id'=>$meiinfo['advertiser']]);//更新公司续费值
            JdContract::updateAllCounters(['yu_e'=>-$renewinfo['xf_cost']],['id'=>$meiinfo['id']]);//更新合同续费值
            //媒介公司和合同的备款余额减
            JdCustomer::updateAllCounters(['beikuan_yue'=>$renewinfo['xf_cost']],['id'=>$meiinfo['advertiser']]);//更新公司备款值
            JdContract::updateAllCounters(['beikuan_yue'=>$renewinfo['xf_cost']],['id'=>$meiinfo['id']]);//更新合同备款值
            //查询备款账户-与此续费关联的备款账户
            $beikuan_account=BeikuanAccountToRenew::find()->where(['xf_id'=>$mrenewinfo['id']])->one();


            BeikuanAccount::updateAllCounters(['yu_e'=>$renewinfo['xf_cost']],['id'=>$beikuan_account['bk_account_id']]);//更新备款账户余额


            //续费欠额驳回
            //MrenewHuikuan::updateAll(['xf_qiane'=>$mrenewinfo['xf_cost']],['id'=>$mrenewinfo['id']]);
//
//            $myihuikuanxufei=BeikuanRenewDy::find()->where("xf_id=$mrenewinfo[id]")->all();
//            foreach ($myihuikuanxufei as $key=>$val)
//            {
//                //回款回滚
//                Beikuan::updateAllCounters(['dakuan_yue'=>$val['money']],['id'=>$val['hk_id']]);
//                //续费回滚
//                MrenewHuikuan::updateAll(['xf_qiane'=>$mrenewinfo['xf_cost']],['id'=>$val['xf_id']]);
//                //删除已回款续费记录
//                BeikuanRenewDy::deleteAll(['id'=>$val['id']]);
//            }
            //处理消耗 不会
            MrenewHuikuan::deleteAll(['id'=>$mrenewinfo['id']]);
            //删除账户对应续费关系
            BeikuanAccountToRenew::deleteAll(['xf_id'=>$mrenewinfo['id']]);
        }

        return array('code'=>'200','msg'=>'驳回续费成功');
    }

    public function actionLs($id){
        //查这笔续费
        $renewinfo=RenewHuikuan::findOne($id);
        //账户信息
        $accountinfo=Account::findOne($renewinfo->account);
        $mrenewinfo=MrenewHuikuan::find()->where("xf_id=$id")->one();
//获取媒体合同信息
        $meiinfo=JdContract::findOne($accountinfo->mht_id);
//3.将媒介公司续费和合同续费减少
        // $update1=$advertisers->where("id=$meiinfo[mht][advertiser]")->setDec('myu_e', $renewinfo['xf_cost']);//更新公司续费值
        // $update2=$contract->where("id=$meiinfo[mht][id]")->setDec('yu_e', $renewinfo['xf_cost']);//更新合同续费值

        //2018年5月8日17:44:42  因为更新了备款功能所以续费驳回功能需要改动 o(╥﹏╥)o
        //查询是否是关联了备款的续费 如果是就不走媒介驳回的步骤了
        $is_beikuan=BeikuanAccountToRenew::find()->where(['xf_id'=>$mrenewinfo['id']])->count();
        if($is_beikuan==0)
        {

            JdCustomer::updateAllCounters(['myu_e'=>-$renewinfo['xf_cost']],['id'=>$meiinfo['advertiser']]);//更新公司续费值
            JdContract::updateAllCounters(['yu_e'=>-$renewinfo['xf_cost']],['id'=>$meiinfo['id']]);//更新合同续费值
            $myihuikuanxufei=Myihuikuanxufei::find()->where("xf_id=$mrenewinfo[id]")->all();
            foreach ($myihuikuanxufei as $key=>$val)
            {
                //回款回滚
                MrenewHuikuan::updateAllCounters(['dakuan_yue'=>$val['money']],['id'=>$val['hk_id']]);
                //续费回滚
                MrenewHuikuan::updateAll(['xf_qiane'=>$mrenewinfo['xf_cost']],['id'=>$val['xf_id']]);
                //删除已回款续费记录
                Myihuikuanxufei::deleteAll(['id'=>$val['id']]);
            }
            //处理消耗 不会
            MrenewHuikuan::deleteAll(['id'=>$mrenewinfo['id']]);
        }else
        {
            JdCustomer::updateAllCounters(['myu_e'=>-$renewinfo['xf_cost']],['id'=>$meiinfo['advertiser']]);//更新公司续费值
            JdContract::updateAllCounters(['yu_e'=>-$renewinfo['xf_cost']],['id'=>$meiinfo['id']]);//更新合同续费值
            //媒介公司和合同的备款余额减
            JdCustomer::updateAllCounters(['beikuan_yue'=>$renewinfo['xf_cost']],['id'=>$meiinfo['advertiser']]);//更新公司备款值
            JdContract::updateAllCounters(['beikuan_yue'=>$renewinfo['xf_cost']],['id'=>$meiinfo['id']]);//更新合同备款值
            //查询备款账户-与此续费关联的备款账户
            $beikuan_account=BeikuanAccountToRenew::find()->where(['xf_id'=>$mrenewinfo['id']])->one();


            BeikuanAccount::updateAllCounters(['yu_e'=>$renewinfo['xf_cost']],['id'=>$beikuan_account['bk_account_id']]);//更新备款账户余额


            //续费欠额驳回
            //MrenewHuikuan::updateAll(['xf_qiane'=>$mrenewinfo['xf_cost']],['id'=>$mrenewinfo['id']]);
//
//            $myihuikuanxufei=BeikuanRenewDy::find()->where("xf_id=$mrenewinfo[id]")->all();
//            foreach ($myihuikuanxufei as $key=>$val)
//            {
//                //回款回滚
//                Beikuan::updateAllCounters(['dakuan_yue'=>$val['money']],['id'=>$val['hk_id']]);
//                //续费回滚
//                MrenewHuikuan::updateAll(['xf_qiane'=>$mrenewinfo['xf_cost']],['id'=>$val['xf_id']]);
//                //删除已回款续费记录
//                BeikuanRenewDy::deleteAll(['id'=>$val['id']]);
//            }
            //处理消耗 不会
            MrenewHuikuan::deleteAll(['id'=>$mrenewinfo['id']]);
            //删除账户对应续费关系
            BeikuanAccountToRenew::deleteAll(['xf_id'=>$mrenewinfo['id']]);
        }
    }

    //退款驳回

    /**
     * @param $id
     * @return array
     */
    public function actionBohui_tuikuanaccount($id){

        //查这笔退款
        $renewinfo=RenewHuikuan::findOne($id);
        //账户信息
        $accountinfo=Account::findOne($renewinfo->account);
        //普通续费驳回
        if($renewinfo['audit']=='2')
        {
            return array('code'=>'500','msg'=>'程序已终止，原因：这笔退款已经驳回了,请勿进行这样的操作。');
        }

        //1.将退款改为驳回状态
        //2.将退款余额改为金额
        RenewHuikuan::updateAll(['audit'=>'2','is_ultimate_shenhe'=>'0','backmoney_yue'=>$renewinfo['money'],'last_time'=>date('Y-m-d').' 00:00:00'],['id'=>$id]);

        //3.将公司续费和合同续费增加
        JdCustomer::updateAllCounters(['yu_e'=>$renewinfo['money']],['id'=>$renewinfo['advertiser']]);//更新公司续费值
        JdContract::updateAllCounters(['yu_e'=>$renewinfo['money']],['id'=>$renewinfo['xf_contractid']]);//更新合同续费值
        //4.根据退款类型读出已续费回款进行回滚操作
        if($renewinfo['payment_type']==15)
        {
            $yihuikuanxufei=Yihuikuanxufei::find()->where(['hk_id'=>$id])->all();
            foreach ($yihuikuanxufei as $key=>$val)
            {
                //回款回滚
                RenewHuikuan::updateAllCounters(['backmoney_yue'=>$val['money']],['id'=>$val['hk_id']]);
                //续费回滚
                RenewHuikuan::updateAllCounters(['xf_qiane'=>$val['money']],['id'=>$val['xf_id']]);
                //删除已回款续费记录
                RenewHuikuan::deleteAll(['id'=>$val['id']]);
                //M("Yihuikuanxufei")->delete($val[id]);
            }




            //判断退款是否与转出退款一一对应 如果是 则删除此比转出退款 is_tk_deduction
            $tk_deduction=self::find()->where(['id'=>$renewinfo->relevance_id,'payment_type'=>'20'])->one();
            if($tk_deduction)
            {
                //合同回款减少
                JdContract::updateAllCounters(['huikuan'=>$tk_deduction['money']],['id'=>$tk_deduction['xf_contractid']]);
                //公司未分配余额减少
                JdCustomer::updateAllCounters(['tuikuan_undistributed_yu_e'=>-$tk_deduction['money']],['id'=>$tk_deduction['advertiser']]);
                RenewHuikuan::delete($tk_deduction['id']);
            }


        }
        //重跑媒介回款对应关系
        RenewHuikuan::renew_huikuan($renewinfo->xf_contractid);

//媒介处理
//1.删除媒介续费
        //$mrennew=M("MrenewHuikuan");
//查出续费生成的媒介续费记录
        $mrenewinfo=MrenewHuikuan::find()->where("xf_id=$id")->one();
//获取媒体合同信息
        $meiinfo=JdContract::findOne($accountinfo->mht_id);
//3.将媒介公司续费和合同续费减少
        // $update1=$advertisers->where("id=$meiinfo[mht][advertiser]")->setDec('myu_e', $renewinfo['xf_cost']);//更新公司续费值
        // $update2=$contract->where("id=$meiinfo[mht][id]")->setDec('yu_e', $renewinfo['xf_cost']);//更新合同续费值


        JdCustomer::updateAllCounters(['myu_e'=>$renewinfo['xf_cost']],['id'=>$meiinfo['advertiser']]);//更新公司续费值
        JdContract::updateAllCounters(['yu_e'=>$renewinfo['xf_cost']],['id'=>$meiinfo['id']]);//更新合同续费值

        $myihuikuanxufei=Myihuikuanxufei::find()->where("hk_id=$mrenewinfo[id]")->all();
        foreach ($myihuikuanxufei as $key=>$val)
        {
           // MrenewHuikuan::updateAllCounters(['dakuan_yue'=>$val['money']],['id'=>$val['hk_id']]);
            //续费回滚
            MrenewHuikuan::updateAllCounters(['xf_qiane'=>$val['money']],['id'=>$val['xf_id']]);
            //回款回滚
            MrenewHuikuan::updateAll(['dakuan_yue'=>$mrenewinfo['xf_cost']],['id'=>$val['hk_id']]);
            //删除已回款续费记录

            Myihuikuanxufei::deleteAll(['id'=>$val['id']]);


        }

        //处理消耗 不会

        //如果勾选了备款账户
        $beikuaninfo=Beikuan::find()->where(['xf_id'=>$mrenewinfo->id])->asArray()->all();
        foreach ($beikuaninfo as $key=>$val)
        {
            BeikuanAccount::updateAllCounters(['yu_e'=>-$val['xf_cost']],['id'=>$val['account']]);
            Beikuan::deleteAll(['id'=>$val['id']]);
        }




        MrenewHuikuan::deleteAll(['id'=>$mrenewinfo['id']]);
        return array('code'=>'200','msg'=>'驳回退款成功');
    }


    //媒介分配款驳回
    //媒体分配款回滚
    public function  actionMeijie_fp_back($fpk_id){
        $advertisers = M("Customer");
        $contract = M("Contract");
        //查询此比分配款
        $mrennew=M("MrenewHuikuan");
        $meiinfo=MrenewHuikuan::findOne($fpk_id);
        //3.将媒介公司的未分配打款增加和合同分配打款减少


        JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e'=>$meiinfo['money']],['id'=>$meiinfo['advertiser']]);//增加公司未分配余额
        JdContract::updateAllCounters(['dakuan'=>-$meiinfo['money']],['id'=>$meiinfo['mht_id']]);//减少合同打款值


        $myihuikuanxufei=Myihuikuanxufei::find()->where("hk_id=$fpk_id")->all();
        foreach ($myihuikuanxufei as $key=>$val)
        {



            //回款值回滚
            MrenewHuikuan::updateAllCounters(['dakuan_yue'=>$val['money']],['id'=>$val['hk_id']]);
            //续费欠额增加
            MrenewHuikuan::updateAll(['xf_qiane'=>$val['xf_cost']],['id'=>$val['xf_id']]);
            //删除已回款续费记录

            Myihuikuanxufei::deleteAll(['id'=>$val['id']]);

        }
        //删除分配打款

        MrenewHuikuan::deleteAll(['id'=>$meiinfo['id']]);
        return array('code'=>'200','msg'=>'驳回媒介分配款成功');
    }




    //补款回滚
    public function  actionBukuan_back($fpk_id){


        $info=RenewHuikuan::findOne($fpk_id);
        //3.将媒介公司的未分配打款增加和合同分配打款减少


        JdCustomer::updateAllCounters(['bukuan'=>-$info['money']],['id'=>$info['advertiser']]);//减少公司补款值
        JdContract::updateAllCounters(['bukuan'=>-$info['money']],['id'=>$info['xf_contractid']]);//减少合同补款值


        $yihuikuanxufei=Yihuikuanxufei::find()->where("hk_id=$fpk_id")->all();
        foreach ($yihuikuanxufei as $key=>$val)
        {



            //回款值回滚
            RenewHuikuan::updateAllCounters(['backmoney_yue'=>$val['money'],'audit'=>2,'is_ultimate_shenhe'=>0],['id'=>$val['hk_id']]);
            //续费欠额增加
            RenewHuikuan::updateAll(['xf_qiane'=>$val['money']],['id'=>$val['xf_id']]);
            //删除已回款续费记录

            Yihuikuanxufei::deleteAll(['id'=>$val['id']]);

        }


        //RenewHuikuan::deleteAll(['id'=>$meiinfo['id']]);
        return array('code'=>'200','msg'=>'驳回客户补款成功');
    }



    //分配款回滚
    public function  actionHuikuan_back($fpk_id){


        $info=RenewHuikuan::findOne($fpk_id);
        //3.将媒介公司的未分配打款增加和合同分配打款减少


        JdCustomer::updateAllCounters(['undistributed_yu_e'=>$info['money']],['id'=>$info['advertiser']]);//增加公司未分配值
        JdContract::updateAllCounters(['huikuan'=>-$info['money']],['id'=>$info['xf_contractid']]);//减少合同回款值


        $yihuikuanxufei=Yihuikuanxufei::find()->where("hk_id=$fpk_id")->all();
        foreach ($yihuikuanxufei as $key=>$val)
        {
            //回款值回滚
            RenewHuikuan::updateAllCounters(['backmoney_yue'=>$val['money'],'audit'=>2,'is_ultimate_shenhe'=>0],['id'=>$val['hk_id']]);
            //续费欠额增加
            RenewHuikuan::updateAll(['xf_qiane'=>$val['money']],['id'=>$val['xf_id']]);
            //删除已回款续费记录
            Yihuikuanxufei::deleteAll(['id'=>$val['id']]);
        }

        //删除此比回款
       // RenewHuikuan::deleteAll(['id'=>$fpk_id]);





        //RenewHuikuan::deleteAll(['id'=>$meiinfo['id']]);
        return array('code'=>'200','msg'=>'驳回客户回款成功');
    }



    //根据appid 读取销售名称
    public function actionAppid_to_markey($appid){
        return \backend\models\UserBackend::appid_to_markey($appid);
    }


    public function actionTuikuan(){
        $list=JdContract::find()->where(['is_meijie'=>1])->asArray()->all();
        foreach ($list as $key=>$val)
        {
            $yue= $val['yu_e'];
            $xyue=$this->actionXyuea($val['id']);
            if( abs($yue-$xyue)>2){
                echo $val['id'].',';
           }
        }
        exit;
    }

    public function actionXyue($id){
       // $huikuan=RenewHuikuan::find()->where("is_huikuan=1 and xf_contractid=$id  and is_ultimate_shenhe=1")->sum('money');
       // $tuikuan_k=RenewHuikuan::find()->where("payment_type in(14) and xf_contractid=$id  and is_ultimate_shenhe=1")->sum('money');
       // $bukuan=RenewHuikuan::find()->where("payment_type in(3) and xf_contractid=$id  and is_ultimate_shenhe=1")->sum('money');
        $xufei=RenewHuikuan::find()->where("payment_type in(1,2) and xf_contractid=$id  and is_ultimate_shenhe=1")->sum('money');
        $tuikuan_z=RenewHuikuan::find()->where("payment_type in(15) and xf_contractid=$id  and is_ultimate_shenhe=1")->sum('money');
        if(empty($xufei)){$xufei=0;}
        if(empty($tuikuan_z)){$tuikuan_z=0;}
       $mo=($xufei-$tuikuan_z);
        return $mo;
    }

    public function actionXyuea($id){
        // $huikuan=RenewHuikuan::find()->where("is_huikuan=1 and xf_contractid=$id  and is_ultimate_shenhe=1")->sum('money');
        // $tuikuan_k=RenewHuikuan::find()->where("payment_type in(14) and xf_contractid=$id  and is_ultimate_shenhe=1")->sum('money');
        // $bukuan=RenewHuikuan::find()->where("payment_type in(3) and xf_contractid=$id  and is_ultimate_shenhe=1")->sum('money');
        $xufei=MrenewHuikuan::find()->where("payment_type in(1,2) and mht_id=$id  and is_ultimate_shenhe=1")->sum('xf_cost');
        $tuikuan_z=MrenewHuikuan::find()->where("payment_type in(15) and mht_id=$id  and is_ultimate_shenhe=1")->sum('xf_cost');
        if(empty($xufei)){$xufei=0;}
        if(empty($tuikuan_z)){$tuikuan_z=0;}
        $mo=($xufei-$tuikuan_z);
        return $mo;
    }


    public function actionMcontrctup(){
        $mcontractlist=JdContract::find()->where(['is_meijie'=>1])->asArray()->all();
        foreach ($mcontractlist as $key=>$val)
        {
            if(($val['yu_e']-$this->actionXyuea($val[id]))>1)
            {
                echo $val['id'].',';
            }
        }

    }

    //媒介合同 公司续费值 更正
    public function actionMyueup(){
       /*
        *1.更改合同的续费值
        *2.更改合同的续费值
        *3.把公司下所有合同所有款余额重置
        *4.重跑媒介公司下所有合同的回款续费对应
        */
        $mcontractlist=JdContract::find()->where("id in(1292509,1321877,1262,2022601,1999,1265183,1300294)")->asArray()->all();

       foreach ($mcontractlist as $key=>$value)
       {
           $xufei=MrenewHuikuan::find()->where("payment_type in(1,2) and mht_id=$value[id]  and is_ultimate_shenhe=1")->sum('xf_cost');
           $tuikuan_z=MrenewHuikuan::find()->where("payment_type in(15) and mht_id=$value[id]  and is_ultimate_shenhe=1")->sum('xf_cost');
           if(empty($xufei)){$xufei=0;}
           if(empty($tuikuan_z)){$tuikuan_z=0;}
           //设置合同续费值
           JdContract::updateAll(['yu_e'=>$xufei-$tuikuan_z],['id'=>$value[id]]);
           //设置公司id
           $avid[]=$value['advertiser'];
       }



       $adlist=JdCustomer::find()->where(['id'=>$avid])->asArray()->all();

       foreach ($adlist as $k=>$v)
       {
           //更新媒介公司的总续费
           $xufei=JdContract::find()->where(['advertiser'=>$v['id']])->sum('yu_e');

           JdCustomer::updateAll(['myu_e'=>$xufei],['id'=>$v['id']]);

       }



    }

    public function actionLinshi(){

exit;

        $audit=1;

            $renew_info=RenewHuikuan::findOne(1326364);//退款详情



            //账户信息
            $account_info=Account::findOne($renew_info['account']);

            //如果最终审核通过


                //查询媒介退款需要几审
                $meijietuishenhe=AuditAction::find()->where(['action_name'=>'meijie_tuikuan'])->one();




                $data=new MrenewHuikuan();
                $data->advertiser=$renew_info['advertiser'];
                $data->submituser=$renew_info['submituser'];
                $data->type=$renew_info['type'];
                $data->xf_contractid=$renew_info['xf_contractid'];
                $data->market=$renew_info['market'];
                $data->account=$renew_info['account'];
                $data->appname=$renew_info['appname'];
                //金额改为百度币
                $data->money=$renew_info['show_money'];
                //获取媒体合同信息

                $meiinfo=JdContract::findOne($account_info->mht_id);

                //获取媒介返点算出实付金额
                //$fandian=($meiinfo['rebates_proportion']+100)/100; //媒体返点

                $data->dakuan_yue=$renew_info['xf_cost']; //退款余额
                $data->xf_cost=$renew_info['xf_cost']; //续费实付
                $data->rebates_proportion=$meiinfo['rebates_proportion'];//媒介合同返点
                $data->mht_id=$meiinfo['id'];//媒介合同id

                $data->show_money=$renew_info['show_money'];
                $data->payment_type=15;
                $data->payment_time=$renew_info['payment_time'];
                $data->note='退款审核通过 由系统自动生成的退款记录. 操作人：crm管理员~';
                $data->contract_start=$renew_info['contract_start'];
                $data->contract_end=$renew_info['contract_end'];
                $data->ctime=time();
                $data->users2=$renew_info['users2'];
                $data->audit=1;//1级默认审核通过
                $data->audit_count=$meijietuishenhe->audit_count;;//1级默认审核通过
                $data->audit_u=$renew_info['audit_u'];
                $data->is_ultimate_shenhe=1;
                $data->xf_id=$renew_info['id'];
                //保存媒介信息
                if($data->save())
                {
                    //修改媒介合同和媒介公司续费值
                    MrenewHuikuan::money_change($meiinfo['advertiser'],$meiinfo['id'],1,$renew_info['xf_cost'],'jian');
                    //重跑媒介续费对应
                    //--MrenewHuikuan::mrenew_huikuan($meiinfo['mht']['id']);
                    //退款对应消耗
                    //self::Tuikuan_cost_real($id);
                }else
                {
                    return false;
                }

                //修改公司续费值-减少
               // self::money_change_jian($renew_info['advertiser'],$renew_info['xf_contractid'],15,$renew_info['money']);
                //--self::renew_huikuan($renew_info['xf_contractid']);

        //读取转款
        $zhuankuanfinfo=Zhuankuan::find()->where(['tk_id'=>$renew_info['id']])->one();


        $mtuiinfo=MrenewHuikuan::find()->where(['xf_id'=>$zhuankuanfinfo['tk_id']])->one();

        $mxuinfo= MrenewHuikuan::find()->where(['xf_id'=>$zhuankuanfinfo['xf_id']])->one();
        MrenewHuikuan::updateAll(['shishifu_onid'=>$mxuinfo['id']],['id'=>$mtuiinfo['id']]);




        exit;
        $ar=array(
            1300045,1299759,1299997,1299668,1299559,1299304,1299302
);
        foreach ($ar  as $k=>$v)
        {

            $tr1 = \Yii::$app->db->beginTransaction();
            try {
                $this->actionBohui_renew($v);

                $tr1->commit(); //提交数据

            } catch (Exception $e) {
                //回滚
                $tr1->rollBack();
                return $e->getMessage(); //返回自定义异常信息
            }

            echo '第'.$k.'执行成功.<br>';


        }


    }


    public function actionTable_audit_count_update_one($table,$audit_type){
       //检查功能需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>$audit_type])->one();
        if(!$renew_shenhe)
        {
            return array('code'=>500,'msg'=>'没有找到这个功能');
        }
        //修改驳回的数据 并且小于审核层数的
        $db=\Yii::$app->db->createCommand()->update($table,['audit_count'=>$renew_shenhe['audit_count']],"audit =2 and audit_count>{$renew_shenhe[audit_count]}")->execute();
        //修改驳回的数据 并且大于审核层数的
        $db=\Yii::$app->db->createCommand()->update($table,['audit_count'=>$renew_shenhe['audit_count']],"audit =1 and is_ultimate_shenhe=1 and audit_count>{$renew_shenhe[audit_count]}")->execute();

    }

    public function actionRefund_z(){
       $xufei=RenewHuikuan::find()->where("payment_type in(14,15)")->asArray()->all();

       foreach ($xufei as $key=>$val)
       {
           if($val['payment_type']=='14')
           {
               $audit_type='refund_kehu';
           }
           if($val['payment_type']=='15')
           {
               $kh= \Yii::$app->db->createCommand('SELECT * FROM jd_contract where id='.$val['xf_contractid'])->queryOne();
               $mht= \Yii::$app->db->createCommand('SELECT * FROM jd_contract where id='.$kh['mht_id'])->queryOne();
               if($mht['state']=='1')
               {
                   $audit_type='refund_account_z';
               }elseif($mht['state']=='2' or $mht['state']=='3')
               {
                   $audit_type='refund_account_p';
               }
               //判断媒体合同状态
               $renew_shenhe=AuditAction::find()->where(['action_name'=>$audit_type])->one();

           }
       }


    }


    public function actionContractstartre(){
       $contract_list=JdContract::find()->where(['is_meijie'=>'0'])->asArray()->all();

       foreach ($contract_list as $key=>$value)
       {

           //$hetong=M("RenewHuikuan");
           $count=0;
           //如果不是分配的客户就检查是否要改变合同状态
           if($value['contract_state']!='4')
           {
               //合同基本信息
               $contractinfo=ContractRelevance::find()
                   ->select('b.three')
                   ->join('a left join','jd_product_line b','a.product_line=b.id')
                   ->where(['a.contract_id'=>$value['id']])
                   ->asArray()
                   ->one();

               //查看续费的产品线是否满一年如果不满一年就把所有此产品线的合同状态就改为1（新客户） 或 老客新开
               $contract_time=RenewHuikuan::find()
                   ->select('a.*')
                   ->join('a left join','jd_contract_relevance b','a.xf_contractid=b.contract_id')
                   ->leftJoin('jd_product_line c','c.id=b.product_line')
                   ->where(["c.three"=>$contractinfo[three],"a.advertiser"=>$value['advertiser'],"a.payment_type"=>array(1,2)])
                   ->orderBy("a.payment_time asc")
                   ->one();

               $a=date("Y-m-d",$contract_time['payment_time']);//此产品线第一笔续费的开始时间
               $b=date("Y-m-d");
               if(strtotime($b)<strtotime($a."+1 year") && $contract_time!='')
               {

                   //修改合同为老客新媒状态
                   JdContract::updateAll(['contract_state'=>'3'],['id'=>$value['id']]);
                   //把第一个合同 改成新客
                   JdContract::updateAll(['contract_state'=>'1'],['id'=>$contract_time['xf_contractid']]);
                   //修改该合同下续费为老客新媒状态
                   RenewHuikuan::updateAll(['ht_state'=>'3'],['xf_contractid'=>$value['id'],'payment_type'=>array(1,2,14,15)]);


                   \Yii::warning('修改合同id'.$value['id'].'为老客新媒状态',__METHOD__);
                   $count++;

               }

           }
       }
   }


    /*
    public function actionContractcopy(){
        $list=JdContract::find()->with('rencontract')->where(['is_meijie'=>0,'audit'=>1,'audit_count'=>2])->asArray()->all();
       foreach ($list as $key=>$val)
       {

       }

        exit;
    }*/

    //消耗对应续费
    public static function actionAccount_coust_toren(){
        $list=AccountCost::find()->select(['id'])->where("left_cost>0 ")->asArray()->all();

        $logfile=\Yii::getAlias('@app').'/runtime/logs/hjd.log';
       // error_log(PHP_EOL.date("Y-m-d H:i:s").'  aaaa'.PHP_EOL,3,$logfile);
       // var_dump(is_file($logfile));exit;
      //

        foreach ($list as $key=>$value)
        {

            error_log(PHP_EOL.date("Y-m-d H:i:s").'  消耗对应续费 消耗id:'.$value['id'].'...('.$key.'/'.count($list).')'.PHP_EOL,3,$logfile);
            self::actionAccount_cost_real($value['id']);
        }
        error_log(PHP_EOL.date("Y-m-d H:i:s").'  消耗对应续费完成'.PHP_EOL,3,$logfile);
        return array('code'=>'200','msg'=>'消耗对应成功');
    }

    //账户对应的真实消耗 合同id 账户名称
    public static function actionAccount_cost_real($id){
        $costinfo=AccountCost::findOne($id);

        $account=$costinfo->account_name;
        $avid=$costinfo->av_id;
        $date=$costinfo->date;
        $date=strtotime($date);
        //查询公司下的账户
        //查询是否有关联账户
        $Guanlian=Account::find()
            ->select("id,a_users,alias")
            ->where(['avid'=>$avid,'a_users'=>$account])
            ->andWhere("alias!=''")
            ->asArray()
            ->one();
        //如果有关联账户
        if($Guanlian!='')
        {
            $account_list=Account::find()->select("id,a_users,alias")->where(['avid'=>$avid,'alias'=>$Guanlian['alias']])->asArray()->all();
        }else{
            $account_list=Account::find()->select("id,a_users,alias")->where(['avid'=>$avid,'a_users'=>$account])->asArray()->all();
        }

        $account_id_list=ArrayHelper::getColumn($account_list,'id');

        //查询账户相关没有对应过的续费
        $renew_list=RenewHuikuan::find()
            ->where(['account'=>$account_id_list,'payment_type'=>array(1,2),'is_ultimate_shenhe'=>1])
            ->andWhere(['>','no_cost_xf',0])
            ->orderBy(['payment_time'=>'asc'])
            ->asArray()
            ->all();

        if(count($renew_list)<1)
        {
            return ;
        }

        foreach ($renew_list as $key=>$value)
        {
            $costinfo=AccountCost::findOne($id);
            //如果续费未消耗百度币 大于 消耗 （余额大于续费金额）停止循环并返回续费欠额为0
            if($value['no_cost_xf']-$costinfo['left_cost']>0) {
                //设置续费剩余消耗=续费剩余消耗-消耗
                RenewHuikuan::updateAll(['no_cost_xf'=>$value['no_cost_xf']-$costinfo['left_cost']],['id'=>$value['id']]);

                //查询之前有没有同合同同账户同一天的数据如果有就执行修改 如果没有就执行新增
                $accoust_cost_real=AccountCostReal::find()->where(['date'=>$costinfo->date,'a_user'=>$costinfo->account_name,'contract_id'=>$value['xf_contractid'],'istuikuan'=>0])->one();

                if($accoust_cost_real['id'])
                {
                    $accoust_cost_real->cost=$accoust_cost_real->cost+$costinfo->left_cost;
                    $accoust_cost_real->no_back_cost=$accoust_cost_real->no_back_cost+$costinfo->left_cost;

                }else
                {
                    //增加真实消耗记录
                    $accoust_cost_real= new AccountCostReal();
                    $accoust_cost_real->date=$costinfo->date;
                    $accoust_cost_real->a_user=$costinfo->account_name;
                    $accoust_cost_real->cost=$costinfo->left_cost;
                    $accoust_cost_real->no_back_cost=$costinfo->left_cost;
                    $accoust_cost_real->istuikuan=0;
                    $accoust_cost_real->contract_id=$value['xf_contractid'];
                }



                if(!$accoust_cost_real->save())
                {
                    var_dump($accoust_cost_real->errors);
                    exit;
                }
                //修改消耗剩下的费用为0
                AccountCost::updateAll(['left_cost'=>0],['id'=>$id]);
                return ;
            }else{
                //如果续费未消耗百度币 小于 消耗  （此回款余额不足以抹平此续费）则继续执行循环 并把此回款的余额为0
                //查询之前有没有同合同同账户同一天的数据如果有就执行修改 如果没有就执行新增
                $accoust_cost_real=AccountCostReal::find()->where(['date'=>$costinfo->date,'a_user'=>$costinfo->account_name,'contract_id'=>$value['xf_contractid'],'istuikuan'=>0])->one();

                if($accoust_cost_real['id'])
                {
                    $accoust_cost_real->cost=$accoust_cost_real->cost+$costinfo->left_cost;
                    $accoust_cost_real->no_back_cost=$accoust_cost_real->no_back_cost+$costinfo->left_cost;

                }else {
                    //增加真实消耗记录
                    $accoust_cost_real = new AccountCostReal();
                    $accoust_cost_real->date = $costinfo->date;
                    $accoust_cost_real->a_user = $costinfo->account_name;
                    $accoust_cost_real->cost = $value['no_cost_xf'];
                    $accoust_cost_real->no_back_cost = $value['no_cost_xf'];
                    $accoust_cost_real->contract_id = $value['xf_contractid'];
                }

                if(!$accoust_cost_real->save())
                {
                    var_dump($accoust_cost_real->errors);
                    exit;
                }
                /*
                $yixufeihuikuan_date->hk_id=$value['id'];
                //谁大取谁
                if ($value['payment_time'] >= $xufei_info['payment_time']) {
                    $yixufeihuikuan_date->time = $value['payment_time'];
                } else
                {
                    $yixufeihuikuan_date->time = $xufei_info['payment_time'];
                }

                $yixufeihuikuan_date->money=$value['dakuan_yue'];
                $yixufeihuikuan_date->shifu_money=$value['dakuan_yue'];
                $yixufeihuikuan_date->save();
                */
                //设置回款余额
                RenewHuikuan::updateAll(['no_cost_xf'=>0],['id'=>$value[id]]);
                //修改续费欠额
                AccountCost::updateAll(['left_cost'=>$costinfo['left_cost']-$value['no_cost_xf']],['id'=>$id]);
            }
        }

        return array('code'=>'200','msg'=>'success');


    }

    //退款对应消耗
    public function actionTuikuantocost(){
        $list=RenewHuikuan::find()
            ->select('id')
            ->where(['payment_type'=>15,'is_ultimate_shenhe'=>1])
            ->asArray()
            ->all();
        foreach ($list as $key=>$value)
        {
            RenewHuikuan::Tuikuan_cost_real($value);
        }
        return array('code'=>'200','msg'=>'success');
    }








    //每隔十分钟跑一次续费回款对应
    public function actionDuiying(){
        //查询十一分钟内产生的分配款和续费
        //跑客户续费对应关系
        $date=strtotime(date("Y-m-d H:i:s").'-11 minutes');
//        $last_time=date("Y-m-d H:i:s",$date);
//        $renewhuikuan=RenewHuikuan::find()
//            ->select('xf_contractid')
//            ->where(['>=','last_time',$last_time])
//            ->andWhere(['is_ultimate_shenhe'=>1])
//            ->groupBy('xf_contractid')
//            ->all();

        $sql='select * from (
select * from  (
select xf_contractid,sum(xf_qiane) xf from jd_renew_huikuan where payment_type in(1,2,14,16,31,20) and is_ultimate_shenhe=1 and audit=1 GROUP BY xf_contractid) a left join (
select xf_contractid,sum(backmoney_yue) hk from jd_renew_huikuan where (payment_type in(3,15,311) or is_huikuan=1) and is_ultimate_shenhe=1 and audit=1 GROUP BY xf_contractid) b on a.xf_contractid=b.xf_contractid ) t2 where xf>0 and hk>0';
        $renewhuikuan=\Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($renewhuikuan as $key=>$val)
        {
            RenewHuikuan::renew_huikuan($val['xf_contractid']);
        }


        //跑媒介续费对应关系
        $renewhuikuan=MrenewHuikuan::find()
            ->select('mht_id')
            ->where(['>=','ctime',$date])
            ->groupBy('mht_id')
            ->all();

        foreach ($renewhuikuan as $key=>$val)
        {
            MrenewHuikuan::mrenew_huikuan($val);
            //Beikuan::beikuan_renew($val);
        }

        return array('code'=>'200','msg'=>'对应关系跑完成');
    }

    //每隔一天重跑续费回款对应
    public function actionRenewday(){
        //\Yii::beginProfile('myBenchmark');
        
        //error_log('续费重跑开始',3,__DIR__.'/runtime/logs/hjd.log');
        RenewHuikuan::renew_huikuan(); //客户重跑

        //error_log('续费重跑结束',3,__DIR__.'/runtime/logs/hjd.log');
        //\Yii::endProfile('myBenchmark');
        MrenewHuikuan::mrenew_huikuan();//媒介重跑
       // Beikuan::beikuan_renew();//重跑备款

        return array('code'=>'200','msg'=>'对应关系跑完成');

    }

    //单独跑某一个媒介合同对应关系
    public function actionMrenewcontdy($id){
        RenewHuikuan::renew_huikuan($id); //客户重跑
        //MrenewHuikuan::mrenew_huikuan();//媒介重跑
    }


    //更新账户的总续费和总消耗  pay 是续费  cost 消耗
    public function actionAccountupcost(){
        $sql='
        select a.xf_contractid,a.a_users,a.pay,b.pay as tui,(a.pay-b.pay) as xufe from (
select b.a_users,a.xf_contractid,sum(show_money) as pay from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.is_ultimate_shenhe=1 and a.payment_type in(1,2)  group by b.a_users,a.xf_contractid
) a,
(
select b.a_users,a.xf_contractid,sum(show_money) as pay from jd_renew_huikuan a left join jd_account b on a.account=b.id where a.is_ultimate_shenhe=1 and a.payment_type in(15)  group by b.a_users,a.xf_contractid
) b
where a.a_users=b.a_users and a.xf_contractid=b.xf_contractid 
        ';

        $list=\Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($list as $key=>$value)
        {
            Account::updateAll(['total_pay'=>$value['xufe']],['a_users'=>$value['a_users'],'contract_id'=>$value['xf_contractid']]);
        }


        $list2=AccountCostReal::find()->select(['a_user','contract_id','sum(cost) as cost'])->where(['istuikuan'=>0])->groupBy('a_user,contract_id')->all();

        foreach ($list2 as $key=>$value)
        {

            Account::updateAll(['total_cost'=>$value['cost']],['a_users'=>$value['a_user'],'contract_id'=>$value['contract_id']]);
        }
        return array('code'=>'200','msg'=>'账户续费 消耗值更新成功');
    }

    public function actionZhouxiaohao(){

    }



    //年假更新
    public function actionNianjiajisuan()
    {
        $userslist = UserBackend::find()->where('is_delete != 1 and id=100035')->all();

        foreach ($userslist as $key => $users) {

            //首先先算我是不是正式员工
            /*
            if ($users['njuptime'] > time()) {
                return false;
            }*/

            $m = date("4");

            if ($users['istrue'] == 1) {

                //算我的工龄
                $a = date("Y-m-d", $users['jobtime']);//工作时间
                $c = date("Y-m-d", $users['intime']);//入职时间
                //$b = date("Y-m-d");
                $b = date("2017-12-31");

                //入职是否满一年
                if (strtotime($a . "+1 year") < strtotime($b)) {
                    $ca3 = date_diff(date_create($c), date_create($b));//入职时间和现在时间差值
                    $nianjia = $nianjia + 5 + $ca3->y;//多一年加一天


                    //echo $gongling."<br>";
                    $nianjiazhouqi = 12 / round($nianjia, 1);//年假周期  12个月 除以 工龄


                    $nianjiazhouqi = $nianjiazhouqi / 2; //0.5天的周期


                    //$nianjia=$m/round($nianjiazhouqi,1);
                    $nianjia = $m / $nianjiazhouqi; //实际年假天数等于 当前月除以年假半天周期



                    $nianjia = floor($nianjia);
                    echo $users['name'].'年假天数'.$nianjia."<br>";


                    //上次年假
                    echo  $this->actionSnianjia(100035,3);
                    exit;
                    /*
                    if ($nianjia - $snianjia > 0) {
                        $nianjiazjts = ($nianjia - $snianjia) * 0.5;
                    }

                    //$User = M("Users"); // 实例化User对象
                    if ($nianjiazjts > 0) {
                        // $User->where("id=$uid")->setInc('nianjia',$nianjiazjts); // 年假加0.5
                        UserBackend::updateAllCounters(['nianjia' => $nianjiazjts], ['id' => $users['id']]);
                        \Yii::warning('修改用户id'.$users['id'].'年假+'.$nianjiazjts.'天',__METHOD__);
                    }

                    UserBackend::updateAll(['njuptime' => time()], ['id' => $users['id']]);
                    $md = date("m-d");


                    if ($md == '01-01') {

                        $b = strtotime(date("Y-m-d") . " -1 years");

                        $yjjj = floor(1 / $nianjiazhouqi) * 0.5;//1月应有年假

                        //算出去年应有年假
                        if (strtotime($a . "+1 year") < $b) {
                            $ca3 = date_diff(date_create($c), date_create(date("Y-m-d", $b)));//入职时间和现在时间差值

                            $nianjia = 5 + $ca3->y;

                            if ($nianjia < $users['nianjia']) {

                                UserBackend::updateAll(['nianjia' => $nianjia + $yjjj], ['id' => $users['id']]);
                            } else {

                                UserBackend::updateAll(['nianjia' => $users['nianjia'] + $yjjj], ['id' => $users['id']]);
                            }

                        }

                        UserBackend::updateAll(['njuptime' => time()], ['id' => $users['id']]);
                    }
                    */
                } else {
                    //入职时间不满一年
                    $nianjia = 0;

                }

            }
        }

        exit;
        return array('code'=>'200','msg'=>'年假更新执行成功');
    }


    public function actionCopycontract(){
        $sql='select 
advertiser,contract_no,max(is_ultimate_shenhe) as is_ultimate_shenhe ,max(payment_type) as payment_type,max(zhouqi) as zhouqi,max(is_jstype) as is_jstype,min(contract_start) as contract_state,max(contract_end) as contract_end,max(isguidang) as isguidang from jd_contract where ctime<=1513647734 and audit!=2 and is_meijie!=1 group by 
contract_no,advertiser';
        $list=\Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($list as $key=>$val) {
            $min = JdContract::find()->where(['contract_no' => $val['contract_no']])->andWhere("ctime<=1513647734 and audit!=2 and is_meijie!=1")->orderBy("ctime asc")->asArray()->one();
            $max = JdContract::find()->where(['contract_no' => $val['contract_no']])->andWhere("ctime<=1513647734 and audit!=2 and is_meijie!=1")->orderBy("ctime desc")->asArray()->one();

            //新建合同
            $contract = new ContractNew();
            $contract->advertiser = $val['advertiser'];
            $contract->contract_no = $val['contract_no'];
            $contract->agent_company = $min['agent_company'];
            $contract->audit = $max['audit'];
            if ($val['is_ultimate_shenhe'] == 1) {
                $contract->audit_count = 4;
            } else
            {
                $contract->audit_count =0;
            }
            $contract->submituser=$min['submituser'];
            $contract->ctime=$min['ctime'];
            $contract->audit_u=$max['audit_u'];
            $contract->is_ultimate_shenhe=$val['is_ultimate_shenhe'];
            $contract->note='从协议复制的合同-来自CRM程序';
            $contract->payment_type=$val['payment_type'];
            $contract->zhouqi=$val['zhouqi'];
            $contract->is_jstype=$val['is_jstype'];
            $contract->contract_start=$val['contract_start'];
            $contract->contract_end=$val['contract_end'];
            $contract->market=$max['market'];
            $contract->iszuofei=$max['iszuofei'];
            $contract->isguidang=$val['isguidang'];
            $contract->save();


        }



    }


    public function actionLasttimeup(){
        //查询公司需要几级审核
        $shenhe=AuditAction::find()->where(['action_name'=>'contract_new'])->one();

        //修改合同的最后审核时间
        $list=ContractNew::find()->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>'contract_new','yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            ContractNew::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }

        //修改协议的最后审核时间
        $list=JdContract::find()->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>'contract','yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            JdContract::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }

        //修改续费的最后审核时间
        $list=RenewHuikuan::find()->where('payment_type in(1,2)')->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>'renew','yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            RenewHuikuan::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }

        //修改回款的最后审核时间
        $list=BackMoney::find()->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>'backmoney','yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            BackMoney::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }

        //修改退款到客户的最后审核时间
        $list=RenewHuikuan::find()->where("payment_type=14")->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>'refund_kehu','yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            RenewHuikuan::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }


        //修改退款到总账户的最后审核时间
        $list=RenewHuikuan::find()->where("payment_type in(15)")->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('refund_account_p','refund_account_z'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            RenewHuikuan::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }


        //修改补款的最后审核时间
        $list=RenewHuikuan::find()->where("payment_type in(3)")->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('bukuan'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            RenewHuikuan::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }

        //修改媒介收打款的最后审核时间
        $list=MbackMoney::find()->where("type in(1,2)")->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('meijie-dakuan','meijie-huikuan'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            MbackMoney::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }




        //修改媒介补款的最后审核时间
        $list=MrenewHuikuan::find()->where("payment_type in(3)")->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('meijie-bukuan'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            MrenewHuikuan::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }


        //修改媒介补款的最后审核时间
        $list=Waichu::find()->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('waichu'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            Waichu::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }

        //修改媒介补款的最后审核时间
        $list=Holiday::find()->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('qingjia'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            Holiday::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }

        //修改媒介补款的最后审核时间
        $list=QdDakuan::find()->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('qd_dakuan'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            QdDakuan::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }

        //修改媒介补款的最后审核时间
        $list=Invoice::find()->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('invoice'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            Invoice::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }
        //修改媒介补款的最后审核时间
        $list=Zhuankuan::find()->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('zhuankuan'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
            if($audit!='1970-01-01 08:00:00')
            Zhuankuan::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }


        $list=JdContract::find()->all();
        foreach ($list as $key=>$value)
        {
            $audit=Audit::find()->where(['tablea'=>array('meijie_contract'),'yid'=>$value['id']])->max('catated_at');
            $audit=date("Y-m-d H:i:s",$audit);
             if($audit!='1970-01-01 08:00:00')
             JdContract::updateAll(['last_time'=>$audit],['id'=>$value['id']]);

        }

        exit;







    }


    //默认广告主和产品
    public function actionAdverprset(){

        $list=JdCustomer::find()
            ->select('id,advertiser,appname')
            ->where("customer_type!=3")->asArray()->all();

        foreach ($list as $key=>$val)
        {
            $ad= new CustomerAdvertiser();
            $ad->advertiser=$val['advertiser'];
            $ad->advertiser_for_short='crm_';
            $ad->customer_id=$val['id'];
            $ad->ctime=time();

            $ad->save();

            $pr=new CustomerAdvertiserReleveance();
            $pr->advertiser=$ad->id;
            $pr->product=$val['appname'];
            $pr->product_short_name=$val['appname'];
            $pr->save();


        }

        //修改账户所属广告主和产品
        $accountlist=Account::find()->where("cu_ad_id isnull")->asArray()->all();
        foreach ($accountlist as $key=>$val)
        {
            //查询广告主
            $ggz=CustomerAdvertiser::find()->where(['customer_id'=>$val['avid'],'advertiser_for_short'=>'crm_'])->one();
            //查询相关广告主产品
            $pr=CustomerAdvertiserReleveance::find()->where(['advertiser'=>$ggz['id']])->one();
            Account::updateAll(['cu_ad_id'=>$ggz['id'],'cu_ad_product'=>$pr['id']],['id'=>$val['id']]);
        }

        //修改续费，退款到总账户 记录的广告主id 和产品id

        $renewlist=RenewHuikuan::find()->where("payment_type in(1,2,15)")->asArray()->all();
        foreach ($renewlist as $key=>$val)
        {
            //查询账户续费账户的广告主和产品id
            $accountinfo= Account::findOne($val['account']);

            RenewHuikuan::updateAll(['cu_ad_id'=>$accountinfo['cu_ad_product']],['id'=>$val['id']]);
        }


        $renewlist=RenewHuikuan::find()->where("payment_type in(3) or is_huikuan=1")->asArray()->all();
        foreach ($renewlist as $key=>$val)
        {
            //查询广告主
            $ggz=CustomerAdvertiser::find()->where(['customer_id'=>$val['advertiser'],'advertiser_for_short'=>'crm_'])->one();

            RenewHuikuan::updateAll(['cu_ad_id'=>$ggz['id']],['id'=>$val['id']]);
        }


        exit;
    }


    public function actionCopyadverinfo(){

        $list=JdCustomer::find()
            ->where("customer_type!=3")->asArray()->all();

        foreach ($list as $key=>$val)
        {
            $ad=CustomerAdvertiser::find()->where(['advertiser'=>$val['advertiser']])->one();
            if($ad){
                //var_dump($ad);
            $ad->tax_identification=$val['tax_identification'];
            $ad->ticket_address=$val['ticket_address'];
            $ad->open_account=$val['open_account'];
            $ad->account=$val['account'];
            $ad->kp_tel=$val['kp_tel'];

            $ad->save();

            }


        }

        exit;
    }

    public function actionPitest(){
        //如果成功 发送邮件
        $mail= \Yii::$app->mailer->compose();
        $mail->setTo('2881486189@qq.com');
        $mail->setSubject("小田，您有新的解析需求");
//$mail->setTextBody('zheshisha ');   //发布纯文字文本
        $mail->setHtmlBody("内容：".$model->note);    //发布可以带html标签的文本
        $mail->send();
    }

    //转款 生成的退款id 对应 相应生成的续费
    public function actionZuankuanph(){
        $zklist=Zhuankuan::find()->where('tk_id notnull')->andWhere(['is_ultimate_shenhe'=>1])->all();

        foreach ($zklist as $key=>$val)
        {
            RenewHuikuan::updateAll(['relevance_id'=>$val['xf_id']],['id'=>$val['tk_id']]);
        }

    }


    //默认广告主
    public function actionContractmrad(){
        $list=JdContract::find()->where('is_meijie=0')->all();

        foreach ($list as $key=>$val)
        {
            $k=CustomerAdvertiser::find()->where(['customer_id'=>$val['advertiser']])->orderBy('id asc ')->one();

            if($k['id']!='')
            {
                JdContract::updateAll(['cu_ad_id'=>$k['id']],['id'=>$val['id']]);
            }
        }


        $list=ContractNew::find()->all();

        foreach ($list as $key=>$val)
        {
            $k=CustomerAdvertiser::find()->where(['customer_id'=>$val['advertiser']])->orderBy('id asc ')->one();

            if($k['id']!='')
            {
                ContractNew::updateAll(['cu_ad_id'=>$k['id']],['id'=>$val['id']]);
            }
        }




    }

    //賬戶mht_id
    public function actionAccountmht(){
        $account_list=Account::find()->select('jd_account.id,c.mht_id')->leftJoin('jd_contract c','c.id=jd_account.contract_id')->all();

        foreach ($account_list as $key=>$val)
        {
            //查询所属合同的媒介合同id
            Account::updateAll(['mht_id'=>$val['mht_id']],['id'=>$val['id']]);

        }
    }

    //客户协议 title 改为媒介合同title
    public function actionUpdatecontracttitle(){

        $account_list=JdContract::find()->select('jd_contract.id,c.title')->leftJoin('jd_contract c','c.id=jd_contract.mht_id')->where(['jd_contract.is_meijie'=>'0'])->all();

        foreach ($account_list as $key=>$val)
        {
            //查询所属合同的媒介合同id
            JdContract::updateAll(['title'=>$val['title']],['id'=>$val['id']]);

        }
    }

    //迁移
    public function actionMvcrm(){

        $list=AuthItem::find()->where("name like '%/%'")->all();

        foreach ($list as $key=>$val)
        {

        }

    }


    public function actionUpdaxfcot(){
        //客户续费list（已完成但无续费cost）

        /*
        $list=RenewHuikuan::find()
            ->select('a.id,a.xf_cost,b.a_users,b.mht_id,con.title,con.rebates_proportion,con.dl_fandian,con.note,a.show_money')
            ->join('a left join','jd_account b','a.account=b.id')
            ->leftjoin('jd_contract con','b.mht_id=con.id')

            ->where('a.xf_cost isnull and a.is_ultimate_shenhe=1 and a.payment_type in(1,2,15) and b.mht_id notnull')
            ->andWhere('a.id in(1321501,2549,1980,1321292,1323020,1322596,3163,3274,3929,4724,1322841,1321412,1322926,2637,106,104,116,118,105,100,121,107,122,103,115,110,111,120,112,117,119)')
            ->asArray()
            ->all();



        foreach ($list as $key=>$value)
        {

            $fandian=($value['rebates_proportion']+100)/100;//媒体返点
            $dlfandian=(100-$value['dl_fandian'])/100;//代理返点
            $xf_cost=$value['show_money']/$fandian*$dlfandian;//续费成本
            RenewHuikuan::updateAll(['xf_cost'=>$xf_cost],['id'=>$value['id']]);
            MrenewHuikuan::updateAll(['xf_cost'=>$xf_cost],['xf_id'=>$value['id']]);
           // echo $value['id'].'=>'.$xf_cost."<br>";
        }
        */

        $list=BeikuanAccount::find()->all();
        foreach ($list as $key=>$val)
        {
            $sql="select (select sum(money) from beikuan where account={$val[id]} and is_ultimate_shenhe=1)-(select sum(fk_money) from beikuan_account_to_renew where bk_account_id={$val[id]})";
            $one=\Yii::$app->db->createCommand($sql)->queryScalar();

            if($val['yu_e']!=$one and $one!='')
            {
               // BeikuanAccount::updateAll(['yu_e'=>$one],['id'=>$val['id']]);
                echo 'id:'.$val[id].'账户余额：'.$val['yu_e'].'计算余额:'.$one.'<br>';
                echo $sql."<br>";
            }

        }

        exit;



    }

    public function actionYihuikuanxufeituikuanxiufu(){

        $list=Yihuikuanxufei::find()->select('a.*,b.payment_type,b.account')->join('a left join' ,'jd_renew_huikuan b','a.hk_id=b.id')->where(['b.payment_type'=>'15','a.id'=>'1337570'])->asArray()->all();

        foreach ($list as $key=>$val)
        {
            if($val['payment_type']=='15')
            {

                //退款详情
                $tkinfo=RenewHuikuan::findOne($val['hk_id']);

                //退款账户详情
                $tkaccountinfo=Account::findOne($val['account']);
                //获取媒体合同
                $tkmjhtinfo=JdContract::find()
                    ->select('a.rebates_proportion,a.dl_fandian,a.product_line,b.fk_type')
                    ->join("a left join","jd_product_line b","a.product_line=b.id")
                    ->where(["a.id"=>$tkaccountinfo['mht_id']])
                    ->asArray()
                    ->one();

                $tk_fd=(intval($tkinfo['rebates_proportion'])+100)/100;

                //产品线 类型 判断实付金额
                if ($tkmjhtinfo['fk_type'] == '0') {
                    $tkshifu=($val['money']*$tk_fd)/(($tkmjhtinfo['rebates_proportion']+100)/100)*((100-$tkmjhtinfo['dl_fandian'])/100);
                }else
                {

                    $tkshifu=$tkinfo['xf_cost']/$val['money']*$val['money'];
                }

                Yihuikuanxufei::updateAll(['tk_shifu'=>$tkshifu],['id'=>$val['id']]);
               //$yixufeihuikuan_date->tk_shifu=$tkshifu;


            }


        }


        exit;
    }



    public function actionHjd()
    {

        for ($ahjd=1;$ahjd<=124;$ahjd++) {

            //http://www.anzhi.com/list_2_124_hot.html
            $url = 'http://www.anzhi.com/list_2_' . $ahjd . '_hot.html';

            // $url = 'Public/txt/movies.txt';

            $content = file_get_contents($url);

            // $content = iconv("gb2312", "utf-8//IGNORE",$content);
            //<span class="app_name"><a style="float:left;" title="小会计记账" href="/pkg/3bad_com.hsdan.nnth.html">小会计记账</a></span>
            $reg = "|<span class=\"app_name\">(.*?)<\/span>|is";//正则匹配div
            $res = preg_match_all($reg, $content, $match);
            $count = count($match[1]);

            //有数据
            if ($count) {

                $arr = array();
                $array = array();
                for ($i = 0; $i < $count; $i++) {
                    $a = "|<a[^>]*>(.*?)<\/a>|is"; //匹配value
                    $patten = "/<a href=[\'\"]?([^\'\" ]+).*?>/";//匹配href值
                    preg_match_all($a, $match[1][$i], $mat);
                    preg_match_all($patten, $match[1][$i], $href);
                    foreach ($mat[1] as $key => $val) {
                        $array = array(
                            //'href'=>'http://www.dytt8.net/'.$href[1][$key],
                            'name' => $val,
                            //'ctime'=>time(),
                        );
                        echo $val . "<br>";
                        array_push($arr, $array);
                    }


                }

                // $res = $this->Movies->addAll($arr);

            }

        }

        exit;


    }

    public function actionTree(){
        $list=ArticleClass::find()->asArray()->indexBy('id')->all();

        return $this->genTree9($list);

    }

    function genTree9($items) {
        $tree = array(); //格式化好的树
        foreach ($items as $item)
            if (isset($items[$item['parent_id']]))
                $items[$item['parent_id']]['son'][] = $items[$item['id']];
            else
                $tree[] = &$items[$item['id']];
        return $tree;
    }

    public function actionCustomer_level(){
        $list=\Yii::$app->db->createCommand('select a.id,b.maxtime from jd_customer a left join 
(select advertiser as avid,max(ctime) as maxtime from jd_renew_huikuan GROUP BY advertiser ) b on b.avid=a.id where maxtime notnull order by maxtime desc')->queryAll();


        foreach ($list as $key=>$val)
        {
            $cha=date_diff(date_create(date('Y-m-d',strtotime("-1 day"))),date_create(date('Y-m-d H:i:s',$val['maxtime'])));
            if($cha->days <= 1)
            {
                JdCustomer::updateAll(['liveness'=>5],['id'=>$val['id']]);
            }elseif($cha->days <= 7)
            {
                JdCustomer::updateAll(['liveness'=>4],['id'=>$val['id']]);
            }elseif($cha->days <= 30)
            {
                JdCustomer::updateAll(['liveness'=>3],['id'=>$val['id']]);
            }elseif($cha->days <= 90)
            {
                JdCustomer::updateAll(['liveness'=>2],['id'=>$val['id']]);
            }elseif($cha->days <= 365)
            {
                JdCustomer::updateAll(['liveness'=>1],['id'=>$val['id']]);
            }

        }

        return array('msg'=>'success','code'=>'200');

    }


    //自动设置回款计划
    public function actionSetPlanAuto(){
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y'));

        $thisMonth = date('Y-m-',$beginThismonth);
        $beginThismonth=date('Y-m-d',$beginThismonth);
        $endThismonth=date('Y-m-d',$endThismonth);

        $sql='select a.advertiser,sum(a.xf_qiane) money ,to_char(a.pay_day, \'dd\') pay_date,c.id as xs  from jd_renew_huikuan a left join jd_customer b on b.id=a.advertiser left join user_backend c on c.id=b.submituser where  a.pay_day <=\''.$endThismonth.'\' and a.xf_qiane>0 and a.payment_type in (1,2) and a.is_ultimate_shenhe = 1 GROUP BY a.advertiser,pay_date,c.id';
        $result=\Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($result as $key=>$value)
        {
            $huikuanplan=new HuikuanPlan();
            $huikuanplan->advertiser=$value['advertiser'];
            $huikuanplan->ctime=time();
            $huikuanplan->money=$value['money'];
            $huikuanplan->receivable_day=strtotime(\Yii::$app->hjd->date_is_true($thisMonth.$value['pay_date']));
            $huikuanplan->submituser=$value['xs'];
            $huikuanplan->note='crm回款计划自动生成';
            if(!$huikuanplan->save())
            {
                var_dump($huikuanplan->getErrors());exit;
            }
        }
        return ['code'=>'200','msg'=>'自动执行添加回款计划成功'];
    }

    //设置客户补款相对于的回款
    public function actionKhbukuan_hk(){
        $list=RenewHuikuan::find()->
            where(['payment_type'=>'31'])
            ->asArray()
            ->all();

        foreach ($list as $key=>$val)
        {
            $model_hk=new RenewHuikuan();
            $model_hk->is_huikuan=1;
            $model_hk->backmoney_yue=$val['money'];
            $model_hk->note='协议分配款 由CRM系统生成 为抵消客户补款';
            $model_hk->cu_ad_id=$val['cu_ad_id'];
            $model_hk->is_ultimate_shenhe=1;
            $model_hk->audit=1;
            $model_hk->audit_count=2;
            $model_hk->audit_u=$val['audit_u'];
            $model_hk->xf_contractid=$val['xf_contractid'];
            $model_hk->advertiser=$val['advertiser'];
            $model_hk->payment_time=$val['payment_time'];
            $model_hk->ctime=$val['ctime'];
            $model_hk->money=$val['money'];
            $model_hk->submituser=$val['submituser'];
            $model_hk->ht_state=$val['ht_state'];
            $model_hk->save();
            RenewHuikuan::money_change($val['advertiser'], $val['xf_contractid'], '4', $val['money']);


        }
        var_dump($list);

    }

    public function actionBeikuansh(){
        $list=Beikuan::find()->where([
            'payment_type'=>2,
            'audit'=>1,
            'audit_count'=>3,
            'is_ultimate_shenhe'=>0
        ])->asArray()->all();

        //查询续费需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'beikuan'])->one();
        foreach ($list as $key=>$value)
        {
                Beikuan::beikuan_shenhe($value['id'],3,1);
        }

    }


    public function actionInvoiceup(){
        $list=Invoice::find()
            ->select('invoice_head')
            ->groupBy('invoice_head')
            ->asArray()
            ->all();

        foreach ($list as $value)
        {

            Invoice::up_invoice_state($value['invoice_head']);
        }

        exit;
    }


}
