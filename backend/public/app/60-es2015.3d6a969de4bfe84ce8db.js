(window.webpackJsonp=window.webpackJsonp||[]).push([[60],{UF04:function(e,t,i){"use strict";i.r(t),i.d(t,"DaftarPasienDepositLebihModule",function(){return T});var a=i("ofXK"),n=i("PCNd"),o=i("tyNb"),l=i("7zfz"),r=i("wd/R"),s=i("fXoL"),b=i("EJUL"),c=i("U+s4"),d=i("G0w9"),p=i("ujBT"),h=i("Nf9I"),m=i("eO1q"),u=i("3Pt+"),g=i("7kUa"),f=i("jIHw"),P=i("xlun"),w=i("rEr+"),v=i("/RsI"),R=i("arFO");function M(e,t){if(1&e&&(s.Qb(0,"th",30),s.Jc(1),s.Lb(2,"p-sortIcon",31),s.Pb()),2&e){const e=t.$implicit;s.Fc("width: ",e.width,""),s.jc("pSortableColumn",e.field),s.xb(1),s.Lc(" ",e.header," "),s.xb(1),s.jc("field",e.field)}}function k(e,t){if(1&e&&(s.Qb(0,"tr"),s.Qb(1,"th",28),s.Jc(2," Aksi "),s.Pb(),s.Hc(3,M,3,6,"th",29),s.Pb()),2&e){const e=t.$implicit;s.xb(3),s.ic("ngForOf",e)}}function Q(e,t){if(1&e&&(s.Qb(0,"span"),s.Jc(1),s.Pb()),2&e){const e=s.bc().$implicit,t=s.bc().$implicit,i=s.bc();s.xb(1),s.Lc(" ",i.formatRupiah(t[e.field],"")," ")}}function C(e,t){if(1&e&&(s.Qb(0,"span"),s.Jc(1),s.Pb()),2&e){const e=s.bc().$implicit,t=s.bc().$implicit;s.xb(1),s.Lc(" ",t[e.field],"")}}function D(e,t){if(1&e&&(s.Qb(0,"td"),s.Hc(1,Q,2,1,"span",35),s.Hc(2,C,2,1,"span",35),s.Pb()),2&e){const e=t.$implicit;s.Fc("width: ",e.width,""),s.xb(1),s.ic("ngIf",null!=e.isCurrency),s.xb(1),s.ic("ngIf",null==e.isCurrency)}}function x(e,t){if(1&e){const e=s.Sb();s.Qb(0,"tr",32),s.Qb(1,"td",28),s.Qb(2,"button",33),s.Yb("click",function(){s.yc(e);const i=t.$implicit;return s.bc().kembalianDeposit(i)}),s.Pb(),s.Pb(),s.Hc(3,D,3,5,"td",34),s.Pb()}if(2&e){const e=t.columns,i=t.rowIndex;s.ic("pSelectableRow",t.$implicit)("pSelectableRowIndex",i),s.xb(3),s.ic("ngForOf",e)}}function y(e,t){if(1&e){const e=s.Sb();s.Qb(0,"p-button",36),s.Yb("click",function(){return s.yc(e),s.bc().cariFilter()}),s.Pb(),s.Qb(1,"p-button",37),s.Yb("click",function(){return s.yc(e),s.bc().clearFilter()}),s.Pb()}}const Y=function(){return[5,10,25,50]},N=function(){return{width:"50vw"}},F=[{path:"",component:(()=>{class e{constructor(e,t,i,a,n,o,l,r,s){this.apiService=e,this.authService=t,this.confirmationService=i,this.messageService=a,this.cacheHelper=n,this.dateHelper=o,this.alertService=l,this.route=r,this.router=s,this.item={},this.popFilter=!1}ngOnInit(){this.dataLogin=this.authService.dataLoginUser,this.kelUser=this.dataLogin.kelompokUser.kelompokUser,this.dateNow=new Date,this.item.tglAwal=r(this.dateNow).format("YYYY-MM-DD 00:00"),this.item.tglAkhir=r(this.dateNow).format("YYYY-MM-DD 23:59"),this.item.jmlRows=50,this.column=[{field:"no",header:"No",width:"65px"},{field:"tglMasuk",header:"Tgl Masuk",width:"140px"},{field:"tglPulang",header:"Tgl Pulang",width:"140px"},{field:"tglStruk",header:"Tgl Deposit",width:"140px"},{field:"noCm",header:"No RM",width:"150px"},{field:"noRegistrasi",header:"Noregistrasi",width:"150px"},{field:"jenisPasien",header:"Tipe Pasien",width:"150px"},{field:"namaPasien",header:"Nama Pasien",width:"250px"},{field:"lastRuangan",header:"Ruang Rawat",width:"200px"},{field:"sisdeposit",header:"Total Deposit Sisa",width:"180px",isCurrency:!0}],this.getDataCombo()}getDataCombo(){this.apiService.get("kasir/get-combo-kasir").subscribe(e=>{var t=e;this.listDepartemen=t.departemen,this.listKelompokPasien=t.kelompokpasien,this.idKdRanap=t.kdRanap,this.LoadCache()})}onRowSelect(e){}formatRupiah(e,t){return t+" "+parseFloat(e).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,"$1,")}LoadCache(){var e=this.cacheHelper.get("DaftarPasienPulangCtrl");null!=e?(this.item.tglAwal=new Date(e[0]),this.item.tglAkhir=new Date(e[1]),this.item.status=e[2],this.item.namaOrReg=e[3],null!=e[6]&&(this.listDepartemen=[e[6]],this.item.dataDepartemen=e[6]),null!=e[5]&&(this.listRuangan=[e[5]],this.item.dataRuangan=e[5]),null!=e[4]&&""!=e[4]&&(this.item.noReg=e[4]),null!=e[7]&&""!=e[7]&&(this.item.noRm=e[7]),null!=e[8]&&""!=e[8]&&(this.item.jmlRows=e[8]),this.loadData()):this.loadData()}isiRuangan(){null!=this.item.dataDepartemen&&(this.listRuangan=this.item.dataDepartemen.ruangan)}loadData(){var e=r(this.item.tglAwal).format("YYYY-MM-DD HH:mm"),t=r(this.item.tglAkhir).format("YYYY-MM-DD HH:mm"),i="",a=void 0;null!=this.item.dataRuangan&&(i=this.item.dataRuangan.id,a={id:this.item.dataRuangan.id,ruangan:this.item.dataRuangan.ruangan});var n="",o=void 0;null!=this.item.instalasi&&(n=this.item.dataDepartemen.id,o={id:this.item.dataDepartemen.id,departemen:this.item.dataDepartemen.departemen});var l="";null!=this.item.dataKelPasien&&(l=this.item.dataKelPasien.id);var s="";null!=this.item.noRM&&(s=this.item.noRM);var b="";null!=this.item.Noregistrasi&&(b=this.item.Noregistrasi);var c="";null!=this.item.namaPasien&&(c=this.item.namaPasien);var d="";null!=this.item.jmlRows&&(d=this.item.jmlRows),this.cacheHelper.set("DaftarPasienPulangCtrl",{0:e,1:t,2:void 0,3:c,4:b,5:a,6:o,7:s}),this.apiService.get("kasir/get-data-tagihan-pasien?namaPasien="+c+"&ruanganId="+i+"&status=&tglAwal="+e+"&tglAkhir="+t+"&noReg="+b+"&instalasiId="+n+"&noRm="+s+"&jmlRows="+d+"&kelompokPasienId="+l).subscribe(e=>{e=e;for(let t=0;t<e.length;t++){const i=e[t];i.no=t+1,i.sisdeposit=Math.abs(i.totalBayar)}this.dataTable=e,this.totalRecords=e.totalRow})}filter(){this.popFilter=!0}cariFilter(){this.popFilter=!1,this.loadData()}cari(){}clearFilter(){this.popFilter=!1,this.item.dataRuangan=void 0,this.item.dataDepartemen=void 0,this.item.dataKelPasien=void 0,this.item.tglAwal=r(this.dateNow).format("YYYY-MM-DD 00:00"),this.item.tglAkhir=r(this.dateNow).format("YYYY-MM-DD 23:59"),this.item.jmlRows=50,this.loadData()}kembalianDeposit(e){parseFloat(e.sisdeposit)<0?this.alertService.warn("Info","Nilai tidak boleh negatif!"):(parseFloat(e.totalBayar),this.router.navigate(["bayar-tagihan-pasien",e.norec_pd,"PenyetoranDepositKasirKembali",e.sisdeposit]))}}return e.\u0275fac=function(t){return new(t||e)(s.Jb(b.a),s.Jb(b.b),s.Jb(l.a),s.Jb(l.h),s.Jb(c.a),s.Jb(d.a),s.Jb(p.a),s.Jb(o.a),s.Jb(o.f))},e.\u0275cmp=s.Db({type:e,selectors:[["app-daftar-pasien-deposit-lebih"]],features:[s.wb([l.a])],decls:59,vars:44,consts:[["header","Confirmation","icon","fa fa-question-circle"],[1,"p-fluid","p-formgrid"],[1,"card","card-w-title"],[1,"p-col-12","p-md-12"],[1,"p-grid"],[1,"p-col-12","p-md-10"],[1,"p-col-12","p-md-2"],["for","input",1,"label"],["yearRange","1980:2030","dateFormat","yy-mm-dd",3,"ngModel","showIcon","monthNavigator","yearNavigator","showTime","ngModelChange"],["type","text","pInputText","","placeholder","No Rekam Medis",3,"ngModel","ngModelChange","keyup.enter"],["type","text","pInputText","","placeholder","No Registrasi",3,"ngModel","ngModelChange","keyup.enter"],[1,"p-col-12","p-md-3"],["type","text","pInputText","","placeholder","Nama Pasien",3,"ngModel","ngModelChange","keyup.enter"],[1,"p-col-12","p-md-1"],["type","text","pInputText","","placeholder","Jml Rows",3,"ngModel","ngModelChange","keyup.enter"],[1,"p-col-12","p-md-12",2,"margin-top","16px"],["pButton","","type","button","icon","pi pi-search","pTooltip","Cari",1,"p-mr-1",3,"click"],["pButton","","type","button","icon","pi pi-filter","pTooltip","Filter Detail",1,"p-button-success",3,"click"],["styleClass","p-datatable-customers","scrollable","true","sortMode","multiple","selectionMode","single",3,"columns","value","selection","rowHover","rows","showCurrentPageReport","rowsPerPageOptions","paginator","pageLinks","selectionChange","onRowSelect"],["dt",""],["pTemplate","header"],["pTemplate","body"],["header","Filter",3,"visible","modal","maximizable","draggable","resizable","visibleChange"],[1,"p-col-12","p-md-4"],["placeholder","Instalasi","optionLabel","departemen","appendTo","body",3,"options","ngModel","filter","showClear","ngModelChange","onChange"],["placeholder","Ruangan","optionLabel","ruangan","appendTo","body",3,"options","ngModel","filter","showClear","ngModelChange"],["placeholder","Tipe Pasien","optionLabel","kelompokpasien","appendTo","body",3,"options","ngModel","filter","showClear","ngModelChange"],["pTemplate","footer"],["width","100px"],[3,"pSortableColumn","style",4,"ngFor","ngForOf"],[3,"pSortableColumn"],[3,"field"],[3,"pSelectableRow","pSelectableRowIndex"],["pButton","","type","button","icon","fa fa-money","pTooltip","Kembali Deposit","label","",1,"p-button-rounded","p-button-info","p-mr-1",3,"click"],[3,"style",4,"ngFor","ngForOf"],[4,"ngIf"],["icon","pi pi-filter","label","Filter",1,"p-button-success",3,"click"],["icon","pi pi-close","label","Clear","styleClass","p-button-text",3,"click"]],template:function(e,t){1&e&&(s.Lb(0,"p-confirmDialog",0),s.Qb(1,"div",1),s.Qb(2,"div",2),s.Qb(3,"h4"),s.Jc(4,"Daftar Deposit Pasien"),s.Pb(),s.Qb(5,"div",3),s.Qb(6,"div",4),s.Qb(7,"div",5),s.Qb(8,"div",4),s.Qb(9,"div",6),s.Qb(10,"label",7),s.Jc(11,"Periode Awal"),s.Pb(),s.Qb(12,"p-calendar",8),s.Yb("ngModelChange",function(e){return t.item.tglAwal=e}),s.Pb(),s.Pb(),s.Qb(13,"div",6),s.Qb(14,"label",7),s.Jc(15,"Periode Akhir"),s.Pb(),s.Qb(16,"p-calendar",8),s.Yb("ngModelChange",function(e){return t.item.tglAkhir=e}),s.Pb(),s.Pb(),s.Qb(17,"div",6),s.Qb(18,"label",7),s.Jc(19,"No RM "),s.Pb(),s.Qb(20,"input",9),s.Yb("ngModelChange",function(e){return t.item.noRM=e})("keyup.enter",function(){return t.cari()}),s.Pb(),s.Pb(),s.Qb(21,"div",6),s.Qb(22,"label",7),s.Jc(23,"No Registrasi"),s.Pb(),s.Qb(24,"input",10),s.Yb("ngModelChange",function(e){return t.item.Noregistrasi=e})("keyup.enter",function(){return t.cari()}),s.Pb(),s.Pb(),s.Qb(25,"div",11),s.Qb(26,"label",7),s.Jc(27,"Nama Pasien "),s.Pb(),s.Qb(28,"input",12),s.Yb("ngModelChange",function(e){return t.item.namaPasien=e})("keyup.enter",function(){return t.cari()}),s.Pb(),s.Pb(),s.Qb(29,"div",13),s.Qb(30,"label",7),s.Jc(31,"jml Rows"),s.Pb(),s.Qb(32,"input",14),s.Yb("ngModelChange",function(e){return t.item.jmlRows=e})("keyup.enter",function(){return t.cari()}),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Qb(33,"div",6),s.Qb(34,"div",4),s.Qb(35,"div",15),s.Qb(36,"button",16),s.Yb("click",function(){return t.cari()}),s.Pb(),s.Qb(37,"button",17),s.Yb("click",function(){return t.filter()}),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Qb(38,"div",3),s.Qb(39,"p-table",18,19),s.Yb("selectionChange",function(e){return t.selected=e})("onRowSelect",function(e){return t.onRowSelect(e)}),s.Hc(41,k,4,1,"ng-template",20),s.Hc(42,x,4,3,"ng-template",21),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Qb(43,"p-dialog",22),s.Yb("visibleChange",function(e){return t.popFilter=e}),s.Qb(44,"div",1),s.Qb(45,"div",4),s.Qb(46,"div",23),s.Qb(47,"label",7),s.Jc(48,"Instalasi"),s.Pb(),s.Qb(49,"p-dropdown",24),s.Yb("ngModelChange",function(e){return t.item.dataDepartemen=e})("onChange",function(){return t.isiRuangan()}),s.Pb(),s.Pb(),s.Qb(50,"div",23),s.Qb(51,"label",7),s.Jc(52,"Ruangan"),s.Pb(),s.Qb(53,"p-dropdown",25),s.Yb("ngModelChange",function(e){return t.item.dataRuangan=e}),s.Pb(),s.Pb(),s.Qb(54,"div",23),s.Qb(55,"label",7),s.Jc(56,"Tipe Pasien"),s.Pb(),s.Qb(57,"p-dropdown",26),s.Yb("ngModelChange",function(e){return t.item.dataKelPasien=e}),s.Pb(),s.Pb(),s.Pb(),s.Pb(),s.Hc(58,y,2,0,"ng-template",27),s.Pb()),2&e&&(s.xb(12),s.ic("ngModel",t.item.tglAwal)("showIcon",!0)("monthNavigator",!0)("yearNavigator",!0)("showTime",!0),s.xb(4),s.ic("ngModel",t.item.tglAkhir)("showIcon",!0)("monthNavigator",!0)("yearNavigator",!0)("showTime",!0),s.xb(4),s.ic("ngModel",t.item.noRM),s.xb(4),s.ic("ngModel",t.item.Noregistrasi),s.xb(4),s.ic("ngModel",t.item.namaPasien),s.xb(4),s.ic("ngModel",t.item.jmlRows),s.xb(7),s.ic("columns",t.column)("value",t.dataTable)("selection",t.selected)("rowHover",!0)("rows",20)("showCurrentPageReport",!0)("rowsPerPageOptions",s.mc(42,Y))("paginator",!0)("pageLinks",5),s.xb(4),s.Ec(s.mc(43,N)),s.ic("visible",t.popFilter)("modal",!0)("maximizable",!0)("draggable",!1)("resizable",!0),s.xb(6),s.ic("options",t.listDepartemen)("ngModel",t.item.dataDepartemen)("filter",!0)("showClear",!0),s.xb(4),s.ic("options",t.listRuangan)("ngModel",t.item.dataRuangan)("filter",!0)("showClear",!0),s.xb(4),s.ic("options",t.listKelompokPasien)("ngModel",t.item.dataKelPasien)("filter",!0)("showClear",!0))},directives:[h.a,m.a,u.g,u.h,u.a,g.a,f.b,P.a,w.h,l.k,v.a,R.a,a.k,w.g,w.f,w.e,a.l,f.a],styles:[""]}),e})()}];let J=(()=>{class e{}return e.\u0275mod=s.Hb({type:e}),e.\u0275inj=s.Gb({factory:function(t){return new(t||e)},imports:[[o.j.forChild(F)],o.j]}),e})(),T=(()=>{class e{}return e.\u0275mod=s.Hb({type:e}),e.\u0275inj=s.Gb({factory:function(t){return new(t||e)},imports:[[a.b,J,n.a]]}),e})()}}]);