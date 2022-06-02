import { Component, OnInit, ViewChild, OnDestroy, Injectable } from '@angular/core';

import { Router, ActivatedRoute } from '@angular/router';
import * as $ from 'jquery';
//import * as mdb from 'mdbootstrap';
import { ViewEncapsulation } from '@angular/compiler/src/core';
import { FormBuilder, FormGroup, FormControl } from '@angular/forms';
import { Observable } from 'rxjs';
import { map, startWith, debounceTime, tap, switchMap, finalize } from 'rxjs/operators';
import * as moment from 'moment';
import { DateAdapter, MAT_DATE_FORMATS, MAT_DATE_LOCALE } from '@angular/material/core';
// import { BrowserQRCodeReader, VideoInputDevice } from 'zxing-typescript/src/browser/BrowserQRCodeReader'
import QRCode from 'qrcode';
import { ConfirmationService, MenuItem, MessageService, SelectItem } from 'primeng/api';
import { ApiService } from 'src/app/service';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';

// import { JsBarcode } from 'JsBarcode';
export const MY_FORMATS = {
  parse: {
    dateInput: 'LL',
  },
  display: {
    dateInput: 'LL',
    monthYearLabel: 'MMM YYYY',
    dateA11yLabel: 'LL',
    monthYearA11yLabel: 'MMMM YYYY',
  },
};
@Component({
  selector: 'app-reservasi',
  templateUrl: './reservasi.component.html',
  styleUrls: ['./reservasi.component.scss'],
  providers: [ConfirmationService]
})
export class ReservasiComponent implements OnInit {


  loading: boolean = false;
  isBaru: boolean = false
  isLama: boolean = false
  minDate = new Date()
  // minDate = new Date(new Date().setDate(new Date().getDate() + 1))
  maxDate: any
  model: any = {};
  stepsMenuItems: MenuItem[];
  activeIndex: number = 0;
  listJK: any[] = []
  listRuangan: any[] = []
  listDokter: any = []
  listTipePembayaran: any[] = []
  isSelected1: any;
  isSelected2: any;
  formGroup: FormGroup;
  formHistory: FormGroup;
  formPelayanan: FormGroup;
  myControl = new FormControl();
  isLoading: boolean
  isAsuransi: boolean = false
  filteredOptions: Observable<string[]>;
  items2: MenuItem[];
  activeItem: MenuItem;
  loadingHistory: boolean = false
  @ViewChild('menuItems') menu: MenuItem[];
  dataSource: any[]
  listJenisPasien: any[] =
    [
      { 'id': 1, 'jenis': 'Baru', 'src': 'assets/layout/images/baru.png', 'style': 'kanan', 'images': 'pasien-baru' },
      { 'id': 2, 'jenis': 'Lama', 'src': 'assets/layout/images/lama.png', 'style': '', 'images': 'pasien-lama' }
    ]
  selection: any = {}
  imageIndex: any = 0
  // imagesDenah: any="assets/img/denah_rs.jpg";
  images = [
    'assets/img/denah_rs.jpg',
    // 'https://i.ytimg.com/vi/nlYlNF30bVg/hqdefault.jpg',
    // 'https://www.askideas.com/media/10/Funny-Goat-Closeup-Pouting-Face.jpg'
  ];
  displayDialog: boolean = false;
  kodeReservasi: any;
  tglReservasi: any;
  poliTujuan: any;
  dokter: any;
  qrCode: any;
  isBuktiReservasi: boolean = false

  // displayDialog: boolean;
  sortOptions: SelectItem[];
  sortKey: string;
  sortField: string;
  sortOrder: number;
  isCollalsedPasien: boolean = true
  displayPopUpQr: boolean = false
  linkBarcode = 'https://barcode.tec-it.com/barcode.ashx?data='
  barCode: any
  linkBarcode2 = '&code=Code128&dpi=96&dataseparator='
  listJam: any = []
  loadSlot: boolean = false
  dataSlot: any[]
  disableSelect = new FormControl(false);
  isBPJS: boolean = false
  myFilter: any;
  maxJamReservasi: string
  rentangReservasi: any
  listHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  simpanVisible: boolean = true
  isShowRiwayatPelayanan: boolean
  item: any = {}
  isShowRincian: boolean = true
  dataSourceRincian: any[]
  rowGroupMetadata: any;
  totalTagihan: any
  totalTerbilang: any
  deposit: any
  totalBayar: any
  sisaTagihan: any
  noBpjs: any
  selectedItemGridRiwayat: any
  displayDialogRM: boolean
  dataSourceRm: any[]
  totalKlaim: any;
  isShowRiwayatRegis: boolean;
  dataSourceBank: any[]
  selectedNorek: any
  titleKlaim: any = 'Total Klaim'
  statusRujukanBerlaku: any = true
  isPanduanReservasi: boolean
  itemsPanduan: MenuItem[];
  isDukcapil: boolean
  imageIndexOne = 0;
  imageIndexTwo = 0;
  sortOptions2: SelectItem[];
  listJadwal: any[]
  sortOrder2: number;
  items: any = {}
  sortField2: string;
  config: ImageViewerConfig = {
    btnClass: 'default', // The CSS class(es) that will apply to the buttons
    zoomFactor: 0.1, // The amount that the scale will be increased by
    containerBackgroundColor: '#ccc', // The color to use for the background. This can provided in hex, or rgb(a).
    wheelZoom: true, // If true, the mouse wheel can be used to zoom in
    allowFullscreen: true, // If true, the fullscreen button will be shown, allowing the user to entr fullscreen mode
    allowKeyboardNavigation: true, // If true, the left / right arrow keys can be used for navigation
    btnIcons: { // The icon classes that will apply to the buttons. By default, font-awesome is used.
      zoomIn: 'fa fa-plus',
      zoomOut: 'fa fa-minus',
      rotateClockwise: 'fa fa-repeat',
      rotateCounterClockwise: 'fa fa-undo',
      next: 'fa fa-arrow-right',
      prev: 'fa fa-arrow-left',
      fullscreen: 'fa fa-arrows-alt',
    },
    btnShow: {
      zoomIn: true,
      zoomOut: true,
      rotateClockwise: false,
      rotateCounterClockwise: false,
      next: false,
      prev: false
    }
  }; // {customBtns: [{name: 'print', icon: 'fa fa-print'}, {name: 'link', icon: 'fa fa-link'}]};

  handleEvent(event: CustomEvent) {
    console.log(`${event.name} has been click on img ${event.imageIndex + 1}`);

    switch (event.name) {
      case 'print':
        console.log('run print logic');
        break;
    }
  }
  constructor(
    private httpService: ApiService,
    private cacheHelper: CacheService,
    private dateHelper: HelperService,
    private messageService: AlertService,
    private confirmationService: ConfirmationService,
    private fb: FormBuilder,
  ) {

  }

  onSortChange2(event) {
    let value = event.value;

    if (value.indexOf('!') === 0) {
      this.sortOrder = -1;
      this.sortField = value.substring(1, value.length);
    }
    else {
      this.sortOrder = 1;
      this.sortField = value;
    }
  }
  onSortChange(event) {
    let value = event.value;

    if (value.indexOf('!') === 0) {
      this.sortOrder = -1;
      this.sortField = value.substring(1, value.length);
    }
    else {
      this.sortOrder = 1;
      this.sortField = value;
    }
  }


  ngOnInit() {
    // var ssn = document.getElementById('ssn');
    var ssn = (<HTMLInputElement>document.getElementById('ssn'));

    // ssn.addEventListener('input', this.ssnMask, false);
    // var ssnFirstFive = "";
    // var secondHalf = "";
    // var fullSSN = "";

    let namafield = 'isRentangReservasi'
    this.httpService.get('sysadmin/settingdatafixed/get/' + namafield).subscribe(resp => {
      this.maxDate = new Date(new Date().setDate(new Date().getDate() + resp))
    }, error => {
      this.maxDate = new Date(new Date().setDate(new Date().getDate() + 28))
    })

    //
    this.httpService.get('reservasionline/get-libur').subscribe(e => {
      let arr = []
      if (e.libur.length > 0) {
        for (let i = 0; i < e.libur.length; i++) {
          const element = e.libur[i];
          arr.push(element.tgllibur)
        }
        this.myFilter = (d): boolean => {
          const day: number = d._d.getDay()

          for (let i = 0; i < arr.length; i++) {
            const element = arr[i];
            if (moment(d._d).format('YYYY-MM-DD') == element) {
              return false
            }
          }

          // Prevent Saturday and Sunday from being selected.
          return day != 0 && day != 6 ? true : false;
        }

      }
      this.getMaxJamReservasis()

      this.loadNoRek()
      //['2019-05-30', '2019-05-31', '2019-06-01', '2019-06-03', '2019-06-04', '2019-06-05', '2019-06-06', '2019-06-07']
      this.sortOptions2 = [
        { label: 'Terbaru', value: '!tanggalreservasi' },
        { label: 'Terlama', value: 'tanggalreservasi' }
      ];
    })

    // $("#barcode").JsBarcode("Hi!");
    // $("#barcode").barcode("1234567890128", "ean13");  

    // JsBarcode("#barcode", "Hi world!");
    // this.carService.getCarsLarge().then(cars => this.cars = cars);

    // $('#input_starttime').pickatime({
    //   // Light or Dark theme
    //   darktheme: true
    //   });
    this.items2 = [
      { label: 'Reservasi', icon: 'fa fa-fw fa-calendar' },
      { label: 'History', icon: 'fa fa-fw fa-history' },
      // { label: 'Slotting', icon: 'fa fa-fw fa fa-calendar-check-o' },
      { label: 'E-Billing', icon: 'fa fa-fw fa-heartbeat' },
      // { label: 'Bank Account', icon: 'fa fa-fw fa fa-bank' },
      { label: 'Panduan', icon: 'fa fa-fw fa-question-circle' },
      { label: 'Jadwal Dokter', icon: 'fa fa-fw fa-user-md' },
      // { label: 'Denah', icon: 'fa fa-fw fa-map-marker' },
    ];

    this.activeItem = this.items2[0];
    this.formGroup = this.fb.group({
      'namaPasien': new FormControl(null),
      'jenisKelamin': new FormControl(null),
      'tglLahir': new FormControl(null),
      'noTelpon': new FormControl(null),
      'noCm': new FormControl(null),
      'tglLahirLama': new FormControl(null),
      'dokter': new FormControl(null),
      'poliKlinik': new FormControl(null),
      'tipePembayaran': new FormControl(null),
      'tglReservasi': new FormControl({ value: null, disabled: true }),
      'noKartuPeserta': new FormControl(null),
      'jamReservasi': new FormControl(null),
      'noRujukan': new FormControl(null),
      'resumeNoRM': new FormControl({ value: null, disabled: true }),
      'resumeNama': new FormControl({ value: null, disabled: true }),
      'resumeNamaDukcapil': new FormControl({ value: null, disabled: true }),
      'resumeTglLahir': new FormControl({ value: null, disabled: true }),
      'resumeJK': new FormControl({ value: null, disabled: true }),
      'resumeNoTelp': new FormControl({ value: null, disabled: true }),
      'resumeTglReservasi': new FormControl({ value: null, disabled: true }),
      'resumePoli': new FormControl({ value: null, disabled: true }),
      'resumeDokter': new FormControl({ value: null, disabled: true }),
      'resumeTipe': new FormControl({ value: null, disabled: true }),
      'resumNoka': new FormControl({ value: null, disabled: true }),
      'resumNoRujukan': new FormControl({ value: null, disabled: true }),
      'tglReservasiFix': new FormControl(null),
      'isBaru': new FormControl(null),
      'tglAwal': new FormControl(null),
      'tglAkhir': new FormControl(null),
      'nik': new FormControl(null),
      'resumeNIK': new FormControl({ value: null, disabled: true }),
      'namaPasienDukcapil': new FormControl(null),
    });

    this.sortOptions = [
      { label: 'Newest First', value: '!year' },
      { label: 'Oldest First', value: 'year' },
      { label: 'Brand', value: 'brand' }
    ];
    this.formHistory = this.fb.group({
      'noCm': new FormControl(null),
      'namaPasien': new FormControl({ value: null, disabled: true }),
      'tglLahir': new FormControl({ value: null, disabled: false }),
      'tglLahir2': new FormControl(null),
      'noTelpon': new FormControl({ value: null, disabled: true }),
      'alamat': new FormControl({ value: null, disabled: true }),
      'noAsuransi': new FormControl({ value: null, disabled: true }),
      'jenisKelamin': new FormControl({ value: null, disabled: true }),
      'pendidikan': new FormControl({ value: null, disabled: true }),
      'pekerjaan': new FormControl({ value: null, disabled: true }),
      'tempatLahir': new FormControl({ value: null, disabled: true }),
      'noBPJS': new FormControl({ value: null, disabled: true }),
      'nik': new FormControl({ value: null, disabled: true }),
      'sortKey': new FormControl(null),

      'sortKey2': new FormControl(null),
    });

    this.formPelayanan = this.fb.group({
      'noRegistrasi': new FormControl(null),
      'noRm': new FormControl(null),

    });
    this.setSourceStepMenu()
    this.setSourceCombo()

    // this.formGroup.controls['tipePembayaran'].valueChanges.subscribe(
    //   (selectedValue) => {
    //     if (selectedValue.id == 2) {
    //       // this.isAsuransi = false
    //       this.isBPJS = true

    //     } else {
    //       // this.isAsuransi = true
    //       this.isBPJS = false

    //     }
    //   }
    // );
    // var ssn = document.getElementById('idNamaPasien');
    // ssn.addEventListener('input', this.ssnMask(ssn), false);


  }
  ssnMask() {
    var ssn = (<HTMLInputElement>document.getElementById('ssn'));

    if (ssn.value.length <= 5) {
      ssn.type = 'password';
    }
    else {
      var ssnFirstFive = "";
      var secondHalf = "";
      var fullSSN = "";
      this.detectChanges(ssn);
      secondHalf = ssn.value.substring(5);
      ssn.type = 'text';
      ssn.value = "•••••";
      ssn.value += secondHalf;
      fullSSN = ssnFirstFive + secondHalf;
    }
  }

  detectChanges(ssn) {
    var ssnFirstFive = "";
    var secondHalf = "";
    var fullSSN = "";
    for (var i = 0; i < 5; i++) {
      if (ssn.value[i] != "•") {
        ssnFirstFive = ssnFirstFive.substring(0, i) + ssn.value[i] + ssnFirstFive.substring(i + 1);
      }
    }
  }
  getNik() {
    let nik = this.formGroup.get('nik').value
    if (nik != null && nik.length > 10)
      this.isDukcapil = false
    this.httpService.get('bridging/dukcapil/get-nik/' + nik).subscribe(e => {
      if (e.content == undefined) {
        return
      }
      if (e.messages) {
        this.messageService.error('Error', e.messages)
        return
      }
      if (e.content[0] != "") {
        let result = e.content[0]
        let namaPasien = result.NAMA_LGKP
        let subStr = namaPasien.substring(0, 5)
        this.isDukcapil = true
        // this.formHistory.get('namaPasien').setValue(subStr + ' ********')
        this.messageService.success('Sukses', 'Nama Lengkap : ' + subStr + ' ********')
        this.formGroup.get('namaPasien').setValue(result.NAMA_LGKP)
        this.formGroup.get('namaPasienDukcapil').setValue(subStr + ' ********')


        for (let i = 0; i < this.listJK.length; i++) {
          const element = this.listJK[i];
          if (element.jeniskelamin.toLowerCase().indexOf(result.JENIS_KLMIN.toLowerCase()) > -1) {
            this.formGroup.get('jenisKelamin').setValue(element)
            break
          }
        }
        this.formGroup.get('tglLahir').setValue(new Date(result.TGL_LHR))
        this.formGroup.get('namaPasien').setValue(result.NAMA_LGKP)
      } else {
        this.messageService.error('Info', e.content.RESPON)
      }
    }, function (err) {
      this.isDukcapil = false
    });

  }
  changeTipeBayar(event) {
    if (event.value.id == 2) {
      if (!this.isBaru && this.noBpjs) {
        this.formGroup.get('noKartuPeserta').setValue(this.noBpjs)
        this.formGroup.get('noRujukan').setValue("-")
      }
      this.isBPJS = true
    } else {
      if (!this.isBaru) {
        this.formGroup.get('noKartuPeserta').reset()
        this.formGroup.get('noRujukan').reset()
      }
      this.isBPJS = false
    }
  }
  onMenuButtonClick(event) {
    this.activeItem = {
      icon: "fa fa-fw fa-history",
      label: "History"
    }
  }
  selectedJenis2(select) {
    if (select.id == 1) {
      this.isBaru = true
      this.isLama = false
    } else if (select.id == 2) {
      this.isBaru = false
      this.isLama = true
    } else {
      this.isBaru = false
      this.isLama = false
    }
    this.selection.selectedJenis2 = select;
  }
  closeItem(event, index) {
    this.items2 = this.items2.filter((item, i) => i !== index);
    event.preventDefault();
  }
  activateMenu() {
    this.activeItem = this.menu['activeItem'];
    if (this.activeItem.label == "Reservasi") {
      this.activeIndex = 0;
    }
    // if (this.activeItem.label == "Bank Account") {
    //   this.loadNoRek()
    // }
    if (this.activeItem.label == "Jadwal Dokter") {
      this.loadDokter()
    }
  }
  activateMenu2(data) {
    this.activeItem = data;
    if (this.activeItem.label == "Reservasi") {
      this.activeIndex = 0;
    }
    // if (this.activeItem.label == "Bank Account") {
    //   this.loadNoRek()
    // }
    if (this.activeItem.label == "Jadwal Dokter") {
      this.loadDokter()
    }
  }

  loadNoRek() {
    this.dataSourceBank = []

    // this.httpService.get('reservasionline/get-bank-account')
    //   .subscribe(response => {

    //     this.dataSourceBank = response.data
    //   }, error => {
    //     this.dataSourceBank = []
    //   })
  }
  clearHistory() {
    this.formHistory.get('noCm').reset()
    this.formHistory.get('tglLahir').reset()
  }
  editHistory(selected) {

  }
  hapusHistory(selected) {
    let data = {
      'norec': selected.norec
    }
    this.confirmationService.confirm({
      message: 'Yakin mau menghapus data?',
      accept: () => {
        this.httpService.post('reservasionline/delete', data).subscribe(res => {
          this.findHistory()

        }, error => {

        })
      }
    })
  }
  cetakHistory(selected) {
    this.kodeReservasi = selected.noreservasi
    this.tglReservasi = selected.tanggalreservasi
    this.poliTujuan = selected.namaruangan
    this.dokter = selected.dokter

    this.barCode = this.linkBarcode + selected.noreservasi + this.linkBarcode2
    QRCode.toDataURL(selected.noreservasi)
      .then(url => {
        this.qrCode = url
        console.log(url)
      })
      .catch(err => {
        console.error(err)
      })
    this.isBuktiReservasi = true
    // this.displayDialog = true
  }
  findHistory() {

    this.loadingHistory = true
    let noCM = this.formHistory.get('noCm').value
    if (noCM == null) {
      this.messageService.warn('Peringatan', 'Nama atau No CM harus di isi !')
      return
    }

    let tglLahirLama = moment(this.formHistory.get('tglLahir').value).format('DD-MM-YYYY')
    if (tglLahirLama == null) {
      this.messageService.warn('Peringatan', 'Tgl Lahir harus di isi !')
      return
    }
    this.httpService.get('reservasionline/get-history?nocmNama=' + noCM + '&tgllahir=' + tglLahirLama).subscribe(res => {
      this.loadingHistory = false
      if (res.data.length > 0) {
        this.isCollalsedPasien = false
        for (let i = 0; i < res.data.length; i++) {
          const element = res.data[i];
          if (element.isconfirm != null) {
            element.status = 'Confirm'
          } else {
            element.status = 'Reservasi'
          }
          let tglReserDay = new Date(element.tanggalreservasi)
          let _hari = tglReserDay.getDay();
          let hariFix = this.listHari[_hari]
          element.tanggalreservasi = hariFix + ', ' + moment(new Date(element.tanggalreservasi)).format('DD-MMM-YYYY HH:mm') //HH:mm
        }
        let namaPasien = res.data[0].namapasien
        let subStr = namaPasien.substring(0, 5)

        // this.formHistory.get('namaPasien').setValue(subStr + '********')
        this.formHistory.get('namaPasien').setValue(res.data[0].namapasien)
        this.formHistory.get('jenisKelamin').setValue(res.data[0].jeniskelamin)
        let notelp = ''
        if (res.data[0].notelepon == null)
          res.data[0].notelepon = ''
        if (res.data[0].nohp == null)
          res.data[0].nohp = ''
        notelp = res.data[0].notelepon + ' - ' + res.data[0].nohp
        this.formHistory.get('nik').setValue(res.data[0].noidentitas)
        this.formHistory.get('tempatLahir').setValue(res.data[0].tempatlahir)
        this.formHistory.get('noTelpon').setValue(notelp)
        // this.formHistory.get('noBPJS').setValue(res.data[0].nobpjs != null ? res.data[0].nobpjs : '-')
        this.formHistory.get('noAsuransi').setValue(res.data[0].nobpjs != null ? res.data[0].nobpjs : '-' +
          ' / ' + res.data[0].noasuransilain != null ? res.data[0].noasuransilain : '-')
        this.formHistory.get('alamat').setValue(res.data[0].alamatlengkap)
        this.formHistory.get('pekerjaan').setValue(res.data[0].pekerjaan)
        this.formHistory.get('pendidikan').setValue(res.data[0].pendidikan)

        this.dataSource = res.data
      } else {
        this.dataSource = []
        this.isCollalsedPasien = true
        this.messageService.info('Info', 'Data tidak ada')
      }

    }, error => {
      this.isCollalsedPasien = true
      this.dataSource = []
      this.loadingHistory = false
      this.messageService.info('Info', 'Data tidak ada')
    })
  }

  setSourceStepMenu() {
    this.stepsMenuItems = [
      {
        label: 'Jenis Pasien',
        command: (event: any) => {
          this.activeIndex = 0;
        }
      },
      {
        label: 'Tgl Reservasi & Poli',
        command: (event: any) => {
          this.activeIndex = 1;

        }
      },
      {
        label: 'Resume',
        command: (event: any) => {
          this.activeIndex = 2;
        }
      },
      // {
      //   label: '',
      //   command: (event: any) => {
      //     this.activeIndex = 3;

      //   }
      // },
    ];
  }
  selectedJenis(e, color) {
    if (e == 1) {
      this.isBaru = true
      this.isLama = false
    } else if (e == 2) {
      this.isBaru = false
      this.isLama = true
    } else {
      this.isBaru = false
      this.isLama = false
    }
  }

  nextWizard(valueIndex) {
    let noCM = this.formGroup.get('noCm').value
    let jenisKelamin = this.formGroup.get('jenisKelamin').value
    let tglLahir = this.formGroup.get('tglLahir').value
    let noTelpon = this.formGroup.get('noTelpon').value
    let namaPasien = this.formGroup.get('namaPasien').value
    let tglLahirLama = this.formGroup.get('tglLahirLama').value
    let nik = this.formGroup.get('nik').value

    if (valueIndex == 1) {
      if (this.isBaru) {
        if (!nik) {
          this.messageService.warn('Peringatan', 'NIK harus di isi !')
          return
        }
        if (!namaPasien) {
          this.messageService.warn('Peringatan', 'Nama Pasien harus di isi !')
          return
        }
        if (!tglLahir) {
          this.messageService.warn('Peringatan', 'Tgl Lahir harus di isi !')
          return
        }
        if (!jenisKelamin) {
          this.messageService.warn('Peringatan', 'Jenis Kelamin harus di pilih !')
          return
        }
        if (!noTelpon) {
          this.messageService.warn('Peringatan', 'No Telepon harus di isi !')
          return
        }
        if (nik.length > 14) {
          this.httpService.get('reservasionline/cek-pasien-baru-by-nik/' + nik).subscribe(e => {
            if (e.data.length > 0) {
              this.messageService.info('Info', 'Pasien ini adalah pasien lama dengan No RM ' + e.data[0].nocm)
              this.messageService.info('Info', 'Mohon daftar dengan Tipe Pasien Lama')
              return
            } else
              this.activeIndex = valueIndex
          })
        } else {
          this.activeIndex = valueIndex
        }



      }
      if (this.isLama) {
        if (!noCM) {
          this.messageService.warn('Peringatan', 'No RM harus di isi !')
          return
        }
        // if (!tglLahirLama) {
        //   this.messageService.warn('Peringatan', 'Tgl Lahir harus di isi !')
        //   return
        // }
        // var tgllahir = moment(tglLahirLama).format('DD-MM-YYYY')
        this.httpService.get('reservasionline/get-pasien/' + noCM + '/' + tglLahirLama).subscribe(res => {
          if (res.data.length > 0) {
            if (noCM.length > 6) {
              this.formGroup.get('noCm').setValue(res.data[0].nocm)
            }

            this.formGroup.get('resumeNIK').setValue(res.data[0].noidentitas)
            this.formGroup.get('noTelpon').setValue(res.data[0].notelepon)
            this.formGroup.get('namaPasien').setValue(res.data[0].namapasien)
            let tglLhia = new Date(res.data[0].tgllahir)
            console.log(tglLhia)
            this.formGroup.get('tglLahirLama').setValue(moment(tglLhia).format('YYYY-MM-DD'))
            this.formGroup.get('jenisKelamin').setValue({ id: res.data[0].objectjeniskelaminfk, jeniskelamin: res.data[0].jeniskelamin })
            let namaPasien = res.data[0].namapasien
            let subStr = namaPasien.substring(0, 5)
            this.messageService.success('Sukses', 'Nama Pasien ' + namaPasien )
            // this.messageService.success('Sukses', 'Nama Pasien ' + subStr + '********')
            this.noBpjs = res.data[0].nobpjs
            this.activeIndex = valueIndex
          }

          else
            this.messageService.error('Info', 'Data tidak ditemukan')

        }, error => {
          this.messageService.error('Info', 'Data tidak ditemukan')
        })


      }
    }
    if (valueIndex == 2) {
      // this.getMaxJamReservasi()
      let tglReservasi = this.formGroup.get('tglReservasi').value
      tglReservasi = new Date(tglReservasi._i.year, tglReservasi._i.month, tglReservasi._i.date)
      let poliKlinik = this.formGroup.get('poliKlinik').value
      // let dokter = this.formGroup.get('dokter').value
      let tipePembayaran = this.formGroup.get('tipePembayaran').value
      let jamReservasi = this.formGroup.get('jamReservasi').value
      let noRujukan = this.formGroup.get('noRujukan').value
      let noKartuPeserta = this.formGroup.get('noKartuPeserta').value
      if (!tglReservasi) {
        this.messageService.warn('Peringatan', 'Tgl Reservasi harus di isi !')
        return
      }
      if (!poliKlinik) {
        this.messageService.warn('Peringatan', 'Poliklinik belum di pilih !')
        return
      }
      if (!jamReservasi) {
        this.messageService.warn('Peringatan', 'Jam Reservasi belum di pilih !')
        return
      }
      // if (!dokter) {
      //   this.messageService.warn('Peringatan', 'Dokter belum di pilih !')
      //   return
      // }
      if (!tipePembayaran) {
        this.messageService.warn('Peringatan', 'Tipe Pembayaran belum di pilih !')
        return
      }
      if (tipePembayaran.id == 2) {
        if (!noKartuPeserta) {
          this.messageService.warn('Peringatan', 'No Kartu Peserta belum di isi !')
          return
        }
        if (!noRujukan) {
          this.messageService.warn('Peringatan', 'No Rujukan belum di isi !')
          return
        }
      }

      let tgl = ''
      if (this.isBaru) {
        tgl = tglLahir
        this.formGroup.get('isBaru').setValue(true)
      }
      else {
        tgl = tglLahirLama
        this.formGroup.get('isBaru').setValue(false)
      }

      if (noCM != null && noCM.length > 6)
        this.formGroup.get('resumeNIK').setValue(noCM)
      else
        this.formGroup.get('resumeNoRM').setValue(noCM)
      let namaPasiensss = namaPasien
      let subStr = namaPasiensss.substring(0, 5)
      this.formGroup.get('resumeNamaDukcapil').setValue(subStr + ' *******')
      this.formGroup.get('resumeNama').setValue(namaPasien)
      this.formGroup.get('resumeTglLahir').setValue(moment(tgl).format('DD-MM-YYYY'))
      this.formGroup.get('resumeJK').setValue(jenisKelamin.jeniskelamin)
      this.formGroup.get('resumeNoTelp').setValue(noTelpon)

      this.formGroup.get('resumeTglReservasi').setValue(moment(tglReservasi).format('DD-MM-YYYY') + ' ' + jamReservasi.jam)
      this.formGroup.get('resumePoli').setValue(poliKlinik.namaruangan)
      // this.formGroup.get('resumeDokter').setValue(dokter.namalengkap)
      this.formGroup.get('resumeDokter').setValue('-')
      this.formGroup.get('resumeTipe').setValue(tipePembayaran.kelompokpasien)
      this.formGroup.get('resumNoka').setValue(this.formGroup.get('noKartuPeserta').value != null ? this.formGroup.get('noKartuPeserta').value : '')
      this.formGroup.get('resumNoRujukan').setValue(this.formGroup.get('noRujukan').value != null ? this.formGroup.get('noRujukan').value : '-')
      let tipePasiens = ''
      if (this.isBaru) {
        tipePasiens = 'baru'
      } else {
        tipePasiens = 'lama'
      }

      if (this.formGroup.get('tipePembayaran').value.id == 2) {
        // cek pasien dihari yang sama hanya boleh satu kali daftar
        let tglLahirs = moment(new Date(this.formGroup.get('tglLahir').value)).format('YYYY-MM-DD')
        let cektglReservasi = moment(tglReservasi).format('YYYY-MM-DD')
        this.httpService.get('reservasionline/cek-reservasi-satu?tglReservasi=' + cektglReservasi
          + '&tipePasien=' + tipePasiens
          + '&noCm=' + this.formGroup.get('noCm').value
          + '&tglLahir=' + tglLahirs
          + '&namaPasien=' + this.formGroup.get('namaPasien').value
          + '&ruanganId=' + poliKlinik.id
        ).subscribe(e => {
          if (e.data.length > 0) {
            this.messageService.warn('Perhatian', 'Pasien BPJS di Hari yang sama hanya dapat mendaftar Satu Kali')
            return
          } else {
            this.onCariNoRujukan(this.formGroup.get('noKartuPeserta').value, valueIndex)
            // this.activeIndex = valueIndex
          }
        }, error => {
          this.activeIndex = valueIndex
        })

      } else {
        this.activeIndex = valueIndex
      }
      //end cek

    }
    if (valueIndex == 3) {
      this.listJam = []

      this.activeIndex = valueIndex
    }
  }
  onCariNoRujukan(searchValue: string, valueIndex): void {
    // if (searchValue.length <= 14) {
    //   this.cekPesertaByNoKartu(this.formGroup.get('noRujukan').value)
    // } else {
    //   this.cekPesertaByNoRujukan(this.formGroup.get('noRujukan').value)
    // }
    this.activeIndex = valueIndex
    // this.httpService.get('bridging/bpjs/get-rujukan-pcare-nokartu?nokartu=' + searchValue).subscribe(e => {
    //   if (e.response != null) {
    //     this.formGroup.get('noRujukan').setValue(e.response.rujukan.noKunjungan)
    //     this.formGroup.get('resumNoRujukan').setValue(this.formGroup.get('noRujukan').value != null ? this.formGroup.get('noRujukan').value : '-')
    //     this.activeIndex = valueIndex
    //   } else {
    //     this.httpService.get('bridging/bpjs/get-rujukan-rs-nokartu?nokartu=' + searchValue).subscribe(e => {
    //       if (e.response != null) {
    //         this.formGroup.get('noRujukan').setValue(e.response.rujukan.noKunjungan)
    //         this.formGroup.get('resumNoRujukan').setValue(this.formGroup.get('noRujukan').value != null ? this.formGroup.get('noRujukan').value : '-')
    //         this.activeIndex = valueIndex
    //       } else {
    //         // this.statusRujukanBerlaku = false
    //         this.messageService.error('Peringatan', 'Rujukan Tidak Ada atau Masa Berlaku habis, Silahkan Hubungi Call Center RSUD Mataram')

    //       }
    //     }, error => {
    //       // this.statusRujukanBerlaku = false
    //       this.messageService.error('Peringatan', 'Rujukan Tidak Ada atau Masa Berlaku habis, Silahkan Hubungi Call Center RSUD Mataram')

    //     })
    //   }
    // }, error => {
    //   // this.statusRujukanBerlaku = false
    //   this.messageService.error('Peringatan', 'Rujukan Tidak Ada atau Masa Berlaku habis, Silahkan Hubungi Call Center RSUD Mataram')

    // })

  }
  cekPasienMendaftarDihariSama(tglReser, tipePasien) {
    let tglLahir = moment(new Date(this.formGroup.get('tglLahir').value)).format('YYYY-MM-DD')
    tglReser = moment(tglReser).format('YYYY-MM-DD')
    this.httpService.get('reservasionline/cek-reservasi-satu?tglReservasi=' + tglReser
      + '&tipePasien=' + tipePasien
      + '&noCm=' + this.formGroup.get('noCm').value
      + '&tglLahir=' + tglLahir
      + '&namaPasien=' + this.formGroup.get('namaPasien').value).subscribe(e => {

      }, error => {

      })
  }

  setSourceCombo() {
    this.httpService.get('reservasionline/get-list-data').subscribe(e => {

      e.jeniskelamin.forEach(element => {
        this.listJK.push(element)
      });

      e.ruanganrajal.forEach(element => {
        this.listRuangan.push(element)
      });

      e.dokter.forEach(element => {
        this.listDokter.push(element)
      });

      e.kelompokpasien.forEach(element => {
        this.listTipePembayaran.push(element)
      });

    }, error => {

    })

    // this.filteredOptions = this.formGroup.get('dokter').valueChanges
    // .pipe(
    //   startWith(''),
    //   map(value => this._filter(value))
    // );

  }

  save() {
    if (this.isBaru) {
      this.formGroup.get('tglLahir').setValue(moment(this.formGroup.get('tglLahir').value).format('YYYY-MM-DD'))
    } else {
      this.formGroup.get('tglLahir').setValue(moment(this.formGroup.get('tglLahirLama').value).format('YYYY-MM-DD'))
    }
    let tglReservasi = this.formGroup.get('tglReservasi').value
    tglReservasi = new Date(tglReservasi._i.year, tglReservasi._i.month, tglReservasi._i.date)
    var tanggal: any = moment(tglReservasi).format('YYYY-MM-DD') + ' ' + this.formGroup.get('jamReservasi').value.jam
    // console.log(tanggal)
    this.formGroup.get('tglReservasiFix').setValue(tanggal)

    // this.formGroup.get('dokter').setValue({ id: this.formGroup.get('dokter').value.id, namalengkap: this.formGroup.get('dokter').value.namalengkap })
    this.formGroup.get('dokter').setValue(null)
    this.confirmationService.confirm({
      message: 'Yakin mau menyimpan data?',
      accept: () => {
        this.simpanVisible = false
        this.httpService.post('reservasionline/save', this.formGroup.value).subscribe(res => {

          var resultNoReservasi = res.data
          if (this.isBaru) {
            this.formHistory.get('noCm').setValue(this.formGroup.get('namaPasien').value)
          } else {
            this.formHistory.get('noCm').setValue(this.formGroup.get('noCm').value)
          }
          this.formHistory.get('tglLahir').setValue(this.formGroup.get('tglLahir').value)
          let tglLahirLama = moment(this.formHistory.get('tglLahir').value).format('DD-MM-YYYY')
          this.httpService.get('reservasionline/get-history?nocmNama=' + this.formHistory.get('noCm').value + '&tgllahir='
            + tglLahirLama).subscribe(res => {
              let dataReserv = {}
              this.loadingHistory = false
              if (res.data.length > 0) {
                this.isCollalsedPasien = false
                for (let i = 0; i < res.data.length; i++) {
                  const element = res.data[i];
                  let tglReserDay = new Date(element.tanggalreservasi)
                  let _hari = tglReserDay.getDay();
                  let hariFix = this.listHari[_hari]
                  element.tanggalreservasi = hariFix + ', ' + moment(new Date(element.tanggalreservasi)).format('DD-MMM-YYYY HH:mm') //HH:mm
                  // element.tanggalreservasi = moment(new Date(element.tanggalreservasi)).format('DD-MMM-YYYY HH:mm')
                }
                let tglReserDays = new Date(resultNoReservasi.tanggalreservasi)
                let _haris = tglReserDays.getDay();
                let hariFix = this.listHari[_haris]
                resultNoReservasi.tanggalreservasi = hariFix + ', ' + moment(new Date(resultNoReservasi.tanggalreservasi)).format('DD-MMM-YYYY HH:mm') //HH:mm

                let namaPasien = res.data[0].namapasien
                let subStr = namaPasien.substring(0, 5)

                this.formHistory.get('namaPasien').setValue(subStr + '********')
                this.formHistory.get('jenisKelamin').setValue(res.data[0].jeniskelamin)
                let notelp = ''
                if (res.data[0].notelepon == null)
                  res.data[0].notelepon = ''
                if (res.data[0].nohp == null)
                  res.data[0].nohp = ''
                notelp = res.data[0].notelepon + ' - ' + res.data[0].nohp

                this.formHistory.get('tempatLahir').setValue(res.data[0].tempatlahir)
                this.formHistory.get('noTelpon').setValue(notelp)
                // this.formHistory.get('noBPJS').setValue(res.data[0].nobpjs != null ? res.data[0].nobpjs : '-')
                this.formHistory.get('noAsuransi').setValue(res.data[0].nobpjs != null ? res.data[0].nobpjs : '-' +
                  ' / ' + res.data[0].noasuransilain != null ? res.data[0].noasuransilain : '-')
                this.formHistory.get('alamat').setValue(res.data[0].alamatlengkap)
                this.formHistory.get('pekerjaan').setValue(res.data[0].pekerjaan)
                this.formHistory.get('pendidikan').setValue(res.data[0].pendidikan)
                this.dataSource = res.data

                this.formGroup.reset()
              } else {
                this.dataSource = []
                this.isCollalsedPasien = true
                // this.messageService.info('Info', 'Data tidak ada')
              }
              this.activeItem = {
                icon: "fa fa-fw fa-history",
                label: "History"
              }

              this.cetakHistory(resultNoReservasi)
              this.simpanVisible = true
            }, error => {
              this.isCollalsedPasien = true
              this.dataSource = []
              this.loadingHistory = false
              this.simpanVisible = true
              // this.messageService.info('Info', 'Data tidak ada')
            })


          // this.formGroup.reset()

        }, error => {
          this.simpanVisible = true
        })
      }
    })
  }
  popUpQR() {
    this.displayPopUpQr = true
  }
  getSlotting(event) {
    // debugger
    let idRuangan = event.value.id
    if (idRuangan == undefined) {
      idRuangan = this.formGroup.get('poliKlinik').value != null ? this.formGroup.get('poliKlinik').value.id : undefined
    }
    if (idRuangan == undefined) {
      return
    }
    this.formGroup.get('jamReservasi').reset()
    // let tglReservasi = moment(this.formGroup.get('tglReservasi').value).format('YYYY-MM-DD')
    let tglReservasi = this.formGroup.get('tglReservasi').value
    tglReservasi = moment(new Date(tglReservasi._i.year, tglReservasi._i.month, tglReservasi._i.date)).format('YYYY-MM-DD')
    this.httpService.get('reservasionline/get-slotting-by-ruangan-new/'
      + idRuangan + '/' + tglReservasi).subscribe(res => {
        let now = moment(new Date()).format('YYYY-MM-DD')
        let tglNow = new Date(now + ' ' + '13:00')
        if (this.maxJamReservasi != undefined) {
          tglNow = new Date(now + ' ' + this.maxJamReservasi)
        }

        let tglNowNol = new Date(now + ' ' + '00:00')
        let tglPesen = this.formGroup.get('tglReservasi').value
        tglPesen = new Date(tglPesen._i.year, tglPesen._i.month, tglPesen._i.date)

        this.listJam = [];
        if(res.slot == null){
          this.messageService.error('Maaf', 'Slot Belum di Setting')
          return
        }
        let tglPesenNol = new Date(moment(tglPesen).format('YYYY-MM-DD 00:00'))
        let tglRes = tglPesen
        let yesterday = new Date(tglRes.setDate(tglRes.getDate() - 1))
        let tglReservasis = moment(tglPesen).format('YYYY-MM-DD')
        // if (new Date() > tglNow && new Date() < new Date(tglReservasis)) {
        if (new Date() > tglNow && new Date() > tglPesen) {
          if (this.maxJamReservasi != undefined) {
            this.messageService.error('Maaf', 'Jadwal Reservasi untuk besok maksimal dipesan sebelum jam ' + this.maxJamReservasi + ', Coba reservasi dihari diberikutnya')
            return
          }
          this.messageService.error('Maaf', 'Jadwal Reservasi untuk besok maksimal dipesan sebelum jam 13:00, Coba reservasi dihari diberikutnya')
          return
        }
        if (tglNowNol == tglPesenNol) {
          this.messageService.error('Maaf', 'Tidak Bisa Pesan Untuk Tanggal Sekarang')
          return
        }
        let jamBuka = res.tanggal + ' ' + res.slot.jambuka
        let jamTutup = res.tanggal + ' ' + res.slot.jamtutup
        let interval = res.slot.interval
        let quota = res.slot.quota
        let result = [];
        result = this.intervals(jamBuka, jamTutup, interval)

        if (result.length > 0) {
          for (let i = 0; i < quota; i++) {
            let hour: any = new Date(result[i]).getHours() < 10 ? '0' + new Date(result[i]).getHours() : new Date(result[i]).getHours()
            let minutes: any = new Date(result[i]).getMinutes() < 10 ? '0' + new Date(result[i]).getMinutes() : new Date(result[i]).getMinutes()
            this.listJam.push({
              'jam': hour + ':' + minutes  // moment(new Date(result[i])).format('HH:mm')
            })
          }
        }
        let listReservasi = []
        if (res.reservasi.length > 0) {
          listReservasi = res.reservasi
        }

        if (listReservasi.length > 0) {
          // for (let j = 0; j < listReservasi.length; j++) {
          // for (let i = 0; i < this.listJam.length; i++) {
          for (var j = listReservasi.length - 1; j >= 0; j--) {
            for (var i = this.listJam.length - 1; i >= 0; i--) {
              if (this.listJam[i].jam == listReservasi[j].jamreservasi) {
                // this.listJam.splice[i]
                this.listJam.splice([i], 1)
                // this.listJam[i].disable = false
              }
            }
          }
        }
        //reservasi hari ini
        let tglReservasiJam = this.formGroup.get('tglReservasi').value
        let nowss = moment(new Date()).format('YYYY-MM-DD');
        if (moment(new Date(tglReservasiJam._i.year, tglReservasiJam._i.month, tglReservasiJam._i.date)).format('YYYY-MM-DD') == nowss) {
          let hourReser = moment(new Date()).format('HH:mm')
          for (var i = this.listJam.length - 1; i >= 0; i--) {
            let stringJam = moment(new Date()).format('YYYY-MM-DD ' + this.listJam[i].jam)
            let stringRes = moment(new Date()).format('YYYY-MM-DD ' + hourReser)
            let dt = new Date(stringRes)
            dt.setHours(dt.getHours() + 1);
            if (new Date(stringJam) < dt) {
              // this.listJam.splice[i]
              this.listJam.splice([i], 1)
              // this.listJam[i].disable = false
            }
          }
        }
        if (this.listJam.length == 0)
          this.messageService.error('Maaf', 'Jadwal Reservasi Sudah Penuh, Coba dihari lain')
        else
          this.messageService.success('Info', 'Slot Reservasi Tersedia : ' + this.listJam.length + ' Slot')

      }, error => {
        this.listJam = []
        this.messageService.error('Maaf', 'Jadwal Reservasi Sudah Penuh, Coba dihari lain')
      })

  }
  intervals(startString, endString, interval) {
    var start = moment(startString, 'YYYY-MM-DD HH:mm');
    var end = moment(endString, 'YYYY-MM-DD HH:mm');

    // round starting minutes up to nearest 15 (12 --> 15, 17 --> 30)
    // note that 59 will round up to 60, and moment.js handles that correctly
    start.minutes(Math.ceil(start.minutes() / 15) * 15);

    var result = [];

    var current = moment(start);

    while (current <= end) {
      result.push(current.format('YYYY-MM-DD HH:mm'));
      current.add(interval, 'minutes');
    }

    return result;
  }
  getDaftarSlot() {

    let tglAwal = moment(this.formGroup.get('tglAwal').value).format('YYYY-MM-DD')
    let tglAkhir = moment(this.formGroup.get('tglAkhir').value).format('YYYY-MM-DD')

    this.httpService.get('reservasionline/get-slot-available?tglAwal=' + tglAwal
      + '&tglAkhir=' + tglAkhir).subscribe(res => {
        let jamBuka = res.tanggal + ' ' + res.slot.jambuka
        let jamTutup = res.tanggal + ' ' + res.slot.jamtutup
        let interval = res.slot.interval
        let quota = res.slot.quota
        this.listJam = [];
        let result = [];
        result = this.intervals(jamBuka, jamTutup, interval)

        if (result.length > 0) {
          for (let i = 0; i < quota; i++) {
            this.listJam.push({
              'jam': moment(new Date(result[i])).format('HH:mm')
            })
          }
        }
        let listReservasi = []
        if (res.reservasi.length > 0) {
          listReservasi = res.reservasi
        }

        if (listReservasi.length > 0) {
          for (let j = 0; j < listReservasi.length; j++) {
            for (let i = 0; i < this.listJam.length; i++) {
              if (this.listJam[i].jam == listReservasi[j].jamreservasi) {
                // this.listJam.splice[i]
                this.listJam.splice([i], 1)
                // this.listJam[i].disable = false
              }
            }
          }
        }
        if (this.listJam.length == 0)
          this.messageService.error('Maaf', 'Jadwal Reservasi Sudah Penuh, Coba dihari lain')
      }, error => {
        this.listJam = []
        this.messageService.error('Maaf', 'Jadwal Reservasi Sudah Penuh, Coba dihari lain')
      })

  }
  findPelayanan() {
    if (!this.formPelayanan.get('noRegistrasi').value) {
      this.messageService.warn('Warn', 'No Registrasi harus di isi')
      return
    }
    this.deposit = 0
    this.totalBayar = 0
    this.sisaTagihan = 0
    this.totalKlaim = 0
    this.totalTagihan = 0
    this.item = {}

    this.httpService.get('reservasionline/tagihan/get-pasien/' + this.formPelayanan.get('noRegistrasi').value).subscribe(res => {
      // this.httpService.get('reservasionline/get-data-pelayanan/' + this.formPelayanan.get('noRegistrasi').value).subscribe(e => {
      let e = res.data
      this.item.noRegistrasi = e.noregistrasi
      this.item.noCm = e.nocm
      this.item.namaPasien = e.namapasien.toUpperCase()
      this.item.ruangan = e.namaruangan
      this.item.kelasRawat = e.namakelas

      if (e.kelompokpasien.indexOf('BPJS') > -1)
        this.titleKlaim = 'Tanggungan BPJS/INA-CBGs'
      else
        this.titleKlaim = 'Total Klaim'
      let tglReg = new Date(e.tglregistrasi)
      let _hari = tglReg.getDay();
      let hariFix = this.listHari[_hari]
      e.tglregistrasi = hariFix + ', ' + moment(new Date(e.tglregistrasi)).format('DD-MMM-YYYY HH:mm') //HH:mm
      this.item.tglRegistrasi = e.tglregistrasi
      if (e.tglpulang == null) {
        e.tglpulang = '-'
      } else {
        let tglPlg = new Date(e.tglpulang)
        let _haris = tglPlg.getDay();
        let hariFixx = this.listHari[_haris]
        e.tglpulang = hariFixx + ', ' + moment(new Date(e.tglpulang)).format('DD-MMM-YYYY HH:mm') //HH:mm
      }

      this.item.tglPulang = e.tglpulang
      this.item.jenisPasien = e.kelompokpasien
      // if (e.jumlahBayar == null)
      //   e.jumlahBayar = 0
      // if (e.deposit == null)
      //   e.deposit = 0
      // if (e.billing == null)
      //   e.billing = 0
      // this.deposit = this.formatRupiah(e.deposit, 'Rp. ');
      // this.totalBayar = this.formatRupiah(e.jumlahBayar, 'Rp. ');

      // let sisa = parseFloat(e.billing) - e.jumlahBayar - parseFloat(e.deposit)
      // this.sisaTagihan = this.formatRupiah(sisa, 'Rp. ');
      // this.httpService.get('valet/terbilang/' + sisa).subscribe(res => {
      //   this.totalTerbilang = res.terbilang.toUpperCase()
      // })
    }, error => {
      // this.deposit = 0
      // this.totalBayar = 0
      // this.sisaTagihan = 0
      this.item = {}
    })
    this.dataSourceRincian = []
    this.httpService.get('reservasionline/get-tagihan-pasien/' + this.formPelayanan.get('noRegistrasi').value).subscribe(e => {
      this.totalKlaim = e.totalklaim
      this.deposit = e.deposit
      this.totalBayar = 0
      for (let i = 0; i < e.details.length; i++) {
        // if (e.details[i].sbmfk != null)
        //   this.totalBayar = this.totalBayar + e.details[i].total
        if (e.details[i].strukresepfk != null) {
          e.details[i].ruanganTindakan = 'Pemakaian Obat & Alkes ' + e.details[i].ruanganTindakan
        }
      }
      let sama = false
      let arrGroup = [];
      for (let i = 0; i < e.details.length; i++) {
        sama = false
        for (let x = 0; x < arrGroup.length; x++) {
          if (arrGroup[x].ruanganTindakan == e.details[i].ruanganTindakan) {
            arrGroup[x].total = parseFloat(arrGroup[x].total) + parseFloat(e.details[i].total)
            sama = true;
          }
        }
        if (sama == false) {
          let result = {
            ruanganTindakan: e.details[i].ruanganTindakan,
            total: e.details[i].total,

          }
          arrGroup.push(result)
        }
      }
      let totals = 0
      this.totalTagihan = 0
      for (let i = 0; i < arrGroup.length; i++) {
        const element = arrGroup[i];
        totals = element.total + totals
        this.totalTagihan = element.total + this.totalTagihan
        element.total = this.formatRupiah(element.total, 'Rp. ');
      }
      this.totalBayar = e.bayar
      // this.totalBayar = parseFloat(this.totalTagihan) - parseFloat(this.deposit)
      this.sisaTagihan = parseFloat(this.totalTagihan) - this.totalBayar - parseFloat(this.deposit) - this.totalKlaim

      this.httpService.get('sysadmin/general/get-terbilang/' + this.sisaTagihan).subscribe(res => {
        this.totalTerbilang = res.terbilang.toUpperCase()
      })
      this.totalTagihan = this.formatRupiah(this.totalTagihan, 'Rp. ');
      this.deposit = this.formatRupiah(this.deposit, 'Rp. ');
      this.totalBayar = this.formatRupiah(this.totalBayar, 'Rp. ');
      this.sisaTagihan = this.formatRupiah(this.sisaTagihan, 'Rp. ');
      this.totalKlaim = this.formatRupiah(this.totalKlaim, 'Rp. ');
      arrGroup.sort(this.compare);
      this.dataSourceRincian = arrGroup


      this.isShowRincian = false
      // this.updateRowGroupMetaData();
    }, error => {
      this.isShowRincian = true
      this.dataSourceRincian = []
      this.totalTerbilang = '-'
      this.deposit = 0
      this.totalBayar = 0
      this.sisaTagihan = 0
      this.messageService.info('Info', 'Data Tidak ditemukan')
    })
  }
  compare(a, b) {
    if (a.ruanganTindakan < b.ruanganTindakan) {
      return -1;
    }
    if (a.ruanganTindakan > b.ruanganTindakan) {
      return 1;
    }
    return 0;
  }


  formatRupiah(value, currency) {
    return currency + "" + parseFloat(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
  }
  onSort() {
    this.updateRowGroupMetaData();
  }

  updateRowGroupMetaData() {
    this.rowGroupMetadata = {};
    if (this.dataSourceRincian) {
      for (let i = 0; i < this.dataSourceRincian.length; i++) {
        let rowData = this.dataSourceRincian[i];
        let ruanganTindakan = rowData.ruanganTindakan;
        if (i == 0) {
          this.rowGroupMetadata[ruanganTindakan] = { index: 0, size: 1 };
        }
        else {
          let previousRowData = this.dataSourceRincian[i - 1];
          let previousRowGroup = previousRowData.brand;
          if (ruanganTindakan === previousRowGroup)
            this.rowGroupMetadata[ruanganTindakan].size++;
          else
            this.rowGroupMetadata[ruanganTindakan] = { index: i, size: 1 };
        }
      }
    }
  }
  getMaxJamReservasis() {
    let maxJam = 'maxJamReservasi'
    this.httpService.get('reservasionline/get-setting?namaField=' + maxJam)
      .subscribe(response => {
        this.maxJamReservasi = response
        console.log(response)
      }, error => {

      })
  }
  pilihNoregis(event) {
    this.selectedItemGridRiwayat = event
    this.formPelayanan.get('noRegistrasi').setValue(event.noregistrasi)
    this.isShowRiwayatRegis = false
    this.findPelayanan()
    this.dataSourceRm = []
  }
  tutupPopUpRm() {
    this.isShowRiwayatRegis = false

  }
  findRiwayatRegis() {
    if (!this.formPelayanan.get('noRm').value) {
      this.messageService.warn('Info', 'Masukan No Rekam Medis')
      return
    }
    this.dataSourceRm = []

    this.httpService.get('reservasionline/daftar-riwayat-registrasi?norm=' + this.formPelayanan.get('noRm').value)
      .subscribe(response => {
        for (let i = 0; i < response.daftar.length; i++) {
          const element = response.daftar[i];
          element.tglregistrasi = moment(new Date(element.tglregistrasi)).format('YYYY-MM-DD')
        }
        let datas = []
        if (response.daftar.length > 0) {
          datas = response.daftar
          datas.sort(this.compareRiwaayat)
        }
        this.dataSourceRm = datas
      }, error => {
        this.dataSourceRm = []
      })

  }
  compareRiwaayat(a, b) {
    if (a.tglregistrasi > b.tglregistrasi) {
      return -1;
    }
    if (a.tglregistrasi < b.tglregistrasi) {
      return 1;
    }
    return 0;
  }
  // copyNorek() {
  //   if (this.selectedNorek == undefined) {
  //     this.messageService.warn('Peringatan', 'Pilih data dulu')
  //     return
  //   }
  //   let selec = this.selectedNorek.bankaccountnomor
  //   selec.select()
  //   document.execCommand('copy');
  //   alert("Copied the text: " + selec.value);
  // }
  // copyInputMessage(inputElement) {
  //   inputElement.select();
  //   document.execCommand('copy');
  //   inputElement.setSelectionRange(0, 0);
  // }
  onRowSelectNoRek(event) {
    this.selectedNorek = event.data
  }
  clickPanduan() {
    this.isPanduanReservasi = !this.isPanduanReservasi
  }

  clearDokter() {
    this.items = {}
    this.loadDokter()
  }
  cariDokter() {
    this.loadDokter()
  }
  loadDokter() {
    var nama = ''
    if (this.items.namaDokter != undefined) {
      nama = this.items.namaDokter
    }
    var ruangn = ''
    if (this.items.namaPoli != undefined) {
      ruangn = this.items.namaPoli
    }
    this.listJadwal = []
    this.httpService.get('humas/get-daftar-jadwal-dokter?namaDokter=' + nama + '&namaRuangan=' + ruangn).subscribe(e => {
      this.listJadwal = []
      for (let i = 0; i < e.data.length; i++) {
        const element = e.data[i];
        if (element.objectjeniskelaminfk == 2) {
          element.image = 'assets/layout/images/female.png'
        } else {
          element.image = 'assets/layout/images/male.png'
        }
        this.listJadwal.push(element)
      }
    })
  }
}
export interface ImageViewerConfig {
  btnClass?: string;
  zoomFactor?: number;
  containerBackgroundColor?: string;
  wheelZoom?: boolean;
  allowFullscreen?: boolean;
  allowKeyboardNavigation?: boolean;

  btnShow?: {
    zoomIn?: boolean;
    zoomOut?: boolean;
    rotateClockwise?: boolean;
    rotateCounterClockwise?: boolean;
    next?: boolean;
    prev?: boolean;
  };

  btnIcons?: {
    zoomIn?: string;
    zoomOut?: string;
    rotateClockwise?: string;
    rotateCounterClockwise?: string;
    next?: string;
    prev?: string;
    fullscreen?: string;
  };

  customBtns?: Array<
    {
      name: string;
      icon: string;
    }
  >;
}

export class CustomEvent {
  name: string;
  imageIndex: number;

  constructor(name, imageIndex) {
    this.name = name;
    this.imageIndex = imageIndex;
  }

}