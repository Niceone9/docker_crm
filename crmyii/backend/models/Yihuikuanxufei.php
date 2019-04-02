<?php

namespace backend\models;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * This is the model class for table "jd_yihuikuanxufei".
 *
 * @property integer $time
 * @property integer $xf_id
 * @property integer $hk_id
 * @property string $money
 * @property string $mt_fandian
 * @property string $dl_fandian
 * @property string $xf_fandian
 * @property double $gr_fandian
 * @property string $xs_fandian
 * @property string $shifu_money
 *
 * @property double $market_tc
 * @property integer $id
 * @property integer $avid
 * @property integer $xsid
 * @property integer $ht_id
 */
class Yihuikuanxufei extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_yihuikuanxufei';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'xf_id', 'hk_id', 'avid', 'xsid', 'ht_id'], 'integer'],
            [['money', 'gr_fandian', 'shifu_money', 'market_tc','tk_shifu'], 'number'],
          
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'time' => 'Time',
            'xf_id' => 'Xf ID',
            'hk_id' => 'Hk ID',
            'money' => 'Money',
            'mt_fandian' => 'Mt Fandian',
            'dl_fandian' => 'Dl Fandian',
            'xf_fandian' => 'Xf Fandian',
            'gr_fandian' => 'Gr Fandian',
            'xs_fandian' => 'Xs Fandian',
            'shifu_money' => 'Shifu Money',
            'market_tc' => 'Market Tc',
            'id' => 'ID',
            'avid' => 'Avid',
            'xsid' => 'Xsid',
            'ht_id' => 'Ht ID',
        ];
    }

    //关联数据
    public function getXiaoshou(){
        return $this->hasOne(UserBackend::className(),['id'=>'xsid'])->select('id,name');
    }
    //关联数据
    public function getAdvertiser0(){
        return $this->hasOne(JdCustomer::className(),['id'=>'avid'])->select('id,advertiser,customer_type');
    }
    //关联数据
    public function getContract(){
        return $this->hasOne(JdContract::className(),['id'=>'ht_id'])->select('id,mht_id');
    }

    //关联数据
    public function getXufei(){
        return $this->hasOne(RenewHuikuan::className(),['id'=>'xf_id'])->select('id,payment_type');
    }

    //关联数据
    public function getHuikuan(){
        return $this->hasOne(RenewHuikuan::className(),['id'=>'hk_id'])->select('id,payment_type,is_huikuan');
    }

    public  static function yihuikuanxufeilist(){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部

        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['ad.business'=>$uid],['cont.market'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','ad.id',0];
        }

        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','xf.appname' ,$Search_str],['like','xs.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        //$end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime(Yii::$app->request->post('end_date')." +1 day");


        if($start and $end)
        {
            $search_data=['and',['>=','jd_yihuikuanxufei.time',$start],['<=','jd_yihuikuanxufei.time',$end]];
            $search_data2=['and',['>=','jd_renew_huikuan.payment_time',$start],['<=','jd_renew_huikuan.payment_time',$end]];
        }else
        {
            $search_data=['and',['>','jd_yihuikuanxufei.time',1]];
            $search_data2=['and',['>','jd_renew_huikuan.payment_time',1]];
        }

        //款type条件
        $ktype=Yii::$app->request->post('ktype');
        /*
        if($ktype!='')
        {

            foreach ($ktype as $key=>$val)
            {


                if($val=='1')
                {
                    $in1.=' or xf.payment_type=1';
                }
                if($val=='2')
                {
                    $in1.=' or xf.payment_type=2';
                }
                if($val=='14')
                {
                    $in1.=' or xf.payment_type=14';
                }
                if($val=='16')
                {
                    $in1.=' or xf.payment_type=16';
                }

                if($val=='4')
                {
                    $in2.=' or hk.is_huikuan=1 ';
                }
                if($val=='3')
                {
                    $in2.=' or hk.payment_type=3';
                }
                if($val=='15')
                {
                    $in2.=' or hk.payment_type=15';
                }

            }
            $in1= substr($in1,3,100);
            $in2= substr($in2,3,100);
            if($in1=='')
            {
                $in1=' jd_yihuikuanxufei.id!=0';
            }
            if($in2=='')
            {
                $in2='jd_yihuikuanxufei.id!=0';
            }

            $where.=" ($in1) and ($in2)";
        }else{
            $where.="jd_yihuikuanxufei.id >0";
        }*/




        //如果是续费条件
        if($ktype=='renew')
        {
            $where.='xf.payment_type in(1,2)';
        }




        //查询自己的组

        $query=self::find()
            ->select('ad.advertiser as adname,jd_yihuikuanxufei.*,xf.payment_type as xf_type,hk.payment_type as hk_type,hk.is_huikuan,xs.name,prlin.name as prlinname,account.a_users,xf.payment_time as xftime,cupr.product as appname,xf.account as xf_account,hk.account as tk_account,mht.rebates_proportion as mtfd,mht.dl_fandian  as mtdlfd,cont.kh_grfd,contre.xj_fandian')
            //->joinWith(['xiaoshou xs','advertiser0 ad','contract cont','xufei xf','huikuan hk'])
            ->leftJoin('user_backend xs',"jd_yihuikuanxufei.xsid=xs.id")
            ->leftJoin('jd_customer ad',"jd_yihuikuanxufei.avid=ad.id")
            ->leftJoin('jd_contract cont',"jd_yihuikuanxufei.ht_id=cont.id")
            ->leftJoin('jd_renew_huikuan xf',"jd_yihuikuanxufei.xf_id=xf.id")
            ->leftJoin('jd_renew_huikuan hk',"jd_yihuikuanxufei.hk_id=hk.id")
            ->leftJoin("jd_contract_relevance contre","cont.id=contre.contract_id")
            ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
            ->leftJoin('jd_account account',"xf.account=account.id")
            ->leftJoin('jd_contract mht','mht.id=account.mht_id')
            ->leftJoin('customer_advertiser_releveance cupr','cupr.id=cu_ad_product')
            ->andFilterWhere($userswhere)
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andWhere($where)
            ->asArray();


        if($ktype=='tuikuan')
        {
            $where.='hk.payment_type in(15)';
            $query=self::find()
                ->select('ad.advertiser as adname,jd_yihuikuanxufei.*,xf.payment_type as xf_type,hk.payment_type as hk_type,hk.is_huikuan,xs.name,prlin.name as prlinname,account.a_users,xf.payment_time as xftime,hk.payment_time as tktime,cupr.product as appname,xf.account as xf_account,hk.account as tk_account,mht.rebates_proportion as mtfd,mht.dl_fandian  as mtdlfd,cont.kh_grfd,contre.xj_fandian,contre.fandian as xf_fandian,')
                //->joinWith(['xiaoshou xs','advertiser0 ad','contract cont','xufei xf','huikuan hk'])
                ->leftJoin('user_backend xs',"jd_yihuikuanxufei.xsid=xs.id")
                ->leftJoin('jd_customer ad',"jd_yihuikuanxufei.avid=ad.id")
                ->leftJoin('jd_contract cont',"jd_yihuikuanxufei.ht_id=cont.id")
                ->leftJoin('jd_renew_huikuan xf',"jd_yihuikuanxufei.xf_id=xf.id")
                ->leftJoin('jd_renew_huikuan hk',"jd_yihuikuanxufei.hk_id=hk.id")
                ->leftJoin("jd_contract_relevance contre","cont.id=contre.contract_id")
                ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
                ->leftJoin('jd_account account',"hk.account=account.id")
                ->leftJoin('jd_contract mht','mht.id=account.mht_id')
                ->leftJoin('customer_advertiser_releveance cupr','cupr.id=cu_ad_product')
                ->andFilterWhere($userswhere)
                ->andFilterWhere($searchwhere)
                ->andFilterWhere($search_data)
                ->andWhere($where)
                ->asArray();
        }

        if($ktype=='bukuan')
        {
            $where.='hk.payment_type in(3,311)';
            $query=self::find()
                ->select('ad.advertiser as adname,jd_yihuikuanxufei.*,xf.payment_type as xf_type,hk.payment_type as hk_type,hk.is_huikuan,xs.name,prlin.name as prlinname,xf.payment_time as xftime,xf.account as xf_account,hk.account as tk_account,cont.kh_grfd,hk.note,contre.xj_fandian')
                //->joinWith(['xiaoshou xs','advertiser0 ad','contract cont','xufei xf','huikuan hk'])
                ->leftJoin('user_backend xs',"jd_yihuikuanxufei.xsid=xs.id")
                ->leftJoin('jd_customer ad',"jd_yihuikuanxufei.avid=ad.id")
                ->leftJoin('jd_contract cont',"jd_yihuikuanxufei.ht_id=cont.id")
                ->leftJoin('jd_renew_huikuan xf',"jd_yihuikuanxufei.xf_id=xf.id")
                ->leftJoin('jd_renew_huikuan hk',"jd_yihuikuanxufei.hk_id=hk.id")
                ->leftJoin("jd_contract_relevance contre","cont.id=contre.contract_id")
                ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
                ->andFilterWhere($userswhere)
                ->andFilterWhere($searchwhere)
                ->andFilterWhere($search_data)
                ->andWhere($where)
                ->asArray();
        }

        if($ktype=='khbukuan')
        {
            $where.='jd_renew_huikuan.payment_type in(31)';
            $searchwhere = ['or',['like','ad.advertiser', $Search_str],['like','xs.name',$Search_str]];

            $query=RenewHuikuan::find()
                ->select('ad.advertiser as adname,jd_renew_huikuan.*,jd_renew_huikuan.ctime as time,xs.name,prlin.name as prlinname,jd_renew_huikuan.note,contre.xj_fandian')
                //->joinWith(['xiaoshou xs','advertiser0 ad','contract cont','xufei xf','huikuan hk'])

                ->leftJoin('jd_customer ad',"jd_renew_huikuan.advertiser=ad.id")
                ->leftJoin('jd_contract cont',"jd_renew_huikuan.xf_contractid=cont.id")
                ->leftJoin("jd_contract_relevance contre","cont.id=contre.contract_id")
                ->leftJoin('user_backend xs',"cont.market=xs.id")
                ->leftJoin("jd_product_line prlin","prlin.id=contre.product_line")
                ->where(['jd_renew_huikuan.is_ultimate_shenhe'=>'1'])
                ->andFilterWhere($userswhere)
                ->andFilterWhere($searchwhere)
                ->andFilterWhere($search_data2)
                ->andWhere($where)
                ->asArray();
        }

        /*
        $a=clone $query;
        echo $a->createCommand()->sql;
        exit;
        */
        //

        //  echo $a->createCommand()->sql;exit;
        $provider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);

        $lit=$query->all();

        foreach ($lit as $key=>$val)
        {
            if($ktype=='renew')
            {
                $shifu_sum+=$val['shifu_money']; //实付金额总额
                $huikuan_sum+=$val['money'];
                $grfdxsjr=$val['money']*($val['gr_fandian']/100);
                $tcqlr=$val['money']-$val['shifu_money']-$grfdxsjr;
                $tcqlr_sum+=$tcqlr; //提成钱利润总额
            }
            if($ktype=='tuikuan')
            {
                $shifu_sum+=$val['tk_shifu']; //实付金额总额
                $huikuan_sum+=$val['money'];
                $grfdxsjr=$val['money']*($val['gr_fandian']/100);
                $tcqlr=$val['money']-$val['tk_shifu']-$grfdxsjr;
                $tcqlr_sum+=$tcqlr; //提成钱利润总额
            }
            if($ktype=='bukuan')
            {
                $shifu_sum+=$val['tk_shifu']; //实付金额总额
                $huikuan_sum+=$val['money'];
                $grfdxsjr=$val['money']*($val['gr_fandian']/100);
                $tcqlr=$val['money']-$val['shifu_money']-$grfdxsjr;
                $tcqlr_sum+=-$val['money']; //提成钱利润总额
            }
            if($ktype=='khbukuan')
            {
                $shifu_sum+=$val['tk_shifu']; //实付金额总额
                $huikuan_sum+=$val['money'];
                $grfdxsjr=$val['money']*($val['gr_fandian']/100);
                $tcqlr=$val['money']-$val['shifu_money']-$grfdxsjr;
                $tcqlr_sum+=$val['money']; //提成钱利润总额
            }
        }
        // 获取分页和排序数据
        $ar['data'] = $provider->getModels();
        // 在当前页获取数据项的数目
        $ar['page-count'] = $provider->getCount();

        // 获取所有页面的数据项的总数
        $ar['totalCount'] = $provider->getTotalCount();
        $ar['shifu_sum']=$shifu_sum;
        $ar['huikuan']=$huikuan_sum;
        $ar['tcqlr_sum']=$tcqlr_sum;

        return $ar;
    }
}
