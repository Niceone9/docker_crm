<?php

namespace backend\models;

use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

/**
 * This is the model class for table "qudao_renew".
 *
 * @property integer $id
 * @property integer $account
 * @property integer $submitusers
 * @property integer $ctime
 * @property string $last_time
 * @property string $note
 * @property integer $audit
 * @property integer $audit_u
 * @property integer $is_ultimate_shenhe
 * @property integer $audit_count
 * @property string $qiniu_key
 * @property integer $advertiser
 * @property string $money
 * @property string $show_money
 */
class QudaoRenew extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qudao_renew';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['account_id', 'submituser', 'ctime', 'audit', 'audit_u', 'is_ultimate_shenhe', 'audit_count', 'advertiser','crm_renew_id','payment_type','payment_time'], 'integer'],
            [['last_time'], 'safe'],
            [['note'], 'string'],
            [['money', 'show_money'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account' => 'Account',
            'submitusers' => 'Submituser',
            'ctime' => 'Ctime',
            'last_time' => 'Last Time',
            'note' => 'Note',
            'audit' => 'Audit',
            'audit_u' => 'Audit U',
            'is_ultimate_shenhe' => '是否是最终审核 0不是 1是',
            'audit_count' => 'Audit Count',
            'qiniu_key' => 'Qiniu Key',
            'advertiser' => 'Advertiser',
            'money' => 'Money',
            'show_money' => 'Show Money',
        ];
    }


    static function addrenew(){
        $model=new QudaoRenew();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->ctime=time();
        $model->last_time=date("Y-m-d H:i:s");
        $model->payment_time = strtotime(Yii::$app->getRequest()->getBodyParams()['payment_time']);
        if($model->save())
        {
            return true;
        }else
        {
            var_dump($model->errors);exit;

        }
    }

    static function alist(){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $avidin=QudaoToAdvertiser::find()->select('advertiser_id')->where(['user_id'=>$uid])->column();

            $userswhere=['or',['c.id'=>$avidin]];

        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['a.submituser'=>$uid]];


        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','a.submituser',0];

        }
        //筛选条件
        $Search_str=\Yii::$app->request->get('Search_str');
        $searchwhere = ['or',['like','c.advertiser', $Search_str],['like','b.appname' ,$Search_str],['like','u.name',$Search_str],['like','b.a_users' ,$Search_str],['like','pr.name' ,$Search_str]];
        //日期筛选条件
        $start=strtotime(\Yii::$app->request->get('start_date'));
        $end=strtotime(\Yii::$app->request->get('end_date'));
        $end=strtotime("+1 days",$end);

        //公司条件筛选
        $av_id=\Yii::$app->request->get('av_id');

        if($start and $end)
        {
            //$search_data=['and',['>','a.ctime',$start],['<','a.ctime',$end]];
            if(Yii::$app->request->post('date_type')!='2') {
                $search_data = ['and', ['>=', 'a.ctime', $start], ['<', 'a.ctime', $end]];
            }else{
                $search_data = ['and', ['>=', 'a.last_time', date("Y-m-d H:i:s",$start)], ['<', 'a.last_time',  date("Y-m-d H:i:s",$end)]];
            }
        }else
        {
            $search_data=['and',['>','a.ctime',1]];
        }


        //审核条件
        $shenhe=Yii::$app->request->get('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="a.audit=1 and a.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="(a.audit=0 or a.audit=1) and a.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="a.audit=2";
        }else
        {
            $shenhewhere="a.id>0";
        }



        $list=QudaoRenew::find()
            ->select('a.id,a.note,a.money,a.show_money,a.ctime,b.a_users,b.url,b.appname,a.last_time,c.advertiser,u.name,pr.name as prname,a.audit,a.is_ultimate_shenhe,a.audit_count,pr.fk_type,a.payment_type,a.payment_time')
            ->join('a left join','jd_account b','b.id=a.account_id')
            ->leftJoin('jd_customer c','c.id=b.avid')
            ->leftJoin('user_backend u','u.id=a.submituser')
            ->leftJoin('jd_product_line pr','pr.id=b.prlin_id')
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['c.id'=>$av_id])
            ->andWhere($shenhewhere)
            ->andWhere('a.audit in(0,2)')
            ->orderBy('a.id desc')
            ->asArray()
            ->all();



        $list2=RenewHuikuan::find()
            ->select('a.id,a.note,a.money,a.show_money,a.ctime,b.a_users,b.url,b.appname,a.last_time,c.advertiser,u.name,pr.name as prname,a.audit,a.is_ultimate_shenhe,a.audit_count,pr.fk_type,a.payment_type,a.payment_time')
            ->join('a left join','jd_account b','b.id=a.account')
            ->leftJoin('jd_customer c','c.id=b.avid')
            ->leftJoin('user_backend u','u.id=a.submituser')
            ->leftJoin('jd_product_line pr','pr.id=b.prlin_id')
            ->andWhere($userswhere)
            ->andFilterWhere(['a.is_ultimate_shenhe'=>1,'a.payment_type'=>[1,2]])
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->asArray()
            ->all();

        if(!is_array($list))
        {
            $list=[];
        }
        if(!is_array($list2))
        {
            $list2=[];
        }

        $alllist=array_merge($list,$list2);




        $provider = new ArrayDataProvider([
            'allModels' => $alllist,
            'sort' => [
                'attributes' => ['id'],
            ],

        ]);
// get the posts in the current page
        $list_page = $provider->getModels();

        $return_data['count']=$provider->getTotalCount();
        $return_data['current_page']=$provider->pagination->page+1;
        $return_data['size']=$provider->pagination->pageSize;
        $list_page=array_merge($list_page);

        return array('code'=>'200','msg'=>'success','data'=>$list_page,'page_data'=>$return_data);
    }

    public static function shenhe($id){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $xfinfo=self::findOne($id);//续费详情
        //账户信息
        $accountinfo=Account::findOne($xfinfo['account_id']);
        $contract=JdContract::contractinfonomt($accountinfo->contract_id);

        if($audit=='1')
        {
            //如果有个人返点的话
            if($contract['rencontract']['xj_fandian']>0)
            {
                $money=$xfinfo->money*(1-($contract['rencontract']['xj_fandian']/100));
                $ys_money=$xfinfo->money;
            }else
            {
                $money=$xfinfo->money;
                $ys_money=$xfinfo->money;
            }


            //生成续费
            $renew=RenewHuikuan::add_renew([
                'advertiser'=>$xfinfo->advertiser,
                'submituser'=>$xfinfo->submituser,
                'xf_contractid'=>$accountinfo->contract_id,
                'market'=>$contract['market0']['id'],
                'account'=>$accountinfo->id,
                'rebates_proportion'=>$contract['rencontract']['fandian'],
                'money'=>$money,
                'ys_money'=>$ys_money,
                'show_money'=>Yii::$app->hjd->show_money($xfinfo->money,$accountinfo->contract_id,$xfinfo->show_money),
                //'show_money'=>$backmoney_info->money*((100+$zrhtinfo['rencontract']['fandian'])/100),
                'payment_type'=>$xfinfo->payment_type,
                'payment_time'=>time(),
                'note'=>'由渠道系统续费审核通过生成的续费 crm系统操作',
                'cu_ad_id'=>$accountinfo->cu_ad_id
            ]);

            $xfinfo->is_ultimate_shenhe=1;
            $xfinfo->crm_renew_id=$renew->id;
            $xfinfo->save();
        }


    }


}
