<?php

namespace backend\models;

use backend\modules\api\controllers\BeikuanController;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "jd_mback_money".
 *
 * @property integer $id
 * @property integer $b_company
 * @property integer $advertiser
 * @property string $b_money
 * @property integer $b_time
 * @property string $note
 * @property integer $belongs
 * @property integer $ctime
 * @property integer $submituser
 * @property string $appname
 * @property integer $audit_1
 * @property integer $audit_2
 * @property integer $susers1
 * @property integer $susers2
 * @property string $huikuanren
 * @property integer $type
 * @property integer $ftype
 * @property string $zhanghu
 * @property string $shoukuanren
 * @property string $kaihuhang
 * @property integer $pici
 */
class MbackMoney extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_mback_money';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['b_company', 'advertiser', 'b_money', 'b_time', 'ctime', 'submituser'], 'required'],
            [['b_company', 'advertiser', 'b_time', 'belongs', 'ctime', 'submituser', 'audit', 'audit_count', 'audit_u', 'type', 'ftype', 'pici'], 'integer'],
            [['b_money','y_money'], 'number'],
            [['note'], 'string'],
            [['appname'], 'string', 'max' => 100],
            [['huikuanren', 'zhanghu', 'shoukuanren', 'kaihuhang'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'b_company' => 'B Company',
            'advertiser' => 'Advertiser',
            'b_money' => 'B Money',
            'b_time' => 'B Time',
            'note' => 'Note',
            'belongs' => 'Belongs',
            'ctime' => 'Ctime',
            'submituser' => 'Submituser',
            'appname' => 'Appname',
            'huikuanren' => 'Huikuanren',
            'type' => 'Type',
            'ftype' => 'Ftype',
            'zhanghu' => 'Zhanghu',
            'shoukuanren' => 'Shoukuanren',
            'kaihuhang' => 'Kaihuhang',
            'pici' => 'Pici',
        ];
    }

    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }


    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'advertiser'])->select('id,advertiser,customer_type');
    }


    //关联审核人
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }

    //关联打款主题
    public function getBcompany(){
        return $this->hasOne(AgentCompany::className(),['id'=>'b_company'])->select('id,companyname');
    }

    public static function dakuan_list($shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['jd_mback_money.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['jd_mback_money.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_mback_money.id',0];
        }

        //筛选条件
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));

        if($start and $end)
        {
            //$search_data=['and',['>=','jd_mback_money.b_time',$start],['<=','jd_mback_money.b_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_mback_money.b_time', $start], ['<=', 'jd_mback_money.b_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_mback_money.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_mback_money.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_mback_money.b_time',1]];
        }


        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_mback_money.audit=1 and is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_mback_money.audit=0 or jd_mback_money.audit=1 and is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_mback_money.audit=2";
        }else
        {
            $shenhewhere="jd_mback_money.id>0";
        }

        $shenhewhere.=$shenhewhere2;


        //打款类型
        $dktype=Yii::$app->request->post("dktype");
        if($dktype==3)
        {
            $type=3;
            unset($dktype);
        }
        //已打款条件


        $dstart=strtotime(Yii::$app->request->post('dtime_start_date'));
        $dend=strtotime(Yii::$app->request->post('dtime_end_date'));

        if($dstart and $dend)
        {
            $search_data2=['and',['>=','jd_mback_money.pici',$dstart],['<=','jd_mback_money.pici',$dend]];
        }else
        {
            $search_data2=['and',['>','jd_mback_money.b_time',1]];
        }


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_ultimate_shenhe");



        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_mback_money.*,subt.name as submitname')
            ->where("jd_mback_money.type in(1,3)")
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['jd_mback_money.is_ultimate_shenhe'=>$is_accomplish])
            ->andFilterWhere(['ftype'=>$dktype])
            ->andFilterWhere(['jd_mback_money.type'=>$type])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andWhere($search_data2)
            ->joinWith(['submituser0 subt','advertiser0 ad'])->asArray();
        $dakuan_sum=$query->sum('jd_mback_money.b_money');
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
        $ar['dakuan_sum']=$dakuan_sum;


        return $ar;

    }


    public static function adddakuan($shengcheng=''){
        $model=new MbackMoney();
        $postdata=\Yii::$app->request->post();

        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
        if($shengcheng!='')
        {
            $postdata=$shengcheng;
            $model->load($postdata,'');
        }else{
            $model->b_time=strtotime($postdata['b_time']);
        }

        $model->type=1;
        $model->ctime=time();
        $model->y_money=$postdata['amount'];

        if($postdata['amount']!=array_sum($postdata['fk_money'])){
            throw new Exception('打款总额错误.'.$postdata['amount'].'====>'.array_sum($postdata['fk_money']));
        }

        if($model->save())
        {
            //如果是续费
            if($postdata['dakuan_type']==1){
                $tkdikou=$postdata['tk_money'];
                //如果勾选了退款抵扣
                if(count($postdata['tukuan_idin'])>0)
                {
                    //执行打款对应退款入库操作并且把退款减去相应的余额
                    foreach ($postdata['tukuan_idin'] as $key=>$val)
                    {
                        $dakuan_to_tuikuan_dk=new DakuanToTuikuan();
                        $dakuan_to_tuikuan_dk->dk_id=$model->id;
                        $dakuan_to_tuikuan_dk->tk_id=$val;
                        $dakuan_to_tuikuan_dk->fk_money=$postdata['tk_money'][$key];
                        $dakuan_to_tuikuan_dk->type=$postdata['tk_type'][$key];
                        $dakuan_to_tuikuan_dk->save();

                        //如果是退款或者退保证金就减Mrenew_huikuan表的dakuan_yue字段
                        if($postdata['tk_type'][$key]=='1' or $postdata['tk_type'][$key]=='2'){
                            MrenewHuikuan::updateAllCounters(['dakuan_yue'=>-$postdata['tk_money'][$key]],['id'=>$val]);
                        }
                        if($postdata['tk_type'][$key]=='3' or $postdata['tk_type'][$key]=='4'){
                            Beikuan::updateAllCounters(['dakuan_yue'=>-$postdata['tk_money'][$key]],['id'=>$val]);
                        }
                    }


                    /*
                     * 抵消的续费 减去续费欠额
                     *
                     * */
                    //退款总额
                    $tksum=array_sum($postdata['tk_money']);

                    //循环打款条数
                    foreach ($postdata['fk_money'] as $key=>$val)
                    {
                        //退款总额-=付款
                        $tksum-=$val;
                        /*
                         * 如果剩余退款大于此条打款 则退款抵消后此条打款应付金额为0
                         * 如果剩余退款小于此条打款 则题库抵消后此条打款应付金额为退款-打款 的负数值 的绝对值 （负负得正）
                        */
                        if($tksum>=0)
                        {
                            $tkdikou[$key]=0;
                        }
                        elseif($tksum < 0)
                        {
                            $yuan=-$tksum;
                            $tkdikou[$key]=$yuan;
                            break;
                        }
                    }




                }








                foreach ($postdata['renew_idin'] as $key=>$val)
                {
                    $model_renew_guanlian=new MrenewRelevance();
                    //判断是普通续费 还是备款
                    $pt=MrenewHuikuan::find()->where(['id'=>$val])->count('id');

                    $model_renew_guanlian->type=1;
                    //如果对应的金额大于0才操作入库  用于有退款抵扣的情况
                    if($postdata['fk_money'][$key]>0){
                        if($pt==0)
                        {

                            $bk=Beikuan::find()->count('id');

                            if($bk==0)
                            {
                                throw new \Exception('找不到该续费或者备款 ID：'.$val);
                            }else{
                                Beikuan::updateAllCounters(['xf_qiane'=>-$postdata['fk_money'][$key]],['id'=>$val]);
                                $model_renew_guanlian->type=2;
                            }
                        }else
                        {
                            MrenewHuikuan::updateAllCounters(['xf_qiane'=>-$postdata['fk_money'][$key]],['id'=>$val]);
                        }



                        $model_renew_guanlian->dk_id=$model->id;
                        $model_renew_guanlian->xf_id=$val;
                        $model_renew_guanlian->fk_money=$postdata['fk_money'][$key];
                        $model_renew_guanlian->tk_dixiaohou=isset($tkdikou[$key])?$tkdikou[$key]:$postdata['fk_money'][$key];
                        $model_renew_guanlian->amount=$postdata['amount'];

                        $model_renew_guanlian->save();
                    }
                }

            }

            //如果是备款
            /*
            elseif($postdata['dakuan_type']==2){
                foreach ($postdata['renew_idin'] as $key=>$val)
                {
                    $model_renew_guanlian=new MrenewRelevance();
                    $model_renew_guanlian->dk_id=$model->id;
                    $model_renew_guanlian->xf_id=$val;
                    $model_renew_guanlian->type=2;
                    $model_renew_guanlian->fk_money=$model->b_money;
                    $model_renew_guanlian->save();
                }
            }
             */

            /*
            if($postdata['ftype']==1)
            {
            }*/

            return $model;
        }else
        {
           var_dump($model->errors);exit;
        }

    }

    public static function dakuaninfo($id){
        //具体数据
       $data['data']=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname','bcompany'])->asArray()->one();

       //$data_renew=MrenewRelevance::find()->select('xf_id')->where(['dk_id'=>$id])->column();

       $a=MrenewRelevance::find()
           ->select('a.id,a.appname,a.money,a.account,a.rebates_proportion,a.show_money,a.xf_cost,a.payment_time,b.a_users,c.dl_fandian,a.xf_qiane,mrel.fk_money')
           ->join('mrel right join','jd_mrenew_huikuan a','mrel.xf_id=a.id')
           ->leftJoin('jd_account b',' a.account=b.id')
           ->leftJoin( 'jd_contract c','a.mht_id=c.id')
           ->where(['mrel.dk_id'=>$id])
           //->where(['a.id'=>$data_renew])
           ->asArray()
           ->all();

       $b=MrenewRelevance::find()
           ->select('a.id,a.appname,a.money,a.account,a.rebates_proportion,a.show_money,a.xf_cost,a.payment_time,b.a_users,c.dl_fandian,a.xf_qiane,mrel.fk_money')
           ->join('mrel right join','beikuan a','mrel.xf_id=a.id')
           ->leftjoin('beikuan_account b',' a.account=b.id')
           ->leftJoin( 'jd_contract c','a.mht_id=c.id')
           ->where(['mrel.dk_id'=>$id])
           //->where(['a.id'=>$data_renew])
           ->asArray()
           ->all();

        $data['renew']=array_merge($a,$b);

        //包含的退款信息
        //$tk_idin=DakuanToTuikuan::find()->select('tk_id')->where(['dk_id'=>$id])->column();

        $dikoukuan1=DakuanToTuikuan::find()
            ->select('a.id,a.appname,a.money,a.account,a.rebates_proportion,a.show_money,a.xf_cost,a.payment_time,b.a_users,c.dl_fandian,a.dakuan_yue,mrel.fk_money,mrel.type')
            ->join('mrel right join','jd_mrenew_huikuan a','mrel.tk_id=a.id')
            ->leftJoin('jd_account b',' a.account=b.id')
            ->leftJoin( 'jd_contract c','a.mht_id=c.id')
            ->where(['mrel.dk_id'=>$id])
            ->asArray()
            ->all();

        $dikoukuan2=DakuanToTuikuan::find()
            ->select('a.id,a.appname,a.money,a.account,a.rebates_proportion,a.show_money,a.xf_cost,a.payment_time,b.a_users,c.dl_fandian,a.dakuan_yue,mrel.fk_money,mrel.type')
            ->join('mrel right join','beikuan a','mrel.tk_id=a.id')
            ->leftJoin('beikuan_account b',' a.account=b.id')
            ->leftJoin( 'jd_contract c','a.mht_id=c.id')
            ->where(['mrel.dk_id'=>$id])
            ->asArray()
            ->all();

        $data['tuikuan']=ArrayHelper::merge($dikoukuan1,$dikoukuan2);

        $data['code']="200";
        return $data;
    }

    public static function margin_dakuaninfo($id){
        //具体数据
        $data['data']=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname','bcompany'])->asArray()->one();

        $data_renew=MrenewRelevance::find()->select('xf_id')->where(['dk_id'=>$id])->column();

        $data['renew']=MrenewHuikuan::find()->select('a.id,a.appname,a.money,a.account,a.rebates_proportion,a.show_money,a.xf_cost,a.payment_time,b.advertiser,c.title,a.ctime')->join('a left join','jd_customer b',' a.advertiser=b.id')->leftJoin( 'jd_contract c','a.mht_id=c.id')->where(['a.id'=>$data_renew])->asArray()->all();
        $data['code']="200";
        return $data;
    }

    //打款审核
    public static function dakuan_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $renew_info=self::findOne($id);//退款详情
        if(empty($audit))
        {
            $audit=1;
        }


        if($renew_info->type=='3')
        {
            return self::margin_shenhe($id,$audit_type);
            exit;
        }
        //驳回
        if($audit=='2')
        {
            $renew_array=MrenewRelevance::find()->where("dk_id=$renew_info[id]")->all();
            foreach ($renew_array as $key=>$value)
            {
                if($value['type']==1) {
                    MrenewHuikuan::updateAllCounters(['xf_qiane' => $value['fk_money']], ['id' => $value['xf_id']]);
                }
                if($value['type']==2) {
                    Beikuan::updateAllCounters(['xf_qiane' => $value['fk_money']], ['id' => $value['xf_id']]);
                }
            }


            //如果有退款抵扣则把退款抵扣 包括退款余额
            $tuik_array=DakuanToTuikuan::find()->where(['dk_id'=>$renew_info['id']])->all();
            foreach ($tuik_array as $key=>$value)
            {

                //MrenewHuikuan::updateAllCounters(['dakuan_yue' => $value['fk_money']], ['id' => $value['tk_id']]);
                //续费
                if($value['type']==1 or $value['type']==2) {
                    MrenewHuikuan::updateAllCounters(['dakuan_yue' => $value['fk_money']], ['id' => $value['tk_id']]);
                }
                else if($value['type']==3 or $value['type']==4){
                    Beikuan::updateAllCounters(['dakuan_yue' => $value['fk_money']], ['id' => $value['tk_id']]);
                }

            }

            return ;

        }

        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie-dakuan'])->one();


        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {
            //var_dump($renew_info->);exit;
            //增加公司打款 和 未分配打款
            MrenewHuikuan::money_change($renew_info['advertiser'],'',4,$renew_info['b_money']);
            $renew_array=MrenewRelevance::find()->where("dk_id=$renew_info[id]")->asArray()->all();

            $mht_dakuan_rw_jisuan=[];
            $mht_dakuan_dikou_jisuan=[];
            $mht_id=[];

            foreach ($renew_array as $key=>$value)
            {
                //续费
                if($value['type']==1) {
                    $renone=MrenewHuikuan::findOne($value['xf_id']);
                }
                else if($value['type']==2){
                    $renone=Beikuan::findOne($value['xf_id']);
                }
                //续费
                $mht_dakuan_rw_jisuan[$renone['mht_id']]+=$value['fk_money'];
                $mht_id[]=$renone['mht_id'];
            }

            $dikoukuan=DakuanToTuikuan::find()->where("dk_id=$renew_info[id]")->asArray()->all();
           // var_dump($mht_dakuan_rw_jisuan);exit;
//            var_dump($renew_array);
//            var_dump($dikoukuan);
//            exit;
            foreach ($dikoukuan as $key=>$value)
            {
                //续费
                if($value['type']==1 or $value['type']==2) {
                    $dikou_one=MrenewHuikuan::findOne($value['tk_id']);
                    if($dikou_one['dakuan_yue']==0)
                    {
                        if($dikou_one['payment_type']=='15')
                        {
                            //退款退现金
                            self::auto_shenhe('tk',$dikou_one['audit_count'],$dikou_one['id'],$value['fk_money']);
                        }elseif($dikou_one['payment_type']=='42')
                        {
                            //退保证金
                            self::auto_shenhe('tbzj',$dikou_one['audit_count'],$dikou_one['id'],$value['fk_money']);
                        }
                    }else
                    {
                        MrenewHuikuan::updateAll(['note'=>'抵扣打款金额'.$value['fk_money'].'__'.$dikou_one->note],['id'=>$value['tk_id']]);
                    }
                }
                else if($value['type']==3 or $value['type']==4){
                    $dikou_one=Beikuan::findOne($value['tk_id']);
                    if($dikou_one['dakuan_yue']==0)
                    {
                        if($dikou_one['payment_type']=='3')
                        {
                            //补款
                            self::auto_shenhe('bk',$dikou_one['audit_count'],$dikou_one['id'],$value['fk_money']);
                        }elseif($dikou_one['payment_type']=='155')
                        {
                            //备款账户退款
                            self::auto_shenhe('b_account_tk',$dikou_one['audit_count'],$dikou_one['id'],$value['fk_money']);
                        }
                    }else
                    {
                        Beikuan::updateAll(['note'=>'抵扣打款金额'.$value['fk_money'].'__'.$dikou_one->note],['id'=>$value['tk_id']]);
                    }
                }
                //续费
                $mht_dakuan_dikou_jisuan[$dikou_one['mht_id']]+=$value['fk_money'];
                $mht_id[]=$dikou_one['mht_id'];
            }
            $mht_id=array_unique($mht_id);
//            var_dump($mht_dakuan_rw_jisuan);
//            var_dump($mht_dakuan_dikou_jisuan);

            foreach ($mht_id as $key=>$val)
            {

                $xf=$mht_dakuan_rw_jisuan[$val];

                $tk=$mht_dakuan_dikou_jisuan[$val];
                //生成分配打款
                $money=$xf-$tk;
               // echo $money;exit;
                if($money>0)
                {
                    $model=new MrenewHuikuan();
                    $model->money=$money;
                    $model->xf_contractid=$val;
                    $model->mht_id=$val;
                    //$model->shishifu_onid=$renone['id'];
                    //$model->market=$renone['market'];
                    $model->advertiser=$renew_info['advertiser'];
                    //$model->product_line=$renone['product_line'];
                    $model->note='打款-分配款';
                    $model->dakuan_yue=0;
                    //$model->back_type=0;
                    $model->type=1;

                    $model->payment_type=5;
                    $model->payment_time=time();
                    $model->submituser=Yii::$app->user->getId();
                    $model->is_huikuan=0;
                    $model->xf_qiane=0;
                    $model->audit=1;
                    $model->audit_count=2;
                    $model->audit_u=Yii::$app->user->getId();
                    $model->ctime=time();
                    $model->xf_qiane=0;
                    $model->backmoney_yue=0;
                    $model->is_ultimate_shenhe=1;
                    $model->save();

                   // 分配公司打款 合同打款
                    JdContract::updateAllCounters(['dakuan' => $money], ['id' =>$val]);
                    //减去客户未分配打款
                    JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e' => -$money], ['id' => $renew_info['advertiser']]);
                    //平掉续费前额
                    //MrenewHuikuan::updateAll(['xf_qiane' => 0], ['id' => $renone['id']]);

                }elseif ($money<0)
                {

                    $money=$money*-1;
                    $model=new MrenewHuikuan();
                    $model->advertiser=$renew_info['advertiser'];
                    //$model->product_line=$info['product_line'];
                    $model->type=1;
                    $model->money=$money;
                    $model->payment_type=16;
                    $model->payment_time=time();
                    $model->submituser=\Yii::$app->user->getId();
                    $model->xf_contractid=$val;
                    $model->is_huikuan=0;
                    //$model->market=$info['submituser'];
                    //$model->xf_qiane=0;
                    $model->audit=1;
                    $model->audit_count=2;
                    $model->audit_u=\Yii::$app->user->getId();
                    $model->is_ultimate_shenhe=1;
                    $model->ctime=time();
                    $model->xf_qiane=0;
                    $model->mht_id=$val;
                    $model->note='打款-转出款';
                    $model->save();
                    //合同回款减少
                    JdContract::updateAllCounters(['dakuan'=>-$money],['id'=>$val]);
                    //公司未分配余额增加
                    JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e'=>$money],['id'=>$renew_info['advertiser']]);

                }
            }


//            foreach ($renew_array as $key=>$value)
//            {
//
//                if($value['tk_dixiaohou']<=0)
//                {
//                    continue;
//                }
//                $model=new MrenewHuikuan();
//                $renone=MrenewHuikuan::findOne($value['xf_id']);
//                //自动添加合同打款
//
//                /*
//                 *2018年4月27日15:38:13
//                 *今日新增需求：不再以续费id作为唯一判断分配款标准， 变更为 续费&备款 如果选择备款则xf_id 的值是要分配款合同id 并加入了分配款项
//                 * type 1为续费 2为备款
//                 */
//
//                if($value['type']==1)
//                {
//                    $model->money=$value['tk_dixiaohou'];
//                    $model->xf_contractid=$renone['xf_contractid'];
//                    $model->mht_id=$renone['mht_id'];
//                    $model->shishifu_onid=$renone['id'];
//                    $model->market=$renone['market'];
//                    $model->advertiser=$renone['advertiser'];
//                    $model->product_line=$renone['product_line'];
//                    $model->note='打款对应续费生成的分配款 CRM ';
//                    $model->dakuan_yue=0;
//                    $model->back_type=0;
//
//                }
//
//                if($value['type']==2)
//                {
//                    $renone=Beikuan::findOne($value['xf_id']);
//
//                    $model->money=$value['tk_dixiaohou'];
//                    $model->xf_contractid=$renone['xf_contractid'];
//                    $model->mht_id=$renone['mht_id'];
//                    $model->shishifu_onid=$renone['id'];
//                    //$model->market=$renone['market'];
//                    $model->advertiser=$renone['advertiser'];
//                    $model->product_line=$renone['product_line'];
//                    $model->note='打款对应备款生成的分配款 CRM ';
//                    $model->dakuan_yue=0;
//                    $model->back_type=1;
//                    /*
//                    //读取媒介合同信息
//                    $mht=JdContract::findOne($value['xf_id']);
//
//                    $model->money=$value['fk_money'];
//                    $model->xf_contractid=$value['xf_id'];
//                    $model->mht_id=$value['xf_id'];
//                    //$model->shishifu_onid=$renone['id'];
//                    $model->market=$mht['market'];
//                    $model->advertiser=$mht['advertiser'];
//                    $model->product_line=$mht['product_line'];
//                    $model->dakuan_yue=$value['fk_money'];
//                    */
//                }
//
//
//
//                $model->type=1;
//
//                $model->payment_type=5;
//                $model->payment_time=time();
//                $model->submituser=Yii::$app->user->getId();
//                $model->is_huikuan=0;
//                $model->xf_qiane=0;
//                $model->audit=1;
//                $model->audit_count=2;
//                $model->audit_u=Yii::$app->user->getId();
//                $model->ctime=time();
//                $model->xf_qiane=0;
//                $model->backmoney_yue=0;
//
//
//
//                $model->is_ultimate_shenhe=1;
//                //如果添加打款成功
//                if(!$model->save()){
//                    //  var_dump($value['xf_id']);
//                    var_dump($model->errors);exit;
//                    throw new NotFoundHttpException('生成打款失败');
//                }
//
//                if($value['type']==1) {
//                   var_dump($renone['xf_qiane']);exit;
//                    //分配公司打款 合同打款
//                    JdContract::updateAllCounters(['dakuan' => $renone['xf_qiane']], ['id' => $renone['mht_id']]);
//                    //减去客户未分配打款
//                    JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e' => -$renone['xf_qiane']], ['id' => $renew_info['advertiser']]);
//                    //平掉续费前额
//                    //MrenewHuikuan::updateAll(['xf_qiane' => 0], ['id' => $renone['id']]);
//                    //添加已打款的续费
//                    $yixufeihuikuan_date = new Myihuikuanxufei();
//                    $yixufeihuikuan_date->mt_fandian = $renone['rebates_proportion'];
//                    $yixufeihuikuan_date->dl_fandian = 0;
//                    $yixufeihuikuan_date->gr_fandian = 0;
//                    $yixufeihuikuan_date->xs_fandian = 0;
//                    $yixufeihuikuan_date->avid = $renone['advertiser'];
//                    $yixufeihuikuan_date->xsid = $renone['market'];
//                    $yixufeihuikuan_date->hk_id = $model->id;
//                    //$yixufeihuikuan_date->xs_tc=0;
//                    $yixufeihuikuan_date->mht_id = $renone['mht_id'];
//                    $yixufeihuikuan_date->xf_fandian = $renone['rebates_proportion'];
//                    $yixufeihuikuan_date->xf_id = $renone['id'];
//                    $yixufeihuikuan_date->time = $renone['payment_time'];
//                    $yixufeihuikuan_date->time = time();
//                    $yixufeihuikuan_date->money = $value['tk_dixiaohou'];
//                    $yixufeihuikuan_date->shifu_money = $value['tk_dixiaohou'];
//                    $yixufeihuikuan_date->save();
//                }
//
//                if($value['type']==2) {
//
//                    //分配公司打款 合同打款
//                    JdContract::updateAllCounters(['dakuan' => $renone['xf_qiane']], ['id' => $renone['mht_id']]);
//                    //减去客户未分配打款
//                    JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e' => -$renone['xf_qiane']], ['id' => $renew_info['advertiser']]);
//                    //平掉续费前额
//                    //Beikuan::updateAll(['xf_qiane' => 0], ['id' => $renone['id']]);
//                    //添加已打款的续费
//                    $yixufeihuikuan_date = new Myihuikuanxufei();
//                    $yixufeihuikuan_date->mt_fandian = $renone['rebates_proportion'];
//                    $yixufeihuikuan_date->dl_fandian = 0;
//                    $yixufeihuikuan_date->gr_fandian = 0;
//                    $yixufeihuikuan_date->xs_fandian = 0;
//                    $yixufeihuikuan_date->avid = $renone['advertiser'];
//                    //$yixufeihuikuan_date->xsid = $renone['market'];
//                    $yixufeihuikuan_date->hk_id = $model->id;
//                    //$yixufeihuikuan_date->xs_tc=0;
//                    $yixufeihuikuan_date->mht_id = $renone['mht_id'];
//                    $yixufeihuikuan_date->xf_fandian = $renone['rebates_proportion'];
//                    $yixufeihuikuan_date->xf_id = $renone['id'];
//                    $yixufeihuikuan_date->time = $renone['payment_time'];
//                    $yixufeihuikuan_date->time = time();
//                    $yixufeihuikuan_date->money = $value['tk_dixiaohou'];
//                    $yixufeihuikuan_date->shifu_money = $value['tk_dixiaohou'];
//                    $yixufeihuikuan_date->save();
//
//
//
//                    /*
//                    //分配公司打款 合同打款
//                    JdContract::updateAllCounters(['dakuan' => $value['fk_money']], ['id' => $value['xf_id']]);
//                    //减去客户未分配打款
//                    JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e' => -$value['fk_money']], ['id' => $mht['advertiser']]);
//                    */
//                }
//
//            }


            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            //修改打款时间
            $renew_info->pici=time();
            $renew_info->save();




        }

        //如果是最终审核
        if($audit_type==$renew_shenhe['audit_count'] || $audit=='2'){
            //查询打款关联的父子关联  查备款的父级操作
            $fzflow=Flow::find()->where(['zi_flow'=>'dakuan','is_true'=>1])->asArray()->all();
            foreach ($fzflow as $key=>$val)
            {
                if($val['fu_flow']=='beikuan')
                {

                    //如果父流程等于备款 则 走备款的审核流程  子最终审核 为父的审核
                    //查询关联对应父id
                    $fzdy=FlowGl::find()->where(['type'=>$val['id'],'ziid'=>$renew_info['id']])->one();

                    // BeikuanController::
                    if($fzdy){
                            Audit::audit_auto($fzdy['fuid'],'beikuan',$val['fu_audit'],'beikuan',$audit);
                            Beikuan::beikuan_shenhe($fzdy['fuid'],$val['fu_audit'],$audit);
                    }
                }
            }



        }
    }


    public static function auto_shenhe($type,$audit_status,$id,$dx_money){
        $audit_status+=1;
        $note_msg=date('Y-m-d').'抵扣款金额'.$dx_money.'__';
        switch ($type){
            case 'tk':
                //查询退款需要几级审核
                $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie_tuikuan'])->one();
                for($i=$audit_status;$i<=$renew_shenhe['audit_count'];$i++)
                {
                    if($i!=$renew_shenhe['audit_count'])
                    {
                        $note_msg='';
                    }
                    Audit::audit_auto($id,'meijie_tuikuan',$i,'jd_mrenew_huikuan',1,$note_msg);
                    //审核续费后续
                    MrenewHuikuan::meijie_tuikuanshenhe($id,$i);
                }
                break;
            case 'tbzj':
                //查询退保证金需要几级审核
                $renew_shenhe=AuditAction::find()->where(['action_name'=>'meiti-margin-tuikuan'])->one();
                if($i!=$renew_shenhe['audit_count'])
                {
                    $note_msg='';
                }
                for($i=$audit_status;$i<=$renew_shenhe['audit_count'];$i++)
                {
                    Audit::audit_auto($id,'meiti-margin-tuikuan',$i,'jd_mrenew_huikuan',1,$note_msg);
                    MbackMoney::tui_margin_shenhe($id,$i);
                }
                break;
            case 'bk':
                //查询续费需要几级审核
                $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie-bukuan'])->one();

                for($i=$audit_status;$i<=$renew_shenhe['audit_count'];$i++)
                {
                    Audit::audit_auto($id,'meijie-bukuan',$i,'beikuan',1,$note_msg);
                    Beikuan::bukuan_shenhe($id,$i);
                }
                break;
            case 'b_account_tk':
                //查询续费需要几级审核
                $renew_shenhe=AuditAction::find()->where(['action_name'=>'beikuan_account_tuikuan'])->one();
                for($i=$audit_status;$i<=$renew_shenhe['audit_count'];$i++)
                {
                    Audit::audit_auto($id,'beikuan_account_tuikuan',$i,'beikuan',1,$note_msg);
                    Beikuan::beikuan_tui_shenhe($id,$i);
                }
                break;
        };




    }

    public static function fp_dakuanru(){
        $postdata=Yii::$app->request->post();
        $submitusers=Yii::$app->user->id;
        if($postdata['ht_id'])
        {
            foreach ($postdata['ht_id'] as $key=>$val)
            {
                //如果分配款等于0则跳过本次循环 执行下一次循环
                if ($postdata['pmoney'][$key]==0)
                {
                    continue;
                }
                //合同信息
                $htinfo=JdContract::findOne($postdata['ht_id'][$key]);

                //媒体合同信息
              //  $mhtinfo=JdContract::findOne($htinfo['mht_id']);

                $model=new MrenewHuikuan();
                //判断分配款类型   1 分配到回款  2 分配到保证金 3 分配到客户补款（负数）

                if($postdata['backmoney_type']=='')
                {
                    $postdata['backmoney_type']=1;
                }
                if($postdata['backmoney_type']=='1')
                {
                    $model->payment_type=5;
                    $model->money=$postdata['pmoney'][$key];
                    $model->dakuan_yue=$postdata['pmoney'][$key];
                    $model->note='系统分配打款 由CRM系统生成 ';

                    $model->is_ultimate_shenhe=1;
                    $model->audit=1;
                    $model->audit_count=2;
                    $model->audit_u=$submitusers;
                }
                elseif($postdata['backmoney_type']=='2')
                {
                    $model->payment_type=4;
                    $model->money=$postdata['pmoney'][$key];
                    $model->dakuan_yue=$postdata['pmoney'][$key];
                    $model->note='媒介合同分配保证金 由CRM系统生成 ';
                    $model->is_ultimate_shenhe=0;
                    $model->audit=0;
                    $model->audit_count=1;
                }

                $model->xf_contractid=$htinfo['id'];
                $model->mht_id=$htinfo['id'];
                $model->advertiser=$htinfo['advertiser'];
                $model->payment_time=time();
                $model->ctime=time();
                $model->submituser=$submitusers;
                $model->ht_state=$htinfo['contract_state'];

                if($model->save())
                {
                    //修改媒介合同打款总额
                  //  MrenewHuikuan::money_change($htinfo['advertiser'],$htinfo['id'],'5',$postdata['pmoney'][$key]);
                    if($postdata['backmoney_type']=='1') {
                        //修改媒介合同打款总额
                        MrenewHuikuan::money_change($htinfo['advertiser'], $htinfo['id'], '5', $postdata['pmoney'][$key]);
                    }
                    elseif($postdata['backmoney_type']=='2')
                    {
                        //分配保证金成功后协议保证金加
                        MrenewHuikuan::money_change($htinfo['advertiser'], $htinfo['id'], '5-2', $postdata['pmoney'][$key]);
                    }

                    //减少公司未分配余额
                    JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e'=>-$postdata['pmoney'][$key]],['id'=>$htinfo['advertiser']]);
                    //回款对应

                    //--MrenewHuikuan::mrenew_huikuan($htinfo['id']);

                }
            }


        }

    }



    //媒介回款列表
    public static function back_money_list($shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['jd_mback_money.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['jd_mback_money.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_mback_money.id',0];
        }



        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_mback_money.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));

        if($start and $end)
        {
           // $search_data=['and',['>=','jd_mback_money.b_time',$start],['<=','jd_mback_money.b_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_mback_money.b_time', $start], ['<=', 'jd_mback_money.b_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_mback_money.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_mback_money.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_mback_money.b_time',1]];
        }
        //审核条件
        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_mback_money.audit=1 and jd_mback_money.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_mback_money.audit=0 or jd_mback_money.audit=1 and jd_mback_money.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_mback_money.audit=2";
        }else
        {
            $shenhewhere="jd_mback_money.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");

        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_mback_money.*,subt.name as submitname')->where("jd_mback_money.type=2")->andWhere($shenhewhere)->andFilterWhere($userswhere)->andFilterWhere(['is_accomplish'=>$is_accomplish])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','advertiser0 ad'])->asArray();
        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;
        //总额
        $query_count=self::find()->andFilterWhere($userswhere)->andWhere($shenhewhere)->where("jd_mback_money.type=2")->andFilterWhere(['is_accomplish'=>$is_accomplish])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','advertiser0 ad'])->sum('jd_mback_money.b_money');


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

        //总回款
        $ar['money_sum']=$query_count;

        return $ar;
    }



    //新增公司回款
    public static function add_backmoney(){
        $model=new MbackMoney();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->b_time=strtotime($model->b_time);
        $model->ctime=time();
        $model->type=2;
        if($model->b_money<0)
        {
            return array('code'=>500,'msg'=>'不能输入负数');
        }
        if($model->save())
        {
            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }
    }

    public static function backmonet_info($id){
        return self::find()->where(['jd_mback_money.id'=>$id])->with(['submituser0','audituname','bcompany'])->joinWith(['advertiser0 b'=>function($query){
            $query->select("id,advertiser,customer_type,submituser,business")
                ->with(
                    ['submituser0'=>function($query){
                        $query->select(['id','name']);
                    }])
                ->with(
                    ['business0'=>function($query){
                        $query->select(['id','name']);
                    }]);

        }])->asArray()->one();
    }

    public static function backmoney_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $backmoney_info=self::findOne($id);//回款详情
        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'meijie-huikuan'])->one();
        //如果审核通过 并且是最后一级审核
        if($audit=='1' and $audit_type==$renew_shenhe['audit_count'])
        {
            //修改客户金额 公司回款加 公司未分配余额加
            MrenewHuikuan::money_change($backmoney_info['advertiser'],'','41',$backmoney_info['b_money']);
            //修改为最终审核
            $backmoney_info->is_ultimate_shenhe=1;
            $backmoney_info->save();
        }
    }

    public static function margin_da_list($id='',$shenhewhere2='',$role=''){

            //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

            if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
            {
                $uid=Yii::$app->user->getId();
                $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];
            }elseif (Yii::$app->hjd->list_type($role)==2)
            {
                $uid=Yii::$app->hjd->asbumen();
                $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];

            }elseif (Yii::$app->hjd->list_type($role)==3)
            {
                $userswhere=['>','jd_mrenew_huikuan.id',0];
            }

            //筛选条件
            $Search_type=Yii::$app->request->post('Search_type');
            $Search_str=Yii::$app->request->post('Search_str');

            $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_mrenew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
            //日期筛选条件
            $start=strtotime(Yii::$app->request->post('start_date'));
            $end=strtotime(Yii::$app->request->post('end_date'). " +1 day");

            if($start and $end)
            {
                //$search_data=['and',['>=','jd_mrenew_huikuan.payment_time',$start],['<','jd_mrenew_huikuan.payment_time',$end]];
                if(Yii::$app->request->post('date_type')!='2') {
                    $search_data = ['and', ['>=', 'jd_mrenew_huikuan.payment_time', $start], ['<', 'jd_mrenew_huikuan.payment_time', $end]];
                }else{
                    $search_data = ['and', ['>=', 'jd_mrenew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_mrenew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
                }
            }else
            {
                $search_data=['and',['>','jd_mrenew_huikuan.payment_time',1]];
            }

            //审核条件
            $shenhe=Yii::$app->request->post('shenhe');
            if($shenhe=='1')
            {
                $shenhewhere="jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=1";
            }elseif($shenhe=='0')
            {
                $shenhewhere="jd_mrenew_huikuan.audit=0 or jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=0";
            }elseif($shenhe=='2')
            {
                $shenhewhere="jd_mrenew_huikuan.audit=2";
            }else
            {
                $shenhewhere="jd_mrenew_huikuan.id>0";
            }
            $shenhewhere.=$shenhewhere2;

            //查询自己的组
            $query=self::find()->select('ad.advertiser as adname,jd_mrenew_huikuan.*,subt.name as submitname,markets.name as marketname,prlin.name as prlina')->where("jd_mrenew_huikuan.payment_type in(41)")->andFilterWhere($userswhere)->andWhere($shenhewhere)->andFilterWhere(['jd_contract.advertiser'=>$id])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','market0 markets','advertiser0 ad'])->leftJoin('jd_contract ht','ht.id=jd_mrenew_huikuan.mht_id')->leftJoin("jd_product_line prlin","prlin.id=ht.product_line")->asArray();

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


    public static function margin_list($shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['jd_mback_money.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();


            $userswhere=['or',['ad.submituser'=>$uid],['jd_mback_money.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_mback_money.id',0];
        }

        //筛选条件
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));

        if($start and $end)
        {
            //$search_data=['and',['>=','jd_mback_money.b_time',$start],['<=','jd_mback_money.b_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_mback_money.b_time', $start], ['<=', 'jd_mback_money.b_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_mback_money.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_mback_money.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_mback_money.b_time',1]];
        }


        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_mback_money.audit=1 and is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_mback_money.audit=0 or jd_mback_money.audit=1 and is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_mback_money.audit=2";
        }else
        {
            $shenhewhere="jd_mback_money.id>0";
        }

        $shenhewhere.=$shenhewhere2;


        //打款类型
        $dktype=Yii::$app->request->post("dktype");

        //已打款条件


        $dstart=strtotime(Yii::$app->request->post('dtime_start_date'));
        $dend=strtotime(Yii::$app->request->post('dtime_end_date'));

        if($dstart and $dend)
        {
            $search_data2=['and',['>=','jd_mback_money.pici',$dstart],['<=','jd_mback_money.pici',$dend]];
        }else
        {
            $search_data2=['and',['>','jd_mback_money.b_time',1]];
        }


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_ultimate_shenhe");



        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_mback_money.*,subt.name as submitname')->where("jd_mback_money.type=3")->andWhere($shenhewhere)->andFilterWhere($userswhere)->andFilterWhere(['jd_mback_money.is_ultimate_shenhe'=>$is_accomplish])->andFilterWhere(['ftype'=>$dktype])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->andWhere($search_data2)->joinWith(['submituser0 subt','advertiser0 ad'])->asArray();
        $dakuan_sum=$query->sum('jd_mback_money.b_money');
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
        $ar['dakuan_sum']=$dakuan_sum;


        return $ar;

    }



    //保证金打款审核
    public static function margin_shenhe($id,$audit_type,$audit_mr='1'){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回

        if($audit=='')
        {
            $audit=$audit_mr;
        }

        $renew_info=self::findOne($id);//退款详情


        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'margin-dakuan'])->one();



        if($audit=='2')
        {
            //如果是生成咱们给媒体打保证金 驳回了就删除
            $dkinfo=MrenewRelevance::find()->select('xf_id')->where(['dk_id'=>$id])->asArray()->all();

            $arin=ArrayHelper::getColumn($dkinfo,'xf_id');
            foreach ($arin as $v)
            {
                $onebzj=MrenewHuikuan::find()->where(['id'=>$v])->andWhere(['like','note','本公司打保证金'])->one();
                $onebzj->delete();
            }
        }


        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {

            //  var_dump($renew_info->);exit;
            //增加公司和媒介合同的实际保证金
            //查询媒介合同id
            $mhtid=MrenewRelevance::find()->where(['dk_id'=>$id])->one();
            $ren=MrenewHuikuan::findOne($mhtid['xf_id']);

            MrenewHuikuan::money_change($renew_info['advertiser'],$ren['mht_id'],'5-2-1',$renew_info['b_money']);

            //如果是实时付款的话 则 给合同分配款 并平掉实时付勾选中的续费欠额
            if($renew_info['ftype']==1)
            {
                $renew_array=MrenewRelevance::find()->where("dk_id=$renew_info[id]")->all();

                foreach ($renew_array as $key=>$value)
                {
                    $renone=MrenewHuikuan::findOne($value['xf_id']);
                    /*
                     *    $model=new MrenewHuikuan();
                     //自动添加合同打款
                     $model->advertiser=$renone['advertiser'];
                     $model->product_line=$renone['product_line'];
                     $model->type=1;
                     $model->money=$renone['xf_qiane'];
                     $model->payment_type=5;
                     $model->payment_time=time();
                     $model->submituser=Yii::$app->user->getId();
                     $model->xf_contractid=$renone['xf_contractid'];
                     $model->is_huikuan=0;
                     $model->market=$renone['market'];
                     $model->xf_qiane=0;
                     $model->audit=1;
                     $model->audit_count=2;
                     $model->audit_u=Yii::$app->user->getId();
                     $model->ctime=time();
                     $model->xf_qiane=0;
                     $model->backmoney_yue=0;
                     $model->dakuan_yue=0;
                     $model->mht_id=$renone['mht_id'];
                     $model->shishifu_onid=$renone['id'];
                     $model->is_ultimate_shenhe=1;
                     //如果添加打款成功
                     if(!$model->save()){
                         //  var_dump($value['xf_id']);
                         var_dump($model->errors);exit;
                         throw new NotFoundHttpException('生成打款失败');
                     }*/
                    //分配公司打款 合同打款
                    //JdContract::updateAllCounters(['dakuan'=>$renone['xf_qiane']],['id'=>$renone['mht_id']]);
                    //减去客户未分配打款
                   // JdCustomer::updateAllCounters(['dakuan_undistributed_yu_e'=>-$renone['xf_qiane']],['id'=>$renew_info['advertiser']]);
                    //平掉续费前额
                    MrenewHuikuan::updateAll(['xf_qiane'=>0],['id'=>$renone['id']]);
                }

            }


            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            //修改打款时间
            $renew_info->pici=time();
            $renew_info->save();



        }
    }


    public static function adddabaozhengjin($sum){
        $model=new MbackMoney();
        $postdata=\Yii::$app->request->post();



        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
        $model->b_time=strtotime($postdata['b_time']);
        $model->type=3;
        $model->ctime=time();
        if($model->save())
        {

            if($postdata['ftype']==1)
            {

                //如果输入的金额大于选中的金额就生成一条媒体打保证金的记录
                if(($model->b_money)-$sum>0){

                    $model_new=new MrenewHuikuan();
                    //判断分配款类型   1 分配到回款  2 分配到保证金 3 分配到客户补款（负数）
                    $htinfo=JdContract::findOne($postdata['ht_id']);
                    $model_new->payment_type=41;
                    $model_new->xf_qiane=$model->b_money-$sum;
                    $model_new->money=$model->b_money-$sum;
                    $model_new->dakuan_yue=0;
                    $model_new->note='本公司打保证金';
                    $model_new->is_ultimate_shenhe=1;


                    $model_new->xf_contractid=$htinfo['id'];
                    $model_new->mht_id=$htinfo['id'];
                    $model_new->advertiser=$htinfo['advertiser'];
                    $model_new->payment_time=time();
                    $model_new->ctime=time();
                    $model_new->type=3;
                    $model_new->submituser=Yii::$app->user->id;
                    $model_new->ctime=time();
                    $model_new->audit_u=Yii::$app->user->id;
                    $model_new->audit=1;//默认审核通过
                    $model_new->audit_count=2;//2级默认审核通过

                    $model_new->ht_state=$htinfo['contract_state'];

                    if(!$model_new->save())
                    {
                        var_dump($model_new->errors);
                        throw new Exception('生成打保证金失败！请检查 详情');
                        exit;
                    }

                    array_push($postdata['renew_idin'],$model_new->id);
                }
                foreach ($postdata['renew_idin'] as $key=>$val)
                {
                    $model_renew_guanlian=new MrenewRelevance();
                    $model_renew_guanlian->dk_id=$model->id;
                    $model_renew_guanlian->xf_id=$val;
                    $model_renew_guanlian->save();
                }

            }

            return $model;
        }else
        {
            var_dump($model->errors);exit;
        }

    }

    public static function margin_m_list($id='',$shenhewhere2='',$role=''){


        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_mrenew_huikuan.id',0];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_mrenew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'). " +1 day");

        if($start and $end)
        {
            //$search_data=['and',['>=','jd_mrenew_huikuan.payment_time',$start],['<','jd_mrenew_huikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.payment_time', $start], ['<', 'jd_mrenew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_mrenew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_mrenew_huikuan.payment_time',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');
        if($shenhe=='1')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=0 or jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_mrenew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;

        //是否退款
        $istui=Yii::$app->request->post('istui');
        if($istui=='1')
        {
            $shenhewhere.=' and mren.id >0 and jd_mrenew_huikuan.margin_istui=1';
        }elseif ($istui=='2')
        {
            $shenhewhere.=' and mren.id notnull and jd_mrenew_huikuan.margin_istui=0';
        }elseif ($istui=='3')
        {
            $shenhewhere.=' and mren.id isnull and jd_mrenew_huikuan.margin_istui=0';
        }

        //查询自己的组
        $query=MrenewHuikuan::find()
            ->select('ad.advertiser as adname,jd_mrenew_huikuan.*,subt.name as submitname,prlin.name as prlina,ht.title,mren.id as issqtui')
            ->where("jd_mrenew_huikuan.payment_type in(41)")
            ->andFilterWhere($userswhere)
            ->andWhere($shenhewhere)
            ->andFilterWhere(['jd_contract.advertiser'=>$id])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','advertiser0 ad'])
            ->leftJoin('jd_contract ht','ht.id=jd_mrenew_huikuan.mht_id')
            ->leftJoin("jd_product_line prlin","prlin.id=ht.product_line")
            ->leftJoin('jd_mrenew_huikuan mren','mren.xf_id=jd_mrenew_huikuan.id')
            ->asArray();

        $bzj_tui_list=Yii::$app->db->createCommand('select * from  jd_mrenew_huikuan where id in(select max(id) from jd_mrenew_huikuan where payment_type=42 group by xf_id) ')->queryAll();
        foreach ($bzj_tui_list as $val)
        {
            $bzj_max_list[$val['xf_id']]=$val;
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

        foreach ($ar['data']  as $key=>$val)
        {
            if($bzj_max_list[$val['id']])
            {
               
                if($bzj_max_list[$val['id']]['audit']=='2')
                {
                    unset($ar['data'][$key]['issqtui']);
                }
            }

        }

        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();

        return $ar;
    }



    //媒体退保证金审核
    public static function tui_margin_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        //默认通过
        if(empty($audit))
        {
            $audit=1;
        }

        $renew_info=MrenewHuikuan::findOne($id);//退款详情

        //查询退款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'meiti-margin-tuikuan'])->one();

        //如果最终审核通过
        if($audit=='1' && $audit_type==$renew_shenhe['audit_count'] )
        {

            //  var_dump($renew_info->);exit;
            $mhtinfo=JdContract::findOne($renew_info['mht_id']);
            //减少媒体合同 和公司实际保证金
            MrenewHuikuan::money_change($mhtinfo['advertiser'],$renew_info['mht_id'],'5-2-1',$renew_info['dakuan_yue'],'jian');



            //查询此比退款对应的媒体保证金
            //$renone=MrenewHuikuan::findOne($id);


            MrenewHuikuan::updateAll(['margin_istui'=>1],['id'=>$renew_info['xf_id']]);

            //查找退款对应的打款id,再查找客户的保证金 设置为已退款
            $mrenewone=MrenewHuikuan::findOne($renew_info['xf_id']);
            RenewHuikuan::updateAll(['meiti_margin_istui'=>1],['id'=>$mrenewone['xf_id']]);

            //修改为最终审核
            $renew_info->is_ultimate_shenhe=1;
            //修改备注内容
            if(\Yii::$app->request->post('audit')){
                $renew_info->note=date('Y-m-d').'退保证金'.$renew_info->dakuan_yue.'__'.$renew_info->note;
            }
            $renew_info->dakuan_yue=0;


            $renew_info->save();





        }
    }


    public static function margin_tui_list($id='',$shenhewhere2='',$role=''){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.submituser'=>$uid],['ht.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_mrenew_huikuan.id',0];
        }

        //筛选条件
        $Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_mrenew_huikuan.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'). " +1 day");

        if($start and $end)
        {
            //$search_data=['and',['>=','jd_mrenew_huikuan.payment_time',$start],['<','jd_mrenew_huikuan.payment_time',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.payment_time', $start], ['<', 'jd_mrenew_huikuan.payment_time', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_mrenew_huikuan.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_mrenew_huikuan.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_mrenew_huikuan.payment_time',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');
        if($shenhe=='1')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=0 or jd_mrenew_huikuan.audit=1 and jd_mrenew_huikuan.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_mrenew_huikuan.audit=2";
        }else
        {
            $shenhewhere="jd_mrenew_huikuan.id>0";
        }
        $shenhewhere.=$shenhewhere2;

        //查询自己的组
        $query=MrenewHuikuan::find()
            ->select('ad.advertiser as adname,jd_mrenew_huikuan.*,subt.name as submitname,prlin.name as prlina,ht.title')
            ->where("jd_mrenew_huikuan.payment_type in(42)")
            ->andFilterWhere($userswhere)
            ->andWhere($shenhewhere)
            ->andFilterWhere(['jd_contract.advertiser'=>$id])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->joinWith(['submituser0 subt','advertiser0 ad'])
            ->leftJoin('jd_contract ht','ht.id=jd_mrenew_huikuan.mht_id')
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

}
