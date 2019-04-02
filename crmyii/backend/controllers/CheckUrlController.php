<?php

namespace backend\controllers;
set_time_limit(0);
use backend\models\Account;
use backend\models\CherckUrlYuming;
use backend\models\JdContract;
use backend\models\RenewHuikuan;
use backend\models\UserBackend;
use Yii;
use backend\models\CherckUrl;
use backend\models\CheckUrlSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CheckUrlController implements the CRUD actions for CherckUrl model.
 */
class CheckUrlController extends Controller
{

    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CherckUrl models.
     * @return mixed
     */
    public function actionIndex()
    {

//        $path = "D:\工作\landpage\\11youxism.zhiniaotec.com";
//        $result = Yii::$app->hjd->read_all($path);
//        foreach ($result as $k=>$v) {
//            echo $v."<br>";
//        }
//
//        exit;
        
        
        $list=CherckUrlYuming::find()
            ->asArray()
            ->all();

        foreach ($list as $key=>$val)
        {

            $path = "D:\work\landpage\\".$val['url'];
            $result = Yii::$app->hjd->read_all($path,1);
            if(count($result)>1){
            foreach ($result as $k=>$v)
            {
                if(strstr($v,'htm')){

                    $url=str_replace('D:\work\landpage\\','http://',$v); // zesz
                    $url=str_replace('\\','/',$url); // zesz


                    $new_data=new CherckUrl();
                    $new_data->url=$url;
                    $new_data->userstype=2;
                    $new_data->ip=$val['account_name'];
                    if($new_data->save()){

                    }else
                    {
                        var_dump($new_data->errors);exit;
                    }


                }

            }
            }
        }

        echo 'ok';

        exit;

//        $searchModel = new CheckUrlSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CherckUrl model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CherckUrl model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CherckUrl();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CherckUrl model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    public function actionResult(){
        $model=CherckUrl::find()
            ->where( "is_weigui!='0'")
            ->asArray()
            ->all();

            $arr[1]='无LOGO或者版权';
            $arr[2]='LOGO过小';
            $arr[3]='全屏下载';
            $arr[4]='域名外跳转';
            $arr[5]='涉黄涉暴及诱导';
            $arr[6]='含有二维码,微信号,电话或者地址';
            $arr[7]='LOGO版权错误';

            foreach ($model as $key=>$value)
            {
                $weigui=explode(',',$value['is_weigui']);
                foreach ($weigui as $v)
                {
                    $model[$key]['weiguilist'].=$arr[$v];
                }

            }

            foreach ($model as $key=>$value)
            {
                echo $value['url'].'<br>'.$value['ip'].'<br> '.$value['weiguilist'] .'<br>';
                echo '---------------------------------------------------------------------------------<br>';
            }

            exit;
            var_dump($model);

    }

    public function actionUpdate($id)
    {
        $model =CherckUrl::findOne($id);
        if($model->is_check=='1'){

            return $this->redirect('checkweb?type=2');

        }

        $model->is_check=1;
        $model->is_open=0;
        $weigui=Yii::$app->request->post('is_weigui');
        //如果没有违规项就设置已经检查过了，
        if(count($weigui)>0)
        {

            $model->is_weigui=implode(",", $weigui);
        }else{
            $model->is_weigui='0';
        }

        if($model->save())
        {
            return $this->redirect('checkweb?type=2');
        }else{
            var_dump($model->getErrors());exit;
        }

    }

    /**
     * Deletes an existing CherckUrl model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CherckUrl model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CherckUrl the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CherckUrl::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionHjd(){

        /*1
        self::ActionImport('ayushan.cc.xls');
        self::ActionImport('ayushan.com.cn.xls');
        self::ActionImport('ayushan.com.xls');
        self::ActionImport('ayushan.mobi.xls');
        self::ActionImport('ayushan.net.xls');
        self::ActionImport('ayushan.ren.xls');
        self::ActionImport('ayushan.wang.xls');
        self::ActionImport('eyushan.cc.xls');
        self::ActionImport('eyushan.cn.xls');
        self::ActionImport('eyushan.com.cn.xls');
        self::ActionImport('eyushan.net.xls');
        self::ActionImport('eyushan.ren.xls');
        self::ActionImport('eyushan.top.xls');
        self::ActionImport('eyushan.wang.xls');
        self::ActionImport('iyushan.cc.xls');
        self::ActionImport('myushan.com.xls');
        self::ActionImport('yushansem.com.xls');
        */

        /*
        self::ActionImport('zjzdao.cn.xls');
        self::ActionImport('zjzdao.top.xls');
        self::ActionImport('zjzdao.ren.xls');
        self::ActionImport('zhijiaozhidao.mobi.xls');
        self::ActionImport('zhijiaozhidao.wang.xls');
        self::ActionImport('zhijiaozhidao.top.xls');
        self::ActionImport('zhijiaozhidao.ren.xls');
        self::ActionImport('zhijiaozhidao.cn.xls');
        */

        /*
        self::ActionImport('zdaozhushou.cc.xls');
        self::ActionImport('zdaozhushou.cn.xls');
        self::ActionImport('zdaozhushou.com.cn.xls');
        self::ActionImport('zdaozhushou.com.xls');
        self::ActionImport('zdaozhushou.mobi.xls');
        self::ActionImport('zdaozhushou.net.xls');
        self::ActionImport('zdaozhushou.ren.xls');
        self::ActionImport('zdaozhushou.top.xls');
        self::ActionImport('zdaozhushou.wang.xls');
        self::ActionImport('zdzhushou.com.cn.xls');
        self::ActionImport('zdzhushou.net.xls');
        self::ActionImport('zdzhushou.ren.xls');
        self::ActionImport('zdzhushou.top.xls');
        self::ActionImport('zdzhushou.wang.xls');
        self::ActionImport('zdzhushou.xin.xls');
        self::ActionImport('zhidaozhushou.cc.xls');
        self::ActionImport('zhidaozhushou.cn.xls');
        self::ActionImport('zhidaozhushou.mobi.xls');
        self::ActionImport('zhidaozhushou.top.xls');
        self::ActionImport('zhidaozhushou.wang.xls');
        self::ActionImport('zhidzhushou.cc.xls');
        self::ActionImport('zhidzhushou.cn.xls');
        self::ActionImport('zhidzhushou.com.cn.xls');
        self::ActionImport('zhidzhushou.com.xls');
        self::ActionImport('zhidzhushou.mobi.xls');
        self::ActionImport('zhidzhushou.net.xls');
        self::ActionImport('zhidzhushou.ren.xls');
        self::ActionImport('zhidzhushou.top.xls');
        self::ActionImport('zhidzhushou.wang.xls');

        */
        self::ActionImport('account.xlsx');
        return 'ok';
        exit;
    }

    public function actionCheckweb($type){
        $list=CherckUrl::find()
            ->select('id')
            ->where(['is_check'=>0,'userstype'=>$type])
            ->asArray()
            ->column();

        $one=rand(0,count($list));

        $ooo=CherckUrl::find()->where(['id'=>$list[$one]])->asArray()->one();

        //已经检查过得
        $ycheck=CherckUrl::find()->where(['is_check'=>1,'userstype'=>$type])->count();



        return $this->render('checkweb',['list' => $ooo,'title'=>'check web page','type'=>$type,'count'=>count($list),'ycount'=>$ycheck]);

    }

    public function actionDone($id,$type){

        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('checkweb?type='.$type);
        } else {
           echo '错误';exit;
        }

    }

    //导入消耗
    public static function actionImport($file='')
    {

        $array=[];


        $fileName=dirname(__Dir__.'../').'/web/ty/'.$file;


        //$fileName='Uploads/3.xls';
        if($fileName=='')
        {
            return false;
        }




        $data = \moonland\phpexcel\Excel::import($fileName, [
            'setFirstRecordAsKeys' => false, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel.
            'setIndexSheetByName' => false, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric.
            //  'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
        ]);

        $urlname=substr($file,0,-4);

        //var_dump($data);

        $level=@\Yii::$app->hjd->getmaxdim($data);

        //var_dump($data);
        if($level==2)
        {
            foreach ($data as $key=>$val)
            {
                if($key!=1 and $val['B']!='' and $val['D']!='182.92.27.23' and $val['D']!='47.90.34.8' and $val['D']!='f1g1ns2.dnspod.net.' and $val['B']!='@'){


                        $new_data=new CherckUrl();
                        $new_data->url='http://'.$val['B'].'.'.$urlname;
                        $new_data->userstype=4;
                        $new_data->ip=$val['D'];
                        if($new_data->save()){

                        }else
                        {
                            var_dump($new_data->errors);exit;
                        }
                    /*
                    if(self::dataxiaohao($val['A'],$val['B'],$val['C'])==false){
                        $err[]=$val['A'].'-'.$val['B'];
                    }
                    */
                }
            }
        }
        if($level==3)
        {
            foreach ($data as $key=>$val)
            {


                foreach ($val as $k=>$v)
                {
                    if($k!=1 and $v['A']!=''){
                        //从账户列表中找到相关账户 及 二级域名 如果没有就记录到文件中

                        $one=Account::find()
                            ->where(['endtime'=>'4092599349','a_users'=>$v['A']])
                            ->with(['yuming','advertiser0'])
                            ->orderBy('id desc')
                            ->asArray()
                            ->one();
//                        if($one){
//                            $u=UserBackend::findOne($one['advertiser0']['submituser']);
//                            array_push($array,['account'=>$one['a_users'],'advertiser'=>$one['advertiser0']['advertiser'],'name'=>$u['name']]);
//                            //echo $one['a_users'].'|'.$one['advertiser0']['advertiser'].'|'.$u['name'].'<br>';
//
//                        }

                        if(count($one['yuming'])==0)
                        {
                            $logfile=dirname(__Dir__.'../').'/web/account_error.log';
                            error_log($v['A'].PHP_EOL,3,$logfile);
                        }else{
                            foreach ($one['yuming'] as $ackey=>$acvalue)
                            {
                                if($acvalue['is_ultimate_shenhe']=='1' && $acvalue['status']=='1')
                                {
                                    $new_data=new CherckUrlYuming();
                                    $new_data->url=$acvalue['true_url'];
                                    $new_data->account_name=$v['A'];
                                    $new_data->account=$one['id'];
                                    if($new_data->save()){

                                    }else
                                    {
                                        var_dump($new_data->errors);exit;
                                    }
                                }
                            }
                        }






                        /*
                        if(self::dataxiaohao($val['A'],$val['B'],$val['C'])==false){
                            $err[]=$val['A'].'-'.$val['B'];
                        }
                        */
                    }
                }


//                foreach ($val as $k=>$v){
//                    if($k!=1 and $v['B']!=''){
//
//                        if(self::dataxiaohao($v['A'],$v['B'],$v['C'])==false){
//                            $err[]=$v['A'].'-'.$v['B'];
//                        }
//                    }
//                }
            }
        }

        \moonland\phpexcel\Excel::export([
            'models'=>$array,
            'fileName'=>'数据'.date("Y-m-d"),
            'columns' => ['account','advertiser','name'], //without header working, because the header will be get label from attribute label.
            'headers' => ['account'=>'账户','advertiser'=>'公司','name'=>'销售']
        ]);

        var_dump($array);exit;
        $dataru['code']="200";
        $dataru['msg']="上传成功";
        $dataru['error_account_list']=$err;
        //删除临时文件
        @unlink($file);



        return $dataru;


    }


    //客户账期
    //导入消耗
    public static function actionZhangqi($file='')
    {

        $array=[];


        $fileName=dirname(__Dir__.'../').'/web/ty/'.$file;


        //$fileName='Uploads/3.xls';
        if($fileName=='')
        {
            return false;
        }




        $data = \moonland\phpexcel\Excel::import($fileName, [
            'setFirstRecordAsKeys' => false, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel.
            'setIndexSheetByName' => false, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric.
            //  'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
        ]);

        $urlname=substr($file,0,-4);

        //var_dump($data);

        $level=@\Yii::$app->hjd->getmaxdim($data);

        $zhangqi_date=[];
        $i=0;
        //var_dump($data);
        if($level==2)
        {

            foreach ($data as $key=>$val)
            {
                if($key!=1 and $val['B']!=''){
                    $zhangqi_date[$i]['id']=$val['A'];
                    $zhangqi_date[$i]['advertiser']=$val['B'];
                    $zhangqi_date[$i]['market']=$val['C'];
                    $zhangqi_date[$i]['jiesuanfangshi']=$val['D'];
                    $zhangqi_date[$i]['jiesuanleixing']=$val['E'];
                    $zhangqi_date[$i]['fukuanri']=$val['F'];
                    $zhangqi_date[$i]['fukuanfanwei']=$val['G'];
                    $zhangqi_date[$i]['jiesuanri']=$val['H'];
                    $zhangqi_date[$i]['linshidianfu']=$val['I'];
                    $zhangqi_date[$i]['anchongzhimeibijiesuan']=$val['J'];
                }
                $i++;
            }

        }
        if($level==3)
        {
            foreach ($data as $key=>$val)
            {


                foreach ($val as $k=>$v)
                {
                    if($k!=1 and $v['A']!=''){
                        //从账户列表中找到相关账户 及 二级域名 如果没有就记录到文件中

                        $one=Account::find()
                            ->where(['endtime'=>'4092599349','a_users'=>$v['A']])
                            ->with(['yuming','advertiser0'])
                            ->orderBy('id desc')
                            ->asArray()
                            ->one();
                        if($one){
                            $u=UserBackend::findOne($one['advertiser0']['submituser']);
                            array_push($array,['account'=>$one['a_users'],'advertiser'=>$one['advertiser0']['advertiser'],'name'=>$u['name']]);
                            //echo $one['a_users'].'|'.$one['advertiser0']['advertiser'].'|'.$u['name'].'<br>';

                        }
                        /*
                        if(count($one['yuming'])==0)
                        {
                            $logfile=dirname(__Dir__.'../').'/web/account_error.log';
                            error_log($v['A'].PHP_EOL,3,$logfile);
                        }else{
                            foreach ($one['yuming'] as $ackey=>$acvalue)
                            {
                                if($acvalue['is_ultimate_shenhe']=='1' && $acvalue['status']=='1')
                                {
                                    $new_data=new CherckUrlYuming();
                                    $new_data->url=$acvalue['true_url'];
                                    $new_data->account_name=$v['A'];
                                    $new_data->account=$one['id'];
                                    if($new_data->save()){

                                    }else
                                    {
                                        var_dump($new_data->errors);exit;
                                    }
                                }
                            }
                        }
                        */





                        /*
                        if(self::dataxiaohao($val['A'],$val['B'],$val['C'])==false){
                            $err[]=$val['A'].'-'.$val['B'];
                        }
                        */
                    }
                }


//                foreach ($val as $k=>$v){
//                    if($k!=1 and $v['B']!=''){
//
//                        if(self::dataxiaohao($v['A'],$v['B'],$v['C'])==false){
//                            $err[]=$v['A'].'-'.$v['B'];
//                        }
//                    }
//                }
            }
        }



        //处理数据
        foreach ($zhangqi_date as $key=>$val)
        {
            //获取公司下的续费
            //如果不是特殊临时垫付的
            if(empty($val['linshidianfu']))
            {
                if($val['jiesuanfangshi']=='1')
                {
                    //如果结算类型是按月结算
                    if($val['jiesuanleixing']=='1')
                    {
                        //修改合同；
                        JdContract::updateAll(['is_jstype'=>0,'pay_day'=>$val['fukuanri'],'clearing_form'=>'1','pay_scope'=>$val['fukuanfanwei'],'jiesuan_day'=>0],['advertiser'=>$val['id']]);
                    }elseif ($val['jiesuanleixing']=='2')
                    {
                        //修改合同；
                        JdContract::updateAll(['is_jstype'=>0,'clearing_form'=>'2','zhouqi'=>$val['anchongzhimeibijiesuan']],['advertiser'=>$val['id']]);

                    }

                }elseif($val['jiesuanfangshi']=='2')
                {
                    //修改合同；
                    JdContract::updateAll(['is_jstype'=>1,'pay_day'=>$val['fukuanri'],'clearing_form'=>'1','pay_scope'=>$val['fukuanfanwei'],'jiesuan_day'=>0],['advertiser'=>$val['id']]);
                }


            }else
            {
                 $list=RenewHuikuan::find()->where(['advertiser'=>$val['id'],'payment_type'=>[1,2]])->andWhere('xf_qiane > 0')->asArray()->all();
                 foreach ($list as $k =>$v)
                 {
                    $pay_day=date('Y-m-d',strtotime(date('Y-m-d',$v['payment_time'])." +".$val['linshidianfu']." day"));
                     RenewHuikuan::updateAll(['pay_day'=>$pay_day],['id'=>$v['id']]);
                 }
            }


        }


//        \moonland\phpexcel\Excel::export([
//            'models'=>$array,
//            'fileName'=>'数据'.date("Y-m-d"),
//            'columns' => ['account','advertiser','name'], //without header working, because the header will be get label from attribute label.
//            'headers' => ['account'=>'账户','advertiser'=>'公司','name'=>'销售']
//        ]);
        exit;

        $dataru['code']="200";
        $dataru['msg']="上传成功";
        $dataru['error_account_list']=$err;
        //删除临时文件
        @unlink($file);



        return $dataru;


    }

    public function actionRenewpaydate(){
        $list=JdContract::find()->where('clearing_form notnull')->asArray()->all();
        foreach ($list as $contractinfo)
        {

                $renewlist=RenewHuikuan::find()->where(['xf_contractid'=>$contractinfo['id'],'payment_type'=>[1,2]])->asArray()->all();
                foreach ($renewlist as $xfinfo) {
                    //按月结算
                    if($contractinfo['clearing_form']=='1')
                    {
                        //算出付款时间
                        //如果是按充值结算
                        $xf_m = date('Y-m-', strtotime(date('Y-m-d', $xfinfo['payment_time']))) . '01';

                        $m = $contractinfo['pay_scope'];
                        $d = $contractinfo['pay_day'];

                        //判断结算日 如果结算日大于当前日 则往后推一个月
                        $jiesuanri = $contractinfo['jiesuan_day'];

                        if ($jiesuanri > date('d', $xfinfo['payment_time'])) {
                            $m += 1;
                        }


                        if ($contractinfo['is_jstype'] == '0') {
                            $pay_day = date('Y-m-', strtotime($xf_m . "+$m month"));
                        } else {
                            //如果是按消耗结算
                            $m += 1;

                            $pay_day = date('Y-m-', strtotime($xf_m . "+$m month"));
                        }

                        $xf_pay_day = $pay_day . $d;
                    }elseif($contractinfo['clearing_form']=='2')
                    {
                        $zhouqi=$contractinfo['zhouqi'];
                        $xf_pay_day=date('Y-m-d',strtotime(date('Y-m-d',$xfinfo['payment_time'])."+$zhouqi day"));
                    }

                    //如果付款日期不存在 例如2月31日 这样 就默认还款日为月最后一天
                    $xf_pay_day=Yii::$app->hjd->date_is_true($xf_pay_day);

                    RenewHuikuan::updateAll(['pay_day'=>$xf_pay_day],['id'=>$xfinfo['id']]);
                }

        }

    }


    public function actionUpyufu(){
        $renewlist=RenewHuikuan::find()->where(['payment_type'=>[1]])->asArray()->all();
        foreach ($renewlist as $xfinfo) {


            //如果付款日期不存在 例如2月31日 这样 就默认还款日为月最后一天
            $xf_pay_day=date('Y-m-d',$xfinfo['payment_time']);

            RenewHuikuan::updateAll(['pay_day'=>$xf_pay_day],['id'=>$xfinfo['id']]);
        }
    }


}
