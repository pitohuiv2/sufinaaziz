(window.webpackJsonp=window.webpackJsonp||[]).push([[79],{DRXx:function(t,a,i){"use strict";i.r(a),i.d(a,"InputOrderBarangModule",function(){return J});var e=i("ofXK"),n=i("tyNb"),r=i("7zfz"),s=i("wd/R"),o=i("fXoL"),l=i("EJUL"),d=i("U+s4"),h=i("G0w9"),u=i("ujBT"),b=i("7CaW"),m=i("3Pt+"),g=i("7kUa"),p=i("eO1q"),c=i("arFO"),k=i("V5BG"),f=i("Ks7X"),P=i("jIHw"),v=i("xlun"),S=i("rEr+");function Q(t,a){if(1&t&&(o.Qb(0,"th"),o.Jc(1),o.Pb()),2&t){const t=a.$implicit;o.Fc("width: ",t.width,""),o.xb(1),o.Lc(" ",t.header," ")}}function w(t,a){if(1&t&&(o.Qb(0,"tr"),o.Lb(1,"th",32),o.Hc(2,Q,2,4,"th",33),o.Pb()),2&t){const t=a.$implicit;o.xb(2),o.ic("ngForOf",t)}}function T(t,a){if(1&t&&(o.Qb(0,"span"),o.Jc(1),o.Pb()),2&t){const t=o.bc().$implicit,a=o.bc().$implicit,i=o.bc();o.xb(1),o.Lc(" ",i.formatRupiah(a[t.field],""),"")}}function M(t,a){if(1&t&&(o.Qb(0,"span"),o.Jc(1),o.Pb()),2&t){const t=o.bc().$implicit,a=o.bc().$implicit;o.xb(1),o.Lc(" ",a[t.field],"")}}function j(t,a){if(1&t&&(o.Qb(0,"td"),o.Hc(1,T,2,1,"span",36),o.Hc(2,M,2,1,"span",36),o.Pb()),2&t){const t=a.$implicit;o.Fc("width: ",t.width,""),o.xb(1),o.ic("ngIf",null!=t.isCurrency),o.xb(1),o.ic("ngIf",null==t.isCurrency)}}function K(t,a){if(1&t){const t=o.Sb();o.Qb(0,"tr"),o.Qb(1,"td",32),o.Qb(2,"button",34),o.Yb("click",function(){o.yc(t);const i=a.$implicit;return o.bc().editD(i)}),o.Pb(),o.Qb(3,"button",35),o.Yb("click",function(){o.yc(t);const i=a.$implicit;return o.bc().hapusD(i)}),o.Pb(),o.Pb(),o.Hc(4,j,3,5,"td",33),o.Pb()}if(2&t){const t=a.columns;o.xb(4),o.ic("ngForOf",t)}}function R(t,a){if(1&t&&(o.Qb(0,"tr"),o.Qb(1,"td",37),o.Jc(2,"Grand Total : "),o.Pb(),o.Qb(3,"td"),o.Jc(4),o.Pb(),o.Pb()),2&t){const t=o.bc();o.xb(4),o.Lc(" \xa0",t.item.totalSubTotal,"")}}const C=function(){return[5,10,25,50]},y=[{path:"",component:(()=>{class t{constructor(t,a,i,e,n,r,s,o,l){this.apiService=t,this.authService=a,this.confirmationService=i,this.messageService=e,this.cacheHelper=n,this.dateHelper=r,this.alertService=s,this.route=o,this.router=l,this.params={},this.item={},this.listSatuan=[],this.listProduk=[],this.dataProdukDetail=[],this.columnGrid=[],this.dataSource=[],this.data2=[],this.hrg1=0,this.statusTambah=!0}ngOnInit(){this.disabledRuangan=!1,this.isSimpan=!1,this.dateNow=new Date,this.dataLogin=this.authService.getDataLoginUser(),this.kelUser=this.dataLogin.kelompokUser.kelompokUser,this.item.tglOrder=this.dateNow,this.loadColumn(),this.loadCombo(),this.firstLoad()}formatRupiah(t,a){return a+" "+parseFloat(t).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,"$1,")}loadColumn(){this.columnGrid=[{field:"no",header:"No",width:"80px"},{field:"produkfk",header:"Kode Produk",width:"100px"},{field:"namaproduk",header:"Nama Produk",width:"180px"},{field:"asalproduk",header:"Asal Produk",width:"120px"},{field:"satuanstandar",header:"Satuan",width:"120px"},{field:"jmlstok",header:"Stok",width:"120px"},{field:"jumlah",header:"Qty Order",width:"140px"}]}loadCombo(){this.apiService.get("logistik/get-combo-logistik").subscribe(t=>{var a=t;this.listRuangan=null!=this.dataLogin.mapLoginUserToRuangan?this.dataLogin.mapLoginUserToRuangan:a.ruangfarmasi,this.listJenisKirim=a.jeniskirim}),this.apiService.get("logistik/get-combo-distribusi").subscribe(t=>{this.listAsalProduk=t.asalproduk})}firstLoad(){this.route.params.subscribe(t=>{this.params.norec_data=t.norec,this.norec_data=t.norec,this.loadData()})}loadData(){"-"!=this.norec_data&&this.apiService.get("logistik/get-detail-order-barang-ruangan?norecOrder="+this.norec_data).subscribe(t=>{var a=t.head,i=t.detail;this.item.nomorOrder=a.noorder,this.item.tglOrder=s(a.tglorder).format("YYYY-MM-DD HH:mm"),this.item.dataJenisKirim={id:a.jeniskirimfk,jeniskirim:a.jeniskirim},this.item.ruangan={id:a.objectruanganasalfk,namaruangan:a.namaruanganasal},this.listRuanganAll=[{id:a.objectruangantujuanfk,namaruangan:a.namaruangantujuan}],this.item.dataRuanganAll={id:a.objectruangantujuanfk,namaruangan:a.namaruangantujuan},this.item.keteranganOrder=a.keteranganorder,this.data2=i;var e=0;for(let n=0;n<this.data2.length;n++){const t=this.data2[n];t.no=n+1,e+=parseFloat(t.total)}this.dataSource=this.data2,this.item.totalSubTotal=this.formatRupiah(e,"Rp.")})}filterRuangan(t){this.apiService.get("general/get-data-combo-ruangan-part?namaruangan="+t.query).subscribe(t=>{this.listRuanganAll=t})}filterProduk(t){this.apiService.get("logistik/get-produk-distribusi?namaproduk="+t.query).subscribe(t=>{this.listProduk=t})}getSatuan(){null!=this.item.produk.id&&this.GETKONVERSI()}GETKONVERSI(){this.listSatuan=this.item.produk.konversisatuan,0==this.listSatuan.length&&(this.listSatuan=[{ssid:this.item.produk.ssid,satuanstandar:this.item.produk.satuanstandar}]),this.item.satuan={ssid:this.item.produk.ssid,satuanstandar:this.item.produk.satuanstandar},this.item.nilaiKonversi=1,null!=this.item.ruangan&&(this.statusTambah=!1,this.apiService.get("general/get-produkdetail-general?produkfk="+this.item.produk.id+"&ruanganfk="+this.item.dataRuanganAll.id).subscribe(t=>{this.dataProdukDetail=t.detail,this.item.stok=t.jmlstok/this.item.nilaiKonversi,this.onChangeKonversi(),this.dataProdukDetail.length>0&&(this.tglkadaluarsa=s(this.dataProdukDetail[0].tglkadaluarsa).format("YYYY-MM-DD HH:mm"),this.listAsalProduk=[{id:this.dataProdukDetail[0].objectasalprodukfk,asalproduk:this.dataProdukDetail[0].asalproduk}],this.item.asal=this.listAsalProduk[0],null!=this.dataSelected&&(this.item.nilaiKonversi=this.dataSelected.nilaikonversi,this.item.stok=this.dataSelected.jmlstok,this.item.jumlah=this.dataSelected.jumlah,this.item.hargaSatuan=this.dataSelected.hargasatuan,this.item.hargaTotal=this.dataSelected.total,this.item.hargadiskon=0)),this.statusTambah=!0,this.gettotal()}))}onChangeKonversi(){this.item.stok>0&&(this.item.stok=parseFloat(this.item.stok)*parseFloat(this.item.nilaiKonversi),this.item.jumlah=1,this.item.hargaSatuan=0,this.item.hargaTotal=0,this.item.hargadiskon=0)}getNilaiKonversi(){this.item.nilaiKonversi=this.item.satuan.nilaikonversi}onChangeQty(t){this.item.jumlah=t.value,this.gettotal()}gettotal(){for(var t=!1,a=0;a<this.dataProdukDetail.length;a++)if(t=!1,this.item.jumlah*parseFloat(this.item.nilaiKonversi)>0){this.hrg1=Math.round(parseFloat(this.dataProdukDetail[a].hargajual)*parseFloat(this.item.nilaiKonversi)),this.item.hargaSatuan=parseFloat(this.hrg1),this.item.hargaTotal=this.item.jumlah*this.hrg1,this.item.hargadiskon=0,this.noTerima=this.dataProdukDetail[a].norec,t=!0;break}0==t&&(this.item.hargaSatuan=0,this.item.total=0,this.noTerima=""),0==this.item.jumlah&&(this.item.hargaSatuan=0)}Kosongkan(){this.dataSelected=void 0,delete this.item.produk,delete this.item.satuan,delete this.item.nilaiKonversi,this.item.no=void 0,this.item.stok=0,this.hrg1=0,this.item.jumlah=0,this.item.hargaSatuan=0,this.item.hargadiskon=0,this.item.total=0,this.item.hargaTotal=0,this.noTerima=void 0,this.tglkadaluarsa=void 0,this.listAsalProduk=void 0,this.item.asal=void 0}tambah(){if(0!=this.statusTambah)if(null!=this.item.dataRuanganAll)if(null!=this.item.produk)if(null!=this.item.satuan)if(0!=this.item.jumlah)if(0!=parseFloat(this.item.stok))if(this.item.jumlah>parseFloat(this.item.stok))this.alertService.error("Info","Jumlah Order Melebihi Stok!");else{if(""==this.noTerima)return this.item.jumlah=0,void this.alertService.error("Info","Jumlah harus di isi!");var t;t=null==this.dataSource?1:this.data2.length+1;var a={};if(this.disabledRuangan=!0,null!=this.item.no){for(var i=this.data2.length-1;i>=0;i--)if(this.data2[i].no==this.item.no){a.no=this.item.no,a.hargajual=String(this.item.hargaSatuan),a.jenisobatfk=null,a.stock=String(this.item.stok),a.harganetto=String(this.item.hargaSatuan),a.nostrukterimafk=this.noTerima,a.ruanganfk=this.item.ruangan.id,a.asalprodukfk=this.item.asal.id,a.asalproduk=this.item.asal.asalproduk,a.produkfk=this.item.produk.id,a.kdproduk=this.item.produk.id,a.namaproduk=this.item.produk.namaproduk,a.nilaikonversi=this.item.nilaiKonversi,a.satuanstandarfk=this.item.satuan.ssid,a.satuanstandar=this.item.satuan.satuanstandar,a.satuanviewfk=this.item.satuan.ssid,a.satuanview=this.item.satuan.satuanstandar,a.jmlstok=String(this.item.stok),a.jumlah=this.item.jumlah,a.hargasatuan=String(this.item.hargaSatuan),a.hargadiscount=String(this.item.hargadiskon),a.total=this.item.hargaTotal,this.data2[i]=a,this.dataSource=this.data2;var e=0,n=0;for(i=this.data2.length-1;i>=0;i--)e+=parseFloat(this.data2[i].total),n+=i;this.item.totalSubTotal=this.formatRupiah(e,"Rp."),this.item.TotalItem=parseFloat(n)}}else{a={no:t,hargajual:String(this.item.hargaSatuan),jenisobatfk:null,stock:String(this.item.stok),harganetto:String(this.item.hargaSatuan),nostrukterimafk:this.noTerima,ruanganfk:this.item.ruangan.id,asalprodukfk:this.item.asal.id,asalproduk:this.item.asal.asalproduk,produkfk:this.item.produk.id,kdproduk:this.item.produk.id,namaproduk:this.item.produk.namaproduk,nilaikonversi:this.item.nilaiKonversi,satuanstandarfk:this.item.satuan.ssid,satuanstandar:this.item.satuan.satuanstandar,satuanviewfk:this.item.satuan.ssid,satuanview:this.item.satuan.satuanstandar,jmlstok:String(this.item.stok),jumlah:this.item.jumlah,hargasatuan:String(this.item.hargaSatuan),hargadiscount:String(this.item.hargadiskon),total:this.item.hargaTotal},this.data2.push(a),this.dataSource=this.data2;var r=0;for(n=0,i=this.data2.length-1;i>=0;i--)r+=parseFloat(this.data2[i].total),n+=i;this.item.totalSubTotal=this.formatRupiah(r,"Rp."),this.item.TotalItem=parseFloat(n)}this.Kosongkan()}else this.alertService.error("Info","Stok tidak ada!");else this.alertService.error("Info","Jumlah harus di isi!");else this.alertService.error("Info","Pilih Satuan terlebih dahulu!!");else this.alertService.error("Info","Pilih Produk terlebih dahulu!!");else this.alertService.error("Info","Data Ruangan Tujuan Masih Kosong!")}batal(){this.Kosongkan()}editD(t){if(0!=this.statusTambah){var a=[];this.dataSelected=t,this.item.no=t.no,this.item.asal={id:t.asalprodukfk,asalproduk:t.asalproduk},this.apiService.get("logistik/get-produk-distribusi?idproduk="+t.produkfk).subscribe(i=>{this.listProduk=i;for(var e=this.listProduk.length-1;e>=0;e--)if(this.listProduk[e].id==t.produkfk){a=this.listProduk[e];break}this.item.produk=a,this.GETKONVERSI()})}}hapusD(t){for(var a=this.data2.length-1;a>=0;a--)if(this.data2[a].no==t.no){this.data2.splice(a,1);var i=0,e=0;for(a=this.data2.length-1;a>=0;a--)i+=parseFloat(this.data2[a].total),e+=a,this.data2[a].no=a+1;this.dataSource=this.data2,this.item.totalSubTotal=this.formatRupiah(i,"Rp."),this.item.TotalItem=parseFloat(e)}this.Kosongkan()}batalGrid(){this.Kosongkan(),this.data2=[],this.dataSource=void 0}Kembali(){window.history.back()}save(){if(null!=this.item.ruangan)if(null!=this.item.dataRuanganAll)if(null!=this.item.dataJenisKirim)if(null!=this.item.keteranganOrder)if(0!=this.data2.length){this.isSimpan=!0;var t="Order Barang";null==this.item.keteranganOrder&&""==this.item.keteranganOrder||(t=this.item.keteranganOrder);var a={norecorder:this.norec_data,pegawaiorderfk:this.dataLogin.pegawai.id,ruanganfk:this.item.ruangan.id,ruangantujuanfk:this.item.dataRuanganAll.id,jenispermintaanfk:this.item.dataJenisKirim.id,keteranganorder:t,qtyjenisproduk:this.data2.length,tglorder:s(this.item.tglOrder).format("YYYY-MM-DD hh:mm")};this.apiService.post("logistik/save-order-barang-ruangan",{strukorder:a,details:this.data2}).subscribe(t=>{this.apiService.postLog("Simpan Order Barang Ruangan","Norec transaksiorder",t.data.norec,"Simpan Order Barang Ruangan Dengan Noorder - "+t.data.noorder).subscribe(t=>{}),this.Kosongkan(),this.data2=[],this.dataSource=void 0,window.history.back()})}else alert("Pilih Produk terlebih dahulu!!");else alert("Keterangan Tidak Boleh Kosong!!");else alert("Pilih Jenis Kiriman!!");else this.alertService.error("Info","Pilih Ruangan Tujuan!");else this.alertService.error("Info","Pilih Ruangan Pengorder!")}}return t.\u0275fac=function(a){return new(a||t)(o.Jb(l.a),o.Jb(l.b),o.Jb(r.a),o.Jb(r.h),o.Jb(d.a),o.Jb(h.a),o.Jb(u.a),o.Jb(n.a),o.Jb(n.f))},t.\u0275cmp=o.Db({type:t,selectors:[["app-input-order-barang"]],features:[o.wb([r.a])],decls:76,vars:42,consts:[[1,"p-fluid","p-formgrid"],[1,"card","card-w-title"],[1,"p-grid"],[1,"p-col-12","p-md-3"],["header","Data Order",3,"toggleable"],[1,"p-col-12","p-md-12"],["for","input",1,"label"],["type","text","pInputText","","placeholder","Nomor Order","disabled","",3,"ngModel","ngModelChange"],["yearRange","1980:2030","dateFormat","yy-mm-dd","placeholder","Tgl Order","hourFormat","24",3,"ngModel","showIcon","monthNavigator","yearNavigator","showTime","maxDate","ngModelChange"],["placeholder","Jenis Kirim","optionLabel","jeniskirim","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange"],["placeholder","Ruangan ","optionLabel","namaruangan","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange"],["field","namaruangan","placeholder","Pilih Ruangan",3,"ngModel","suggestions","dropdown","disabled","ngModelChange","completeMethod"],["type","text","pInputText","","placeholder","Keterangan Order",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-9"],["header","Detail Order",3,"toggleable"],[1,"p-col-12","p-md-6"],["field","namaproduk","placeholder","Pilih Produk",3,"ngModel","suggestions","dropdown","ngModelChange","completeMethod","onSelect"],[1,"p-col-12","p-md-2"],["placeholder","Satuan ","optionLabel","satuanstandar","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange","onChange"],["type","text","pInputText","","placeholder","Stok",3,"ngModel","ngModelChange"],["placeholder","Qty","inputId","stacked",3,"ngModel","showButtons","ngModelChange","onInput"],[1,"p-col-12","p-md-2","p-md-offset-8"],["pButton","","type","button","icon","pi pi-plus","pTooltip","Tambah","label","Tambah",1,"p-button","p-button-success","p-mr-2",3,"disabled","click"],["pButton","","type","button","icon","pi pi-refresh","pTooltip","Batal","label","Batal",1,"p-button","p-button-warning","p-mr-2",3,"click"],["styleClass","p-datatable-customers","scrollable","true","dataKey","no",3,"value","columns","rows","showCurrentPageReport","rowsPerPageOptions","paginator","pageLinks"],["pTemplate","header"],["pTemplate","body"],["pTemplate","summary"],[1,"p-col-12","p-md-2","p-md-offset-6"],["pButton","","type","button","icon","pi pi-save","pTooltip","Simpan Data","label","Simpan",1,"p-button","p-mr-2",3,"disabled","click"],["pButton","","type","button","icon","pi pi-refresh","pTooltip","Batal","label","Reset",1,"p-button","p-button-danger","p-mr-2",3,"click"],["pButton","","type","button","icon","fa fa-arrow-left","pTooltip","Kembali","label","Kembali",1,"p-button","p-button-danger","p-mr-2",3,"click"],[2,"width","80px"],[3,"style",4,"ngFor","ngForOf"],["pButton","","type","button","icon","pi pi-user-edit","pTooltip","Edit Data","label","",1,"p-button-rounded","p-mr-2",3,"click"],["pButton","","type","button","icon","pi pi-trash","pTooltip","Hapus Data","label","",1,"p-button-rounded","p-button-danger","p-mr-2",3,"click"],[4,"ngIf"],["rowspan","6"]],template:function(t,a){1&t&&(o.Qb(0,"div",0),o.Qb(1,"div",1),o.Qb(2,"h4"),o.Jc(3,"Order Barang Ruangan"),o.Pb(),o.Qb(4,"div",2),o.Qb(5,"div",3),o.Qb(6,"div",2),o.Qb(7,"p-panel",4),o.Qb(8,"div",5),o.Qb(9,"div",2),o.Qb(10,"div",5),o.Qb(11,"label",6),o.Jc(12,"Nomor Order"),o.Pb(),o.Qb(13,"input",7),o.Yb("ngModelChange",function(t){return a.item.nomorOrder=t}),o.Pb(),o.Pb(),o.Qb(14,"div",5),o.Qb(15,"label",6),o.Jc(16,"Tgl Order"),o.Pb(),o.Qb(17,"p-calendar",8),o.Yb("ngModelChange",function(t){return a.item.tglOrder=t}),o.Pb(),o.Pb(),o.Qb(18,"div",5),o.Qb(19,"label",6),o.Jc(20,"Jenis Order"),o.Pb(),o.Qb(21,"p-dropdown",9),o.Yb("ngModelChange",function(t){return a.item.dataJenisKirim=t}),o.Pb(),o.Pb(),o.Qb(22,"div",5),o.Qb(23,"label",6),o.Jc(24,"Ruangan Asal"),o.Pb(),o.Qb(25,"p-dropdown",10),o.Yb("ngModelChange",function(t){return a.item.ruangan=t}),o.Pb(),o.Pb(),o.Qb(26,"div",5),o.Qb(27,"label",6),o.Jc(28,"Ruangan Tujuan"),o.Pb(),o.Qb(29,"p-autoComplete",11),o.Yb("ngModelChange",function(t){return a.item.dataRuanganAll=t})("completeMethod",function(t){return a.filterRuangan(t)}),o.Pb(),o.Pb(),o.Qb(30,"div",5),o.Qb(31,"label",6),o.Jc(32,"Keterangan Order"),o.Pb(),o.Qb(33,"input",12),o.Yb("ngModelChange",function(t){return a.item.keteranganOrder=t}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Qb(34,"div",13),o.Qb(35,"div",2),o.Qb(36,"div",5),o.Qb(37,"div",2),o.Qb(38,"p-panel",14),o.Qb(39,"div",5),o.Qb(40,"div",2),o.Qb(41,"div",15),o.Qb(42,"label",6),o.Jc(43,"Produk "),o.Pb(),o.Qb(44,"p-autoComplete",16),o.Yb("ngModelChange",function(t){return a.item.produk=t})("completeMethod",function(t){return a.filterProduk(t)})("onSelect",function(){return a.getSatuan()}),o.Pb(),o.Pb(),o.Qb(45,"div",17),o.Qb(46,"label",6),o.Jc(47,"Satuan "),o.Pb(),o.Qb(48,"p-dropdown",18),o.Yb("ngModelChange",function(t){return a.item.satuan=t})("onChange",function(){return a.getNilaiKonversi()}),o.Pb(),o.Pb(),o.Qb(49,"div",17),o.Qb(50,"label",6),o.Jc(51,"Stok"),o.Pb(),o.Qb(52,"input",19),o.Yb("ngModelChange",function(t){return a.item.stok=t}),o.Pb(),o.Pb(),o.Qb(53,"div",17),o.Qb(54,"label",6),o.Jc(55,"Qty Order "),o.Pb(),o.Qb(56,"p-inputNumber",20),o.Yb("ngModelChange",function(t){return a.item.jumlah=t})("onInput",function(t){return a.onChangeQty(t)}),o.Pb(),o.Pb(),o.Pb(),o.Qb(57,"div",2),o.Qb(58,"div",21),o.Qb(59,"button",22),o.Yb("click",function(){return a.tambah()}),o.Pb(),o.Pb(),o.Qb(60,"div",17),o.Qb(61,"button",23),o.Yb("click",function(){return a.batal()}),o.Pb(),o.Pb(),o.Pb(),o.Qb(62,"div",2),o.Qb(63,"div",5),o.Qb(64,"p-table",24),o.Hc(65,w,3,1,"ng-template",25),o.Hc(66,K,5,1,"ng-template",26),o.Hc(67,R,5,1,"ng-template",27),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Qb(68,"div",5),o.Qb(69,"div",2),o.Qb(70,"div",28),o.Qb(71,"button",29),o.Yb("click",function(){return a.save()}),o.Pb(),o.Pb(),o.Qb(72,"div",17),o.Qb(73,"button",30),o.Yb("click",function(){return a.batalGrid()}),o.Pb(),o.Pb(),o.Qb(74,"div",17),o.Qb(75,"button",31),o.Yb("click",function(){return a.Kembali()}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb()),2&t&&(o.xb(7),o.ic("toggleable",!0),o.xb(6),o.ic("ngModel",a.item.nomorOrder),o.xb(4),o.ic("ngModel",a.item.tglOrder)("showIcon",!0)("monthNavigator",!0)("yearNavigator",!0)("showTime",!0)("maxDate",a.maxDateValue),o.xb(4),o.ic("options",a.listJenisKirim)("ngModel",a.item.dataJenisKirim)("filter",!0)("showClear",!0),o.xb(4),o.ic("options",a.listRuangan)("ngModel",a.item.ruangan)("filter",!0)("showClear",!0),o.xb(4),o.ic("ngModel",a.item.dataRuanganAll)("suggestions",a.listRuanganAll)("dropdown",!0)("disabled",a.disabledRuangan),o.xb(4),o.ic("ngModel",a.item.keteranganOrder),o.xb(5),o.ic("toggleable",!1),o.xb(6),o.ic("ngModel",a.item.produk)("suggestions",a.listProduk)("dropdown",!0),o.xb(4),o.ic("options",a.listSatuan)("ngModel",a.item.satuan)("filter",!0)("showClear",!0),o.xb(4),o.ic("ngModel",a.item.stok),o.xb(4),o.ic("ngModel",a.item.jumlah)("showButtons",!0),o.xb(3),o.ic("disabled",!a.statusTambah),o.xb(5),o.ic("value",a.dataSource)("columns",a.columnGrid)("rows",20)("showCurrentPageReport",!0)("rowsPerPageOptions",o.mc(41,C))("paginator",!0)("pageLinks",5),o.xb(7),o.ic("disabled",a.isSimpan))},directives:[b.a,m.a,g.a,m.g,m.h,p.a,c.a,k.a,f.a,P.b,v.a,S.h,r.k,e.k,e.l],styles:[""]}),t})()}];let O=(()=>{class t{}return t.\u0275mod=o.Hb({type:t}),t.\u0275inj=o.Gb({factory:function(a){return new(a||t)},imports:[[n.j.forChild(y)],n.j]}),t})();var x=i("PCNd");let J=(()=>{class t{}return t.\u0275mod=o.Hb({type:t}),t.\u0275inj=o.Gb({factory:function(a){return new(a||t)},imports:[[e.b,O,x.a]]}),t})()}}]);