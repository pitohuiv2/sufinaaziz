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
  selector: 'app-daftar-pasien-ri',
  templateUrl: './daftar-pasien-ri.component.html',
  styleUrls: ['./daftar-pasien-ri.component.scss']
})
export class DaftarPasienRiComponent implements OnInit {
  item: any = {
    tglMenu: new Date()
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
    this.column = [
      { field: 'no', header: 'No', width: "60px" },
      { field: 'tglregistrasi', header: 'Tgl Registrasi', width: "150px" },
      { field: 'noregistrasi', header: 'No Registrasi', width: "150px" },
      { field: 'norm', header: 'No RM', width: "100px" },
      { field: 'namapasien', header: 'Nama Pasien', width: "250px" },
      { field: 'umurzz', header: 'Umur', width: "100px" },
      { field: 'jeniskelamin', header: 'JK', width: "80px" },
      { field: 'namadokter', header: 'Dokter', width: "180px" },
      { field: 'namaruangan', header: 'Ruang', width: "200px" },
      { field: 'namakelas', header: 'Kelas', width: "120px" },
      { field: 'kelompokpasien', header: 'Tipe Pasien', width: "120px" },
    ];
    this.columnRiwayat = [
      { field: 'tglorder', header: 'Tgl Order', width: "100px" },
      { field: 'tglpelayananawal', header: 'Tgl Men', width: "100px" },
      { field: 'noorder', header: 'No Order', width: "100px" },
      { field: 'pengorder', header: 'Pengorder', width: "150px" },
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
    var noRegistrasi = ""
    if (this.item.noRegistrasi != undefined) {
      var noRegistrasi = "&noreg=" + this.item.noRegistrasi
    }

    var ruangId = ""
    if (this.item.ruangan != undefined) {
      var ruangId = "&ruangId=" + this.item.ruangan.id
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
    var jmlRow = ""
    if (this.item.rows != undefined) {
      jmlRow = "&jmlRow=" + this.item.rows
    }

    this.apiService.get("rawatinap/get-daftar-antrian-ranap?" +
      // "&tglAwal=" + tglAwal +
      // "&tglAkhir=" + tglAkhir +
      "&norm=" + nocm +
      "&noreg=" + noRegistrasi +
      "&nama=" + nama +
      "&ruanganArr=" + listRuangan
      + ruangId
      + jmlRow).subscribe(datas => {
        for (var i = 0; i < datas.length; i++) {
          datas[i].no = i + 1
          var tanggal = new Date()
          var tanggalLahir = new Date(datas[i].tgllahir);
          datas[i].umurzz = this.dateHelper.getUmur(tanggalLahir, tanggal);
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
  riwayat() {
    delete this.item.jenisWaktu
    delete this.item.jenisDiet
    delete this.item.kategoriDiet
    delete this.item.keterangan
    this.apiService.get('rawatinap/get-combo-gizi').subscribe(e => {
      this.listJenisWaktu = e.jeniswaktu
      this.listJenisDiet = e.jenisdiet
      this.listKategoriDiet = e.kategorydiet
      this.pop_daftarOrder = true

    })
    this.indexTab = 1
    this.handleChangeTab({ index: this.indexTab })
  }
  
  order() {
    if (this.selectedData.length == 0) {
      this.alertService.error('Info', 'Ceklis data dulu')
      return
    }
    this.alertService.info('Info', this.selectedData.length + ' data terpilih')

    delete this.item.jenisWaktu
    delete this.item.jenisDiet
    delete this.item.kategoriDiet
    delete this.item.keterangan
    this.item.tglMenu = new Date()
    this.apiService.get('rawatinap/get-combo-gizi').subscribe(e => {
      this.listJenisWaktu = e.jeniswaktu
      this.listJenisDiet = e.jenisdiet
      this.listKategoriDiet = e.kategorydiet
      this.pop_daftarOrder = true
    })

  }
  saveOrder() {
    if (this.selectedData.length == 0) {
      this.alertService.error('Info', 'Ceklis data dulu')
      return
    }
    if (this.item.jenisWaktu == undefined || this.item.jenisWaktu.length == 0) {
      this.alertService.error('Info', 'Jenis Waktu belum di pilih')
      return
    }

    var ket = null
    if (this.item.keterangan != undefined) {
      ket = this.item.keterangan
    }

    var jnsOrder = "Order Makan"
    if (this.item.jenisDiet == undefined) {
      this.alertService.error('Info', 'Jenis Diet belum di pilih !')
      return
    }
    if (this.item.jenisDiet.length == 0) {
      this.alertService.error('Info', 'Jenis Diet belum di pilih !')
      return
    }
    if (this.item.kategoriDiet == undefined) {
      this.alertService.error('Info', 'Kategori Diet belum di pilih !')
      return
    }


    for (var i = 0; i < this.selectedData.length; i++) {
      this.selectedData[i].keterangan = ket
      this.selectedData[i].volume = null
      this.selectedData[i].cc = null
      this.selectedData[i].objectkategorydietfk = this.item.kategoriDiet.id
    }
    var jenisDiet = ""

    if (this.item.jenisDiet.length != 0) {
      var a = ""
      var b = ""
      for (var i = this.item.jenisDiet.length - 1; i >= 0; i--) {

        var c = this.item.jenisDiet[i].id
        b = "," + c
        a = a + b
      }
      jenisDiet = a.slice(1, a.length)
    }
    var objSave = {
      "norec_so": "",
      "jenisorder": jnsOrder,
      "tglorder": moment(new Date()).format('YYYY-MM-DD HH:mm'),
      "tglmenu": moment(this.item.tglMenu).format('YYYY-MM-DD HH:mm'),
      "qtyproduk": this.selectedData.length,
      "details": this.selectedData,
      "jeniswaktufk": this.item.jenisWaktu.id,
      "jenisdietfk": jenisDiet
    }

    var data = {
      "strukorder": objSave
    }
    this.isSimpan = true;
    this.apiService.post('rawatinap/save-order-gizi', data).subscribe(result => {
      this.isSimpan = false;
      this.selectedData = [];
      this.pop_daftarOrder = false

      this.loadData()

    })
  }
  handleChangeTab(e) {
    this.indexTab = e.index
    if (e.index == 0) {

    } else {
      this.loadRiwayat()
    }

  }
  loadRiwayat() {
    var tglAwal = moment(this.itemR.tglAwal).format('YYYY-MM-DD HH:mm:ss');
    var tglAkhir = moment(this.itemR.tglAkhir).format('YYYY-MM-DD HH:mm:ss');

    var reg = ""
    if (this.itemR.noReg != undefined) {
      var reg = "&noreg=" + this.itemR.noReg
    }
    var rm = ""
    if (this.itemR.noRm != undefined) {
      var rm = "&norm=" + this.itemR.noRm
    }
    var nm = ""
    if (this.itemR.nama != undefined) {
      var nm = "&nama=" + this.itemR.nama
    }

    var rg = ""
    if (this.itemR.namaRuangan != undefined) {
      var rg = "&ruang=" + this.itemR.namaRuangan
    }

    var noorder = ""
    if (this.itemR.noOrder != undefined) {
      noorder = "&noorder=" + this.itemR.noOrder
    }
    var jenisDiet = ""

    if (this.itemR.jenisDiet != undefined && this.itemR.jenisDiet.length != 0) {
      var a = ""
      var b = ""
      for (var i = this.itemR.jenisDiet.length - 1; i >= 0; i--) {

        var c = this.itemR.jenisDiet[i].id
        b = "," + c
        a = a + b
      }
      jenisDiet = a.slice(1, a.length)
    }


    this.apiService.get("rawatinap/get-daftar-order-detail?" +
      "tglAwal=" + tglAwal +
      "&tglAkhir=" + tglAkhir +
      "&jenisDietId=" + jenisDiet +
      reg + rm + nm + rg + noorder)
      .subscribe(data => {
        var result = data.data
        for (var i = 0; i < result.length; i++) {
          for (var j = 0; j < result[i].details.length; j++) {
            const det = result[i].details[j]
            det.jenisdiet = []
            if (det.arrjenisdiet) {
              var arr = det.arrjenisdiet.split(',')
              if (arr.length > 0) {
                for (var x = 0; x < arr.length; x++) {
                  const jenis = arr[x]
                  for (var z = 0; z < this.listJenisDiet.length; z++) {
                    const diet = this.listJenisDiet[z]
                    if (jenis == diet.id) {
                      det.jenisdiet.push(diet)
                    }
                  }
                }
              }
            }
          }
        }
        this.dataSourceRiwayat = result
      });
  }
  hapusOrder(e) {

    var itemDelete = {
      "norec": e.norec
    }
    this.apiService.post('rawatinap/hapus-order-gizi', itemDelete).subscribe(e => {
      this.loadRiwayat()
    })
  }
  hapusItem(e) {
    if (e.nokirim != null) {
      this.alertService.warn('Info', 'Data Sudah Dikirim tidak bisa dihapus')
      return
    }
    var itemDelete = {
      "norec_op": e.norec_op
    }
    this.apiService.post('rawatinap/hapus-order-gizi-detail', itemDelete).subscribe(e => {
      this.loadRiwayat()
    })
  }
}
