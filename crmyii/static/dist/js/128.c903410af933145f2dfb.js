webpackJsonp([128],{1175:function(t,e,a){a(2204),a(2203);var i=a(19)(a(1770),a(2437),"data-v-cac34c14",null);t.exports=i.exports},1770:function(t,e,a){"use strict";function i(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(e,"__esModule",{value:!0});var o=a(32),c=i(o),n=a(490),l=a(50),r=a(20),s=(i(r),a(1));e.default={name:"dashboard",data:function(){return{note:"",company:"",name:"",address:"",input:"",data:{},start:"",end:""}},computed:(0,c.default)({},(0,l.mapGetters)(["user","roles"])),methods:{newcontact:function(t){this.enclo[t].showImg=!0},huitui:function(){this.$router.go(-1)},submitForm:function(){var t=this;if(""==this.company)this.$message.error("公司名称不能为空");else if(""==this.start||""==this.end)this.$message.error("时间不能为空");else if(""==this.name)this.$message.error("联系人不能为空");else if(""==this.address)this.$message.error("联系地址不能为空");else if(""==this.note)this.$message.error("事由不能为空");else{var e={submituser:this.user.id,gongsi:this.company,lianxiren:this.name,dizhi:this.address,starttime:s(this.start).format("YYYY-MM-DD HH:mm:ss"),endtime:s(this.end).format("YYYY-MM-DD HH:mm:ss"),shiyou:this.note};(0,n.waichu_addru)(e).then(function(e){if(200==e.code){t.$notify({title:"提交成功",message:"此页面将跳转到-外出列表",type:"success"});var a=t;setTimeout(function(){a.$router.push({name:"goOut"})},1e3)}else t.$message.error("提交失败！")}).catch(function(t){})}},resetForm:function(){this.start="",this.end="",this.note="",this.company="",this.name="",this.address=""}},watch:{id:function(t){}},created:function(){}}},1977:function(t,e,a){e=t.exports=a(965)(),e.push([t.i,".vue-table{border:1px solid #b4b4b4!important}.el-table td,.el-table th{padding:0!important;height:35px!important}.vue-table .el-table__header thead tr th{border-bottom:none}.vue-table .el-table--fit{text-align:center}.vue-table .el-table--fit,.vue-table .el-table__empty-block{height:400px!important;min-height:400px!important}.vue-table .el-input__inner{height:30px!important;line-height:30px!important}.vue-table .tool-bar{margin-bottom:0}.vue-table .cell,.vue-table .td-text{width:100%;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important;color:#000;padding-left:20px}.vue-table td,.vue-table th.is-leaf{border-bottom:none!important}.el-table thead th,.el-table thead th>.cell{background:#f7f7f7!important}.vue-table .cell{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.vue-table .cell .xfshow{position:absolute;left:0;top:0;width:0;height:0;border-top:10px solid #ff4081;border-right:10px solid transparent}.vue-table .cell .appInfor{width:20px;height:20px;text-align:center;line-height:20px;font-size:12px;border-radius:50%;display:inline-block}.vue-table .cell .appInfor.qu{color:#fff;background:#ff8754}.vue-table .cell .appInfor.zhi{color:#fff;background:#3f7ffc}.vue-table .el-pagination{white-space:nowrap;padding:2px 5px;color:#606987;float:right;border-radius:5px}.vue-table .el-pager li,.vue-table .el-pagination button,.vue-table .el-pagination span{min-width:25px;height:25px;line-height:25px}.vue-table .el-pager li{padding:0 4px;font-size:14px;border-radius:5px;text-align:center;margin:0 2px;color:#bfcbd9}.vue-table .el-input__inner,.vue-table .el-pagination .btn-next,.vue-table .el-pagination .btn-prev{border-radius:5px;border:1px solid #bfcbd9}.vue-table .el-input__inner{width:100%;height:20px}.vue-table .el-pagination .el-select .el-input input{padding-right:25px;border-radius:5px;height:25px!important;margin-left:5px}.vue-table .el-pagination__editor{border:1px solid #d1dbe5;border-radius:5px;padding:4px 2px;width:25px!important;height:25px;line-height:25px;text-align:center;margin:0 6px;box-sizing:border-box;transition:border .3s}.vue-table .pagination-wrap{text-align:center;margin-top:8px;height:40px}.vue-table .el-pager li{box-sizing:border-box}.vue-table .el-pager li.active+li{border:1px solid #d1dbe5}.vue-table .el-table__body td{cursor:pointer;font-size:12px;border-right:none}.vue-table.el-table .cell,.vue-table.el-table th>div{padding-right:0}.vue-table ::-webkit-scrollbar{width:7px;height:16px;border-radius:0;background-color:#fff}.vue-table ::-webkit-scrollbar-track{border-radius:10px;background-color:#fff}.vue-table ::-webkit-scrollbar-thumb{height:10px;border-radius:10px;-webkit-box-shadow:inset 0 0 6px #fff;background-color:rgba(205,211,237,.4)}.vue-table tbody tr:nth-child(2n) td{background:#f8f9fb;background-clip:padding-box!important}.vue-table tbody tr:nth-child(odd) td{background-color:#fff}.block{height:50px;position:relative;padding:.1px}.block .el-pagination{margin-top:10px}.block .el-pagination .el-pagination__sizes .el-select{height:30px!important}.block .el-pagination .el-pagination__sizes .el-select input{height:29px!important;line-height:29px!important}.textBorder{text-decoration:line-through!important}.border,.textBorder{color:#757575!important}.textColor{background:#757575!important}.crm_title{width:100%;height:20px;line-height:20px;font-size:14px;margin:0;text-indent:20px;position:relative;color:#000;background:#fff}.crm_title .crm_line{display:inline-block;position:absolute;left:0;top:0;bottom:0;margin:auto;width:4px;height:20px;background:#1a2834}.crm_title span{cursor:pointer}.crm_title .list{margin:0 10px;text-decoration:underline}.crm_title .list img{width:14px}.replenishment2 .el-date-editor.el-input{width:180px}.replenishment2 .el-date-editor.el-input .el-input__icon{left:-5px;line-height:0}.replenishment2 .el-date-editor.el-input .el-input__inner{padding:0 0 0 30px}.replenishment2 .el-input-group,.replenishment2 .el-select .el-input{width:200px}.replenishment2 .el-input{position:relative;font-size:14px;display:inline-block;width:200px}.replenishment2 .el-input input{border:none;height:100%!important}.replenishment2 .el-input__inner{height:24px!important;line-height:24px!important}.replenishment2 .el-button--primary{color:#fff;background-color:#20a0ff;border-color:#20a0ff}.replenishment2 .el-button--default,.replenishment2 .el-button--primary{font-size:12px;width:80px;height:30px}.replenishment2 .el-button--text{margin-left:8px;border:none;color:#000;background:0 0;font-size:12px;padding-left:0;padding-right:0}",""])},1978:function(t,e,a){e=t.exports=a(965)(),e.push([t.i,'.block-checkbox[data-v-cac34c14]{display:block}.operation-container .cell[data-v-cac34c14]{padding:10px!important}.operation-container .el-button[data-v-cac34c14]:nth-child(3){margin-top:10px;margin-left:0}.operation-container .el-button[data-v-cac34c14]:nth-child(4){margin-top:10px}.el-upload input[type=file][data-v-cac34c14]{display:none!important}.el-upload__input[data-v-cac34c14]{display:none}.cell .el-tag[data-v-cac34c14]{margin-right:8px}.small-padding .cell[data-v-cac34c14]{padding-left:8px;padding-right:8px}.status-col .cell[data-v-cac34c14]{padding:0 10px;text-align:center}.status-col .cell .el-tag[data-v-cac34c14]{margin-right:0}.el-dialog[data-v-cac34c14]{transform:none;left:0;position:relative;margin:0 auto}.article-textarea textarea[data-v-cac34c14]{padding-right:40px;resize:none;border:none;border-radius:0;border-bottom:1px solid #bfcbd9}.upload-container .el-upload[data-v-cac34c14]{width:100%}.upload-container .el-upload .el-upload-dragger[data-v-cac34c14]{width:100%;height:200px}html[data-v-cac34c14]{font-family:\\\\5FAE\\8F6F\\96C5\\9ED1,Arial,sans-serif}b[data-v-cac34c14],body[data-v-cac34c14],button[data-v-cac34c14],dd[data-v-cac34c14],div[data-v-cac34c14],dl[data-v-cac34c14],dt[data-v-cac34c14],fieldset[data-v-cac34c14],form[data-v-cac34c14],h1[data-v-cac34c14],h2[data-v-cac34c14],h3[data-v-cac34c14],h4[data-v-cac34c14],h5[data-v-cac34c14],h6[data-v-cac34c14],i[data-v-cac34c14],input[data-v-cac34c14],li[data-v-cac34c14],ol[data-v-cac34c14],p[data-v-cac34c14],strong[data-v-cac34c14],td[data-v-cac34c14],textarea[data-v-cac34c14],th[data-v-cac34c14],ul[data-v-cac34c14]{padding:0;margin:0;font-family:Microsoft YaHei,sans-serif;list-style:none}b[data-v-cac34c14],strong[data-v-cac34c14]{font-weight:400;font-size:13px}fieldset[data-v-cac34c14],img[data-v-cac34c14]{border:0}a[data-v-cac34c14]{text-decoration:none;color:#000;outline:none}li[data-v-cac34c14]{list-style:none}i[data-v-cac34c14]{font-style:normal}h2[data-v-cac34c14],h3[data-v-cac34c14],h4[data-v-cac34c14],h5[data-v-cac34c14],h6[data-v-cac34c14]{font-size:100%;font-weight:400}button[data-v-cac34c14],input[data-v-cac34c14],optgroup[data-v-cac34c14],option[data-v-cac34c14],select[data-v-cac34c14],textarea[data-v-cac34c14]{font-family:inherit;font-size:inherit;font-style:inherit;font-weight:inherit}button[data-v-cac34c14],input[data-v-cac34c14],select[data-v-cac34c14],textarea[data-v-cac34c14]{*font-size:100%}.clear[data-v-cac34c14]:after{display:block;content:"clear";height:0;clear:both;visibility:hidden}.clear[data-v-cac34c14]{zoom:1}body[data-v-cac34c14]{min-width:1000px}.zhi[data-v-cac34c14]{display:inline-block;width:20px;height:20px;font-size:12px;text-align:center;line-height:20px;border-radius:100%;color:#54adff;border:1px solid #54adff}.tongguo[data-v-cac34c14]{background:#879aff}.tongguo[data-v-cac34c14],.yutongguo[data-v-cac34c14]{width:80px;height:30px;line-height:30px;text-align:center;border:none;color:#fff;font-size:12px;border-radius:5px}.yutongguo[data-v-cac34c14]{background:#54adff}.notongguo[data-v-cac34c14]{width:80px;height:30px;line-height:30px;text-align:center;border:none;color:#fff;font-size:12px;border-radius:5px;background:#f75976}.icon[data-v-cac34c14]{width:2em;height:2em;vertical-align:-.15em;fill:currentColor;overflow:hidden}input[type=radio][data-v-cac34c14]{width:20px;height:20px;opacity:0}.cusmessage[data-v-cac34c14]{height:30px;font-size:12px;line-height:30px;padding-left:17px;border-bottom:1px solid #e6e6e6}label[data-v-cac34c14]{position:absolute;left:15px;top:10px;width:20px;height:20px;border-radius:50%;border:1px solid #999}input:checked+label[data-v-cac34c14]{background-color:#54adff;border:1px solid #54adff}input:checked+label[data-v-cac34c14]:after{position:absolute;content:"";width:5px;height:10px;top:3px;left:6px;border:2px solid #fff;border-top:none;border-left:none;transform:rotate(45deg)}',""])},2203:function(t,e,a){var i=a(1977);"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);a(966)("2a911716",i,!0)},2204:function(t,e,a){var i=a(1978);"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);a(966)("a0f47940",i,!0)},2437:function(t,e){t.exports={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("el-row",{staticClass:"replenishment2"},[a("p",{staticClass:"crm_title"},[a("i",{staticClass:"crm_line"}),t._v(" "),a("span",{staticStyle:{cursor:"pointer"},on:{click:t.huitui}},[t._v("\n            外出列表>\n        ")]),t._v("\n         我要外出\n    ")]),t._v(" "),a("el-col",{staticStyle:{"margin-top":"10px"},attrs:{span:24}},[a("el-col",{attrs:{lg:14}},[a("el-col",{staticStyle:{padding:"0"},attrs:{span:24}},[a("el-col",{staticStyle:{height:"30px","line-height":"30px","border-bottom":"1px solid #e6e6e6",color:"#222f3b","font-size":"12px",padding:"0"},attrs:{span:24}},[a("el-col",{staticStyle:{"padding-left":"0"},attrs:{span:24}},[a("span",{staticStyle:{display:"inline-block",width:"8px",height:"8px","border-radius":"100%",background:"#222f3b"}}),t._v("\n                            外出信息\n                        ")])],1),t._v(" "),a("el-col",{staticStyle:{"margin-top":"10px",padding:"0"},attrs:{span:24}},[a("el-col",{staticStyle:{border:"1px solid #e6e6e6",padding:"0"},attrs:{span:6}},[a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#9c9c9c"},attrs:{span:24}},[t._v("\n                                外出公司\n                            ")]),t._v(" "),a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#9c9c9c"},attrs:{span:24}},[t._v("\n                                外出联系人\n                            ")]),t._v(" "),a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#9c9c9c"},attrs:{span:24}},[t._v("\n                                外出公司地址\n                            ")]),t._v(" "),a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#9c9c9c"},attrs:{span:24}},[t._v("\n                                外出时间\n                            ")]),t._v(" "),a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#9c9c9c","border-bottom":"none"},attrs:{span:24}},[t._v("\n                                外出事由\n                            ")])],1),t._v(" "),a("el-col",{staticStyle:{border:"1px solid #e6e6e6",padding:"0"},attrs:{span:18}},[a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#222f3b"},attrs:{span:24}},[a("el-input",{attrs:{placeholder:"请输入公司名称"},model:{value:t.company,callback:function(e){t.company=e},expression:"company"}})],1),t._v(" "),a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#222f3b"},attrs:{span:24}},[a("el-input",{attrs:{placeholder:"请输入联系人名称"},model:{value:t.name,callback:function(e){t.name=e},expression:"name"}})],1),t._v(" "),a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#222f3b"},attrs:{span:24}},[a("el-input",{attrs:{placeholder:"请输入公司地址"},model:{value:t.address,callback:function(e){t.address=e},expression:"address"}})],1),t._v(" "),a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#222f3b"},attrs:{span:24}},[a("el-date-picker",{attrs:{clearable:!1,type:"datetime",placeholder:"选择日期"},model:{value:t.start,callback:function(e){t.start=e},expression:"start"}}),t._v("\n                                ~\n                                "),a("el-date-picker",{attrs:{clearable:!1,type:"datetime",placeholder:"选择日期"},model:{value:t.end,callback:function(e){t.end=e},expression:"end"}})],1),t._v(" "),a("el-col",{staticClass:"cusmessage",staticStyle:{color:"#222f3b","border-bottom":"none"},attrs:{span:24}},[a("el-input",{attrs:{placeholder:"请输入内容"},model:{value:t.note,callback:function(e){t.note=e},expression:"note"}})],1)],1)],1)],1),t._v(" "),a("el-col",{attrs:{span:24}},[a("p",{staticStyle:{"font-size":"12px",color:"#c4c4c4","margin-top":"10px"}},[a("el-col",{attrs:{span:1}},[t._v("注：")]),t._v(" "),a("el-col",{attrs:{span:20}},[t._v("\n                             1、请严格遵守此系统提交申请单 "),a("br"),t._v("\n                             2、请假必须通过审核才算生效（交后为未审核状态） "),a("br"),t._v("\n                             3、外出请注意安全 "),a("br")])],1)]),t._v(" "),a("el-col",{staticStyle:{"text-align":"right"},attrs:{span:24}},[a("el-button",{attrs:{type:"primary"},on:{click:t.submitForm}},[t._v("提交")]),t._v(" "),a("el-button",{on:{click:t.resetForm}},[t._v("重置")])],1)],1)],1)],1)},staticRenderFns:[]}}});