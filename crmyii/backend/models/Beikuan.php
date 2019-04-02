<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "jd_mrenew_huikuan".
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
 * @property integer $mht_id
 * @property string $dakuan_yue
 * @property integer $xf_id
 * @property string $shishifu_onid
 * @property integer $ht_state
 * @property integer $is_ultimate_shenhe
 */
class Beikuan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'beikuan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['advertiser', 'money', 'payment_time', 'submituser', 'ctime', 'xf_contractid'], 'required'],
            [['advertiser', 'product_line', 'xf_id','payment_type', 'payment_time', 'audit_u', 'audit', 'submituser', 'ctime', 'audit_u', 'audit_count', 'xf_contractid', 'is_huikuan', 'account', 'mht_id', 'ht_state','is_ultimate_shenhe'], 'integer'],
            [['show_money', 'money', 'xf_cost', 'xf_qiane', 'backmoney_yue', 'dakuan_yue'], 'number'],
            [['note'], 'string'],
            [['rebates_proportion'], 'string', 'max' => 50],
            [['appname'], 'string', 'max' => 100],
            //[['huikuanren'], 'string', 'max' => 255],
            ['shishifu_onid','default', 'value'=>0]
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
            'submituser' => 'Submituser',
            'ctime' => 'Ctime',
            'appname' => 'Appname',
            'users2' => 'Users2',
            'xf_contractid' => 'Xf Contractid',
            'is_huikuan' => 'Is Huikuan',
            'account' => 'Account',
            'market' => 'Market',
            'huikuanren' => 'Huikuanren',
            'xf_cost' => 'Xf Cost',
            'xf_qiane' => 'Xf Qiane',
            'is_accomplish' => 'Is Accomplish',
            'accomplish_users' => 'Accomplish Users',
            'backmoney_yue' => 'Backmoney Yue',
            'mht_id' => 'Mht ID',
            'dakuan_yue' => 'Dakuan Yue',
            'xf_id' => 'Xf ID',
            'shishifu_onid' => 'Shishifu Onid',
            'ht_state' => 'Ht State',
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
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'advertiser'])->select('id,advertiser,customer_type');
    }

    //关联账户
    public function getAccount0(){
        return $this->hasOne(BeikuanAccount::className(),['id'=>'account'])->select('id,a_users,state,a_password,promote_url,mt_payment_type,dk_date,dk_zhouqi');
    }
    //关联审核人
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }
    //关联数据
    public function getContract(){
        return $this->hasOne(JdContract::className(),['id'=>'mht_id'])->select('id,mht_id');
    }



    //媒介退打款
    public static function beikuan_list($shenhewhere2='',$role=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','beikuan.id',0];
        }

        //筛选条件
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','beikuan.appname' ,$Search_str],['like','subt.name',$Search_str],['like','ac.a_users',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");

        if($start and $end)
        {
            //$search_data=['and',['>=','beikuan.payment_time',$start],['<','beikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'beikuan.payment_time', $start], ['<', 'beikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'beikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'beikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','beikuan.payment_time',1]];
        }


        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="beikuan.audit=1 and beikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="beikuan.audit=0 or beikuan.audit=1 and beikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="beikuan.audit=2";
        }else
        {
            $shenhewhere="beikuan.id>0";
        }

        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");




        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,beikuan.*,ac.a_users,subt.name as submitname,prlin.name as prlina,ac.a_users,ht.title')
            ->where("beikuan.payment_type in(1,2)")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['is_accomplish'=>$is_accomplish])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','advertiser0 ad','account0 ac'])
            ->leftJoin('jd_contract ht','ht.id=beikuan.mht_id')
            ->leftJoin("jd_product_line prlin","prlin.id=ht.product_line")

            ->asArray();


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



    //媒介退打款
    public static function bukuan_list($shenhewhere2='',$role=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','beikuan.id',0];
        }

        //筛选条件
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','beikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");

        if($start and $end)
        {
            //$search_data=['and',['>=','beikuan.payment_time',$start],['<','beikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'beikuan.payment_time', $start], ['<', 'beikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'beikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'beikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','beikuan.payment_time',1]];
        }


        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="beikuan.audit=1 and beikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="beikuan.audit=0 or beikuan.audit=1 and beikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="beikuan.audit=2";
        }else
        {
            $shenhewhere="beikuan.id>0";
        }

        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");




        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,beikuan.*,ac.a_users,subt.name as submitname,prlin.name as prlina,ac.a_users,ht.title')
            ->where("beikuan.payment_type in(3)")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['is_accomplish'=>$is_accomplish])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','advertiser0 ad','account0 ac'])
            ->leftJoin('jd_contract ht','ht.id=beikuan.mht_id')
            ->leftJoin("jd_product_line prlin","prlin.id=ht.product_line")

            ->asArray();


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



    //添加续费
    public static function add_beikuan(){

        $model = new Beikuan();
        //接收数据并处理
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');


        $model->payment_time = strtotime($model->payment_time);
        $model->ctime = time();
        $model->xf_qiane=$model->money;
        //$model->xf_qiane=$model->money;
        $model->xf_cost=$model->money;
        $model->mht_id=$model->xf_contractid;
        $model->dakuan_yue=$model->money;
        //$model->payment_type=1;
        //$model->no_cost_xf=$model->show_money;

        $mtinfo=JdContract::findOne($model->xf_contractid);

        //跟系统计算账户比的公式进行对比。
        $money_db=round($model->show_money/(1+($model->rebates_proportion/100))*(1-($mtinfo->dl_fandian/100)),2);
        if($money_db>$model->money)
        {
            if($money_db-$model->money >0.05)
            {
                throw new \Exception('错误!前端账户金额跟系统计算不一致！（应为：）'.$money_db);
            }
        }
        //Yii::$app->hjd->show_money($model->money,$model->xf_contractid,$model->show_money);




        //查询所属合同状态
        $contractinfo = JdContract::contractinfonomt($model->xf_contractid);


        //如果是预付查询合同余额是否足以低效此次续费 -$contractinfo['margin_money']
      //  $yue=$contractinfo['huikuan']+$contractinfo['bukuan']-$contractinfo['yu_e'];

        /*
        $fandian=($contractinfo['mht']['rebates_proportion']+100)/100;//媒体返点
        $dlfandian=(100-$contractinfo['mht']['dl_fandian'])/100;//代理返点
        $model->xf_cost=$model->show_money/$fandian*$dlfandian;//续费成本
        $model->ht_state=$contractinfo['contract_state'];//合同状态

        //计算回扣
        $model->yhuikou=$model->money-$model->money*(100+$model->rebates_proportion)/(100+$model->rebates_proportion+$contractinfo->kh_grfd);

        $model->whuikou=$model->yhuikou;
         */
        if($model->save())
        {

            //写入金额变动
            //self::money_change($model->advertiser,$model->xf_contractid,$model->payment_type,$model->money);


            return $model;
        }else
        {
            var_dump($model->errors);exit;
            return $model->errors;
        }


    }

    //添加补款
    //添加续费
    public static function add_bukuan(){

        $model = new Beikuan();
        //接收数据并处理
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');


        $model->payment_time = strtotime($model->payment_time);
        $model->ctime = time();
        $model->xf_qiane=$model->money;
        //$model->xf_qiane=$model->money;
        $model->xf_cost=$model->money;
        $model->mht_id=$model->xf_contractid;
        $model->dakuan_yue=$model->money;
        $model->payment_type=3;
        //$model->no_cost_xf=$model->show_money;

        $mtinfo=JdContract::findOne($model->xf_contractid);

        //跟系统计算账户比的公式进行对比。
        $money_db=round($model->show_money/(1+($model->rebates_proportion/100))*(1-($mtinfo->dl_fandian/100)),2);
        if($money_db>$model->money)
        {
            if($money_db-$model->money >0.05)
            {
                throw new \Exception('错误!前端账户金额跟系统计算不一致！（应为：）'.$money_db);
            }
        }
        //Yii::$app->hjd->show_money($model->money,$model->xf_contractid,$model->show_money);




        //查询所属合同状态
        $contractinfo = JdContract::contractinfonomt($model->xf_contractid);


        //如果是预付查询合同余额是否足以低效此次续费 -$contractinfo['margin_money']
        //  $yue=$contractinfo['huikuan']+$contractinfo['bukuan']-$contractinfo['yu_e'];

        /*
        $fandian=($contractinfo['mht']['rebates_proportion']+100)/100;//媒体返点
        $dlfandian=(100-$contractinfo['mht']['dl_fandian'])/100;//代理返点
        $model->xf_cost=$model->show_money/$fandian*$dlfandian;//续费成本
        $model->ht_state=$contractinfo['contract_state'];//合同状态

        //计算回扣
        $model->yhuikou=$model->money-$model->money*(100+$model->rebates_proportion)/(100+$model->rebates_proportion+$contractinfo->kh_grfd);

        $model->whuikou=$model->yhuikou;
         */
        if($model->save())
        {

            //写入金额变动
            //self::money_change($model->advertiser,$model->xf_contractid,$model->payment_type,$model->money);


            return $model;
        }else
        {
            var_dump($model->errors);exit;
            return $model->errors;
        }


    }

    //退款到备款账户
    public static function add_tuikuan(){

        $model = new Beikuan();
        //接收数据并处理
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');


        $model->payment_time = strtotime($model->payment_time);
        $model->ctime = time();
        $model->xf_qiane=$model->money;
        //$model->xf_qiane=$model->money;
        $model->xf_cost=$model->money;
        $model->mht_id=$model->xf_contractid;
        $model->dakuan_yue=$model->money;
        $model->payment_type=3;
        //$model->no_cost_xf=$model->show_money;

        $mtinfo=JdContract::findOne($model->xf_contractid);

        //跟系统计算账户比的公式进行对比。
        $money_db=round($model->show_money/(1+($model->rebates_proportion/100))*(1-($mtinfo->dl_fandian/100)),2);
        if($money_db>$model->money)
        {
            if($money_db-$model->money >0.05)
            {
                throw new \Exception('错误!前端账户金额跟系统计算不一致！（应为：）'.$money_db);
            }
        }
        //Yii::$app->hjd->show_money($model->money,$model->xf_contractid,$model->show_money);




        //查询所属合同状态
        $contractinfo = JdContract::contractinfo($model->xf_contractid);


        //如果是预付查询合同余额是否足以低效此次续费 -$contractinfo['margin_money']
        //  $yue=$contractinfo['huikuan']+$contractinfo['bukuan']-$contractinfo['yu_e'];

        /*
        $fandian=($contractinfo['mht']['rebates_proportion']+100)/100;//媒体返点
        $dlfandian=(100-$contractinfo['mht']['dl_fandian'])/100;//代理返点
        $model->xf_cost=$model->show_money/$fandian*$dlfandian;//续费成本
        $model->ht_state=$contractinfo['contract_state'];//合同状态

        //计算回扣
        $model->yhuikou=$model->money-$model->money*(100+$model->rebates_proportion)/(100+$model->rebates_proportion+$contractinfo->kh_grfd);

        $model->whuikou=$model->yhuikou;
         */
        if($model->save())
        {

            //写入金额变动
            //self::money_change($model->advertiser,$model->xf_contractid,$model->payment_type,$model->money);


            return $model;
        }else
        {
            var_dump($model->errors);exit;
            return $model->errors;
        }


    }



    public static function beikuan_info($id){
        //具体数据
        $data=self::find()->where(['id'=>$id])->With(['submituser0','account0','advertiser0','audituname'])->asArray()->one();
        $contarat_info=JdContract::meijie_contractinfo($data['xf_contractid']);
        $data['contractinfo']=$contarat_info;
        return $data;
    }

    //退款审核
    public static function beikuan_shenhe($id,$audit_type,$state=''){
        if($state==''){
            $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回

        }else
        {
            $audit=$state;
        }

        $renew_info=self::findOne($id);//退款详情
        $mhtinfo=JdContract::findOne($renew_info['mht_id']);


        //查询退款需要几级审核hhhhhh
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'beikuan'])->one();


        //查询父子流程

        $fzflow=Flow::find()->where(['fu_flow'=>'beikuan','is_true'=>1])->asArray()->all();

        foreach ($fzflow as $key =>$val)
        {

            //如果子流程等于打款的话
            if($val['zi_flow']=='dakuan')
            {
                //如果备款是预付就生成一笔打款跟这笔备款对应！
                if($renew_info->payment_type=='1' && $audit_type==($val['fu_audit']-1)){

                    $dakuan= new MbackMoney();
                    $dakuan->advertiser=$renew_info->advertiser;
                    $dakuan->b_company=$mhtinfo->agent_company;
                    $dakuan->b_money=$renew_info->money;
                    $dakuan->b_time=$renew_info->payment_time;
                    $dakuan->ftype=1;
                    $dakuan->note='备款预付生成的打款-关联备款id'.$renew_info->id;
                    $dakuan->type=1;
                    $dakuan->ctime=time();
                    $dakuan->submituser=$renew_info->submituser;

                    if($dakuan->save())
                    {
                        $model_renew_guanlian=new MrenewRelevance();
                        $model_renew_guanlian->type=2;
                        $model_renew_guanlian->dk_id=$dakuan->id;
                        $model_renew_guanlian->xf_id=$renew_info->id;
                        $model_renew_guanlian->fk_money=$renew_info->money;
                        $model_renew_guanlian->save();
                    }

                    /*添加父子流程对应关系*/
                    $fzflow_dy=new FlowGl();
                    $fzflow_dy->fuid=$renew_info->id;
                    $fzflow_dy->ziid=$dakuan->id;
                    $fzflow_dy->ctime=time();
                    $fzflow_dy->type=$val['id'];
                    $fzflow_dy->last_time=date('Y-m-d H:i:s');

                    if(!$fzflow_dy->save())
                    {
                        var_dump($fzflow_dy->errors);exit;
                    }



                    /*
                    //查询打款需要几级审核
                    $dakuan_shenhe=AuditAction::find()->where(['action_name'=>'meijie-dakuan'])->one();
                    for($i=1;$i<=$dakuan_shenhe['audit_count'];$i++)
                    {
                        Audit::audit_auto($dakuan[id],'beikuan',$i,'jd_mback_money');
                        MbackMoney::dakuan_shenhe($dakuan[id],$i,'1');
                    }
                    */

                }


            }



        }



        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {

            //媒介公司 和 合同备款余额加
            JdCustomer::updateAllCounters(['beikuan_yue'=>$renew_info->money],['id'=>$renew_info->advertiser]);
            JdContract::updateAllCounters(['beikuan_yue'=>$renew_info->money],['id'=>$renew_info->xf_contractid]);

            //备款账户余额加
            BeikuanAccount::updateAllCounters(['yu_e'=>$renew_info->money],['id'=>$renew_info->account]);




            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;


            if(!$renew_info->save())
            {
                var_dump($renew_info->getErrors());
            }
        }

    }


    //退款审核
    public static function bukuan_shenhe($id,$audit_type){

        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $renew_info=self::findOne($id);//退款详情
        if(empty($audit))
        {
            $audit=1;
        }

        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie-bukuan'])->one();


        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {


            //如果选择了备款账户 就生成一笔备款账户充值 垫付
            if($renew_info->account!='')
            {
                $model = new Beikuan();
                //接收数据并处理
                /*
                account: 241677
                advertiser: 4576
                dl_fandian: "0"
                money: "1.00"
                note: "ლ(′◉❥◉｀ლ)"
                payment_time: "2019-03-07"
                payment_type: 2
                product_line: 1
                rebates_proportion: "0"
                show_money: "1"
                submituser: 16
                xf_contractid: 13327
                */
                $model->account=$renew_info->account;
                $model->advertiser=$renew_info->advertiser;
                $model->money=$renew_info->money;
                $model->payment_time =$renew_info->payment_time;
                $model->ctime = time();
                $model->payment_type=2;//垫付
                $model->product_line=$renew_info->product_line;
                $model->rebates_proportion=$renew_info->rebates_proportion;
                $model->show_money=$renew_info->show_money;
                $model->submituser=$uid=Yii::$app->user->id;
                $model->xf_qiane=$model->money;
                $model->xf_contractid=$renew_info->xf_contractid;
                //$model->xf_qiane=$model->money;
                $model->xf_cost=$model->money;
                $model->mht_id=$model->xf_contractid;
                $model->dakuan_yue=$model->money;
                $model->note='因补款到备款账户系统自动生成的备款！';
                //$model->payment_type=1;
                //$model->no_cost_xf=$model->show_money;

                $mtinfo=JdContract::findOne($model->xf_contractid);

                //跟系统计算账户比的公式进行对比。
                $money_db=round($model->show_money/(1+($model->rebates_proportion/100))*(1-($mtinfo->dl_fandian/100)),2);
                if($money_db>$model->money)
                {
                    if($money_db-$model->money >0.05)
                    {
                        throw new \Exception('错误!前端账户金额跟系统计算不一致！（应为：）'.$money_db);
                    }
                }

                if(!$model->save())
                {
                    var_dump($model->getErrors());
                    exit;
                }

                //备款过审
                //查询备款需要几级审核
                $renew_shenhe=AuditAction::find()->where(['action_name'=>'beikuan'])->one();
                for($i=1;$i<=$renew_shenhe['audit_count'];$i++)
                {
                    Audit::audit_auto($model->id,'beikuan',$i,'beikuan');
                    Beikuan::beikuan_shenhe($model->id,$i,'1');
                    //RenewHuikuan::renew_shenhe($renew[id],$i,'1');
                }

                //生成一笔打款 并且对应了这个退款和备款
                /*
                 *
                 * advertiser: 4584
amount: 25384.62
b_company: 1
b_money: 25128.469999999998
b_time: "2019-03-22"
dakuan_type: 1
dikou_money: 256.15
dk_money: 25384.62
fk_money: ["25384.62"]
ftype: 1
note: "123213"
renew_idin: [140622]
submituser: 16
tk_money: ["256.15"]
tk_type: ["3"]
tukuan_idin: [140634]
                 *
                 * */
                $moni_post=[
                    'advertiser'=>$renew_info->advertiser,
                    'amount'=>$renew_info->xf_cost,
                    'b_company'=>1,
                    'b_money'=>0,
                    'b_time'=>$renew_info->payment_time,
                    'dakuan_type'=>1,
                    'dikoy_money'=>$renew_info->xf_cost,
                    'dk_money'=>0,
                    'fk_money'=>[$renew_info->xf_cost],
                    'ftype'=>1,
                    'renew_idin'=>[$model->id],
                    'submituser'=>Yii::$app->user->id,
                    'tk_money'=>[$renew_info->xf_cost],
                    'tk_type'=>[3],
                    'tukuan_idin'=>[$renew_info->id]
                ];
                $rs=MbackMoney::adddakuan($moni_post);
                //生成打款抵扣关联关系
                $logfile= dirname(__Dir__.'../').'/runtime/logs/hjd.log';
                error_log(date("Y-m-d H:i:s").' 执行2方法开始'.PHP_EOL,3,$logfile);
//                $dk_to_renew=new MrenewRelevance();
//                $dk_to_renew->dk_id=$rs->id;
//                $dk_to_renew->xf_id=$model->id;
//                $dk_to_renew->fk_money=$renew_info->xf_cost;
//                $dk_to_renew->tk_dixiaohou=0;
//                $dk_to_renew->amount=$renew_info->xf_cost;
//                $dk_to_renew->type=2;
//                $dk_to_renew->save();
//
//
//                $dk_to_tk=new DakuanToTuikuan();
//                $dk_to_tk->dk_id=$rs->id;
//                $dk_to_tk->tk_id=$renew_info->id;
//                $dk_to_tk->fk_money=$renew_info->xf_cost;
//                $dk_to_tk->type=3;
//                $dk_to_tk->save();
                //查询打款需要几级审核
                $renew_shenhe1=AuditAction::find()->where(['action_name'=>'meijie-dakuan'])->one();
                for($i=1;$i<=$renew_shenhe1['audit_count'];$i++)
                {
                    Audit::audit_auto($rs->id,'meijie-dakuan',$i,'jd_mback_money');
                    MbackMoney::dakuan_shenhe($rs->id,$i,'1');
                    //RenewHuikuan::renew_shenhe($renew[id],$i,'1');
                }



            }

            //如果没有选择备款账户 则判断是否是第一审 如果是 就生成一笔

            //媒介公司 和 合同备款余额加
            JdCustomer::updateAllCounters(['bukuan'=>$renew_info->money],['id'=>$renew_info->advertiser]);
            JdContract::updateAllCounters(['bukuan'=>$renew_info->money],['id'=>$renew_info->xf_contractid]);

            //备款账户余额加
            //BeikuanAccount::updateAllCounters(['yu_e'=>$renew_info->money],['id'=>$renew_info->account]);



            //修改媒介合同和媒介公司续费值
            //self::money_change($renew_info['advertiser'],$renew_info['xf_contractid'],411,$renew_info['money'],'jian');
            //重跑媒介续费对应
            //--self::mrenew_huikuan($renew_info['xf_contractid']);



            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            //修改备注内容
            if(\Yii::$app->request->post('audit') && $renew_info->account==''){
                $renew_info->note=date('Y-m-d').'补现金'.$renew_info->dakuan_yue.'__'.$renew_info->note;
            }


            $renew_info->save();
        }
    }

    //媒介续费对应打款
    public static function beikuan_renew($mhtid=''){

        //不跑跟备款账户绑定的续费 因为他会跟备款对应 而不是打款
        $l=\backend\models\BeikuanAccountToRenew::find()->select('xf_id')->column();
        $l=implode(',',$l);
        if($mhtid=='')
        {
            $mhtid=null;
        }


        //循环续费欠额大于0的数据 并且对应该续费合同的回款
        $list=MrenewHuikuan::find()
            ->select('id')
            ->where("(payment_type=1 or payment_type=2 or payment_type=17 or payment_type=16) and xf_qiane>0 and audit!=2 and id in ($l) ")
            ->andFilterWhere(['mht_id'=>$mhtid])
            ->orderBy("payment_time asc")
            ->asArray()
            ->all();


        if($list){
            foreach ($list as $key=>$val)
            {
                self::mrenew_auto_huikuan($val['id']);
            }
        }

    }

    public static function mrenew_auto_huikuan($xf_id){
        //续费信息
        $xufei_info=MrenewHuikuan::findOne($xf_id);

        //此合同实时付对应的续费优先
        //此合同备款列表

        /*
         * 查出这笔续费关联的备款/补款 列表
         *
         *这笔续费可能对应多个
         * */
        $beikuan_account_id=BeikuanAccountToRenew::find()->where(['xf_id'=>$xf_id])->all();

        foreach ($beikuan_account_id as $k=>$v)
        {

        }

        $backmoney=self::find()
            ->where("(payment_type in(1,2,3,15))  and mht_id='$xufei_info[mht_id]' and audit!=2  and dakuan_yue >0 and account='$beikuan_account_id->bk_account_id'")
            ->orderBy('payment_time asc')
            ->asArray()
            ->all();

        var_dump($backmoney);exit;

        /*
        if(!$backmoney)
        {
            //此合同回款列表e
            $backmoney=self::find()
                ->where("(payment_type in(1,2,3))  and mht_id='$xufei_info[mht_id]' and audit!=2  and dakuan_yue >0  and shishifu_onid =0")
                ->orderBy("payment_time asc")
                ->all();
        }*/

        foreach ($backmoney as $key=>$value)
        {
            $xufei_info=MrenewHuikuan::findOne($xf_id);
            //如果续费欠额为0则跳出本次循环
            if($xufei_info['xf_qiane']==0)
            {
                break ;
            }
            $yixufeihuikuan_date=new BeikuanRenewDy();
            $yixufeihuikuan_date->mt_fandian=$xufei_info['rebates_proportion'];
            $yixufeihuikuan_date->dl_fandian=0;
            $yixufeihuikuan_date->mht_id=$xufei_info[mht_id];
            $yixufeihuikuan_date->xf_fandian=0;
            $yixufeihuikuan_date->gr_fandian=0;
            $yixufeihuikuan_date->xs_fandian=0;
            $yixufeihuikuan_date->avid=$xufei_info['advertiser'];
            //销售
            $yixufeihuikuan_date->xsid=$xufei_info['market'];
            $yixufeihuikuan_date->xf_id=$xf_id;
            //$yixufeihuikuan_date->xs_tc=0;
            //$yixufeihuikuan_date->ht_id=$xufei_info[xf_contractid];



            //如果打款金额 大于 续费欠额 （余额大于续费金额）停止循环并返回续费欠额为0
            if($value['dakuan_yue']-$xufei_info['xf_qiane']>0) {
                //设置打款余额=打款金额-续费欠额
                self::updateAll(['dakuan_yue'=>$value['dakuan_yue']-$xufei_info['xf_qiane']],['id'=>$value['id']]);
                //增加已回款续费记录
                $yixufeihuikuan_date->hk_id = $value['id'];
                //谁大取谁
                if ($value['payment_time'] >= $xufei_info['payment_time']) {
                    $yixufeihuikuan_date->time = $value['payment_time'];
                } else
                {
                    $yixufeihuikuan_date->time = $xufei_info['payment_time'];
                }
                $yixufeihuikuan_date->money=$xufei_info['xf_qiane'];
                $yixufeihuikuan_date->shifu_money=$xufei_info['xf_qiane'];
                $yixufeihuikuan_date->save();
                //修改续费欠额
                MrenewHuikuan::updateAll(['xf_qiane'=>0],['id'=>$xf_id]);
                return ;
            }else{
                //如果回款余额 小于 续费金额  （此回款余额不足以抹平此续费）则继续执行循环 并把此回款的余额为0
                //增加已回款续费记录
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
                //设置回款余额
                self::updateAll(['dakuan_yue'=>0],['id'=>$value[id]]);
                //修改续费欠额
                MrenewHuikuan::updateAll(['xf_qiane'=>$xufei_info['xf_qiane']-$value['dakuan_yue']],['id'=>$xf_id]);
            }
        }
    }

    public static function add_refund_money(){

        /*
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
        */





        $model = new Beikuan();
        //接收数据并处理
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');


        $model->payment_time = time();
        $model->ctime = time();
        $model->xf_qiane=0;
        //$model->xf_qiane=$model->money;
        $model->xf_cost=$model->money;
        $model->dakuan_yue=$model->money;
        $model->payment_type=155;

        //$model->no_cost_xf=$model->show_money;

        $mtinfo=JdContract::findOne($model->xf_contractid);
        $model->mht_id=$model->xf_contractid;

        //跟系统计算账户比的公式进行对比。
        $money_db=round($model->show_money/(1+($model->rebates_proportion/100))*(1-($mtinfo->dl_fandian/100)),2);
        if($money_db>$model->money)
        {
            if($money_db-$model->money >0.05)
            {
                throw new \Exception('错误!前端账户金额跟系统计算不一致！（应为：）'.$money_db);
            }
        }

        //查询所属合同状态
        //$contractinfo = JdContract::contractinfo($model->xf_contractid);



        if($model->save())
        {

            //写入金额变动
            //self::money_change($model->advertiser,$model->xf_contractid,$model->payment_type,$model->money);


            return $model;
        }else
        {
            var_dump($model->errors);exit;
            return $model->errors;
        }



    }


    //媒介退打款
    public static function beikuan_tui_list($shenhewhere2='',$role=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.market'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','beikuan.id',0];
        }

        //筛选条件
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','beikuan.appname' ,$Search_str],['like','subt.name',$Search_str],['like','ac.a_users',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");

        if($start and $end)
        {
            //$search_data=['and',['>=','beikuan.payment_time',$start],['<','beikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'beikuan.payment_time', $start], ['<', 'beikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'beikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'beikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','beikuan.payment_time',1]];
        }


        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="beikuan.audit=1 and beikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="beikuan.audit=0 or beikuan.audit=1 and beikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="beikuan.audit=2";
        }else
        {
            $shenhewhere="beikuan.id>0";
        }

        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");




        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,beikuan.*,ac.a_users,subt.name as submitname,prlin.name as prlina,ac.a_users,ht.title')
            ->where("beikuan.payment_type in(155)")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['is_accomplish'=>$is_accomplish])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','advertiser0 ad','account0 ac'])
            ->leftJoin('jd_contract ht','ht.id=beikuan.mht_id')
            ->leftJoin("jd_product_line prlin","prlin.id=ht.product_line")

            ->asArray();


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



    //退款审核
    public static function beikuan_tui_shenhe($id,$audit_type,$state=''){
        if($state==''){
            $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回

        }else
        {
            $audit=$state;
        }

        $renew_info=self::findOne($id);//退款详情

        //查询退款需要几级审核hhhhhh
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'beikuan_account_tuikuan'])->one();



        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {
            MrenewHuikuan::money_change($renew_info['advertiser'],$renew_info['mht_id'],1,$renew_info['xf_cost'],'jian');
            //媒介公司 和 合同备款余额减
            JdCustomer::updateAllCounters(['beikuan_yue'=>-$renew_info->dakuan_yue],['id'=>$renew_info->advertiser]);
            JdContract::updateAllCounters(['beikuan_yue'=>-$renew_info->dakuan_yue],['id'=>$renew_info->xf_contractid]);

            //备款账户余额减
            BeikuanAccount::updateAllCounters(['yu_e'=>-$renew_info->dakuan_yue],['id'=>$renew_info->account]);

            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            //修改备注内容
            if(\Yii::$app->request->post('audit')){
                $renew_info->note=date('Y-m-d').'退现金'.$renew_info->dakuan_yue.'__'.$renew_info->note;
            }

            $renew_info->save();
        }
    }




}
