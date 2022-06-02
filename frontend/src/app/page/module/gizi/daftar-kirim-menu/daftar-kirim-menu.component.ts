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
  selector: 'app-daftar-kirim-menu',
  templateUrl: './daftar-kirim-menu.component.html',
  styleUrls: ['./daftar-kirim-menu.component.scss'],
  providers: [ConfirmationService]
})
export class DaftarKirimMenuComponent implements OnInit {
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
  selectedData: any
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
  constructor(private apiService: ApiService,
    private authService: AuthService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
    private confirmationService: ConfirmationService,
  ) { }

  ngOnInit(): void {
    this.item.periodeAwal = new Date(moment(new Date()).format('YYYY-MM-DD 00:00'))
    this.item.periodeAkhir = new Date(moment(new Date()).format('YYYY-MM-DD 23:59'))

    this.column = [
      { field: 'no', header: 'No', width: "60px" },
      { field: 'nokirim', header: 'No Kirim', width: "120px" },
      { field: 'tglkirim', header: 'Tgl Kirim', width: "150px" },
      { field: 'ruanganasal', header: 'Ruang Asal', width: "200px" },
      { field: 'pegawaikirim', header: 'Petugas', width: "150px" },
      { field: 'keterangan', header: 'Ket', width: "150px" },
      // { field: 'jenisdiet', header: 'Jenis Diet', width: "200px", isArray: true },
    ]
    this.apiService.get('rawatinap/get-combo-gizi').subscribe(e => {
      this.listJenisWaktu = e.jeniswaktu
      this.listJenisDiet = e.jenisdiet
      this.loadData()
    })

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
    var noKirim = ""
    if (this.item.noKirim != undefined) {
      noKirim = this.item.noKirim
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

    this.apiService.get("rawatinap/get-daftar-kirim-gizi?" +
      "&tglAwal=" + tglAwal +
      "&tglAkhir=" + tglAkhir +
      "&norm=" + nocm +
      "&noreg=" + noRegistrasi +
      "&nokirim=" + noKirim +
      "&nama=" + nama
      // +
      // "&ruanganArr=" + listRuangan +
      // "&jenisdietarr=" + listJenisDiet +

      + jmlRow
      // + status
      // + jenisWaktu
    ).subscribe(da => {
      let datas = da.data
      for (var i = 0; i < datas.length; i++) {
        datas[i].no = i + 1
        // var tanggal = new Date()
        // var tanggalLahir = new Date(datas[i].tgllahir);
        // datas[i].umurzz = this.dateHelper.getUmur(tanggalLahir, tanggal);
        for (let x = 0; x < datas[i].details.length; x++) {
          const element = datas[i].details[x];
          element.jenisdiet = []
          if (element.arrjenisdiet) {
            var arr = element.arrjenisdiet.split(',')
            if (arr.length > 0) {
              for (var z = 0; z < arr.length; z++) {
                const jenis = arr[z]
                for (var zz = 0; zz < this.listJenisDiet.length; zz++) {
                  const diet = this.listJenisDiet[zz]
                  if (jenis == diet.id) {
                    element.jenisdiet.push(diet)
                  }
                }
              }
            }
          }
        }

      }
      this.dataSource = datas


    });
  }

  cetakLabel(e) {

    this.selectedData == e
    this.pop_Cetak = true
    this.item.jmlCetak = 1
  }
  cetakEuy() {

    if (this.item.jmlCetak = 0) {
      this.alertService.warn('Info', 'qty tidak boleh nol')
      return
    }


    var norec = this.selectedData.norec_op;
    var stt = 'false'
    if (confirm('View Label Gizi? ')) {
      // Save it!
      stt = 'true';
    } else {
      // Do nothing!
      stt = 'false'
    }

    this.apiService.getUrlCetak("http://127.0.0.1:3885/desk/routes?cetak-labelgizi=1&norec=" +
      norec + "&qty=" + this.item.jmlCetak + "&view=" + stt, function (e) { });

    this.pop_Cetak = false
  }

  batalKirim(event: Event, data) {
    this.confirmationService.confirm({
      target: event.target,
      message: 'Yakin mau batal kirim?',
      icon: 'pi pi-exclamation-triangle',
      acceptLabel: "Ya",
      rejectLabel: "Tidak",
      accept: () => {
       
          this.apiService.post('rawatinap/batal-kirim-gizi', {
            'norec': data.norec_sk
          }).subscribe(e => {
            this.loadData()
          })
      },
      reject: () => {
        //reject action
      }
    });
  }
}
