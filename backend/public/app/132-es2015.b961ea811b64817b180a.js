(window.webpackJsonp=window.webpackJsonp||[]).push([[132],{Ox9s:function(e,i,t){"use strict";t.r(i),t.d(i,"MappingModulModule",function(){return B});var n=t("ofXK"),l=t("tyNb"),o=t("7zfz"),a=t("fXoL"),b=t("EJUL"),u=t("U+s4"),d=t("G0w9"),p=t("ujBT"),s=t("7CaW"),c=t("jIHw"),r=t("Q4Mo"),h=t("xlun"),m=t("7kUa"),g=t("rEr+"),f=t("Y+ZO"),M=t("3Pt+"),P=t("g9iH"),S=t("/RsI"),v=t("arFO");function Q(e,i){if(1&e&&(a.Qb(0,"th",47),a.Jc(1),a.Lb(2,"p-sortIcon",48),a.Pb()),2&e){const e=i.$implicit;a.Fc("width: ",e.width,""),a.jc("pSortableColumn",e.field),a.xb(1),a.Lc(" ",e.header," "),a.xb(1),a.jc("field",e.field)}}function x(e,i){if(1&e&&(a.Qb(0,"tr"),a.Hc(1,Q,3,6,"th",46),a.Pb()),2&e){const e=i.$implicit;a.xb(1),a.ic("ngForOf",e)}}function k(e,i){if(1&e&&(a.Qb(0,"td"),a.Jc(1),a.Pb()),2&e){const e=i.$implicit,t=a.bc().$implicit;a.Fc("width: ",e.width,""),a.xb(1),a.Lc(" ",t[e.field]," ")}}function w(e,i){if(1&e&&(a.Qb(0,"tr",49),a.Hc(1,k,2,4,"td",50),a.Pb()),2&e){const e=i.columns,t=i.rowIndex;a.ic("pSelectableRow",i.$implicit)("pSelectableRowIndex",t),a.xb(1),a.ic("ngForOf",e)}}function C(e,i){if(1&e&&(a.Qb(0,"th",47),a.Jc(1),a.Lb(2,"p-sortIcon",48),a.Pb()),2&e){const e=i.$implicit;a.Fc("width: ",e.width,""),a.jc("pSortableColumn",e.field),a.xb(1),a.Lc(" ",e.header," "),a.xb(1),a.jc("field",e.field)}}function y(e,i){if(1&e&&(a.Qb(0,"tr"),a.Hc(1,C,3,6,"th",46),a.Pb()),2&e){const e=i.$implicit;a.xb(1),a.ic("ngForOf",e)}}function H(e,i){if(1&e&&(a.Qb(0,"td"),a.Jc(1),a.Pb()),2&e){const e=i.$implicit,t=a.bc().$implicit;a.Fc("width: ",e.width,""),a.xb(1),a.Lc(" ",t[e.field]," ")}}function I(e,i){if(1&e&&(a.Qb(0,"tr",49),a.Hc(1,H,2,4,"td",50),a.Pb()),2&e){const e=i.columns,t=i.rowIndex;a.ic("pSelectableRow",i.$implicit)("pSelectableRowIndex",t),a.xb(1),a.ic("ngForOf",e)}}function T(e,i){if(1&e&&(a.Qb(0,"b",51),a.Jc(1),a.Pb()),2&e){const e=i.$implicit;a.kc("pTooltip","No Urut : ",e.nourut,""),a.xb(1),a.Lc("",e.subCategoryName," ")}}function J(e,i){if(1&e){const e=a.Sb();a.Qb(0,"div",32),a.Qb(1,"label",26),a.Jc(2,"Sub Sistem"),a.Pb(),a.Qb(3,"p-dropdown",52),a.Yb("ngModelChange",function(i){return a.yc(e),a.bc().pop.subSistemHead=i}),a.Pb(),a.Pb()}if(2&e){const e=a.bc();a.xb(3),a.ic("options",e.d_modulAplikasiHead)("ngModel",e.pop.subSistemHead)("filter",!0)("showClear",!0)}}function Y(e,i){if(1&e){const e=a.Sb();a.Qb(0,"div",32),a.Qb(1,"label",26),a.Jc(2,"Modul Aplikasi"),a.Pb(),a.Qb(3,"input",53),a.Yb("ngModelChange",function(i){return a.yc(e),a.bc().pop.modulaplikasi=i}),a.Pb(),a.Pb()}if(2&e){const e=a.bc();a.xb(3),a.ic("ngModel",e.pop.modulaplikasi)}}function L(e,i){if(1&e){const e=a.Sb();a.Qb(0,"div",32),a.Qb(1,"label",26),a.Jc(2,"Sub Sistem"),a.Pb(),a.Qb(3,"input",54),a.Yb("ngModelChange",function(i){return a.yc(e),a.bc().pop.modulaplikasi=i}),a.Pb(),a.Pb()}if(2&e){const e=a.bc();a.xb(3),a.ic("ngModel",e.pop.modulaplikasi)}}function F(e,i){if(1&e){const e=a.Sb();a.Qb(0,"button",55),a.Yb("click",function(){return a.yc(e),a.bc().simpanModul()}),a.Pb(),a.Qb(1,"button",56),a.Yb("click",function(){return a.yc(e),a.bc().pop_Tambah=!1}),a.Pb()}if(2&e){const e=a.bc();a.ic("disabled",e.isSimpan)}}const R=function(){return[5,10,25,50]},j=function(){return["modulaplikasi"]},N=function(){return{width:"100%",height:"280px",overflow:"auto"}},U=function(){return{width:"900px",minWidth:"900px"}},A=[{path:"",component:(()=>{class e{constructor(e,i,t,n,l,o,a,b,u){this.apiService=e,this.authService=i,this.confirmationService=t,this.messageService=n,this.cacheHelper=l,this.dateHelper=o,this.alertService=a,this.route=b,this.router=u,this.item={},this.d_jenis=[{name:"Menu"},{name:"Modul"}],this.treeSourceMenu=[],this.isLoadingNav=!1,this.isSimpan=!1,this.checked2=!0,this.pop_Tambah=!1,this.pop={}}ngOnInit(){this.loadColumn(),this.loadModul()}loadColumn(){this.columnSubSistem=[{field:"no",header:"No",width:"40px"},{field:"modulaplikasi",header:"Sub Sistem",width:"200px"}],this.columnModulApp=[{field:"no",header:"No",width:"40px"},{field:"modulaplikasi",header:"Modul Aplikasi",width:"200px"}]}loadModul(){this.apiService.get("modul/get-modul-aplikasi?jenis=subsistem").subscribe(e=>{for(let i=0;i<e.length;i++)e[i].no=i+1;this.dsSubSistem=e,this.d_modulAplikasiHead=e})}onSelectSub(e){this.apiService.get("modul/get-modul-aplikasi?jenis=modulaplikasi&id="+e.data.id).subscribe(e=>{for(let i=0;i<e.length;i++)e[i].no=i+1;this.dsModulApp=e})}onSelectModul(e){this.treeSourceMenu=[],this.isLoadingNav=!0,this.item={},this.idModul=e.data.id,this.apiService.get("modul/get-modul-aplikasi?jenis=objekMenuRecursive&id="+e.data.id).subscribe(e=>{this.isLoadingNav=!1,this.treeSourceMenu=e})}nodeSelect(e){let i=e.node;this.item.idMenu=i.id,0!=i.parent_id&&(this.item.idHeadMenu=i.parent_id),this.item.nmMenu=i.label,this.item.fungsi=i.fungsi,this.item.keterangan=i.keterangan,this.item.noUrut2=i.nourut,this.item.url=i.alamaturlform}cancel(){this.item={}}save(){null!=this.item.nmMenu?null!=this.idModul?(this.isSimpan=!0,this.apiService.post("modul/simpan-objek-modul-aplikasi",{id:null==this.item.idMenu?0:this.item.idMenu,fungsi:this.item.fungsi,keterangan:this.item.keterangan,objekmodulaplikasi:this.item.nmMenu,nourut:this.item.noUrut2,alamaturlform:null!=this.item.url?this.item.url:null,kdobjekmodulaplikasihead:null!=this.item.idHeadMenu?this.item.idHeadMenu:null,modulaplikasiid:this.idModul}).subscribe(e=>{this.onSelectModul({data:{id:this.idModul}}),this.isSimpan=!1,this.expandAll(),this.cancel()})):this.alertService.warn("Info","Pilih Modul Aplikasi!"):this.alertService.warn("Info","Nama Menu belum di isi!")}hapus(){null!=this.item.idMenu?this.apiService.post("modul/hapus-objek-modul-aplikasi",{id:this.item.idMenu}).subscribe(e=>{this.onSelectModul({data:{id:this.idModul}})}):this.alertService.warn("Info"," Menu belum di pilih!")}expandAll(){this.treeSourceMenu.forEach(e=>{this.expandRecursive(e,!0)})}collapseAll(){this.treeSourceMenu.forEach(e=>{this.expandRecursive(e,!1)})}handleChange(e){0==e.checked?this.expandAll():this.collapseAll()}expandRecursive(e,i){e.expanded=i,e.children&&e.children.forEach(e=>{this.expandRecursive(e,i)})}tambah(e){this.isModul="Sub Sistem"!=e,this.pop.judul=e,this.pop_Tambah=!0}simpanModul(){null!=this.pop.modulaplikasi?1!=this.isModul||null!=this.pop.subSistemHead?this.apiService.post("modul/save-modul-aplikasi",{id:null!=this.pop.id?this.pop.id:"",modulaplikasi:this.pop.modulaplikasi,nourut:null!=this.pop.noUrut?this.pop.noUrut:null,reportdisplay:1==this.isModul?"Menu":"Modul",kdmodulaplikasihead:null!=this.pop.subSistemHead?this.pop.subSistemHead.id:null}).subscribe(e=>{1==this.isModul?this.onSelectSub({data:{id:this.pop.subSistemHead.id}}):this.loadModul()}):this.alertService.warn("Info","Sub Sistem harus di pilih"):this.alertService.warn("Info","Nama Modul/Sub Sistem harus di isi")}hapusSub(){null!=this.selectedSub?this.apiService.post("modul/hapus-modul-aplikasi",{id:this.selectedSub.id}).subscribe(e=>{this.loadModul()}):this.alertService.warn("Info","Pilih data dulu")}hapusModul(){null!=this.selectedMod?this.apiService.post("modul/hapus-modul-aplikasi",{id:this.selectedMod.id}).subscribe(e=>{null!=this.selectedSub?this.onSelectSub({data:{id:this.selectedSub.id}}):this.loadModul()}):this.alertService.warn("Info","Pilih data dulu")}}return e.\u0275fac=function(i){return new(i||e)(a.Jb(b.a),a.Jb(b.b),a.Jb(o.a),a.Jb(o.h),a.Jb(u.a),a.Jb(d.a),a.Jb(p.a),a.Jb(l.a),a.Jb(l.f))},e.\u0275cmp=a.Db({type:e,selectors:[["app-mapping-modul"]],features:[a.wb([o.a])],decls:102,vars:58,consts:[[1,"p-fluid","p-formgrid"],[1,"card","card-w-title"],[1,"p-grid"],[1,"p-col-12","p-md-12"],[1,"p-col-12","p-md-3"],["header","Sub Sistem",3,"toggleable"],[1,"p-col-12","p-md-2"],["type","button","pButton","","pRipple","","icon","pi pi-plus","pTooltip","Tambah","tooltipPosition","bottom",1,"p-button-success","p-mr-2",3,"click"],["type","button","pButton","","pRipple","","icon","pi pi-trash","pTooltip","Hapus","tooltipPosition","bottom",1,"p-button-danger","p-mr-2",3,"click"],[1,"p-col-12","p-md-8"],[1,"p-input-icon-left"],[1,"pi","pi-search"],["pInputText","","type","text","placeholder","Pencarian ",3,"input"],[1,"p-col-12"],["styleClass","p-datatable-customers","scrollable","true","sortMode","multiple","selectionMode","single",3,"columns","value","selection","rowHover","rows","showCurrentPageReport","rowsPerPageOptions","paginator","pageLinks","globalFilterFields","selectionChange","onRowSelect"],["dt",""],["pTemplate","header"],["pTemplate","body"],["header","Modul Aplikasi",3,"toggleable"],["dt2",""],[1,"p-col-12","p-md-6"],["header","Menu",3,"toggleable"],["onLabel","Expand","offLabel","Collapsed","onIcon","fa fa-expand","offIcon","fa fa-collapsed",3,"ngModel","ngModelChange","onChange"],["selectionMode","single",3,"value","loading","selection","filter","selectionChange","onNodeSelect"],["pTemplate","default"],["header","Detail Menu",3,"toggleable"],["for","input",1,"label"],["type","text","pInputText","","placeholder","Id Head","pTooltip","di isi ID, dari menu dikanan atas",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Id Menu","pTooltip"," ","disabled","",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-5"],["type","text","pInputText","","placeholder","Nama Menu",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Fungsi ",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-4"],["type","text","pInputText","","placeholder","Keterangan ",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","No Urut",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","URL",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-2","p-md-offset-6"],["pButton","","type","button","icon","pi pi-save","pTooltip","Simpan","label","Simpan",1,"p-button","p-mr-2",3,"disabled","click"],["pButton","","type","button","icon","pi pi-trash","pTooltip","Hapus","label","Hapus",1,"p-button","p-button-warning","p-mr-2",3,"click"],["pButton","","type","button","icon","pi pi-arrow-left","pTooltip","Batal","label","Batal",1,"p-button","p-button-danger","p-mr-2",3,"click"],["header","Tambah ",3,"visible","modal","maximizable","draggable","resizable","visibleChange"],[1,"p-fluid","p-grid"],[3,"header","toggleable"],["class","p-col-12 p-md-4",4,"ngIf"],["type","number","pInputText","","placeholder","No Urut ",3,"ngModel","ngModelChange"],["pTemplate","footer"],[3,"pSortableColumn","style",4,"ngFor","ngForOf"],[3,"pSortableColumn"],[3,"field"],[3,"pSelectableRow","pSelectableRowIndex"],[3,"style",4,"ngFor","ngForOf"],["tooltipPosition","bottom",3,"pTooltip"],["placeholder","Sub Sistem","optionLabel","modulaplikasi","appendTo","body",3,"options","ngModel","filter","showClear","ngModelChange"],["type","text","pInputText","","placeholder","Modul Aplikasi",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Sub Sistem",3,"ngModel","ngModelChange"],["pButton","","icon","pi pi-save","label","Simpan",1,"p-button","p-button-success","p-mr-2",3,"disabled","click"],["pButton","","icon","fa fa-refresh","label","Batal",1,"p-button","p-mr-2",3,"click"]],template:function(e,i){if(1&e){const e=a.Sb();a.Qb(0,"div",0),a.Qb(1,"div",1),a.Qb(2,"h4"),a.Jc(3,"Setting Menu"),a.Pb(),a.Qb(4,"div",2),a.Qb(5,"div",3),a.Qb(6,"div",2),a.Qb(7,"div",4),a.Qb(8,"p-panel",5),a.Qb(9,"div",2),a.Qb(10,"div",6),a.Qb(11,"button",7),a.Yb("click",function(){return i.tambah("Sub Sistem")}),a.Pb(),a.Pb(),a.Qb(12,"div",6),a.Qb(13,"button",8),a.Yb("click",function(){return i.hapusSub()}),a.Pb(),a.Pb(),a.Qb(14,"div",9),a.Qb(15,"span",10),a.Lb(16,"i",11),a.Qb(17,"input",12),a.Yb("input",function(i){return a.yc(e),a.wc(20).filterGlobal(i.target.value,"contains")}),a.Pb(),a.Pb(),a.Pb(),a.Qb(18,"div",13),a.Qb(19,"p-table",14,15),a.Yb("selectionChange",function(e){return i.selectedSub=e})("onRowSelect",function(e){return i.onSelectSub(e)}),a.Hc(21,x,2,1,"ng-template",16),a.Hc(22,w,2,3,"ng-template",17),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Qb(23,"div",4),a.Qb(24,"p-panel",18),a.Qb(25,"div",2),a.Qb(26,"div",6),a.Qb(27,"button",7),a.Yb("click",function(){return i.tambah("Modul Aplikasi")}),a.Pb(),a.Pb(),a.Qb(28,"div",6),a.Qb(29,"button",8),a.Yb("click",function(){return i.hapusModul()}),a.Pb(),a.Pb(),a.Qb(30,"div",9),a.Qb(31,"span",10),a.Lb(32,"i",11),a.Qb(33,"input",12),a.Yb("input",function(i){return a.yc(e),a.wc(36).filterGlobal(i.target.value,"contains")}),a.Pb(),a.Pb(),a.Pb(),a.Qb(34,"div",13),a.Qb(35,"p-table",14,19),a.Yb("selectionChange",function(e){return i.selectedMod=e})("onRowSelect",function(e){return i.onSelectModul(e)}),a.Hc(37,y,2,1,"ng-template",16),a.Hc(38,I,2,3,"ng-template",17),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Qb(39,"div",20),a.Qb(40,"div",2),a.Qb(41,"div",3),a.Qb(42,"p-panel",21),a.Qb(43,"div",2),a.Qb(44,"div",6),a.Qb(45,"p-toggleButton",22),a.Yb("ngModelChange",function(e){return i.checked2=e})("onChange",function(e){return i.handleChange(e)}),a.Pb(),a.Pb(),a.Lb(46,"div",6),a.Pb(),a.Pb(),a.Pb(),a.Qb(47,"div",3),a.Qb(48,"p-tree",23),a.Yb("selectionChange",function(e){return i.selectedFile=e})("onNodeSelect",function(e){return i.nodeSelect(e)}),a.Hc(49,T,2,2,"ng-template",24),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Qb(50,"div",20),a.Qb(51,"p-panel",25),a.Qb(52,"div",2),a.Qb(53,"div",6),a.Qb(54,"label",26),a.Jc(55,"Id Head"),a.Pb(),a.Qb(56,"input",27),a.Yb("ngModelChange",function(e){return i.item.idHeadMenu=e}),a.Pb(),a.Pb(),a.Pb(),a.Qb(57,"div",2),a.Qb(58,"div",6),a.Qb(59,"label",26),a.Jc(60,"Id Menu"),a.Pb(),a.Qb(61,"input",28),a.Yb("ngModelChange",function(e){return i.item.idMenu=e}),a.Pb(),a.Pb(),a.Qb(62,"div",29),a.Qb(63,"label",26),a.Jc(64,"Nama Menu"),a.Pb(),a.Qb(65,"input",30),a.Yb("ngModelChange",function(e){return i.item.nmMenu=e}),a.Pb(),a.Pb(),a.Qb(66,"div",29),a.Qb(67,"label",26),a.Jc(68,"Fungsi"),a.Pb(),a.Qb(69,"input",31),a.Yb("ngModelChange",function(e){return i.item.fungsi=e}),a.Pb(),a.Pb(),a.Qb(70,"div",32),a.Qb(71,"label",26),a.Jc(72,"Keterangan"),a.Pb(),a.Qb(73,"input",33),a.Yb("ngModelChange",function(e){return i.item.keterangan=e}),a.Pb(),a.Pb(),a.Qb(74,"div",32),a.Qb(75,"label",26),a.Jc(76,"No Urut"),a.Pb(),a.Qb(77,"input",34),a.Yb("ngModelChange",function(e){return i.item.noUrut2=e}),a.Pb(),a.Pb(),a.Qb(78,"div",32),a.Qb(79,"label",26),a.Jc(80,"URL"),a.Pb(),a.Qb(81,"input",35),a.Yb("ngModelChange",function(e){return i.item.url=e}),a.Pb(),a.Pb(),a.Pb(),a.Qb(82,"div",2),a.Qb(83,"div",36),a.Qb(84,"button",37),a.Yb("click",function(){return i.save()}),a.Pb(),a.Pb(),a.Qb(85,"div",6),a.Qb(86,"button",38),a.Yb("click",function(){return i.hapus()}),a.Pb(),a.Pb(),a.Qb(87,"div",6),a.Qb(88,"button",39),a.Yb("click",function(){return i.cancel()}),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Qb(89,"p-dialog",40),a.Yb("visibleChange",function(e){return i.pop_Tambah=e}),a.Qb(90,"div",41),a.Qb(91,"div",3),a.Qb(92,"p-panel",42),a.Qb(93,"div",2),a.Hc(94,J,4,4,"div",43),a.Hc(95,Y,4,1,"div",43),a.Hc(96,L,4,1,"div",43),a.Qb(97,"div",32),a.Qb(98,"label",26),a.Jc(99,"No Urut"),a.Pb(),a.Qb(100,"input",44),a.Yb("ngModelChange",function(e){return i.pop.noUrut=e}),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Pb(),a.Hc(101,F,2,1,"ng-template",45),a.Pb()}2&e&&(a.xb(8),a.ic("toggleable",!0),a.xb(11),a.ic("columns",i.columnSubSistem)("value",i.dsSubSistem)("selection",i.selectedSub)("rowHover",!0)("rows",20)("showCurrentPageReport",!0)("rowsPerPageOptions",a.mc(52,R))("paginator",!0)("pageLinks",5)("globalFilterFields",a.mc(53,j)),a.xb(5),a.ic("toggleable",!0),a.xb(11),a.ic("columns",i.columnModulApp)("value",i.dsModulApp)("selection",i.selectedMod)("rowHover",!0)("rows",20)("showCurrentPageReport",!0)("rowsPerPageOptions",a.mc(54,R))("paginator",!0)("pageLinks",5)("globalFilterFields",a.mc(55,j)),a.xb(7),a.ic("toggleable",!0),a.xb(3),a.ic("ngModel",i.checked2),a.xb(3),a.Ec(a.mc(56,N)),a.ic("value",i.treeSourceMenu)("loading",i.isLoadingNav)("selection",i.selectedFile)("filter",!0),a.xb(3),a.ic("toggleable",!0),a.xb(5),a.ic("ngModel",i.item.idHeadMenu),a.xb(5),a.ic("ngModel",i.item.idMenu),a.xb(4),a.ic("ngModel",i.item.nmMenu),a.xb(4),a.ic("ngModel",i.item.fungsi),a.xb(4),a.ic("ngModel",i.item.keterangan),a.xb(4),a.ic("ngModel",i.item.noUrut2),a.xb(4),a.ic("ngModel",i.item.url),a.xb(3),a.ic("disabled",i.isSimpan),a.xb(5),a.Ec(a.mc(57,U)),a.ic("visible",i.pop_Tambah)("modal",!0)("maximizable",!0)("draggable",!0)("resizable",!0),a.xb(3),a.kc("header","Detail ",i.pop.judul,""),a.ic("toggleable",!0),a.xb(2),a.ic("ngIf",i.isModul),a.xb(1),a.ic("ngIf",i.isModul),a.xb(1),a.ic("ngIf",!i.isModul),a.xb(4),a.ic("ngModel",i.pop.noUrut))},directives:[s.a,c.b,r.a,h.a,m.a,g.h,o.k,f.a,M.g,M.h,P.a,M.a,S.a,n.l,M.i,n.k,g.g,g.f,g.e,v.a],styles:[".my-tree[_ngcontent-%COMP%], [_nghost-%COMP%]     .p-tree.my-tree, [_nghost-%COMP%]     .ui-tree.my-tree{width:100%}"]}),e})()}];let O=(()=>{class e{}return e.\u0275mod=a.Hb({type:e}),e.\u0275inj=a.Gb({factory:function(i){return new(i||e)},imports:[[l.j.forChild(A)],l.j]}),e})();var _=t("PCNd");let B=(()=>{class e{}return e.\u0275mod=a.Hb({type:e}),e.\u0275inj=a.Gb({factory:function(i){return new(i||e)},imports:[[n.b,O,_.a]]}),e})()}}]);