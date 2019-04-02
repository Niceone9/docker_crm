<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "jd_holiday".
 *
 * @property integer $id
 * @property string $bumen
 * @property string $zhiwu
 * @property string $type
 * @property string $shiyou
 * @property integer $starttime
 * @property integer $endtime
 * @property string $shuoming
 * @property integer $submituser
 * @property integer $audit_1
 * @property integer $audit_2
 * @property integer $ctime
 * @property integer $susers1
 * @property integer $susers2
 * @property double $day
 */
class Holiday extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jd_holiday';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'shiyou', 'starttime', 'endtime', 'submituser', 'ctime'], 'required'],
            [['shiyou', 'shuoming'], 'string'],
            [['starttime', 'endtime', 'submituser', 'audit', 'audit_count', 'ctime'], 'integer'],
            [['day'], 'number'],
            [['bumen', 'zhiwu', 'type'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bumen' => 'Bumen',
            'zhiwu' => 'Zhiwu',
            'type' => 'Type',
            'shiyou' => 'Shiyou',
            'starttime' => 'Starttime',
            'endtime' => 'Endtime',
            'shuoming' => 'Shuoming',
            'submituser' => 'Submituser',
            'audit_1' => 'Audit 1',
            'audit_2' => 'Audit 2',
            'ctime' => 'Ctime',
            'susers1' => 'Susers1',
            'susers2' => 'Susers2',
            'day' => 'Day',
        ];
    }


    //关联数据
    public function getSubmituser0(){
        return $this->hasOne(UserBackend::className(),['id'=>'submituser'])->select('id,name');
    }



    //关联审核人
    public function getAudituname(){
        return $this->hasOne(UserBackend::className(),['id'=>'audit_u'])->select('name');
    }



    public static function qingjia_list($shenhewhere2='',$role=''){


        //获取用户拥有的访问function权限
        $rowes=\Yii::$app->hjd->get_roles();
        //判断显示权限 1 只能看自己 2 能看自己组 3能看全部


        if(Yii::$app->hjd->list_type($role)==1 or Yii::$app->hjd->list_type($role)=='')
        {
            $uid=Yii::$app->user->getId();
            $userswhere=['or',['jd_holiday.submituser'=>$uid]];
        }elseif (Yii::$app->hjd->list_type($role)==2)
        {
            $uid=Yii::$app->hjd->asbumen();
            //查询销售负责所有客户
            $userswhere=['or',['jd_holiday.submituser'=>$uid]];

        }elseif (Yii::$app->hjd->list_type($role)==3)
        {
            $userswhere=['>','jd_holiday.id',0];
        }




        //筛选条件
        //$Search_type=Yii::$app->request->post('Search_type');
        $Search_str=Yii::$app->request->post('Search_str');

        $searchwhere = ['or',['like','subt.name',$Search_str]];
        //日期筛选条件
        $start=strtotime(Yii::$app->request->post('start_date'));
        $end=strtotime(Yii::$app->request->post('end_date'));
        $end=strtotime("+1 days",$end);
        if($start and $end)
        {
            $search_data=['and',['>','jd_holiday.ctime',$start],['<','jd_holiday.ctime',$end]];

        }else
        {
            $search_data=['and',['>','jd_holiday.ctime',1]];
        }

        //审核条件
        $shenhe=Yii::$app->request->post('shenhe');

        if($shenhe=='1')
        {
            $shenhewhere="jd_holiday.audit=1 and jd_holiday.is_ultimate_shenhe=1";
        }elseif($shenhe=='0')
        {
            $shenhewhere="(jd_holiday.audit=0 or jd_holiday.audit=1) and jd_holiday.is_ultimate_shenhe=0";
        }elseif($shenhe=='2')
        {
            $shenhewhere="jd_holiday.audit=2";
        }else
        {
            $shenhewhere="jd_holiday.id>0";
        }


        /*
        //不是待办的时候执行
        if($shenhewhere2==''){
            //查询公司需要几级审核
            $shenhe=AuditAction::find()->where(['action_name'=>'qingjia'])->one();

            for ($i=$shenhe['audit_count'];$i>0;$i--){

                if(in_array('/api/qingjia/shenhe'.$i,$rowes))
                {
                    if($i>1) {
                        $shenhewhere2 = " and jd_holiday.audit=1 and jd_holiday.audit_count>=$i-1";
                    }else
                    {
                        $shenhewhere2 = " and jd_holiday.audit_count>=$i";
                    }

                }

                break;
            }
        }
        $shenhewhere.=$shenhewhere2;
        */

       // print_r($shenhewhere);
       // exit;

        //完成状态
        $is_accomplish=Yii::$app->request->post("is_accomplish");

        //收支类型
        $f_type=Yii::$app->request->post("f_type");

        //查询自己的组
        $query=self::find()->select('jd_holiday.*,subt.name as submitname')->andWhere($shenhewhere)->andFilterWhere($userswhere)->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt'])->asArray();
        //$a=clone  $query;
        //echo $a->createCommand()->sql;exit;
        //总额
        //  $query_count=self::find()->andFilterWhere($userswhere)->andFilterWhere(['jd_invoice.audit'=>$shenhe,'is_accomplish'=>$is_accomplish])->andFilterWhere($searchwhere)->andFilterWhere($search_data)->joinWith(['submituser0 subt','advertiser0 ad'])->sum('jd_back_money.b_money');


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

    public static function add_qingjia(){
        $model=new Holiday();
        $model->load(Yii::$app->getRequest()->getBodyParams(),'');
        $model->ctime=time();
        $model->starttime=strtotime($model->starttime);
        $model->endtime=strtotime($model->endtime);
        if($model->type=='年假')
        {
            $users=UserBackend::findOne($model->submituser);
            if($users->nianjia<$model->day)
            {
                return array('code'=>500,'msg'=>'剩余年假时间不足');
            }
        }
        if($model->save())
        {

            return $model;
        }else
        {
            return array('code'=>500,'msg'=>'添加失败','data'=>$model->errors);
        }

    }

    public static function qingjia_info($id){
        //具体数据
        $data1=self::find()->where(['id'=>$id])->With(['submituser0','audituname'])->asArray()->one();
        $data['code']='200';
        $data['data']=$data1;
        return $data;
    }

    //发票审核操作

    public static function qingjia_shenhe($id,$audit_type){
        $audit=\Yii::$app->request->post('audit'); //审核状态 1通过 2 驳回
        $invoice_info=self::findOne($id);//回款详情

        $subinfo=UserBackend::findOne($invoice_info['submituser']);



        //查询公司回款需要几级审核
        $renew_shenhe=AuditAction::find()->where(['action_name'=>'qingjia'])->one();

        //如果审核通过 并且是最后一级审核
        if($audit=='1' and $audit_type==$renew_shenhe['audit_count'])
        {
            //修改年假时间
            if($invoice_info['type']=='年假')
            {
                $subinfo=UserBackend::findOne($invoice_info['submituser']);
                if($subinfo['nianjia']-$invoice_info->day <0)
                {
                    throw new \Exception('年假不足！');
                }

                UserBackend::updateAllCounters(['nianjia'=>-$invoice_info->day],['id'=>$invoice_info['submituser']]);



                $nianjialog= new NianjiaLog();
                $nianjialog->uid=$invoice_info['submituser'];
                $nianjialog->note='修改'.$subinfo['name'].'用户id'.$invoice_info['submituser'].'年假-'.$invoice_info->day.'天';
                $nianjialog->time=date('Y-m-d H:i:s');
                $nianjialog->save();



            }
            //修改为最终审核
            $invoice_info->is_ultimate_shenhe=1;
            $invoice_info->save();
        }



    }
}
