<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "jd_invoice".
 *
 * @property integer $id
 * @property integer $main_company
 * @property integer $invoice_head
 * @property string $type
 * @property double $money
 * @property integer $is_back_money
 * @property string $file
 * @property string $contract_no
 * @property string $note
 * @property integer $state
 * @property integer $ctime
 * @property integer $submituser
 * @property integer $audit_1
 * @property integer $audit_2
 * @property string $fp_on
 * @property integer $kp_time
 * @property integer $type2
 * @property string $appname
 * @property integer $users2
 * @property integer $susers1
 * @property integer $susers2
 * @property integer $contract_id
 * @property integer $shouzhi
 */
class Invoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['main_company', 'invoice_head', 'type', 'money', 'ctime', 'submituser'], 'required'],
            [['main_company', 'invoice_head', 'is_back_money', 'state', 'ctime', 'submituser', 'audit_count', 'audit', 'kp_time', 'type2', 'users2', 'audit_u', 'contract_id', 'shouzhi','cu_ad_id','is_huikuan'], 'integer'],
            [['money'], 'number'],
            [['note'], 'string'],
            [[ 'contract_no', 'appname'], 'string', 'max' => 50],
            [['file', 'fp_on'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'main_company' => 'Main Company',
            'invoice_head' => 'Invoice Head',
            'type' => 'Type',
            'money' => 'Money',
            'is_back_money' => 'Is Back Money',
            'file' => 'File',
            'contract_no' => 'Contract No',
            'note' => 'Note',
            'state' => 'State',
            'ctime' => 'Ctime',
            'submituser' => 'Submituser',
            'audit_1' => 'Audit 1',
            'audit_2' => 'Audit 2',
            'fp_on' => 'Fp On',
            'kp_time' => 'Kp Time',
            'type2' => 'Type2',
            'appname' => 'Appname',
            'users2' => 'Users2',
            'susers1' => 'Susers1',
            'susers2' => 'Susers2',
            'contract_id' => 'Contract ID',
            'shouzhi' => 'Shouzhi',
        ];
    }


    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }

    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'invoice_head'])->select('id,advertiser,customer_type,tax_identification,ticket_address,open_account,account,kp_tel');
    }

    //关联审核人
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }
    //关联主体公司
    public function getAgenCompany(){
        return $this->hasOne(AgentCompany::className(),['id'=>'main_company']);
    }
    //关联开票类型
    public function getFptype(){
        return $this->hasOne(Piaotype::className(),['id'=>'type']);
    }
    public function getGuanggaozhu(){
        return $this->hasOne(CustomerAdvertiser::className(),['id'=>'cu_ad_id']);
    }


    public static function fp_list($shenhewhere2='',$role=''){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();

            $userswhere=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
             $uid=Yii::$app->hjd->asbumen();

            //查询销售负责所有客户
            $adidlist=JdContract::find()->select('advertiser')->where(['market'=>$uid])->distinct('advertiser')->column();
            $userswhere=['or',['ad.business'=>$uid],['ad.id'=>$adidlist]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_invoice.id',0];
        }



        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','jd_invoice.appname' ,$Search_str],['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            //$search_data=['and',['>','jd_invoice.ctime',$start],['<','jd_invoice.ctime',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'jd_invoice.ctime', $start], ['<', 'jd_invoice.ctime', $end]];
            }else{
                $search_data = ['and', ['>=', 'jd_invoice.last_time', date("Y-m-d H:i:s",$start)], ['<', 'jd_invoice.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','jd_invoice.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_invoice.audit=1 and jd_invoice.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="jd_invoice.audit=0 or jd_invoice.audit=1 and jd_invoice.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_invoice.audit=2";
        }else
        {
            $shenhewhere="jd_invoice.id>0";
        }
        $shenhewhere.=$shenhewhere2;


        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");


        //发票回款状态
        $is_huikuan=Yii::$app->request->post("is_huikuan");

        //收支类型
        $f_type=Yii::$app->request->post("f_type"); //1开出 2 收到

        //查询自己的组
        $query=self::find()->select('ad.advertiser as adname,jd_invoice.*,subt.name as submitname,ggz.advertiser as guanggaozhu')
            ->andWhere($shenhewhere)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['is_accomplish'=>$is_accomplish,'shouzhi'=>$f_type])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere(['is_huikuan'=>$is_huikuan])
            ->joinWith(['submituser0 subt','advertiser0 ad','guanggaozhu ggz'])
            ->asArray();
        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;
        //总额
       //  $query_count=self::find()->andFilterWhere($userswhere)->andFilterWhere(['jd_invoice.audit'=>$shenhe,'is_accomplish'=>$is_accomplish])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','advertiser0 ad'])->sum('jd_back_money.b_money');
        $query_count=$query->sum('jd_invoice.money');

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

    public static function add_fapiao(){
        $model=new Invoice();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->ctime=time();
        /*
        if($model->money<0)
        {
            return array('code'=>500,'msg'=>'不能输入负数');
        }*/
        //判断该发票是否已经回款
        $customer=JdCustomer::findOne($model->invoice_head);
        $fp_huikuan=$customer->huikuan - $customer->fapiao;

        if($fp_huikuan >= $model->money)
        {
            $model->is_huikuan=1;
        }else
        {
            $model->is_huikuan=0;
        }


        if($model->save())
        {
            if($model->shouzhi=='1'){
                //如果申请发票成功则修改公司发票总额
                 JdCustomer::updateAllCounters(['fapiao'=>$model->money],['id'=>$model->invoice_head]);
            }
            if($model->shouzhi=='2'){
                //如果申请发票成功则修改公司发票总额
                JdCustomer::updateAllCounters(['s_invoice'=>$model->money],['id'=>$model->invoice_head]);
            }
            return $model;
        }else
        {
            throw new NotFoundHttpException('添加失败');
            //return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }

    }

    public static function invoice_info($id){
        //具体数据
        $data1=self::find()->where(['id'=>$id])->With(['submituser0','advertiser0','audituname','agenCompany','fptype','guanggaozhu'])->asArray()->one();

        return $data1;
    }

    //发票审核操作

    public static function fapiao_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $invoice_info=self::findOne($id);//回款详情
        //如果是驳回减少公司发票值
        if($audit=='2' && $invoice_info->shouzhi=='1')
        {
            //如果申请发票成功则修改公司发票总额
            JdCustomer::updateAllCounters(['fapiao'=>-$invoice_info->money],['id'=>$invoice_info->invoice_head]);
        }
        if($audit=='2' && $invoice_info->shouzhi=='2')
        {
            //如果申请发票成功则修改公司发票总额
            JdCustomer::updateAllCounters(['s_invoice'=>-$invoice_info->money],['id'=>$invoice_info->invoice_head]);
        }

        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'Invoice'])->one();
        //如果审核通过 并且是最后一级审核
        if($audit=='1' and $audit_type==$renew_shenhe['audit_count'])
        {
            //修改为最终审核
            $invoice_info->is_ultimate_shenhe=1;
            $invoice_info->save();
        }




    }


    //更新发票回款状态 根据回款总额 和发票总额
    public static function up_invoice_state($ad_id){
        $adinfo=JdCustomer::findOne($ad_id);
        $huikuan=$adinfo->huikuan;
        //获取公司发票总额 未审核和已经通过审核的
        $invoice_list=Invoice::find()
            ->where([
                'invoice_head'=>$ad_id,
            ])
            ->andWhere('audit != 2')
            ->orderBy('ctime asc')
            ->asArray()
            ->all();

        //先把所有发票改为未回款 然后再根据回款算出发票是否已经回款
        Invoice::updateAll(['is_huikuan'=>0],['invoice_head'=>$ad_id]);

        foreach ($invoice_list as $key=>$value)
        {
            if($value['money']-$huikuan >0)
            {
                break;
            }else{
                $huikuan-=$value['money'];
                Invoice::updateAll(['is_huikuan'=>1],['id'=>$value['id']]);
            }
        }
    }

}
