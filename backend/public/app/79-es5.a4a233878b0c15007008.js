!function(){function t(t,a){if(!(t instanceof a))throw new TypeError("Cannot call a class as a function")}function a(t,a){for(var i=0;i<a.length;i++){var e=a[i];e.enumerable=e.enumerable||!1,e.configurable=!0,"value"in e&&(e.writable=!0),Object.defineProperty(t,e.key,e)}}(window.webpackJsonp=window.webpackJsonp||[]).push([[79],{DRXx:function(i,e,n){"use strict";n.r(e),n.d(e,"InputOrderBarangModule",function(){return L});var r=n("ofXK"),o=n("tyNb"),s=n("7zfz"),l=n("wd/R"),u=n("fXoL"),d=n("EJUL"),h=n("U+s4"),b=n("G0w9"),m=n("ujBT"),g=n("7CaW"),c=n("3Pt+"),p=n("7kUa"),k=n("eO1q"),f=n("arFO"),v=n("V5BG"),P=n("Ks7X"),S=n("jIHw"),Q=n("xlun"),w=n("rEr+");function y(t,a){if(1&t&&(u.Qb(0,"th"),u.Jc(1),u.Pb()),2&t){var i=a.$implicit;u.Fc("width: ",i.width,""),u.xb(1),u.Lc(" ",i.header," ")}}function T(t,a){if(1&t&&(u.Qb(0,"tr"),u.Lb(1,"th",32),u.Hc(2,y,2,4,"th",33),u.Pb()),2&t){var i=a.$implicit;u.xb(2),u.ic("ngForOf",i)}}function j(t,a){if(1&t&&(u.Qb(0,"span"),u.Jc(1),u.Pb()),2&t){var i=u.bc().$implicit,e=u.bc().$implicit,n=u.bc();u.xb(1),u.Lc(" ",n.formatRupiah(e[i.field],""),"")}}function M(t,a){if(1&t&&(u.Qb(0,"span"),u.Jc(1),u.Pb()),2&t){var i=u.bc().$implicit,e=u.bc().$implicit;u.xb(1),u.Lc(" ",e[i.field],"")}}function K(t,a){if(1&t&&(u.Qb(0,"td"),u.Hc(1,j,2,1,"span",36),u.Hc(2,M,2,1,"span",36),u.Pb()),2&t){var i=a.$implicit;u.Fc("width: ",i.width,""),u.xb(1),u.ic("ngIf",null!=i.isCurrency),u.xb(1),u.ic("ngIf",null==i.isCurrency)}}function R(t,a){if(1&t){var i=u.Sb();u.Qb(0,"tr"),u.Qb(1,"td",32),u.Qb(2,"button",34),u.Yb("click",function(){u.yc(i);var t=a.$implicit;return u.bc().editD(t)}),u.Pb(),u.Qb(3,"button",35),u.Yb("click",function(){u.yc(i);var t=a.$implicit;return u.bc().hapusD(t)}),u.Pb(),u.Pb(),u.Hc(4,K,3,5,"td",33),u.Pb()}if(2&t){var e=a.columns;u.xb(4),u.ic("ngForOf",e)}}function C(t,a){if(1&t&&(u.Qb(0,"tr"),u.Qb(1,"td",37),u.Jc(2,"Grand Total : "),u.Pb(),u.Qb(3,"td"),u.Jc(4),u.Pb(),u.Pb()),2&t){var i=u.bc();u.xb(4),u.Lc(" \xa0",i.item.totalSubTotal,"")}}var O,x,J,D=function(){return[5,10,25,50]},I=[{path:"",component:(O=function(){function i(a,e,n,r,o,s,l,u,d){t(this,i),this.apiService=a,this.authService=e,this.confirmationService=n,this.messageService=r,this.cacheHelper=o,this.dateHelper=s,this.alertService=l,this.route=u,this.router=d,this.params={},this.item={},this.listSatuan=[],this.listProduk=[],this.dataProdukDetail=[],this.columnGrid=[],this.dataSource=[],this.data2=[],this.hrg1=0,this.statusTambah=!0}var e,n,r;return e=i,(n=[{key:"ngOnInit",value:function(){this.disabledRuangan=!1,this.isSimpan=!1,this.dateNow=new Date,this.dataLogin=this.authService.getDataLoginUser(),this.kelUser=this.dataLogin.kelompokUser.kelompokUser,this.item.tglOrder=this.dateNow,this.loadColumn(),this.loadCombo(),this.firstLoad()}},{key:"formatRupiah",value:function(t,a){return a+" "+parseFloat(t).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,"$1,")}},{key:"loadColumn",value:function(){this.columnGrid=[{field:"no",header:"No",width:"80px"},{field:"produkfk",header:"Kode Produk",width:"100px"},{field:"namaproduk",header:"Nama Produk",width:"180px"},{field:"asalproduk",header:"Asal Produk",width:"120px"},{field:"satuanstandar",header:"Satuan",width:"120px"},{field:"jmlstok",header:"Stok",width:"120px"},{field:"jumlah",header:"Qty Order",width:"140px"}]}},{key:"loadCombo",value:function(){var t=this;this.apiService.get("logistik/get-combo-logistik").subscribe(function(a){var i=a;t.listRuangan=null!=t.dataLogin.mapLoginUserToRuangan?t.dataLogin.mapLoginUserToRuangan:i.ruangfarmasi,t.listJenisKirim=i.jeniskirim}),this.apiService.get("logistik/get-combo-distribusi").subscribe(function(a){t.listAsalProduk=a.asalproduk})}},{key:"firstLoad",value:function(){var t=this;this.route.params.subscribe(function(a){t.params.norec_data=a.norec,t.norec_data=a.norec,t.loadData()})}},{key:"loadData",value:function(){var t=this;"-"!=this.norec_data&&this.apiService.get("logistik/get-detail-order-barang-ruangan?norecOrder="+this.norec_data).subscribe(function(a){var i=a.head,e=a.detail;t.item.nomorOrder=i.noorder,t.item.tglOrder=l(i.tglorder).format("YYYY-MM-DD HH:mm"),t.item.dataJenisKirim={id:i.jeniskirimfk,jeniskirim:i.jeniskirim},t.item.ruangan={id:i.objectruanganasalfk,namaruangan:i.namaruanganasal},t.listRuanganAll=[{id:i.objectruangantujuanfk,namaruangan:i.namaruangantujuan}],t.item.dataRuanganAll={id:i.objectruangantujuanfk,namaruangan:i.namaruangantujuan},t.item.keteranganOrder=i.keteranganorder,t.data2=e;for(var n=0,r=0;r<t.data2.length;r++){var o=t.data2[r];o.no=r+1,n+=parseFloat(o.total)}t.dataSource=t.data2,t.item.totalSubTotal=t.formatRupiah(n,"Rp.")})}},{key:"filterRuangan",value:function(t){var a=this;this.apiService.get("general/get-data-combo-ruangan-part?namaruangan="+t.query).subscribe(function(t){a.listRuanganAll=t})}},{key:"filterProduk",value:function(t){var a=this;this.apiService.get("logistik/get-produk-distribusi?namaproduk="+t.query).subscribe(function(t){a.listProduk=t})}},{key:"getSatuan",value:function(){null!=this.item.produk.id&&this.GETKONVERSI()}},{key:"GETKONVERSI",value:function(){var t=this;this.listSatuan=this.item.produk.konversisatuan,0==this.listSatuan.length&&(this.listSatuan=[{ssid:this.item.produk.ssid,satuanstandar:this.item.produk.satuanstandar}]),this.item.satuan={ssid:this.item.produk.ssid,satuanstandar:this.item.produk.satuanstandar},this.item.nilaiKonversi=1,null!=this.item.ruangan&&(this.statusTambah=!1,this.apiService.get("general/get-produkdetail-general?produkfk="+this.item.produk.id+"&ruanganfk="+this.item.dataRuanganAll.id).subscribe(function(a){t.dataProdukDetail=a.detail,t.item.stok=a.jmlstok/t.item.nilaiKonversi,t.onChangeKonversi(),t.dataProdukDetail.length>0&&(t.tglkadaluarsa=l(t.dataProdukDetail[0].tglkadaluarsa).format("YYYY-MM-DD HH:mm"),t.listAsalProduk=[{id:t.dataProdukDetail[0].objectasalprodukfk,asalproduk:t.dataProdukDetail[0].asalproduk}],t.item.asal=t.listAsalProduk[0],null!=t.dataSelected&&(t.item.nilaiKonversi=t.dataSelected.nilaikonversi,t.item.stok=t.dataSelected.jmlstok,t.item.jumlah=t.dataSelected.jumlah,t.item.hargaSatuan=t.dataSelected.hargasatuan,t.item.hargaTotal=t.dataSelected.total,t.item.hargadiskon=0)),t.statusTambah=!0,t.gettotal()}))}},{key:"onChangeKonversi",value:function(){this.item.stok>0&&(this.item.stok=parseFloat(this.item.stok)*parseFloat(this.item.nilaiKonversi),this.item.jumlah=1,this.item.hargaSatuan=0,this.item.hargaTotal=0,this.item.hargadiskon=0)}},{key:"getNilaiKonversi",value:function(){this.item.nilaiKonversi=this.item.satuan.nilaikonversi}},{key:"onChangeQty",value:function(t){this.item.jumlah=t.value,this.gettotal()}},{key:"gettotal",value:function(){for(var t=!1,a=0;a<this.dataProdukDetail.length;a++)if(t=!1,this.item.jumlah*parseFloat(this.item.nilaiKonversi)>0){this.hrg1=Math.round(parseFloat(this.dataProdukDetail[a].hargajual)*parseFloat(this.item.nilaiKonversi)),this.item.hargaSatuan=parseFloat(this.hrg1),this.item.hargaTotal=this.item.jumlah*this.hrg1,this.item.hargadiskon=0,this.noTerima=this.dataProdukDetail[a].norec,t=!0;break}0==t&&(this.item.hargaSatuan=0,this.item.total=0,this.noTerima=""),0==this.item.jumlah&&(this.item.hargaSatuan=0)}},{key:"Kosongkan",value:function(){this.dataSelected=void 0,delete this.item.produk,delete this.item.satuan,delete this.item.nilaiKonversi,this.item.no=void 0,this.item.stok=0,this.hrg1=0,this.item.jumlah=0,this.item.hargaSatuan=0,this.item.hargadiskon=0,this.item.total=0,this.item.hargaTotal=0,this.noTerima=void 0,this.tglkadaluarsa=void 0,this.listAsalProduk=void 0,this.item.asal=void 0}},{key:"tambah",value:function(){if(0!=this.statusTambah)if(null!=this.item.dataRuanganAll)if(null!=this.item.produk)if(null!=this.item.satuan)if(0!=this.item.jumlah)if(0!=parseFloat(this.item.stok))if(this.item.jumlah>parseFloat(this.item.stok))this.alertService.error("Info","Jumlah Order Melebihi Stok!");else{if(""==this.noTerima)return this.item.jumlah=0,void this.alertService.error("Info","Jumlah harus di isi!");var t;t=null==this.dataSource?1:this.data2.length+1;var a={};if(this.disabledRuangan=!0,null!=this.item.no){for(var i=this.data2.length-1;i>=0;i--)if(this.data2[i].no==this.item.no){a.no=this.item.no,a.hargajual=String(this.item.hargaSatuan),a.jenisobatfk=null,a.stock=String(this.item.stok),a.harganetto=String(this.item.hargaSatuan),a.nostrukterimafk=this.noTerima,a.ruanganfk=this.item.ruangan.id,a.asalprodukfk=this.item.asal.id,a.asalproduk=this.item.asal.asalproduk,a.produkfk=this.item.produk.id,a.kdproduk=this.item.produk.id,a.namaproduk=this.item.produk.namaproduk,a.nilaikonversi=this.item.nilaiKonversi,a.satuanstandarfk=this.item.satuan.ssid,a.satuanstandar=this.item.satuan.satuanstandar,a.satuanviewfk=this.item.satuan.ssid,a.satuanview=this.item.satuan.satuanstandar,a.jmlstok=String(this.item.stok),a.jumlah=this.item.jumlah,a.hargasatuan=String(this.item.hargaSatuan),a.hargadiscount=String(this.item.hargadiskon),a.total=this.item.hargaTotal,this.data2[i]=a,this.dataSource=this.data2;var e=0,n=0;for(i=this.data2.length-1;i>=0;i--)e+=parseFloat(this.data2[i].total),n+=i;this.item.totalSubTotal=this.formatRupiah(e,"Rp."),this.item.TotalItem=parseFloat(n)}}else{a={no:t,hargajual:String(this.item.hargaSatuan),jenisobatfk:null,stock:String(this.item.stok),harganetto:String(this.item.hargaSatuan),nostrukterimafk:this.noTerima,ruanganfk:this.item.ruangan.id,asalprodukfk:this.item.asal.id,asalproduk:this.item.asal.asalproduk,produkfk:this.item.produk.id,kdproduk:this.item.produk.id,namaproduk:this.item.produk.namaproduk,nilaikonversi:this.item.nilaiKonversi,satuanstandarfk:this.item.satuan.ssid,satuanstandar:this.item.satuan.satuanstandar,satuanviewfk:this.item.satuan.ssid,satuanview:this.item.satuan.satuanstandar,jmlstok:String(this.item.stok),jumlah:this.item.jumlah,hargasatuan:String(this.item.hargaSatuan),hargadiscount:String(this.item.hargadiskon),total:this.item.hargaTotal},this.data2.push(a),this.dataSource=this.data2;var r=0;for(n=0,i=this.data2.length-1;i>=0;i--)r+=parseFloat(this.data2[i].total),n+=i;this.item.totalSubTotal=this.formatRupiah(r,"Rp."),this.item.TotalItem=parseFloat(n)}this.Kosongkan()}else this.alertService.error("Info","Stok tidak ada!");else this.alertService.error("Info","Jumlah harus di isi!");else this.alertService.error("Info","Pilih Satuan terlebih dahulu!!");else this.alertService.error("Info","Pilih Produk terlebih dahulu!!");else this.alertService.error("Info","Data Ruangan Tujuan Masih Kosong!")}},{key:"batal",value:function(){this.Kosongkan()}},{key:"editD",value:function(t){var a=this;if(0!=this.statusTambah){var i=[];this.dataSelected=t,this.item.no=t.no,this.item.asal={id:t.asalprodukfk,asalproduk:t.asalproduk},this.apiService.get("logistik/get-produk-distribusi?idproduk="+t.produkfk).subscribe(function(e){a.listProduk=e;for(var n=a.listProduk.length-1;n>=0;n--)if(a.listProduk[n].id==t.produkfk){i=a.listProduk[n];break}a.item.produk=i,a.GETKONVERSI()})}}},{key:"hapusD",value:function(t){for(var a=this.data2.length-1;a>=0;a--)if(this.data2[a].no==t.no){this.data2.splice(a,1);var i=0,e=0;for(a=this.data2.length-1;a>=0;a--)i+=parseFloat(this.data2[a].total),e+=a,this.data2[a].no=a+1;this.dataSource=this.data2,this.item.totalSubTotal=this.formatRupiah(i,"Rp."),this.item.TotalItem=parseFloat(e)}this.Kosongkan()}},{key:"batalGrid",value:function(){this.Kosongkan(),this.data2=[],this.dataSource=void 0}},{key:"Kembali",value:function(){window.history.back()}},{key:"save",value:function(){var t=this;if(null!=this.item.ruangan)if(null!=this.item.dataRuanganAll)if(null!=this.item.dataJenisKirim)if(null!=this.item.keteranganOrder)if(0!=this.data2.length){this.isSimpan=!0;var a="Order Barang";null==this.item.keteranganOrder&&""==this.item.keteranganOrder||(a=this.item.keteranganOrder);var i={norecorder:this.norec_data,pegawaiorderfk:this.dataLogin.pegawai.id,ruanganfk:this.item.ruangan.id,ruangantujuanfk:this.item.dataRuanganAll.id,jenispermintaanfk:this.item.dataJenisKirim.id,keteranganorder:a,qtyjenisproduk:this.data2.length,tglorder:l(this.item.tglOrder).format("YYYY-MM-DD hh:mm")};this.apiService.post("logistik/save-order-barang-ruangan",{strukorder:i,details:this.data2}).subscribe(function(a){t.apiService.postLog("Simpan Order Barang Ruangan","Norec transaksiorder",a.data.norec,"Simpan Order Barang Ruangan Dengan Noorder - "+a.data.noorder).subscribe(function(t){}),t.Kosongkan(),t.data2=[],t.dataSource=void 0,window.history.back()})}else alert("Pilih Produk terlebih dahulu!!");else alert("Keterangan Tidak Boleh Kosong!!");else alert("Pilih Jenis Kiriman!!");else this.alertService.error("Info","Pilih Ruangan Tujuan!");else this.alertService.error("Info","Pilih Ruangan Pengorder!")}}])&&a(e.prototype,n),r&&a(e,r),i}(),O.\u0275fac=function(t){return new(t||O)(u.Jb(d.a),u.Jb(d.b),u.Jb(s.a),u.Jb(s.h),u.Jb(h.a),u.Jb(b.a),u.Jb(m.a),u.Jb(o.a),u.Jb(o.f))},O.\u0275cmp=u.Db({type:O,selectors:[["app-input-order-barang"]],features:[u.wb([s.a])],decls:76,vars:42,consts:[[1,"p-fluid","p-formgrid"],[1,"card","card-w-title"],[1,"p-grid"],[1,"p-col-12","p-md-3"],["header","Data Order",3,"toggleable"],[1,"p-col-12","p-md-12"],["for","input",1,"label"],["type","text","pInputText","","placeholder","Nomor Order","disabled","",3,"ngModel","ngModelChange"],["yearRange","1980:2030","dateFormat","yy-mm-dd","placeholder","Tgl Order","hourFormat","24",3,"ngModel","showIcon","monthNavigator","yearNavigator","showTime","maxDate","ngModelChange"],["placeholder","Jenis Kirim","optionLabel","jeniskirim","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange"],["placeholder","Ruangan ","optionLabel","namaruangan","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange"],["field","namaruangan","placeholder","Pilih Ruangan",3,"ngModel","suggestions","dropdown","disabled","ngModelChange","completeMethod"],["type","text","pInputText","","placeholder","Keterangan Order",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-9"],["header","Detail Order",3,"toggleable"],[1,"p-col-12","p-md-6"],["field","namaproduk","placeholder","Pilih Produk",3,"ngModel","suggestions","dropdown","ngModelChange","completeMethod","onSelect"],[1,"p-col-12","p-md-2"],["placeholder","Satuan ","optionLabel","satuanstandar","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange","onChange"],["type","text","pInputText","","placeholder","Stok",3,"ngModel","ngModelChange"],["placeholder","Qty","inputId","stacked",3,"ngModel","showButtons","ngModelChange","onInput"],[1,"p-col-12","p-md-2","p-md-offset-8"],["pButton","","type","button","icon","pi pi-plus","pTooltip","Tambah","label","Tambah",1,"p-button","p-button-success","p-mr-2",3,"disabled","click"],["pButton","","type","button","icon","pi pi-refresh","pTooltip","Batal","label","Batal",1,"p-button","p-button-warning","p-mr-2",3,"click"],["styleClass","p-datatable-customers","scrollable","true","dataKey","no",3,"value","columns","rows","showCurrentPageReport","rowsPerPageOptions","paginator","pageLinks"],["pTemplate","header"],["pTemplate","body"],["pTemplate","summary"],[1,"p-col-12","p-md-2","p-md-offset-6"],["pButton","","type","button","icon","pi pi-save","pTooltip","Simpan Data","label","Simpan",1,"p-button","p-mr-2",3,"disabled","click"],["pButton","","type","button","icon","pi pi-refresh","pTooltip","Batal","label","Reset",1,"p-button","p-button-danger","p-mr-2",3,"click"],["pButton","","type","button","icon","fa fa-arrow-left","pTooltip","Kembali","label","Kembali",1,"p-button","p-button-danger","p-mr-2",3,"click"],[2,"width","80px"],[3,"style",4,"ngFor","ngForOf"],["pButton","","type","button","icon","pi pi-user-edit","pTooltip","Edit Data","label","",1,"p-button-rounded","p-mr-2",3,"click"],["pButton","","type","button","icon","pi pi-trash","pTooltip","Hapus Data","label","",1,"p-button-rounded","p-button-danger","p-mr-2",3,"click"],[4,"ngIf"],["rowspan","6"]],template:function(t,a){1&t&&(u.Qb(0,"div",0),u.Qb(1,"div",1),u.Qb(2,"h4"),u.Jc(3,"Order Barang Ruangan"),u.Pb(),u.Qb(4,"div",2),u.Qb(5,"div",3),u.Qb(6,"div",2),u.Qb(7,"p-panel",4),u.Qb(8,"div",5),u.Qb(9,"div",2),u.Qb(10,"div",5),u.Qb(11,"label",6),u.Jc(12,"Nomor Order"),u.Pb(),u.Qb(13,"input",7),u.Yb("ngModelChange",function(t){return a.item.nomorOrder=t}),u.Pb(),u.Pb(),u.Qb(14,"div",5),u.Qb(15,"label",6),u.Jc(16,"Tgl Order"),u.Pb(),u.Qb(17,"p-calendar",8),u.Yb("ngModelChange",function(t){return a.item.tglOrder=t}),u.Pb(),u.Pb(),u.Qb(18,"div",5),u.Qb(19,"label",6),u.Jc(20,"Jenis Order"),u.Pb(),u.Qb(21,"p-dropdown",9),u.Yb("ngModelChange",function(t){return a.item.dataJenisKirim=t}),u.Pb(),u.Pb(),u.Qb(22,"div",5),u.Qb(23,"label",6),u.Jc(24,"Ruangan Asal"),u.Pb(),u.Qb(25,"p-dropdown",10),u.Yb("ngModelChange",function(t){return a.item.ruangan=t}),u.Pb(),u.Pb(),u.Qb(26,"div",5),u.Qb(27,"label",6),u.Jc(28,"Ruangan Tujuan"),u.Pb(),u.Qb(29,"p-autoComplete",11),u.Yb("ngModelChange",function(t){return a.item.dataRuanganAll=t})("completeMethod",function(t){return a.filterRuangan(t)}),u.Pb(),u.Pb(),u.Qb(30,"div",5),u.Qb(31,"label",6),u.Jc(32,"Keterangan Order"),u.Pb(),u.Qb(33,"input",12),u.Yb("ngModelChange",function(t){return a.item.keteranganOrder=t}),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Qb(34,"div",13),u.Qb(35,"div",2),u.Qb(36,"div",5),u.Qb(37,"div",2),u.Qb(38,"p-panel",14),u.Qb(39,"div",5),u.Qb(40,"div",2),u.Qb(41,"div",15),u.Qb(42,"label",6),u.Jc(43,"Produk "),u.Pb(),u.Qb(44,"p-autoComplete",16),u.Yb("ngModelChange",function(t){return a.item.produk=t})("completeMethod",function(t){return a.filterProduk(t)})("onSelect",function(){return a.getSatuan()}),u.Pb(),u.Pb(),u.Qb(45,"div",17),u.Qb(46,"label",6),u.Jc(47,"Satuan "),u.Pb(),u.Qb(48,"p-dropdown",18),u.Yb("ngModelChange",function(t){return a.item.satuan=t})("onChange",function(){return a.getNilaiKonversi()}),u.Pb(),u.Pb(),u.Qb(49,"div",17),u.Qb(50,"label",6),u.Jc(51,"Stok"),u.Pb(),u.Qb(52,"input",19),u.Yb("ngModelChange",function(t){return a.item.stok=t}),u.Pb(),u.Pb(),u.Qb(53,"div",17),u.Qb(54,"label",6),u.Jc(55,"Qty Order "),u.Pb(),u.Qb(56,"p-inputNumber",20),u.Yb("ngModelChange",function(t){return a.item.jumlah=t})("onInput",function(t){return a.onChangeQty(t)}),u.Pb(),u.Pb(),u.Pb(),u.Qb(57,"div",2),u.Qb(58,"div",21),u.Qb(59,"button",22),u.Yb("click",function(){return a.tambah()}),u.Pb(),u.Pb(),u.Qb(60,"div",17),u.Qb(61,"button",23),u.Yb("click",function(){return a.batal()}),u.Pb(),u.Pb(),u.Pb(),u.Qb(62,"div",2),u.Qb(63,"div",5),u.Qb(64,"p-table",24),u.Hc(65,T,3,1,"ng-template",25),u.Hc(66,R,5,1,"ng-template",26),u.Hc(67,C,5,1,"ng-template",27),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Qb(68,"div",5),u.Qb(69,"div",2),u.Qb(70,"div",28),u.Qb(71,"button",29),u.Yb("click",function(){return a.save()}),u.Pb(),u.Pb(),u.Qb(72,"div",17),u.Qb(73,"button",30),u.Yb("click",function(){return a.batalGrid()}),u.Pb(),u.Pb(),u.Qb(74,"div",17),u.Qb(75,"button",31),u.Yb("click",function(){return a.Kembali()}),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb(),u.Pb()),2&t&&(u.xb(7),u.ic("toggleable",!0),u.xb(6),u.ic("ngModel",a.item.nomorOrder),u.xb(4),u.ic("ngModel",a.item.tglOrder)("showIcon",!0)("monthNavigator",!0)("yearNavigator",!0)("showTime",!0)("maxDate",a.maxDateValue),u.xb(4),u.ic("options",a.listJenisKirim)("ngModel",a.item.dataJenisKirim)("filter",!0)("showClear",!0),u.xb(4),u.ic("options",a.listRuangan)("ngModel",a.item.ruangan)("filter",!0)("showClear",!0),u.xb(4),u.ic("ngModel",a.item.dataRuanganAll)("suggestions",a.listRuanganAll)("dropdown",!0)("disabled",a.disabledRuangan),u.xb(4),u.ic("ngModel",a.item.keteranganOrder),u.xb(5),u.ic("toggleable",!1),u.xb(6),u.ic("ngModel",a.item.produk)("suggestions",a.listProduk)("dropdown",!0),u.xb(4),u.ic("options",a.listSatuan)("ngModel",a.item.satuan)("filter",!0)("showClear",!0),u.xb(4),u.ic("ngModel",a.item.stok),u.xb(4),u.ic("ngModel",a.item.jumlah)("showButtons",!0),u.xb(3),u.ic("disabled",!a.statusTambah),u.xb(5),u.ic("value",a.dataSource)("columns",a.columnGrid)("rows",20)("showCurrentPageReport",!0)("rowsPerPageOptions",u.mc(41,D))("paginator",!0)("pageLinks",5),u.xb(7),u.ic("disabled",a.isSimpan))},directives:[g.a,c.a,p.a,c.g,c.h,k.a,f.a,v.a,P.a,S.b,Q.a,w.h,s.k,r.k,r.l],styles:[""]}),O)}],Y=((x=function a(){t(this,a)}).\u0275mod=u.Hb({type:x}),x.\u0275inj=u.Gb({factory:function(t){return new(t||x)},imports:[[o.j.forChild(I)],o.j]}),x),F=n("PCNd"),L=((J=function a(){t(this,a)}).\u0275mod=u.Hb({type:J}),J.\u0275inj=u.Gb({factory:function(t){return new(t||J)},imports:[[r.b,Y,F.a]]}),J)}}])}();