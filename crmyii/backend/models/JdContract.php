<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "jd_contract".
 *
 * @property integer $id
 * @property integer $advertiser
 * @property string $contract_no
 * @property integer $agent_company
 * @property integer $product_line
 * @property integer $type
 * @property integer $contract_start
 * @property integer $contract_end
 * @property string $rebates_proportion
 * @property string $show_money
 * @property string $contract_money
 * @property integer $payment_type
 * @property integer $payment_time
 * @property string $note
 * @property integer $state
 * @property integer $the_archive
 * @property integer $audit_1
 * @property integer $audit_2
 * @property integer $submituser
 * @property integer $ctime
 * @property string $fk_money
 * @property string $margin
 * @property integer $isxufei
 * @property string $xf_hetonghao
 * @property string $appname
 * @property string $account
 * @property integer $isguidang
 * @property integer $users2
 * @property integer $susers1
 * @property integer $susers2
 * @property integer $xf_contractid
 * @property string $market
 * @property string $yu_e
 * @property string $huikuan
 * @property string $bukuan
 * @property double $invoice
 * @property integer $iszuofei
 * @property integer $is_meijie
 * @property integer $parent_id
 * @property integer $mht_id
 * @property string $dl_fandian
 * @property integer $contract_state
 * @property string $title
 * @property integer $market1
 * @property string $dakuan
 * @property integer $zhouqi
 * @property integer $is_copy
 */

class JdContract extends \yii\db\ActiveRecord
{

   // public $product_line;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_contract';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser', 'contract_no', 'agent_company',  'submituser', 'ctime','market'], 'required'],
            [['advertiser', 'agent_company', 'type', 'contract_start', 'payment_type',  'state', 'the_archive', 'audit','submituser', 'ctime', 'isxufei', 'isguidang', 'audit_u', 'xf_contractid', 'iszuofei', 'is_meijie', 'parent_id', 'mht_id', 'contract_state', 'zhouqi', 'is_copy','is_ultimate_shenhe','kh_grfd','m_xsfd','fd_type','clearing_form','pay_day','pay_scope','jiesuan_day','is_jstype'], 'integer'],
            [['note'], 'string'],
            [['yu_e', 'huikuan', 'bukuan', 'invoice', 'dakuan'], 'number'],
            [['contract_no', 'rebates_proportion'], 'string', 'max' => 50],
            [['fk_money', 'margin'], 'string', 'max' => 12],
            [['xf_hetonghao', 'account'], 'string', 'max' => 200],
            [['appname'], 'string', 'max' => 100],
            [['dl_fandian', 'title'], 'string', 'max' => 255],
            [['product_line','kh_grfd'],'default','value'=>0],
            ['contract_end','default','value'=>'null']
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
            'contract_no' => 'Contract No',
            'agent_company' => 'Agent Company',
            'product_line' => 'Product Line',
            'type' => 'Type',
            'contract_start' => 'Contract Start',
            'contract_end' => 'Contract End',
            'rebates_proportion' => 'Rebates Proportion',
            'show_money' => 'Show Money',
            'contract_money' => 'Contract Money',
            'payment_type' => 'Payment Type',
            'payment_time' => 'Payment Time',
            'note' => 'Note',
            'state' => 'State',
            'the_archive' => 'The Archive',

            'submituser' => 'Submituser',
            'ctime' => 'Ctime',
            'fk_money' => 'Fk Money',
            'margin' => 'Margin',
            'isxufei' => 'Isxufei',
            'xf_hetonghao' => 'Xf Hetonghao',
            'appname' => 'Appname',
            'account' => 'Account',
            'isguidang' => 'Isguidang',
            'xf_contractid' => 'Xf Contractid',
            'market' => 'Market',
            'yu_e' => 'Yu E',
            'huikuan' => 'Huikuan',
            'bukuan' => 'Bukuan',
            'invoice' => 'Invoice',
            'iszuofei' => 'Iszuofei',
            'is_meijie' => 'Is Meijie',
            'parent_id' => 'Parent ID',
            'mht_id' => 'Mht ID',
            'dl_fandian' => 'Dl Fandian',
            'contract_state' => 'Contract State',
            'title' => 'Title',
            'dakuan' => 'Dakuan',
            'zhouqi' => 'Zhouqi',
            'is_copy' => 'Is Copy',
        ];
    }
    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }
    //关联数据
    public function getMarket0(){
        return $this->hasOne(UserBackend::className(),['id'=>'market'])->select('id,name');
    }
    //关联数据
    /*
    public function getMht(){
        return $this->hasOne(JdContract::className(),['id'=>'mht_id'])->select('id,title,rebates_proportion,dl_fandian');
    }*/
    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'advertiser'])->select('id,advertiser,business,customer_type,true_advertiser');
    }
    //关联数据
    public function getPrlin(){
        return $this->hasOne(ProductLine::className(),['id'=>'product_line']);
    }
    //关联数据
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }
    //关联合同信息
    //关联数据
    public function getRencontract(){
        return $this->hasOne(ContractRelevance::className(),['contract_id'=>'id']);
    }
    //关联中间表
    public function getContractproduct(){
        return $this->hasMany(ProductLine::className(),['id'=>'product_line'])
            ->viaTable(ContractRelevance::tableName(),['contract_id'=>'id']);
    }

    /*
        public function getDailigongsi(){
        return $this->hasOne(ProductLine::className(),['id'=>'product_line']);
    }*/


    //销售只看自己的列表
    public static function contract_list($avid,$shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['jd_contract.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.business'=>$uid],['jd_contract.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_contract.id',0];
        }

        if($avid!='All')
        {
            $id=$avid;
        }else
        {
            $id=null;
        }

        //如果有媒介合同ID 则只显示媒介合同下的普通合同
        $mht_id=Yii::$app->request->post('mht_id');


        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_contract.appname' ,$Search_str],['like','subt.name',$Search_str],['like','markets.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            //$search_data=['and',['>','jd_contract.ctime',$start],['<','jd_contract.ctime',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_contract.ctime', $start], ['<', 'jd_contract.ctime', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_contract.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_contract.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_contract.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_contract.audit=1 and jd_contract.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_contract.audit=0 or (jd_contract.audit=1 and jd_contract.is_ultimate_shenhe=0)";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_contract.audit=2";
        }else
        {
            $shenhewhere="jd_contract.id>0";
        }
        $shenhewhere.=$shenhewhere2;
        //合同属性
        $contract_state=Yii::$app->request->post('contract_state');
        //归档条件
        $isguidang=Yii::$app->request->post('isguidang');
        //产品线条件
        $pr_id=Yii::$app->request->post('pr_id');
        //是否作废条件
        $iszuofei=Yii::$app->request->post('iszuofei');
        /*
        if($iszuofei==0 && isset($iszuofei))
        {
            $zuofeiwhere=array(0,2);
        }elseif($iszuofei==1)
        {
            $zuofeiwhere=1;
        }*/
        $zuofeiwhere=$iszuofei;
        //如果有合同id 就只显示此合同的协议
        $htid=Yii::$app->request->post('htid');




        //查询自己的组
        $query=self::find()->select('jd_contract.*,subt.name as submitname,markets.name as marketname,pr.name as prlin,ren.fandian,cuad.advertiser as guanggaozhu,(jd_contract.huikuan+jd_contract.bukuan-jd_contract.yu_e) as balance')
            ->where("jd_contract.is_meijie=0")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['jd_contract.iszuofei'=>$zuofeiwhere,'jd_contract.advertiser'=>$id,'jd_contract.contract_state'=>$contract_state,'jd_contract.isguidang'=>$isguidang,'pr.id'=>$pr_id,'jd_contract.contract_new_id'=>$htid])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['advertiser0 ad'=>function($query){
            $query->select(['id','advertiser','business'])->with(
                ['business0'=>function($query){
                   $query->select(['id','name']);
                }
            ]);
        }])->joinWith(['submituser0 subt','market0 markets','rencontract ren'])->with(['contractproduct'])->leftJoin('jd_product_line pr','pr.id=ren.product_line')->leftJoin('customer_advertiser cuad','jd_contract.cu_ad_id=cuad.id')->asArray();

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
               'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'iszuofei'=>SORT_ASC,
                    'ctime' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);
        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();
        foreach ($ar['data'] as $key=>$val)
        {
            $ar['data'][$key]['balance']=round($val['huikuan']+$val['bukuan']-$val['yu_e']-$val['kehu_bukuan'],2);
        }
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();

        return $ar;
    }

    //修改合同字段
    public static function updataContract($id,$field,$sava_data){
        $data=self::findOne($id);

        $data->$field=$sava_data;

        if($data->save())
        {
            return true;
        }else
        {
            var_dump($data->errors);exit;
        }
    }

    public static function contractinfonomt($id){
        $data=self::find()->where(['id'=>$id])->With(['submituser0','market0','advertiser0','audituname','rencontract','contractproduct'])->asArray()->one();
        //负责商务
        if($data['advertiser0']['business']){
            $data0=UserBackend::find()->select('name')->where("id=".$data['advertiser0']['business'])->one();
            $data['business']=$data0->name;
        }else
        {
            $data['business']='';
        }

        //查询合同产品线信息
        $htgl=ContractRelevance::find()->where(['contract_id'=>$id])->one();
        $prlin=ProductLine::findOne($htgl->product_line);
        $data['mht']['prlin']=$prlin;
   

        //代理公司
        $data1=AgentCompany::find()->select('companyname,id')->where("id=".$data['agent_company'])->one();
        $data['agent_company']=$data1->companyname;
        $data['agent_company_id']=$data1->id;
        $account_cu_ad_info=JdContract::find()->select("cuad.advertiser as guanggaozhu")->join(' a left join','customer_advertiser cuad','a.cu_ad_id=cuad.id')->where(['a.id'=>$data['id']])->asArray()->one();
        $data['cu_ad_info']=$account_cu_ad_info;
        $data['balance']=round($data['huikuan']+$data['bukuan']-$data['yu_e']-$data['kehu_bukuan'],2);

        return $data;
    }

    //查看合同详情
    public static function contractinfo($accountid){

        /*
         * 2018年5月11日16:56:04
         * ->with(['mht'=>function($query){
            $query->select("id,title,product_line,rebates_proportion,dl_fandian,advertiser,iszuofei,state")
                ->with(['prlin'=>function($query){
                    $query->select("id,name,fk_type");
                }])->with(['advertiser0'=>function($query){
                    $query->select("id,advertiser as madname");
                }]);
        }])->asArray()
         *
         * */
        $accountinfo=Account::findOne($accountid);
        //媒体合同信息
        $mht=JdContract::find()->where(['id'=>$accountinfo->mht_id])->with(['prlin',advertiser0])->asArray()->one();


        //具体数据
        $data=self::find()->where(['id'=>$accountinfo->contract_id])->With(['submituser0','market0','advertiser0','audituname','rencontract','contractproduct'])->asArray()->one();
        $data['mht']=$mht;
        //负责商务
        if($data['advertiser0']['business']){
            $data0=UserBackend::find()->select('name')->where("id=".$data['advertiser0']['business'])->one();
             $data['business']=$data0->name;
        }else
        {
            $data['business']='';
        }

        //代理公司
        $data1=AgentCompany::find()->select('companyname,id')->where("id=".$data['agent_company'])->one();
        $data['agent_company']=$data1->companyname;
        $data['agent_company_id']=$data1->id;
        $account_cu_ad_info=JdContract::find()->select("cuad.advertiser as guanggaozhu")->join(' a left join','customer_advertiser cuad','a.cu_ad_id=cuad.id')->where(['a.id'=>$data['id']])->asArray()->one();
        $data['cu_ad_info']=$account_cu_ad_info;

     return $data;
    }


    //获取合同下所有账户
    public static function contact_account($ht_id){
        return Account::find()->where(['contract_id'=>$ht_id])->andWhere("endtime='4092599349'")->all();
    }

    //获取合同概述和媒介合同信息
    //查看合同详情
    public static function contractinfo_i($accountid){
        $accountinfo=Account::findOne($accountid);
        //媒体合同信息
        $mht=JdContract::find()->where(['id'=>$accountinfo->mht_id])->with(['prlin',advertiser0])->asArray()->one();


        //具体数据
        $data=self::find()->where(['id'=>$accountinfo->contract_id])->With(['submituser0','market0','advertiser0','audituname','contractproduct'])->asArray()->one();
        $data['mht']=$mht;
        //负责商务
        if($data['advertiser0']['business']){
        $data0=UserBackend::find()->select('name')->where("id=".$data['advertiser0']['business'])->one();
        $data['business']=$data0->name;
        }else
        {
            $data['business']='';
        }

        if($data['agent_company']){
            //代理公司
            $data1=AgentCompany::find()->select('companyname,id')->where("id=".$data['agent_company'])->one();
            $data['agent_company']=$data1->companyname;
            $data['agent_company_id']=$data1->id;
        }else
            {
                $data['agent_company']='';
                $data['agent_company_id']='';
            }
        //账户返点和现金返点
        $data['contract_fdinfo']=ContractRelevance::find()->where(['contract_id'=>$accountinfo->contract_id])->one();

        return $data;
    }

    //查看合同详情
    public static function contractinfo_mei($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','market0','advertiser0','audituname','prlin'])->asArray()->one();
        //负责商务
        if($data['advertiser0']['business']){
            $data0=UserBackend::find()->select('name')->where("id=".$data['advertiser0']['business'])->one();
            $data['business']=$data0->name;
        }else
        {
            $data['business']='';
        }

        if($data['agent_company']){
            //代理公司
            $data1=AgentCompany::find()->select('companyname,id')->where("id=".$data['agent_company'])->one();
            $data['agent_company']=$data1->companyname;
            $data['agent_company_id']=$data1->id;
        }else
        {
            $data['agent_company']='';
            $data['agent_company_id']='';
        }
        return $data;
    }



    //新增合同返回
    public static function add_countract_ru(){
        $postdata=\Yii::$app->request->post();
        //合同状态
        /*
         * 查询客户之前是否有签过合同，如果没有签过就是新客 如果有合同就判断从第一个合同开始的三个月内有没有新的产品线合同，如果有就属于老客新开，如果没有或者大于三个月就属于老客户
         * 每次回款的时候更新合同的状态，如果大于一年则是老客户
         * 合同状态:1新客，2 老客，3 老客新开
         */
        //查出这个客户之前有没有签过合同并且已经续费
        //$newke=M("RenewHuikuan")->field('payment_time')->where(" payment_type in (1,2) and advertiser=".I('post.advertiser'))->order("payment_time asc")->find();

        $newke=RenewHuikuan::find()->select(['payment_time'])->where(" payment_type in (1,2) and advertiser=".$postdata['advertiser'])->orderBy('payment_time asc')->one();
        if($newke)
        {

            //提交合同的产品线
            $prlininfo=ProductLine::findOne($postdata['product_line'][0]);

            //相同产品线的合同信息 (相同产品线是从产品线表的三级标识读出来的 产品线可能会多个产品线为一个产品线 比如 百度搜索直开 百度搜索套壳 都是百度搜索)
            $one_contract=self::find()
                ->select('a.contract_state')
                ->join('a left join','jd_contract_relevance b','a.id=b.contract_id')
                ->leftJoin('jd_product_line c','c.id=b.product_line')
                ->where("a.advertiser=".$postdata['advertiser']." and c.three='".$prlininfo->three."'")
                ->orderBy("a.id desc")
                ->one();

            if($one_contract['contract_state']!='')
            {
                $contract_state=$one_contract['contract_state'];//读取上一个合同的状态
            }else
            {
                $contract_state=3;//老客新开
            }



        }else{
            $contract_state=1;//没有任何续费则为新客
        }


        $hetong=new JdContract();


        $hetong->load(\Yii::$app->getRequest()->getBodyParams(),'');

        $hetong->contract_start=strtotime($hetong->contract_start);
        $hetong->contract_end=strtotime($hetong->contract_end);
        //$hetong->payment_time=strtotime($hetong->payment_time);
        $hetong->ctime=time();

        $hetong->contract_state=$contract_state;


        if($postdata['copy']=='1')
        {
            $hetong->is_copy=1;
        }

        if($hetong->save())
        {
            //插入合同关联字段

            $contract_relevance= new ContractRelevance();
           // $contract_relevance->money=$postdata['money'][0];
           // $contract_relevance->xianshijine=$postdata['xianshijine'][0];

            $contract_relevance->money=0;
            $contract_relevance->xianshijine=0;
            $contract_relevance->product_line=$postdata['product_line'][0];
            $contract_relevance->fandian=$postdata['fandian'][0];
            $contract_relevance->xj_fandian=$postdata['xj_fandian'][0];
            $contract_relevance->advertiser=$postdata['advertiser'];
            $contract_relevance->contract_id=$hetong->id;
           if(!$contract_relevance->save())
           {
               return array("code"=>'500',"msg"=>"保存合同关联数据没成功 所以导致提交合同失败","error"=>$contract_relevance->errors);
           }


            //如果是复制的合同 并且复制账户啦
            if(count($postdata['account_list']) > 0 && is_array($postdata['account_list']))
            {
                foreach ($postdata['account_list'] as $key=>$val)
                {
                    //查询复制账户信息
                    $account_info=Account::findOne($val);
                    //复制这些账户
                    $new_account=new Account();

                    foreach ($account_info as $key=>$val)
                    {
                        if($key!='id' and $key!='contract_id'){
                          $new_account->$key=$val;
                        }
                    }
                    $new_account->contract_id=$hetong->id;
                    $new_account->ctime=time();
                    $new_account->save();
                    //修改账户结束时间
                    $account_info->endtime=time();
                    $account_info->save();
                }
            }
        }else{

            return array("code"=>'500',"msg"=>"保存合同关联数据没成功 所以导致提交合同失败","error"=>$hetong->errors);
        }
    }


    //合同审核操作
    public static function contract_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $contract=self::findOne($id);//合同详情
        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'contract'])->one();
        //如果审核通过 并且是最后一级审核

        if($audit!='2' and $audit_type==$renew_shenhe['audit_count'])
        {
            //修改为最终审核
            $contract->is_ultimate_shenhe=1;
            if(!$contract->save()){
                var_dump($contract->errors);
                exit;

            }

        }
    }


    //媒介合同审核操作
    public static function mcontract_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $contract=self::findOne($id);//合同详情
        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie_contract'])->one();
        //如果审核通过 并且是最后一级审核

        if($audit!='2' and $audit_type==$renew_shenhe['audit_count'])
        {
            //修改为最终审核
            $contract->is_ultimate_shenhe=1;
            if(!$contract->save()){
                var_dump($contract->errors);
                exit;

            }

        }
    }

    //查看媒介合同列表
    //销售只看自己的列表
    public static function meijie_contract_list($avid,$shenhewhere2='',$role=''){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['jd_contract.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.business'=>$uid],['jd_contract.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_contract.id',0];
        }

        if($avid!='All')
        {
            $id=$avid;
        }else
        {
            $id=null;
        }


        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_contract.appname' ,$Search_str],['like','jd_contract.title' ,$Search_str],['like','pr.name' ,$Search_str],['like','jd_contract.title' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
           // $search_data=['and',['>','jd_contract.ctime',$start],['<','jd_contract.ctime',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_contract.ctime', $start], ['<', 'jd_contract.ctime', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_contract.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_contract.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_contract.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_contract.audit=1 and jd_contract.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_contract.audit=0 or jd_contract.audit=1 and jd_contract.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_contract.audit=2";
        }else
        {
            $shenhewhere="jd_contract.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //合同属性
        $contract_state=Yii::$app->request->post('contract_state');
        //归档条件
        $isguidang=Yii::$app->request->post('isguidang');
        //产品线条件
        $pr_id=Yii::$app->request->post('pr_id');
        //是否作废条件
        $iszuofei=Yii::$app->request->post('iszuofei');
        if($iszuofei==0 && isset($iszuofei))
        {
            $zuofeiwhere=array(0,2);
        }elseif($iszuofei==1)
        {
            $zuofeiwhere=1;
        }


        //查询自己的组
        $query=self::find()->select('jd_contract.*,subt.name as submitname,pr.name as prlin,(jd_contract.dakuan-jd_contract.yu_e) as balance')->where("jd_contract.is_meijie=1")->andWhere($shenhewhere)->andFilterWhere($userswhere)->andFilterWhere(['jd_contract.iszuofei'=>$zuofeiwhere,'jd_contract.advertiser'=>$id,'jd_contract.contract_state'=>$contract_state,'jd_contract.isguidang'=>$isguidang,'pr.id'=>$pr_id])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['advertiser0 ad'=>function($query){
            $query->select(['id','advertiser','business'])->with(
                ['business0'=>function($query){
                    $query->select(['id','name']);
                }
                ]);
        }])->joinWith(['submituser0 subt'])->leftJoin('jd_product_line pr','pr.id=jd_contract.product_line')->asArray();

        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'iszuofei'=>SORT_ASC,
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





    //查看媒介合同列表
    //销售只看自己的列表
    public static function meijie_margin_list(){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['jd_contract.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.business'=>$uid],['jd_contract.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_contract.id',0];
        }

        if($avid!='All')
        {
            $id=$avid;
        }else
        {
            $id=null;
        }


        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_contract.title' ,$Search_str],['like','pr.name' ,$Search_str],['like','jd_contract.title' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            //$search_data=['and',['>','jd_contract.ctime',$start],['<','jd_contract.ctime',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_contract.ctime', $start], ['<', 'jd_contract.ctime', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_contract.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_contract.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_contract.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_contract.audit=1 and jd_contract.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_contract.audit=0 or jd_contract.audit=1 and jd_contract.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_contract.audit=2";
        }else
        {
            $shenhewhere="jd_contract.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //合同属性
        $contract_state=Yii::$app->request->post('contract_state');

        //产品线条件
        $pr_id=Yii::$app->request->post('pr_id');





        //查询自己的组
        $query=self::find()->select("sum(jd_contract.margin_meiti) as margin_meiti,ad.advertiser,pr.name as prlin")
            ->where("jd_contract.is_meijie=1 and jd_contract.margin_meiti>0")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['jd_contract.advertiser'=>$id,'jd_contract.contract_state'=>$contract_state,'pr.id'=>$pr_id])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt'])
            ->leftJoin('jd_product_line pr','pr.id=jd_contract.product_line')
            ->leftJoin('jd_customer ad','ad.id=jd_contract.advertiser')
            ->groupBy('ad.advertiser,pr.name')
            ->asArray();
        $ar['sum']=$query->sum('margin_meiti');
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'margin_meiti'=>SORT_ASC,
                    //'ctime' => SORT_DESC,
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



    //查看媒介合同详情
    public static function meijie_contractinfo($id){

        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','market0','advertiser0','audituname','rencontract','prlin'])->asArray()->one();
        //负责商务
        if($data['advertiser0']['business']!='')
        {
            $data0=UserBackend::find()->select('name')->where("id=".$data['advertiser0']['business'])->one();

        }else
        {
            $data0='';
        }
        $data['business']=$data0->name;
        //代理公司
        if($data['agent_company']){
          $data1=AgentCompany::find()->select('companyname,id')->where("id=".$data['agent_company'])->one();
            $data['agent_company']=$data1->companyname;
            $data['agent_company_id']=$data1->id;
        }else
        {
            $data['agent_company']='';
            $data['agent_company_id']='';
        }

        $data['balance']=round($data['dakuan']-$data['yu_e']-$data['fakuan']-$data['beikuan_yue'],2);
        return $data;
    }




    public static function meijie_add_countrat(){

    }


    //销售只看自己的列表
    public static function margin_contract_list(){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['jd_contract.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.business'=>$uid],['jd_contract.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_contract.id',0];
        }

        if($avid!='All')
        {
            $id=$avid;
        }else
        {
            $id=null;
        }

        //如果有媒介合同ID 则只显示媒介合同下的普通合同
        $mht_id=Yii::$app->request->post('mht_id');


        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_contract.appname' ,$Search_str],['like','subt.name',$Search_str],['like','markets.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            //$search_data=['and',['>','jd_contract.ctime',$start],['<','jd_contract.ctime',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_contract.ctime', $start], ['<', 'jd_contract.ctime', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_contract.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_contract.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_contract.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_contract.audit=1 and jd_contract.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_contract.audit=0 or (jd_contract.audit=1 and jd_contract.is_ultimate_shenhe=0)";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_contract.audit=2";
        }else
        {
            $shenhewhere="jd_contract.id>0";
        }
        $shenhewhere.=$shenhewhere2;
        //合同属性
        $contract_state=Yii::$app->request->post('contract_state');
        //归档条件
        $isguidang=Yii::$app->request->post('isguidang');
        //产品线条件
        $pr_id=Yii::$app->request->post('pr_id');
        //是否作废条件
        $iszuofei=Yii::$app->request->post('iszuofei');
        if($iszuofei==0 && isset($iszuofei))
        {
            $zuofeiwhere=array(0,2);
        }elseif($iszuofei==1)
        {
            $zuofeiwhere=1;
        }

        //如果有合同id 就只显示此合同的协议
        $htid=Yii::$app->request->post('htid');




        //查询自己的组
        $query=self::find()
            ->select('ad.advertiser,sum(jd_contract.margin_money) as margin_money,markets.name as marketname,pr.name as prlin')
            ->where("jd_contract.is_meijie=0 and jd_contract.margin_money>0")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['jd_contract.iszuofei'=>$zuofeiwhere,'jd_contract.advertiser'=>$id,'jd_contract.contract_state'=>$contract_state,'pr.id'=>$pr_id])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['market0 markets'])
            ->leftJoin('jd_contract_relevance crn','crn.contract_id=jd_contract.id')
            ->leftJoin('jd_product_line pr','pr.id=crn.product_line')
            ->leftJoin('jd_customer ad','ad.id=jd_contract.advertiser')
            //->leftJoin('customer_advertiser cuad','jd_contract.cu_ad_id=cuad.id')
            ->groupBy('ad.advertiser,pr.name,markets.name')
            ->asArray();

        $ar['sum']=$query->sum('margin_money');
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    //'iszuofei'=>SORT_ASC,
                   // 'ctime' => SORT_DESC,
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

}
