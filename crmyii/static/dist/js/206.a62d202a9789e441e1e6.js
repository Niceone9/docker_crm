webpackJsonp([206],{1376:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var s=e(32),n=function(t){return t&&t.__esModule?t:{default:t}}(s),i=e(50),o=e(470),l=e(1);a.default={data:function(){return{routeId:"",disabledPull:!0,dataInfor:[],cu_ad_id:"",accountOptions:[],textShow:!0,agent_company:"",add_bukuanGet:function(){var t=this,a=location.href.split("?")[1],e=a.split("&"),s=decodeURI(e[0].split("=")[1]);(0,o.add_bukuanGet)({id:s}).then(function(a){t.dataInfor=[],t.dataInfor.push(a.contract_info),t.formData[0].submituser=t.user.id,t.formData[0].xf_contractid=a.contract_info.id,t.formData[0].advertiser=a.contract_info.advertiser,t.formData[0].appname=a.contract_info.appname,t.agent_company=a.contract_info.agent_company}).catch(function(a){t.$message.error("获取失败")})},customer_advertiser_list:function(){var t=this;(0,o.customer_advertiser_list)({id:this.accounts.id}).then(function(a){t.accountOptions=a.list}).catch(function(a){t.$message.error("获取失败")})},formData:[{advertiser:"",xf_contractid:" ",submituser:" ",money:"",company:"",appname:"",payment_time:l().format("YYYY-MM-DD"),note:"",cu_ad_id:""}],companyOptions:[],dataObj:{"File[yid]":"","File[type]":31},judge:function(){""!=this.formData[0].money?this.disabledPull=!1:this.disabledPull=!0},tan:function(t){var a=this;this.$confirm(t,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){a.disabledPull=!0,a.formData[0].payment_time=l(a.formData[0].payment_time).format("YYYY-MM-DD"),a.add_bukuan_ru(a.formData[0])}).catch(function(){a.$message({type:"info",message:"已取消提交"})})},add_bukuan_ru:function(t){var a=this;(0,o.add_bukuan_ru)({data:t}).then(function(t){if("200"==t.code){a.$message({message:"上传成功",type:"success"}),a.disabledPull=!0,a.dataObj["File[yid]"]=t.data.id,a.dataObj["File[type]"]=31;a.$refs.upload.submit(),a.success()}else a.disabledPull=!1,a.$message({message:t.meg,type:"warning"})}).catch(function(t){a.$message.error("获取失败")})},success:function(){this.$notify({title:"上传成功",message:"此页面将跳转到--协议详情",type:"success"});var t=this;setTimeout(function(){t.$router.push({name:"agreementDetails",query:{id:t.routeId}})},500)}}},components:{},computed:(0,n.default)({},(0,i.mapGetters)(["user","accounts"])),watch:{user:function(t){}},methods:{bukuandata:function(){this.routeId=this.$route.query.id,this.customer_advertiser_list(),this.add_bukuanGet()},bukuanchange:function(){this.judge()},cu_ad_id_change:function(t){this.formData[0].cu_ad_id=this.cu_ad_id},pullData:function(){this.tan("提交后不可更改，是否继续?")},handleChange:function(t,a){this.textShow=!1},removeChange:function(t,a){0==a.length?this.textShow=!0:this.textShow=!1},handleAvatarSuccess:function(){this.success(),0==this.dateShow&&this.add_picifile(this.dataObj["File[yid]"])},reset:function(){this.formData[0].payment_time=l().format("YYYY-MM-DD"),this.formData[0].money="",this.formData[0].note="",this.$refs.upload.clearFiles()}},created:function(){},filters:{fileFun:function(t){return t||"---"},fileFunName:function(t){return t?t.name:"---"}}}},1542:function(t,a){t.exports={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"Addkh_bukuanForm ku_addBox"},[e("el-row",{staticClass:"bukuanBox"},[e("el-col",{staticClass:"kh_InforBox",attrs:{span:24}},[e("el-col",{staticClass:"kh_left_box",attrs:{span:16}},[e("el-col",{staticClass:"topInfor",attrs:{span:24}},[e("div",{staticClass:"titleInfor"},[e("span",{staticClass:"listStyle"}),t._v(" "),e("span",{staticClass:"listText"},[t._v("补款信息")])]),t._v(" "),e("div",{staticClass:"titleBox"},[e("div",{staticStyle:{width:"60%",display:"inline-block","vertical-align":"top"}},[t._l(t.formData,function(a,s){return e("div",[e("div",{staticStyle:{height:"30px","margin-bottom":"5px"}},[e("el-col",{staticClass:"infor_a",attrs:{span:24}},[e("el-col",{staticClass:"title",staticStyle:{"font-size":"16px",color:"black"},attrs:{span:5}},[t._v("\n                                                补款金额\n                                            ")]),t._v(" "),e("el-col",{staticClass:"input_search",attrs:{span:19}},[e("el-input",{staticClass:"inputNum inputText",attrs:{type:"text",placeholder:"请输入内容"}},[e("template",{slot:"append"},[t._v("元")])],2),t._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:a.money,expression:"data.money"}],staticStyle:{"line-height":"normal",border:"none","font-size":"12px",height:"24px",position:"absolute",left:"36px",top:"4px",width:"248px",outline:"none","z-index":"9"},attrs:{type:"number",name:"mouse2",placeholder:"请输入金额",onmousewheel:"return false;"},domProps:{value:a.money},on:{input:[function(e){e.target.composing||t.$set(a,"money",e.target.value)},t.bukuanchange]}})],1)],1)],1),t._v(" "),e("div",{staticStyle:{height:"30px","margin-bottom":"5px"}},[e("el-col",{staticClass:"infor_a",attrs:{span:24}},[e("el-col",{staticClass:"title",attrs:{span:5}},[t._v("\n                                                补款日期\n                                            ")]),t._v(" "),e("el-col",{staticClass:"input_search",attrs:{span:19}},[e("el-date-picker",{attrs:{clearable:!1,type:"date",placeholder:"选择日期"},model:{value:a.payment_time,callback:function(e){t.$set(a,"payment_time",e)},expression:"data.payment_time"}})],1)],1)],1),t._v(" "),e("div",{staticStyle:{height:"30px","margin-bottom":"5px"}},[e("el-col",{staticClass:"infor_a",attrs:{span:24}},[e("el-col",{staticClass:"title",attrs:{span:5}},[t._v("\n                                                广告主名称\n                                            ")]),t._v(" "),e("el-col",{staticClass:"input_search",attrs:{span:19}},[e("el-select",{attrs:{filterable:"",placeholder:"请选择"},on:{change:t.cu_ad_id_change},model:{value:t.cu_ad_id,callback:function(a){t.cu_ad_id=a},expression:"cu_ad_id"}},t._l(t.accountOptions,function(t,a){return e("el-option",{key:a,attrs:{label:t.advertiser,value:t.id}})}),1)],1)],1)],1),t._v(" "),e("div",{staticStyle:{height:"30px","margin-bottom":"5px"}},[e("el-col",{staticClass:"infor_a",attrs:{span:24}},[e("el-col",{staticClass:"title",attrs:{span:5}},[t._v("\n                                                补款主体\n                                            ")]),t._v(" "),e("el-col",{staticClass:"input_search",attrs:{span:19}},[e("div",{staticClass:"inputBox",staticStyle:{background:"none",border:"none"}},[t._v("\n                                                    "+t._s(t.agent_company)+"\n                                                ")])])],1)],1)])}),t._v(" "),e("div",{staticStyle:{height:"30px","margin-bottom":"5px"}},[e("el-col",{staticClass:"infor_a",staticStyle:{height:"auto"},attrs:{span:24}},[e("el-col",{staticClass:"title",attrs:{span:5}},[t._v("\n                                            附件信息\n                                        ")]),t._v(" "),e("el-col",{staticClass:"input_search",attrs:{span:19}},[e("el-upload",{ref:"upload",attrs:{"list-type":"picture",name:"File[imageFiles][]",action:"/api/file/addfile","on-success":t.handleAvatarSuccess,"on-change":t.handleChange,"on-remove":t.removeChange,data:t.dataObj,"auto-upload":!1,multiple:""}},[e("el-button",{attrs:{slot:"trigger",size:"small",type:"primary"},slot:"trigger"},[t._v("选取文件")]),t._v(" "),t.textShow?e("span",{staticStyle:{"font-size":"12px",color:"#9c9c9c"}},[t._v("未选择任何文件")]):t._e()],1)],1)],1)],1)],2),t._v(" "),t._l(t.formData,function(a,s){return e("div",{staticStyle:{width:"39%",display:"inline-block","vertical-align":"top","margin-left":"-10px"}},[e("div",{staticStyle:{height:"30px","margin-bottom":"5px"}},[e("el-col",{staticClass:"infor_a",attrs:{span:24}},[e("el-col",{staticClass:"title",staticStyle:{"font-size":"16px",color:"black","text-align":"left"},attrs:{span:7}},[t._v("\n                                            备注信息\n                                        ")]),t._v(" "),e("el-col",{staticClass:"input_search",attrs:{span:17}},[e("el-input",{attrs:{type:"textarea",rows:7,placeholder:"请输入内容"},model:{value:a.note,callback:function(e){t.$set(a,"note",e)},expression:"data.note"}})],1)],1)],1)])})],2)]),t._v(" "),e("el-col",{staticClass:"fujianInfor",attrs:{span:24}},[e("el-col",{staticClass:"foot_btn",attrs:{span:24}},[e("el-button",{attrs:{type:"primary",disabled:t.disabledPull},on:{click:t.pullData}},[t._v("提交")]),t._v(" "),e("el-button",{staticClass:"reset",on:{click:t.reset}},[t._v("重置")])],1)],1)],1)],1)],1)],1)},staticRenderFns:[]}},988:function(t,a,e){var s=e(19)(e(1376),e(1542),null,null);t.exports=s.exports}});