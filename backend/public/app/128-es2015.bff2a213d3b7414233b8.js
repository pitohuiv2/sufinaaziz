(window.webpackJsonp=window.webpackJsonp||[]).push([[128],{iNqN:function(a,i,t){"use strict";t.r(i),t.d(i,"MapAdministrasiOtomatisModule",function(){return C});var n=t("ofXK"),e=t("tyNb"),o=t("fXoL"),s=t("EJUL"),r=t("7zfz"),l=t("U+s4"),d=t("G0w9"),p=t("ujBT"),b=t("arFO"),u=t("3Pt+"),c=t("7kUa"),m=t("jIHw"),g=t("xlun"),h=t("rEr+");function f(a,i){if(1&a&&(o.Qb(0,"th",21),o.Jc(1),o.Lb(2,"p-sortIcon",22),o.Pb()),2&a){const a=i.$implicit;o.Fc("width: ",a.width,""),o.jc("pSortableColumn",a.field),o.xb(1),o.Lc(" ",a.header," "),o.xb(1),o.jc("field",a.field)}}function P(a,i){if(1&a&&(o.Qb(0,"tr"),o.Lb(1,"th",19),o.Hc(2,f,3,6,"th",20),o.Pb()),2&a){const a=i.$implicit;o.xb(2),o.ic("ngForOf",a)}}function y(a,i){if(1&a&&(o.Qb(0,"td"),o.Jc(1),o.Pb()),2&a){const a=i.$implicit,t=o.bc().$implicit;o.Fc("width: ",a.width,""),o.xb(1),o.Lc(" ",t[a.field]," ")}}function v(a,i){if(1&a){const a=o.Sb();o.Qb(0,"tr"),o.Qb(1,"td",19),o.Qb(2,"button",23),o.Yb("click",function(){o.yc(a);const t=i.$implicit;return o.bc().hapusRow(t)}),o.Pb(),o.Pb(),o.Hc(3,y,2,4,"td",24),o.Pb()}if(2&a){const a=i.columns;o.xb(3),o.ic("ngForOf",a)}}const k=function(){return[5,10,25,50]},w=[{path:"",component:(()=>{class a{constructor(a,i,t,n,e,o,s,r){this.apiService=a,this.authService=i,this.messageService=t,this.cacheHelper=n,this.dateHelper=e,this.alertService=o,this.route=s,this.router=r,this.item={}}ngOnInit(){this.norecMap=void 0,this.loadCombo(),this.loadColumn()}loadCombo(){this.apiService.get("sysadmin/general/get-map-administrasi-combo").subscribe(a=>{this.listRuangan=a.ruangan,this.listJenisPelayanan=a.jenispelayanan})}loadColumn(){this.column=[{field:"no",header:"No",width:"40px"},{field:"namaproduk",header:"Pelayanan",width:"150px"},{field:"jenispelayanan",header:"Jenis Pelayanan",width:"150px"}]}getProduk(){this.apiService.get("sysadmin/general/get-combo-administrasi?produk=1&objectruanganfk="+this.item.ruangan.id).subscribe(a=>{for(var i=a.listakomodasi.length-1;i>=0;i--)a.listakomodasi[i].no=i+1,a.listakomodasi[i].israwatgabungSS=1==a.listakomodasi[i].israwatgabung?"Yes":"No";this.listPelayanan=a.produk,this.dataSource=a.listakomodasi})}getKomponenHarga(){this.item.Harga=0,this.listKomponen=[],null!=this.item.pelayanan&&this.apiService.get("tindakan/get-komponenharga?idRuangan="+this.item.ruangan.id+"&idKelas=6&idProduk="+this.item.pelayanan.id+"&idJenisPelayanan="+this.item.jenispelayanan.id).subscribe(a=>{this.listKomponen=a.data,this.item.Harga=a.data2[0].hargasatuan,this.item.jumlah=1})}batalInput(){this.item.Harga=0,this.listKomponen=[],this.item.pelayanan=void 0,this.item.jenispelayanan=void 0,this.item.ruangan=void 0}hapusAll(){if(null!=this.item.pelayanan)if(null!=this.item.ruangan){var a="";null!=this.norecMap&&(a=this.norecMap);var i=null;null!=this.item.rg&&(i=(this.item.rg.status="Yes")?1:null),this.apiService.post("sysadmin/general/save-map-administrasi-otomatis",{maid:a,pelayanan:this.item.pelayanan.id,rg:i,ruangan:this.item.ruangan.id,jenispelayanan:this.item.jenispelayanan.id,status:"HAPUS"}).subscribe(a=>{this.batalInput(),this.apiService.get("sysadmin/general/get-combo-administrasi?produk=1&objectruanganfk="+this.item.ruangan.id).subscribe(a=>{for(var i=a.data.listakomodasi.length-1;i>=0;i--)a.data.listakomodasi[i].no=i+1,a.data.listakomodasi[i].israwatgabungSS=1==a.data.listakomodasi[i].israwatgabung?"Yes":"No";this.dataSource=a.data.listakomodasi})})}else this.alertService.error("Info","Pilih Ruangan dulu");else this.alertService.error("Info","Pilih Pelayanan dulu")}simpanMapping(){if(null!=this.item.pelayanan)if(null!=this.item.ruangan){var a="";null!=this.norecMap&&(a=this.norecMap);var i="NO";null!=this.item.rg&&(i="Yes"==this.item.rg.status?"YES":"NO"),this.apiService.post("sysadmin/general/save-map-administrasi-otomatis",{maid:a,pelayanan:this.item.pelayanan.id,rg:i,ruangan:this.item.ruangan.id,jenispelayanan:this.item.jenispelayanan.id,status:"SIMPAN_JANG"}).subscribe(a=>{this.batalInput(),this.apiService.get("sysadmin/general/get-combo-administrasi?produk=1&objectruanganfk="+this.item.ruangan.id).subscribe(a=>{for(var i=a.data.listakomodasi.length-1;i>=0;i--)a.data.listakomodasi[i].no=i+1,a.data.listakomodasi[i].israwatgabungSS=1==a.data.listakomodasi[i].israwatgabung?"Yes":"No";this.dataSource=a.data.listakomodasi})})}else this.alertService.error("Info","Pilih Ruangan dulu");else this.alertService.error("Info","Pilih Pelayanan dulu")}hapusRow(a){this.apiService.post("sysadmin/general/save-map-administrasi-otomatis",{maid:a.maid,status:"HAPUS"}).subscribe(a=>{this.dataSource=[],this.apiService.get("sysadmin/general/get-combo-administrasi?produk=1&objectruanganfk="+this.item.ruangan.id).subscribe(a=>{for(var i=a.data.listakomodasi.length-1;i>=0;i--)a.data.listakomodasi[i].no=i+1,a.data.listakomodasi[i].israwatgabungSS=1==a.data.listakomodasi[i].israwatgabung?"Yes":"No";this.dataSource=a.data.listakomodasi})})}}return a.\u0275fac=function(i){return new(i||a)(o.Jb(s.a),o.Jb(s.b),o.Jb(r.h),o.Jb(l.a),o.Jb(d.a),o.Jb(p.a),o.Jb(e.a),o.Jb(e.f))},a.\u0275cmp=o.Db({type:a,selectors:[["app-map-administrasi-otomatis"]],decls:37,vars:24,consts:[[1,"p-fluid","p-formgrid"],[1,"card","card-w-title"],[1,"p-grid"],[1,"p-col-12","p-md-12"],[1,"p-col-12","p-md-9"],[1,"p-col-12","p-md-3"],["for","input",1,"label"],["placeholder","Ruangan","optionLabel","namaruangan",3,"options","ngModel","filter","showClear","ngModelChange","onChange"],["placeholder","jenis pelayanan","optionLabel","jenispelayanan",3,"options","ngModel","filter","showClear","ngModelChange"],["placeholder","pelayanan","optionLabel","namaproduk",3,"options","ngModel","filter","showClear","ngModelChange","onChange"],["type","text","pInputText","","placeholder","Harga","disabled","",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-4",2,"margin-top","16px"],["pButton","","type","button","icon","pi pi-plus","pTooltip","Simpan","label","Simpan",1,"p-button","p-mr-2",3,"disabled","click"],["pButton","","type","button","icon","pi pi-trash","pTooltip","Hapus","label","Hapus",1,"p-button","p-button-danger","p-mr-2",3,"click"],["pButton","","type","button","icon","pi pi-refresh","pTooltip","Batal","label","Batal",1,"p-button","p-button-warning","p-mr-2",3,"click"],[1,"p-col-12"],["styleClass","p-datatable-customers","scrollable","true","sortMode","multiple","selectionMode","single",3,"columns","value","selection","rowHover","rows","showCurrentPageReport","rowsPerPageOptions","paginator","pageLinks","selectionChange"],["pTemplate","header"],["pTemplate","body"],[2,"width","50px"],[3,"pSortableColumn","style",4,"ngFor","ngForOf"],[3,"pSortableColumn"],[3,"field"],["pButton","","type","button","icon","pi pi-trash","pTooltip","Hapus ","label","",1,"p-button-rounded","p-button-danger","p-mr-2",3,"click"],[3,"style",4,"ngFor","ngForOf"]],template:function(a,i){1&a&&(o.Qb(0,"div",0),o.Qb(1,"div",1),o.Qb(2,"h4"),o.Jc(3,"Setting Administrasi Ruangan Otomatis"),o.Pb(),o.Qb(4,"div",2),o.Qb(5,"div",3),o.Qb(6,"div",2),o.Qb(7,"div",4),o.Qb(8,"div",2),o.Qb(9,"div",5),o.Qb(10,"label",6),o.Jc(11,"Ruangan"),o.Pb(),o.Qb(12,"p-dropdown",7),o.Yb("ngModelChange",function(a){return i.item.ruangan=a})("onChange",function(){return i.getProduk()}),o.Pb(),o.Pb(),o.Qb(13,"div",5),o.Qb(14,"label",6),o.Jc(15,"Jenis Pelayanan"),o.Pb(),o.Qb(16,"p-dropdown",8),o.Yb("ngModelChange",function(a){return i.item.jenispelayanan=a}),o.Pb(),o.Pb(),o.Qb(17,"div",5),o.Qb(18,"label",6),o.Jc(19,"Pelayanan"),o.Pb(),o.Qb(20,"p-dropdown",9),o.Yb("ngModelChange",function(a){return i.item.pelayanan=a})("onChange",function(){return i.getKomponenHarga()}),o.Pb(),o.Pb(),o.Qb(21,"div",5),o.Qb(22,"label",6),o.Jc(23,"Harga"),o.Pb(),o.Qb(24,"input",10),o.Yb("ngModelChange",function(a){return i.item.Harga=a}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Qb(25,"div",5),o.Qb(26,"div",2),o.Qb(27,"div",11),o.Qb(28,"button",12),o.Yb("click",function(){return i.simpanMapping()}),o.Pb(),o.Pb(),o.Qb(29,"div",11),o.Qb(30,"button",13),o.Yb("click",function(){return i.hapusAll()}),o.Pb(),o.Pb(),o.Qb(31,"div",11),o.Qb(32,"button",14),o.Yb("click",function(){return i.batalInput()}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Qb(33,"div",15),o.Qb(34,"p-table",16),o.Yb("selectionChange",function(a){return i.selectedGrid=a}),o.Hc(35,P,3,1,"ng-template",17),o.Hc(36,v,4,1,"ng-template",18),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb()),2&a&&(o.xb(12),o.ic("options",i.listRuangan)("ngModel",i.item.ruangan)("filter",!0)("showClear",!0),o.xb(4),o.ic("options",i.listJenisPelayanan)("ngModel",i.item.jenispelayanan)("filter",!0)("showClear",!0),o.xb(4),o.ic("options",i.listPelayanan)("ngModel",i.item.pelayanan)("filter",!0)("showClear",!0),o.xb(4),o.ic("ngModel",i.item.Harga),o.xb(4),o.ic("disabled",i.isClosing),o.xb(6),o.ic("columns",i.column)("value",i.dataSource)("selection",i.selectedGrid)("rowHover",!0)("rows",20)("showCurrentPageReport",!0)("rowsPerPageOptions",o.mc(23,k))("paginator",!0)("pageLinks",5))},directives:[b.a,u.g,u.h,u.a,c.a,m.b,g.a,h.h,r.k,n.k,h.g,h.f],styles:[""]}),a})()}];let S=(()=>{class a{}return a.\u0275mod=o.Hb({type:a}),a.\u0275inj=o.Gb({factory:function(i){return new(i||a)},imports:[[e.j.forChild(w)],e.j]}),a})();var Q=t("PCNd");let C=(()=>{class a{}return a.\u0275mod=o.Hb({type:a}),a.\u0275inj=o.Gb({factory:function(i){return new(i||a)},imports:[[n.b,S,Q.a]]}),a})()}}]);