import { Component, OnInit } from '@angular/core';
import { ConfirmationService, MenuItem, MessageService, SelectItem, TreeNode } from 'primeng/api';
import { ApiService, AuthService } from 'src/app/service';
import * as moment from 'moment';
import { CacheService } from 'src/app/service/cache.service';
import { HelperService } from 'src/app/service/helperService';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-jadwal-dokter',
  templateUrl: './jadwal-dokter.component.html',
  styleUrls: ['./jadwal-dokter.component.scss'],
  providers: [ConfirmationService]
})
export class JadwalDokterComponent implements OnInit {
  item: any = {
    mapModul: [],
    mapRuangan: [],
    mapModul2: []
  }
  column: any[];
  dataSource: any[];
  pop_User: boolean = false;
  isSimpan: boolean = false;
  indexTab = 0;
  searchText = '';
  searchText2 = '';
  listRuangan: any[] = [];
  listDokter: any[] = [];
  listhari: any = [];
  selectedGrid: any;
  constructor(
    private apiService: ApiService,
    private authService: AuthService,
    private confirmationService: ConfirmationService,
    private messageService: MessageService,
    private router: Router,
    private DateHelper: HelperService,
    private cacheHelper: CacheService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {
    this.loadColumn();
    this.loadCombo();
    this.loadData();
  }

  loadCombo() {
    this.apiService.get('sysadmin/general/get-combo-jadwal-dokter').subscribe(e => {
      this.listRuangan = e.poli;
      this.listDokter = e.dokter;
      this.listhari = e.hari;
    })
  }

  loadColumn() {
    this.column = [
      { field: 'no', header: 'No', width: "60px" },
      { field: 'namalengkap', header: 'Nama Dokter', width: "200px" },
      { field: 'hari', header: 'Hari', width: "250px" },
      { field: 'jammulai', header: 'Jam Mulai', width: "150px" },
      { field: 'jamakhir', header: 'Jam Selesai', width: "150px" },
    ];
  }

  loadData() {
    var dokter = "";
    // if ($scope.item.dokter != undefined) {
    //   dokter = $scope.item.dokter.id
    // }
    var ruangan = "";
    // if ($scope.item.ruangan != undefined) {
    //   ruangan = $scope.item.ruangan.id
    // }
    this.apiService.get("sysadmin/general/get-data-informasi-jadwal-dokter?" + "&dokterId=" + dokter + "&ruanganId=" + ruangan).subscribe(dat => {      
      var datas = dat.data;
      for (let i = 0; i < datas.length; i++) {
        const element = datas[i];
        element.no = i + 1;
      } 
      this.dataSource = datas;
    })
  }

  tambah() {
    this.item.idjadwal = "";
    this.pop_User = true
    this.clearInputan()
  }

  clearInputan() {
    delete this.item.idjadwal
    delete this.item.dataRuangan
    delete this.item.selectedHari
    delete this.item.jammulai
    delete this.item.jamselesai
    delete this.item.dataDokter
    delete this.item.keterangan
  }

  simpanJadwal() {
    if (this.item.dataRuangan == undefined) {
      this.alertService.error("Peringatan!", "Ruangan Tidak Boleh Kosong!");
      return;
    }

    if (this.item.dataDokter == undefined) {
      this.alertService.error("Peringatan!", "Dokter Tidak Boleh Kosong!")
      return;
    }

    var listHari = '';
    if (this.item.selectedHari.length != 0) {
      var a = ""
      var b = ""
      for (var i = 0; i < this.item.selectedHari.length; i++) {
        if (i == this.item.selectedHari.length - 1) {
          listHari += this.item.selectedHari[i].hari
        } else {
          listHari += this.item.selectedHari[i].hari + ", "
        }
      }
    }

    var objSave = {
      "idjadwal": this.item.idjadwal,
      "objectpegawaifk": this.item.dataDokter != undefined ? this.item.dataDokter.id : null,
      "objectruanganfk": this.item.dataRuangan != undefined ? this.item.dataRuangan.id : null,
      "hari": listHari != undefined ? listHari : null,
      "jammulai": this.item.jammulai != undefined ? moment(this.item.jammulai).format('HH:mm') : null,
      "jamakhir": this.item.jamselesai != undefined ? moment(this.item.jamselesai).format('HH:mm') : null,
      "keterangan": this.item.keterangan != undefined ? this.item.keterangan : "",
    }
    this.isSimpan = true
    this.apiService.post('sysadmin/general/save-informasi-jadwal-dokter', objSave).subscribe(res => {
      this.isSimpan = false
      this.clearInputan();
      this.pop_User = false
      this.loadData();
    }, function (error) {
      this.isSimpan = true
    })
  }

  editJadwal(e){
    var dataItem = e;
    this.item.idjadwal = dataItem.idjadwalpegawai;
    this.item.dataDokter = { id: dataItem.pegawaiidfk, namalengkap: dataItem.namalengkap };
    this.item.dataRuangan = { id: dataItem.ruanganidfk, namaruangan: dataItem.namaruangan };
    this.item.jammulai  = dataItem.jammulai;
    this.item.jamselesai  = dataItem.jamakhir;
    this.item.keterangan = dataItem.keterangan;
    if (dataItem.hari) {
      this.item.selectedHari =[]
      let split = dataItem.hari.split(',')
      if(split.length>0){
        for (var i = 0; i < split.length; i++) {
          const elem =	split[i]
          for (var z = 0; z < this.listhari.length; z++) {
            const elem2 = this.listhari[z]
            if(elem.indexOf(elem2.hari) > -1){
              this.item.selectedHari.push(elem2)
              break
            }
          }
        }
      }
    }
    this.pop_User = true
  }

  hapusjadwal(e){
    var dataItem = e;
    var objSave = {
      idJadwal : dataItem.idjadwalpegawai,
      objectpegawaifk : dataItem.pegawaiidfk
    }    
    this.apiService.post('sysadmin/general/hapus-informasi-jadwal-dokter', objSave).subscribe( dat => {      
      this.loadData();
    });
  }
  cari() {
    this.loadData();
  }

}
