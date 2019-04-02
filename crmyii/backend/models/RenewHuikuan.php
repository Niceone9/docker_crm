<?php

namespace backend\models;

use Yii;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 *
 * This is the model class for table "jd_renew_huikuan".
 *
 * @property integer $id
 * @property integer $advertiser
 * @property integer $product_line
 * @property integer $type
 * @property integer $contract_start
 * @property integer $contract_end
 * @property string $rebates_proportion
 * @property double $show_money
 * @property double $money
 * @property integer $payment_type
 * @property integer $payment_time
 * @property string $note
 * @property integer $audit_1
 * @property integer $audit_2
 * @property integer $submituser
 * @property integer $ctime
 * @property string $appname
 * @property integer $users2
 * @property integer $susers1
 * @property integer $susers2
 * @property integer $xf_contractid
 * @property integer $is_huikuan
 * @property integer $account
 * @property integer $market
 * @property integer $audit_3
 * @property integer $susers3
 * @property integer $audit_4
 * @property integer $susers4
 * @property string $huikuanren
 * @property string $xf_cost
 * @property string $xf_qiane
 * @property integer $is_accomplish
 * @property integer $accomplish_users
 * @property string $backmoney_yue
 * @property integer $company
 * @property string $pici
 * @property integer $ht_state
 * @property integer $no_cost_xf
 * @property integer $is_ultimate_shenhe
 * @property integer $relevance_id
 * @property integer $cu_ad_id
 */
class RenewHuikuan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_renew_huikuan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser', 'money', 'payment_time', 'submituser', 'ctime', 'xf_contractid'], 'required'],
            [['advertiser', 'product_line', 'type', 'contract_start', 'contract_end', 'payment_type', 'payment_time', 'submituser', 'ctime', 'users2', 'xf_contractid', 'is_huikuan', 'account', 'market', 'is_accomplish', 'accomplish_users','company', 'ht_state','cu_ad_id','margin_istui','relevance_id','is_xiadan','is_tk_deduction'], 'integer'],
            [['show_money', 'money', 'xf_cost', 'xf_qiane', 'backmoney_yue','no_cost_xf','yhuikou','whuikou'], 'number'],
            [['note'], 'string'],
            [['rebates_proportion'], 'string', 'max' => 50],
            [['appname'], 'string', 'max' => 100],
            [['huikuanren', 'pici'], 'string', 'max' => 255],
            [
                'ys_money','default','value'=>function($model,$attribute){
                    if(!$model[$attribute])
                    {
                        return $model['money'];
                    }
                }
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'advertiser' => 'Advertiser',
            'product_line' => 'Product Line',
            'type' => 'Type',
            'contract_start' => 'Contract Start',
            'contract_end' => 'Contract End',
            'rebates_proportion' => 'Rebates Proportion',
            'show_money' => 'Show Money',
            'money' => 'Money',
            'payment_type' => 'Payment Type',
            'payment_time' => 'Payment Time',
            'note' => 'Note',
            'audit_1' => 'Audit 1',
            'audit_2' => 'Audit 2',
            'submituser' => 'Submituser',
            'ctime' => 'Ctime',
            'appname' => 'Appname',
            'users2' => 'Users2',
            'susers1' => 'Susers1',
            'susers2' => 'Susers2',
            'xf_contractid' => 'Xf Contractid',
            'is_huikuan' => 'Is Huikuan',
            'account' => 'Account',
            'market' => 'Market',
            'audit_3' => 'Audit 3',
            'susers3' => 'Susers3',
            'audit_4' => 'Audit 4',
            'susers4' => 'Susers4',
            'huikuanren' => 'Huikuanren',
            'xf_cost' => 'Xf Cost',
            'xf_qiane' => 'Xf Qiane',
            'is_accomplish' => 'Is Accomplish',
            'accomplish_users' => 'Accomplish Users',
            'backmoney_yue' => 'Backmoney Yue',
            'company' => 'Company',
            'pici' => 'Pici',
            'ht_state' => 'Ht State',
        ];
    }
    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }
    //关联数据

    public function getContract(){
        return $this->hasOne(JdContract::className(),['id'=>'xf_contractid'])->select('id,mht_id');
    }
    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'advertiser'])->select('id,advertiser,customer_type');
    }
    //关联数据
    public function getPrlin(){
        return $this->hasOne(ProductLine::className(),['id'=>'product_line']);
    }
    //关联数据
    public function getMarket0(){
        return $this->hasOne(UserBackend::className(),['id'=>'market'])->select('id,name');
    }
    //关联账户
    public function getAccount0(){
        return $this->hasOne(Account::className(),['id'=>'account'])->select('id,a_users,state,a_password,promote_url,mt_payment_type,dk_date,dk_zhouqi,server_type,mht_id');
    }
    //关联审核人
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }
    //关联下单信息
    public function getXiadan(){
        return $this->hasOne(RenewOrder::className(),['xf_id'=>'id']);
    }
    //
    public function getGuanggaozhu(){
        return $this->hasOne(CustomerAdvertiser::className(),['id'=>'cu_ad_id']);
    }

    //关联中间表
    public function getContractproduct(){
        return $this->hasOne(ProductLine::className(),['id'=>'product_line'])
            //->viaTable(Contract)
            ->viaTable(ContractRelevance::tableName(),['contract_id'=>'xf_contractid']);
    }

    public static function xufei_list($id='',$shenhewhere2='',$role='',$xiadan=0){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_renew_huikuan.id',0];
        }else
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');



        $searchwhere = ['or',['like','ad.advertiser', $search_adname],['like','prlin.name', $Search_str],['like','ad.advertiser', $Search_str],['like','jd_renew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str],['like','markets.name',$Search_str],['like','ac.a_users',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");

        if($start and $end)
        {
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_renew_huikuan.payment_time', $start], ['<', 'jd_renew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_renew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_renew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_renew_huikuan.payment_time',1]];
        }


        //公司名称单独搜索
        $search_adname=Yii::$app->request->post('search_adname');


        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="(jd_renew_huikuan.audit=0 or jd_renew_huikuan.audit=1) and jd_renew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_renew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_renew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //账户状态
        $account=Yii::$app->request->post("account_state");

        //下单条件
        if(Yii::$app->request->post('xiadan')==1 or $xiadan=='1')
        {
            $ordeid=RenewOrder::find()->select('xf_id')->column();
        }else
        {
            $ordeid=null;
            //$ordeid=RenewOrder::find()->select('xf_id')->column();

            //$shenhewhere.=' and jd_renew_huikuan.id not in(select xf_id from jd_renew_order)';

        }

        //是否只显示垫款
        $is_diankuan=Yii::$app->request->post("diankuan");
        if($is_diankuan!='' && $is_diankuan=='1')
        {
            $payment_type="2";
        }else
        {
            $payment_type="1,2";
        }
        //完成状态
        $is_accomplish=Yii::$app->request->post("is_ultimate_shenhe");

        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser as adname,mhtad.advertiser as mhtad,jd_renew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,mht.rebates_proportion as mtfandian,mht.title as mhtname,cuad.advertiser as guanggaozhu,cuadrele.product as product')
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac','contract cont'])
            ->leftJoin("jd_contract_relevance contre","cont.id=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_contract mht','mht.id=ac.mht_id')
            ->leftJoin('jd_customer mhtad','mhtad.id=mht.advertiser')
            ->leftJoin('customer_advertiser cuad','ac.cu_ad_id=cuad.id')
            ->leftJoin('customer_advertiser_releveance cuadrele','ac.cu_ad_product=cuadrele.id')
            ->where('jd_renew_huikuan.payment_type in('.$payment_type.')')
            ->andWhere($shenhewhere)
            ->andWhere($userswhere)
            ->andFilterWhere(['ac.state'=>$account,'jd_renew_huikuan.is_ultimate_shenhe'=>$is_accomplish])
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.id'=>$ordeid])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['like','ad.advertiser',$search_adname])
            ->asArray();

        $ar['sum']=$query->sum('jd_renew_huikuan.money');

        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        // 获取分页和排序数据
       $ar['data'] = $provider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();

        return $ar;
    }
    //续费详情

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function renewinfo($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','account0','advertiser0','audituname','xiadan'])->asArray()->one();
        $contarat_info=JdContract::contractinfo_i($data['account']);
        //account关联广告主及产品信息
        $account_cu_ad_info=Account::find()->select("cuad.advertiser as guanggaozhu,cuadrele.product as product")->join(' ac left join','customer_advertiser cuad','ac.cu_ad_id=cuad.id')->leftJoin('customer_advertiser_releveance cuadrele','ac.cu_ad_product=cuadrele.id')->where(['ac.id'=>$data['account']])->asArray()->one();

        $data['cu_ad_info']=$account_cu_ad_info;
        //实付金额计算公式 2017年5月27日14:27:39
        $dengyu=$data['show_money']/(1+$contarat_info['mht']['rebates_proportion']/100)*(1-$contarat_info['mht']['dl_fandian']/100);
        $data['jisuangongshi']=$data['show_money']."/(1+".($contarat_info['mht']['rebates_proportion']/100).")*(1-".($contarat_info['mht']['dl_fandian']/100).")=".round($dengyu,2);
        $data['contractinfo']=$contarat_info;
        //查看续费详情
        if($data['pici']!='')
        {
            $pilist=self::find()->select(['id','money','show_money','payment_time','xf_cost'])->where("pici='$data[pici]'")->orderBy('id asc')->asArray()->all();
            $picimoney_sum=0;
            $picishowmoney_sum=0;
            foreach ($pilist as $pv)
            {
                $picimoney_sum+=$pv['money'];
                $picishowmoney_sum+=$pv['show_money'];
            }
            $data['picilist']=$pilist;
            $data['picimoney_sum']=$picimoney_sum;
            $data['picishowmoney_sum']=$picishowmoney_sum;
        }


        return $data;
    }

    //添加续费
    public static function add_renew($shengcheng='',$tk_deduction='0'){

        $model = new RenewHuikuan();
        //接收数据并处理
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');


        //如果是生成续费
        if($shengcheng!='')
        {
            $postdata=$shengcheng;
            $model->load($shengcheng,'');
        }else
        {
            $model->payment_time = strtotime(Yii::$app->getRequest()->getBodyParams()['payment_time']);
        }
        $model->ctime = time();
        $model->xf_qiane=$model->money;
        $model->no_cost_xf=$model->show_money;

        //跟系统计算账户比的公式进行对比。
        Yii::$app->hjd->show_money($model->money,$model->xf_contractid,$model->show_money);



        //查询所属合同状态
        $contractinfo = JdContract::contractinfonomt($model->xf_contractid);
        //媒介合同信息
        $accointinfo=Account::findOne($model->account);
        $mhtinfo=JdContract::findOne($accointinfo->mht_id);




        //如果没有选择媒介合同
        /*
        if(empty($contractinfo['mht']['id']))
        {
            return array('code'=>'500','meg'=>'查找不到对应的媒介合同！');
        }*/
        //如果是预付查询合同余额是否足以低效此次续费 -$contractinfo['margin_money']
        $yue=$contractinfo['huikuan']+$contractinfo['bukuan']-$contractinfo['yu_e'];

        if(Yii::$app->request->post('tk_deduction')=='1')
        {
            $tk_deduction='1';
        }

        if($model->payment_type=='1' && $tk_deduction!='1')
        {
            //用高精度比较两个数值
            $c=bccomp($model->money,round($yue,2));
            if($c==1)
            {
                throw new \Exception('合同余额不足以抵消此比预付续费 '.$model->money.'-'.$yue);

                exit;
            }
        }

        //如果是垫付的话 计算还款日期
        //垫付 显示 结算方式
        //   结算方式 : 每月结算（结算类型，付款日，付款范围）  充值结算（消耗显示付款周期）

        if($model->payment_type=='2')
        {
            if($contractinfo['payment_type']=='1')
            {
                $zhouqi=Yii::$app->request->getBodyParams()['zhouqi'];
                if($zhouqi=='')
                {
                    throw new \Exception('必须传入周期字段');
                }

                $model->pay_day=date('Y-m-d',strtotime(Yii::$app->getRequest()->getBodyParams()['payment_time']."+$zhouqi day"));

            }
            else{
                //按月结算
                if($contractinfo['clearing_form']=='1')
                {
                    //算出付款时间
                    //如果是按充值结算
                    $xf_m=date('Y-m-',strtotime(Yii::$app->getRequest()->getBodyParams()['payment_time'])).'01';
                    $m=$contractinfo['pay_scope'];
                    $d=$contractinfo['pay_day'];

                    //判断结算日 如果结算日大于当前日 则往后推一个月
                    $jiesuanri=$contractinfo['jiesuan_day'];

                    if($jiesuanri>date('d',$model->payment_time))
                    {
                        $m+=1;
                    }


                    if($contractinfo['is_jstype']=='0'){
                        $pay_day=date('Y-m-',strtotime($xf_m."+$m month"));
                    }else{
                        //如果是按消耗结算
                        $m+=1;
                        $pay_day=date('Y-m-',strtotime($xf_m."+$m month"));
                    }

                    $model->pay_day=$pay_day.$d;

                }elseif($contractinfo['clearing_form']=='2')
                {
                    $zhouqi=$contractinfo['zhouqi'];
                    $model->pay_day=date('Y-m-d',strtotime(Yii::$app->getRequest()->getBodyParams()['payment_time']."+$zhouqi day"));
                }
            }

            //如果付款日期不存在 例如2月31日 这样 就默认还款日为月最后一天

            $model->pay_day=Yii::$app->hjd->date_is_true($model->pay_day);



        }else
        {
            $model->pay_day=date('Y-m-d',$model->payment_time);
        }


        //计算续费成本
        /*
        if($mhtinfo['iszuofei']!=0)
        {
            return array('code'=>'500','meg'=>'媒介合同已经结束，不可以续费！');
        }*/

        $fandian=($mhtinfo['rebates_proportion']+100)/100;//媒体返点
        $dlfandian=(100-$mhtinfo['dl_fandian'])/100;//代理返点
        $model->xf_cost=$model->show_money/$fandian*$dlfandian;//续费成本
        $model->ht_state=$contractinfo['contract_state'];//合同状态
        //计算回扣

        $model->yhuikou=$model->money-$model->money*(100+$model->rebates_proportion)/(100+$model->rebates_proportion+$contractinfo->kh_grfd);

        $model->whuikou=$model->yhuikou;

        if(Yii::$app->request->post('isorder'))
        {
            $model->is_xiadan=1;
        }

        if($model->save())
        {

            //写入金额变动
            self::money_change($model->advertiser,$model->xf_contractid,$model->payment_type,$model->money);

            //如果是下单 则插入下单关联数据
            if(Yii::$app->request->post('isorder'))
            {
                $xdmodel=new RenewOrder();
                $xdmodel->xf_id=$model->id;
                $xdmodel->url=Yii::$app->request->post('url');
                $xdmodel->xingzhi=Yii::$app->request->post('xingzhi');
                $xdmodel->guanggaozhu=Yii::$app->request->post('guanggaozhu');
                $xdmodel->murl=Yii::$app->request->post('murl');
                $xdmodel->save();
            }

            //如果勾选了用退款余额抵扣就判断是否能抵扣 如果能就生成一笔回款
            if(Yii::$app->request->post('tk_deduction')=='1')
            {
                $adinfo=JdCustomer::findOne($model->advertiser);
                $c=bccomp($model->money,$adinfo->tuikuan_undistributed_yu_e);
                if($c==1)
                {
                    throw new \Exception('公司退款余额不足以抵消此比续费');
                    exit;
                }else{
                    //生成退款转出款并与转出款11对应
                    self::shengcheng_xf_zhuanru($model->id);
                }


            }
            return $model;
        }else
        {
            var_dump($model->errors);exit;
            return $model->errors;
        }


    }

    public static function add_pici(){

        $pici=time().rand(1000,9999);
        $post=Yii::$app->request->post();
        $money=$post['money'];
        foreach ($money as $key=>$val)
        {
            self::add_renew_pici($post['money'][$key],$post['show_money'][$key],$post['ys_money'][$key],$post['payment_time'][$key],$pici);
        }
        return self::find()->where(['pici'=>$pici])->all();
    }

    //添加批次续费
    public static function add_renew_pici($money,$show_money,$ys_money,$payment_time,$pici){

        $model = new RenewHuikuan();
        //接收数据并处理
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->ctime = time();
        $model->money=$money;
        $model->ys_money=$ys_money;
        $model->show_money=$show_money;
        $model->payment_time = strtotime($payment_time);
        $model->xf_qiane=$model->money;
        $model->pici=$pici;
        //查询所属合同状态
        $contractinfo = JdContract::contractinfonomt($model->xf_contractid);

        //账户信息
        $accountinfo=Account::findOne($model->account);
        $mhtinfo=JdContract::findOne($accountinfo->mht_id);

        //如果没有选择媒介合同
        /*
        if(empty($mhtinfo['id']))
        {
           //return array('code'=>'500','meg'=>'查找不到对应的媒介合同！');
        }*/
        //如果是预付查询合同余额是否足以低效此次续费
        $yue=$contractinfo['huikuan']-$contractinfo['margin_money']+$contractinfo['bukuan']-$contractinfo['yu_e'];

        if($model->payment_type=='1')
        {
            //用高精度比较两个数值
            $c=bccomp($model->money,$yue);
            if($c==1)
            {
                throw new \Exception('合同余额不足以抵消此比预付续费');
                return array('code'=>'500','meg'=>'合同余额不足以抵消此比预付续费！');
            }
        }
        //如果是垫付的话 计算还款日期
        //垫付 显示 结算方式
        //   结算方式 : 每月结算（结算类型，付款日，付款范围）  充值结算（消耗显示付款周期）

        if($model->payment_type=='2')
        {
            //判断合同是否为预付 如果是合同是预付 续费是垫付 则按照前端传过来的日期进行 还款日期计算

            if($contractinfo['payment_type']=='1')
            {
                $zhouqi=Yii::$app->request->getBodyParams()['zhouqi'];
                if($zhouqi=='')
                {
                    throw new \Exception('必须传入周期字段');
                }
                $model->pay_day=date('Y-m-d',strtotime($payment_time."+$zhouqi day"));
            }
            else{
                //按月结算
                if($contractinfo['clearing_form']=='1')
                {
                    //算出付款时间
                    //如果是按充值结算
                    $xf_m=date('Y-m-',strtotime($payment_time)).'01';
                    $m=$contractinfo['pay_scope'];
                    $d=$contractinfo['pay_day'];

                    //判断结算日 如果结算日大于当前日 则往后推一个月
                    $jiesuanri=$contractinfo['jiesuan_day'];
                    if($jiesuanri>date('d',$model->payment_time))
                    {
                        $m+=1;
                    }

                    if($contractinfo['is_jstype']=='0'){
                        $pay_day=date('Y-m-',strtotime($xf_m."+$m month"));
                    }else{
                        //如果是按消耗结算
                        $m+=1;
                        $pay_day=date('Y-m-',strtotime($xf_m."+$m month"));
                    }

                    $model->pay_day=$pay_day.$d;
                }elseif($contractinfo['clearing_form']=='2')
                {
                    $zhouqi=$contractinfo['zhouqi'];
                    $model->pay_day=date('Y-m-d',strtotime($payment_time."+$zhouqi day"));
                }
            }


            //如果付款日期不存在 例如2月31日 这样 就默认还款日为月最后一天
            $model->pay_day=Yii::$app->hjd->date_is_true($model->pay_day);

        }else
        {
            $model->pay_day=date('Y-m-d',$model->payment_time);
        }



        //计算续费成本
        /*
        if($mhtinfo['iszuofei']!=0)
        {

            return array('code'=>'500','meg'=>'媒介合同已经结束，不可以续费！');
        }*/

        $fandian=($mhtinfo['rebates_proportion']+100)/100;//媒体返点
        $dlfandian=(100-$mhtinfo['dl_fandian'])/100;//代理返点
        $model->xf_cost=$model->show_money/$fandian*$dlfandian;//续费成本
        $model->ht_state=$contractinfo['contract_state'];//合同状态
        //计算回扣
        $model->yhuikou=$model->money-$model->money*(100+$model->rebates_proportion)/(100+$model->rebates_proportion+$contractinfo->kh_grfd);

        $model->whuikou=$model->yhuikou;
        if($model->save())
        {

            //写入金额变动
            self::money_change($model->advertiser,$model->xf_contractid,$model->payment_type,$model->money);

            //如果是下单 则插入下单关联数据
            if(Yii::$app->request->post('isorder'))
            {
                $xdmodel=new RenewOrder();
                $xdmodel->xf_id=$model->id;
                $xdmodel->url=Yii::$app->request->post('url');
                $xdmodel->xingzhi=Yii::$app->request->post('xingzhi');
                $xdmodel->guanggaozhu=Yii::$app->request->post('guanggaozhu');
                $xdmodel->murl=Yii::$app->request->post('murl');
                $xdmodel->save();
            }

            //如果勾选了用退款余额抵扣就判断是否能抵扣 如果能就生成一笔回款
            if(Yii::$app->request->post('tk_deduction')=='1')
            {
                $adinfo=JdCustomer::findOne($model->advertiser);
                $c=bccomp($model->money,$adinfo->tuikuan_undistributed_yu_e);
                if($c==1)
                {
                    throw new \Exception('公司退款余额不足以抵消此比续费');
                    exit;
                }else{
                    //生成退款转出款并与转出款11对应
                    self::shengcheng_xf_zhuanru($model->id);
                }


            }

            return $model;
        }else
        {
            return $model->errors;
            exit;
        }


    }


    //增加公司和合同的续费
    public static function money_change($advertisers_id,$contract_id,$type,$value,$accountid=''){
        //续费预付垫付 增加续费总额
        if($type=='1' or $type=='2')
        {
            JdCustomer::updateAllCounters(['yu_e'=>$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['yu_e'=>$value],['id'=>$contract_id]);
        }
        //补款 加
        if($type=='3')
        {
            JdCustomer::updateAllCounters(['bukuan'=>$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['bukuan'=>$value],['id'=>$contract_id]);
        }
        //分配款 加
        if($type=='4')
        {
            JdContract::updateAllCounters(['huikuan'=>$value],['id'=>$contract_id]);
        }
        //保证金款 加  保证金余额加
        if($type=='4-2')
        {
            JdContract::updateAllCounters(['margin_money'=>$value,'margin_yue'=>$value],['id'=>$contract_id]);
            JdCustomer::updateAllCounters(['margin_money'=>$value,'margin_yue'=>$value],['id'=>$advertisers_id]);
        }

        //保证金余额款 加  因：打给媒体的保证金被驳回了
        if($type=='4-2-1')
        {
            JdContract::updateAllCounters(['margin_yue'=>$value],['id'=>$contract_id]);
            JdCustomer::updateAllCounters(['margin_yue'=>$value],['id'=>$advertisers_id]);
        }

        //客户补款 加
        if($type=='4-3')
        {
            JdContract::updateAllCounters(['kehu_bukuan'=>$value],['id'=>$contract_id]);
            JdCustomer::updateAllCounters(['kehu_bukuan'=>$value],['id'=>$advertisers_id]);

        }
        //公司回款 加 公司回款金额 和 未分配余额
        if($type=='6')
        {
            JdCustomer::updateAllCounters(['huikuan'=>$value],['id'=>$advertisers_id]);
            JdCustomer::updateAllCounters(['undistributed_yu_e'=>$value],['id'=>$advertisers_id]);
        }

        //如果驳回了退款到客户 则客户回款加。
        if($type=='14')
        {
            JdCustomer::updateAllCounters(['huikuan'=>$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['huikuan'=>$value],['id'=>$contract_id]);
        }

    }

    //减少公司和合同的金额
    public static function money_change_jian($advertisers_id,$contract_id,$type,$value,$accountid=''){
        if($type=='1' or $type=='2')
        {
            JdCustomer::updateAllCounters(['yu_e'=>-$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['yu_e'=>-$value],['id'=>$contract_id]);
        }
        //公司分配款减少
        if($type=='4')
        {
            JdCustomer::updateAllCounters(['undistributed_yu_e'=>-$value],['id'=>$advertisers_id]);
        }
        //保证金款 保证金余额 减少 未分配余额加
        if($type=='4-2')
        {
            JdContract::updateAllCounters(['margin_money'=>-$value,'margin_yue'=>-$value],['id'=>$contract_id]);
            JdCustomer::updateAllCounters(['margin_money'=>-$value],['id'=>$advertisers_id]);
            JdCustomer::updateAllCounters(['undistributed_yu_e'=>$value],['id'=>$advertisers_id]);
        }

        //保证金余额减 因：给媒体打了保证金 所以客户的保证金余额减少。
        if($type=='4-2-1')
        {
            JdContract::updateAllCounters(['margin_yue'=>-$value],['id'=>$contract_id]);
            JdCustomer::updateAllCounters(['margin_yue'=>-$value],['id'=>$advertisers_id]);
        }
        //客户补款 减少 未分配余额加
        if($type=='4-3')
        {
            JdContract::updateAllCounters(['kehu_bukuan'=>-$value],['id'=>$contract_id]);
            JdCustomer::updateAllCounters(['kehu_bukuan'=>-$value],['id'=>$advertisers_id]);
            JdCustomer::updateAllCounters(['undistributed_yu_e'=>$value],['id'=>$advertisers_id]);
        }
        /*
        //公司补款减少
        if($type=='4-3')
        {
            JdContract::updateAllCounters(['bukuan'=>-$value],['id'=>$contract_id]);
        }*/
        //公司汇款减少
        if($type=='14')
        {
            JdCustomer::updateAllCounters(['huikuan'=>-$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['huikuan'=>-$value],['id'=>$contract_id]);
        }
        if($type=='15')
        {
            JdCustomer::updateAllCounters(['yu_e'=>-$value],['id'=>$advertisers_id]);
            JdContract::updateAllCounters(['yu_e'=>-$value],['id'=>$contract_id]);
        }

    }
    //续费对应回款消耗
    public static function renew_huikuan($contract_id='',$cu_ad_id=''){
        $list=self::find()->select(['id'])->where("(payment_type=1 or payment_type=2 or payment_type=14 or payment_type=16 or payment_type=31 or payment_type=20) and xf_qiane>0 and audit!=2 and is_ultimate_shenhe=1")->andFilterWhere(['xf_contractid'=>$contract_id])->orderBy("payment_time asc")->all();

        $logfile= dirname(__Dir__.'../').'/runtime/logs/hjd.log';
        foreach ($list as $key=>$value)
        {
            error_log(PHP_EOL.date("Y-m-d H:i:s").'  续费对应回款 xf_id:'.$value['id'].'...('.$key.'/'.count($list).')'.PHP_EOL,3,$logfile);
            self::renew_auto_huikuan($value['id']);

        }
        error_log(date("Y-m-d H:i:s").'  续费对应回款完成'.PHP_EOL,3,$logfile);
    }

    public static function renew_auto_huikuan($xf_id){
        $logfile= dirname(__Dir__.'../').'/runtime/logs/hjd.log';
        //续费信息
        $xufei_info=self::findOne($xf_id);
        error_log('查找续费信息完成',3,$logfile);
        if($xufei_info->cu_ad_id>0)
        {
          $cuwhere="cu_ad_id=$xufei_info->cu_ad_id or";
        }
        //此合同的回款列表
        $backmoney=self::find()
            ->where("(is_huikuan=1 or payment_type=3 or payment_type=15 or payment_type=311)  and xf_contractid='$xufei_info[xf_contractid]' and audit!=2 and backmoney_yue >0 and is_ultimate_shenhe=1")
           // ->andWhere("($cuwhere  cu_ad_id isnull)")
            ->andWhere(['is_ultimate_shenhe'=>1,'relevance_id'=>$xf_id])
            ->orderBy("payment_time asc")
            ->asArray()->all();



        //此合同实时付对应的续费优先
        //此合同回款列表

        if(!$backmoney)
        {
            $backmoney=self::find()
                ->where("(is_huikuan=1 or payment_type=3 or payment_type=15)  and xf_contractid='$xufei_info[xf_contractid]' and audit!=2 and backmoney_yue >0")
                ->andWhere(['is_ultimate_shenhe'=>1])
                ->andWhere('relevance_id isnull')
                ->orderBy("payment_time asc")
                ->asArray()->all();
        }






        error_log('回款列表查找完成',3,$logfile);
        //续费合同信息
        $yhtinfo=JdContract::findOne($xufei_info['xf_contractid']);
        //获取账户id
        $accountinfo=Account::findOne($xufei_info->account);

        //续费合同信息
        //$mjhtinfo=JdContract::findOne($yhtinfo['mht_id']);
        //媒介合同信息 关联产品线 获取产品线类型 （品专，华表）
        $mjhtinfo=JdContract::find()
            ->select('a.rebates_proportion,a.dl_fandian,a.product_line,b.fk_type')
            ->join("a left join","jd_product_line b","a.product_line=b.id")
            ->where(["a.id"=>$accountinfo['mht_id']])
            ->asArray()
            ->one();


        error_log('获取产品类型',3,$logfile);
        foreach ($backmoney as $key=>$value)
        {
            //查询续费欠额
            $xufei_info=self::findOne($xf_id);
            //如果续费欠额为0则跳出本次循环
            if($xufei_info['xf_qiane']==0)
            {
                break;
            }
            //已回款续费数据ready
            //销售提成
            $xs_fandian=0;
            if($yhtinfo['contract_state']=='1')
            {
                $xs_fandian=18;
            }elseif($yhtinfo['contract_state']=='2')
            {
                $xs_fandian=5;
            }elseif($yhtinfo['contract_state']=='3')
            {
                $xs_fandian=8;
            }elseif($yhtinfo['contract_state']=='3')
            {
                $xs_fandian=2;
            }

            $yixufeihuikuan_date= new Yihuikuanxufei();
            $yixufeihuikuan_date->mt_fandian=$mjhtinfo['rebates_proportion'];
            $yixufeihuikuan_date->dl_fandian=$mjhtinfo['dl_fandian'];
            $yixufeihuikuan_date->xf_fandian=$xufei_info['rebates_proportion']?$xufei_info['rebates_proportion']:0;

            $yixufeihuikuan_date->gr_fandian=0;
            $yixufeihuikuan_date->xs_fandian=$xs_fandian;
            $yixufeihuikuan_date->avid=$xufei_info['advertiser'];
            //销售
            $yixufeihuikuan_date->xsid=$yhtinfo['market'];
            $yixufeihuikuan_date->xf_id=$xf_id;

            $yixufeihuikuan_date->ht_id=$xufei_info['xf_contractid'];

            $xf_fd=(intval($xufei_info['rebates_proportion'])+100)/100;


            if($value['payment_type']=='15'){
                //退款账户详情
                $tkaccountinfo=Account::findOne($value['id']);
                //获取媒体合同
                $tkmjhtinfo=JdContract::find()
                    ->select('a.rebates_proportion,a.dl_fandian,a.product_line,b.fk_type')
                    ->join("a left join","jd_product_line b","a.product_line=b.id")
                    ->where(["a.id"=>$tkaccountinfo['mht_id']])
                    ->asArray()
                    ->one();

                $tk_fd=(intval($value['rebates_proportion'])+100)/100;
            }




            //如果这笔续费是退款到客户或者是转款则返点是回款或者退款到总账户
            if($xufei_info['payment_type']=='14' or  $xufei_info['payment_type']=='16' )
            {
                $yixufeihuikuan_date->xf_fandian=$value['rebates_proportion']?$value['rebates_proportion']:0;
                $xf_fd=($yixufeihuikuan_date->xf_fandian+100)/100;
            }

            //如果回款金额 大于 续费金额 （余额大于续费金额）停止循环并返回续费欠额为0
            if($value['backmoney_yue']-$xufei_info['xf_qiane']>0) {
                //设置回款余额
                self::updateAll(['backmoney_yue'=>$value['backmoney_yue']-$xufei_info['xf_qiane']],['id'=>$value['id']]);
                error_log('修改回款余额',3,$logfile);
                //增加已回款续费记录
                $yixufeihuikuan_date->hk_id = $value['id'];
                //续费时间和回款时间谁大取谁
                if($xufei_info['payment_time']>strtotime($xufei_info['last_time'])){
                    $xufei_info['last_time']=date("Y-m-d h:i:s",$xufei_info['payment_time']);
                }


                if ($value['last_time'] >= $xufei_info['last_time']) {
                    //$yixufeihuikuan_date->time = $value['payment_time'];
                    $yixufeihuikuan_date->time=strtotime($value['last_time']);
                } else {
                   // $yixufeihuikuan_date->time = $xufei_info['payment_time'];
                    $yixufeihuikuan_date->time=strtotime($xufei_info['last_time']);
                }
                //已回款续费金额字段值
                $yixufeihuikuan_date->money = $xufei_info['xf_qiane'];
                //产品线 类型 判断实付金额
                /*
                if ($mjhtinfo['fk_type'] == '0') {
                    $shifu=($yixufeihuikuan_date->money*$xf_fd)/(($mjhtinfo['rebates_proportion']+100)/100)*((100-$mjhtinfo['dl_fandian'])/100);
                }else
                {
                    $shifu=$xufei_info['xf_cost']/$xufei_info['money']*$xufei_info['xf_qiane'];
                }*/
                $shifu=$xufei_info['xf_cost']/$xufei_info['money']*$xufei_info['xf_qiane'];
                //实付金额
                $yixufeihuikuan_date->shifu_money=$shifu;




                //如果是退款计算退款实付
                if($value['payment_type']=='15')
                {
                    /*
                    //产品线 类型 判断实付金额
                    if ($tkmjhtinfo['fk_type'] == '0') {
                        $tkshifu=($yixufeihuikuan_date->money*$tk_fd)/(($tkmjhtinfo['rebates_proportion']+100)/100)*((100-$tkmjhtinfo['dl_fandian'])/100);
                    }else
                    {
                        $tkshifu=$value['xf_cost']/$value['money']*$xufei_info['xf_qiane'];
                    }
                    */
                    $tkshifu=$value['xf_cost']/$value['money']*$xufei_info['xf_qiane'];
                    $yixufeihuikuan_date->tk_shifu=$tkshifu;
                }


                //添加已回款续费
                if(!$yixufeihuikuan_date->save())
                {
                    var_dump($yixufeihuikuan_date->errors);exit;
                    throw new NotFoundHttpException('新增已回款续费失败');
                }
                error_log('添加已续费回款',3,$logfile);
                //修改续费欠额
                self::updateAll(['xf_qiane'=>0],['id'=>$xf_id]);
                error_log('修改续费欠额',3,$logfile);
                return;
            }else
            {
                //如果回款余额 小于 续费金额  （此回款余额不足以抹平此续费）则继续执行循环 并把此回款的余额为0
                if($xufei_info['payment_time']>strtotime($xufei_info['last_time'])){
                    $xufei_info['last_time']=date("Y-m-d h:i:s",$xufei_info['payment_time']);
                }
                //增加已回款续费记录
                $yixufeihuikuan_date->hk_id=$value['id'];
                //续费时间和回款时间谁大取谁
                if ($value['last_time'] >= $xufei_info['last_time']) {
                    //$yixufeihuikuan_date->time = $value['payment_time'];
                    $yixufeihuikuan_date->time=strtotime($value['last_time']);
                } else {
                    //$yixufeihuikuan_date->time = $xufei_info['payment_time'];
                    $yixufeihuikuan_date->time=strtotime($xufei_info['last_time']);
                }
                //已回款续费金额字段值
                $yixufeihuikuan_date['money']=$value['backmoney_yue'];
                //产品线 类型 判断实付金额
                /*
                if ($mjhtinfo['fk_type'] == '0') {
                    $shifu=($yixufeihuikuan_date['money']*$xf_fd)/(($mjhtinfo['rebates_proportion']+100)/100)*((100-$mjhtinfo['dl_fandian'])/100);
                }else
                {
                    $shifu=$xufei_info['xf_cost']/$xufei_info['money']*$yixufeihuikuan_date['money'];
                }*/
                $shifu=$xufei_info['xf_cost']/$xufei_info['money']*$yixufeihuikuan_date['money'];
                //实付金额
                $yixufeihuikuan_date->shifu_money=$shifu;



                //如果是退款计算退款实付
                if($value['payment_type']=='15')
                {

                    /*
                    //产品线 类型 判断实付金额
                    if ($tkmjhtinfo['fk_type'] == '0') {
                        $tkshifu=($yixufeihuikuan_date['money']*$tk_fd)/(($tkmjhtinfo['rebates_proportion']+100)/100)*((100-$tkmjhtinfo['dl_fandian'])/100);
                    }else
                    {
                        $tkshifu=$value['xf_cost']/$value['money']*$yixufeihuikuan_date['money'];
                    }*/
                    $tkshifu=$value['xf_cost']/$value['money']*$yixufeihuikuan_date['money'];
                    $yixufeihuikuan_date->tk_shifu=$tkshifu;
                }



                //添加已回款续费
                if(!$yixufeihuikuan_date->save())
                {
                    var_dump($yixufeihuikuan_date->errors);exit;
                    throw new NotFoundHttpException('新增已回款续费失败');
                }
                error_log('添加已续费回款',3,$logfile);
                //设置回款余额

                self::updateAll(['backmoney_yue'=>0],['id'=>$value[id]]);
                error_log('修改回款余额',3,$logfile);
                // self::
                //修改续费欠额
                self::updateAll(['xf_qiane'=>$xufei_info['xf_qiane']-$value['backmoney_yue']],['id'=>$xf_id]);
                error_log('修改续费欠额',3,$logfile);
            }
        }
    }
    //续费驳回 续费ID几级审核
    public static function renew_shenhe($id,$audit_type,$audit=''){
        if($audit!='')
        {
            $audit=1; //审核状态 1通过 2 驳回
        }else
        {
            $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        }

        $xfinfo=self::findOne($id);//续费详情
        //账户信息
        $accountinfo=Account::findOne($xfinfo['account']);
        //如果驳回了
        if($audit=='2')
        {
            //回款回款
            //写入金额变动
            self::money_change_jian($xfinfo->advertiser,$xfinfo->xf_contractid,$xfinfo->payment_type,$xfinfo->money);
            $yihuikuanxufei=Yihuikuanxufei::find()->where(["xf_id"=>$id])->asArray()->all();
            foreach ($yihuikuanxufei as $key=>$val)
            {
                self::updateAllCounters(['backmoney_yue'=>$val['money']],['id'=>$val['hk_id']]);
                self::updateAll(['xf_qiane'=>$xfinfo['money']],['id'=>$val['xf_id']]);
                $Y = Yihuikuanxufei::findOne($val['id']);
                $Y->delete();
            }


            //判断回款是否与续费一一对应 如果是 则删除此比分配款 is_tk_deduction
            $tk_deduction=self::find()->where(['relevance_id'=>$id,'is_huikuan'=>'1','is_tk_deduction'=>'1'])->one();
            if($tk_deduction)
            {
                //合同回款减少
                JdContract::updateAllCounters(['huikuan'=>-$tk_deduction['money']],['id'=>$tk_deduction['xf_contractid']]);
                //公司未分配余额减少
                JdCustomer::updateAllCounters(['tuikuan_undistributed_yu_e'=>$tk_deduction['money']],['id'=>$tk_deduction['advertiser']]);
                self::delete($tk_deduction['id']);
            }



        }

        //查询续费需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'renew'])->one();
        //如果是下单就读取下单审核
        $isxiadan=RenewOrder::find()->where(['xf_id'=>$id])->one();
        if($isxiadan)
        {
            $renew_shenhe=AuditAction::find()->where(['action_name'=>'xiadan'])->one();
        }

        //续费续费成功 并且全部审核已经通过 则复制一条媒介续费记录
        if($audit=='1' and $audit_type==$renew_shenhe['audit_count'])
        {
            if($accountinfo['mht_id']=='')
            {
                throw new \Exception('没有媒介合同.请选择完媒介合同再重试此操作');
            }

            $mrenew_model=new MrenewHuikuan();
            $mrenew_model->advertiser=$xfinfo['advertiser'];
            $mrenew_model->submituser=$xfinfo['submituser'];
            $mrenew_model->type=1;//1续费 2 打款
            $mrenew_model->xf_contractid=$xfinfo['xf_contractid'];
            $mrenew_model->market=$xfinfo['market'];
            $mrenew_model->account=$xfinfo['account'];
            $mrenew_model->appname=$xfinfo['appname'];
            //金额改为百度币
            $mrenew_model->money=$xfinfo['show_money'];
            //获取媒体合同信息

            $meiinfo=JdContract::findOne($accountinfo->mht_id);
            //获取媒介返点算出实付金额

            $mrenew_model->xf_qiane=$xfinfo['xf_cost']-$fk_money; //续费欠额
            $mrenew_model->xf_cost=$xfinfo['xf_cost']; //续费实付
            $mrenew_model->rebates_proportion=$meiinfo['rebates_proportion'];//媒介合同返点
            $mrenew_model->mht_id=$meiinfo['id'];//媒介合同id

            $mrenew_model->show_money=$xfinfo['show_money'];
            $mrenew_model->payment_type=$xfinfo['payment_type'];
            $mrenew_model->payment_time=$xfinfo['payment_time'];
            $mrenew_model->note='';
            $mrenew_model->contract_start=$xfinfo['contract_start'];
            $mrenew_model->contract_end=$xfinfo['contract_end'];
            $mrenew_model->ctime=time();
            $mrenew_model->audit_u=$xfinfo['audit_u'];
            $mrenew_model->audit=1;//1级默认审核通过
            $mrenew_model->audit_count=2;//2级默认审核通过
            $mrenew_model->xf_id=$xfinfo['id'];
            $mrenew_model->is_ultimate_shenhe=1;

            if(!$mrenew_model->save())
            {
                die("出现严重错误，媒介续费记录自动生成失败。请联系CRM 管理员");
            }else
            {

                //var_dump($meiinfo);exit;
               // echo $meiinfo['mht']['advertiser'].'=-='.$meiinfo['mht']['id'];exit;
                //公司总续费加
                MrenewHuikuan::money_change($meiinfo['advertiser'],$meiinfo['id'],1,$xfinfo['xf_cost']);


                //如果勾选了备款账户
                $beikuan_account_id=Yii::$app->request->post('beikuan_account_id');

                if($beikuan_account_id!=''){
                    //判断账户余额是否足以抵消续费续费
                    $bk_ac_info=BeikuanAccount::findOne($beikuan_account_id);
                    if($bk_ac_info->yu_e < $mrenew_model->xf_cost)
                    {
                       // throw new \Exception('不足以抵消！');
                    }

                    //抵扣金额
                    $fk_money=Yii::$app->request->post('fk_money');
                    //插入数据
                    $ba_t_ren= new BeikuanAccountToRenew();
                    $ba_t_ren->bk_account_id=$beikuan_account_id;
                    $ba_t_ren->xf_id=$mrenew_model->id;
                    $ba_t_ren->fk_money=$fk_money;
                    $ba_t_ren->save();




                    //如果备款账户选择了一直关联
                    $beiluan_account_gl=Yii::$app->request->post('beikuan_account_gl');
                    if($beiluan_account_gl=='1')
                    {
                        //读取账户名字
                        $a_users=Account::findOne($mrenew_model->account);

                        //如果数据库里已经关联过这个账户就不新建啦
                        $bglcont=BeikuanAccountToRenewAccount::find()->where(['bk_account_id'=>$beikuan_account_id,'account'=>$a_users])->count();
                        if($bglcont==0){

                        $ba_gl=new BeikuanAccountToRenewAccount();
                        $ba_gl->bk_account_id=$beikuan_account_id;
                        $ba_gl->account=$a_users->a_users;
                        $ba_gl->save();
                        }
                    }

                    //续费欠额减
                    MrenewHuikuan::updateAllCounters(['xf_qiane'=>-$fk_money],['id'=>$mrenew_model->id]);


                    //备款账户减续费金额
                    BeikuanAccount::updateAllCounters(['yu_e'=>-$fk_money],['id'=>$beikuan_account_id]);
                    //媒介公司 和 合同备款余额jian
                    $mhtinfo=JdContract::findOne($mrenew_model->mht_id);
                    JdCustomer::updateAllCounters(['beikuan_yue'=>-$fk_money],['id'=>$mhtinfo->advertiser]);
                    JdContract::updateAllCounters(['beikuan_yue'=>-$fk_money],['id'=>$mhtinfo->id]);



                }

                //新-续费对应打款
               //--MrenewHuikuan::mrenew_huikuan($meiinfo['mht']['id']);
            }


            //修改为最终审核
            $xfinfo->is_ultimate_shenhe=1;
            $xfinfo->save();
            //执行账户总续费变更
            \Yii::$app->hjd->updateaccountpay($xfinfo['account']);
        }


    }







//公司下所有欠额的续费
public static function qiane_renew($avid){
    return self::find()->where(['advertiser'=>$avid,'payment_type'=>[1,2]])->andWhere(['<>',"audit","2"])->andWhere(['>','xf_qiane',0])->orderBy('ctime asc')->all();
}


//退款列表
public static function tuikuan_list($id='',$type,$t_account_type='',$shenhewhere2='',$role=''){

    //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

    if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
    {
        $uid=Yii::$app->user->getId();
        $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];
        if(Yii::$app->request->post('qudao')=='1')
        {
            $uid=Yii::$app->user->getId();
            $avidin=QudaoToAdvertiser::find()->select('advertiser_id')->where(['user_id'=>$uid])->column();
            $userswhere=['or',['ad.id'=>$avidin]];
        }
    }elseif (Yii::$app->hjd->list_type($role)==2)
    {
        $uid=Yii::$app->hjd->asbumen();
        $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];

    }elseif (Yii::$app->hjd->list_type($role)==3)
    {
        $userswhere=['>','jd_renew_huikuan.id',0];
    }

    //筛选条件
    $Search_type=Yii::$app->request->post('Search_type');
    $Search_str=Yii::$app->request->post('Search_str');

    $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_renew_huikuan.appname' ,$Search_str],['like','ac.a_users' ,$Search_str],['like','subt.name',$Search_str]];
    //日期筛选条件
    $start=strtotime(Yii::$app->request->post('start_date'));
    $end=strtotime(Yii::$app->request->post('end_date') ."+1 day");

    if($start and $end)
    {
        if(Yii::$app->request->post('date_type')!='2') {
            $search_data = ['and', ['>=', 'jd_renew_huikuan.payment_time', $start], ['<', 'jd_renew_huikuan.payment_time', $end]];
        }else{
            $search_data = ['and', ['>=', 'jd_renew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_renew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
        }

    }else
    {
        $search_data=['and',['>','jd_renew_huikuan.payment_time',1]];
    }


    //审核条件
    $shenhe=Yii::$app->request->post('shenhe');

    if($shenhe=='1')
    {
        $shenhewhere="jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=1";
    }elseif($shenhe=='0')
    {
        $shenhewhere="jd_renew_huikuan.audit=0 or jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=0";
    }elseif($shenhe=='2')
    {
        $shenhewhere="jd_renew_huikuan.audit=2";
    }else
    {
        $shenhewhere="jd_renew_huikuan.id>0";
    }
    $shenhewhere.=$shenhewhere2;
    //下单条件
    if(Yii::$app->request->post('xiadan')==1)
    {
        $ordeid=RenewOrder::find()->select('xf_id')->column();
    }else
    {
        $ordeid=null;
    }



    //完成状态
    $is_accomplish=Yii::$app->request->post("is_accomplish");


    //公司名称单独搜索
    $search_adname=Yii::$app->request->post('search_adname');


    if($type=='15'){
        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_renew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,mht.state as mstate,cuad.advertiser as guanggaozhu,cuadrele.product as product')
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac'])
            ->leftJoin("jd_contract_relevance contre","jd_renew_huikuan.xf_contractid=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_contract ht','ht.id=contre.contract_id')
            ->leftJoin('jd_contract mht','mht.id=ac.mht_id')
            ->leftJoin('customer_advertiser cuad','ac.cu_ad_id=cuad.id')
            ->leftJoin('customer_advertiser_releveance cuadrele','ac.cu_ad_product=cuadrele.id')
            ->where("jd_renew_huikuan.payment_type in($type)")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['is_accomplish'=>$is_accomplish])
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.id'=>$ordeid])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['like','ad.advertiser',$search_adname])
            ->asArray();

    }elseif($type=='14')
    {
        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_renew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,mht.state as mstate,cuad.advertiser as guanggaozhu')
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac'])
            ->leftJoin("jd_contract_relevance contre","jd_renew_huikuan.xf_contractid=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_contract ht','ht.id=contre.contract_id')
            ->leftJoin('jd_contract mht','mht.id=ac.mht_id')
            ->leftJoin('customer_advertiser cuad','jd_renew_huikuan.cu_ad_id=cuad.id')
            ->where("jd_renew_huikuan.payment_type in($type)")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['is_accomplish'=>$is_accomplish])
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.id'=>$ordeid])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['like','ad.advertiser',$search_adname])
            ->asArray();
    }

    $provider = new ActiveDataProvider([
        'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
        'sort' => [
            'defaultOrder' => [
                'ctime' => SORT_DESC,
                //'title' => SORT_ASC,
            ]
        ],
    ]);
    // 获取分页和排序数据
    $ar['data'] = $provider->getModels();
    // 在当前页获取数据项的数目
    $ar['page-count'] = $provider->getCount();

    // 获取所有页面的数据项的总数
    $ar['totalCount'] = $provider->getTotalCount();





    return $ar;
}

//退款到客户审核操作
public static function tkehu_shenhe($id,$audit_type){
    $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
    $xfinfo=self::findOne($id);//退款详情

    //如果成功了
    //查询退款需要几级审核
    $renew_shenhe=AuditAction::find()->where(['action_name'=>'refund_kehu'])->one();
    //如果最终审核通过
    if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] ) {
        //修改为最终审核
        $xfinfo->is_ultimate_shenhe=1;
        $xfinfo->save();
    }

    //如果驳回了
    if($audit=='2')
    {

        //回款回款
        //写入金额变动
        self::money_change($xfinfo->advertiser,$xfinfo->xf_contractid,$xfinfo->payment_type,$xfinfo->money);

        //回滚已回款续费 1包含本续费的回款 回滚 2，续费欠额回滚  3，已回款续费删除

        $yihuikuanxufei=Yihuikuanxufei::find()->where(["xf_id"=>$id])->asArray()->all();
        foreach ($yihuikuanxufei as $key=>$val)
        {
            //回款回滚
            self::updateAllCounters(['backmoney_yue'=>$val['money']],['id'=>$val['hk_id']]);
            //续费回滚
            self::updateAll(['xf_qiane'=>$xfinfo['money']],['id'=>$val['xf_id']]);
            //删除已回款续费记录
            $Y = Yihuikuanxufei::findOne($val['id']);
            $Y->delete();
        }
        //续费回款对应重跑
        //--RenewHuikuan::renew_huikuan($xfinfo['xf_contractid']);

        //修改公司发票回款状态
        Invoice::up_invoice_state($xfinfo->advertiser);


    }
}


//退款到总账户审核操作
public static function account_shenhe($id,$audit_type,$audit_action,$audit=''){

    if($audit!='')
    {
        $audit=$audit; //审核状态 1通过 2 驳回
    }else{
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
    }

    $renew_info=self::findOne($id);//退款详情


    //查询退款需要几级审核
    $renew_shenhe=AuditAction::find()->where(['action_name'=>$audit_action])->one();
    //账户信息
    $account_info=Account::findOne($renew_info['account']);




    //查询媒体判定啊 是退款到备款账户还是退款到账户现金，如果是退款到备款账户就直接走完所有以下的流程--从父子流程里面取得
    $fzlc=Flow::find()->where(['fu_flow'=>'tuikuan','is_true'=>1])->all();
    foreach ($fzlc as $key=>$val)
    {
        if($val['zi_flow']=='meijie_tuikuan')
        {
            $flow_audit_count=$val['fu_audit']-1;
        }
    }



    //判断是否是退现金 如果有就该此退款是退现金的 1 直接退 2是等待媒体退款流程走完才能退
    $istxj=\Yii::$app->request->post('to_beikuanaccount');

    if($istxj=='0')
    {
        $renew_info->is_tuixianjin=1;
        //RenewHuikuan::updateAll(['is_tuixianjin'=>1],['id'=>$renew_info->id]);
    }



    //如果最终审核通过
    if(($audit=='1' && $audit_type==$renew_shenhe['audit_count']) || ($audit=='1' && $audit_type==$flow_audit_count))
    {



        //如果退款账户没选媒介合同。就报错
        $tkaccountinfo=Account::findOne($renew_info->account);
        if($tkaccountinfo['mht_id']=='')
        {
            throw new \Exception('没有媒介合同.请选择媒介合同');
        }

        //执行账户总续费变更
        \Yii::$app->hjd->updateaccountpay($renew_info->account);

        //如果是最终审核，并且是退款到现金的 就不生成媒介退款了 因为在之前已经生成过了
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'])
        {
            //修改公司续费值-减少
            self::money_change_jian($renew_info['advertiser'], $renew_info['xf_contractid'], 15, $renew_info['money']);
            //--self::renew_huikuan($renew_info['xf_contractid']);

            //修改为最终审核
            $renew_info->is_ultimate_shenhe = 1;

            $renew_info->save();
            //生成退款转出款并与转出款11对应
            self::shengcheng_tk_zhuanchu($id);
            return ;
            exit;
        }


        //如果是到备款账户则该此比退款为最终审核
        if($audit=='1' && $audit_type==$flow_audit_count && Yii::$app->request->post('to_beikuanaccount')=='1')
        {
            //如果是退款到备款账户之间审核通过
            $renew_info->audit_count=$renew_shenhe['audit_count'];
            //RenewHuikuan::updateAll(['audit_count'=>$renew_shenhe['audit_count']],['id'=>$renew_info->id]);
        }


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

        $data->dakuan_yue=0; //退款余额
        $data->xf_cost=$renew_info['xf_cost']; //续费实付
        $data->rebates_proportion=$meiinfo['rebates_proportion'];//媒介合同返点
        $data->mht_id=$meiinfo['id'];//媒介合同id

        $data->show_money=$renew_info['show_money'];
        $data->payment_type=15;
        $data->payment_time=$renew_info['payment_time'];
        $data->note='';
        $data->contract_start=$renew_info['contract_start'];
        $data->contract_end=$renew_info['contract_end'];
        $data->ctime=time();
        $data->users2=$renew_info['users2'];
        $data->audit=1;
        $data->audit_count=$meijietuishenhe->audit_count;//1级默认审核通过;//1级默认审核通过
        $data->audit_u=$renew_info['audit_u'];
        $data->is_ultimate_shenhe=1;
        $data->xf_id=$renew_info['id'];



        if($renew_info['is_tuixianjin']=='1')
        {
            $data->audit=0;
            $data->audit_count=1;//1级默认审核通过
            $data->is_ultimate_shenhe=0;
            $data->dakuan_yue=$renew_info['xf_cost']; //退款余额
        }

        //保存媒介信息
        if($data->save())
        {
            //如果选择了备款账户则修改媒介公司 与 合同的续费值减
            if(Yii::$app->request->post('to_beikuanaccount')=='1'){
                //修改媒介合同和媒介公司续费值
                MrenewHuikuan::money_change($meiinfo['advertiser'],$meiinfo['id'],1,$renew_info['xf_cost'],'jian');
                //重跑媒介续费对应
                //--MrenewHuikuan::mrenew_huikuan($meiinfo['mht']['id']);
                //退款对应消耗
                self::Tuikuan_cost_real($id);
            }else
            {
                //如果选择了等待媒体退完的逻辑 就生成父子对应关系
                if($renew_info['is_tuixianjin']=='1')
                {
                    //循环父子级别
                    foreach ($fzlc as $key=>$val)
                    {
                        if($val['zi_flow']=='meijie_tuikuan')
                        {
                            /*添加父子流程对应关系*/
                            $fzflow_dy=new FlowGl();
                            $fzflow_dy->fuid=$renew_info->id;
                            $fzflow_dy->ziid=$data->id;
                            $fzflow_dy->ctime=time();
                            $fzflow_dy->type=$val['id'];
                            $fzflow_dy->last_time=date('Y-m-d H:i:s');

                            if(!$fzflow_dy->save())
                            {
                                var_dump($fzflow_dy->errors);exit;
                            }
                        }
                    }
                }
            }

        }else
        {
            return false;
        }

        if(Yii::$app->request->post('to_beikuanaccount')=='1') {
            //修改公司续费值-减少
            self::money_change_jian($renew_info['advertiser'], $renew_info['xf_contractid'], 15, $renew_info['money']);
            //--self::renew_huikuan($renew_info['xf_contractid']);

            //修改为最终审核
            $renew_info->is_ultimate_shenhe = 1;

            //生成退款转出款并与转出款11对应
            self::shengcheng_tk_zhuanchu($id);

        }
        $renew_info->save();


        //如果最终审核通过并且选择了退款到备款账户
        if(Yii::$app->request->post('to_beikuanaccount')=='1')
        {
            $bk_account=Yii::$app->request->post('bk_account');
            $tk_beikuan=new Beikuan();
            $tk_beikuan->advertiser=$renew_info['advertiser'];
            $tk_beikuan->account=$bk_account;
            $tk_beikuan->money=$renew_info['show_money'];
            $tk_beikuan->show_money=$renew_info['show_money'];
            $tk_beikuan->payment_type=15;
            $tk_beikuan->payment_time=$renew_info['payment_time'];
            $tk_beikuan->mht_id=$meiinfo['id'];//媒介合同id
            $tk_beikuan->xf_contractid=$renew_info['xf_contractid'];
            $tk_beikuan->submituser=$renew_info['submituser'];
            $tk_beikuan->ctime=time();
            $tk_beikuan->audit=1;//1级默认审核通过
            $tk_beikuan->audit_count=1;//1级默认审核通过

            $tk_beikuan->audit_u=$renew_info['audit_u'];
            $tk_beikuan->is_ultimate_shenhe=1;
            $tk_beikuan->xf_id=$data->id;
            $tk_beikuan->dakuan_yue=0; //退款余额
            $tk_beikuan->xf_cost=$renew_info['xf_cost']; //续费实付
            $tk_beikuan->rebates_proportion=$meiinfo['rebates_proportion'];//媒介合同返点
            $tk_beikuan->note='退款生成的备款退款-crm';
            $tk_beikuan->save();

            //媒介公司 和 合同备款余额加
            JdCustomer::updateAllCounters(['beikuan_yue'=>$renew_info->xf_cost],['id'=>$meiinfo['advertiser']]);
            JdContract::updateAllCounters(['beikuan_yue'=>$renew_info->xf_cost],['id'=>$meiinfo['id']]);

            //备款账户余额加
            BeikuanAccount::updateAllCounters(['yu_e'=>$renew_info->xf_cost],['id'=>$bk_account]);
        }



    }
}


//退款到总账户审核操作
    public static function account_shenhe_zhuankuan($id,$audit_type,$audit_action,$audit=''){

        if($audit!='')
        {
            $audit=$audit; //审核状态 1通过 2 驳回
        }else{
            $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        }

        $renew_info=self::findOne($id);//退款详情


        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>$audit_action])->one();
        //账户信息
        $account_info=Account::findOne($renew_info['account']);

        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {

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
                self::Tuikuan_cost_real($id);
            }else
            {
                return false;
            }

            //修改公司续费值-减少
            self::money_change_jian($renew_info['advertiser'],$renew_info['xf_contractid'],15,$renew_info['money']);
            //--self::renew_huikuan($renew_info['xf_contractid']);

            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            $renew_info->save();

            //生成退款转出款并与转出款11对应
            self::shengcheng_tk_zhuanchu($id);

            //如果最终审核通过并且选择了退款到备款账户
            if(Yii::$app->request->post('to_beikuanaccount')=='1')
            {
                $bk_account=Yii::$app->request->post('bk_account');
                $tk_beikuan=new Beikuan();
                $tk_beikuan->advertiser=$renew_info['advertiser'];
                $tk_beikuan->account=$bk_account;
                $tk_beikuan->money=$renew_info['show_money'];
                $tk_beikuan->show_money=$renew_info['show_money'];
                $tk_beikuan->payment_type=15;
                $tk_beikuan->payment_time=$renew_info['payment_time'];
                $tk_beikuan->mht_id=$meiinfo['id'];//媒介合同id
                $tk_beikuan->xf_contractid=$renew_info['xf_contractid'];
                $tk_beikuan->submituser=$renew_info['submituser'];
                $tk_beikuan->ctime=time();
                $tk_beikuan->audit=1;//1级默认审核通过
                $tk_beikuan->audit_count=1;//1级默认审核通过
                $tk_beikuan->audit_u=$renew_info['audit_u'];
                $tk_beikuan->is_ultimate_shenhe=1;
                $tk_beikuan->xf_id=$data->id;
                $tk_beikuan->dakuan_yue=$renew_info['xf_cost']; //退款余额
                $tk_beikuan->xf_cost=$renew_info['xf_cost']; //续费实付
                $tk_beikuan->rebates_proportion=$meiinfo['rebates_proportion'];//媒介合同返点
                $tk_beikuan->note='退款生成的备款退款-crm';
                $tk_beikuan->save();

                //媒介公司 和 合同备款余额加
                JdCustomer::updateAllCounters(['beikuan_yue'=>$renew_info->xf_cost,],['id'=>$meiinfo['advertiser']]);
                JdContract::updateAllCounters(['beikuan_yue'=>$renew_info->xf_cost,],['id'=>$meiinfo['id']]);

                //备款账户余额加
                BeikuanAccount::updateAllCounters(['yu_e'=>$renew_info->xf_cost],['id'=>$bk_account]);
            }

        }
    }




//退款详情
public static function tuikuaninfo($id){
    //具体数据
    $data=self::find()->where(['id'=>$id])->With(['submituser0','account0','advertiser0','audituname'])->asArray()->one();





    if($data['payment_type']=='15'){
        $contarat_info=JdContract::contractinfo_i($data['account']);


        //account关联广告主及产品信息
        $account_cu_ad_info=Account::find()->select("cuad.advertiser as guanggaozhu,cuadrele.product as product")->join(' ac left join','customer_advertiser cuad','ac.cu_ad_id=cuad.id')->leftJoin('customer_advertiser_releveance cuadrele','ac.cu_ad_product=cuadrele.id')->where(['ac.id'=>$data['account']])->asArray()->one();
    }elseif($data['payment_type']=='14')
    {
        $contarat_info=JdContract::contractinfonomt($data['xf_contractid']);
        $account_cu_ad_info=RenewHuikuan::find()->select("cuad.advertiser as guanggaozhu")->join(' a left join','customer_advertiser cuad','a.cu_ad_id=cuad.id')->where(['a.id'=>$data['id']])->asArray()->one();

    }
    $data['contractinfo']=$contarat_info;
    $data['cu_ad_info']=$account_cu_ad_info;


    return $data;
}



//补款列表
    public static function bukuan_list($id='',$shenhewhere2='',$role=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_renew_huikuan.id',0];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_renew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date') . "+1 day");

        if($start and $end)
        {
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_renew_huikuan.payment_time', $start], ['<', 'jd_renew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_renew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_renew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }

        }else
        {
            $search_data=['and',['>','jd_renew_huikuan.payment_time',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');
        if($shenhe=='1')
        {
            $shenhewhere="jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_renew_huikuan.audit=0 or jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_renew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_renew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;
        //完成状态
        $is_accomplish=Yii::$app->request->post("is_ultimate_shenhe");


        //查询自己的组
        /*
        $query=self::find()
            ->select('ad.advertiser as adname,jd_renew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,mht.state as mstate,cuad.advertiser as guanggaozhu')
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac'])
            ->leftJoin("jd_contract_relevance contre","jd_renew_huikuan.xf_contractid=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_contract ht','ht.id=contre.contract_id')
            ->leftJoin('jd_contract mht','mht.id=ht.mht_id')
            ->leftJoin('customer_advertiser cuad','jd_renew_huikuan.cu_ad_id=cuad.id')
            ->where("jd_renew_huikuan.payment_type in(3,311)")
            ->andFilterWhere($userswhere)
            ->andWhere($shenhewhere)
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.is_ultimate_shenhe'=>$is_accomplish])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->asArray();

        */

        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser as adname,mhtad.advertiser as mhtad,jd_renew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,mht.rebates_proportion as mtfandian,mht.title as mhtname,cuad.advertiser as guanggaozhu,cuadrele.product as product')
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac','contract cont'])
            ->leftJoin("jd_contract_relevance contre","cont.id=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_contract mht','mht.id=ac.mht_id')
            ->leftJoin('jd_customer mhtad','mhtad.id=mht.advertiser')
            ->leftJoin('customer_advertiser cuad','ac.cu_ad_id=cuad.id')
            ->leftJoin('customer_advertiser_releveance cuadrele','ac.cu_ad_product=cuadrele.id')
            ->where('jd_renew_huikuan.payment_type in(3,311)')
            ->andWhere($shenhewhere)
            ->andWhere($userswhere)
            ->andFilterWhere(['ac.state'=>$account,'jd_renew_huikuan.is_ultimate_shenhe'=>$is_accomplish])
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.id'=>$ordeid])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['like','ad.advertiser',$search_adname])
            ->asArray();




        $ar['sum']=$query->sum('jd_renew_huikuan.money');
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();





        return $ar;
    }


    //补款详情
    public static function bukuan_info($id){
        //具体数据
        /*
        $data=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname'])->asArray()->one();

        $contarat_info=JdContract::contractinfo_i($data['xf_contractid']);

        $data['contractinfo']=$contarat_info;

        $account_cu_ad_info=RenewHuikuan::find()->select("cuad.advertiser as guanggaozhu")->join(' a left join','customer_advertiser cuad','a.cu_ad_id=cuad.id')->where(['a.id'=>$data['id']])->asArray()->one();
        $data['cu_ad_info']=$account_cu_ad_info;
        */

        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','account0','advertiser0','audituname','xiadan'])->asArray()->one();
        if($data['account'])
        {
            $contarat_info=JdContract::contractinfo($data['account']);
        }else
        {
            $contarat_info=JdContract::contractinfonomt($data['xf_contractid']);
        }

        //account关联广告主及产品信息
        $account_cu_ad_info=Account::find()->select("cuad.advertiser as guanggaozhu,cuadrele.product as product")->join(' ac left join','customer_advertiser cuad','ac.cu_ad_id=cuad.id')->leftJoin('customer_advertiser_releveance cuadrele','ac.cu_ad_product=cuadrele.id')->where(['ac.id'=>$data['account']])->asArray()->one();

        $data['cu_ad_info']=$account_cu_ad_info;
        //实付金额计算公式 2017年5月27日14:27:39
        $data['contractinfo']=$contarat_info;
        //查看续费详情
        if($data['pici']!='')
        {
            $pilist=self::find()->select(['id','money','show_money','payment_time','xf_cost'])->where("pici='$data[pici]'")->orderBy('id asc')->asArray()->all();
            $picimoney_sum=0;
            $picishowmoney_sum=0;
            foreach ($pilist as $pv)
            {
                $picimoney_sum+=$pv['money'];
                $picishowmoney_sum+=$pv['show_money'];
            }
            $data['picilist']=$pilist;
            $data['picimoney_sum']=$picimoney_sum;
            $data['picishowmoney_sum']=$picishowmoney_sum;
        }


        return $data;

        return $data;
    }


    //新增补款
    public static function add_bukuan()
    {
        $model = new RenewHuikuan();
        $postdata = \Yii::$app->request->post();
        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        $model->payment_time = strtotime($model->payment_time);
        $model->ctime = time();
        $model->payment_type=3;
        $model->backmoney_yue = $postdata['money'];
        $model->cu_ad_id=$postdata['cu_ad_id'];
        //合同信息
        $contract_info=JdContract::contractinfonomt($model->xf_contractid);
        $model->ht_state=$contract_info['contract_state'];//合同状态

        if ($model->save()) {
            return $model;
        }else{
            return $model->errors;
        }
    }

    //补款审核操作
    public static function bukuan_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $renew_info=self::findOne($id);//退款详情

        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'bukuan'])->one();


        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {
            //修改公司 合同补款值 （增）
            self::money_change($renew_info['advertiser'],$renew_info['xf_contractid'],3,$renew_info['money']);
            //--self::renew_huikuan($renew_info['xf_contractid']);

            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            $renew_info->save();
        }
    }









    /*
     *
     * TV 统计 移植   2017-11-24 09:36:04
     *
     *
     *
     * */


    //续费排名
    public static function xufei_rank($number,$strat='',$end='',$ourMain=''){
        //如果有时间筛选条件
        if($strat!='default' && $end!='default')
        {
            $andwhere=" a.payment_time >=$strat and a.payment_time<$end";
        }else
        {
            $andwhere=" a.id!=0";
        }
        //如果有公司筛选条件
        if($ourMain!='default' and is_numeric($ourMain))
        {
            $andwhere.=" and ht.agent_company=$ourMain";
        }


        $list=parent::find()
            ->select('a.advertiser,sum(a.money) as money,cu.advertiser as adname,dlgs.companyname,Row_Number() OVER (ORDER BY sum (a.money) desc) rank')//->sum('money')
            ->join("a left join",'jd_customer cu','cu.id=a.advertiser')
            ->leftJoin("jd_contract ht","ht.id=a.xf_contractid")
            ->leftJoin("jd_agent_company dlgs","dlgs.id=ht.agent_company")
            ->where([
                'a.is_ultimate_shenhe'=>'1',
                'a.payment_type'=>[1,2]
            ])
            ->andWhere($andwhere)
            ->groupBy("a.advertiser,adname,companyname")
            ->orderBy('money desc')
            ->limit($number)
            ->asArray()
            ->all();


        return $list;
    }


    //总续费按月份统计
    public static function xufei_timedate($startTime,$endTime,$ourMain,$cuid='',$prlin='')
    {


        //按公司主体筛选
        if($ourMain!='default' && is_numeric($ourMain)) {
            $andwhere=" ht.agent_company=$ourMain";
        }else
        {
            $andwhere=" a.id!=0";
        }

        if($cuid!='')
        {
            $andwhere2=' and a.advertiser='.$cuid;
        }

        if($prlin!='')
        {
            $andwhere3=' and htre.product_line='.$prlin;
        }

        //echo $startTime; echo'-'.$endTime.'!';
        $one= parent::find()
            ->select(['sum(a.money) as money'])
            ->join("a left join","jd_contract ht","a.xf_contractid=ht.id")
            ->leftJoin("jd_agent_company dlgs","dlgs.id=ht.agent_company")
            ->leftJoin("jd_contract_relevance htre",'htre.contract_id=ht.id')
            ->where([
                'a.is_ultimate_shenhe'=>'1',
                'a.payment_type'=>[1,2]
            ])
            ->andWhere(['>=','a.payment_time',$startTime])
            ->andWhere(['<','a.payment_time',$endTime])
            ->andWhere($andwhere.$andwhere2.$andwhere3)
            ->one();
        $a=clone $one;


        return $one['money']?$one['money']:'0';

    }



    //利润排名 实际

    public static function xufei_profits($number,$strat='',$end='',$ourMain='',$cuid='',$prlin=''){
        //如果有时间筛选条件
        if($strat!='default' && $end!='default')
        {
            $andwhere=" and a.payment_time >=$strat and a.payment_time<$end";
        }else
        {
            $andwhere=" a.id!=0";
        }

        //如果有公司筛选条件
        if($ourMain!='default' and is_numeric($ourMain))
        {
            $andwhere.=" and ht.agent_company=$ourMain";
        }
        //如果有客户id的话就只返回这个客户的
        if($cuid!='')
        {
            $andwhere2="  quan.advertiser=$cuid";
        }else
        {
            $andwhere2="  quan.advertiser!=0";
        }
        //如果有合同条件就只返回这个产品线的
        if($prlin!='')
        {
            $andwhere.=" and htre.product_line=$prlin";
        }

        $sql="SELECT
	lirun.advertiser,
	(lirun.money - lirun.xf_cost) AS liruns,
	lirun.adname,
	lirun.companyname,
	lirun.submituser,
	coalesce(tuikuan.tuikuan,0) as tuikuan_cost,
	coalesce(tuikuan.tuikmoney,0) as tuikuan_money,
	coalesce(tuikuan.tuikmoney-tuikuan.tuikuan,0) as tuikuan,
	coalesce(bukuan.bukuan,0) as bukuan
FROM
	(
		SELECT
			A .advertiser,
			SUM (A .money) AS money,
			SUM (A .xf_cost) AS xf_cost,
			cu.advertiser AS adname,
			cu.submituser,
			dlgs.companyname
		FROM
			jd_renew_huikuan A
		LEFT JOIN jd_customer cu ON cu. ID = A .advertiser
		LEFT JOIN jd_contract ht ON ht. ID = A .xf_contractid
		LEFT JOIN jd_agent_company dlgs ON dlgs. ID = ht.agent_company
		LEFT JOIN jd_contract_relevance htre on htre.contract_id=ht.id
		WHERE
			a.is_ultimate_shenhe=1 
			and xf_cost >0
		AND A .payment_type IN (1, 2)
	   ".$andwhere."
		GROUP BY
			A .advertiser,
			adname,
			companyname,
			cu.submituser
	) lirun left join (select sum(a.money) as tuikmoney,sum(a.xf_cost) as tuikuan,a.advertiser from jd_renew_huikuan a left join jd_contract ht on ht.id=a.xf_contractid LEFT JOIN jd_contract_relevance htre on htre.contract_id=ht.id where a.payment_type='15' and a.is_ultimate_shenhe=1 ".$andwhere." GROUP BY a.advertiser) tuikuan on lirun.advertiser=tuikuan.advertiser left join (select sum(a.money) as bukuan,a.advertiser from jd_renew_huikuan a left join jd_contract ht on ht.id=a.xf_contractid LEFT JOIN jd_contract_relevance htre on htre.contract_id=ht.id where a.payment_type='3'  and a.is_ultimate_shenhe=1 ".$andwhere." GROUP BY a.advertiser) bukuan on lirun.advertiser=bukuan.advertiser";

        $sqlone='select quan.advertiser as avid,quan.liruns,quan.bukuan,quan.advertiser,quan.adname as advertiser,quan.companyname,quan.bukuan,quan.tuikuan_money,quan.tuikuan,quan.tuikuan_cost,quan.submituser,uu.name,(quan.liruns-quan.bukuan-quan.tuikuan) as lirun,Row_Number() OVER (ORDER BY (quan.liruns-quan.bukuan-quan.tuikuan) desc) rank from ('.$sql.')  quan left join user_backend uu on uu.id=quan.submituser where '.$andwhere2.' order by lirun desc limit '.$number;
        $lrlist=parent::findBySql($sqlone)->asArray()->all();



        return $lrlist;
    }


    //利润趋势图 实际
    public static function lirun_timedate($startTime,$endTime,$ourMain,$cuid='',$prlin='')
    {

        $money = 0;
        $lirunlist = self::xufei_profits(10000, $startTime, $endTime, $ourMain,$cuid,$prlin);

        foreach ($lirunlist as $val) {
            $money += $val['lirun'];
        }
        return $money;
    }




    //续费实际利润 --已回款
    public static function xufei_true_profits($number,$strat='',$end='',$ourMain='',$cuid='',$prlin='')
    {






        //如果有时间筛选条件
        if ($strat != 'default' && $end != 'default') {
            $andwhere = " and a.payment_time >=$strat and a.payment_time<$end";
            $t=" and a.time>=$strat and a.time<$end";
        } else {
            $andwhere = "and id!=0";
            $t=' and a.id !=0';
        }
        //如果有公司筛选条件
        if ($ourMain != 'default' and is_numeric($ourMain)) {
            $andwhere .= " and ht.agent_company=$ourMain";
        }

        //如果有客户id的话就只返回这个客户的
        if($cuid!='')
        {
            $andwhere2="  avid=$cuid";
        }else
        {
            $andwhere2="  avid!=0";
        }
        //如果有产品线条件的话
        if($prlin!='')
        {
            $andwhere = " and htre.product_line=$prlin";
            $t=" and htre.product_line=$prlin";
        }





        $sql='SELECT
	SUM (A .money) AS money,
	SUM (A .shifu_money) AS shifu_money,
	coalesce(adbukuan.buk,0) as buk,
	A.avid,
	b.advertiser,
	coalesce((adtuikuan.tmon-adtuikuan.tuik),0) as tuikuan
FROM
	jd_yihuikuanxufei A
LEFT JOIN jd_customer b ON A .avid = b. ID
left join jd_renew_huikuan c on a.xf_id=c.id 
left join jd_contract ht on ht.id=c.xf_contractid
left join jd_contract_relevance htre on htre.contract_id=ht.id
left join (select sum(a.money) as buk,a.advertiser from jd_renew_huikuan a left join jd_contract ht on ht.id=a.xf_contractid left join jd_contract_relevance htre on htre.contract_id=ht.id where a.payment_type=3  and a.is_ultimate_shenhe=1 '.$andwhere.'  GROUP BY a.advertiser) adbukuan on a.avid=adbukuan.advertiser
left join (select sum(a.money) as tmon,sum(a.xf_cost) as tuik,a.advertiser  from jd_renew_huikuan a left join jd_contract ht on ht.id=a.xf_contractid left join jd_contract_relevance htre on htre.contract_id=ht.id where a.payment_type=15   '.$andwhere.'  GROUP BY a.advertiser) adtuikuan on a.avid=adtuikuan.advertiser
where c.payment_type in(1,2) '.$t.' 
group BY
	A.avid,
	b.advertiser,
	adbukuan.buk,
	adtuikuan.tuik,
	adtuikuan.tmon
	';

        $sqlone='select money,shifu_money,buk,avid,advertiser,tuikuan,(money-buk-shifu_money-tuikuan) as lirun,Row_Number() OVER (ORDER BY (money-buk-shifu_money-tuikuan) desc) rank from ('.$sql.')  quan where '.$andwhere2.' order by lirun desc limit '.$number;
        $lrlist=parent::findBySql($sqlone)->asArray()->all();
        return $lrlist;
    }



    //客户续费月份趋势图 已回款
    public function actionLirun_true_trend($month,$ourMain,$cuid='',$prlin=''){
        $month=Yii::$app->hjd->teodate_month_12($month);

        foreach ($month as $key=>$val)
        {


            $data[$key]['startTime']=$val['start'];
            $val['start']=strtotime($val['start']);
            $val['end']=strtotime($val['end']);
            $data[$key]['money']=RenewHuikuan::lirun_true_timedate($val['start'],$val['end'],$ourMain,$cuid,$prlin);
            if($cuid!='')
            {
                $data[$key]['xf_money']=RenewHuikuan::xufei_timedate($val['start'],$val['end'],$ourMain,$cuid,$prlin);
            }
            $data[$key]['month']=date('m',$val['start']);
        }
        $return_data['code']=200;
        $return_data['data']=$data;
        return $return_data;
    }

    //已汇款利润实际
    public static function lirun_true_timedate($startTime,$endTime,$ourMain,$cuid='',$prlin='')
    {
        $money = 0;
        $lirunlist = self::xufei_true_profits(10000, $startTime, $endTime, $ourMain,$cuid,$prlin);

        foreach ($lirunlist as $val) {
            $money += $val['lirun'];
        }
        return $money;
    }


    //未回款逾期
       public static function whkyq(){
        //查询自己的组
        $getdata=Yii::$app->request->post();
        $adname=$getdata['adname'];

        if($adname!='')
        {
            $where=" and (b.advertiser like '%".$adname."%' or c.name like '%".$adname."%')";
        }

           //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

           if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
           {
               $uid=Yii::$app->user->getId();
               //查询销售负责所有客户
              // $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
              // $adidlist=implode(',',$uid);

               $where.=" and (con.market in($uid) or b.business in($uid))";
           }elseif (Yii::$app->hjd->list_type()==2)
           {
               $uid=Yii::$app->hjd->asbumen();
               //查询销售负责所有客户
              // $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();

               if(count($uid)>1){
                $adidlist=implode(',',$uid);
               }else
               {
                   $uid=Yii::$app->user->getId();
                   $adidlist=$uid;
               }
               $where.=" and (con.market in($adidlist) or b.business in($adidlist))";
               //$where.=" and sna.market in($adidlist)";

           }elseif (Yii::$app->hjd->list_type()==3)
           {
               $where.=" and a.submituser >0";
           }


        $date=$getdata['date'];
        $sqlone='select  a.money, a.advertiser, a.xf_qiane, a.payment_time,c.name,ae.name as ae,b.advertiser as gongsi,a.pay_day as hk_time,(\''.$date.'\'-a.pay_day) as rg  
        from jd_renew_huikuan a left join jd_customer b on a.advertiser=b.id
         left join user_backend c on c.id=b.submituser 
         left join user_backend ae on ae.id=b.business 
         left join jd_contract con on a.xf_contractid=con.id   
         where a.payment_type in(1,2) and a.xf_qiane >0 and a.is_ultimate_shenhe=1 and  a.pay_day<=\''.$date.'\''.$where;


        $cont="select count(1)  from ($sqlone) a";


        $sum="select sum(money) as sum_money,sum(xf_qiane) as sum_qiane  from ($sqlone) a";



        $sumq=Yii::$app->db->createCommand($sum)->queryOne();

        $count = Yii::$app->db->createCommand($cont)->queryScalar();

           $dataProvider = new SqlDataProvider([
               'sql' => $sqlone,
               // 'params' => [':status' => 1],
               'totalCount' => $count,
               /* 'sort' => [
                    'attributes' => [
                        'age',
                        'name' => [
                            'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
                            'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
                            'default' => SORT_DESC,
                            'label' => 'Name',
                        ],
                    ],
                ],
                'pagination' => [
                    'pageSize' => 20,
                ],*/
           ]);

           $ar['data'] = $dataProvider->getModels();
           // 在当前页获取数据项的数目
           $ar['page-count'] = $dataProvider->getCount();

           // 获取所有页面的数据项的总数
           $ar['totalCount'] = $dataProvider->getTotalCount();
           $ar['sum_qiane']=$sumq['sum_qiane'];
           $ar['sum_money']=$sumq['sum_money'];
           return $ar;


       }


    //未回款逾期按公司
    public static function whkyqcu(){
        //查询自己的组
        $getdata=Yii::$app->request->post();
        $adname=$getdata['adname'];
        $date=$getdata['date'];
        if($adname!='')
        {
            $where=" and (b.advertiser like '%".$adname."%' or c.name like '%".$adname."%')";
        }

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            //查询销售负责所有客户
            // $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            // $adidlist=implode(',',$uid);

            $where.=" and con.market in($uid)";
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            // $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();

            if(count($uid)>1){
                $adidlist=implode(',',$uid);
            }else
            {
                $uid=Yii::$app->user->getId();
                $adidlist=$uid;
            }
           // $where.=" and sna.market in($adidlist)";
            $where.=" and (con.market in($adidlist) or b.business in($adidlist))";

        }elseif (Yii::$app->hjd->list_type()==3)
        {


            $where.=" and con.market >0";
        }



        $sqlone2='select  a.money, a.advertiser, a.xf_qiane, a.payment_time,c.name,ae.name as ae,b.advertiser as gongsi,a.pay_day as hk_time,(\''.$date.'\'-a.pay_day) as rg  
        from jd_renew_huikuan a left join jd_customer b on a.advertiser=b.id
         left join user_backend c on c.id=b.submituser 
         left join user_backend ae on ae.id=b.business 
         left join jd_contract con on a.xf_contractid=con.id   
         where a.payment_type in(1,2) and a.xf_qiane >0 and a.is_ultimate_shenhe=1 and  a.pay_day<=\''.$date.'\''.$where;


        $sqlone="SELECT
	sum(sna.money) as money,
	sna.advertiser,
	sum(sna.xf_qiane) as xf_qiane,
	sna.gongsi AS gongsi,
	sna.name,
	sna.ae,
	max(sna.rg) as minrg,
	min(sna.rg) as maxrg
FROM
	(
      $sqlone2
	) sna group by  advertiser,name,ae,gongsi
";




        $cont="select count(1) from ($sqlone) k";
        $sum="select sum(xf_qiane) as sum_qiane from ($sqlone2) k";
        $count = Yii::$app->db->createCommand($cont)->queryScalar();
        $sumq=Yii::$app->db->createCommand($sum)->queryOne();
        $dataProvider = new SqlDataProvider([
            'sql' => $sqlone,
            // 'params' => [':status' => 1],
            'totalCount' => $count,
            /* 'sort' => [
                 'attributes' => [
                     'age',
                     'name' => [
                         'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
                         'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
                         'default' => SORT_DESC,
                         'label' => 'Name',
                     ],
                 ],
             ],
             'pagination' => [
                 'pageSize' => 20,
             ],*/
        ]);

        $ar['data'] = $dataProvider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $dataProvider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $dataProvider->getTotalCount();
        $ar['sum_qiane']=$sumq['sum_qiane'];

        return $ar;




    }


    //已回款逾期
    public static function yhkyq(){
        //查询自己的组
        $getdata=Yii::$app->request->post();
        $adname=$getdata['Search_str'];


        if($adname!='')
        {
            $where=" and (b.advertiser like '%".$adname."%' or c.name like '%".$adname."%')";
        }


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            //查询销售负责所有客户
            // $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            // $adidlist=implode(',',$uid);
            $where.=" and (monkey.market in($uid) or b.business in($uid))";
            //$where.=" and monkey.market in($uid)";
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            // $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();

            if(count($uid)>1){
                $adidlist=implode(',',$uid);
            }else
            {
                $uid=Yii::$app->user->getId();
                $adidlist=$uid;
            }
            $where.=" and monkey.market in($adidlist)";

        }elseif (Yii::$app->hjd->list_type()==3)
        {


            $where.=" and monkey.market >0";
        }


        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date') .'+1 day');


        if($start and $end)
        {


            if(Yii::$app->request->post('date_type')!='2') {
                $search_data="and hk.payment_time>= $start and hk.payment_time<$end";
            }else{
                $search_data="and hk.payment_time>= ".date("Y-m-d H:i:s",$start)." and hk.payment_time<".date("Y-m-d H:i:s",$end);
            }


        }

        $sqlone='select monkey.*,b.advertiser,c.name from(
select *,(ort-zhouqi) as cha from (
select *,(hk_time-xf_time)/3600/24 as ort from(
select a.avid,a.money,xf.money as xfmoney,hk.money as hkmoney,xf.payment_time as xf_time,hk.payment_time as hk_time,c.zhouqi,c.market from jd_yihuikuanxufei a,jd_renew_huikuan xf,jd_renew_huikuan hk,jd_contract c where xf.id=a.xf_id and hk.id=a.hk_id and c.id=a.ht_id  and xf.payment_time!=0 and hk.payment_time!=0 '.$search_data.'
) T ) osu ) monkey LEFT JOIN jd_customer b on b.id=monkey.avid left join user_backend c on c.id=b.submituser where cha >0'.$where;
       /* $query=parent::findBySql($sqlone)->asArray();
        $provider = new ActiveDataProvider([
            'query' => $query,
           'pagination' => [
                'pageSize' => 1,
            ],
            'sort' => [
                'defaultOrder' => [
                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();
       */

       $cont= 'select count(1) from(
select *,(ort-zhouqi) as cha from (
select *,(hk_time-xf_time)/3600/24 as ort from(
select a.avid,a.money,xf.money as xfmoney,hk.money as hkmoney,xf.payment_time as xf_time,hk.payment_time as hk_time,c.zhouqi,c.market from jd_yihuikuanxufei a,jd_renew_huikuan xf,jd_renew_huikuan hk,jd_contract c where xf.id=a.xf_id and hk.id=a.hk_id and c.id=a.ht_id  and xf.payment_time!=0 and hk.payment_time!=0 '.$search_data.'
) T ) osu ) monkey LEFT JOIN jd_customer b on b.id=monkey.avid left join user_backend c on c.id=b.submituser where cha >0'.$where;



       $sum='select sum(monkey.money) as sum_money,sum(monkey.xfmoney) as sum_xfmoney from(
select *,(ort-zhouqi) as cha from (
select *,(hk_time-xf_time)/3600/24 as ort from(
select a.avid,a.money,xf.money as xfmoney,hk.money as hkmoney,xf.payment_time as xf_time,hk.payment_time as hk_time,c.zhouqi,c.market from jd_yihuikuanxufei a,jd_renew_huikuan xf,jd_renew_huikuan hk,jd_contract c where xf.id=a.xf_id and hk.id=a.hk_id and c.id=a.ht_id  and xf.payment_time!=0 and hk.payment_time!=0 '.$search_data.'
) T ) osu ) monkey LEFT JOIN jd_customer b on b.id=monkey.avid left join user_backend c on c.id=b.submituser where cha >0'.$where;

        $sumq=Yii::$app->db->createCommand($sum)->queryOne();
        $count = Yii::$app->db->createCommand($cont)->queryScalar();

        $dataProvider = new SqlDataProvider([
            'sql' => $sqlone,
           // 'params' => [':status' => 1],
            'totalCount' => $count,
           /* 'sort' => [
                'attributes' => [
                    'age',
                    'name' => [
                        'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
                        'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Name',
                    ],
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],*/
        ]);

        $ar['data'] = $dataProvider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $dataProvider->getCount();
        $ar['sum_money']=$sumq['sum_money'];
        $ar['sum_xfmoney']=$sumq['sum_xfmoney'];
        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $dataProvider->getTotalCount();

        return $ar;
    }



    //保证金列表

    public static function bzj_list($id='',$shenhewhere2=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','jd_renew_huikuan.id',0];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_renew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date') ."+1 day");

        if($start and $end)
        {
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_renew_huikuan.payment_time', $start], ['<', 'jd_renew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_renew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_renew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_renew_huikuan.payment_time',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');
        if($shenhe=='1')
        {
            $shenhewhere="jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_renew_huikuan.audit=0 or jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_renew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_renew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;
        //完成状态
        $is_accomplish=Yii::$app->request->post("is_ultimate_shenhe");


        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser as adname,jd_renew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,mht.state as mstate,cuad.advertiser as guanggaozhu')
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac'])
            ->leftJoin("jd_contract_relevance contre","jd_renew_huikuan.xf_contractid=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_contract ht','ht.id=contre.contract_id')
            ->leftJoin('jd_contract mht','mht.id=ac.mht_id')
            ->leftJoin('customer_advertiser cuad','jd_renew_huikuan.cu_ad_id=cuad.id')
            ->where("jd_renew_huikuan.payment_type in(4)")
            ->andFilterWhere($userswhere)
            ->andWhere($shenhewhere)
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.is_ultimate_shenhe'=>$is_accomplish])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->asArray();
        $ar['sum']=$query->sum('jd_renew_huikuan.money');
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();





        return $ar;
    }


    //保证金详情
    public static function baozhengjin_info($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname'])->asArray()->one();

        $contarat_info=JdContract::contractinfonomt($data['xf_contractid']);

        $data['contractinfo']=$contarat_info;

        $account_cu_ad_info=RenewHuikuan::find()->select("cuad.advertiser as guanggaozhu")->join(' a left join','customer_advertiser cuad','a.cu_ad_id=cuad.id')->where(['a.id'=>$data['id']])->asArray()->one();
        $data['cu_ad_info']=$account_cu_ad_info;

        return $data;
    }

    //保证金审核操作
    //补款审核操作
    public static function baozhengjin_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $renew_info=self::findOne($id);//退款详情
        if($audit=='2')
        {
            //驳回分配保证金成功后协议保证金减
            RenewHuikuan::money_change_jian($renew_info['advertiser'], $renew_info['xf_contractid'], '4-2', $renew_info['money']);
            //增加公司未分配余额
           // RenewHuikuan::money_change($renew_info['advertiser'],$renew_info['xf_contractid'],'4',$renew_info['money']);
        }else{
            //查询退款需要几级审核
            $renew_shenhe=AuditAction::find()->where(['action_name'=>'baozhengjin'])->one();


            //如果最终审核通过
            if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
            {
                //修改为最终审核
                $renew_info->is_ultimate_shenhe=1;
                $renew_info->save();
            }
        }





    }




    //保证金列表

    public static function kehubukuan_list($id='',$shenhewhere2=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','jd_renew_huikuan.id',0];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_renew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 daye");

        if($start and $end)
        {
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_renew_huikuan.payment_time', $start], ['<', 'jd_renew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_renew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_renew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_renew_huikuan.payment_time',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');
        if($shenhe=='1')
        {
            $shenhewhere="jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_renew_huikuan.audit=0 or jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_renew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_renew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;
        //完成状态
        $is_accomplish=Yii::$app->request->post("is_ultimate_shenhe");


        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser as adname,jd_renew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,mht.state as mstate,cuad.advertiser as guanggaozhu')
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac'])
            ->leftJoin("jd_contract_relevance contre","jd_renew_huikuan.xf_contractid=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_contract ht','ht.id=contre.contract_id')
            ->leftJoin('jd_contract mht','mht.id=ac.mht_id')
            ->leftJoin('customer_advertiser cuad','jd_renew_huikuan.cu_ad_id=cuad.id')
            ->where("jd_renew_huikuan.payment_type in(31)")
            ->andFilterWhere($userswhere)
            ->andWhere($shenhewhere)
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.is_ultimate_shenhe'=>$is_accomplish])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->asArray();
        $ar['sum']=$query->sum('jd_renew_huikuan.money');
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();





        return $ar;
    }


    //保证金详情
    public static function kehubukuan_info($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname','account0 ac'])->asArray()->one();

        $contarat_info=JdContract::contractinfo($data['account']);

        $data['contractinfo']=$contarat_info;

        $account_cu_ad_info=RenewHuikuan::find()->select("cuad.advertiser as guanggaozhu")->join(' a left join','customer_advertiser cuad','a.cu_ad_id=cuad.id')->where(['a.id'=>$data['id']])->asArray()->one();
        $data['cu_ad_info']=$account_cu_ad_info;

        return $data;
    }

    //保证金审核操作
    //补款审核操作
    public static function kehubukuan_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $renew_info=self::findOne($id);//退款详情
        if($audit=='2')
        {
            //驳回分配保证金成功后协议保证金减
            RenewHuikuan::money_change_jian($renew_info['advertiser'], $renew_info['xf_contractid'], '4-3', $renew_info['money']);
            //增加公司未分配余额
            // RenewHuikuan::money_change($renew_info['advertiser'],$renew_info['xf_contractid'],'4',$renew_info['money']);
        }else{
            //查询退款需要几级审核
            $renew_shenhe=AuditAction::find()->where(['action_name'=>'kehubukuan'])->one();


            //如果最终审核通过
            if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
            {
                //修改为最终审核
                $renew_info->is_ultimate_shenhe=1;
                $renew_info->save();
            }
        }





    }




    //账户对应的真实消耗 退款id 公司id
    public static function Tuikuan_cost_real($id){
        $costinfo=self::find()->joinWith("account0")->where(['jd_renew_huikuan.id'=>$id])->asArray()->one();

        $account=$costinfo['account0']['a_users'];

        $avid=$costinfo['advertiser'];
        $date=$costinfo['payment_time'];
        // $date=strtotime($date);
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

        //查询账户相关续费
        $renew_list=self::find()
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
            $costinfo=self::find()->joinWith("account0")->where(['jd_renew_huikuan.id'=>$id])->asArray()->one();
            //如果续费未消耗百度币 大于 消耗 （余额大于续费金额）停止循环并返回续费欠额为0
            if($value['no_cost_xf']-$costinfo['no_cost_xf']>0) {
                //设置续费剩余消耗=续费剩余消耗-消耗
                self::updateAll(['no_cost_xf'=>$value['no_cost_xf']-$costinfo['no_cost_xf']],['id'=>$value['id']]);


                //增加真实消耗记录
                $accoust_cost_real= new AccountCostReal();
                $accoust_cost_real->date=date("Y-m-d",$costinfo['payment_time']);
                $accoust_cost_real->a_user=$account;
                $accoust_cost_real->cost=$costinfo['no_cost_xf'];
                $accoust_cost_real->no_back_cost=$costinfo['no_cost_xf'];
                $accoust_cost_real->contract_id=$value['xf_contractid'];
                $accoust_cost_real->istuikuan=1;



                if(!$accoust_cost_real->save())
                {
                    var_dump($accoust_cost_real->errors);
                    exit;
                }
                //修改消耗剩下的费用为0
                self::updateAll(['no_cost_xf'=>0],['id'=>$id]);
                return ;
            }else{
                //如果续费未消耗百度币 小于 消耗  （此回款余额不足以抹平此续费）则继续执行循环 并把此回款的余额为0

                //增加真实消耗记录
                $accoust_cost_real = new AccountCostReal();
                $accoust_cost_real->date = date("Y-m-d",$costinfo['payment_time']);
                $accoust_cost_real->a_user = $account;
                $accoust_cost_real->cost = $value['no_cost_xf'];
                $accoust_cost_real->no_back_cost = $value['no_cost_xf'];
                $accoust_cost_real->contract_id = $value['xf_contractid'];
                $accoust_cost_real->istuikuan=1;

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
                self::updateAll(['no_cost_xf'=>0],['id'=>$value[id]]);
                //修改续费欠额
                self::updateAll(['no_cost_xf'=>$costinfo['no_cost_xf']-$value['no_cost_xf']],['id'=>$id]);
            }
        }



    }


    public static function markeyticheng($start='',$end='',$uid='',$type='',$adstate=''){

        if($start!='' && $end!='')
        {
            //$start=strtotime($start);
            $end=strtotime($end."+1 day");
            $end=date("Y-m-d",$end);
            $whereand=" where last_time>='{$start}' and last_time<'{$end}'";
        }

        if($type=='renew')
        {
            $patyment_type='1,2';
            $tablename="'renew'";
        }elseif($type=='tuikuan')
        {
            $patyment_type='15';
            $tablename="'refund_account_p','refund_account_z'";
        }elseif($type=='bukuan')
        {
            $patyment_type='3';
            $tablename="'bukuan'";
        }

        if($type=='all')
        {
            $patyment_type='1,2,15,3';
            $tablename="'renew','refund_account_p','refund_account_z','bukuan'";
        }


        if($adstate!='')
        {
            $adstatewhere=' and rh.ht_state='.$adstate;
        }


        if($uid!='')
        {
            $userwhere=" and ht.market=$uid";
        }

        //公司条件如果有公司就只显示该公司的记录
        $adid=Yii::$app->request->get('adid');
        if($adid!='')
        {
            $adwhere=' and a.advertiser='.$adid;
        }


        //产品线条件
        $prlin=Yii::$app->request->get('prlin');
        if($prlin!='')
        {
            $prlinwhere=' and pl.id='.$prlin;
        }



        $shaixuan='
        select id from ( 
         select a.id,a.last_time from jd_renew_huikuan a  LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(' . $patyment_type . ') and a.is_ultimate_shenhe=1  ' . $userwhere . $adwhere . '  group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';

        //实际利润计算条件 以 已回款续费为准
        $yixufeihuikuan=Yii::$app->request->get('yihuikuan');
        if($yixufeihuikuan!='' && $type=='renew')
        {
            $shaixuan='
        select id from ( 
         select a.id,a.last_time from jd_renew_huikuan a LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID left join jd_yihuikuanxufei y on a.id=y.xf_id   where y.xf_id notnull and a.payment_type in(' . $patyment_type . ') and a.is_ultimate_shenhe=1 ' . $userwhere . $adwhere . '  group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';
           // $shaixuan='select a.id,max(b.catated_at) from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  left join jd_yihuikuanxufei y on a.id=y.xf_id where y.xf_id notnull and a.payment_type in('.$patyment_type.') and a.is_ultimate_shenhe=1 and b.tablea in('.$tablename.') and b.type=1 and b."type"=1 '.$whereand.$userwhere.$adwhere.'  group by a.id order by a.id desc ';
        }


        $list=\Yii::$app->db->createCommand($shaixuan)->queryAll();


        $xfidin=ArrayHelper::getColumn($list,'id');
        $xfidin=implode(',',$xfidin);
        if($xfidin=='')
        {
            $xfidin=0;
        }


        $sql='select cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,rh.ht_state,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(c.contract_end),\'YYYY-MM-DD\') contract_ent,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and  ac.id=rh.account and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in('.$patyment_type.') and rh.is_ultimate_shenhe=1 and rh.id in('.$xfidin.') '.$adstatewhere.$prlinwhere.' order by cu.advertiser asc';
        $sql="
            select *,money-money*(100+fandian)/(100+fandian+kh_grfd) gf,show_money/(100+xs_fd)*100 sf_money from ($sql) t 
        ";

        if($type=='bukuan')
        {
            $sql="
                select *,COALESCE(money,0) as gs_lirun,COALESCE(money,0) as xs_lirun,(COALESCE(money,0))*ticheng/100 as tc from ($sql) t1  
            ";

        }else
        {
            $sql="
                select *,COALESCE(money,0)-COALESCE(xf_cost,0)-COALESCE(gf,0) as gs_lirun,COALESCE(money,0)-COALESCE(sf_money,0)-COALESCE(gf,0) as xs_lirun,(COALESCE(money,0)-COALESCE(sf_money,0)-COALESCE(gf,0))*COALESCE(ticheng,0)/100 as tc from ($sql) t1  
            ";
        }
        $countsql="select count(1) from ($sql) t2";




        $count = Yii::$app->db->createCommand($countsql)->queryScalar();
        $tichengsum=Yii::$app->db->createCommand("select sum(tc) from ($sql) t3")->queryScalar();
        $lirunsum=Yii::$app->db->createCommand("select sum(xs_lirun) from ($sql) t3")->queryScalar();
        $gs_lirun=Yii::$app->db->createCommand("select sum(gs_lirun) from ($sql) t3")->queryScalar();
        $renewsum=Yii::$app->db->createCommand("select sum(money) from ($sql) t3")->queryScalar();
        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            // 'params' => [':status' => 1],
            'totalCount' => $count,

        ]);
        $ar['tichengsum']=$tichengsum;
        $ar['lirunsum']=$lirunsum;
        $ar['gs_lirun']=$gs_lirun;
        $ar['renewsum']=$renewsum;
        $ar['data'] = $dataProvider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $dataProvider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $dataProvider->getTotalCount();



        return $ar;


    }

    public static function markeyticheng_zj($start='',$end=''){

        if($start!='' && $end!='')
        {
            $start=strtotime($start);
            $end=strtotime($end."+1 day");
            $whereand=" where created>='{$start}' and created<'{$end}'";
            //$whereand=" and b.catated_at>={$start} and b.catated_at<{$end}";
        }



       // $shaixuan='(select a.id from jd_renew_huikuan a left join audit b on a.id=b.yid where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 and b.tablea=\'renew\' and b."type"=1 '.$whereand.' group by a.id order by a.id desc )';
         $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 and b.tablea in(\'renew\') and b.type=1 and b."type"=1   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';

        $sql='select cu.id as cuid,u.id as uid,cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac 
        where c.id=rh.xf_contractid and c.id=cr.contract_id and rh.account=ac.id and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in(1,2) and rh.is_ultimate_shenhe=1 and rh.id in('.$shaixuan.') order by cu.advertiser asc';
        $sql="
            select *,money-money*(100+fandian)/(100+fandian+kh_grfd) gf,show_money/(100+xs_fd)*100 sf_money from ($sql) t 
        ";
        $sql="
            select *,money-xf_cost-gf as gs_lirun,money-sf_money-gf as xs_lirun,(money-sf_money-gf)*ticheng/100 as tc from ($sql) t1  
        ";

        $sql="select xs,uid,count(cuid) as adcount,sum(money) as money,sum(xf_qiane) as qiane,sum(xs_lirun) as lirun,sum(tc) as ticheng from ($sql) t2 GROUP by xs,uid order by ticheng desc";


        //退款
        //$shaixuan='(select a.id from jd_renew_huikuan a left join audit b on a.id=b.yid where a.payment_type in(15) and a.is_ultimate_shenhe=1 and b.tablea in(\'refund_account_p\',\'refund_account_z\') and b."type"=1 '.$whereand.' group by a.id order by a.id desc )';
        $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(15) and a.is_ultimate_shenhe=1 and b.tablea in(\'refund_account_p\',\'refund_account_z\') and b.type=1 and b."type"=1   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';

        $sql2='select cu.id as cuid,u.id as uid,cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and ac.id=rh.account and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in(15) and rh.is_ultimate_shenhe=1 and rh.id in('.$shaixuan.') order by cu.advertiser asc';
        $sql2="
            select *,money-money*(100+fandian)/(100+fandian+kh_grfd) gf,show_money/(100+xs_fd)*100 sf_money from ($sql2) t 
        ";
        $sql2="
            select *,money-xf_cost-gf as gs_lirun,money-sf_money-gf as xs_lirun,(money-sf_money-gf)*ticheng/100 as tc from ($sql2) t1  
        ";

        $sql2="select xs,uid,count(cuid) as adcount,sum(money) as money,sum(xf_qiane) as qiane,sum(xs_lirun) as lirun,sum(tc) as ticheng from ($sql2) t2 GROUP by xs,uid order by ticheng desc";



        //补款
       // $shaixuan='(select a.id from jd_renew_huikuan a left join audit b on a.id=b.yid where a.payment_type in(3) and a.is_ultimate_shenhe=1 and b.tablea=\'bukuan\' and b."type"=1 '.$whereand.' group by a.id order by a.id desc )';
        $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(3) and a.is_ultimate_shenhe=1 and b.tablea in(\'bukuan\') and b.type=1 and b."type"=1   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';

        $sql3='select cu.id as cuid,u.id as uid,cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and ac.id=rh.account and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in(3) and rh.is_ultimate_shenhe=1 and rh.id in('.$shaixuan.') order by cu.advertiser asc';
        $sql3="
            select *,money-money*(100+fandian)/(100+fandian+kh_grfd) gf,show_money/(100+xs_fd)*100 sf_money from ($sql3) t 
        ";
        $sql3="
            select *,money as gs_lirun,money as xs_lirun,(money)*ticheng/100 as tc from ($sql3) t1  
        ";

        $sql3="select xs,uid,count(cuid) as adcount,sum(money) as money,sum(xf_qiane) as qiane,sum(xs_lirun) as lirun,sum(tc) as ticheng from ($sql3) t2 GROUP by xs,uid order by ticheng desc";


        $sql4="select a.*,(COALESCE(a.lirun,0)-COALESCE(b.lirun,0)-COALESCE(c.lirun,0)) as xiaoshou_lirun,(COALESCE(a.ticheng,0)-COALESCE(b.ticheng,0)-COALESCE(c.ticheng,0)) as ticheng_money from ($sql) a left join ($sql2) b on a.uid=b.uid  left join ($sql3) c on a.uid=c.uid ";

        $list=\Yii::$app->db->createCommand($sql4)->queryAll();
        return $list;


    }

    public static function markeyticheng_adstate($start='',$end='',$uid,$type='renew'){

        $a=$start;
        $b=$end;
        if($start!='' && $end!='')
        {
            $start=strtotime($start);
            $end=strtotime($end."+1 day");
            $whereand=" where created>='{$start}' and created<'{$end}'";
        }
        /*
        if($type=='renew')
        {
            $patyment_type='1,2';
        }elseif($type=='tuikuan')
        {
            $patyment_type='15';
        }elseif($type=='bukuan')
        {
            $patyment_type='3';
        }*/
        if($type=='renew')
        {
            $patyment_type='1,2';
            $tablename="'renew'";
        }elseif($type=='tuikuan')
        {
            $patyment_type='15';
            $tablename="'refund_account_p','refund_account_z'";
        }elseif($type=='bukuan')
        {
            $patyment_type='3';
            $tablename="'bukuan'";
        }


        //$shaixuan='select a.id,max(b.catated_at) from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in('.$patyment_type.') and a.is_ultimate_shenhe=1 and b.tablea=\'renew\' and b."type"=1 '.$whereand.'  and ht.market='.$uid.' group by a.id order by a.id desc ';
        $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(' . $patyment_type . ') and a.is_ultimate_shenhe=1 and b.tablea in(' . $tablename . ') and b.type=1 and b."type"=1  and ht.market='.$uid.'   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';



        $dataprall=\Yii::$app->db->createCommand($shaixuan)->queryAll();
        $xfidin=ArrayHelper::getColumn($dataprall,'id');
        $xfidin=implode(',',$xfidin);
        if($xfidin=='')
        {
            $xfidin=0;
        }

        $sql='select cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        case 
        when rh.ht_state=1 then \'新客\'
        when rh.ht_state=2 then \'老客\'
        when rh.ht_state=3 then \'老客新媒\'
        when rh.ht_state=4 then \'公司分配\'
        end as adstatename,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time,
        rh.ht_state,
        cu.id as cuid
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and ac.id=rh.account and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in('.$patyment_type.') and rh.is_ultimate_shenhe=1 and rh.id in('.$xfidin.') order by cu.advertiser asc';
        $sql="
            select *,money-money*(100+fandian)/(100+fandian+kh_grfd) gf,show_money/(100+xs_fd)*100 sf_money from ($sql) t 
        ";
        $sql="
            select *,money-xf_cost-gf as gs_lirun,money-sf_money-gf as xs_lirun,(money-sf_money-gf)*ticheng/100 as tc from ($sql) t1  
        ";

        $sql="select ht_state,adstatename,count(cuid) as adcount,sum(money) as money,sum(xf_qiane) as qiane,sum(xs_lirun) as lirun,sum(tc) as ticheng from ($sql) t2 GROUP by ht_state,adstatename order by ht_state asc";

        $list=\Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($list as $key=>$val)
        {
            $tuikuan=self::markeyticheng($a,$b,$uid,'tuikuan',$val['ht_state']);
            $bukuan=self::markeyticheng($a,$b,$uid,'bukuan',$val['ht_state']);
            $list[$key]['lirun']=$val['lirun']-$tuikuan['lirunsum']-$bukuan['lirunsum'];
            $list[$key]['ticheng']=$val['ticheng']-$tuikuan['tichengsum']-$bukuan['tichengsum'];
        }

        return $list;


    }


    public static function users_ad_ticheng($start='',$end='',$uid=''){

        if($start!='' && $end!='')
        {
            $start=strtotime($start);
            $end=strtotime($end."+1 day");

            $whereand=" where created>='{$start}' and created<'{$end}'";
        }
        //排序规则
        $order=Yii::$app->request->get('order');
        if($order!='')
        {
            $orderby=$order;
        }else
        {
            $orderby='ticheng_money';
        }
        //如果有uid 筛选条件
        if($uid!='')
        {
            $userwhere="and c.market=$uid ";
        }



        //$shaixuan='(select a.id from jd_renew_huikuan a left join audit b on a.id=b.yid where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 and b.tablea=\'renew\' and b."type"=1 '.$whereand.' group by a.id order by a.id desc )';
        $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 and b.tablea in(\'renew\') and b.type=1 and b."type"=1   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';

        //实际利润计算条件 以 已回款续费为准
        $yixufeihuikuan=Yii::$app->request->get('yihuikuan');
        if($yixufeihuikuan!='')
        {
            $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID left join jd_yihuikuanxufei y on a.id=y.xf_id   where y.xf_id notnull and a.payment_type in(1,2) and a.is_ultimate_shenhe=1 and b.tablea in(\'renew\') and b.type=1 and b."type"=1 ' . $userwhere . $adwhere . '  group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';
        }




        $sql='select cu.id as cuid,u.id as uid,cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and ac.id=rh.account and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in(1,2) and rh.is_ultimate_shenhe=1 and rh.id in('.$shaixuan.') '.$userwhere.' order by cu.advertiser asc';
        $sql="
            select *,money-money*(100+fandian)/(100+fandian+kh_grfd) gf,show_money/(100+xs_fd)*100 sf_money from ($sql) t 
        ";
        $sql="
            select *,money-xf_cost-gf as gs_lirun,money-sf_money-gf as xs_lirun,(money-sf_money-gf)*ticheng/100 as tc from ($sql) t1  
        ";

        $sql="select cuid,advertiser,sum(money) as money,sum(xf_qiane) as qiane,sum(gf) as gf,sum(gs_lirun) as gs_lirun,sum(xs_lirun) as lirun,sum(tc) as ticheng from ($sql) t2 GROUP by cuid,advertiser order by ticheng desc";


        //退款
        //$shaixuan='(select a.id from jd_renew_huikuan a left join audit b on a.id=b.yid where a.payment_type in(15) and a.is_ultimate_shenhe=1 and b.tablea in(\'refund_account_p\',\'refund_account_z\') and b."type"=1 '.$whereand.' group by a.id order by a.id desc )';
        $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(15) and a.is_ultimate_shenhe=1 and b.tablea in(\'refund_account_p\',\'refund_account_z\') and b.type=1 and b."type"=1   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';

        $sql2='select cu.id as cuid,u.id as uid,cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and ac.id=rh.account and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in(15) and rh.is_ultimate_shenhe=1 and rh.id in('.$shaixuan.')'.$userwhere.' order by cu.advertiser asc';
        $sql2="
            select *,money-money*(100+fandian)/(100+fandian+kh_grfd) gf,show_money/(100+xs_fd)*100 sf_money from ($sql2) t 
        ";
        $sql2="
            select *,money-xf_cost-gf as gs_lirun,money-sf_money-gf as xs_lirun,(money-sf_money-gf)*ticheng/100 as tc from ($sql2) t1  
        ";

        $sql2="select cuid,advertiser,count(cuid) as adcount,sum(money) as money,sum(gs_lirun) as gs_lirun,sum(gf) as gf,sum(xf_qiane) as qiane,sum(xs_lirun) as lirun,sum(tc) as ticheng from ($sql2) t2 GROUP by cuid,advertiser order by ticheng desc";





        //补款
        //$shaixuan='(select a.id from jd_renew_huikuan a left join audit b on a.id=b.yid where a.payment_type in(3) and a.is_ultimate_shenhe=1 and b.tablea=\'bukuan\' and b."type"=1  '.$whereand.' group by a.id order by a.id desc )';
        $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(3) and a.is_ultimate_shenhe=1 and b.tablea in(\'bukuan\') and b.type=1 and b."type"=1   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';

        $sql3='select cu.id as cuid,u.id as uid,cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and ac.id=rh.account and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in(3) and rh.is_ultimate_shenhe=1 and rh.id in('.$shaixuan.')  '.$userwhere.'  order by cu.advertiser asc';
        $sql3="
            select *,0 as gf,show_money/(100+xs_fd)*100 sf_money from ($sql3) t 
        ";
        $sql3="
            select *,money as gs_lirun,money as xs_lirun,(money)*ticheng/100 as tc from ($sql3) t1  
        ";

        $sql3="select cuid,advertiser,count(cuid) as adcount,sum(money) as money,sum(gs_lirun) as gs_lirun,sum(gf) as gf,sum(xf_qiane) as qiane,sum(xs_lirun) as lirun,sum(tc) as ticheng from ($sql3) t2 GROUP by cuid,advertiser order by ticheng desc";



        $sql4="select a.advertiser,a.cuid,ROUND(COALESCE(a.qiane,0),2) as qiane,
ROUND((COALESCE(a.money,0)-COALESCE(b.money,0))::numeric,2) as chongzhimoney,
ROUND((COALESCE(a.lirun,0)-COALESCE(b.lirun,0)-COALESCE(c.lirun,0))::numeric,2) as xiaoshou_lirun,
ROUND((COALESCE(a.gs_lirun,0)-COALESCE(b.gs_lirun,0)-COALESCE(c.gs_lirun,0))::numeric,2) as gs_lirun,
ROUND((COALESCE(a.gf,0)-COALESCE(b.gf,0))::numeric,2) as gefanmoney,
ROUND((COALESCE(a.ticheng,0)-COALESCE(b.ticheng,0)-COALESCE(c.ticheng,0))::numeric,2) as ticheng_money from ($sql) a left join ($sql2) b on a.cuid=b.cuid left join ($sql3) c on a.cuid=c.cuid  order by $orderby desc ";

        $list=\Yii::$app->db->createCommand($sql4)->queryAll();
        return $list;




    }

    //销售新增客户折线图 按年统计
    public static function market_new_customer_cn($uid,$year){
        $sql='select count(1), to_char(to_timestamp(ctime),\'YYYY-MM\') timea from jd_customer where submituser='.$uid.' GROUP by timea order by timea asc';
        $sql="select * from ($sql) t where timea like '%$year%'";
        $list=\Yii::$app->db->createCommand($sql)->queryAll();

        $a=0;
        $data=[];
        for($i=1;$i<=12;$i++)
        {
            if($i<10)
            {
                $a=$year.'-0'.$i;
            }else
            {
                $a=$year.'-'.$i;
            }
            if($list){


                foreach ($list as $key=>$val)
                {
                    if($val['timea']!=$a)
                    {
                       $data[]=array('date'=>$a,'count'=>0);
                    }else
                    {
                        $data[]=array('date'=>$a,'count'=>$val['count']);
                    }
                }
            }else
            {
                $data[]=array('date'=>$a,'count'=>0);
            }


        }



        return $data;
    }
    //销售利润金额12月折线图
    public static function market_lirun_money_zhexian($year,$uid=''){

        $fordata=Yii::$app->hjd->teodate_month_121(12,$year.'-01-01');

      foreach ($fordata as $key=>$val){

           $renew=RenewHuikuan::markeyticheng($val['start'],$val['end'],$uid,'renew');

            $tuikuan=RenewHuikuan::markeyticheng($val['start'],$val['end'],$uid,'tuikuan');

            $bukuan=RenewHuikuan::markeyticheng($val['start'],$val['end'],$uid,'bukuan');


            $data[$key]=array(
                'date'=>$val['start'],
                'lirun'=>$renew['lirunsum']-$tuikuan['lirunsum']-$bukuan['lirunsum'],
                'gs_lirun'=>$renew['gs_lirun']-$tuikuan['gs_lirun']-$bukuan['gs_lirun']
            );



        }

        return $data;

    }

    //销售续费金额12月折线图
    public static function market_renew_money_zhexian($year,$uid=''){

        $fordata=Yii::$app->hjd->teodate_month_121(12,$year.'-01-01');

        foreach ($fordata as $key=>$val){

            $renew=RenewHuikuan::markeyticheng($val['start'],$val['end'],'','renew');

            $tuikuan=RenewHuikuan::markeyticheng($val['start'],$val['end'],'','tuikuan');

            $bukuan=RenewHuikuan::markeyticheng($val['start'],$val['end'],$uid,'bukuan');


            $data[$key]=array(
                'date'=>$val['start'],
                'money'=>$renew['renewsum']-$tuikuan['renewsum'],
                'lirun'=>round($renew['gs_lirun'],2)-round($tuikuan['gs_lirun'],2)-round($bukuan['gs_lirun'],2)
            );



        }



        return $data;

    }


    public static function ad_renew_product_lirun($id,$start='',$end=''){


        $order=Yii::$app->request->get('order');
        if($order!='')
        {
            $orderby=$order;
        }else
        {
            $orderby='ticheng_money';
        }

        if($start!='' && $end!='')
        {
            $start=strtotime($start);
            $end=strtotime($end."+1 day");

            $whereand=" where created>='{$start}' and created<'{$end}'";
        }


        //$shaixuan='(select a.id from jd_renew_huikuan a left join audit b on a.id=b.yid where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 and b.tablea=\'renew\' and b."type"=1 '.$whereand.' group by a.id order by a.id desc )';
        $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(1,2) and a.is_ultimate_shenhe=1 and b.tablea in(\'renew\') and b.type=1 and b."type"=1   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';
        $sql='select rh.id as xfid,pl.id as prid ,u.id as uid,cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and ac.id=rh.account and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in(1,2) and rh.id in('.$shaixuan.') and rh.is_ultimate_shenhe=1  and cu.id='.$id.' order by cu.advertiser asc';
        $sql="
            select *,money-money*(100+fandian)/(100+fandian+kh_grfd) gf,show_money/(100+xs_fd)*100 sf_money from ($sql) t 
        ";
        $sql="
            select *,money-xf_cost-gf as gs_lirun,money-sf_money-gf as xs_lirun,(money-sf_money-gf)*ticheng/100 as tc from ($sql) t1  
        ";

        $sql="select name as prlin ,prid,min(xfid) as minrenewid,max(xfid) as maxrenewid,sum(money) as money,sum(xf_qiane) as qiane,sum(gf) as gf,sum(xs_lirun) as lirun,sum(tc) as ticheng,sum(gs_lirun) as gs_lirun from ($sql) t2 GROUP by name,prid order by ticheng desc";



        //退款
        //$shaixuan='(select a.id from jd_renew_huikuan a left join audit b on a.id=b.yid where a.payment_type in(15) and a.is_ultimate_shenhe=1 and b.tablea in(\'refund_account_p\',\'refund_account_z\') and b."type"=1 '.$whereand.' group by a.id order by a.id desc )';
        $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(15) and a.is_ultimate_shenhe=1 and b.tablea in(\'refund_account_p\',\'refund_account_z\') and b.type=1 and b."type"=1   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';
        $sql2='select cu.id as cuid,pl.id as prid,u.id as uid,cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and ac.id=rh.account and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in(15) and rh.id in('.$shaixuan.')  and rh.is_ultimate_shenhe=1  and cu.id='.$id.' order by cu.advertiser asc';
        $sql2="
            select *,money-money*(100+fandian)/(100+fandian+kh_grfd) gf,show_money/(100+xs_fd)*100 sf_money from ($sql2) t 
        ";
        $sql2="
            select *,money-xf_cost-gf as gs_lirun,money-sf_money-gf as xs_lirun,(money-sf_money-gf)*ticheng/100 as tc from ($sql2) t1  
        ";

        $sql2="select name,count(cuid) as adcount,prid,sum(money) as money,sum(gf) as gf,sum(xf_qiane) as qiane,sum(xs_lirun) as lirun,sum(tc) as ticheng,sum(gs_lirun) as gs_lirun from ($sql2) t2 GROUP by name,prid order by ticheng desc";





        //补款
        //$shaixuan='(select a.id from jd_renew_huikuan a left join audit b on a.id=b.yid where a.payment_type in(3) and a.is_ultimate_shenhe=1 and b.tablea=\'bukuan\' and b."type"=1  '.$whereand.' group by a.id order by a.id desc )';
        $shaixuan='
        select id from ( 
         select a.id,greatest(max(b.catated_at),a.payment_time) created from jd_renew_huikuan a left join audit b on a.id=b.yid LEFT JOIN jd_contract ht ON A.xf_contractid = ht.ID  where a.payment_type in(3) and a.is_ultimate_shenhe=1 and b.tablea in(\'bukuan\') and b.type=1 and b."type"=1   group by a.id order by a.id desc 
        ) t '.$whereand.' 
        ';


        $sql3='select cu.id as cuid,pl.id as prid,u.id as uid,cu.advertiser,mt.title,mt.rebates_proportion mt_fd,mt.m_xsfd xs_fd,mt.dl_fandian,c.kh_grfd,cr.fandian,pl.name,rh.money,rh.xf_cost,rh.show_money,rh.xf_qiane,u.name as xs,
        case 
        when rh.ht_state=1 then 18
        when rh.ht_state=2 then 5
        when rh.ht_state=3 then 18
        when rh.ht_state=4 then 2
        end as ticheng,
        to_char(to_timestamp(c.contract_start),\'YYYY-MM-DD\') contract_start,
        to_char(to_timestamp(rh.payment_time),\'YYYY-MM-DD\') pay_time
        from jd_renew_huikuan rh,jd_contract c ,jd_contract_relevance cr,jd_contract mt,jd_product_line pl ,jd_customer cu,user_backend u ,jd_account ac
        where c.id=rh.xf_contractid and c.id=cr.contract_id and ac.id=rh.id and ac.mht_id=mt.id and mt.product_line=pl.id and c.advertiser=cu.id and c.market=u.id   and rh.payment_type in(3)  and rh.id in('.$shaixuan.')  and rh.is_ultimate_shenhe=1  and cu.id='.$id.' order by cu.advertiser asc';
        $sql3="
            select *,0 as gf,show_money/(100+xs_fd)*100 sf_money from ($sql3) t 
        ";
        $sql3="
            select *,money as gs_lirun,money as xs_lirun,(money)*ticheng/100 as tc from ($sql3) t1  
        ";

        $sql3="select name,prid,count(cuid) as adcount,sum(money) as money,sum(gf) as gf,sum(xf_qiane) as qiane,sum(xs_lirun) as lirun,sum(tc) as ticheng,sum(gs_lirun) as gs_lirun from ($sql3) t2 GROUP by name,prid order by ticheng desc";



        $sql4="select a.prid,a.prlin,minrenew.payment_time as minrenewtime,minrenew.money as minrenewmone,maxrenew.payment_time as maxrenewtime,maxrenew.money as maxrenewmone,ROUND(COALESCE(a.qiane,0),2) as qiane,
ROUND((COALESCE(a.money,0)-COALESCE(b.money,0))::numeric,2) as chongzhimoney,
ROUND((COALESCE(a.gs_lirun,0)-COALESCE(b.gs_lirun,0))::numeric,2) as gs_lirun,
ROUND((COALESCE(a.lirun,0)-COALESCE(b.lirun,0)-COALESCE(c.lirun,0))::numeric,2) as xiaoshou_lirun,
ROUND((COALESCE(a.gf,0)-COALESCE(b.gf,0))::numeric,2) as gefanmoney,
ROUND((COALESCE(a.ticheng,0)-COALESCE(b.ticheng,0)-COALESCE(c.ticheng,0))::numeric,2) as ticheng_money from ($sql) a left join ($sql2) b on a.prid=b.prid left join ($sql3) c on a.prid=c.prid left join jd_renew_huikuan minrenew on minrenew.id=a.minrenewid left join jd_renew_huikuan maxrenew on maxrenew.id=a.maxrenewid order by $orderby desc ";

        $list=\Yii::$app->db->createCommand($sql4)->queryAll();
        return $list;


    }


    public static function lirun_ad_oroduct_tu($id,$year){
        $fordata=Yii::$app->hjd->teodate_month_121(12,$year.'-01-01');
        //查询公司所有产品线
        $adprlin=ContractRelevance::find()->select('b.id,b.name')->join('a left join','jd_product_line b','a.product_line=b.id')->where(['a.advertiser'=>$id])->groupBy('b.id')->asArray()->all();

        /*
         *

         *
         *
         * */

        foreach ($fordata as $key=>$val){

            $renew=RenewHuikuan::ad_renew_product_lirun($id,$val['start'],$val['end']);

            foreach ($adprlin as $k=>$v)
            {
                $prmoney[$v['name']]=array(
                  'prid'=>$v['id'],
                  'prlin' =>$v['name'],
                  'minrenewtime' => 0,
                  'minrenewmone' => 0,
                  'maxrenewtime' => 0,
                  'maxrenewmone' =>0,
                  'qiane' => 0,
                  'chongzhimoney' => 0,
                  'gs_lirun' => 0,
                  'xiaoshou_lirun' => 0,
                  'gefanmoney' => 0,
                  'ticheng_money' => 0
                );
            }

            foreach ($renew as $key1=>$val1)
            {
                $prmoney[$val1['prlin']]=$renew[$key1];
            }

            $data[$key]=array(
                'date'=>$val['start'],
                'data'=>$prmoney,
            );
        }


        return $data;

    }

    //提交给媒体打保证金  payment_type=41
    public static function add_margin_to_media(){

        $model = new RenewHuikuan();
        //接收数据并处理
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');


        //如果是生成续费
        $model->payment_time = strtotime(Yii::$app->getRequest()->getBodyParams()['payment_time']);

        $model->ctime = time();
        $model->xf_qiane=$model->money;
        $model->no_cost_xf=$model->show_money;
        $model->payment_type=41;


        //查询所属合同状态
        $contractinfo = JdContract::contractinfonomt($model->xf_contractid);

        /*
        //如果没有选择媒介合同
        if(empty($contractinfo['mht']['id']))
        {
            return array('code'=>'500','meg'=>'查找不到对应的媒介合同！');
        }

        //计算续费成本
        if($contractinfo['mht']['iszuofei']!=0)
        {

            return array('code'=>'500','meg'=>'媒介合同已经结束，不可以打保证金！');
        }
        */

        $model->xf_cost=$model->money;
        $model->ht_state=$contractinfo['contract_state'];//合同状态
        //计算回扣
        $model->yhuikou=0;
        $model->whuikou=0;
        if($model->save())
        {

            //写入保证金金额变动 -此处操作减少保证金余额
            self::money_change_jian($model->advertiser,$model->xf_contractid,'4-2-1',$model->money);

            return $model;
        }else
        {
            var_dump($model->errors);exit;
            throw new NotFoundHttpException('创建失败了');
        }


    }

    //续费驳回 续费ID几级审核
    public static function margin_m_shenhe($id,$audit_type,$audit=''){
        if($audit!='')
        {
            $audit=1; //审核状态 1通过 2 驳回
        }else
        {
            $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        }

        $xfinfo=self::findOne($id);//保证金详情
        //账户信息
        //$accountinfo=Account::findOne($xfinfo->account);


        //如果驳回了
        if($audit=='2')
        {
            //回款回款
            //写入金额变动 保证金余额加
            self::money_change($xfinfo->advertiser,$xfinfo->xf_contractid,'4-2-1',$xfinfo->money);
        }

        //查询续费需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'baozhengjin'])->one();
        //续费续费成功 并且全部审核已经通过 则复制一条媒介打保证金记录
        if($audit=='1' and $audit_type==$renew_shenhe['audit_count'])
        {
            //如果没有选择媒介合同就报错
            if($xfinfo->mht_id=='')
            {
                throw new \Exception('没有选择媒介合同');
            }

            $mrenew_model=new MrenewHuikuan();
            $mrenew_model->advertiser=$xfinfo['advertiser'];
            $mrenew_model->submituser=$xfinfo['submituser'];
            $mrenew_model->type=1;//1续费 2 打款
            $mrenew_model->xf_contractid=$xfinfo['xf_contractid'];
            $mrenew_model->market=$xfinfo['market'];
            $mrenew_model->account=$xfinfo['account'];
            $mrenew_model->appname=$xfinfo['appname'];
            //金额改为百度币
            $mrenew_model->money=$xfinfo['show_money'];
            //获取媒体合同信息

            $meiinfo=JdContract::findOne($xfinfo->mht_id);
            //获取媒介返点算出实付金额

            $mrenew_model->xf_qiane=$xfinfo['money']; //续费欠额
            $mrenew_model->xf_cost=$xfinfo['money']; //续费实付
            $mrenew_model->money=$xfinfo['money']; //续费实付
            $mrenew_model->rebates_proportion=$meiinfo['rebates_proportion'];//媒介合同返点
            $mrenew_model->mht_id=$meiinfo['id'];//媒介合同id

            $mrenew_model->show_money=$xfinfo['show_money'];
            $mrenew_model->payment_type=$xfinfo['payment_type'];
            $mrenew_model->payment_time=$xfinfo['payment_time'];
            $mrenew_model->note='生成保证金记录';
            $mrenew_model->contract_start=$xfinfo['contract_start'];
            $mrenew_model->contract_end=$xfinfo['contract_end'];
            $mrenew_model->ctime=time();
            $mrenew_model->audit_u=$xfinfo['audit_u'];
            $mrenew_model->audit=1;//1级默认审核通过
            $mrenew_model->audit_count=2;//2级默认审核通过
            $mrenew_model->xf_id=$xfinfo['id'];
            $mrenew_model->is_ultimate_shenhe=1;

            if(!$mrenew_model->save())
            {
                var_dump($mrenew_model->errors);
                die("出现严重错误，媒介保证金 记录自动生成失败。请联系CRM 管理员");
            }else
            {
                // echo $meiinfo['mht']['advertiser'].'=-='.$meiinfo['mht']['id'];exit;
                //媒体的合同保证金和公司保证金加--维度 客户给我们的保证金加
                MrenewHuikuan::money_change($meiinfo['advertiser'],$meiinfo['id'],'5-2',$xfinfo['money']);

                //新-续费对应打款
                //--MrenewHuikuan::mrenew_huikuan($meiinfo['mht']['id']);
            }


            //修改为最终审核
            $xfinfo->is_ultimate_shenhe=1;
            $xfinfo->save();
        }


    }



    //给媒介的打保证金列表
    public static function margin_m_list($id='',$shenhewhere2='',$role=''){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_renew_huikuan.id',0];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_renew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date') . "+1 day");

        if($start and $end)
        {
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_renew_huikuan.payment_time', $start], ['<', 'jd_renew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_renew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_renew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_renew_huikuan.payment_time',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');
        if($shenhe=='1')
        {
            $shenhewhere="jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_renew_huikuan.audit=0 or jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_renew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_renew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;
        //完成状态
        $is_accomplish=Yii::$app->request->post("is_ultimate_shenhe");

        $isdiankuan=Yii::$app->request->post("isdiankuan");
        if($isdiankuan=='1')
        {
            $shenhewhere.=' and jd_renew_huikuan.margin_istui=0 and jd_renew_huikuan.type=2  and jd_renew_huikuan.audit!=2';
        }
        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser as adname,jd_renew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,mht.state as mstate,cuad.advertiser as guanggaozhu,ren.id as issqtui')
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac'])
            ->leftJoin("jd_contract_relevance contre","jd_renew_huikuan.xf_contractid=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_contract ht','ht.id=contre.contract_id')
            ->leftJoin('jd_contract mht','mht.id=ac.mht_id')
            ->leftJoin('customer_advertiser cuad','jd_renew_huikuan.cu_ad_id=cuad.id')
            ->where("jd_renew_huikuan.payment_type in(41)")
            ->andFilterWhere($userswhere)
            ->andWhere($shenhewhere)
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.is_ultimate_shenhe'=>$is_accomplish])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->leftJoin('jd_renew_huikuan ren','ren.relevance_id=jd_renew_huikuan.id')
            ->asArray();
        $ar['sum']=$query->sum('jd_renew_huikuan.money');
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);

        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();

        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();





        return $ar;
    }

    //给媒体打保证金的详情
    public static function margin_da_info($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname'])->asArray()->one();

        //$contarat_info=JdContract::contractinfo_i($data['account']);
        $contarat_info=JdContract::contractinfonomt($data['xf_contractid']);
        //媒介合同
        //媒体合同信息


        $data['contractinfo']=$contarat_info;
        $data['contractinfo']['mht']=JdContract::find()->where(['id'=>$data['mht_id']])->with(['prlin',advertiser0])->asArray()->one();

        $account_cu_ad_info=RenewHuikuan::find()->select("cuad.advertiser as guanggaozhu")->join(' a left join','customer_advertiser cuad','a.cu_ad_id=cuad.id')->where(['a.id'=>$data['id']])->asArray()->one();
        $data['cu_ad_info']=$account_cu_ad_info;

        return $data;
    }

    public static function tuimargin($id){


        $xfinfo=RenewHuikuan::findOne($id);
        //生成退款
        $mrenew_model=new RenewHuikuan();
        $mrenew_model->advertiser=$xfinfo['advertiser'];
        $mrenew_model->submituser=$xfinfo['submituser'];

        $mrenew_model->xf_contractid=$xfinfo['xf_contractid'];
        $mrenew_model->market=$xfinfo['market'];
        $mrenew_model->account=$xfinfo['account'];
        $mrenew_model->appname=$xfinfo['appname'];
        //金额改为百度币
        $mrenew_model->money=$xfinfo['money'];

         // 账户信息
        // $account=Account::findOne($mrenew_model->account);
       // $mhtinfo=JdContract::findOne($account->mht_id);



        //获取媒体合同信息
        $meiinfo=JdContract::findOne($xfinfo->mht_id);
        //获取媒介返点算出实付金额





        $mrenew_model->xf_qiane=$xfinfo['money']; //续费欠额
        $mrenew_model->xf_cost=$xfinfo['money']; //续费实付
        $mrenew_model->money=$xfinfo['money']; //续费实付
        $mrenew_model->rebates_proportion=$meiinfo['rebates_proportion'];//媒介合同返点
        //$mrenew_model->mht_id=$meiinfo['mht']['id'];//媒介合同id

        $mrenew_model->show_money=$xfinfo['show_money'];
        $mrenew_model->payment_type=42;
        $mrenew_model->payment_time=$xfinfo['payment_time'];
        $mrenew_model->note='自动生成的退保证金记录';
        $mrenew_model->contract_start=$xfinfo['contract_start'];
        $mrenew_model->contract_end=$xfinfo['contract_end'];
        $mrenew_model->ctime=time();
        $mrenew_model->audit_u=$xfinfo['audit_u'];
        $mrenew_model->audit=0;//1级默认审核通过
        $mrenew_model->audit_count=1;//2级默认审核通过
        $mrenew_model->relevance_id=$xfinfo['id'];
        $mrenew_model->is_ultimate_shenhe=0;
        $mrenew_model->mht_id=$xfinfo['mht_id'];

        if(!$mrenew_model->save())
        {
            var_dump($mrenew_model->errors);
            die("出现严重错误，退保证金 记录自动生成失败。请联系CRM 管理员");
        }


    }



    //媒体退保证金审核
    public static function tui_margin_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回

        $renew_info=self::findOne($id);//退款详情


        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'margin_tuikaun'])->one();


        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {

            //  var_dump($renew_info->);exit;
            //客户合同
           // $kehuhetong=JdContract::contractinfo($renew_info['xf_contractid']);
            //账户信息
           // $accountinfo=Account::findOne($renew_info->account);
            $mhtinfo=JdContract::findOne($renew_info['mht_id']);
            //客户跟媒体的保证金减
            MrenewHuikuan::money_change($mhtinfo['advertiser'],$mhtinfo['id'],'5-2',$renew_info['money'],'jian');
            //增加公司保证金余额
            //写入金额变动 保证金余额加
            self::money_change($renew_info->advertiser,$renew_info->xf_contractid,'4-2-1',$renew_info->money);


            //查询此比退款对应的媒体保证金
            //$renone=MrenewHuikuan::findOne($id);


            RenewHuikuan::updateAll(['margin_istui'=>1],['id'=>$renew_info['relevance_id']]);

            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;

            $renew_info->save();





        }
    }

    public static function margin_tui_list($id='',$shenhewhere2='',$role=''){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.business'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_renew_huikuan.id',0];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_renew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date') . "+1 day");

        if($start and $end)
        {
            //$search_data=['and',['>=','jd_renew_huikuan.payment_time',$start],['<','jd_renew_huikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_renew_huikuan.payment_time', $start], ['<', 'jd_renew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_renew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_renew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_renew_huikuan.payment_time',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');
        if($shenhe=='1')
        {
            $shenhewhere="jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_renew_huikuan.audit=0 or jd_renew_huikuan.audit=1 and jd_renew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_renew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_renew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;
        //完成状态
        $is_accomplish=Yii::$app->request->post("is_ultimate_shenhe");


        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser as adname,jd_renew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina,ac.a_users,mht.state as mstate,cuad.advertiser as guanggaozhu')
            ->joinWith(['submituser0 subt','market0 markets','advertiser0 ad','account0 ac'])
            ->leftJoin("jd_contract_relevance contre","jd_renew_huikuan.xf_contractid=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_contract ht','ht.id=contre.contract_id')
            ->leftJoin('jd_contract mht','mht.id=ac.mht_id')
            ->leftJoin('customer_advertiser cuad','jd_renew_huikuan.cu_ad_id=cuad.id')
            ->where("jd_renew_huikuan.payment_type in(42)")
            ->andFilterWhere($userswhere)
            ->andWhere($shenhewhere)
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_renew_huikuan.is_ultimate_shenhe'=>$is_accomplish])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)

            ->asArray();
        $ar['sum']=$query->sum('jd_renew_huikuan.money');
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();





        return $ar;

    }


    public static function addbukuanbi(){
        $model = new RenewHuikuan();
        //接收数据并处理
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');


        $model->payment_time = strtotime(Yii::$app->getRequest()->getBodyParams()['payment_time']);

        $model->ctime = time();
        $model->backmoney_yue=$model->money;
        $model->payment_type=311;


        //跟系统计算账户比的公式进行对比。
        Yii::$app->hjd->show_money($model->money,$model->xf_contractid,$model->show_money);


        //查询所属合同状态
        $contractinfo = JdContract::contractinfo($model->account);

        $account=Account::findOne($model->account);


        $mhtinfo=JdContract::findOne($account->mht_id);

        /*
        //如果没有选择媒介合同
        if(empty($contractinfo['mht']['id']))
        {
            return array('code'=>'500','meg'=>'查找不到对应的媒介合同！');
        }
        */

        /*
        //计算续费成本
        if($contractinfo['mht']['iszuofei']!=0)
        {

            return array('code'=>'500','meg'=>'媒介合同已经结束，不可以续费！');
        }*/

        $fandian=($mhtinfo['rebates_proportion']+100)/100;//媒体返点
        $dlfandian=(100-$mhtinfo['dl_fandian'])/100;//代理返点
        $model->xf_cost=$model->show_money/$fandian*$dlfandian;//续费成本
        $model->ht_state=$contractinfo['contract_state'];//合同状态

        if($model->save())
        {
            return $model;
        }else{
            var_dump($model->errors);exit;
        }


    }


    public static function bukuanbi_shenhe($id,$audit_type){

        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $backmoney_info=self::findOne($id);//补款详情

        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'bukuanbi'])->one();

        //如果审核通过 并且是最后一级审核
        if($audit=='1' and $audit_type==$renew_shenhe['audit_count'])
        {

            //修改公司 合同补款值 （增）
            self::money_change($backmoney_info['advertiser'],$backmoney_info['xf_contractid'],3,$backmoney_info['money']);
            //读出转入合同详情
            $zrhtinfo=JdContract::contractinfonomt($backmoney_info->xf_contractid);



            //生成续费
            $renew=RenewHuikuan::add_renew([
                'advertiser'=>$backmoney_info->advertiser,
                'submituser'=>$backmoney_info->submituser,
                'xf_contractid'=>$backmoney_info->xf_contractid,
                'market'=>$zrhtinfo['market0']['id'],
                'account'=>$backmoney_info->account,
                'rebates_proportion'=>$zrhtinfo['rencontract']['fandian'],
                'money'=>$backmoney_info->money,
                'rebates_proportion'=>$zrhtinfo['rencontract']['fandian'],
                'show_money'=>$backmoney_info->show_money,
                'payment_type'=>'1',
                'payment_time'=>time(),
                'note'=>'由补账户币 生成的续费 crm系统操作',
                'cu_ad_id'=>$backmoney_info->cu_ad_id
            ]);



            //查询续费需要几级审核
            $renew_shenhe=AuditAction::find()->where(['action_name'=>'renew'])->one();
            for($i=1;$i<=$renew_shenhe['audit_count'];$i++)
            {
                Audit::audit_auto($renew[id],'renew',$i,'jd_renew_huikuan');
                RenewHuikuan::renew_shenhe($renew[id],$i,'1');
            }


            /*
            //如果勾选了备款账户
            $beikuan_account_id=Yii::$app->request->post('beikuan_account_id');



            if($beikuan_account_id!=''){
                //查询客户续费ID对应的媒介续费id
                $mrenew_model=MrenewHuikuan::find()->where(['xf_id'=>$renew['id']])->one();

                //判断账户余额是否足以抵消续费续费
                $bk_ac_info=BeikuanAccount::findOne($beikuan_account_id);
                if($bk_ac_info->yu_e < $mrenew_model->xf_cost)
                {
                    // throw new \Exception('不足以抵消！');
                }

                //抵扣金额
                $fk_money=Yii::$app->request->post('fk_money');
                //插入数据
                $ba_t_ren= new BeikuanAccountToRenew();
                $ba_t_ren->bk_account_id=$beikuan_account_id;
                $ba_t_ren->xf_id=$mrenew_model->id;
                $ba_t_ren->fk_money=$fk_money;
                $ba_t_ren->save();




                //如果备款账户选择了一直关联
                $beiluan_account_gl=Yii::$app->request->post('beikuan_account_gl');
                if($beiluan_account_gl=='1')
                {
                    //读取账户名字
                    $a_users=Account::findOne($mrenew_model->account);

                    //如果数据库里已经关联过这个账户就不新建啦
                    $bglcont=BeikuanAccountToRenewAccount::find()->where(['bk_account_id'=>$beikuan_account_id,'account'=>$a_users])->count();
                    if($bglcont==0){

                        $ba_gl=new BeikuanAccountToRenewAccount();
                        $ba_gl->bk_account_id=$beikuan_account_id;
                        $ba_gl->account=$a_users->a_users;
                        $ba_gl->save();
                    }
                }

                //续费欠额减
                MrenewHuikuan::updateAllCounters(['xf_qiane'=>-$fk_money],['id'=>$mrenew_model->id]);

                //备款账户减续费金额
                BeikuanAccount::updateAllCounters(['yu_e'=>-$fk_money],['id'=>$beikuan_account_id]);
                //媒介公司 合同 备款余额减
                //媒介公司 和 合同备款余额加
                $mhtinfo=JdContract::findOne($mrenew_model->mht_id);
                JdCustomer::updateAllCounters(['beikuan_yue'=>-$fk_money],['id'=>$mhtinfo->advertiser]);
                JdContract::updateAllCounters(['beikuan_yue'=>-$fk_money],['id'=>$mhtinfo->id]);


            }
                */


            //$this->global_audit($id,'refund_kehu',1,'jd_renew_huikuan');

            //JdCustomer::updateAllCounters(['qd_money'=>$backmoney_info->b_money],['id'=>$backmoney_info->advertiser]);
            //JdContract::updateAllCounters(['qd_money'=>$backmoney_info->b_money],['id'=>$backmoney_info->contract_id]);
            //修改退款的相关联id 保证退款和续费是一一对应
            //RenewHuikuan::updateAll(['relevance_id'=>$renew[id]],['id'=>$backmoney_info->id]);


            $backmoney_info->relevance_id=$renew[id];

            //修改为最终审核
            $backmoney_info->is_ultimate_shenhe=1;
            $backmoney_info->save();
        }
    }



    public static function shengcheng_tk_zhuanchu($tk_id){
        $info=RenewHuikuan::findOne($tk_id);


            //合同回款减少
            JdContract::updateAllCounters(['huikuan'=>-$info['money']],['id'=>$info['xf_contractid']]);
            //公司未分配余额增加
            JdCustomer::updateAllCounters(['tuikuan_undistributed_yu_e'=>$info['money']],['id'=>$info['advertiser']]);

            $model=new RenewHuikuan();
            $model->advertiser=$info['advertiser'];

            $model->type=$info['type'];
            $model->money=$info['money'];
            $model->payment_type=20;
            $model->payment_time=$info['payment_time'];
            $model->submituser=\Yii::$app->user->getId();
            $model->xf_contractid=$info['xf_contractid'];
            $model->is_huikuan=0;
            $model->market=$info['market'];
            //$model->xf_qiane=0;
            $model->audit=1;
            $model->audit_count=2;
            $model->audit_u=\Yii::$app->user->getId();
            $model->is_ultimate_shenhe=1;
            $model->ctime=time();
            $model->xf_qiane=$info['money'];
            $model->cu_ad_id=$info['cu_ad_id'];
            $model->note='系统因为退款到总账户生成的转出款！';
            if(!$model->save())
            {
                throw new ErrorException('生成转出退款失败');
            }else{
                RenewHuikuan::updateAll(['relevance_id'=>$model->id],['id'=>$tk_id]);
            }

            //--RenewHuikuan::renew_huikuan($info['id']);



    }

    public static function shengcheng_xf_zhuanru($xf_id){
        $info=RenewHuikuan::findOne($xf_id);


        //合同回款增加
        JdContract::updateAllCounters(['huikuan'=>$info['money']],['id'=>$info['xf_contractid']]);
        //公司未分配余额减少
        JdCustomer::updateAllCounters(['tuikuan_undistributed_yu_e'=>-$info['money']],['id'=>$info['advertiser']]);

        $model=new RenewHuikuan();
        $model->advertiser=$info['advertiser'];

        $model->type=$info['type'];
        $model->money=$info['money'];
        $model->payment_time=$info->payment_time;
        $model->submituser=\Yii::$app->user->getId();
        $model->xf_contractid=$info['xf_contractid'];
        $model->is_huikuan=1;
        $model->market=$info['market'];
        //$model->xf_qiane=0;
        $model->audit=1;
        $model->audit_count=2;
        $model->audit_u=\Yii::$app->user->getId();
        $model->is_ultimate_shenhe=1;
        $model->ctime=time();
        $model->backmoney_yue=$info['money'];
        $model->cu_ad_id=$info['cu_ad_id'];
        $model->relevance_id=$xf_id;
        $model->note='系统生成转入款，因为与做了退款抵扣';
        $model->ht_state=$info['ht_state'];
        $model->is_tk_deduction=1;
        if(!$model->save())
        {
            throw new ErrorException('生成转出退款失败');
        }else{
            RenewHuikuan::updateAll(['relevance_id'=>$model->id],['id'=>$xf_id]);
        }

        //--RenewHuikuan::renew_huikuan($info['id']);



    }




}


