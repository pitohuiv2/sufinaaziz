(window.webpackJsonp=window.webpackJsonp||[]).push([[48],{N8le:function(e,t,a){"use strict";a.r(t),a.d(t,"InputResepApotikModule",function(){return H});var i=a("ofXK"),n=a("tyNb"),s=a("7zfz"),r=a("wd/R"),o=a("fXoL"),l=a("EJUL"),h=a("U+s4"),d=a("G0w9"),u=a("ujBT"),p=a("7CaW"),b=a("3Pt+"),m=a("7kUa"),c=a("eO1q"),g=a("arFO"),k=a("Ji6n"),f=a("V5BG"),P=a("Ks7X"),v=a("jIHw"),Q=a("xlun"),M=a("rEr+"),S=a("LuMj");function x(e,t){if(1&e){const e=o.Sb();o.Qb(0,"div",59),o.Qb(1,"p-radioButton",60),o.Yb("ngModelChange",function(t){return o.yc(e),o.bc().item.jenisKemasan=t})("onClick",function(){o.yc(e);const t=o.bc();return t.clickRadio(t.item.jenisKemasan)}),o.Pb(),o.Qb(2,"label",61),o.Jc(3),o.Pb(),o.Pb()}if(2&e){const e=t.$implicit,a=o.bc();o.xb(1),o.ic("inputId",e.id)("value",e)("ngModel",a.item.jenisKemasan),o.xb(1),o.ic("for",e.id),o.xb(1),o.Kc(e.jeniskemasan)}}function R(e,t){if(1&e){const e=o.Sb();o.Qb(0,"div",37),o.Qb(1,"label",6),o.Jc(2,"Jumlah "),o.Pb(),o.Qb(3,"p-inputNumber",62),o.Yb("ngModelChange",function(t){return o.yc(e),o.bc().item.jumlahxmakan=t})("onInput",function(t){return o.yc(e),o.bc().onChangeJmlXMakan(t)}),o.Pb(),o.Pb()}if(2&e){const e=o.bc();o.xb(3),o.ic("ngModel",e.item.jumlahxmakan)("showButtons",!0)}}function C(e,t){if(1&e){const e=o.Sb();o.Qb(0,"div",37),o.Qb(1,"label",6),o.Jc(2,"Dosis "),o.Pb(),o.Qb(3,"input",63),o.Yb("ngModelChange",function(t){return o.yc(e),o.bc().item.dosis=t})("input",function(t){return o.yc(e),o.bc().onChangeJmlXMakan(t.target.value)}),o.Pb(),o.Pb()}if(2&e){const e=o.bc();o.xb(3),o.ic("ngModel",e.item.dosis)}}function j(e,t){if(1&e){const e=o.Sb();o.Qb(0,"div",37),o.Qb(1,"label",6),o.Jc(2,"Jenis Racikan "),o.Pb(),o.Qb(3,"p-dropdown",64),o.Yb("ngModelChange",function(t){return o.yc(e),o.bc().item.jenisRacikan=t}),o.Pb(),o.Pb()}if(2&e){const e=o.bc();o.xb(3),o.ic("options",e.listJenisRacikan)("ngModel",e.item.jenisRacikan)("filter",!0)("showClear",!0)}}function D(e,t){if(1&e){const e=o.Sb();o.Qb(0,"div",65),o.Qb(1,"div",2),o.Qb(2,"div",66),o.Qb(3,"p-checkbox",67),o.Yb("ngModelChange",function(a){o.yc(e);const i=t.$implicit;return o.bc().item.aturanCheck[i.id]=a})("onChange",function(){return o.yc(e),o.bc().getSelected()}),o.Pb(),o.Pb(),o.Qb(4,"div",68),o.Qb(5,"label",61),o.Jc(6),o.Pb(),o.Pb(),o.Pb(),o.Pb()}if(2&e){const e=t.$implicit,a=o.bc();o.xb(3),o.jc("inputId",e.id),o.ic("ngModel",a.item.aturanCheck[e.id]),o.xb(2),o.ic("for",e.id),o.xb(1),o.Lc(" ",e.nama,"")}}function w(e,t){if(1&e&&(o.Qb(0,"th"),o.Jc(1),o.Pb()),2&e){const e=t.$implicit;o.Fc("width: ",e.width,""),o.xb(1),o.Lc(" ",e.header," ")}}function K(e,t){if(1&e&&(o.Qb(0,"tr"),o.Lb(1,"th",69),o.Hc(2,w,2,4,"th",70),o.Pb()),2&e){const e=t.$implicit;o.xb(2),o.ic("ngForOf",e)}}function J(e,t){if(1&e&&(o.Qb(0,"span"),o.Jc(1),o.Pb()),2&e){const e=o.bc().$implicit,t=o.bc().$implicit,a=o.bc();o.xb(1),o.Lc(" ",a.formatRupiah(t[e.field],""),"")}}function y(e,t){if(1&e&&(o.Qb(0,"span"),o.Jc(1),o.Pb()),2&e){const e=o.bc().$implicit,t=o.bc().$implicit;o.xb(1),o.Lc(" ",t[e.field],"")}}function T(e,t){if(1&e&&(o.Qb(0,"td"),o.Hc(1,J,2,1,"span",73),o.Hc(2,y,2,1,"span",73),o.Pb()),2&e){const e=t.$implicit;o.Fc("width: ",e.width,""),o.xb(1),o.ic("ngIf",null!=e.isCurrency),o.xb(1),o.ic("ngIf",null==e.isCurrency)}}function I(e,t){if(1&e){const e=o.Sb();o.Qb(0,"tr"),o.Qb(1,"td",69),o.Qb(2,"button",71),o.Yb("click",function(){o.yc(e);const a=t.$implicit;return o.bc().editD(a)}),o.Pb(),o.Qb(3,"button",72),o.Yb("click",function(){o.yc(e);const a=t.$implicit;return o.bc().hapusD(a)}),o.Pb(),o.Pb(),o.Hc(4,T,3,5,"td",70),o.Pb()}if(2&e){const e=t.columns;o.xb(4),o.ic("ngForOf",e)}}function F(e,t){if(1&e&&(o.Qb(0,"tr"),o.Qb(1,"td",74),o.Jc(2,"Grand Total : "),o.Pb(),o.Qb(3,"td"),o.Jc(4),o.Pb(),o.Pb()),2&e){const e=o.bc();o.xb(4),o.Lc(" \xa0",e.item.totalSubTotal,"")}}const Y=function(){return[5,10,25,50]},O=[{path:"",component:(()=>{class e{constructor(e,t,a,i,n,s,r,o,l){this.apiService=e,this.authService=t,this.confirmationService=a,this.messageService=i,this.cacheHelper=n,this.dateHelper=s,this.alertService=r,this.route=o,this.router=l,this.params={},this.item={pasien:{},tglresep:new Date,aturanCheck:[],rke:1},this.skeleton=[],this.maxDateValue=new Date,this.headData={},this.dataProdukDetail=[],this.listSatuan=[],this.statusTambah=!0,this.listDataSigna=[{id:1,nama:"P",isChecked:!1},{id:2,nama:"S",isChecked:!1},{id:3,nama:"Sr",isChecked:!1},{id:4,nama:"M",isChecked:!1}],this.hrg1=0,this.columnGrid=[],this.dataSource=[],this.data2=[],this.norecOrder="",this.noOrder="",this.strStatus=0,this.isPemakaianObatAlkes=!1,this.tambah=function(){if(0!=this.statusTambah)if(1!=this.headData.isclosing)if(null!=this.item.ruangan)if(null!=this.item.penulisResep)if(null!=this.item.tglresep)if(0!=this.item.jumlah)if(null!=this.item.jenisKemasan)if(null!=this.item.produk)if(null!=this.item.satuan)if(null!=this.item.aturanpakaitxt){var e=0;1==this.item.SelectedObatKronis&&(e=1);var t="";this.item.KeteranganPakai&&(t=this.item.KeteranganPakai);var a=1;"Racikan"==this.item.jenisKemasan.jeniskemasan&&(a=this.item.dosis);var i,n=null;null!=this.item.jenisRacikan&&(n=this.item.jenisRacikan.id),i=null==this.dataSource?1:this.data2.length+1;var s={};if(this.disabledRuangan=!0,null!=this.item.no)for(var r=this.data2.length-1;r>=0;r--)this.data2[r].no==this.item.no&&(s.no=this.item.no,s.noregistrasifk=this.headData.norec,s.generik=null,s.hargajual=this.item.hargaSatuan,s.jenisobatfk=n,s.stock=this.item.stok,s.harganetto=this.item.hargaSatuan,s.nostrukterimafk=this.noTerima,s.ruanganfk=this.item.ruangan.id,s.rke=this.item.rke,s.jeniskemasanfk=this.item.jenisKemasan.id,s.jeniskemasan=this.item.jenisKemasan.jeniskemasan,s.aturanpakaifk=0,s.aturanpakai=this.item.aturanpakaitxt,s.ispagi=this.item.chkp,s.issiang=this.item.chks,s.issore=this.item.chksr,s.ismalam=this.item.chkm,s.asalprodukfk=0,s.asalproduk="",s.produkfk=this.item.produk.id,s.namaproduk=this.item.produk.namaproduk,s.nilaikonversi=this.item.nilaiKonversi,s.satuanstandarfk=this.item.satuan.ssid,s.satuanstandar=this.item.satuan.satuanstandar,s.satuanviewfk=this.item.satuan.ssid,s.satuanview=this.item.satuan.satuanstandar,s.jmlstok=this.item.stok,s.jumlah=this.item.jumlah,s.hargasatuan=this.item.hargaSatuan,s.hargadiscount=null!=this.hargaDiskon?this.hargaDiskon:0,s.persendiscount=null!=this.item.hargaDiskon?this.item.hargaDiskon:0,s.total=this.item.hargaTotal,s.dosis=a,s.jumlahxmakan=this.item.jumlahxmakan,s.jmldosis=String(this.item.jumlah/a)+"/"+String(a),s.keterangan=t,s.satuanresepfk=null!=this.item.satuanresep?this.item.satuanresep.id:null,s.satuanresep=null!=this.item.satuanresep?this.item.satuanresep.satuanresep:null,s.tglkadaluarsa=null!=this.tglkadaluarsa?this.tglkadaluarsa:null,s.isoutofstok=!1,s.iskronis=e,s.belumlengkap=!1,this.data2[r]=s,this.dataSource=this.data2);else{s={no:i,generik:null,hargajual:this.item.hargaSatuan,jenisobatfk:n,stock:this.item.stok,harganetto:this.item.hargaSatuan,nostrukterimafk:this.noTerima,ruanganfk:this.item.ruangan.id,rke:this.item.rke,jeniskemasanfk:this.item.jenisKemasan.id,jeniskemasan:this.item.jenisKemasan.jeniskemasan,aturanpakaifk:0,aturanpakai:this.item.aturanpakaitxt,ispagi:this.item.chkp,issiang:this.item.chks,issore:this.item.chksr,ismalam:this.item.chkm,asalprodukfk:0,asalproduk:"",produkfk:this.item.produk.id,namaproduk:this.item.produk.namaproduk,nilaikonversi:this.item.nilaiKonversi,satuanstandarfk:this.item.satuan.ssid,satuanstandar:this.item.satuan.satuanstandar,satuanviewfk:this.item.satuan.ssid,satuanview:this.item.satuan.satuanstandar,jmlstok:this.item.stok,jumlah:this.item.jumlah,hargasatuan:this.item.hargaSatuan,hargadiscount:null!=this.hargaDiskon?this.hargaDiskon:0,persendiscount:null!=this.item.hargaDiskon?this.item.hargaDiskon:0,total:this.item.hargaTotal,dosis:a,jumlahxmakan:this.item.jumlahxmakan,jmldosis:String(this.item.jumlah/a)+"/"+String(a),keterangan:t,satuanresepfk:null!=this.item.satuanresep?this.item.satuanresep.id:null,satuanresep:null!=this.item.satuanresep?this.item.satuanresep.satuanresep:null,tglkadaluarsa:null!=this.tglkadaluarsa?this.tglkadaluarsa:null,isoutofstok:!1,iskronis:e,belumlengkap:!1},this.data2.push(s),this.dataSource=this.data2;var o=0;for(r=this.data2.length-1;r>=0;r--)o+=parseFloat(this.data2[r].total);this.item.totalSubTotal=this.formatRupiah(o,"Rp.")}"Racikan"!=this.item.jenisKemasan.jeniskemasan&&(this.item.rke=parseFloat(this.item.rke)+1),this.kosongkan(),this.clear()}else this.alertService.error("Info","Aturan Pakai Belum diisi!!");else this.alertService.error("Info","Pilih Satuan terlebih dahulu!!");else this.alertService.error("Info","Pilih Produk terlebih dahulu!!");else this.alertService.error("Info","Pilih Jenis Kemasan terlebih dahulu!!");else this.alertService.error("Info","Jumlah harus di isi!");else this.alertService.error("Info","Data Tanggal Resep Masih Kosong!");else this.alertService.error("Info","Data Penulis Resep Masih Kosong!");else this.alertService.error("Info","Data Ruangan Masih Kosong!");else this.alertService.error("Info","Data Sudah Diclosing!")}}ngOnInit(){this.isSimpan=!1,this.dateNow=new Date,this.dataLogin=this.authService.getDataLoginUser(),this.kelUser=this.dataLogin.kelompokUser.kelompokUser,this.item.SelectedResepPulang=!1,this.item.SelectedObatKronis=!1,this.hargaDiskon=0,this.loadColumn(),this.loadCombo(),this.firstLoad()}formatRupiah(e,t){return t+" "+parseFloat(e).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g,"$1,")}loadColumn(){this.columnGrid=[{field:"no",header:"No",width:"80px"},{field:"rke",header:"R/Ke",width:"100px"},{field:"jeniskemasan",header:"Kemasan",width:"120px"},{field:"jmldosis",header:"Jml/Dosis",width:"120px"},{field:"aturanpakai",header:"Aturan Pakai",width:"120px"},{field:"satuanresep",header:"Satuan Resep",width:"120px"},{field:"namaproduk",header:"Produk",width:"120px"},{field:"satuanstandar",header:"Satuan",width:"100px"},{field:"jumlah",header:"Jumlah",width:"150px"},{field:"hargasatuan",header:"Harga",width:"150px",isCurrency:!0},{field:"total",header:"Total",width:"150px",isCurrency:!0},{field:"keterangan",header:"Keterangan",width:"150px"}]}loadCombo(){this.apiService.get("farmasi/get-combo-resep-rev").subscribe(e=>{this.listJenisKemasan=e.jeniskemasan,this.listAsalProduk=e.asalproduk,this.listsatuanresep=e.satuanresep,this.listJenisRacikan=e.jenisracikan,this.listPenulisResep=e.dokter,this.listRuangan=null!=this.dataLogin.mapLoginUserToRuangan?this.dataLogin.mapLoginUserToRuangan:e.ruanganfarmasi,this.item.jenisKemasan=e.jeniskemasan[1],this.item.tglresep=new Date(e.tglserver)})}firstLoad(){this.route.params.subscribe(e=>{this.params.norec_pd=e.norec_rp,this.params.norec_apd=e.norec_dpr,this.norec_pd=e.norec_rp,this.norec_apd=e.norec_dpr});var e=this.cacheHelper.get("InputResepPasienCtrl");null!=e&&(this.item.RuangRawat=e[11],this.item.pasien.norm=e[0],this.item.pasien.noregistrasi=e[3],this.item.pasien.namapasien=e[1],this.item.pasien.jeniskelamin=e[2],this.item.pasien.umur=e[4],this.item.pasien.kelompokpasien=e[10],this.item.pasien.alamat=e[12],this.JenisData=e[16],this.NorecData=e[9],this.loadData(),this.loadReser())}loadReser(){this.apiService.get("farmasi/get-noreservasi?norec_pd="+this.norec_pd).subscribe(e=>{this.item.pasien.noreservasi=e.noreservasi})}clickRadio(e){"Racikan"==e.jeniskemasan?(this.showRacikanDose=!0,delete this.item.jumlahxmakan,delete this.item.dosis,delete this.item.jenisRacikan,delete this.item.sediaan,delete this.item.kekuatan):this.showRacikanDose=!1}loadData(){null==this.JenisData&&""==this.JenisData||("OrderResep"==this.JenisData?this.apiService.get("farmasi/get-detail-order?norecOrder="+this.NorecData).subscribe(e=>{var t=e.strukorder,a=e.orderpelayanan,i=0;this.noOrder=t.noorder,this.norecResep="",this.disabledRuangan=!0,this.item.ruangan=void 0,this.item.penulisResep=void 0,this.item.ruangan={id:t.id,namaruangan:t.namaruangan},this.item.penulisResep={id:t.pgid,namalengkap:t.namalengkap},this.item.SelectedResepPulang="1"==t.isreseppulang,this.data2=a;for(let n=0;n<this.data2.length;n++){if("1"==this.data2[n].obatkronis){this.item.SelectedObatKronis=!0;break}this.item.SelectedObatKronis=!1;break}for(let n=0;n<this.data2.length;n++){const e=this.data2[n];e.no=n+1,0!=e.nilaikonversi&&null!=e.nilaikonversi||(e.nilaikonversi=1),null==e.hargadiscount?(e.hargadiscount=0,e.persendiscount=0):(e.hargadiscount=parseFloat(e.hargadiscount),e.persendiscount=parseFloat(e.hargajual)/parseFloat(e.hargadiscount)),e.total=parseFloat(e.jumlah)*parseFloat(e.hargasatuan),i+=parseFloat(e.total)}this.item.totalSubTotal=this.formatRupiah(i,"Rp."),this.dataSource=this.data2}):"EditResep"==this.JenisData&&this.apiService.get("farmasi/get-detail-resep?norecResep="+this.NorecData).subscribe(e=>{var t=e.detailresep,a=e.pelayananPasien,i=0;this.item.penulisResep=[],this.disabledRuangan=!0,this.item.nomorResep=t.noresep,this.item.ruangan={id:t.id,namaruangan:t.namaruangan},this.item.penulisResep={id:t.pgid,namalengkap:t.namalengkap},this.item.tglresep=r(t.tglresep).format("YYYY-MM-DD HH:mm"),this.norecResep=t.norec,this.item.SelectedResepPulang="1"==t.isreseppulang,this.data2=a;for(let n=0;n<this.data2.length;n++){if("1"==this.data2[n].obatkronis){this.item.SelectedObatKronis=!0;break}this.item.SelectedObatKronis=!1;break}for(let n=0;n<this.data2.length;n++){const e=this.data2[n];e.no=n+1,0!=e.nilaikonversi&&null!=e.nilaikonversi||(e.nilaikonversi=1),null==e.hargadiscount?(e.hargadiscount=0,e.persendiscount=0):(e.hargadiscount=parseFloat(e.hargadiscount),e.persendiscount=parseFloat(e.hargajual)/parseFloat(e.hargadiscount)),e.total=parseFloat(e.jumlah)*parseFloat(e.hargasatuan),i+=parseFloat(e.total)}this.item.totalSubTotal=this.formatRupiah(i,"Rp."),this.dataSource=this.data2}))}ResepPulang(e){this.item.SelectedResepPulang=1==e.checked}ObatKronis(e){this.item.SelectedObatKronis=1==e.checked}filterProduk(e){this.apiService.get("farmasi/get-produk-resep?namaproduk="+e.query).subscribe(e=>{this.listProduk=e})}getSatuan(){null!=this.item.produk.id&&this.GETKONVERSI()}GETKONVERSI(){this.listSatuan=this.item.produk.konversisatuan,0==this.listSatuan.length&&(this.listSatuan=[{ssid:this.item.produk.ssid,satuanstandar:this.item.produk.satuanstandar}]),this.item.satuan={ssid:this.item.produk.ssid,satuanstandar:this.item.produk.satuanstandar},this.item.nilaiKonversi=1,null!=this.item.ruangan&&(this.statusTambah=!1,this.apiService.get("general/get-produkdetail-general?produkfk="+this.item.produk.id+"&ruanganfk="+this.item.ruangan.id).subscribe(e=>{this.dataProdukDetail=e.detail,this.item.stok=e.jmlstok/this.item.nilaiKonversi,this.onChangeKonversi(),0==e.kekuatan&&(e.kekuatan=1),this.item.kekuatan=e.kekuatan,this.item.sediaan=e.sediaan,this.tglkadaluarsa=r(this.dataProdukDetail[0].tglkadaluarsa).format("YYYY-MM-DD HH:mm"),this.dataProdukDetail.length>0&&(null!=this.dataSelected?(this.item.nilaiKonversi=this.dataSelected.nilaikonversi,this.item.stok=this.dataSelected.jmlstok,this.item.jumlah=this.dataSelected.jumlah,this.item.hargaSatuan=this.dataSelected.hargasatuan,0!=this.dataSelected.persendiscount?(this.hargaDiskon=parseFloat(this.item.hargaSatuan)*parseFloat(this.dataSelected.persendiscount)/100,this.item.hargaDiskon=parseFloat(this.dataSelected.persendiscount)):(this.hargaDiskon=0,this.item.hargaDiskon=0),this.item.hargaTotal=this.dataSelected.total,null!=this.item.kekuatan&&0!=this.item.kekuatan&&(this.item.jumlahxmakan=parseFloat(this.item.jumlah)/parseFloat(this.item.dosis)*parseFloat(this.item.kekuatan))):(this.hargaDiskon=0,this.item.hargaDiskon=0)),this.statusTambah=!0,this.gettotal()}))}onChangeQty(e){this.item.jumlah=e.value,this.gettotal()}onChangeJmlXMakan(e){null!=this.item.kekuatan&&0!=this.item.kekuatan||(this.item.kekuatan=1),this.item.jumlah=parseFloat(this.item.jumlahxmakan)*parseFloat(this.item.dosis)/parseFloat(this.item.kekuatan)}onChangeKonversi(){this.item.stok>0&&(this.item.stok=parseFloat(this.item.stok)*parseFloat(this.item.nilaiKonversi),this.item.jumlah=1,this.item.hargaSatuan=0,this.item.hargaDiskon=0,this.item.hargaTotal=0)}onChangeDiskon(e){this.item.hargaDiskon=parseFloat(e.value),this.hargaDiskon=0;var t=this.item.hargaSatuan-this.item.hargaSatuan*this.item.hargaDiskon/100;this.hargaDiskon=t,this.item.hargaTotal=this.item.jumlah*t}getNilaiKonversi(){this.item.nilaiKonversi=this.item.satuan.nilaikonversi}gettotal(){for(var e=!1,t=0;t<this.dataProdukDetail.length;t++)if(e=!1,this.item.jumlah*parseFloat(this.item.nilaiKonversi)>0){this.hrg1=Math.round(parseFloat(this.dataProdukDetail[t].hargajual)*parseFloat(this.item.nilaiKonversi)),this.item.hargaSatuan=parseFloat(this.hrg1),this.item.hargaTotal=this.item.jumlah*this.hrg1,this.noTerima=this.dataProdukDetail[t].norec,e=!0;break}0==e&&(this.item.hargaSatuan=0,this.item.total=0,this.noTerima=""),0==this.item.jumlah&&(this.item.hargaSatuan=0)}kosongkan(){this.dataSelected=void 0,delete this.item.produk,delete this.item.satuan,delete this.item.nilaiKonversi,delete this.item.sediaan,delete this.item.kekuatan,this.item.stok=0,this.item.jumlah=1,this.item.no=void 0,this.item.hargaSatuan=0,delete this.item.satuanresep,delete this.item.KeteranganPakai,this.item.hargaDiskon=0,this.item.hargaTotal=0,this.hargaDiskon=0,this.tglkadaluarsa=void 0}clear(){"Racikan"!=this.item.jenisKemasan.jeniskemasan&&delete this.item.jenisRacikan}getSelected(){let e=0;if(this.item.aturanCheck.length>0)for(var t=Object.keys(this.item.aturanCheck),a=0;a<t.length;a++){const e=t[a];1==this.item.aturanCheck[parseInt(e)]?("1"==e&&(this.item.chkp=1),"2"==e&&(this.item.chks=1),"3"==e&&(this.item.chksr=1),"4"==e&&(this.item.chkm=1)):("1"==e&&(this.item.chkp=0),"2"==e&&(this.item.chks=0),"3"==e&&(this.item.chksr=0),"4"==e&&(this.item.chkm=0))}1==this.item.chkp&&(e+=1),1==this.item.chks&&(e+=1),1==this.item.chksr&&(e+=1),1==this.item.chkm&&(e+=1),this.item.aturanpakaitxt=e+"x1",0==e&&(this.item.aturanpakaitxt="")}batal(){this.kosongkan(),this.clear()}batalGrid(){this.kosongkan(),this.clear(),"OrderResep"!=this.JenisData&&"EditResep"!=this.JenisData?(this.data2=[],this.dataSource=void 0):this.alertService.warn("Info","Data Tidak Bisa Direset")}hapusD(e){for(var t=this.data2.length-1;t>=0;t--)if(this.data2[t].no==e.no){this.data2.splice(t,1);var a=0;for(t=this.data2.length-1;t>=0;t--)a+=parseFloat(this.data2[t].total),this.data2[t].no=t+1;this.dataSource=this.data2,this.item.totalSubTotal=a}"Racikan"!=this.item.jenisKemasan.jeniskemasan&&(this.item.rke=parseFloat(this.item.rke)-1),this.kosongkan()}editD(e){if(0!=this.statusTambah){var t=[];this.dataSelected=e,this.item.no=e.no,this.item.rke=e.rke,null!=e.jenisobatfk&&this.apiService.get("farmasi/get-jenis-obat?jrid="+e.jenisobatfk).subscribe(e=>{0!=e.data.length&&(this.item.jenisRacikan={id:e.data[0].id,jenisracikan:e.data[0].jenisracikan})}),null!=this.item.jenisKemasan&&this.item.jenisKemasan.id==e.jeniskemasanfk||(this.item.jenisKemasan={id:e.jeniskemasanfk,jeniskemasan:e.jeniskemasan}),this.item.satuanresep={id:e.satuanresepfk,satuanresep:e.satuanresep},this.item.jumlahxmakan=e.jumlahxmakan,this.item.dosis=e.dosis,this.item.aturanPakai={id:e.aturanpakaifk,name:e.aturanpakai},this.item.aturanpakaitxt=e.aturanpakai,this.item.KeteranganPakai=e.keterangan,this.item.aturanCheck=[],null!=e.ispagi&&"0"!=e.ispagi&&(this.item.aturanCheck[1]=!0),null!=e.issiang&&"0"!=e.issiang&&(this.item.aturanCheck[2]=!0),null!=e.issore&&"0"!=e.issore&&(this.item.aturanCheck[3]=!0),null!=e.ismalam&&"0"!=e.ismalam&&(this.item.aturanCheck[4]=!0),this.getSelected(),this.item.asal={id:e.asalprodukfk,asalproduk:e.asalproduk},this.apiService.get("emr/get-produk-resep?idproduk="+e.produkfk).subscribe(a=>{this.listProduk=a;for(var i=this.listProduk.length-1;i>=0;i--)if(this.listProduk[i].id==e.produkfk){t=this.listProduk[i];break}this.item.produk=t,this.GETKONVERSI()})}}Kembali(){window.history.back()}save(){this.isSimpan=!0;var e=0;1==this.item.SelectedResepPulang&&(e=1);var t=0;if(1==this.item.SelectedObatKronis&&(t=1),null!=this.item.tglresep)if(null!=this.item.ruangan)if(null!=this.item.penulisResep)if(0!=this.data2.length){for(var a=this.data2.length-1;a>=0;a--)if(parseFloat(this.data2[a].jmlstok)<parseFloat(this.data2[a].jumlah))return void this.alertService.error("Info","Terdapat obat dengan jumlah melebihi STOK!"+this.data2[a].namaproduk);var i={tglresep:r(this.item.tglresep).format("YYYY-MM-DD HH:mm:ss"),pasienfk:this.norec_apd,nocm:this.item.pasien.norm,namapasien:this.item.pasien.namapasien,penulisresepfk:null!=this.item.penulisResep?this.item.penulisResep.id:null,ruanganfk:this.item.ruangan.id,noorder:this.noOrder,status:this.strStatus,norecResep:this.norecResep,noresep:null!=this.item.nomorResep?this.item.nomorResep:"",retur:"-",isobatalkes:this.isPemakaianObatAlkes,isreseppulang:null!=e?e:null,isobatkronis:null!=t?t:null,jenisdata:this.JenisData};this.apiService.post("farmasi/save-pelayananobat",{strukresep:i,pelayananpasien:this.data2}).subscribe(e=>{this.isSimpan=!0;let t=this.item.pasien.noregistrasi;null!=this.item.pasien.noreservasi&&""!=this.item.pasien.noreservasi&&"Kios-K"!=this.item.pasien.noreservasi&&(t=this.item.pasien.noreservasi),this.saveAntrol(t,5),this.norecResep=e.resep.norec,this.apiService.postLog("Simpan Pelayanan Resep Pasien","Norec strukresep_t",this.norecResep,"Simpan Pelayanan Resep Pasien Dengan Noresep - "+e.resep.noresep+" dan No Registrasi - "+this.item.pasien.noregistrasi).subscribe(e=>{}),"OrderResep"==this.JenisData&&this.apiService.postLog("Simpan Verifikasi Order Resep","Norec transaksiorder",this.NorecData,"Simpan Verifikasi Order Resep Dengan Nooder - "+this.noOrder+" Noresep - "+e.resep.noresep+" dan No Registrasi - "+this.item.pasien.noregistrasi).subscribe(e=>{}),this.kosongkan(),this.clear(),this.data2=[],this.dataSource=void 0,window.history.back()})}else this.alertService.error("Info","Pilih Produk terlebih dahulu!");else this.alertService.error("Info","Penulis Resep Masih Kosong!");else this.alertService.error("Info","Ruangan Masih Kosong!");else this.alertService.error("Info","Tgl Resep Masih Kosong!")}saveAntrol(e,t){var a={url:"antrean/updatewaktu",jenis:"antrean",method:"POST",data:{kodebooking:e,taskid:t,waktu:(new Date).getTime()}};this.apiService.postNonMessage("bridging/bpjs/tools",a).subscribe(e=>{})}}return e.\u0275fac=function(t){return new(t||e)(o.Jb(l.a),o.Jb(l.b),o.Jb(s.a),o.Jb(s.h),o.Jb(h.a),o.Jb(d.a),o.Jb(u.a),o.Jb(n.a),o.Jb(n.f))},e.\u0275cmp=o.Db({type:e,selectors:[["app-input-resep-apotik"]],features:[o.wb([s.a])],decls:174,vars:72,consts:[[1,"p-fluid","p-formgrid"],[1,"card","card-w-title"],[1,"p-grid"],[1,"p-col-12","p-md-3"],[1,"p-col-12","p-md-12"],["header","Data Resep",3,"toggleable"],["for","input",1,"label"],["type","text","pInputText","","placeholder","Ruang Rawat","disabled","",3,"ngModel","ngModelChange"],["yearRange","1980:2030","dateFormat","yy-mm-dd","placeholder","Tgl Resep","hourFormat","24",3,"ngModel","showIcon","monthNavigator","yearNavigator","showTime","maxDate","disabled","ngModelChange"],["placeholder","Ruangan ","optionLabel","namaruangan","dataKey","id",3,"options","ngModel","filter","showClear","disabled","ngModelChange"],["type","text","pInputText","","placeholder","Nomor Resep","disabled","",3,"ngModel","ngModelChange"],["placeholder","Penulis","optionLabel","namalengkap",3,"options","ngModel","filter","showClear","ngModelChange"],["header","Info Pasien",3,"toggleable"],["type","text","pInputText","","placeholder","Nomor RM","disabled","",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Noregistrasi","disabled","",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Nama Pasien","disabled","",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Jenis Kelamin","disabled","",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Umur","disabled","",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Tipe Pasien","disabled","",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Alamat","disabled","",3,"ngModel","ngModelChange"],["header","Pengkajian",3,"toggleable"],["type","text","pInputText","","placeholder","Berat Badan","disabled","",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-12",2,"margin-top","20px"],["type","text","pInputText","","placeholder","Alergi","disabled","",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-9"],["header","Detail Resep",3,"toggleable"],["label","Resep Pulang","binary","true","inputId","item.SelectedResepPulang",3,"ngModel","ngModelChange","onChange"],["label","Obat Kronis","binary","true","inputId","item.SelectedObatKronis",3,"ngModel","ngModelChange","onChange"],[1,"p-col-12","p-md-1"],["type","text","pInputText","","placeholder","R/Ke ",3,"ngModel","ngModelChange"],["class","p-col-12 p-md-6 p-field-checkbox",4,"ngFor","ngForOf"],["class","p-col-12 p-md-2",4,"ngIf"],["type","text","pInputText","","placeholder","Kekuatan",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Stok",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-4"],["field","namaproduk","placeholder","Pilih Produk",3,"ngModel","suggestions","dropdown","ngModelChange","completeMethod","onSelect"],["placeholder","Qty","inputId","stacked",3,"ngModel","showButtons","ngModelChange","onInput"],[1,"p-col-12","p-md-2"],["placeholder","Satuan ","optionLabel","satuanstandar","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange","onChange"],["type","text","pInputText","","placeholder","Harga","disabled","",3,"ngModel","ngModelChange"],["placeholder","Disc %","inputId","stacked",3,"ngModel","showButtons","ngModelChange","onInput"],["type","text","pInputText","","placeholder","Total","disabled","",3,"ngModel","ngModelChange"],["type","text","pInputText","","placeholder","Aturan Pakai",3,"ngModel","ngModelChange"],["class","p-col-12 p-md-3 p-field-checkbox",4,"ngFor","ngForOf"],["placeholder","Satuan Resep  ","optionLabel","satuanresep","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange"],["type","text","pInputText","","placeholder","Keterangan",3,"ngModel","ngModelChange"],[1,"p-col-12","p-md-2","p-md-offset-8"],["pButton","","type","button","icon","pi pi-plus","pTooltip","Tambah","label","Tambah",1,"p-button","p-button-success","p-mr-2",3,"disabled","click"],["pButton","","type","button","icon","pi pi-refresh","pTooltip","Batal","label","Batal",1,"p-button","p-button-warning","p-mr-2",3,"click"],["header","Data Table",3,"toggleable"],["styleClass","p-datatable-customers","scrollable","true","dataKey","no",3,"value","columns","rows","showCurrentPageReport","rowsPerPageOptions","paginator","pageLinks"],["pTemplate","header"],["pTemplate","body"],["pTemplate","summary"],[1,"p-col-12","p-md-6"],[1,"p-col-12","p-md-3","p-md-offset-3"],["pButton","","type","button","icon","pi pi-save","pTooltip","Simpan Data","label","Simpan",1,"p-button","p-mr-2",3,"disabled","click"],["pButton","","type","button","icon","pi pi-refresh","pTooltip","Batal","label","Reset",1,"p-button","p-button-danger","p-mr-2",3,"click"],["pButton","","type","button","icon","fa fa-arrow-left","pTooltip","Kembali","label","Kembali",1,"p-button","p-button-danger","p-mr-2",3,"click"],[1,"p-col-12","p-md-6","p-field-checkbox"],["name","group2",3,"inputId","value","ngModel","ngModelChange","onClick"],[3,"for"],["placeholder","Jumlah","inputId","stacked",3,"ngModel","showButtons","ngModelChange","onInput"],["type","text","pInputText","","placeholder","Dosis",3,"ngModel","ngModelChange","input"],["placeholder","Jenis Racikan","optionLabel","jenisracikan","dataKey","id",3,"options","ngModel","filter","showClear","ngModelChange"],[1,"p-col-12","p-md-3","p-field-checkbox"],[1,"p-col-12"],["binary","true",3,"ngModel","inputId","ngModelChange","onChange"],[1,"p-col-12",2,"margin-top","-12px"],[2,"width","80px"],[3,"style",4,"ngFor","ngForOf"],["pButton","","type","button","icon","pi pi-user-edit","pTooltip","Edit Data","label","",1,"p-button-rounded","p-mr-2",3,"click"],["pButton","","type","button","icon","pi pi-trash","pTooltip","Hapus Data","label","",1,"p-button-rounded","p-button-danger","p-mr-2",3,"click"],[4,"ngIf"],["rowspan","6"]],template:function(e,t){1&e&&(o.Qb(0,"div",0),o.Qb(1,"div",1),o.Qb(2,"h4"),o.Jc(3,"Input Resep Farmasi"),o.Pb(),o.Qb(4,"div",2),o.Qb(5,"div",3),o.Qb(6,"div",2),o.Qb(7,"div",4),o.Qb(8,"p-panel",5),o.Qb(9,"div",4),o.Qb(10,"div",2),o.Qb(11,"div",4),o.Qb(12,"label",6),o.Jc(13,"Ruang Rawat"),o.Pb(),o.Qb(14,"input",7),o.Yb("ngModelChange",function(e){return t.item.RuangRawat=e}),o.Pb(),o.Pb(),o.Qb(15,"div",4),o.Qb(16,"label",6),o.Jc(17,"Tgl Resep"),o.Pb(),o.Qb(18,"p-calendar",8),o.Yb("ngModelChange",function(e){return t.item.tglresep=e}),o.Pb(),o.Pb(),o.Qb(19,"div",4),o.Qb(20,"label",6),o.Jc(21,"Ruangan "),o.Pb(),o.Qb(22,"p-dropdown",9),o.Yb("ngModelChange",function(e){return t.item.ruangan=e}),o.Pb(),o.Pb(),o.Qb(23,"div",4),o.Qb(24,"label",6),o.Jc(25,"Nomor Resep"),o.Pb(),o.Qb(26,"input",10),o.Yb("ngModelChange",function(e){return t.item.nomorResep=e}),o.Pb(),o.Pb(),o.Qb(27,"div",4),o.Qb(28,"label",6),o.Jc(29,"Penulis Resep "),o.Pb(),o.Qb(30,"p-dropdown",11),o.Yb("ngModelChange",function(e){return t.item.penulisResep=e}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Qb(31,"div",4),o.Qb(32,"p-panel",12),o.Qb(33,"div",4),o.Qb(34,"div",2),o.Qb(35,"div",4),o.Qb(36,"label",6),o.Jc(37,"Nomor RM"),o.Pb(),o.Qb(38,"input",13),o.Yb("ngModelChange",function(e){return t.item.pasien.norm=e}),o.Pb(),o.Pb(),o.Qb(39,"div",4),o.Qb(40,"label",6),o.Jc(41,"Noregistrasi"),o.Pb(),o.Qb(42,"input",14),o.Yb("ngModelChange",function(e){return t.item.pasien.noregistrasi=e}),o.Pb(),o.Pb(),o.Qb(43,"div",4),o.Qb(44,"label",6),o.Jc(45,"Nama Pasien"),o.Pb(),o.Qb(46,"input",15),o.Yb("ngModelChange",function(e){return t.item.pasien.namapasien=e}),o.Pb(),o.Pb(),o.Qb(47,"div",4),o.Qb(48,"label",6),o.Jc(49,"Jenis Kelamin"),o.Pb(),o.Qb(50,"input",16),o.Yb("ngModelChange",function(e){return t.item.pasien.jeniskelamin=e}),o.Pb(),o.Pb(),o.Qb(51,"div",4),o.Qb(52,"label",6),o.Jc(53,"Umur"),o.Pb(),o.Qb(54,"input",17),o.Yb("ngModelChange",function(e){return t.item.pasien.umur=e}),o.Pb(),o.Pb(),o.Qb(55,"div",4),o.Qb(56,"label",6),o.Jc(57,"Tipe Pasien"),o.Pb(),o.Qb(58,"input",18),o.Yb("ngModelChange",function(e){return t.item.pasien.kelompokpasien=e}),o.Pb(),o.Pb(),o.Qb(59,"div",4),o.Qb(60,"label",6),o.Jc(61,"Alamat"),o.Pb(),o.Qb(62,"input",19),o.Yb("ngModelChange",function(e){return t.item.pasien.alamat=e}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Qb(63,"div",4),o.Qb(64,"p-panel",20),o.Qb(65,"div",4),o.Qb(66,"div",2),o.Qb(67,"div",4),o.Qb(68,"label",6),o.Jc(69,"Berat Badan"),o.Pb(),o.Qb(70,"input",21),o.Yb("ngModelChange",function(e){return t.item.pasien.beratbadan=e}),o.Pb(),o.Pb(),o.Qb(71,"div",22),o.Qb(72,"label",6),o.Jc(73,"KG"),o.Pb(),o.Pb(),o.Qb(74,"div",4),o.Qb(75,"label",6),o.Jc(76,"Alergi"),o.Pb(),o.Qb(77,"input",23),o.Yb("ngModelChange",function(e){return t.item.pasien.alergi=e}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Qb(78,"div",24),o.Qb(79,"div",2),o.Qb(80,"div",4),o.Qb(81,"div",2),o.Qb(82,"div",4),o.Qb(83,"p-panel",25),o.Qb(84,"div",2),o.Qb(85,"div",3),o.Qb(86,"p-checkbox",26),o.Yb("ngModelChange",function(e){return t.item.SelectedResepPulang=e})("onChange",function(e){return t.ResepPulang(e)}),o.Pb(),o.Pb(),o.Qb(87,"div",3),o.Qb(88,"p-checkbox",27),o.Yb("ngModelChange",function(e){return t.item.SelectedObatKronis=e})("onChange",function(e){return t.ObatKronis(e)}),o.Pb(),o.Pb(),o.Pb(),o.Qb(89,"div",2),o.Qb(90,"div",28),o.Qb(91,"label",6),o.Jc(92,"R/Ke "),o.Pb(),o.Qb(93,"input",29),o.Yb("ngModelChange",function(e){return t.item.rke=e}),o.Pb(),o.Pb(),o.Qb(94,"div",3),o.Qb(95,"label",6),o.Jc(96,"Jenis Kemasan "),o.Pb(),o.Qb(97,"div",2),o.Hc(98,x,4,5,"div",30),o.Pb(),o.Pb(),o.Hc(99,R,4,2,"div",31),o.Hc(100,C,4,1,"div",31),o.Hc(101,j,4,4,"div",31),o.Qb(102,"div",28),o.Qb(103,"label",6),o.Jc(104),o.Pb(),o.Qb(105,"input",32),o.Yb("ngModelChange",function(e){return t.item.kekuatan=e}),o.Pb(),o.Pb(),o.Qb(106,"div",28),o.Qb(107,"label",6),o.Jc(108,"Stok"),o.Pb(),o.Qb(109,"input",33),o.Yb("ngModelChange",function(e){return t.item.stok=e}),o.Pb(),o.Pb(),o.Pb(),o.Qb(110,"div",2),o.Qb(111,"div",34),o.Qb(112,"label",6),o.Jc(113,"Produk "),o.Pb(),o.Qb(114,"p-autoComplete",35),o.Yb("ngModelChange",function(e){return t.item.produk=e})("completeMethod",function(e){return t.filterProduk(e)})("onSelect",function(){return t.getSatuan()}),o.Pb(),o.Pb(),o.Qb(115,"div",28),o.Qb(116,"label",6),o.Jc(117,"Qty Obat "),o.Pb(),o.Qb(118,"p-inputNumber",36),o.Yb("ngModelChange",function(e){return t.item.jumlah=e})("onInput",function(e){return t.onChangeQty(e)}),o.Pb(),o.Pb(),o.Qb(119,"div",37),o.Qb(120,"label",6),o.Jc(121,"Satuan "),o.Pb(),o.Qb(122,"p-dropdown",38),o.Yb("ngModelChange",function(e){return t.item.satuan=e})("onChange",function(){return t.getNilaiKonversi()}),o.Pb(),o.Pb(),o.Qb(123,"div",37),o.Qb(124,"label",6),o.Jc(125,"Harga "),o.Pb(),o.Qb(126,"input",39),o.Yb("ngModelChange",function(e){return t.item.hargaSatuan=e}),o.Pb(),o.Pb(),o.Qb(127,"div",28),o.Qb(128,"label",6),o.Jc(129,"Diskon (%)"),o.Pb(),o.Qb(130,"p-inputNumber",40),o.Yb("ngModelChange",function(e){return t.item.hargaDiskon=e})("onInput",function(e){return t.onChangeDiskon(e)}),o.Pb(),o.Pb(),o.Qb(131,"div",37),o.Qb(132,"label",6),o.Jc(133,"Total "),o.Pb(),o.Qb(134,"input",41),o.Yb("ngModelChange",function(e){return t.item.hargaTotal=e}),o.Pb(),o.Pb(),o.Qb(135,"div",37),o.Qb(136,"label",6),o.Jc(137,"Aturan Pakai "),o.Pb(),o.Qb(138,"input",42),o.Yb("ngModelChange",function(e){return t.item.aturanpakaitxt=e}),o.Pb(),o.Pb(),o.Qb(139,"div",37),o.Qb(140,"label",6),o.Jc(141,"\xa0 "),o.Pb(),o.Qb(142,"div",2),o.Hc(143,D,7,4,"div",43),o.Pb(),o.Pb(),o.Qb(144,"div",3),o.Qb(145,"label",6),o.Jc(146,"Satuan Resep "),o.Pb(),o.Qb(147,"p-dropdown",44),o.Yb("ngModelChange",function(e){return t.item.satuanresep=e}),o.Pb(),o.Pb(),o.Qb(148,"div",3),o.Qb(149,"label",6),o.Jc(150,"Keterangan "),o.Pb(),o.Qb(151,"input",45),o.Yb("ngModelChange",function(e){return t.item.KeteranganPakai=e}),o.Pb(),o.Pb(),o.Pb(),o.Qb(152,"div",2),o.Qb(153,"div",46),o.Qb(154,"button",47),o.Yb("click",function(){return t.tambah()}),o.Pb(),o.Pb(),o.Qb(155,"div",37),o.Qb(156,"button",48),o.Yb("click",function(){return t.batal()}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Qb(157,"div",4),o.Qb(158,"p-panel",49),o.Qb(159,"p-table",50),o.Hc(160,K,3,1,"ng-template",51),o.Hc(161,I,5,1,"ng-template",52),o.Hc(162,F,5,1,"ng-template",53),o.Pb(),o.Pb(),o.Pb(),o.Qb(163,"div",4),o.Qb(164,"div",2),o.Lb(165,"div",54),o.Qb(166,"div",54),o.Qb(167,"div",2),o.Qb(168,"div",55),o.Qb(169,"button",56),o.Yb("click",function(){return t.save()}),o.Pb(),o.Pb(),o.Qb(170,"div",3),o.Qb(171,"button",57),o.Yb("click",function(){return t.batalGrid()}),o.Pb(),o.Pb(),o.Qb(172,"div",3),o.Qb(173,"button",58),o.Yb("click",function(){return t.Kembali()}),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb(),o.Pb()),2&e&&(o.xb(8),o.ic("toggleable",!0),o.xb(6),o.ic("ngModel",t.item.RuangRawat),o.xb(4),o.ic("ngModel",t.item.tglresep)("showIcon",!0)("monthNavigator",!0)("yearNavigator",!0)("showTime",!0)("maxDate",t.maxDateValue)("disabled",!0),o.xb(4),o.ic("options",t.listRuangan)("ngModel",t.item.ruangan)("filter",!0)("showClear",!0)("disabled",t.disabledRuangan),o.xb(4),o.ic("ngModel",t.item.nomorResep),o.xb(4),o.ic("options",t.listPenulisResep)("ngModel",t.item.penulisResep)("filter",!0)("showClear",!0),o.xb(2),o.ic("toggleable",!0),o.xb(6),o.ic("ngModel",t.item.pasien.norm),o.xb(4),o.ic("ngModel",t.item.pasien.noregistrasi),o.xb(4),o.ic("ngModel",t.item.pasien.namapasien),o.xb(4),o.ic("ngModel",t.item.pasien.jeniskelamin),o.xb(4),o.ic("ngModel",t.item.pasien.umur),o.xb(4),o.ic("ngModel",t.item.pasien.kelompokpasien),o.xb(4),o.ic("ngModel",t.item.pasien.alamat),o.xb(2),o.ic("toggleable",!0),o.xb(6),o.ic("ngModel",t.item.pasien.beratbadan),o.xb(7),o.ic("ngModel",t.item.pasien.alergi),o.xb(6),o.ic("toggleable",!0),o.xb(3),o.ic("ngModel",t.item.SelectedResepPulang),o.xb(2),o.ic("ngModel",t.item.SelectedObatKronis),o.xb(5),o.ic("ngModel",t.item.rke),o.xb(5),o.ic("ngForOf",t.listJenisKemasan),o.xb(1),o.ic("ngIf",t.showRacikanDose),o.xb(1),o.ic("ngIf",t.showRacikanDose),o.xb(1),o.ic("ngIf",t.showRacikanDose),o.xb(3),o.Lc("Kekuatan ",t.item.sediaan,""),o.xb(1),o.ic("ngModel",t.item.kekuatan),o.xb(4),o.ic("ngModel",t.item.stok),o.xb(5),o.ic("ngModel",t.item.produk)("suggestions",t.listProduk)("dropdown",!0),o.xb(4),o.ic("ngModel",t.item.jumlah)("showButtons",!0),o.xb(4),o.ic("options",t.listSatuan)("ngModel",t.item.satuan)("filter",!0)("showClear",!0),o.xb(4),o.ic("ngModel",t.item.hargaSatuan),o.xb(4),o.ic("ngModel",t.item.hargaDiskon)("showButtons",!0),o.xb(4),o.ic("ngModel",t.item.hargaTotal),o.xb(4),o.ic("ngModel",t.item.aturanpakaitxt),o.xb(5),o.ic("ngForOf",t.listDataSigna),o.xb(4),o.ic("options",t.listsatuanresep)("ngModel",t.item.satuanresep)("filter",!0)("showClear",!0),o.xb(4),o.ic("ngModel",t.item.KeteranganPakai),o.xb(3),o.ic("disabled",!t.statusTambah),o.xb(4),o.ic("toggleable",!0),o.xb(1),o.ic("value",t.dataSource)("columns",t.columnGrid)("rows",20)("showCurrentPageReport",!0)("rowsPerPageOptions",o.mc(71,Y))("paginator",!0)("pageLinks",5),o.xb(10),o.ic("disabled",t.isSimpan))},directives:[p.a,b.a,m.a,b.g,b.h,c.a,g.a,k.a,i.k,i.l,f.a,P.a,v.b,Q.a,M.h,s.k,S.a],styles:[""]}),e})()}];let N=(()=>{class e{}return e.\u0275mod=o.Hb({type:e}),e.\u0275inj=o.Gb({factory:function(t){return new(t||e)},imports:[[n.j.forChild(O)],n.j]}),e})();var L=a("PCNd");let H=(()=>{class e{}return e.\u0275mod=o.Hb({type:e}),e.\u0275inj=o.Gb({factory:function(t){return new(t||e)},imports:[[i.b,N,L.a]]}),e})()}}]);