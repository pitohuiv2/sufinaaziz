(window.webpackJsonp=window.webpackJsonp||[]).push([[37],{jnCc:function(t,e,i){"use strict";i.r(e),i.d(e,"OrderRadModule",function(){return ft});var a=i("ofXK"),n=i("tyNb"),l=i("wd/R"),o=i("fXoL"),c=i("By0h"),r=i("EJUL"),s=i("7zfz"),b=i("U+s4"),d=i("G0w9"),p=i("ujBT"),u=i("/RsI"),h=i("3Pt+"),g=i("7kUa"),m=i("zFJ7"),f=i("7CaW"),k=i("rEr+"),P=i("eO1q"),v=i("arFO"),y=i("Ji6n"),x=i("jIHw"),Q=i("xlun"),w=i("hYoW"),C=i("jeV5"),S=i("lUkA"),M=i("Q4Mo"),j=i("PU9F");const R=function(){return{width:"100%"}};function H(t,e){if(1&t&&(o.Qb(0,"div",48),o.Lb(1,"p-tag",49),o.Pb()),2&t){const t=e.$implicit;o.xb(1),o.Ec(o.mc(3,R)),o.jc("value",t.namaproduk)}}function F(t,e){if(1&t){const t=o.Sb();o.Qb(0,"p-panel",44),o.Qb(1,"div",4),o.Hc(2,H,2,4,"div",45),o.Pb(),o.Qb(3,"div",4),o.Qb(4,"div",46),o.Qb(5,"button",47),o.Yb("click",function(){return o.yc(t),o.bc(2).clearSelection()}),o.Pb(),o.Pb(),o.Pb(),o.Pb()}if(2&t){const t=o.bc(2);o.ic("toggleable",!0),o.xb(2),o.ic("ngForOf",t.listChecked),o.xb(3),o.ic("disabled",t.isSimpan)}}function T(t,e){1&t&&o.Lb(0,"p-skeleton",53),2&t&&o.jc("styleClass",e.$implicit.class)}function O(t,e){if(1&t&&(o.Qb(0,"div"),o.Hc(1,T,1,1,"p-skeleton",52),o.Pb()),2&t){const t=e.$implicit;o.zb(t.class),o.xb(1),o.ic("ngForOf",t.details)}}function I(t,e){if(1&t&&(o.Qb(0,"div",4),o.Hc(1,O,2,4,"div",51),o.Pb()),2&t){const t=e.$implicit;o.xb(1),o.ic("ngForOf",t.details)}}function J(t,e){if(1&t&&(o.Qb(0,"div",8),o.Hc(1,I,2,1,"div",50),o.Pb()),2&t){const t=o.bc(2);o.xb(1),o.ic("ngForOf",t.skeleton)}}function L(t,e){if(1&t){const t=o.Sb();o.Qb(0,"div",61),o.Qb(1,"p-checkbox",62),o.Yb("ngModelChange",function(i){o.yc(t);const a=e.$implicit;return o.bc(4).item.layanan[a.id]=i})("onChange",function(){return o.yc(t),o.bc(4).getSelected()}),o.Pb(),o.Qb(2,"label",63),o.Jc(3),o.Pb(),o.Pb()}if(2&t){const t=e.$implicit,i=o.bc(4);o.xb(1),o.jc("inputId",t.id),o.ic("ngModel",i.item.layanan[t.id]),o.xb(1),o.jc("for",t.id),o.xb(1),o.Lc(" ",t.namaproduk,"")}}function D(t,e){if(1&t&&(o.Qb(0,"div",56),o.Qb(1,"p-divider",57),o.Qb(2,"div",58),o.Lb(3,"i",59),o.Qb(4,"b"),o.Jc(5),o.Pb(),o.Pb(),o.Pb(),o.Qb(6,"div",4),o.Hc(7,L,4,4,"div",60),o.cc(8,"filter"),o.Pb(),o.Pb()),2&t){const t=e.$implicit,i=o.bc(3);o.xb(5),o.Kc(t.detailjenisproduk),o.xb(2),o.ic("ngForOf",o.ec(8,2,t.details,i.searchText))}}function Y(t,e){if(1&t&&(o.Qb(0,"div",54),o.Hc(1,D,9,5,"div",55),o.Pb()),2&t){const t=o.bc(2);o.xb(1),o.ic("ngForOf",t.listProdukCek)}}function $(t,e){if(1&t){const t=o.Sb();o.Qb(0,"div",4),o.Qb(1,"div",8),o.Qb(2,"p-panel",20),o.Qb(3,"div",4),o.Qb(4,"div",8),o.Qb(5,"div",4),o.Qb(6,"div",21),o.Qb(7,"label",6),o.Jc(8,"Tgl Order "),o.Pb(),o.Qb(9,"p-calendar",22),o.Yb("ngModelChange",function(e){return o.yc(t),o.bc().item.tglPelayanan=e}),o.Pb(),o.Pb(),o.Qb(10,"div",21),o.Qb(11,"label",6),o.Jc(12,"Ruangan Asal "),o.Pb(),o.Qb(13,"input",23),o.Yb("ngModelChange",function(e){return o.yc(t),o.bc().item.ruanganAsal=e}),o.Pb(),o.Pb(),o.Qb(14,"div",21),o.Qb(15,"label",6),o.Jc(16,"Ruangan Tujuan "),o.Pb(),o.Qb(17,"p-dropdown",24),o.Yb("ngModelChange",function(e){return o.yc(t),o.bc().item.ruangantujuan=e})("onChange",function(){o.yc(t);const e=o.bc();return e.getProduk(e.item.ruangantujuan)}),o.Pb(),o.Pb(),o.Qb(18,"div",21),o.Qb(19,"label",6),o.Jc(20,"Keterangan "),o.Pb(),o.Qb(21,"input",25),o.Yb("ngModelChange",function(e){return o.yc(t),o.bc().item.keterangan=e}),o.Pb(),o.Pb(),o.Qb(22,"div",21),o.Qb(23,"div",4),o.Qb(24,"div",5),o.Qb(25,"p-checkbox",26),o.Yb("ngModelChange",function(e){return o.yc(t),o.bc().item.iscito=e}),o.Pb(),o.Qb(26,"label",27),o.Jc(27," Cito"),o.Pb(),o.Pb(),o.Qb(28,"div",5),o.Qb(29,"p-checkbox",28),o.Yb("ngModelChange",function(e){return o.yc(t),o.bc().item.paket=e})("onChange",function(){o.yc(t);const e=o.bc();return e.cekPaket(e.item.paket)}),o.Pb(),o.Qb(30,"label",29),o.Jc(31," Paket"),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Lb(32,"div",4),o.Pb(),o.Pb(),o.Qb(33,"div",4),o.Qb(34,"div",30),o.Hc(35,F,6,3,"p-panel",31),o.Pb(),o.Pb(),o.Qb(36,"div",4),o.Qb(37,"div",32),o.Qb(38,"span",33),o.Lb(39,"i",34),o.Qb(40,"input",35),o.Yb("ngModelChange",function(e){return o.yc(t),o.bc().searchText=e}),o.Pb(),o.Pb(),o.Pb(),o.Qb(41,"div",36),o.Qb(42,"button",37),o.Yb("click",function(){return o.yc(t),o.bc().clearFilter()}),o.Pb(),o.Pb(),o.Qb(43,"div",38),o.Qb(44,"button",39),o.Yb("click",function(){return o.yc(t),o.bc().save()}),o.Pb(),o.Pb(),o.Qb(45,"div",36),o.Qb(46,"button",40),o.Yb("click",function(){return o.yc(t),o.bc().batal()}),o.Pb(),o.Pb(),o.Qb(47,"div",36),o.Qb(48,"button",41),o.Yb("click",function(){return o.yc(t),o.bc().riwayat()}),o.Pb(),o.Pb(),o.Hc(49,J,2,1,"div",42),o.Hc(50,Y,2,1,"div",43),o.Pb(),o.Pb(),o.Pb(),o.Pb()}if(2&t){const t=o.bc();o.xb(2),o.ic("toggleable",!0),o.xb(7),o.ic("ngModel",t.item.tglPelayanan)("showIcon",!0)("monthNavigator",!0)("yearNavigator",!0)("showTime",!0)("maxDate",t.maxDateValue),o.xb(4),o.ic("ngModel",t.item.ruanganAsal),o.xb(4),o.ic("options",t.listRuanganTujuan)("ngModel",t.item.ruangantujuan)("filter",!0)("showClear",!0),o.xb(4),o.ic("ngModel",t.item.keterangan),o.xb(4),o.ic("ngModel",t.item.iscito),o.xb(4),o.ic("ngModel",t.item.paket),o.xb(6),o.ic("ngIf",t.listChecked.length>0),o.xb(5),o.ic("ngModel",t.searchText),o.xb(4),o.ic("disabled",t.isSimpan),o.xb(5),o.ic("ngIf",0==t.listProdukCek.length),o.xb(1),o.ic("ngIf",t.listProdukCek.length>0)}}function _(t,e){if(1&t){const t=o.Sb();o.Qb(0,"div",68),o.Qb(1,"div",21),o.Qb(2,"span",33),o.Lb(3,"i",34),o.Qb(4,"input",69),o.Yb("input",function(e){return o.yc(t),o.bc(),o.wc(6).filterGlobal(e.target.value,"contains")}),o.Pb(),o.Pb(),o.Pb(),o.Pb()}}function E(t,e){if(1&t&&(o.Qb(0,"th",72),o.Jc(1),o.Lb(2,"p-sortIcon",73),o.Pb()),2&t){const t=e.$implicit;o.Fc("width: ",t.width,""),o.jc("pSortableColumn",t.field),o.xb(1),o.Lc(" ",t.header," "),o.xb(1),o.jc("field",t.field)}}function B(t,e){if(1&t&&(o.Qb(0,"tr"),o.Lb(1,"th",70),o.Hc(2,E,3,6,"th",71),o.Pb()),2&t){const t=e.$implicit;o.xb(2),o.ic("ngForOf",t)}}function K(t,e){if(1&t&&(o.Qb(0,"span"),o.Jc(1),o.Pb()),2&t){const t=o.bc().$implicit,e=o.bc().$implicit,i=o.bc(2);o.xb(1),o.Lc(" ",i.formatRupiah(e[t.field],""),"")}}function N(t,e){if(1&t&&(o.Qb(0,"span"),o.Jc(1),o.Pb()),2&t){const t=o.bc().$implicit,e=o.bc().$implicit;o.xb(1),o.Lc(" ",e[t.field],"")}}function z(t,e){if(1&t&&(o.Qb(0,"td"),o.Hc(1,K,2,1,"span",77),o.Hc(2,N,2,1,"span",77),o.Pb()),2&t){const t=e.$implicit;o.Fc("width: ",t.width,""),o.xb(1),o.ic("ngIf",null!=t.isCurrency),o.xb(1),o.ic("ngIf",null==t.isCurrency)}}function A(t,e){if(1&t){const t=o.Sb();o.Qb(0,"tr"),o.Qb(1,"td",70),o.Lb(2,"button",74),o.Qb(3,"button",75),o.Yb("click",function(){o.yc(t);const i=e.$implicit;return o.bc(2).hapusD(i)}),o.Pb(),o.Pb(),o.Hc(4,z,3,5,"td",76),o.Pb()}if(2&t){const t=e.$implicit,i=e.columns,a=e.expanded;o.xb(2),o.ic("pRowToggler",t)("icon",a?"pi pi-chevron-down":"pi pi-chevron-right"),o.xb(2),o.ic("ngForOf",i)}}function U(t,e){1&t&&(o.Qb(0,"tr"),o.Lb(1,"th",70),o.Qb(2,"th"),o.Jc(3,"Nama Layanan"),o.Pb(),o.Qb(4,"th"),o.Jc(5,"Jumlah"),o.Pb(),o.Qb(6,"th"),o.Jc(7,"Expertise"),o.Pb(),o.Pb())}function G(t,e){if(1&t){const t=o.Sb();o.Qb(0,"tr"),o.Qb(1,"td",70),o.Qb(2,"button",81),o.Yb("click",function(){o.yc(t);const i=e.$implicit;return o.bc(3).lihatHasil(i)}),o.Pb(),o.Qb(3,"button",82),o.Yb("click",function(){o.yc(t);const i=e.$implicit;return o.bc(3).expertise(i)}),o.Pb(),o.Pb(),o.Qb(4,"td"),o.Jc(5),o.Pb(),o.Qb(6,"td"),o.Jc(7),o.Pb(),o.Qb(8,"td"),o.Jc(9),o.Pb(),o.Pb()}if(2&t){const t=e.$implicit;o.xb(5),o.Kc(t.namaproduk),o.xb(2),o.Kc(t.qtyproduk),o.xb(2),o.Kc(t.expertise)}}function q(t,e){if(1&t&&(o.Qb(0,"tr"),o.Qb(1,"td",78),o.Qb(2,"div",79),o.Qb(3,"p-table",80),o.Hc(4,U,8,0,"ng-template",17),o.Hc(5,G,10,3,"ng-template",18),o.Pb(),o.Pb(),o.Pb(),o.Pb()),2&t){const t=e.$implicit;o.xb(3),o.ic("value",t.details)}}const V=function(){return[5,10,25,50]},W=function(){return["namaPelayanan"]};function X(t,e){if(1&t){const t=o.Sb();o.Qb(0,"div",4),o.Qb(1,"div",8),o.Qb(2,"p-panel",64),o.Qb(3,"div",4),o.Qb(4,"div",8),o.Qb(5,"p-table",65,66),o.Yb("selectionChange",function(e){return o.yc(t),o.bc().selectedData=e}),o.Hc(7,_,5,0,"ng-template",16),o.Hc(8,B,3,1,"ng-template",17),o.Hc(9,A,5,3,"ng-template",18),o.Hc(10,q,6,1,"ng-template",19),o.Pb(),o.Pb(),o.Pb(),o.Qb(11,"div",4),o.Qb(12,"div",46),o.Qb(13,"button",67),o.Yb("click",function(){return o.yc(t),o.bc().order()}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb()}if(2&t){const t=o.bc();o.xb(2),o.ic("toggleable",!0),o.xb(3),o.ic("columns",t.columnRiwayat)("value",t.dataSourceRiwayat)("selection",t.selectedData)("rowHover",!0)("rows",20)("showCurrentPageReport",!0)("rowsPerPageOptions",o.mc(11,V))("paginator",!0)("pageLinks",5)("globalFilterFields",o.mc(12,W))}}function Z(t,e){if(1&t){const t=o.Sb();o.Qb(0,"p-button",83),o.Yb("click",function(){return o.yc(t),o.bc().pop_expertise=!1}),o.Pb()}}function tt(t,e){if(1&t){const t=o.Sb();o.Qb(0,"div",68),o.Qb(1,"div",8),o.Qb(2,"span",33),o.Lb(3,"i",34),o.Qb(4,"input",69),o.Yb("input",function(e){return o.yc(t),o.bc(),o.wc(29).filterGlobal(e.target.value,"contains")}),o.Pb(),o.Pb(),o.Pb(),o.Pb()}}function et(t,e){if(1&t&&(o.Qb(0,"th",72),o.Jc(1),o.Lb(2,"p-sortIcon",73),o.Pb()),2&t){const t=e.$implicit;o.Fc("width: ",t.width,""),o.jc("pSortableColumn",t.field),o.xb(1),o.Lc(" ",t.header," "),o.xb(1),o.jc("field",t.field)}}function it(t,e){if(1&t&&(o.Qb(0,"tr"),o.Lb(1,"th",70),o.Hc(2,et,3,6,"th",71),o.Pb()),2&t){const t=e.$implicit;o.xb(2),o.ic("ngForOf",t)}}function at(t,e){if(1&t&&(o.Qb(0,"span"),o.Jc(1),o.Pb()),2&t){const t=o.bc().$implicit,e=o.bc().$implicit,i=o.bc();o.xb(1),o.Lc(" ",i.formatRupiah(e[t.field],""),"")}}function nt(t,e){if(1&t&&(o.Qb(0,"span"),o.Jc(1),o.Pb()),2&t){const t=o.bc().$implicit,e=o.bc().$implicit;o.xb(1),o.Lc(" ",e[t.field],"")}}function lt(t,e){if(1&t&&(o.Qb(0,"td"),o.Hc(1,at,2,1,"span",77),o.Hc(2,nt,2,1,"span",77),o.Pb()),2&t){const t=e.$implicit;o.Fc("width: ",t.width,""),o.xb(1),o.ic("ngIf",null!=t.isCurrency),o.xb(1),o.ic("ngIf",null==t.isCurrency)}}function ot(t,e){if(1&t){const t=o.Sb();o.Qb(0,"tr"),o.Qb(1,"td",70),o.Lb(2,"button",74),o.Qb(3,"button",84),o.Yb("click",function(){o.yc(t);const i=e.$implicit;return o.bc().addPaket(i)}),o.Pb(),o.Pb(),o.Hc(4,lt,3,5,"td",76),o.Pb()}if(2&t){const t=e.$implicit,i=e.columns,a=e.expanded;o.xb(2),o.ic("pRowToggler",t)("icon",a?"pi pi-chevron-down":"pi pi-chevron-right"),o.xb(2),o.ic("ngForOf",i)}}function ct(t,e){1&t&&(o.Qb(0,"tr"),o.Qb(1,"th"),o.Jc(2,"Nama Layanan"),o.Pb(),o.Pb())}function rt(t,e){if(1&t&&(o.Qb(0,"tr"),o.Qb(1,"td"),o.Jc(2),o.Pb(),o.Pb()),2&t){const t=e.$implicit;o.xb(2),o.Kc(t.namaproduk)}}function st(t,e){if(1&t&&(o.Qb(0,"tr"),o.Qb(1,"td",85),o.Qb(2,"div",79),o.Qb(3,"p-table",80),o.Hc(4,ct,3,0,"ng-template",17),o.Hc(5,rt,3,1,"ng-template",18),o.Pb(),o.Pb(),o.Pb(),o.Pb()),2&t){const t=e.$implicit;o.xb(3),o.ic("value",t.details)}}function bt(t,e){if(1&t){const t=o.Sb();o.Qb(0,"p-button",83),o.Yb("click",function(){return o.yc(t),o.bc().closePaket()}),o.Pb()}}const dt=function(){return{width:"450px",minWidth:"450px"}},pt=function(){return{width:"600px",minWidth:"600px"}},ut=function(){return["namapaket"]},ht=[{path:"",component:(()=>{class t{constructor(t,e,i,a,n,l,o,c,r){this.rekamMedis=t,this.apiService=e,this.authService=i,this.confirmationService=a,this.cacheHelper=n,this.dateHelper=l,this.alertService=o,this.activeRoute=c,this.router=r,this.item={tglPelayanan:new Date,layanan:[]},this.listProdukCek=[],this.produkDef=[],this.listRuanganTujuan=[],this.skeleton=[],this.searchText="",this.selected_count=0,this.selected_games=[],this.listChecked=[],this.dataSourceRiwayat=[]}ngAfterViewInit(){}ngOnInit(){this.skeleton=this.loadSkeleton(),this.item.idPegawaiLogin=this.authService.getDataLoginUser().pegawai.id,this.apiService.get("emr/get-combo-penunjang?departemenfk=kdDepartemenRad").subscribe(t=>{this.item.ruanganAsal=this.rekamMedis.header.namaruangan,this.item.namaRuangan=this.rekamMedis.header.namaruangan,this.listRuanganTujuan=t.ruangantujuan,this.item.ruangantujuan={id:t.ruangantujuan[0].id,namaruangan:t.ruangantujuan[0].namaruangan,instalasiidfk:t.ruangantujuan[0].instalasiidfk},this.getProduk(this.item.ruangantujuan)}),this.columnRiwayat=[{field:"noregistrasi",header:"No Registrasi",width:"100px"},{field:"tglorder",header:"Tgl Order",width:"100px"},{field:"noorder",header:"No Order",width:"100px"},{field:"dokter",header:"Dokter",width:"150px"},{field:"namaruanganasal",header:"Ruangan Asal",width:"150px"},{field:"namaruangantujuan",header:"Ruangan",width:"150px"},{field:"keteranganlainnya",header:"Keterangan",width:"150px"},{field:"statusorder",header:"Status",width:"70px"},{field:"cito",header:"Cito",width:"70px"}],this.columnPaket=[{field:"namapaket",header:"Nama Paket",width:"120px"},{field:"jml",header:"Jumlah",width:"80px"}]}getProduk(t){this.listProdukCek=[],this.item.layanan=[],this.produkDef=[],null!=t&&this.apiService.get("sysadmin/general/get-tindakan-with-details?idRuangan="+t.id+"&idKelas="+this.rekamMedis.header.objectkelasfk+"&idJenisPelayanan="+this.rekamMedis.header.jenispelayanan+"&isLabRad=true").subscribe(t=>{this.listProdukCek=t.details,this.produkDef=t.data})}getSelected(){if(this.item.layanan.length>0)for(var t=Object.keys(this.item.layanan),e=0;e<t.length;e++){const n=t[e];if(1==this.item.layanan[parseInt(n)])for(var i=0;i<this.produkDef.length;i++){const t=this.produkDef[i];if(t.id==n){for(var a=0;a<this.listChecked.length;a++)this.listChecked[a].namaproduk==t.namaproduk&&this.listChecked.splice(a,1);this.listChecked.push({namaproduk:t.namaproduk})}}else for(i=0;i<this.produkDef.length;i++){const t=this.produkDef[i];if(t.id==n)for(a=0;a<this.listChecked.length;a++)this.listChecked[a].namaproduk==t.namaproduk&&this.listChecked.splice(a,1)}}}clearFilter(){this.searchText=""}save(){if(null!=this.item.ruangantujuan)if(null!=this.item.tglPelayanan)if(null!=this.item.layanan&&0!=this.item.layanan.length){for(var t=Object.keys(this.item.layanan),e=[],i=t.length-1;i>=0;i--)1==this.item.layanan[parseInt(t[i])]&&e.push({no:i+1,produkfk:t[i],namaproduk:t[i],qtyproduk:1,objectruanganfk:this.rekamMedis.header.objectruanganfk,objectruangantujuanfk:this.item.ruangantujuan.id,pemeriksaanluar:1==this.item.pemeriksaanKeluar?1:0,iscito:null!=this.item.iscito&&1==this.item.iscito&&this.item.iscito,objectkelasfk:this.rekamMedis.header.objectkelasfk,nourut:null});if(0!=e.length){var a={tanggal:l(this.item.tglPelayanan).format("YYYY-MM-DD HH:mm:ss"),norec_so:"",norec_apd:this.rekamMedis.header.norec_apd,norec_pd:this.rekamMedis.header.norec_pd,qtyproduk:e.length,objectruanganfk:this.rekamMedis.header.objectruanganfk,objectruangantujuanfk:this.item.ruangantujuan.id,departemenfk:this.item.ruangantujuan.instalasiidfk,pegawaiorderfk:this.item.idPegawaiLogin,keterangan:null!=this.item.keterangan?this.item.keterangan:null,iscito:null!=this.item.iscito&&1==this.item.iscito&&this.item.iscito,details:e};this.isSimpan=!0,this.apiService.post("emr/save-order-pelayanan",a).subscribe(t=>{this.item.layanan=[],this.isSimpan=!1,this.apiService.postLog("Order Radiologi","Norec strukorder_t",t.strukorder.norec,"Order Radiologi No Order - "+t.strukorder.noorder+" dengan No Registrasi "+this.item.noregistrasi).subscribe(t=>{}),this.batal()},t=>{this.isSimpan=!1})}else this.alertService.warn("Info","Pilih Pelayanan dulu")}else this.alertService.warn("Info","Pilih layanan terlebih dahulu!!");else this.alertService.warn("Info","Pilih Tgl Order  terlebih dahulu!!");else this.alertService.warn("Info","Pilih Ruangan Tujuan terlebih dahulu!!")}batal(){for(var t=Object.keys(this.item.layanan),e=t.length-1;e>=0;e--)this.item.layanan[parseInt(t[e])]=!1;this.listChecked=[],delete this.item.keterangan}onRowSelect(t){}hapusD(t){"SELESAI"!=t.status?this.apiService.post("emr/delete-order-pelayanan",{norec_order:t.norec}).subscribe(t=>{this.loadRiwayat("noregistrasi="+this.rekamMedis.header.noregistrasi)}):this.alertService.warn("Info","Status Sudah Selesai tidak bisa di hapus")}order(){this.isRiwayat=!1,this.batal(),this.getProduk(this.item.ruangantujuan)}riwayat(){this.isRiwayat=!0,this.loadRiwayat("noregistrasi="+this.rekamMedis.header.noregistrasi)}loadRiwayat(t){this.apiService.get("emr/get-riwayat-order-penunjang?"+t+"&setting=kdDepartemenRad").subscribe(t=>{for(var e=t.daftar.length-1;e>=0;e--){t.daftar[e].no=e+1,t.daftar[e].cito=1==t.daftar[e].cito?"\u2714":"\u2718";for(var i=t.daftar[e].details.length-1;i>=0;i--){const a=t.daftar[e].details[i];a.expertise=null!=a.norec_hr&&""!=a.norec_hr?"\u2714":"\u2718"}}this.dataSourceRiwayat=t.daftar})}hapus(){null!=this.selectedData?"Verifikasi"!=this.selectedData.status&&"Sudah diproses"!=this.selectedData.status?this.apiService.post("emr/delete-order-pelayanan",{norec_order:this.selectedData.norec}).subscribe(t=>{this.loadRiwayat("noregistrasi="+this.rekamMedis.header.noregistrasi)}):this.alertService.warn("Info","Status Sudah Selesai tidak bisa di hapus"):this.alertService.info("Info","Pilih data dulu")}lihatHasil(t){}expertise(t){null!=t.norec_hr?(this.pop_expertise=!0,this.apiService.get("emr/get-expertise?norec="+t.norec_pp+"&produkfk="+t.id).subscribe(t=>{this.item.nofoto=t.data.nofoto,this.item.dokterExp=t.data.namalengkap,this.item.tglExp=t.data.tanggal,this.item.expertise=t.data.keterangan})):this.alertService.info("Info","Belum ada Expertise")}cekPaket(t){if(1==t){if(null==this.item.ruangantujuan)return void this.alertService.warn("Info","Pilih ruangan dulu");this.pop_paket=!0,this.apiService.get("general/get-paket-tindakan").subscribe(t=>{this.dataSourcePaket=t})}}addPaket(t){for(var e=t.details,i=Object.keys(this.item.layanan),a=0;a<e.length;a++){const t=e[a];if(i.length>0)for(let e=0;e<i.length;e++)t.objectprodukfk!=i[e]&&(this.item.layanan[t.objectprodukfk]=!0);else this.item.layanan[t.objectprodukfk]=!0}this.getSelected(),this.pop_paket=!1}closePaket(){this.pop_paket=!1,this.item.paket=!1}clearSelection(){var t=Object.keys(this.item.layanan);for(let e=0;e<t.length;e++)this.item.layanan[t[e]]=!1;this.getSelected()}loadSkeleton(){return[{id:1,details:[{class:"p-col-2",details:[{class:"p-mb-0"}]}]},{id:2,details:[{class:"p-col-4",details:[{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"}]},{class:"p-col-4",details:[{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"}]},{class:"p-col-4",details:[{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"}]}]},{id:3,details:[{class:"p-col-2",details:[{class:"p-mb-0"}]}]},{id:4,details:[{class:"p-col-4",details:[{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"}]},{class:"p-col-4",details:[{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"}]},{class:"p-col-4",details:[{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"},{class:"p-mb-2"}]}]}]}}return t.\u0275fac=function(e){return new(e||t)(o.Jb(c.a),o.Jb(r.a),o.Jb(r.b),o.Jb(s.a),o.Jb(b.a),o.Jb(d.a),o.Jb(p.a),o.Jb(n.a),o.Jb(n.f))},t.\u0275cmp=o.Db({type:t,selectors:[["app-order-rad"]],decls:35,vars:33,consts:[[1,"p-fluid","p-formgrid"],[1,"card","card-w-title"],["class","p-grid",4,"ngIf"],["header","Expertise",3,"visible","modal","maximizable","draggable","resizable","visibleChange"],[1,"p-grid"],[1,"p-col-12","p-md-6"],["for","input",1,"label"],["type","text","pInputText","","disabled","",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-12"],["rows","5","cols","30","pInputTextarea","",3,"ngModel","ngModelChange"],["pTemplate","footer"],["header","Paket",3,"visible","modal","maximizable","draggable","resizable","visibleChange"],[1,"p-fluid","p-grid"],["header","Detail Paket",3,"toggleable"],["styleClass","p-datatable-customers","scrollable","true","sortMode","multiple","dataKey","id","selectionMode","single",3,"columns","value","rowHover","rows","showCurrentPageReport","rowsPerPageOptions","paginator","pageLinks","globalFilterFields"],["dt2",""],["pTemplate","caption"],["pTemplate","header"],["pTemplate","body"],["pTemplate","rowexpansion"],["header","Detail Pemeriksaan",3,"toggleable"],[1,"p-col-12","p-md-3"],["yearRange","1980:2030","dateFormat","yy-mm-dd","placeholder","Tgl Pelayanan","hourFormat","24",3,"ngModel","showIcon","monthNavigator","yearNavigator","showTime","maxDate","ngModelChange"],["type","text","pInputText","","placeholder","Ruangan ","disabled","",3,"ngModel","ngModelChange"],["placeholder","Ruangan Tujuan","optionLabel","namaruangan","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange","onChange"],["type","text","pInputText","","placeholder","Keterangan ",3,"ngModel","ngModelChange"],["binary","true","inputId","item.iscito",3,"ngModel","ngModelChange"],["for","item.iscito"],["binary","true","inputId","item.paket",3,"ngModel","ngModelChange","onChange"],["for","item.paket"],[1,"p-col-12"],["header","Detail Pemeriksaan Terpilih",3,"toggleable",4,"ngIf"],[1,"p-col-12","p-md-5"],[1,"p-input-icon-left"],[1,"pi","pi-search"],["pInputText","","type","text","placeholder","Filter",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-1"],["pButton","","type","button","icon","pi pi-times","pTooltip","Clear Filter",1,"p-button","p-button","p-mr-2",3,"click"],[1,"p-col-12","p-md-1","p-md-offset-3"],["pButton","","type","button","icon","pi pi-save","pTooltip","Simpan","label","Simpan",1,"p-button","p-mr-2",3,"disabled","click"],["pButton","","type","button","icon","pi pi-arrow-right","pTooltip","Batal ","label","Batal",1,"p-button","p-button-danger","p-mr-2",3,"click"],["pButton","","type","button","icon","pi pi-list","pTooltip","Riwayat Order","label","Riwayat",1,"p-button","p-mr-2",3,"click"],["class","p-col-12 p-md-12",4,"ngIf"],["style","height: 600px;overflow: auto",4,"ngIf"],["header","Detail Pemeriksaan Terpilih",3,"toggleable"],["class","p-col-12 p-md-4",4,"ngFor","ngForOf"],[1,"p-col-12","p-md-1","p-md-offset-11"],["pButton","","type","button","icon","pi pi-trash","pTooltip","Hapus Semua Data Terpilih","label","Clear",1,"p-button","p-button-danger","p-mr-2",3,"disabled","click"],[1,"p-col-12","p-md-4"],["styleClass","p-mr-2","severity","warning",3,"value"],["class","p-grid",4,"ngFor","ngForOf"],[3,"class",4,"ngFor","ngForOf"],[3,"styleClass",4,"ngFor","ngForOf"],[3,"styleClass"],[2,"height","600px","overflow","auto"],["class","p-col-12 p-md-12","style","padding: 10px;",4,"ngFor","ngForOf"],[1,"p-col-12","p-md-12",2,"padding","10px"],["align","left"],[1,"p-d-inline-flex","p-ai-center"],[1,"fa","fa-stethoscope","p-mr-2"],["class","p-col-12 p-md-4 p-field-checkbox",4,"ngFor","ngForOf"],[1,"p-col-12","p-md-4","p-field-checkbox"],["binary","true",3,"ngModel","inputId","ngModelChange","onChange"],[3,"for"],["header","Detail Order",3,"toggleable"],["styleClass","p-datatable-customers","scrollable","true","sortMode","multiple","dataKey","norec","selectionMode","single",3,"columns","value","selection","rowHover","rows","showCurrentPageReport","rowsPerPageOptions","paginator","pageLinks","globalFilterFields","selectionChange"],["dt",""],["pButton","","type","button","icon","pi pi-plus","pTooltip","Order Baru ","label","Order",1,"p-button","p-mr-2",3,"click"],[1,"table-header","p-grid"],["pInputText","","type","text","placeholder","Filter",3,"input"],[2,"width","80px"],[3,"pSortableColumn","style",4,"ngFor","ngForOf"],[3,"pSortableColumn"],[3,"field"],["type","button","pButton","","pRipple","",1,"p-button-text","p-button-rounded","p-button-plain","p-mr-2",3,"pRowToggler","icon"],["pButton","","type","button","icon","pi pi-trash","pTooltip","Hapus Order","label","",1,"p-button-rounded","p-button-danger","p-mr-2",3,"click"],[3,"style",4,"ngFor","ngForOf"],[4,"ngIf"],["colspan","10"],[1,"p-p-3"],["dataKey","id",3,"value"],["pButton","","type","button","icon","fa fa-heartbeat","pTooltip","Lihat Hasil","label","",1,"p-button-rounded","p-button-success","p-mr-2",3,"click"],["pButton","","type","button","icon","fa fa-sticky-note","pTooltip","Lihat Expertise","label","",1,"p-button-rounded","p-button","p-mr-2",3,"click"],["icon","pi pi-close","label","Tutup","styleClass","p-button-text",3,"click"],["pButton","","type","button","icon","pi pi-plus","pTooltip","Tambah Paket","label","",1,"p-button-rounded","p-button-success","p-mr-2",3,"click"],["colspan","3"]],template:function(t,e){1&t&&(o.Qb(0,"div",0),o.Qb(1,"div",1),o.Qb(2,"h4"),o.Jc(3,"Order Radiologi"),o.Pb(),o.Hc(4,$,51,20,"div",2),o.Hc(5,X,14,13,"div",2),o.Pb(),o.Qb(6,"p-dialog",3),o.Yb("visibleChange",function(t){return e.pop_expertise=t}),o.Qb(7,"div",0),o.Qb(8,"div",4),o.Qb(9,"div",5),o.Qb(10,"label",6),o.Jc(11,"Tgl Expertise"),o.Pb(),o.Qb(12,"input",7),o.Yb("ngModelChange",function(t){return e.item.tglExp=t}),o.Pb(),o.Pb(),o.Qb(13,"div",5),o.Qb(14,"label",6),o.Jc(15,"Dokter"),o.Pb(),o.Qb(16,"input",7),o.Yb("ngModelChange",function(t){return e.item.dokterExp=t}),o.Pb(),o.Pb(),o.Qb(17,"div",8),o.Qb(18,"label",6),o.Jc(19,"Keterangan"),o.Pb(),o.Qb(20,"textarea",9),o.Yb("ngModelChange",function(t){return e.item.expertise=t}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Hc(21,Z,1,0,"ng-template",10),o.Pb(),o.Qb(22,"p-dialog",11),o.Yb("visibleChange",function(t){return e.pop_paket=t}),o.Qb(23,"div",12),o.Qb(24,"div",8),o.Qb(25,"p-panel",13),o.Qb(26,"div",4),o.Qb(27,"div",8),o.Qb(28,"p-table",14,15),o.Hc(30,tt,5,0,"ng-template",16),o.Hc(31,it,3,1,"ng-template",17),o.Hc(32,ot,5,3,"ng-template",18),o.Hc(33,st,6,1,"ng-template",19),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Hc(34,bt,1,0,"ng-template",10),o.Pb(),o.Pb()),2&t&&(o.xb(4),o.ic("ngIf",!e.isRiwayat),o.xb(1),o.ic("ngIf",e.isRiwayat),o.xb(1),o.Ec(o.mc(29,dt)),o.ic("visible",e.pop_expertise)("modal",!0)("maximizable",!0)("draggable",!0)("resizable",!0),o.xb(6),o.ic("ngModel",e.item.tglExp),o.xb(4),o.ic("ngModel",e.item.dokterExp),o.xb(4),o.ic("ngModel",e.item.expertise),o.xb(2),o.Ec(o.mc(30,pt)),o.ic("visible",e.pop_paket)("modal",!0)("maximizable",!0)("draggable",!0)("resizable",!0),o.xb(3),o.ic("toggleable",!0),o.xb(3),o.ic("columns",e.columnPaket)("value",e.dataSourcePaket)("rowHover",!0)("rows",20)("showCurrentPageReport",!0)("rowsPerPageOptions",o.mc(31,V))("paginator",!0)("pageLinks",5)("globalFilterFields",o.mc(32,ut)))},directives:[a.l,u.a,h.a,g.a,h.g,h.h,m.a,s.k,f.a,k.h,P.a,v.a,y.a,x.b,Q.a,a.k,w.a,C.a,S.a,k.g,k.f,M.a,k.d,x.a],pipes:[j.a],styles:[".p-field-checkbox[_ngcontent-%COMP%], .p-field-radiobutton[_ngcontent-%COMP%]{margin-bottom:0;display:flex;align-items:center}"]}),t})()}];let gt=(()=>{class t{}return t.\u0275mod=o.Hb({type:t}),t.\u0275inj=o.Gb({factory:function(e){return new(e||t)},imports:[[n.j.forChild(ht)],n.j]}),t})();var mt=i("PCNd");let ft=(()=>{class t{}return t.\u0275mod=o.Hb({type:t}),t.\u0275inj=o.Gb({factory:function(e){return new(e||t)},imports:[[a.b,gt,mt.a]]}),t})()}}]);