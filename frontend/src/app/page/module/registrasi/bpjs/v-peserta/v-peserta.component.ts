import { Component, OnInit } from '@angular/core';
import * as moment from 'moment';
import { ApiService, AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
declare var Prism;
@Component({
  selector: 'app-v-peserta',
  templateUrl: './v-peserta.component.html',
  styleUrls: ['./v-peserta.component.scss']
})
export class VPesertaComponent implements OnInit {
  item: any = {}
  jsonResult: any
  myCode: any
  htmlContent: string = `
<pre><code class=\"language-typescript\" pCode ngNonBindable ngPreserveWhitespaces>##</pre></code>`

  private highlighted: boolean = false

  listTipe: any[] = [{ name: 'No Kartu', id: '1' }, { name: 'NIK', id: '2' }];
  constructor(
    private apiService: ApiService,
    private alertService: AlertService,
  ) { }

  ngOnInit(): void {


    this.item.tipe = this.listTipe[0]
  }

  cari() {
    let param = '';
    if (this.item.tipe.id == 1) {
      param = 'nokartu'
    } else {
      param = 'nik'
    }
    let json = {
      "url": "Peserta/" + param + "/" + this.item.noka + "/tglSEP/" + moment(new Date()).format('YYYY-MM-DD'),
      "method": "GET",
      "data": null
    }
    this.apiService.postNonMessage("bridging/bpjs/tools", json).subscribe(e => {
      if (e.metaData.code == "200") {
        this.alertService.success('Info', e.metaData.message);
      } else {
        this.alertService.error('Info', e.metaData.message);
      }
      this.jsonResult = JSON.stringify(e, undefined, 4);


    })
  }

}
