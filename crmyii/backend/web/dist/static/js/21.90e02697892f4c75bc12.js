webpackJsonp([21],{260:function(t,e,i){function s(t){i(866)}var n=i(7)(i(856),i(877),s,"data-v-4ff35bc6",null);t.exports=n.exports},856:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default={computed:{visitedViews:function(){return this.$store.state.app.visitedViews.slice(-6)}},methods:{closeViewTabs:function(t,e){this.$store.dispatch("delVisitedViews",t),e.preventDefault()},generateRoute:function(){return this.$route.matched[this.$route.matched.length-1].name?this.$route.matched[this.$route.matched.length-1]:(this.$route.matched[0].path="/",this.$route.matched[0])},addViewTabs:function(){this.$store.dispatch("addVisitedViews",this.generateRoute())}},watch:{$route:function(){this.addViewTabs()}}}},860:function(t,e,i){e=t.exports=i(252)(!0),e.push([t.i,".tabs-view-container[data-v-4ff35bc6]{display:inline-block;vertical-align:top;margin-left:10px}.tabs-view-container .tabs-view[data-v-4ff35bc6]{margin-left:10px}","",{version:3,sources:["C:/Users/jy/Desktop/github/yushan-material-lib/src/views/layout/TabsView.vue"],names:[],mappings:"AACA,sCACE,qBAAsB,AACtB,mBAAoB,AACpB,gBAAkB,CACnB,AACD,iDACI,gBAAkB,CACrB",file:"TabsView.vue",sourcesContent:["\n.tabs-view-container[data-v-4ff35bc6] {\n  display: inline-block;\n  vertical-align: top;\n  margin-left: 10px;\n}\n.tabs-view-container .tabs-view[data-v-4ff35bc6] {\n    margin-left: 10px;\n}\n"],sourceRoot:""}])},866:function(t,e,i){var s=i(860);"string"==typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);i(253)("27b01ed2",s,!0)},877:function(t,e){t.exports={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"tabs-view-container"},t._l(Array.from(t.visitedViews),function(e){return i("router-link",{key:e.path,staticClass:"tabs-view",attrs:{to:e.path}},[i("el-tag",{attrs:{closable:!0},on:{close:function(i){t.closeViewTabs(e,i)}}},[t._v("\n      "+t._s(e.name)+"\n    ")])],1)}))},staticRenderFns:[]}}});
//# sourceMappingURL=21.90e02697892f4c75bc12.js.map