import { Component, OnInit } from '@angular/core';
import { ConfirmationService, LazyLoadEvent, MessageService } from 'primeng/api';
import { Config } from '../../../../guard';
import { ApiService, AuthService } from '../../../../service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-daftar-order-gizi',
  templateUrl: './daftar-order-gizi.component.html',
  styleUrls: ['./daftar-order-gizi.component.scss']
})
export class DaftarOrderGiziComponent implements OnInit {
  item: any = {
    tglMenu: new Date(),
    qtyProduk: 1
  }
  itemR: any = {}
  rowGroupMetadata: any;
  disableTgl: boolean
  listRuangan: any = []
  column: any[]
  dataSource: any[]
  selected: any
  pop_daftarOrder: boolean
  pop_DokterPJawab: boolean
  listDokter: any[]
  kelompokUser: any
  lengthKonsul: number = 0
  columnKonsul: any[]
  dataSourceKonsul: any[] = []
  selectedData: any[] = []
  listJenisWaktu: any[]
  listJenisDiet: any[]
  listKategoriDiet: any[]
  isSimpan: boolean
  indexTab: any
  columnRiwayat: any[]
  dataSourceRiwayat: any[]
  data2: any[] = []

  pop_Kirim: boolean
  columnMenu: any[] = [];
  dsMenu: any[] = []
  dataMenuSiklus: any = []
  listMenu: any = []
  isSave: boolean
  pop_Cetak: boolean
  isKirim: boolean
  pop_cetakLabelGizi:boolean
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.itemR.tglAwal = new Date(moment(new Date()).format('YYYY-MM-DD 00:00'))
    this.itemR.tglAkhir = new Date(moment(new Date()).format('YYYY-MM-DD 23:59'))
    this.kelompokUser = this.authService.getKelompokUser()

    this.apiService.get("rawatinap/get-combo").subscribe(table => {
      var dataCombo = table;
      this.listRuangan = dataCombo.ruanganinap;
      this.loadCombo()

    })
    this.apiService.get('rawatinap/get-combo-gizi').subscribe(e => {
      this.listJenisWaktu = e.jeniswaktu
      this.listJenisDiet = e.jenisdiet


    })
    this.column = [
      { field: 'no', header: 'No', width: "60px" },
      { field: 'nokirim', header: 'No Kirim', width: "120px" },
      { field: 'tglorder', header: 'Tgl Order', width: "150px" },
      { field: 'noorder', header: 'No Order', width: "150px" },
      { field: 'tglmenu', header: 'Tgl Menu', width: "150px" },
      { field: 'jeniswaktu', header: 'Jenis Waktu', width: "150px" },
      { field: 'jenisdiet', header: 'Jenis Diet', width: "200px", isArray: true },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "150px" },
      { field: 'noregistrasi', header: 'No Registrasi', width: "150px" },
      { field: 'norm', header: 'No RM', width: "90px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'umurzz', header: 'Umur', width: "100px" },
      { field: 'ruanganasal', header: 'Ruang Asal', width: "200px" },
      { field: 'namakelas', header: 'Kelas', width: "120px" },
      { field: 'keteranganlainnya', header: 'Ket', width: "200px" },

    ];
    this.columnRiwayat = [
      { field: 'tglorder', header: 'Tgl Order', width: "100px" },
      { field: 'tglpelayananawal', header: 'Tgl Men', width: "100px" },
      { field: 'noorder', header: 'No Order', width: "100px" },
      { field: 'pengorder', header: 'Pengorder', width: "150px" },
    ];
    this.columnMenu = [
      { field: 'no', header: 'No', width: "40px" },
      { field: 'namaproduk', header: 'Nama Menu', width: "200px" },
      { field: 'qtyproduk', header: 'Jumlah', width: "80px" },
    ];
  }
  loadCombo() {

    var chacePeriode = this.cacheHelper.get('cache_DaftarPsnRJ');
    if (chacePeriode != undefined) {
      this.item.periodeAwal = new Date(chacePeriode[0]);
      this.item.periodeAkhir = new Date(chacePeriode[1]);
      if (chacePeriode[4] != undefined && chacePeriode[4].length > 0) {
        this.item.ruanganMulti = chacePeriode[4]
      }
    } else {
      this.item.periodeAwal = moment(new Date()).format('YYYY-MM-DD 00:00');
      this.item.periodeAkhir = moment(new Date()).format('YYYY-MM-DD 23:59');
    }
    this.loadData()
  }

  filterProduk(event) {

    let query = event.query;
    this.apiService.get("rawatinap/get-produk-gizi?namaproduk=" + query)
      .subscribe(re => {
        this.listMenu = re;
      })
  }
  cekKirim(e) {
    if (e == true) {
      this.loadData()
    } else {
      this.loadData()
    }

  }
  cari() {
    this.loadData()
  }
  loadData() {
    var tglAwal = moment(this.item.periodeAwal).format('YYYY-MM-DD HH:mm:ss');
    var tglAkhir = moment(this.item.periodeAkhir).format('YYYY-MM-DD HH:mm:ss');

    var nocm = ""
    if (this.item.noCm != undefined) {
      var nocm = "&norm=" + this.item.noCm
    }
    var nama = ""
    if (this.item.namaPasien != undefined) {
      var nama = "&nama=" + this.item.namaPasien
    }
    var noOrder = ""
    if (this.item.noOrder != undefined) {
      noOrder = "&noOrder=" + this.item.noOrder
    }
    var noRegistrasi = ""
    if (this.item.noRegistrasi != undefined) {
      var noRegistrasi = "&noreg=" + this.item.noRegistrasi
    }
    var jenisWaktu = ""
    if (this.item.jenisWaktu != undefined) {
      jenisWaktu = "&jeniswaktuid=" + this.item.jenisWaktu.id
    }
    var status = ""
    if (this.item.isKirim != undefined && this.item.isKirim == true) {
      status = "&iskirim=true"
    }

    var listRuangan = ""

    if (this.item.ruanganMulti != undefined && this.item.ruanganMulti.length != 0) {
      var a = ""
      var b = ""
      for (var i = this.item.ruanganMulti.length - 1; i >= 0; i--) {

        var c = this.item.ruanganMulti[i].id
        b = "," + c
        a = a + b
      }
      listRuangan = a.slice(1, a.length)
    }
    var listJenisDiet = ""

    if (this.item.jenisDiet != undefined && this.item.jenisDiet.length != 0) {
      var a = ""
      var b = ""
      for (var i = this.item.jenisDiet.length - 1; i >= 0; i--) {

        var c = this.item.jenisDiet[i].id
        b = "," + c
        a = a + b
      }
      listJenisDiet = a.slice(1, a.length)
    }
    var jmlRow = ""
    if (this.item.rows != undefined) {
      jmlRow = "&jmlRow=" + this.item.rows
    }

    this.apiService.get("rawatinap/get-daftar-order-gizi?" +
      "&tglAwal=" + tglAwal +
      "&tglAkhir=" + tglAkhir +
      "&norm=" + nocm +
      "&noreg=" + noRegistrasi +
      "&nama=" + nama +
      "&ruanganArr=" + listRuangan +
      "&jenisdietarr=" + listJenisDiet +
      jmlRow
      + status
      + jenisWaktu).subscribe(da => {
        let datas = da.data
        for (var i = 0; i < datas.length; i++) {
          datas[i].no = i + 1
          var tanggal = new Date()
          var tanggalLahir = new Date(datas[i].tgllahir);
          datas[i].umurzz = this.dateHelper.getUmur(tanggalLahir, tanggal);

          datas[i].jenisdiet = []
          if (datas[i].arrjenisdiet) {
            var arr = datas[i].arrjenisdiet.split(',')
            if (arr.length > 0) {
              for (var x = 0; x < arr.length; x++) {
                const jenis = arr[x]
                for (var z = 0; z < this.listJenisDiet.length; z++) {
                  const diet = this.listJenisDiet[z]
                  if (jenis == diet.id) {
                    datas[i].jenisdiet.push(diet)
                  }
                }
              }
            }
          }
        }
        this.dataSource = datas

        var chacePeriode = {
          0: tglAwal,
          1: tglAkhir,
          2: this.item.ruangan != undefined ? this.item.ruangan.id : null,
          3: this.item.ruangan != undefined ? this.item.ruangan.namaruangan : null,
          4: this.item.ruanganMulti,
        }
        this.cacheHelper.set('cache_DaftarPsnRJ', chacePeriode);
        // this.updateRowGroupMetaData();
      });
  }
  kirimMenu() {

    if (this.selectedData.length == 0) {
      this.alertService.error('Info', 'Ceklis data dulu')
      return
    }
    if (this.dataMenuSiklus.length == 0) {
      this.alertService.info('Info', 'Siklus Menu tidak ada ')
    }
    this.clear()

    this.getSiklusMenus()
    this.pop_Kirim = true
    this.data2 = []

  }
  unChechkedGrid(e) {
    let dataItem = e.data
    for (var i = this.dataMenuSiklus.length - 1; i >= 0; i--)
      if (this.dataMenuSiklus[i].norec_op === dataItem.norec_op) {
        this.dataMenuSiklus.splice(i, 1);
        // break;
      }
    // row.removeClass("k-state-selected");
    console.log(JSON.stringify(this.dataMenuSiklus));

    for (var i = 0; i < this.selectedData.length; i++)
      if (this.selectedData[i].norec_op === dataItem.norec_op) {
        this.selectedData.splice(i, 1);
        break;
      }
  }
  checkedGrid(aa) {

    let dataItem = aa.data
    var tglMenu = moment(dataItem.tglmenu).format('YYYY-MM-DD');
    var arrTgl: any = tglMenu.split('-');
    // if(this.selectedData.length > 0){
    //   for (let x = 0; x < this.selectedData.length; x++) {
    //     const element = this.selectedData[x];
    //     if (element.norec_so == dataItem.norec_so) {
    //       return

    //     }
    //   }
    // }


    // var arrTgl = tglMenu.split('-');
    if (arrTgl[2] == 31) {
      arrTgl[2] = 11;
    } else if ((arrTgl[2] % 10) == 0) {
      arrTgl[2] = 10;
    } else {
      arrTgl[2] = arrTgl[2] % 10;
    }
    this.isKirim = true
    this.apiService.get("rawatinap/get-siklus-menudiet?jenisDietId="
      + dataItem.arrjenisdiet
      + "&kelasId=" + dataItem.kelasidfk
      + "&siklusKe=" + arrTgl[2]
      + "&jenisWaktuId=" + dataItem.jeniswaktuidfk
      + "&kategoryDiet=" + dataItem.kategorydietidfk
      + "&norec_op=" + dataItem.norec_op
      + "&norec_pd=" + dataItem.norec_pd
    ).subscribe(e => {
      this.isKirim = false
      var result = e.data
      for (let i = 0; i < result.length; i++) {
        const element = result[i];
        this.dataMenuSiklus.push(element)
      }
      // result.forEach(function (res) {
      //   this.dataMenuSiklus.push(res)

      // })
      console.log(JSON.stringify(this.dataMenuSiklus));

    })

    /*end Get */


    // var result = $.grep($scope.selectedOnCheck, function (e) {
    //   return e.norec_op == dataItem.norec_op;
    // });
    // if (result.length == 0) {
    //   $scope.selectedOnCheck.push(dataItem);
    // } else {
    //   for (var i = 0; i <this.selectedData.length; i++)
    //     if (this.selectedData[i].norec_op === dataItem.norec_op) {
    //       this.selectedData.splice(i, 1);
    //       break;
    //     }
    //   this.selectedData.push(dataItem);
    // }
  }
  hapusAll() {
    this.data2 = []
    this.dsMenu = this.data2
    this.clear();
  }
  editD(e) {
    this.item.no = e.no
    this.item.namaProduk = { id: e.produkfk, namaproduk: e.namaproduk }
    this.item.qtyProduk = parseFloat(e.qtyproduk)
  }
  hapusD(e) {
    for (var i = this.data2.length - 1; i >= 0; i--) {
      if (this.data2[i].no == e.no) {
        this.data2.splice(i, 1);
      }
    }
    this.dsMenu = this.data2
  }
  tambah() {
    if (this.item.qtyProduk == undefined || this.item.qtyProduk == 0) {
      this.alertService.error('Info', 'Jumlah Harus di isi')
      return;
    }

    if (this.item.namaProduk == undefined) {
      this.alertService.error('Info', 'Menu Harus di isi')
      return;
    }
    var nomor = 0
    nomor = this.data2.length + 1

    var data: any = {};
    if (this.item.no != undefined) {
      for (var i = this.data2.length - 1; i >= 0; i--) {
        if (this.data2[i].no == this.item.no) {
          data.no = this.item.no

          data.produkfk = this.item.namaProduk.id
          data.namaproduk = this.item.namaProduk.namaproduk
          data.qtyproduk = parseFloat(this.item.qtyProduk)
          this.data2[i] = data;
          this.dsMenu = this.data2
        }
      }

    } else {
      data = {
        no: nomor,
        produkfk: this.item.namaProduk.id,
        namaproduk: this.item.namaProduk.namaproduk,
        qtyproduk: parseFloat(this.item.qtyProduk),
      }
      this.data2.push(data)
      this.dsMenu = this.data2
    }
    this.clear();
  }
  clear() {
    delete this.item.namaProduk
    this.item.qtyProduk = 1
    delete this.item.no
  }
  getSiklusMenus() {

    var qtyProduk = 0
    var totalMenu = 0;
    var dataDetail: any = [];
    var dataDetail2 = [];
    var adaDetail = 0;
    if (this.dataMenuSiklus.length > 0) {

      for (var i = 0; i < this.dataMenuSiklus.length; i++) {
        totalMenu = totalMenu + 1;
        adaDetail = 0
        for (var x = 0; x < dataDetail2.length; x++) {
          if (dataDetail2[x].produkfk == this.dataMenuSiklus[i].produkfk) {
            dataDetail2[x].qtyproduk = parseFloat(dataDetail2[x].qtyproduk) + parseFloat(this.dataMenuSiklus[i].qtyproduk)
            adaDetail = 1;
          }
        };
        if (adaDetail == 0) {
          dataDetail = {
            "produkfk": this.dataMenuSiklus[i].produkfk,
            "namaproduk": this.dataMenuSiklus[i].namaproduk,
            "qtyproduk": this.dataMenuSiklus[i].qtyproduk,
            "norec_op": this.dataMenuSiklus[i].norec_op,

          }
          dataDetail2.push(dataDetail)
        }
      }

    }

    for (var i = 0; i < dataDetail2.length; i++) {
      dataDetail2[i].no = i + 1
    }
    this.data2 = dataDetail2;
    this.dsMenu = this.data2


  }
  saveMenu() {
    this.isSave = true
    var objSave = {
      "norec_sk": this.item.norecSK == undefined ? '' : this.item.norecSK,
      "objectruanganasalfk": this.selectedData[0].ruanganidfk,
      "keterangan": this.item.keterangan != undefined ? this.item.keterangan : '',
      "qtyproduk": this.data2.length,
      // "objectjeniswaktufk": this.item.jenisWaktu.id,
      "details": this.data2,
      "datapasien": this.selectedData,
    }
    var post = {
      "strukkirim": objSave
    }
    this.apiService.post('rawatinap/save-kirimmenu-gizi', post).subscribe(e => {
      this.selectedData = []
      this.dataMenuSiklus = []
      this.isSave = false
      this.pop_Kirim = false
      this.loadData()
    }, error => {
      this.isSave = false
    })

  }
  cetakLabel() {
    if (this.selectedData.length == 0) {
      this.alertService.error('Info', 'Ceklis data dulu')
      return
    }
    for (let i = 0; i < this.selectedData.length; i++) {
      const element = this.selectedData[i];
      if (element.transaksikirimfk == null) {
        this.alertService.error("Info", 'Cetak Hanya untuk order yang sudah kirim')
        return
      }
    }


    this.pop_Cetak = true
    this.item.jmlCetak = 1
  }
  cetakEuy() {

    if (this.item.jmlCetak = 0) {
      this.alertService.warn('Info', 'qty tidak boleh nol')
      return
    }

    if (this.selectedData.length != 0) {
      var DataGizi = ''
      for (var i = this.selectedData.length - 1; i >= 0; i--) {
        DataGizi = DataGizi + ',' + "'" + this.selectedData[i].norec_op + "'"
      }

      var norec = DataGizi; //$scope.selectedOnCheck[0].norec_op
      var stt = 'false'
      if (confirm('View Label Gizi? ')) {
        // Save it!
        stt = 'true';
      } else {
        // Do nothing!
        stt = 'false'
      }

      this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-label-gizi=1&norec=" +
        norec + "&qty=" + this.item.jmlCetak + "&view=" + stt, function (e) { });

      this.pop_Cetak = false
    }
  }
  hapus() {
    if (this.selectedData.length == 0) {
      this.alertService.error('Info', 'Ceklis data dulu')
      return
    }
    for (let i = 0; i < this.selectedData.length; i++) {
      const element = this.selectedData[i];
      if (element.transaksikirimfk != null) {
        this.alertService.error("Info", 'Tidak bisa hapus data yang sudah dikirim')
        return
      }
    }
    var data = {
      'orderpelayanan': this.selectedData
    }
    this.apiService.post('rawatinap/hapus-peritem-order-gizi', data).subscribe(e => {
      this.selectedData = []
      this.loadData()

    })
  }
  lanjutCetak(){

  }
}
