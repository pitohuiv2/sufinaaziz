!function(){function t(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}function n(t,n){for(var a=0;a<n.length;a++){var e=n[a];e.enumerable=e.enumerable||!1,e.configurable=!0,"value"in e&&(e.writable=!0),Object.defineProperty(t,e.key,e)}}(window.webpackJsonp=window.webpackJsonp||[]).push([[21],{jPp5:function(a,e,o){"use strict";o.r(e),o.d(e,"BedOnlineModule",function(){return Q});var i=o("ofXK"),r=o("tyNb"),s=o("fXoL"),c=o("EJUL"),b=o("ujBT"),l=o("rEr+"),d=o("7zfz"),g=o("jeV5");function p(t,n){1&t&&(s.Qb(0,"div",21),s.Lb(1,"p-skeleton",22),s.Pb())}function h(t,n){if(1&t&&(s.Qb(0,"div",11),s.Hc(1,p,2,0,"div",20),s.Pb()),2&t){var a=s.bc();s.xb(1),s.ic("ngForOf",a.numberss)}}function f(t,n){if(1&t&&(s.Qb(0,"div",23),s.Qb(1,"div"),s.Qb(2,"div",24),s.Qb(3,"h6"),s.Jc(4),s.Pb(),s.Qb(5,"h1"),s.Jc(6),s.Pb(),s.Qb(7,"div",25),s.Lb(8,"div",26),s.Pb(),s.Qb(9,"p"),s.Jc(10,"Tersedia"),s.Pb(),s.Pb(),s.Lb(11,"i",27),s.Pb(),s.Pb()),2&t){var a=n.$implicit;s.xb(1),s.Ab("cards overview-box ",a.color,""),s.xb(3),s.Kc(a.namakelas),s.xb(2),s.Kc(a.tersedia),s.xb(2),s.Fc("width: ",a.persen,"%;")}}function P(t,n){1&t&&(s.Qb(0,"tr"),s.Qb(1,"th",28),s.Jc(2,"No"),s.Pb(),s.Qb(3,"th",29),s.Jc(4,"Kelas"),s.Pb(),s.Qb(5,"th",30),s.Jc(6,"Ruangan"),s.Pb(),s.Qb(7,"th",31),s.Jc(8,"Kapasitas"),s.Pb(),s.Qb(9,"th",31),s.Jc(10,"Terisi"),s.Pb(),s.Qb(11,"th",31),s.Jc(12,"Tersedia"),s.Pb(),s.Pb())}var u=function(t){return{"row-kosong":t}};function m(t,n){if(1&t&&(s.Qb(0,"tr",32),s.Qb(1,"td",28),s.Qb(2,"span",33),s.Jc(3,"No "),s.Pb(),s.Jc(4),s.Pb(),s.Qb(5,"td",29),s.Qb(6,"span",33),s.Jc(7,"Kelas "),s.Pb(),s.Jc(8),s.Pb(),s.Qb(9,"td",30),s.Qb(10,"span",33),s.Jc(11,"Ruangan "),s.Pb(),s.Jc(12),s.Pb(),s.Qb(13,"td",31),s.Qb(14,"span",33),s.Jc(15," Kapasitas "),s.Pb(),s.Jc(16),s.Pb(),s.Qb(17,"td",31),s.Qb(18,"span",33),s.Jc(19," Terisi "),s.Pb(),s.Jc(20),s.Pb(),s.Qb(21,"td",31),s.Qb(22,"span",33),s.Jc(23," Tersedia"),s.Pb(),s.Jc(24),s.Pb(),s.Pb()),2&t){var a=n.$implicit;s.ic("ngClass",s.nc(7,u,0==a.tersedia)),s.xb(4),s.Lc(" ",a.no," "),s.xb(4),s.Lc(" ",a.namakelas," "),s.xb(4),s.Lc(" ",a.namaruangan," "),s.xb(4),s.Lc(" ",a.kapasitas," "),s.xb(4),s.Lc(" ",a.terisi," "),s.xb(4),s.Lc(" ",a.tersedia," ")}}var v,_,x,O=[{path:"",component:(v=function(){function a(n,e){t(this,a),this.apiService=n,this.alertService=e,this.item={},this.dataSource=[],this.color=["white","blue","gray","darkgray","orange"],this.listKelas=[],this.value=10,this.count_defaultTable=15,this.countTable=this.count_defaultTable,this.totalPage=3,this.page_otomatis=1,this.page_default=1,this.start_otomatis=0,this.limit_otomatis=10,this.count_defaultPanel=60,this.countPanel=this.count_defaultPanel,this.numberss=Array(5).map(function(t,n){return n})}var e,o,i;return e=a,(o=[{key:"ngOnInit",value:function(){var t=this;this.loadTable(this.start_otomatis,this.limit_otomatis),this.loadKelas(),this.apiTimer=setInterval(function(){t.timerTable(),t.timerPanel()},1e3)}},{key:"timerPanel",value:function(){this.countPanel=this.countPanel-1,this.countPanel<=0&&(this.countPanel=this.count_defaultPanel,this.loadKelas()),this.timerPanels=this.countPanel+" Detik"}},{key:"timerTable",value:function(){if(this.countTable=this.countTable-1,this.countTable<=0){this.countTable=this.count_defaultTable,this.page_otomatis=this.page_otomatis+1,this.page_otomatis>this.totalPage&&(this.page_otomatis=this.page_default),this.start_otomatis=0,this.limit_otomatis=10;for(var t=1;t<=this.totalPage;t++)t==this.page_otomatis&&this.loadTable(this.start_otomatis,this.limit_otomatis),this.start_otomatis=this.start_otomatis+10}this.infoPage=this.page_otomatis+" dari "+this.totalPage+" Halaman",this.timerTables=this.countTable+" Detik"}},{key:"loadTable",value:function(t,n){var a=this;this.dataSource=[],this.apiService.get("bed/get-ruangan?offset="+t+"&limit="+n).subscribe(function(n){for(var e=0;e<n.length;e++)n[e].no=t+e+1;a.dataSource=n})}},{key:"loadKelas",value:function(){var t=this;this.listKelas=[],this.apiService.get("bed/get-kelas").subscribe(function(n){for(var a=0,e=0;e<n.length;e++){var o=n[e];null==t.color[a]&&(a=0),o.sisa=parseFloat(o.kapasitas)-parseFloat(o.tersedia),o.persen=parseFloat(o.sisa)/parseFloat(o.kapasitas)*100,o.color=t.color[a],a+=1}t.listKelas=n})}}])&&n(e.prototype,o),i&&n(e,i),a}(),v.\u0275fac=function(t){return new(t||v)(s.Jb(c.a),s.Jb(b.a))},v.\u0275cmp=s.Db({type:v,selectors:[["app-bed-online"]],decls:33,vars:7,consts:[[1,"layout-dashboard"],[1,"p-grid"],[1,"p-col-12"],[1,"notification"],[1,"p-col-12","p-md-4"],[1,"cards","timeline",2,"margin-bottom","1rem"],[1,"card-header",2,"padding","0"],[1,"card-title"],[1,"subtitle"],[1,"pi","pi-clock"],["class","p-grid","style","margin: -1rem;",4,"ngIf"],[1,"p-grid",2,"margin","-1rem"],["class","p-col",4,"ngFor","ngForOf"],[1,"p-col-12","p-md-8"],[1,"cards","timeline"],[1,"card-header"],[1,"card-body"],["styleClass","p-datatable-customers","scrollable","true",3,"value","rowHover"],["pTemplate","header"],["pTemplate","body"],["class","p-col-12 p-md-6","style","padding: 10px",4,"ngFor","ngForOf"],[1,"p-col-12","p-md-6",2,"padding","10px"],["height","10rem","borderRadius","24px"],[1,"p-col"],[1,"overview-info"],[1,"progressbar"],[1,"progress"],[1,"fa","fa-bed"],["width","30px"],["width","150px"],["width","200px"],["width","100px"],[3,"ngClass"],[1,"p-column-title"]],template:function(t,n){1&t&&(s.Qb(0,"div",0),s.Qb(1,"div",1),s.Qb(2,"div",2),s.Qb(3,"div",3),s.Qb(4,"h6"),s.Jc(5,"\ud83d\udc4b Bed Online RSUD Pagelaran"),s.Pb(),s.Pb(),s.Pb(),s.Qb(6,"div",4),s.Qb(7,"div",5),s.Qb(8,"div",6),s.Qb(9,"div",7),s.Qb(10,"h6"),s.Jc(11," Bed Berdasarkan Kelas"),s.Pb(),s.Pb(),s.Qb(12,"p",8),s.Lb(13,"i",9),s.Jc(14),s.Pb(),s.Pb(),s.Pb(),s.Hc(15,h,2,1,"div",10),s.Qb(16,"div",11),s.Hc(17,f,12,8,"div",12),s.Pb(),s.Pb(),s.Qb(18,"div",13),s.Qb(19,"div",14),s.Qb(20,"div",15),s.Qb(21,"div",7),s.Qb(22,"h6"),s.Jc(23," Bed Berdasarkan Ruangan"),s.Pb(),s.Pb(),s.Qb(24,"p",8),s.Lb(25,"i",9),s.Jc(26),s.Pb(),s.Pb(),s.Qb(27,"div",16),s.Qb(28,"div",1),s.Qb(29,"div",2),s.Qb(30,"p-table",17),s.Hc(31,P,13,0,"ng-template",18),s.Hc(32,m,25,9,"ng-template",19),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Pb()),2&t&&(s.xb(14),s.Lc(" ",n.timerPanels,""),s.xb(1),s.ic("ngIf",0==n.listKelas.length),s.xb(2),s.ic("ngForOf",n.listKelas),s.xb(9),s.Mc(" ",n.timerTables," ",n.infoPage,""),s.xb(4),s.ic("value",n.dataSource)("rowHover",!0))},directives:[i.l,i.k,l.h,d.k,g.a,i.j],styles:["body[_ngcontent-%COMP%]{color:rgba(41,50,65,.8);padding:0;margin:0;min-height:100%;background-color:#f2f4f6;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.layout-dashboard[_ngcontent-%COMP%]{padding:30px 36px;flex:1 1 auto}.notification[_ngcontent-%COMP%]{padding:30px 24px;background-color:#fff;border-radius:20px}.notification[_ngcontent-%COMP%] > h6[_ngcontent-%COMP%]{margin:0;color:rgba(41,50,65,.8)}.notification[_ngcontent-%COMP%] > h6[_ngcontent-%COMP%] > a[_ngcontent-%COMP%]{margin-left:10px}.notification[_ngcontent-%COMP%] > h6[_ngcontent-%COMP%] > a[_ngcontent-%COMP%]   i[_ngcontent-%COMP%]{transform:rotate(45deg)}.overview-box.white[_ngcontent-%COMP%]{background:#fff;color:rgba(41,50,65,.8)}.card[_ngcontent-%COMP%]:last-child{margin-bottom:0}.cards[_ngcontent-%COMP%]{background:#fff;padding:20px;box-shadow:0 10px 40px rgba(41,50,65,.06);border-radius:24px;margin-bottom:2rem}.cards[_ngcontent-%COMP%], .p-grid[_ngcontent-%COMP%] > .p-col[_ngcontent-%COMP%], .p-grid[_ngcontent-%COMP%] > [class*=p-col][_ngcontent-%COMP%]{box-sizing:border-box}.p-col[_ngcontent-%COMP%]{flex-grow:1;flex-basis:0;padding:1rem}.overview-box[_ngcontent-%COMP%]   .overview-info[_ngcontent-%COMP%] > h6[_ngcontent-%COMP%]{margin:0 0 2px}.overview-box[_ngcontent-%COMP%]   .overview-info[_ngcontent-%COMP%] > h1[_ngcontent-%COMP%]{margin:0}.overview-box.gray[_ngcontent-%COMP%]{background:rgba(41,50,65,.4);color:#fff}.overview-box.darkgray[_ngcontent-%COMP%]{background:rgba(41,50,65,.8);color:#fff}.overview-box.orange[_ngcontent-%COMP%]{background:linear-gradient(90deg,#ffb340,#ffa740);color:#fff}.p-datatable[_ngcontent-%COMP%]   .p-datatable-tbody[_ngcontent-%COMP%] > tr.row-kosong[_ngcontent-%COMP%], .p-datatable[_ngcontent-%COMP%]   .p-datatable-tbody[_ngcontent-%COMP%] > tr[_ngcontent-%COMP%]   .row-kosong[_ngcontent-%COMP%], [_nghost-%COMP%]     .row-kosong{background-color:#fc6161;box-shadow:0 6px 20px rgba(252,97,97,.3);color:#fff}.progressbar[_ngcontent-%COMP%]{margin:auto 10px;height:5px;padding:0;width:200%;background-color:#eaf0f6;border-radius:5px}.progressbar[_ngcontent-%COMP%]   .progress[_ngcontent-%COMP%]{height:5px;background-image:linear-gradient(270deg,#42bbff,#6129ff);opacity:.8;border-radius:5px}.cards[_ngcontent-%COMP%]   .card-header[_ngcontent-%COMP%]{display:flex;justify-content:space-between;padding-bottom:16px}.overview-box.blue[_ngcontent-%COMP%]{background:#69b7ff;color:#fff}.cards[_ngcontent-%COMP%]:last-child{margin-bottom:0}.overview-box[_ngcontent-%COMP%]{display:flex;justify-content:space-between;padding-top:24px;height:100%;min-width:200px}.cards[_ngcontent-%COMP%]   .card-header[_ngcontent-%COMP%]   .subtitle[_ngcontent-%COMP%]{font-weight:600;color:rgba(41,50,65,.5)}p[_ngcontent-%COMP%]:last-child{margin-bottom:0}p[_ngcontent-%COMP%]{margin:0 0 1rem;line-height:1.5}"]}),v)}],C=((_=function n(){t(this,n)}).\u0275mod=s.Hb({type:_}),_.\u0275inj=s.Gb({factory:function(t){return new(t||_)},imports:[[r.j.forChild(O)],r.j]}),_),M=o("PCNd"),Q=((x=function n(){t(this,n)}).\u0275mod=s.Hb({type:x}),x.\u0275inj=s.Gb({factory:function(t){return new(t||x)},imports:[[i.b,C,M.a]]}),x)}}])}();