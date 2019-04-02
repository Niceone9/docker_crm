<?php
/**
 * Created by PhpStorm.
 * User: houjiandong
 * Date: 2017/10/12
 * Time: 14:57
 */

return [
    'getAuth'=>'api/user/get_auth',//判断用户是否有权限操作Action
    'kebao_is_conflict'=>'api/customerb/is_conflict',
    'h'=>'api/user/h',//判断用户是否有权限操作Action
    'POST login'=>'api/public/login',//登录操作

    'market_kebao_list/<id:\d+>'=>'api/customerb/market_kebao_list',//客保列表
    'market_kehuchi_list'=>'api/customerb/market_kebao_list',//客户池列表
    'customer_list'=>'api/customer/customer_list',//客户列表
    'customer_addru'=>'api/customer/add',//新增客户
    'kebao_renling/<id:\d+>/<usersid:\d+>'=>'api/customerb/renling',//认领客户
    'audit'=>'api/audit/shenhe',// 审核
    'Roler_users'=>'api/public/roler_users',//根据角色获取用户
    'mcustomer_info/<id:\d+>'=>'api/customer/minfo',//客户详情

    'upusersinfo/<uid:\d+>'=>'api/user/upusersinfo',//修改个人信息
    'uppassword/<uid:\d+>'=>'api/user/uppassword',//修改密码
    'uptouxiang/<uid:\d+>'=>'api/user/uptouxiang',//修改头像
    'addusers'=>'api/user/addusers',//新增用户
    'userslist'=>'api/user/userslist',//用户列表
    'usersinfo/<id:\d+>'=>'api/user/usersinfo',//用户信息

    'custmober_contact/<id:\d+>'=>'api/contact/custmober_contact',//联系人列表

    'customer_contract_list/<id>'=>'api/contract/contractlist',//获取合同列表
    'Upmarket/<custmoerarray>/<users:\d+>/<type>'=>'api/customer/upcustomermarket',//分配销售或者商务
    'contract_guidang/<id:\d+>'=>'api/contract/guidang',//合同归档
    'contract_zuofei/<id:\d+>'=>'api/contract/zuofei',//合同作废
    'contract_jieshu/<id:\d+>'=>'api/contract/jieshu',//合同结束
    'contract_yanqi/<id:\d+>/<date>'=>'api/contract/yanqi',//合同结束
    'contract_info/<id:\d+>'=>'api/contract/contract_info',//合同详情
    'contract_shenhe1/<id:\d+>'=>'api/contract/shenhe1',//合同审核
    'contract_shenhe2/<id:\d+>'=>'api/contract/shenhe2',//合同审核
    'contract_shenhe3/<id:\d+>'=>'api/contract/shenhe3',//合同审核
    'contract_shenhe4/<id:\d+>'=>'api/contract/shenhe4',//合同审核
    'contract_shenhe5/<id:\d+>'=>'api/contract/shenhe5',//合同审核
    'contract_add/<id:\d+>'=>'api/contract/add-contract',//新增合同
    'contract_addru'=>'api/contract/addru',//新增合同返回
    //'contract_num'=>'api/contract/contract_num',//检查合同 数量
    'contract_account/<id:\d+>'=>'api/contract/contract_account',//合同下账户 数量
    'contract_history/<contract_id:\d+>'=>'api/contract/history',//历史记录
    'contract_meijie_list'=>'api/contract/contract_meijie_list',//媒介合同
    'copy_account/<contract_id:\d+>'=>'api/contract/copy_account',//复制账户

    'productmcontract/<prid:\d+>'=>'api/productline/productmcontract',//根据产品线获取媒介合同列表


    'renew_list'=>'api/renew-huikuan/indexlist',//续费列表
    'renew_list_caiwu'=>'api/renew-huikuan/indexlist_caiwu',//续费列表
    'renew_list_meijie'=>'api/renew-huikuan/indexlist_meijie',//续费列表
    'add_pici/<contract_id:\d+>'=>'api/renew-huikuan/addrupici',//新增续费
    'add_renew/<contract_id:\d+>'=>'api/renew-huikuan/add',//新增续费
    'add_picifile/<xf_id:\d+>'=>'api/renew-huikuan/picifile',//批次新增图片
    'renew_to_huikuan/<renew_id:\d+>'=>'api/renew-huikuan/renew_to_huikuan',//续费对应的回款

    'add_huikuan/<id:\d+>'=>'api/back-money/add',//新增回款
    'huikuan_list'=>'api/back-money/list',//回款列表
    'addhuikuanru'=>'api/back-money/addru',//新增回款返回
    'back_money_info/<id:\d+>'=>'api/back-money/back_money_info',//回款详情
    'back_money_shenhe1/<id:\d+>'=>'api/back-money/shenhe1',//回款一级审核
    'back_money_shenhe2/<id:\d+>'=>'api/back-money/shenhe2',//回款一级审核
    'back_money_shenhe3/<id:\d+>'=>'api/back-money/shenhe3',//回款一级审核
    'back_money_shenhe4/<id:\d+>'=>'api/back-money/shenhe4',//回款一级审核
    'back_money_shenhe5/<id:\d+>'=>'api/back-money/shenhe5',//回款一级审核

    'fphuikuan/<id:\d+>'=>'api/back-money/fphuikuan',//分配回款页面
    'fphuikuanru'=>'api/back-money/fphuikuanru',//分配回款返回
    'dshenhehk/<id:\d+>'=>'api/back-money/dshenhebackmoney',//待审核回款总额
    'dshenhebk/<id:\d+>'=>'api/back-money/dshenhebumoney',//待审核补款总额

    'refund_account_list'=>'api/refund-money/list_zongzhnaghu',//退款到总账户列表
    'refund_kehu_list'=>'api/refund-money/list_kehu',//退款到客户列表
    'add_refundmoney/<contract_id:\d+>'=>'api/refund-money/add',//新增退款
    'add_refundmoney_ru'=>'api/refund-money/addru',//新增退款返回
    'refundmoney_info/<id:\d+>'=>'api/refund-money/refundmoney_info',//退款详情
    'refundmoney_kehu_shenhe1/<id:\d+>'=>'api/refund-money/shenhe1_tuikuan_kehu',//退款到客户1审
    'refundmoney_kehu_shenhe2/<id:\d+>'=>'api/refund-money/shenhe2_tuikuan_kehu',//退款到客户2审
    'refundmoney_kehu_shenhe3/<id:\d+>'=>'api/refund-money/shenhe3_tuikuan_kehu',//退款到客户1审
    'refundmoney_kehu_shenhe4/<id:\d+>'=>'api/refund-money/shenhe4_tuikuan_kehu',//退款到客户2审
    'refundmoney_kehu_shenhe5/<id:\d+>'=>'api/refund-money/shenhe5_tuikuan_kehu',//退款到客户2审

    'refundmoney_account_p/<id:\d+>'=>'api/refund-money/shenhe1_tuikuan_account_p',//退款到总账户拼框套壳
    'refundmoney_account_p1/<id:\d+>'=>'api/refund-money/shenhe1_tuikuan_account_p',//退款到总账户拼框套壳
    'refundmoney_account_p2/<id:\d+>'=>'api/refund-money/shenhe2_tuikuan_account_p',//退款到总账户拼框套壳
    'refundmoney_account_p3/<id:\d+>'=>'api/refund-money/shenhe3_tuikuan_account_p',//退款到总账户拼框套壳
    'refundmoney_account_p4/<id:\d+>'=>'api/refund-money/shenhe4_tuikuan_account_p',//退款到总账户拼框套壳
    'refundmoney_account_p5/<id:\d+>'=>'api/refund-money/shenhe5_tuikuan_account_p',//退款到总账户拼框套壳

    'refundmoney_account_z_shenhe1/<id:\d+>'=>'api/refund-money/shenhe1_tuikuan_account_z',//退款到总账户直开1审
    'refundmoney_account_z_shenhe2/<id:\d+>'=>'api/refund-money/shenhe2_tuikuan_account_z',//退款到总账户直开2审
    'refundmoney_account_z_shenhe3/<id:\d+>'=>'api/refund-money/shenhe3_tuikuan_account_z',//退款到总账户直开1审
    'refundmoney_account_z_shenhe4/<id:\d+>'=>'api/refund-money/shenhe4_tuikuan_account_z',//退款到总账户直开2审
    'refundmoney_account_z_shenhe5/<id:\d+>'=>'api/refund-money/shenhe5_tuikuan_account_z',//退款到总账户直开2审


    'bukuan_list'=>'api/bukuan/list',//补款列表
    'add_bukuan/<contract_id:\d+>'=>'api/bukuan/add',//新增补款页面
    'add_bukuan_ru'=>'api/bukuan/addru',//添加返回
    'bukuan_info/<id:\d+>'=>'api/bukuan/bukuan_info',//补款详情
    'bukuan_shenhe1/<id:\d+>'=>'api/bukuan/shenhe1',//补款审核
    'bukuan_shenhe2/<id:\d+>'=>'api/bukuan/shenhe2',//补款审核
    'bukuan_shenhe3/<id:\d+>'=>'api/bukuan/shenhe3',//补款审核
    'bukuan_shenhe4/<id:\d+>'=>'api/bukuan/shenhe4',//补款审核
    'bukuan_shenhe5/<id:\d+>'=>'api/bukuan/shenhe5',//补款审核

    'zhuankuan/<contract_id:\d+>'=>'api/contract/zhuankuan',//转款
    'zhuankuanru'=>'api/contract/zhuankuanru',//转款返回

    'invoice_list'=>'api/invoice/list',//发票列表
    'invoice_add/<id:\d+>'=>'api/invoice/add',//发票新增
    'invoice_type/<agentcompany_id:\d+>'=>'api/invoice/fptype',//发票新增
    'invoice_addru'=>'api/invoice/addru',//发票新增返回
    'invoice_info/<id:\d+>'=>'api/invoice/invoice_info',//发票详情
    'invoice_update/<id:\d+>'=>'api/invoice/upinvoice',//修改发票
    'invoice_update_ru/<id:\d+>'=>'api/invoice/upinvoice_ru',//修改发票返回
    'invoice_shenhe1/<id:\d+>'=>'api/invoice/shenhe1',//发票审核1
    'invoice_shenhe2/<id:\d+>'=>'api/invoice/shenhe2',//发票审核2
    'invoice_shenhe3/<id:\d+>'=>'api/invoice/shenhe3',//发票审核3
    'invoice_shenhe4/<id:\d+>'=>'api/invoice/shenhe4',//发票审核4
    'invoice_shenhe5/<id:\d+>'=>'api/invoice/shenhe5',//发票审核5

    'audit_history/<id:\d+>/<table>'=>'api/audit/shehe_history',//审核历史记录
    'audit_users'=>'api/audit/shenhe_users',//查询审核人，


    'account_list/<id>'=>'api/account/account_list',//账户列表
    'account_info/<id:\d+>'=>'api/account/account_info',//账户详细信息
    'account_add/<contract_id:\d+>'=>'api/account/account_add',//账户添加
    'account_add_pi_ru'=>'api/account/account_add_pi',//账户添加返回
    'account_add_ru'=>'api/account/account_addru',//账户添加返回
    'account_updataru/<id:\d+>'=>'api/account/updateaccount',//修改账户
    'checkaccount_add/<a_users>'=>'api/account/checkaccount_add',//检查账户信息
    'checkaccount_up/<a_users>/<thisid>'=>'api/account/checkaccount_up',//检查账户信息
    'account_tags_list'=>'api/account/tags_list',//账户标签列表
    'upaccount_tags/<id:\d+>'=>'api/account/upaccount_tags',//修改账户标签
    'account_history/<id>'=>'api/account/account_history',//账户列表


    'yihuikuanxufei_list'=>'api/yihuikuanxufei/list',//已回款续费列表
    'upgerenfandian/<id:\d+>/<fandian>'=>'api/yihuikuanxufei/upgerenfandian',//已回款续费列表修改个人返点



    'qddakuan_list'=>'api/qd-dakuan/list',//给渠道打款列表
    'add_qddakuan/<contract_id:\d+>'=>'api/qd-dakuan/add',//新增给渠道打款页面
    'add_qddakuan_ru'=>'api/qd-dakuan/addru',//添加返回
    'qddakuan_info/<id:\d+>'=>'api/qd-dakuan/qd_dakuan_info',//给渠道打款详情
    'qddakuan_shenhe1/<id:\d+>'=>'api/qd-dakuan/shenhe1',//给渠道打款审核
    'qddakuan_shenhe2/<id:\d+>'=>'api/qd-dakuan/shenhe2',//给渠道打款审核
    'qddakuan_shenhe3/<id:\d+>'=>'api/qd-dakuan/shenhe3',//给渠道打款审核
    'qddakuan_shenhe4/<id:\d+>'=>'api/qd-dakuan/shenhe4',//给渠道打款审核
    'qddakuan_shenhe5/<id:\d+>'=>'api/qd-dakuan/shenhe5',//给渠道打款审核

    'prlin_list'=>'api/productlinemin/prlinlist',//产品线列表
    'prlin_addru'=>'api/productlinemin/addru',//新增产品线返回
    'prlin_info/<id:\d+>'=>'api/productlinemin/pr_info',//产品线详情
    'prlin_up/<id:\d+>'=>'api/productlinemin/upprlin',//产品线修改
    'prlin_delete/<id:\d+>'=>'api/productlinemin/deletea',//删除产品线

    'agent_company_list'=>'api/agent-company/daililist',//代理公司列表
    'agent_company_addru'=>'api/agent-company/addru',//新增代理公司返回
    'agent_company_info/<id:\d+>'=>'api/agent-company/daili_info',//代理公司详情
    'agent_company_up/<id:\d+>'=>'api/agent-company/upprdaili',//代理公司修改
    'agent_company_delete/<id:\d+>'=>'api/agent-company/deletea',//代理公司删除

    'piaotype_list'=>'api/piaotype/piaotypelist',//开票类型列表
    'piaotype_add'=>'api/piaotype/add',//新增开票类型
    'piaotype_addru'=>'api/piaotype/addru',//新增开票类型
    'piaotype_info/<id:\d+>'=>'api/piaotype/piaotype_info',//开票类型详情
    'piaotype_up/<id:\d+>'=>'api/piaotype/uppiaotype',//开票类型修改
    'piaotype_delete/<id:\d+>'=>'api/piaotype/deletea',//删除开票类型


    'weihuikuanyuqi'=>'api/renew-huikuan/weihuikuanyuqi',//未回款逾期
    'weihuikuanyuqicu'=>'api/renew-huikuan/weihuikuanyuqicu',//未回款逾期
    'yihuikuanyuqi'=>'api/renew-huikuan/yihuikuanyuqi',//已回款逾期



    /*
     * 媒介功能伊始
     *
     * */
    'meijie_customer_list'=>'api/meijie-customer/customer_list',//客户列表
    'meijie_customer_addru'=>'api/meijie-customer/add',//新增客户

    'meijie_customer_contract_list/<id>'=>'api/meijie-contract/contractlist',//获取合同列表
    'meijie_contract_guidang/<id:\d+>'=>'api/meijie-contract/guidang',//合同归档
    'meijie_contract_zuofei/<id:\d+>'=>'api/meijie-contract/zuofei',//合同作废
    'meijie_contract_jieshu/<id:\d+>'=>'api/meijie-contract/jieshu',//合同结束
    'meijie_contract_info/<id:\d+>'=>'api/meijie-contract/contract_info',//合同详情
    'meijie_contract_add/<id:\d+>'=>'api/meijie-contract/add-contract',//新增合同
    'meijie_contract_addru'=>'api/meijie-contract/addru',//新增合同返回
    'meijie_contract_num'=>'api/meijie-contract/contract_num',//检查合同 数量
    'up_meijie_markey_fandian/<id:\d+>/<fandian>'=>'api/renew-huikuan/up_meijie_markey_fandian',
    'meijie_contract_shenhe1/<id:\d+>'=>'api/meijie-contract/shenhe1',//合同审核
    'meijie_contract_shenhe2/<id:\d+>'=>'api/meijie-contract/shenhe2',//合同审核
    'meijie_contract_shenhe3/<id:\d+>'=>'api/meijie-contract/shenhe3',//合同审核
    'meijie_contract_shenhe4/<id:\d+>'=>'api/meijie-contract/shenhe4',//合同审核
    'meijie_contract_shenhe5/<id:\d+>'=>'api/meijie-contract/shenhe5',//合同审核

    'meijie_zhuankuan/<contract_id:\d+>'=>'api/meijie-contract/zhuankuan',//转款
    'meijie_zhuankuanru'=>'api/meijie-contract/zhuankuanru',//转款返回

    'meijie_contract_history/<contract_id:\d+>'=>'api/meijie-contract/history',//历史记录


    'meijie_tuikuan_list'=>'api/meijie-refund-money/list',//媒介退款列表
    'meijie_add_refundmoney/<contract_id:\d+>'=>'api/meijie-refund-money/add',//新增退款
    'meijie_add_refundmoney_ru'=>'api/meijie-refund-money/addru',//新增退款
    'meijie_tuikuan_shenhe1/<id:\d+>'=>'api/meijie-refund-money/shenhe1',//m媒介退款审核
    'meijie_tuikuan_shenhe2/<id:\d+>'=>'api/meijie-refund-money/shenhe2',//m媒介退款审核
    'meijie_tuikuan_shenhe3/<id:\d+>'=>'api/meijie-refund-money/shenhe3',//m媒介退款审核
    'meijie_tuikuan_shenhe4/<id:\d+>'=>'api/meijie-refund-money/shenhe4',//m媒介退款审核
    'meijie_tuikuan_shenhe5/<id:\d+>'=>'api/meijie-refund-money/shenhe5',//m媒介退款审核
    'meijie_tuikuan_info/<id:\d+>'=>'api/meijie-refund-money/refundmoney_info',//退款详情

    'meijie_dakuan_add/<id:\d+>'=>'api/mback-money/add',//新增媒介打款
    'mcont_renew_list'=>'api/mback-money/mrenew_list',//媒介合同下续费列表
    'mcont_tuikuan_list'=>'api/mback-money/mtuikuan_list',//媒介合同下的退款列表
    'meijie_dakuan_add_ru'=>'api/mback-money/addru',//新增媒介打款返回
    'meijie_dakuan_list'=>'api/mback-money/list', //媒介打款列表
    'meijie_dakuan_info/<id:\d+>'=>'api/mback-money/dkinfo',//媒介打款详情
    'meijie_dakuan_shenhe1/<id:\d+>'=>'api/mback-money/shenhe1',//打款审核1
    'meijie_dakuan_shenhe2/<id:\d+>'=>'api/mback-money/shenhe2',//打款审核2
    'meijie_dakuan_shenhe3/<id:\d+>'=>'api/mback-money/shenhe3',//打款审核1
    'meijie_dakuan_shenhe4/<id:\d+>'=>'api/mback-money/shenhe4',//打款审核2
    'meijie_dakuan_shenhe5/<id:\d+>'=>'api/mback-money/shenhe5',//打款审核2




    'fpdakuan/<id:\d+>'=>'api/mback-money/fpdakuan',//分配回款页面
    'fpdakuanru'=>'api/mback-money/fpdakuanru',//分配回款返回

    'meijie_bukuan_list'=>'api/meijie-bukuan/list',//补款列表
    'meijie_add_bukuan/<contract_id:\d+>'=>'api/meijie-bukuan/add',//新增补款页面
    'meijie_add_bukuan_ru'=>'api/meijie-bukuan/addru',//添加返回
    'meijie_bukuan_info/<id:\d+>'=>'api/meijie-bukuan/bukuan_info',//补款详情
    'meijie_bukuan_shenhe1/<id:\d+>'=>'api/meijie-bukuan/shenhe1',//补款审核
    'meijie_bukuan_shenhe2/<id:\d+>'=>'api/meijie-bukuan/shenhe2',//补款审核
    'meijie_bukuan_shenhe3/<id:\d+>'=>'api/meijie-bukuan/shenhe3',//补款审核
    'meijie_bukuan_shenhe4/<id:\d+>'=>'api/meijie-bukuan/shenhe4',//补款审核
    'meijie_bukuan_shenhe5/<id:\d+>'=>'api/meijie-bukuan/shenhe5',//补款审核

    'meijie_add_huikuan/<id:\d+>'=>'api/meijie-huikuan/add',//新增回款
    'meijie_huikuan_list'=>'api/meijie-huikuan/list',//回款列表
    'meijie_addhuikuanru'=>'api/meijie-huikuan/addru',//新增回款返回
    'meijie_back_money_info/<id:\d+>'=>'api/meijie-huikuan/back_money_info',//回款详情
    'meijie_huikuan_shenhe1/<id:\d+>'=>'api/meijie-huikuan/shenhe1',//媒介回款审核
    'meijie_huikuan_shenhe2/<id:\d+>'=>'api/meijie-huikuan/shenhe2',//媒介回款审核
    'meijie_huikuan_shenhe3/<id:\d+>'=>'api/meijie-huikuan/shenhe3',//媒介回款审核
    'meijie_huikuan_shenhe4/<id:\d+>'=>'api/meijie-huikuan/shenhe4',//媒介回款审核
    'meijie_huikuan_shenhe5/<id:\d+>'=>'api/meijie-huikuan/shenhe5',//媒介回款审核
    'meijie_dshenhehk/<id:\d+>'=>'api/meijie-huikuan/dshenhebackmoney',//待审核回款总额
    'meijie_dshenhedk/<id:\d+>'=>'api/meijie-huikuan/dshenhedamoney',//待审核打款总额

    'meijie_renew_list'=>'api/meijie-renew-huikuan/indexlist',//媒介续费列表
    'meijie_renew_info/<id:\d+>'=>'api/meijie-renew-huikuan/renew-info',//媒介续费列表


    //媒介打保证金功能 2018年3月14日14:48:10
    'meijie_margin_add/<id:\d+>'=>'api/meijie-margin/add',//新增媒介打款
    'mcont_margin_list'=>'api/meijie-margin/mrenew_list',//媒介合同下续费列表
    'meijie_margin_add_ru'=>'api/meijie-margin/addru',//新增媒介打款返回
    'meijie_margin_list'=>'api/meijie-margin/list', //媒介打款列表
    'meijie_margin_info/<id:\d+>'=>'api/meijie-margin/dkinfo',//媒介打款详情
    'meijie_margin_shenhe1/<id:\d+>'=>'api/meijie-margin/shenhe1',//打款审核1
    'meijie_margin_shenhe2/<id:\d+>'=>'api/meijie-margin/shenhe2',//打款审核2
    'meijie_margin_shenhe3/<id:\d+>'=>'api/meijie-margin/shenhe3',//打款审核1
    'meijie_margin_shenhe4/<id:\d+>'=>'api/meijie-margin/shenhe4',//打款审核2
    'meijie_margin_shenhe5/<id:\d+>'=>'api/meijie-margin/shenhe5',//打款审核2



    //媒介保证金列表
    'meijie_margin_da_list'=>'api/meijie-margin/da_list', //客户申请的打保证金列表

    'tuimargin/<id:\d+>'=>'api/meijie-margin/tuimargin',//申请退保证金
    'meijie_margin_tui_list'=>'api/meijie-margin/tui_list', //退保证金列表
    'meijie_margin_tui_info/<id:\d+>'=>'api/meijie-margin/tui_info', //退保证金详情
    'meijie_margin_tui_shenhe1/<id:\d+>'=>'api/meijie-margin/shenhetui1',//打款审核1
    'meijie_margin_tui_shenhe2/<id:\d+>'=>'api/meijie-margin/shenhetui2',//打款审核2
    'meijie_margin_tui_shenhe3/<id:\d+>'=>'api/meijie-margin/shenhetui3',//打款审核1
    'meijie_margin_tui_shenhe4/<id:\d+>'=>'api/meijie-margin/shenhetui4',//打款审核2
    'meijie_margin_tui_shenhe5/<id:\d+>'=>'api/meijie-margin/shenhetui5',//打款审核2


    //外出
    'waichu_list'=>'api/waichu/list',//外出列表
    'waichu_add/<id:\d+>'=>'api/waichu/add',//发票外出
    'waichu_addru'=>'api/waichu/addru',//外出新增返回
    'waichu_info/<id:\d+>'=>'api/waichu/waichu_info',//外出详情
    'waichu_shenhe1/<id:\d+>'=>'api/waichu/shenhe1',//外出审核1
    'waichu_shenhe2/<id:\d+>'=>'api/waichu/shenhe2',//外出审核2
    'waichu_shenhe3/<id:\d+>'=>'api/waichu/shenhe3',//外出审核1
    'waichu_shenhe4/<id:\d+>'=>'api/waichu/shenhe4',//外出审核2
    'waichu_shenhe5/<id:\d+>'=>'api/waichu/shenhe5',//外出审核2
    //请假
    'qingjia_list'=>'api/qingjia/list',//外出列表
    'qingjia_add/<id:\d+>'=>'api/qingjia/add',//发票外出
    'qingjia_addru'=>'api/qingjia/addru',//外出新增返回
    'qingjia_info/<id:\d+>'=>'api/qingjia/qingjia_info',//外出详情
    'qingjia_shenhe1/<id:\d+>'=>'api/qingjia/shenhe1',//外出审核1
    'qingjia_shenhe2/<id:\d+>'=>'api/qingjia/shenhe2',//外出审核2
    'qingjia_shenhe3/<id:\d+>'=>'api/qingjia/shenhe3',//外出审核2
    'qingjia_shenhe4/<id:\d+>'=>'api/qingjia/shenhe4',//外出审核2
    'qingjia_shenhe5/<id:\d+>'=>'api/qingjia/shenhe5',//外出审核2


    //获取图片
    'get_files/<yid:\d+>/<type:\d+>'=>'api/file/show_files',
    'delete_file/<id\d+>'=>'api/file/delete_file',//删除文件


    //销售评分
    'market_score_list'=>'api/market-score/list',

    //部门
    'bumenlist'=>'api/department/bumenlist',
    'bumenusers/<id:\d+>'=>'api/department/bumenusers', //根据部门ID获取用户
    'add_bumen'=>'api/department/addru',//添加返回
    'delete_bumen/<id:\d+>'=>'api/department/deletebumen',//删除部门
    'up_bumen/<id:\d+>'=>'api/department/upbumen',//修改部门

    'get_roles'=>'api/user/roles',//获取角色
    'users_to_bumen'=>'api/user/userss_to_bumen',//给用户分配部门
    'user_delete/<id:\d+>'=>'api/user/user_delete',//删除用户

    //羽扇
    'account_to_market'=>'api/linux-time/account_to_market',//羽扇同步


    //合同管理 2017年12月15日 （）
    'customer_contract_list_new/<id>'=>'api/contract-new/contractlist',//获取合同列表

    'contract_info_new/<id:\d+>'=>'api/contract-new/contract_info',//合同详情
    'contract_new_shenhe1/<id:\d+>'=>'api/contract-new/shenhe1',//合同审核
    'contract_new_shenhe2/<id:\d+>'=>'api/contract-new/shenhe2',//合同审核
    'contract_new_shenhe3/<id:\d+>'=>'api/contract-new/shenhe3',//合同审核
    'contract_new_shenhe4/<id:\d+>'=>'api/contract-new/shenhe4',//合同审核
    'contract_new_shenhe5/<id:\d+>'=>'api/contract-new/shenhe5',//合同审核
    'contract_add_new/<id:\d+>'=>'api/contract-new/add-contract',//新增合同
    'contract_addru_new'=>'api/contract-new/addru',//新增合同返回
    'contract_num'=>'api/contract-new/contract_num',//检查合同 数量
    'contract_zuofei_new/<id:\d+>'=>'api/contract-new/zuofei',//合同作废
    'contract_guidang_new/<id:\d+>'=>'api/contract-new/guidang',//合同归档
    'advertiser_to_product/<adid:\d+>'=>'api/contract-new/advertiser_to_product',//根据广告主id获取产品
    'account_to_product/<id:\d+>'=>'api/customer-advertiser/accounttoadpr',//根据账户id获取广告主和产品
    'contract_no_num/<no>'=>'api/contract-new/contract_no_num',//查看合同编号数量


    //公司下的广告主
    'customer_advertiser_list/<customer_id:\d+>'=>'api/customer-advertiser/customer_list', //公司下广告主列表
    'customer_advertiser_addru'=>'api/customer-advertiser/add',//添加公司下广告主返回
    'customer_advertiser_info/<id:\d+>'=>'api/customer-advertiser/info',//广告主详情
    'customer_advertiser_upadvertiser/<id:\d+>'=>'api/customer-advertiser/upadvertiser',//修改广告主信息


    //保证金
    'baozhengjin_list'=>'api/baozhengjin/list',//外出列表
    'baozhengjin_info/<id:\d+>'=>'api/baozhengjin/baozhengjin_info',//外出详情
    'baozhengjin_shenhe1/<id:\d+>'=>'api/baozhengjin/shenhe1',//外出审核1
    'baozhengjin_shenhe2/<id:\d+>'=>'api/baozhengjin/shenhe2',//外出审核2
    'baozhengjin_shenhe3/<id:\d+>'=>'api/baozhengjin/shenhe3',//外出审核1
    'baozhengjin_shenhe4/<id:\d+>'=>'api/baozhengjin/shenhe4',//外出审核2
    'baozhengjin_shenhe5/<id:\d+>'=>'api/baozhengjin/shenhe5',//外出审核2

    //保证金
    'kehubukuan_list'=>'api/kehubukuan/list',//外出列表
    'kehubukuan_info/<id:\d+>'=>'api/kehubukuan/kehubukuan_info',//外出详情
    'kehubukuan_shenhe1/<id:\d+>'=>'api/kehubukuan/shenhe1',//外出审核1
    'kehubukuan_shenhe2/<id:\d+>'=>'api/kehubukuan/shenhe2',//外出审核2
    'kehubukuan_shenhe3/<id:\d+>'=>'api/kehubukuan/shenhe3',//外出审核1
    'kehubukuan_shenhe4/<id:\d+>'=>'api/kehubukuan/shenhe4',//外出审核2
    'kehubukuan_shenhe5/<id:\d+>'=>'api/kehubukuan/shenhe5',//外出审核2

    //转款
    'add_zhuankuan/<contract_id:\d+>'=>'/api/zhuankuan/add',//新增转款
    'contract_to_account/<contract_id:\d+>'=>'api/zhuankuan/contracttoaccount',//根据合同返回账户列表
    'add_zhuankuan_ru'=>'/api/zhuankuan/addru',//新增转款返回
    'zhuankuan_info/<id:\d+>'=>'api/zhuankuan/zhuankuan_info',//转款详情
    'zhuankuan_list'=>'api/zhuankuan/list',//转款列表
    'zhuankuan_shenhe1/<id:\d+>'=>'api/zhuankuan/shenhe1',//转款审核1
    'zhuankuan_shenhe2/<id:\d+>'=>'api/zhuankuan/shenhe2',//转款审核2
    'zhuankuan_shenhe3/<id:\d+>'=>'api/zhuankuan/shenhe3',//转款审核1
    'zhuankuan_shenhe4/<id:\d+>'=>'api/zhuankuan/shenhe4',//转款审核2
    'zhuankuan_shenhe5/<id:\d+>'=>'api/zhuankuan/shenhe5',//转款审核2
    //待办uuuuu
    'to-do'=>'api/index/daiban',


        /*
         *
         * TV统计
         *
         * */
    //统计 电视墙功能
    'renewRank/<number:\d+>/<start>/<end>/<ourMain>'=>'tongji/renew/renew_rank',//客户消费排名 参[数量/开始时间/结束时间/主体公司]
    'renewTrend/<month:\d+>/<ourMain>'=>'tongji/renew/renew_trend',//月趋势图  参[读取几月/主体]

    'profits/<number:\d+>/<start>/<end>/<ourMain>'=>'tongji/renew/renew_profits',//客户利润排名 参[数量/开始时间/结束时间/主体公司]-实际
    'profitsTrend/<month:\d+>/<ourMain>'=>'tongji/renew/lirun_trend',//利润续费月趋势图  参[读取几月/主体]-实际

    'trueProfits/<number:\d+>/<start>/<end>/<ourMain>'=>'tongji/renew/renew_true_profits', //客户已回款利润  -已回款
    'profitsTrueTrend/<month:\d+>/<ourMain>'=>'tongji/renew/lirun_true_trend',//利润已回款月趋势图  参[读取几月/主体]-已回款
    'customerList'=>'tongji/customer/customerlist',//客户列表
    'customer_prlin/<id:\d+>'=>'tongji/customer/customer_prlin',//获取公司的产品线
    'mediaProductStatistical/<start>/<end>'=>'tongji/mrenew/media_bing',//利润饼图图  参[开始/结束 时间]'
    'mediaRunning/<month:\d+>'=>'tongji/mrenew/media_running',//利润已回款月趋势图  参[读取几月/主体]'
    'contractAddRank/<marketid>/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/customer/contract_add_rank',//新增合同排名
    'marketProfitsRank/<marketid>/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/customer/market_maoli_rank',//销售毛利排名
    'contractAddList/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/customer/contract_add_list',//新增合同列表
    'contractMarketAddList/<id:\d+>/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/customer/contart_add_markey_list',//新增合同列表详情
    'marketProfitsList/<id:\d+>/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/customer/market_maoli_list',//销售毛利列表


    'contract_date_sum/<state_time>/<end_time>'=>'tongji/contract/contract_date_sum',//根据时间返回期间新建的合同数量
    'xiaohao_date_sum/<state_time>/<end_time>'=>'tongji/xiaohao/xiaohao_date_sum',//根据时间返回消耗
    'money_huikuan_sum/<state_time>/<end_time>'=>'tongji/xiaohao/money_huikuan_sum',//返回金额统计-回款
    'money_fukuan_sum/<state_time>/<end_time>'=>'tongji/xiaohao/money_fukuan_sum',//返回金额统计-付款
    'money_diankuan_sum/<state_time>/<end_time>'=>'tongji/xiaohao/money_diankuan_sum',//返回金额统计-垫款
    'money_bukuan_sum/<state_time>/<end_time>'=>'tongji/xiaohao/money_bukuan_sum',//返回金额统计-垫款
    'Find_market_week_counsumption_statistics/<datetype>'=>'tongji/xiaohao/find_market_week_counsumption_statistics',//查看 日 周 月  消耗

    'contract_date_list/<state_time>/<end_time>'=>'tongji/contract/contract_date_list',//根据时间返回期间新建的合同列表
    'xiaohao_date_list/<state_time>/<end_time>'=>'tongji/xiaohao/xiaohao_date_list',//根据时间返回消耗列表
    'diankuan_compare'=>'tongji/xiaohao/diankuan_compare',//垫款列表
    'dakuan_huikuan_tu/<yyyy>'=>'tongji/xiaohao/dakuanhuikuantu', //统计打款回款折线图
    'xiaohaotj/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/xiaohao/xiaohaotj',//统计消耗 实际金额折线图
    'xiaohaotjprlin/<date:\d{4}-\d{2}-\d{2}>'=>'tongji/xiaohao/xiaohaotjprlin',//根据时间统计各个产品线消耗和金额
    'xiaohaorank'=>'/tongji/xiaohao/xiaohaozhou',//消耗排名
    'money_xh_rank'=>'/tongji/xiaohao/xiaohaozhou_smoney',//消耗对应的金额排名
    'productlinrenewtj/<start>/<end>'=>'/tongji/xiaohao/productlinrenewtj',//按产品线统计续费
    'productlinadtj/<start>/<end>'=>'/tongji/xiaohao/productlinadtj',//按客户续费统计
    'money_kehubukuan_sum/<state_time>/<end_time>'=>'tongji/xiaohao/money_kehubukuan_sum',//返回金额统计-客户补款

    'xufeirank'=>'/tongji/xiaohao/xufeizhou',//续费排名
    'money_xufei_rank'=>'/tongji/xiaohao/xufei_money_zhou',//续费对应的金额排名





    'market_ticheng/<start>/<end>/<uid>/<type>'=>'api/renew-huikuan/market_ticheng', //销售提成
    'market_ticheng_all/<start>/<end>'=>'api/renew-huikuan/market_ticheng_m', //销售提成
    'market_ticheng_adstate/<start>/<end>/<uid>'=>'api/renew-huikuan/market_ticheng_adstate', //销售提成
    'market_ad_ticheng/<start>/<end>/<uid>'=>'api/renew-huikuan/market_ad_ticheng', //销售提成 按客户
    'market_new_customer_cn/<uid>/<year>'=>'api/renew-huikuan/market_new_customer_cn',//销售新增客户数量折现
    'market_lirun_money_zhexian/<year>/<uid>'=>'api/renew-huikuan/market_lirun_money_zhexian',//销售利润折线图
    'market_renew_money_zhexian/<year>'=>'api/renew-huikuan/market_renew_money_zhexian',//续费折线图
    'lirun_money_zhexian/<year>'=>'api/renew-huikuan/lirun_money_zhexian',//销售利润折线图
    'market_ticheng_zongji/<start>/<end>/<uid>'=>'api/renew-huikuan/ticheng_zongji',//提成计算公式

    //公司统计
    'renew_huikuan_tu/<yyyy>/<adid>'=>'api/renew-huikuan/renewhuikuantu', //统计打款回款折线图
    'productlinrenew/<id>'=>'api/renew-huikuan/productlinrenew',///产品线续费利润 首次续费 最后一次续费
    'zhouqi_kehu/<id>'=>'api/renew-huikuan/zhouqi_kehu', //统计打款回款折线图
    'qiankuan_ad/<id>'=>'api/renew-huikuan/qiankuan_ad',//欠款总额公司
    'cost_ad_tongji/<id:\d+>/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'api/renew-huikuan/cost_ad_tongji',//消耗公司统计
    'lirun_ad_product_tu/<id:\d+>/<year:\d{4}>'=>'api/renew-huikuan/lirun_ad_product_tu',//利润统计按公司 产品线
    'customer_profits_rank/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'api/renew-huikuan/customer_profits_rank',//客户利润排名




    //产品线消耗
    'xiaohaomoneytjprlin/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/xiaohao/xiaohaomoneytjprlin',//根据产品线统计日消耗
    'xiaohaoprzhanbi/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/xiaohao/xiaohaoprzhanbi',//根据产品线统计日消耗


    'renewmoneytjprlin/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/xiaohao/renewmoneytjprlin',//根据产品线统计日续费
    'renewproduct/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'tongji/xiaohao/renewproduct',//根据产品线统计日续费

    'acccount_money/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'api/account/acccount_money',//账户金额消耗列表
    'acccount_money_info/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'api/account/acccount_money_info',//账户金额消耗列表
    'acccount_money_info_day/<start:\d{4}-\d{2}-\d{2}>/<end:\d{4}-\d{2}-\d{2}>'=>'api/account/acccount_money_info_day',//账户金额消耗列表
    /*
     * 消
     *
    */

    'importxiaohao'=>'api/account/importxiaohao',//导入消耗
    'cost_list'=>'api/account/cost_list',//消耗列表
    'check_date_add/<date>'=>'api/account/check_date_add',//检查账户的到期状况
    'ad_account_gl/<id:\d+>'=>'api/account/ad_account_gl',//获取公司的关联账户名称
    'account_upalias'=>'api/account/account_upalias',//修改账户的关联账户


    //2018年3月13日17:33:09
    'margin_m_list'=>'api/margin/list_margin_m',//打保证金列表
    'add_margin_to_media/<contract_id:\d+>'=>'api/margin/add',//新增打保证金
    'add_margin_to_media_ru'=>'api/margin/add-margin-to-media',//新增打保证金返回
    'margin_m_shenhe1/<id:\d+>'=>'api/margin/shenhe1',//打保证金审核1
    'margin_m_shenhe2/<id:\d+>'=>'api/margin/shenhe2',//打保证金审核2
    'margin_m_shenhe3/<id:\d+>'=>'api/margin/shenhe3',//打保证金审核3
    'margin_m_shenhe4/<id:\d+>'=>'api/margin/shenhe4',//打保证金审核4
    'margin_m_shenhe5/<id:\d+>'=>'api/margin/shenhe5',//打保证金审核5


    'margin_m_info/<id:\d+>'=>'api/margin/info',//客户申请打保证金详情


    'ktuimargin/<id:\d+>'=>'api/margin/tuimargin',//申请退保证金
    'kehu_margin_tui_list'=>'api/margin/tui_list', //退保证金列表
    'kehu_margin_tui_info/<id:\d+>'=>'api/margin/tui_info', //退保证金详情
    'kehu_margin_tui_shenhe1/<id:\d+>'=>'api/margin/shenhetui1',//打款审核1
    'kehu_margin_tui_shenhe2/<id:\d+>'=>'api/margin/shenhetui2',//打款审核2
    'kehu_margin_tui_shenhe3/<id:\d+>'=>'api/margin/shenhetui3',//打款审核1
    'kehu_margin_tui_shenhe4/<id:\d+>'=>'api/margin/shenhetui4',//打款审核2
    'kehu_margin_tui_shenhe5/<id:\d+>'=>'api/margin/shenhetui5',//打款审核2


    'zhuankuan_marigin/<contract_id:\d+>'=>'api/margin/zhuankuan',//转保证金款
    'zhuankuan_marginru'=>'api/margin/zhuankuanru',//转保证金款返回



    //部门
    'article_class_list'=>'api/article-class/bumenlist',
   // 'bumenusers/<id:\d+>'=>'api/department/bumenusers', //根据部门ID获取用户
    'add_article_class'=>'api/article-class/addru',//添加返回
    'delete_ar_class/<id:\d+>'=>'api/article-class/deletebumen',//删除部门
    'up_ar_class/<id:\d+>'=>'api/article-class/upbumen',//修改部门

    //公告列表
    'article_list'=>'api/article/list',//外出列表
    'article_add/<id:\d+>'=>'api/article/add',//发票外出
    'article_addru'=>'api/article/addru',//外出新增返回
    'article_delete/<id:\d+>'=>'api/article/deletea',//外出新增返回
    'article_info/<id:\d+>'=>'api/article/article_info',//


    //公司金额总记录
    'customer_money_history/<adid:\d+>'=>'api/contract/history_ad',

    //结束账户
    'end_account/<id:\d+>'=>'api/account/account_end',
    //

    //2018年4月17日14:43:48
    //补账户币 ，生成相对应得续费
    'addbukuanbi'=>'api/bukuan/addbukuanbi',
    'bukuanbi_shenhe1/<id:\d+>'=>'api/bukuan/bishenhe1',//补账户币1审
    'bukuanbi_shenhe2/<id:\d+>'=>'api/bukuan/bishenhe2',//补账户币2审
    'bukuanbi_shenhe3/<id:\d+>'=>'api/bukuan/bishenhe3',//补账户币3审
    'bukuanbi_shenhe4/<id:\d+>'=>'api/bukuan/bishenhe4',//补账户币4审
    'bukuanbi_shenhe5/<id:\d+>'=>'api/bukuan/bishenhe5',//补账户币5审



    //修改账户 媒体打款类型 周期  时间
    'account_upkuan'=>'api/account/account_upkuan',
    'yfk_list'=>'api/mback-money/yfk_list',//应付款列表
    
    //2018年4月28日10:05:08
    'margin_meijie_list'=>'api/meijie-margin/margin_meijie_list',//媒体保证金 按公司名产品线
    'margin_kehu_list'=>'api/meijie-margin/margin_kehu_list',//媒体保证金 按公司名产品线



    //2018-5-3 14:45:38
    'beikuan_account_list/<id>'=>'api/beikuan-account/account_list',//备款账户列表
    'beikuan_account_info/<id:\d+>'=>'api/beikuan-account/account_info',//账户详细信息
    'beikuan_account_add/<contract_id:\d+>'=>'api/beikuan-account/account_add',//账户添加
    'beikuan_account_add_ru'=>'api/beikuan-account/account_addru',//账户添加返回
    'beikuan_account_updataru/<id:\d+>'=>'api/beikuan-account/updateaccount',//修改账户
    'beikuan_checkaccount_add/<a_users>'=>'api/beikuan-account/checkaccount_add',//检查账户信息
    'beikuan_checkaccount_up/<a_users>/<thisid>'=>'api/beikuan-account/checkaccount_up',//检查账户信息

    'renew_beikuan_account/<id:\d+>'=>'api/beikuan-account/beikuan_account',//查看媒介合同下所有的备款账户
    'beikuan_account_quey'=>'api/beikuan-account/beikuan_account_quey',//续费账户对应的备款账户id
    'beikuan_account_renewlist'=>'api/beikuan-account/mrenew_list', //备款账户 媒介合同下面的续费列表
    'beikuan_account_renew_binding'=>'api/beikuan-account/beikuan_account_renew_binding',//备款账户绑定续费



    'beikuan_list'=>'api/beikuan/list',//媒介退款列表
    'add_beikuan/<contract_id:\d+>'=>'api/beikuan/add',//新增退款
    'add_beikuan_ru'=>'api/beikuan/addru',//新增退款
    'beikuan_info/<id:\d+>'=>'api/beikuan/beikuan_info',//m媒介退款审核
    'beikuan_shenhe1/<id:\d+>'=>'api/beikuan/shenhe1',//m媒介退款审核
    'beikuan_shenhe2/<id:\d+>'=>'api/beikuan/shenhe2',//m媒介退款审核
    'beikuan_shenhe3/<id:\d+>'=>'api/beikuan/shenhe3',//m媒介退款审核
    'beikuan_shenhe4/<id:\d+>'=>'api/beikuan/shenhe4',//m媒介退款审核
    'beikuan_shenhe5/<id:\d+>'=>'api/beikuan/shenhe5',//m媒介退款审核

    'beikuanAccountStatus/<id:\d+>/<status:\d+>'=>'api/beikuan-account/beikuan-account-status', //备款账户结束

    'refund_money_add_ru_beikuan'=>'api/beikuan/refund-money-add',//新增备款账户退款返回
    'refund_money_list_beikuan'=>'api/beikuan/list-tuikuan',//备款账户退款列表
    'refund_money_beikuan_info/<id:\d+>'=>'api/beikuan/beikuan_tuikuan_info',//备款账户退款详情
    'beikuan_account_tuikuan_shenhe1/<id:\d+>'=>'api/beikuan/tuikuan-shenhe1',//m媒介退款审核
    'beikuan_account_tuikuan_shenhe2/<id:\d+>'=>'api/beikuan/tuikuan-shenhe2',//m媒介退款审核
    'beikuan_account_tuikuan_shenhe3/<id:\d+>'=>'api/beikuan/tuikuan-shenhe3',//m媒介退款审核
    'beikuan_account_tuikuan_shenhe4/<id:\d+>'=>'api/beikuan/tuikuan-shenhe4',//m媒介退款审核
    'beikuan_account_tuikuan_shenhe5/<id:\d+>'=>'api/beikuan/tuikuan-shenhe5',//m媒介退款审核
    //2018年5月10日16:00:22  父子流程

    'flow_fz_show/<fu_flow>/<id>'=>'api/audit/flow_fz_show',//父子流程



    //2018年5月11日18:56:12  媒介合同账户功能 -
    'account_list_m/<id>'=>'api/account/account_list_m',//媒介合同账户列表
    'copyaccount'=>'api/meijie-contract/copyaccount',//提交转入账户
    'updateaccountrenew/<id:\d+>/<account:\d+>/<mhtid:\d+>'=>'api/meijie-contract/updateaccountrenew',//修改续费 退款 补账户币 账户id 并修改实付金额


    'upmarginmht/<id>/<mhtid>'=>'api/margin/upmarginmht',//修改保证金所属媒介合同

    'adtoaccount/<id>'=>'api/customer-advertiser/adtoaccount',//广告主下的账户

    'meitituikuan_list'=>'api/meijie-tuikuan/indexlist',//媒介退款列表
    'meitituikuan_info/<id:\d+>'=>'api/meijie-tuikuan/info',//媒介退款详情
    'meitituikuan_shenhe1/<id:\d+>'=>'api/meijie-tuikuan/shenhe1',//m媒介退款审核
    'meitituikuan_shenhe2/<id:\d+>'=>'api/meijie-tuikuan/shenhe2',//m媒介退款审核
    'meitituikuan_shenhe3/<id:\d+>'=>'api/meijie-tuikuan/shenhe3',//m媒介退款审核
    'meitituikuan_shenhe4/<id:\d+>'=>'api/meijie-tuikuan/shenhe4',//m媒介退款审核
    'meitituikuan_shenhe5/<id:\d+>'=>'api/meijie-tuikuan/shenhe5',//m媒介退款审核


    //渠道管理平台api
    'place_advertiser_list'=>'api/qudaoapi/qudao_to_ad',//渠道下所有客户
    'place_to_advertiser'=>'api/qudaoapi/ad_to_account',//渠道下所有客户
    'place_account_domain/<id:\d+>'=>'api/qudaoapi/account_domain',//渠道下所有客户

    'domain_list'=>'api/account/yuminglist',//域名列表
    'domain_shenhe1'=>'api/qudaoapi/shenhe1',//域名审核1
    'domain_shenhe2'=>'api/qudaoapi/shenhe2',//域名审核2

    'page_shenhe1'=>'api/qudaoapi/yeshenhe1',//域名审核1
    'page_shenhe2'=>'api/qudaoapi/yeshenhe2',//域名审核2

    'create_page_task'=>'api/qudaoapi/add_ye', //新增渠道页面需求
    'page_list'=>'api/qudaoapi/yelist',//落地页需求列表


    'qudao_renewlist'=>'api/qudaoapi/renewlist',//渠道续费列表
    'qudao_tuikuanlist'=>'api/qudaoapi/crm_tuikuan_list',//渠道退款列表
    'qudao_backmoneylist'=>'api/qudaoapi/crm_backmoney_list',//渠道回款列表
    'qd_addrenew/<id:\d+>'=>'api/qudaoapi/addrenewinfo',//添加渠道续费获取信息
    'qd_addrenew_return'=>'api/qudaoapi/addrenew',//添加续费返回
    'qd_renewshenhe1/<id:\d+>'=>'api/qudaoapi/renewshenhe1',//渠道续费审核
    'qd_uprenewshowmoney/<id:\d+>'=>'api/qudaoapi/uprenewshowmoney',//修改渠道续费show_money 方法
    'updemainstatus/<id:\d+>'=>'api/account/upyumingstatusover',//修改域名状态


    'contract_accounts_list/<id>'=>'api/contract-accounts/contractlist',//获取合同列表


    'contract_accounts_info/<id:\d+>'=>'api/contract-accounts/contract_info',//合同详情
    'contract_accounts_shenhe1/<id:\d+>'=>'api/contract-accounts/shenhe1',//合同审核
    'contract_accounts_shenhe2/<id:\d+>'=>'api/contract-accounts/shenhe2',//合同审核
    'contract_accounts_shenhe3/<id:\d+>'=>'api/contract-accounts/shenhe3',//合同审核
    'contract_accounts_shenhe4/<id:\d+>'=>'api/contract-accounts/shenhe4',//合同审核
    'contract_accounts_shenhe5/<id:\d+>'=>'api/contract-accounts/shenhe5',//合同审核
    'contract_accounts_new/<id:\d+>'=>'api/contract-accounts/add-contract',//新增合同
    'contract_accounts_addru'=>'api/contract-accounts/addru',//新增合同返回
    'contract_accounts_guidang/<id:\d+>'=>'api/contract-accounts/guidang',//合同归档


    //2018年9月12日11:16:03
    'meijie_fakuan_list'=>'api/meijie-fakuan/list',//媒介罚款列表
    'meijie_add_fakuan/<contract_id:\d+>'=>'api/meijie-fakuan/add',//新增罚款
    'meijie_add_fakuan_ru'=>'api/meijie-fakuan/addru',//新增罚款返回
    'meijie_fakuan_shenhe1/<id:\d+>'=>'api/meijie-fakuan/shenhe1',//m媒介罚款审核
    'meijie_fakuan_shenhe2/<id:\d+>'=>'api/meijie-fakuan/shenhe2',//m媒介罚款审核
    'meijie_fakuan_shenhe3/<id:\d+>'=>'api/meijie-fakuan/shenhe3',//m媒介罚款审核
    'meijie_fakuan_shenhe4/<id:\d+>'=>'api/meijie-fakuan/shenhe4',//m媒介罚款审核
    'meijie_fakuan_shenhe5/<id:\d+>'=>'api/meijie-fakuan/shenhe5',//m媒介罚款审核
    'meijie_fakuan_info/<id:\d+>'=>'api/meijie-fakuan/fakuan_info',//罚款详情

    //回款计划
    'huikuan_plan_list'=>'api/huikuan-plan/list',//列表
    'huikuan_plan_list_ad'=>'api/huikuan-plan/huikuan_plan_list_ad',//公司回款计划列表
    'huikuan_plan_addru'=>'api/huikuan-plan/addru',//新增返回
    'up_plan/<id:\d+>'=>'api/huikuan-plan/up-plan',//修改回款计划
    'get_huikuan_plan_group'=>'api/huikuan-plan/get-plan-group',//获取计划还款
    'get_huikuan_plan_cost'=>'api/huikuan-plan/get-plan-cost',//获取计划回款和实际回款总数
    'get_overdue_ad'=>'api/huikuan-plan/get-overdue-ad',//获取逾期客户
    'huikuan_ad_plan_list'=>'api/huikuan-plan/ad-plan-list',//获取逾期客户


    //账户涨幅
    'account_last_date'=>'api/account/last_date',
    'account_cost_zf'=>'api/account/account-cost',
    //'account_cost_zf_all'=>'api/account/account-cost-all',
    'account_cost_zf_ad'=>'api/account/account-cost-ad',

    'account_cost_zf_choosable'=>'api/account/account-cost-choosable',
    'account_cost_zf_all_choosable'=>'api/account/account-cost-all-choosable',
    'account_cost_zf_ad_choosable'=>'api/account/account-cost-ad-choosable',


    //公司账户消耗 余额
    'adcost'=>'api/account/adcost',
    'adcost_product_lin'=>'api/account/ad-product-line-cost',
    'adcost_product_lin_up'=>'api/account/account-amendment',


    //盖章
    'gaizhang_list'=>'api/gaizhang/list',//盖章列表
    'gaizhang_add/<id:\d+>'=>'api/gaizhang/add',//发票盖章
    'gaizhang_addru'=>'api/gaizhang/addru',//盖章新增返回
    'gaizhang_info/<id:\d+>'=>'api/gaizhang/gaizhang_info',//盖章详情
    'gaizhang_shenhe1/<id:\d+>'=>'api/gaizhang/shenhe1',//盖章审核1
    'gaizhang_shenhe2/<id:\d+>'=>'api/gaizhang/shenhe2',//盖章审核2
    'gaizhang_shenhe3/<id:\d+>'=>'api/gaizhang/shenhe3',//盖章审核2
    'gaizhang_shenhe4/<id:\d+>'=>'api/gaizhang/shenhe4',//盖章审核2
    'gaizhang_shenhe5/<id:\d+>'=>'api/gaizhang/shenhe5',//盖章审核2

    ['class' => 'yii\rest\UrlRule', 'controller' =>
        [
            'api/user',
            'api/customerb',
            'api/customer',
            'api/contact',
            'api/contract',
            'api/auditAction',
            'api/productline',
            'api/backmoney',
            'api/agentCompany',
            'api/refundMoney',
            'api/product',
            'api/xiaohao'
        ]
    ],
    [
        'class' => 'yii\rest\UrlRule',
        'controller' => 'api/user',
        'extraPatterns' => [
            'GET search' => 'search',
        ],
    ],

];


