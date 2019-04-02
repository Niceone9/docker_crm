<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/12
 * Time: 17:24
 */
namespace backend\modules\api\controllers;
use backend\models\Account;
use backend\models\BackMoney;
use backend\models\Customerb;
use backend\models\JdContract;
use backend\models\JdCustomer;
use backend\models\QudaoDemand;
use backend\models\QudaoDomainTemplate;
use backend\models\QudaoRenew;
use backend\models\RenewHuikuan;
use backend\models\Yuming;
use yii\rest\ActiveController;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use Yii;
use backend\components\Quanxian;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class QudaoapiController extends ActiveController
{
    public $modelClass = 'backend\models\Customerb';
    //页面显示分页信息
    public $serializer=[
        'class'=>'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    //
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        $behaviors['myBehavior']=[
            'class'=>Quanxian::className(),
        ];
        return $behaviors;
    }

    //渠道下所有公司
    public function actionQudao_to_ad(){

        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['b.user_id'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['b.user_id'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','b.user_id',0];
        }


        $ad=JdCustomer::find()
            ->select("a.id,a.advertiser")
            ->join("a left join","qudao_to_advertiser b","a.id=b.advertiser_id")
            ->andFilterWhere($userswhere)
            ->asArray()
            ->all();
        return ['code'=>'200','data'=>$ad];
    }





    //公司下所有账户
    public function actionAd_to_account(){
        //获取公司下所有未到期账户
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['c.user_id'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['c.user_id'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','c.user_id',0];
        }
        //筛选条件
        $adid=Yii::$app->request->post('av_id');
        $Search_str=Yii::$app->request->post('search');
        $searchwhere = ['or',['like','b.advertiser', $Search_str],['like','u.name' ,$Search_str],['like','a.a_users',$Search_str]];




        $query=Account::find()
            ->select("a.id,a.a_users,b.advertiser,u.name as qudao_name,a.ctime,a.promote_url,a.contract_id")
            ->join('a left join','jd_customer b','a.avid=b.id')
            ->leftJoin('qudao_to_advertiser c','c.advertiser_id=b.id')
            ->leftJoin('user_backend u','u.id=c.user_id')
            ->andWhere(['a.endtime'=>'4092599349'])
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['b.id'=>$adid])
            ->andFilterWhere($searchwhere)
            ->asArray();



        $provider = new ActiveDataProvider([
            'query' => $query,
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
        return ['code'=>'200','data'=>$ar];
    }

    //查看账户详情
    public function actionAccount_info(){

    }

    //查看账户下所有二级域名
    public function actionAccount_domain($id)
    {
        $list = Yuming::find()
            ->where(['account_id' => $id])
            ->andWhere("true_url !=''")
            ->asArray()
            ->all();

        foreach ($list as $key => $val)
        {
            $list[$key]['iframeurl']=self::actionDomain_template($val['true_url'])['iframeurl'];
            $logourl=self::actionDomain_template($val['true_url'])['logourl'];
            $list[$key]['logourl']=explode (',',$logourl);
        }

        return array('code'=>'200','msg'=>'success','data'=>$list);
    }

    //新增落地页需求
    public function actionAdd_ye(){

        if($data=Yuming::addy()){
            return array('code'=>'200','msg'=>'success','data'=>$data);
        }else
        {
            return array('code'=>'500','msg'=>'fall');
        }
    }


    //落地页需求列表

    public function actionYelist(){
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部
        if(Yii::$app->hjd->list_type()==1 or Yii::$app->hjd->list_type()=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['a.submitusers'=>$uid]];
        }elseif (Yii::$app->hjd->list_type()==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            $userswhere=['or',['a.submitusers'=>$uid]];

        }elseif (Yii::$app->hjd->list_type()==3)
        {
            $userswhere=['>','a.submitusers',0];
        }
        //筛选条件

        $Search_str=\Yii::$app->request->get('Search_str');

        $searchwhere = ['or',['like','c.advertiser', $Search_str],['like','b.appname' ,$Search_str],['like','u.name',$Search_str],['like','b.a_users' ,$Search_str],['like','pr.name' ,$Search_str],['like','domain.true_url' ,$Search_str]];
        //日期筛选条件

        $start=strtotime(\Yii::$app->request->get('start_date'));
        $end=strtotime(\Yii::$app->request->get('end_date'));
        $end=strtotime("+1 days",$end);

        //公司条件筛选
        $av_id=\Yii::$app->request->get('av_id');

        if($start and $end)
        {
            $search_data=['and',['>','a.ctime',$start],['<','a.ctime',$end]];
        }else
        {
            $search_data=['and',['>','a.ctime',1]];
        }

        //操作条件
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


        $list=QudaoDemand::find()
            ->select('b.id as account_id,a.id,a.note,domain.true_url,a.ctime,b.a_users,a.zip_link,b.appname,a.last_time,c.advertiser,b.promote_url as url,u.name,pr.name as prname,a.note,a.audit,a.is_ultimate_shenhe,a.audit_count,a.link')
            ->join('a left join','jd_account b','b.id=a.account')
            ->leftJoin('jd_customer c','c.id=b.avid')
            ->leftJoin('user_backend u','u.id=a.submitusers')
            ->leftJoin('jd_product_line pr','pr.id=b.prlin_id')
            ->leftJoin('yuming domain','domain.id=a.domain')
            ->andFilterWhere($searchwhere)
            ->andFilterWhere($search_data)
            ->andFilterWhere($userswhere)
            ->andFilterWhere(['c.id'=>$av_id])
            ->andWhere($shenhewhere)
            ->orderBy('a.id desc')
            ->asArray();

        if(\Yii::$app->request->get('excel')=='1')
        {
            set_time_limit(0);
            \moonland\phpexcel\Excel::export([
                'models'=>$list->all(),
                'fileName'=>'二级域名解析数据'.date("Y-m-d"),
                'columns' => ['id','note','true_url','a_users','appname','last_time','advertiser','name','prname'], //without header working, because the header will be get label from attribute label.
                'headers' => ['id' => '编号','note' => '备注', 'true_url' => '二级域名','a_users'=>'账户','appname'=>'appname','last_time'=>'操作时间','advertiser'=>'公司','name'=>'提交人','prname'=>'产品线'],

            ]);


        }


        $count=$list->count();


        $pagination=new Pagination(['totalCount'=>$count]);
        $par_page=Yii::$app->request->get('per-page');
        $list_page=$list->offset($pagination->offset)
            ->limit($par_page?$par_page:$pagination->limit)
            ->all();
        $return_data['count']=$pagination->totalCount;
        $return_data['current_page']=$pagination->page;
        $return_data['size']=$pagination->pageSize;

        return array('code'=>'200','msg'=>'success','data'=>$list_page,'page_data'=>$return_data);
//        if($list_page)
//        {
//            return array('code'=>'200','msg'=>'success','data'=>$list_page,'page_data'=>$return_data);
//        }else
//        {
//            return array('code'=>'500','msg'=>'没有数据');
//        }

    }


    public function actionUpyestatus($id){
        $model= QudaoDemand::findOne($id);
        $model->load(\Yii::$app->getRequest()->getBodyParams(),'');
        $model->last_time=date("Y-m-d H:i:s");

        if($model->save())
        {
            return array('code'=>'200','msg'=>'success','data'=>$model);
        }else
        {
            return array('code'=>'500','msg'=>'FAIL');
        }

    }

    public function actionDeleteye($id)
    {
        if(Yuming::deleteAll(['id'=>$id]))
        {
            return array('code'=>'200','msg'=>'success','data'=>$model);
        }else
        {
            return array('code'=>'500','msg'=>'FAIL');
        }
    }


    //一级审核
    public function actionShenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'yuming',1,'yuming');


            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }

    //二级审核
    public function actionShenhe2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'yuming',2,'yuming');
            //审核续费后续
            //Holiday::qingjia_shenhe($id,2);
            if(\Yii::$app->request->post('audit')=='1')
            {
                Yuming::updateAll(['is_ultimate_shenhe'=>1],['id'=>$id]);
            }
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }


    //一级审核
    public function actionYeshenhe1($id){

        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'demand',1,'qudao_demand');
            $tr1->commit(); //提交数据

        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');
    }

    //二级审核
    public function actionYeshenhe2($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'demand',2,'qudao_demand');
            //审核续费后续
            //Holiday::qingjia_shenhe($id,2);
            if(\Yii::$app->request->post('audit')=='1')
            {
                QudaoDemand::updateAll(['is_ultimate_shenhe'=>1],['id'=>$id]);

            }
            //删除七牛文件
            $info=QudaoDemand::findOne($id);
            PublicController::deleteqiniu($info->qiniu_key);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }


    //根据域名返回相关模板iframe url 地址
    public static function actionDomain_template($url){
        //$url=Yii::$app->request->post('domain');
        $url=explode('.',$url);
        $likeurl=$url[1].".".$url[2];

        $data=QudaoDomainTemplate::find()
            ->where(['like','domain',$likeurl])
            ->one();

        return $data;

    }

    //新增续费参考值
    public function actionAddrenewinfo($id){
        $contract=JdContract::contractinfonomt($id);
        $data['fandian']=$contract['rencontract']['fandian'];
        $data['fk_type']=$contract['mht']['prlin']['fk_type'];
        return $data;
    }

    //渠道续费新增
    public function actionAddrenew(){
        if($data=QudaoRenew::addrenew()){
            return array('code'=>'200','msg'=>'success','data'=>$data);
        }else
        {
            return array('code'=>'500','msg'=>'fall');
        }
    }

    //续费列表
    public function actionRenewlist(){
        return QudaoRenew::alist();
    }

    //修改询价类产品show_money
    public function actionUprenewshowmoney($id){
        $show_money=Yii::$app->request->post('show_money');
        $info=RenewHuikuan::find()
            ->select('pr.fk_type')
            ->join('a left join','account b','a.account=b.id')
            ->leftJoin('jd_product_line pr','pr.id=b.prlin_id')
            ->where(['a.id'=>$id])
            ->one();

        if($info->fk_type=='1')
        {
            RenewHuikuan::updateAll(['show_money'=>$show_money],['id'=>$id]);
            return array('code'=>'200','msg'=>'修改成功');
        }else
        {
            return array('code'=>'500','msg'=>'该续费不是询价类产品！');
        }

    }


    //续费审核
    public function actionRenewshenhe1($id){
        $tr1 = Yii::$app->db->beginTransaction();
        try {
            //写入历史记录
            $this->global_audit($id,'qudao_renew',2,'qudao_renew');
            //审核续费后续
            QudaoRenew::shenhe($id,2);
            $tr1->commit(); //提交数据
        } catch (Exception $e) {
            //回滚
            $tr1->rollBack();
            return $e->getMessage(); //返回自定义异常信息
        }
        return array('code'=>200,'mgs'=>'审核成功');

    }

    //回款列表
    public function actionCrm_backmoney_list(){
        $data['code']=200;
        $data['data']=BackMoney::back_money_list();
        return $data;
    }

    //退款列表
    public function actionCrm_tuikuan_list(){
        $data['code']=200;
        $realdata=RenewHuikuan::tuikuan_list('',15);

        foreach ($realdata['data'] as $key=>$val)
        {
            $returndata[$key]['id']=$val['id'];
            $returndata[$key]['a_users']=$val['a_users'];
            $returndata[$key]['adname']=$val['adname'];
            $returndata[$key]['money']=$val['money'];
            $returndata[$key]['note']=$val['note'];
            $returndata[$key]['show_money']=$val['show_money'];
            $returndata[$key]['submitname']=$val['submitname'];
            $returndata[$key]['audit']=$val['audit'];
            $returndata[$key]['audit_count']=$val['audit_count'];
            $returndata[$key]['is_ultimate_shenhe']=$val['is_ultimate_shenhe'];
            $returndata[$key]['ctime']=$val['ctime'];
            $returndata[$key]['payment_time']=$val['payment_time'];
            $returndata[$key]['prlname']=$val['prlina'];

        }
        $data['data']=$returndata;
        $data['page-count']=$realdata['page-count'];
        $data['totalCount']=$realdata['totalCount'];

        return $data;
    }




}