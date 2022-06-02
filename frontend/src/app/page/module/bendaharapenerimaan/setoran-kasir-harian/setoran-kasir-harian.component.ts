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
  selector: 'app-setoran-kasir-harian',
  templateUrl: './setoran-kasir-harian.component.html',
  styleUrls: ['./setoran-kasir-harian.component.scss'],
  providers: [ConfirmationService, MessageService]
})
export class SetoranKasirHarianComponent implements OnInit {
  page: number;
  rows: number;
  column: any[];
  selected: any;
  dataTable: any[];
  dataPenerimaan: any[] = [];
  item: any = {
    input: {},
  };
  listCaraBayar: any;
  listKelompokTransaksi: any;
  listPetugasPenerima: any = [];
  listStatSetor: any = [];
  listCaraSetor: any = [];
  listCaraBayarInput: any = [];
  dateNow: any;
  loginUser: any;
  isDeposit: boolean = false;
  isCicilanPasien: boolean = false;
  dataSource: any[] = [];
  columnGrid: any[] = [];
  dataSetoranKasir: any = [];
  addDataDetail: any[] = [];
  dataCaraBayar: any[] = [];
  data2: any[] = [];
  dataSelected: any;
  statusTambah: boolean = true;
  ttlPenerimaan: any = 0;
  isSimpan:boolean = false;
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private alertService: AlertService,
    private route: ActivatedRoute,
    private router: Router,
  ) { }

  ngOnInit(): void {
    this.loginUser = this.authService.getDataLoginUser();
    this.dateNow = new Date();
    this.item.tglAwal = new Date(moment(this.dateNow).format('YYYY-MM-DD 00:00'));
    this.item.tglAkhir = new Date(moment(this.dateNow).format('YYYY-MM-DD 23:59'));
    this.item.input.TglSetor = this.dateNow;
    this.item.jmlRows = 50;
    this.column = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'caraBayar', header: 'Cara Bayar', width: "140px" },
      { field: 'totalPenerimaan', header: 'Total Penerimaan', width: "160px", isCurrency: true },
      { field: 'totalSetor', header: 'Total Setor', width: "160px", isCurrency: true },
      { field: 'sisa', header: 'Total Sisa', width: "160px", isCurrency: true },
    ];
    this.columnGrid = [
      { field: 'no', header: 'No', width: "65px" },
      { field: 'caraBayar', header: 'Cara Bayar', width: "140px" },
      { field: 'caraSetor', header: 'Cara Setor', width: "140px" },
      { field: 'jumlah', header: 'Jumlah Setor', width: "140px", isCurrency: true },
    ]
    this.getDataCombo();
  }

  formatRupiah(value, currency) {
    return currency + " " + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }

  getDataCombo() {
    this.apiService.get("bendaharapenerimaan/get-combo-bp").subscribe(table => {
      this.listCaraBayar = table.carabayar;
      this.listKelompokTransaksi = table.jenistransaksi;
      this.listPetugasPenerima = table.petugaskasir;
      this.listStatSetor = table.statussetor;
      this.listCaraSetor = table.carasetor
      this.LoadCache();
    })
  }

  LoadCache() {
    var chacePeriode = this.cacheHelper.get('cacheSetoranKasirHarian');
    if (chacePeriode != undefined) {
      this.item.tglAwal = new Date(chacePeriode[0]);
      this.item.tglAkhir = new Date(chacePeriode[1]);
      this.LoadData();
    } else {
      this.LoadData();
    }
  }

  LoadData() {
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm');
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm');
    var chacePeriode = {
      0: tglAwal,
      1: tglAkhir,
    }
    this.cacheHelper.set('cacheSetoranKasirHarian', chacePeriode);
    var ScraSetor = "";
    if (this.item.StatSetor != undefined) {
      ScraSetor = this.item.StatSetor.id
    }
    var ScaraBayar = "";
    if (this.item.dataCaraBayar != undefined) {
      ScaraBayar = this.item.dataCaraBayar.id;
    }

    var listKasir = ""
    if (this.item.selectedKasir != undefined) {
      var a = ""
      var b = ""
      for (var i = this.item.selectedKasir.length - 1; i >= 0; i--) {

        var c = this.item.selectedKasir[i].id
        b = "," + c
        a = a + b
      }
      listKasir = a.slice(1, a.length)
    }

    this.apiService.get("bendaharapenerimaan/get-penerimaan-kasir?"
      + "dateStartTglSbm=" + tglAwal
      + "&dateEndTglSbm=" + tglAkhir
      + "&idCaraBayar=" + ScaraBayar
      + "&KetSetor" + ScraSetor
      + "&KasirArr=" + listKasir
    ).subscribe(table => {
      var data = table.data;
      var ttlPasien: any = 0;
      var ttlKlaim: any = 0;
      for (let i = 0; i < data.length; i++) {
        const element = data[i];
        element.no = i + 1;
        ttlPasien = ttlPasien + 1;
        ttlKlaim = ttlKlaim + parseInt(element.totalPenerimaan);
      }
      this.ttlPenerimaan = ttlKlaim;
      this.dataSetoranKasir = data
      this.dataPenerimaan = data;
      this.getTotal();
    })
  }

  getTotal() {
    var ttlPasien: any = 0;
    var ttlKlaim: any = 0;
    var ttlSsetor: any = 0;
    var jmlSetor: any = 0;
    var dataDetail: any = [];
    var dataDetail2: any = [];
    var caraBayararr: any = [];
    var caraBayararr1: any = [];
    var adaDetail = 0;
    var dataResult: any = []
    if (this.dataSetoranKasir != undefined) {
      for (let k = 0; k < this.dataSetoranKasir.length; k++) {
        const element = this.dataSetoranKasir[k];
        dataResult.push(element)
      }

      for (var x = dataResult.length - 1; x >= 0; x--) {
        if (dataResult[x].status == 'Sudah Setor') {
          dataResult.splice([x], 1)
        }
      }

      for (var i = 0; i < dataResult.length; i++) {
        ttlPasien = ttlPasien + 1;
        adaDetail = 0
        for (var x = 0; x < dataDetail2.length; x++) {
          if (dataDetail2[x].caraBayar == dataResult[i].caraBayar) {
            dataDetail2[x].totalPenerimaan = parseFloat(dataDetail2[x].totalPenerimaan) + parseFloat(dataResult[i].totalPenerimaan)
            adaDetail = 1;
          }
        };
        if (adaDetail == 0) {
          jmlSetor = 0;
          caraBayararr = { id: dataResult[i].idCaraBayar, namaLengkap: dataResult[i].caraBayar };
          var dataDetail0: any = [];
          for (var f = 0; f < this.addDataDetail.length; f++) {
            if (dataResult[i].caraBayar == this.addDataDetail[f].caraBayar) {
              dataDetail0.push(this.addDataDetail[f]);
              jmlSetor += parseFloat(this.addDataDetail[f].jumlah);
            };
          }
          if (dataDetail0.length > 0) {
            dataDetail = {
              caraBayar: dataResult[i].caraBayar,
              idCaraBayar: dataResult[i].idCaraBayar,
              totalPenerimaan: dataResult[i].totalPenerimaan,
              totalSetor: jmlSetor,
              sisa: dataResult[i].totalPenerimaan,
              detail: dataDetail0
            };
          } else {
            /*get dari serive*/
            var detailSetorans: any = [];
            for (var z = 0; z < dataResult[i].details.length; z++) {
              if (dataResult[i].caraBayar == dataResult[i].details[z].carabayar) {
                dataResult[i].details[z].caraBayar = dataResult[i].details[z].carabayar;
                dataResult[i].details[z].caraSetor = dataResult[i].details[z].carasetor;
                detailSetorans.push(dataResult[i].details[z]);
                jmlSetor += parseFloat(dataResult[i].details[z].jumlah);
              };
            }
            dataDetail = {
              caraBayar: dataResult[i].caraBayar,
              idCaraBayar: dataResult[i].idCaraBayar,
              totalPenerimaan: dataResult[i].totalPenerimaan,
              totalSetor: jmlSetor,
              sisa: dataResult[i].totalPenerimaan,
              detail: detailSetorans
            };
            /*end*/
          }
          dataDetail2.push(dataDetail);
          caraBayararr1.push(caraBayararr);
        }
      };

      for (let f = 0; f < dataDetail2.length; f++) {
        const element = dataDetail2[f];
        element.no = f + 1;
        element.sisa = parseFloat(element.totalPenerimaan) - parseFloat(element.totalSetor)
        ttlKlaim += parseInt(element.totalPenerimaan);
        ttlSsetor += parseInt(element.totalSetor);
      }
      this.dataTable = dataDetail2
      this.dataCaraBayar = caraBayararr1;
    };
    this.item.input.jumlah = 0;
    var sisa: any = 0;
    sisa = parseFloat(ttlKlaim) - parseFloat(ttlSsetor)
    this.item.totalSubTotal = this.formatRupiah(ttlKlaim, "Rp.");
    this.item.totalSetoran = this.formatRupiah(ttlSsetor, "Rp.");
    this.item.totalSisa = this.formatRupiah(sisa, "Rp.");
  }

  cari() {
    this.LoadData();
  }

  onRowSelect(event: any) {
    this.statusTambah = false
    var caraBayar: any = [];
    this.selected = event.data
    this.item.input.jumlah = this.selected.totalPenerimaan
    for (let i = 0; i < this.listCaraBayar.length; i++) {
      const element = this.listCaraBayar[i];
      if (element.id == this.selected.idCaraBayar) {
        caraBayar = { id: this.selected.idCaraBayar, carabayar: this.selected.caraBayar }
        break;
      }
    }
    this.item.input.CaraBayar = caraBayar;
    this.statusTambah = true
  }

  SetorkanSemua(event: any) {
    if (event.checked == true) {
      if (this.item.input.CaraBayar != undefined) {
        for (var i = 0; i < this.dataTable.length; i++) {
          if (this.dataTable[i].caraBayar == this.item.input.CaraBayar.carabayar) {
            this.item.input.jumlah = parseFloat(this.dataTable[i].totalPenerimaan)
          }
        }
      } else {
        this.alertService.error('Info', 'Pilih cara bayar dulu');
        this.item.SelectedSetorkanSemua = false;
      }
    }
  }

  kosongkan() {
    this.item.input.TglSetor = this.dateNow;
    this.item.input.CaraBayar = undefined;
    this.item.input.jumlah = undefined;
    this.item.input.CaraSetor = undefined;
    this.item.SelectedSetorkanSemua = false;
  }

  reset() {
    this.addDataDetail = []
    this.dataSource = undefined
  }

  batal() {
    this.kosongkan();
  }

  batalGrid() {
    this.kosongkan();
    this.reset();
  }

  tambah() {
    if (this.item.input.TglSetor == undefined) {
      this.alertService.error("Info", "Tanggal Setor Masih Kosong!");
      return;
    }

    if (this.item.input.CaraSetor == undefined) {
      this.alertService.error("Info", "Cara Setor Masih Kosong!");
      return;
    }

    if (this.item.input.CaraBayar == undefined) {
      this.alertService.error("Info", "Cara Bayar Masih Kosong!");
      return;
    }

    if (this.item.input.jumlah == 0 || this.item.input.jumlah == '' || this.item.input.jumlah == undefined) {
      this.alertService.error("Info", "Jumlah Setor Masih Kosong!");
      return;
    }

    var nomor = 0
    if (this.addDataDetail.length == 0) {
      nomor = 1
    } else {
      nomor = this.addDataDetail.length + 1
    }
    var arrayS: any = {};
    arrayS = {
      no: nomor,
      idCaraBayar: this.item.input.CaraBayar.id,
      caraBayar: this.item.input.CaraBayar.carabayar,
      idCaraSetor: this.item.input.CaraSetor.id,
      caraSetor: this.item.input.CaraSetor.carasetor,
      jumlah: this.item.input.jumlah
    };
    this.addDataDetail.push(arrayS);
    // var ttlKlaim: any = 0;
    // var ttlSsetor: any = 0;
    // var jmlSetor: any = 0;
    // var sisa:any = 0;
    // for (let i = 0; i < this.addDataDetail.length; i++) {
    //   const element = this.addDataDetail[i];
    //   ttlKlaim = ttlKlaim + parseFloat(element.jumlah);
    // }this.ttlPenerimaan
    // sisa = parseFloat(ttlKlaim) - parseFloat(ttlSsetor)
    // this.item.totalSubTotal = this.formatRupiah(ttlKlaim,"Rp.");
    // this.item.totalSetoran = this.formatRupiah(ttlSsetor,"Rp.");
    // this.item.totalSisa = this.formatRupiah(sisa,"Rp.");
    this.dataSource = this.addDataDetail;
    this.kosongkan();
    // this.getTotal();
  }

  hapusD(dataSelected) {
    for (var i = this.addDataDetail.length - 1; i >= 0; i--) {
      if (this.addDataDetail[i].no == dataSelected.no) {

        this.addDataDetail.splice(i, 1);
        this.dataSource = this.addDataDetail
      }
    }
    if (this.item.jenisKemasan.jeniskemasan != 'Racikan') {
      this.item.rke = parseFloat(this.item.rke) - 1
    }
    this.kosongkan()
  }

  editD(dataSelected) {
    this.dataSelected = dataSelected;
    this.item.no = dataSelected.no;
    this.item.input.CaraBayar = { id: this.dataSelected.idCaraBayar, carabayar: this.dataSelected.caraBayar };
    this.item.input.jumlah = this.dataSelected.jumlah;
    this.item.input.CaraSetor = { id: this.dataSelected.idCaraSetor, carasetor: this.dataSelected.caraSetor };
  }

  save() {
    var objSave: any = [];
    var objData: any = [];
    var objStr = {};
    var ttlSsetor: any = 0;
    for (var i = 0; i < this.addDataDetail.length; i++) {
      ttlSsetor = ttlSsetor + parseFloat(this.addDataDetail[i].jumlah);
      objStr = {
        "kdCaraBayar": this.addDataDetail[i].idCaraBayar,
        "totalPenerimaan": this.addDataDetail[i].jumlah,
        "kdAccountBank": null,
        "idCaraSetor": this.addDataDetail[i].idCaraSetor,
      }
      objData.push(objStr)
    }
    var objSBM = []
    for (let j = 0; j < objData.length; j++) {
      const Datas = objData[j];
      for (let x = 0; x < this.dataSetoranKasir.length; x++) {
        const element = this.dataSetoranKasir[x];
        if (element.idCaraBayar == Datas.kdCaraBayar) {
          objSBM.push({ 'norec_sbm': element.noRec })
        }
      }
    }

    var tgl = moment(this.item.tglAwal).format('YYYY-MM-DD')
    var tglAwal = moment(this.item.tglAwal).format('YYYY-MM-DD HH:mm')
    var tglAkhir = moment(this.item.tglAkhir).format('YYYY-MM-DD HH:mm')
    var tglSetor = moment(this.item.input.TglSetor).format('YYYY-MM-DD HH:mm')
    objSave = {
      "tglPenerimaan": tgl,
      "tglAwal": tglAwal,
      "tglAkhir": tglAkhir,
      "tglsetor": tglSetor,
      "kdPegawai": this.authService.getDataLoginUser().pegawai.id,
      "kdPegawaiLu": this.authService.getDataLoginUser().pegawai.id,
      "totalSetoran": ttlSsetor,
      "detailSetoran": objData,
      "detailSBM": objSBM
    }
    this.apiService.post('bendaharapenerimaan/save-setoran-kasir', objSave).subscribe(dataSave => {
      this.apiService.postLog('Simpan Setoran Kasir', 'norec transaksiclosingtr', dataSave.data[0].norec,
        'Simpan Setoran Kasir Dengan No Closing : ' + dataSave.data[0].noclosingi).subscribe(z => { })
      this.LoadData();
    })
  }

}
