import { Component, OnInit } from '@angular/core';
import { ApiService } from '../../service/api.service';
import { Config } from '../../guard/config';
import { SelectItem } from 'primeng/api';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import { ProductService } from 'src/app/demo/service/productservice';
import { EventService } from 'src/app/demo/service/eventservice';
import { Product } from 'src/app/demo/domain/product';
import { AuthService } from 'src/app/service';
import { AlertService } from 'src/app/service/component/alert/alert.service';
import * as moment from 'moment';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss']
})
export class DashboardComponent implements OnInit {

  color2 = ["#FF6384",
    "#4BC0C0",
    "#FFCE56",
    "#E7E9ED",
    "#36A2EB", '#7cb5ec', '#75b2a3', '#9ebfcc', '#acdda8', '#d7f4d2', '#ccf2e8',
    '#468499', '#088da5', '#00ced1', '#3399ff', '#00ff7f',
    '#b4eeb4', '#a0db8e', '#999999', '#6897bb', '#0099cc', '#3b5998',
    '#000080', '#191970', '#8a2be2', '#31698a', '#87ff8a', '#49e334',
    '#13ec30', '#7faf7a', '#408055', '#09790e']
  color3 = ["#fcec03",
    "#03fc98",
    "#FFCE56",
    "#E7E9ED",
    "#36A2EB", '#7cb5ec', '#75b2a3', '#9ebfcc', '#acdda8', '#d7f4d2', '#ccf2e8',
    '#468499', '#088da5', '#00ced1', '#3399ff', '#00ff7f',
    '#b4eeb4', '#a0db8e', '#999999', '#6897bb', '#0099cc', '#3b5998',
    '#000080', '#191970', '#8a2be2', '#31698a', '#87ff8a', '#49e334',
    '#13ec30', '#7faf7a', '#408055', '#09790e']
  color4 = ['#7cb5ec', '#75b2a3', '#9ebfcc', '#acdda8', '#d7f4d2', '#ccf2e8',
    '#468499', '#088da5', '#00ced1', '#3399ff', '#00ff7f',
    '#b4eeb4', '#a0db8e', '#999999', '#6897bb', '#0099cc', '#3b5998',
    '#000080', '#191970', '#8a2be2', '#31698a', '#87ff8a', '#49e334',
    '#13ec30', '#7faf7a', '#408055', '#09790e'];
  listTipe = [
    { label: 'Bar', value: 'bar' },
    { label: 'Line', value: 'line' },
    { label: 'Polar', value: 'polarArea' },
    { label: 'Pie', value: 'pie' },
    { label: 'Donut', value: 'doughnut' },
    { label: 'Radar', value: 'radar' },
  ];
  item: any = {
    dari: new Date(),
    sampai: new Date(),
  }
  namaLengkap: ''
  chartTrend: any
  dataChartTrend: any
  options = {
    // responsive: false,
    maintainAspectRatio: false,
    scaleLabel:
      function (label) { return '$' + label.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); }
  };

  mapOptions: any
  mapOverlays: any[];


  dialogVisible: boolean;

  markerTitle: string;

  selectedPosition: any;

  infoWindow: any;

  draggable: boolean;
  listKatUmur: any[] = []
  dataChartJenisPasien: any
  chartJenisPasien: any
  dataChartDiagnosa: any
  chartDiagnosa: any
  dataChartRajal: any
  chartRajal: any
  chartRanapJK: any
  dataChartRanapJK: any
  chartDokter:any
  dataChartDokter:any
  dataBorLosToi:any={}
  constructor(
    private apiService: ApiService,
    private productService: ProductService,
    private eventService: EventService,
    private auth: AuthService,
    private alert: AlertService
  ) {
    this.item.chartTipeTrend = this.listTipe[1]
  }

  ngOnInit() {
    this.item.chartTipeTrend = this.listTipe[1]
    // this.refresh()
    this.namaLengkap = this.auth.getDataLoginUser().pegawai.namaLengkap
  }
  refresh() {
 
    this.apiService.get("eis/get-data-dashboard?dari=" + moment(this.item.dari).format('YYYY-MM-DD') + "&sampai=" + moment(this.item.sampai).format('YYYY-MM-DD')).subscribe(data => {
      this.dataChartTrend = data.trendkunjungan
      this.listKatUmur = data.tt_usia
      this.dataChartJenisPasien = data.kunjungan_perjenispasien
      this.dataChartDiagnosa = data.topdiagnosa
      this.dataChartRajal = data.toprajal
      this.dataChartRanapJK = data.ranapjk
     
      this.dataBorLosToi = data.borlostoi[0]
      this.getTrend()
      this.getChartJenisPasien()
      this.getChartDiagnosa()
      this.getChartRajal()
      this.getChartRanapJK()
    })
    // this.getSebaranAlamat()
  }
  getChartRajal() {
    let categori = []
    let data = []
    for (let i = 0; i < this.dataChartRajal.length; i++) {
      const element = this.dataChartRajal[i];
      categori.push(element.name)
      data.push(element.count)
    }
    this.chartRajal =
    {
      labels: categori,
      datasets:
        [
          {
            data: data,
            backgroundColor: this.color4,
            hoverBackgroundColor: this.color4
          }]
    }
  }
  getChartRanapJK() {
    let categori = []
    let data = []
    for (let i = 0; i < this.dataChartRanapJK.length; i++) {
      const element = this.dataChartRanapJK[i];
      categori.push(element.name)
      data.push(element.count)
    }
    this.chartRanapJK =
    {
      labels: categori,
      datasets:
        [
          {
            data: data,
            backgroundColor: this.color3,
            hoverBackgroundColor: this.color3
          }
        ]
    }
  }
  getChartJenisPasien() {
    let categori = []
    let data = []
    for (let i = 0; i < this.dataChartJenisPasien.length; i++) {
      const element = this.dataChartJenisPasien[i];
      categori.push(element.name)
      data.push(element.count)
    }
    this.chartJenisPasien =
    {
      labels: categori,
      datasets:
        [
          {
            data: data,
            backgroundColor: this.color2,
            hoverBackgroundColor: this.color2
          }]
    }

  }
  getChartDiagnosa() {
    let categori = []
    let data = []
    for (let i = 0; i < this.dataChartDiagnosa.length; i++) {
      const element = this.dataChartDiagnosa[i];
      categori.push(element.name)
      data.push(element.count)
    }
    this.chartDiagnosa =
    {
      labels: categori,
      datasets:
        [
          {
            label: 'Diagnosis',
            data: data,
            backgroundColor: this.color2,
            hoverBackgroundColor: this.color2
          }]
    }

  }
  changeChart(e) {
    this.getTrend()
  }
  getTrend() {
    // debugger
    let categori = this.dataChartTrend.labels
    let data = [{
      fill: false,
      borderDash: []
    }, {
      fill: false,
      borderDash: [5, 5],
    }, {
      fill: true,
      borderDash: []
    }]
    let title = 'Pengunjung Rawat Jalan'
    // for (let i = 0; i < this.dataChartTrend.length; i++) {
    //   const element = this.dataChartTrend[i];
    //   categori.push(element.tglstring)
    //   data.push(element.jml)
    // }
    if (this.item.chartTipeTrend.value == 'bar') {
      var dataset = this.dataChartTrend.datasets
      for (let i = 0; i < dataset.length; i++) {
        const element = dataset[i];
        element.backgroundColor = this.color2[i]
        element.borderColor = this.color2[i]

      }
      this.chartTrend = {
        labels: categori,
        datasets: this.dataChartTrend.datasets
        // [
        //   {
        //     label: title,
        //     backgroundColor: '#4BC0C0',
        //     borderColor: '#1E88E5',
        //     data: data
        //   }
        // ],
      }
    }
    if (this.item.chartTipeTrend.value == 'line') {
      var dataset = this.dataChartTrend.datasets
      for (let i = 0; i < dataset.length; i++) {
        const element = dataset[i];
        // element.backgroundColor = 'transparent'
        element.borderColor = this.color2[i]
        element.fill = data[i].fill
        element.borderDash = data[i].borderDash
      }
      this.chartTrend = {
        labels: categori,
        datasets: this.dataChartTrend.datasets,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: false,
              text: ''
            }
          }
        },
      }
    }
    if (this.item.chartTipeTrend.value == 'radar') {
      var dataset = this.dataChartTrend.datasets
      for (let i = 0; i < dataset.length; i++) {
        const element = dataset[i];
        element.backgroundColor = this.color2[i]
        element.borderColor = this.color2[i]
      }
      this.chartTrend = {
        labels: categori,
        datasets: this.dataChartTrend.datasets
      }
    }
    if (this.item.chartTipeTrend.value == 'polarArea') {
      var dataset = this.dataChartTrend.datasets
      for (let i = 0; i < dataset.length; i++) {
        const element = dataset[i];
        element.backgroundColor = this.color2
      }
      this.chartTrend =
      {
        datasets: dataset,
        //  [{
        //   data: data,
        //   backgroundColor: this.color2,
        //   label: 'My dataset'
        // }],
        labels: categori

      }
    }
    if (this.item.chartTipeTrend.value == 'pie') {
      var dataset = this.dataChartTrend.datasets
      for (let i = 0; i < dataset.length; i++) {
        const element = dataset[i];
        element.backgroundColor = this.color2
        element.hoverBackgroundColor = this.color2
      }
      this.chartTrend =
      {
        labels: categori,
        datasets: dataset,
        // [
        //   {
        //     data: data,
        //     backgroundColor: this.color2,
        //     hoverBackgroundColor: this.color2
        //   }]

      }
    }
    if (this.item.chartTipeTrend.value == 'doughnut') {
      var dataset = this.dataChartTrend.datasets
      for (let i = 0; i < dataset.length; i++) {
        const element = dataset[i];
        element.backgroundColor = this.color2
        element.hoverBackgroundColor = this.color2
      }
      this.chartTrend =
      {
        labels: categori,
        datasets: dataset
        //  [
        //   {
        //     data: data,
        //     backgroundColor: this.color2,
        //     hoverBackgroundColor: this.color2,
        //   }]

      }
    }
  }
  // getSebaranAlamat() {
  //   this.mapOptions = {
  //     center: { lat: 36.890257, lng: 30.707417 },
  //     zoom: 12
  //   };

  //   this.initOverlays();

  //   this.infoWindow = new google.maps.InfoWindow();
  // }
  // handleMapClick(event) {
  //   this.dialogVisible = true;
  //   this.selectedPosition = event.latLng;
  // }

  // handleOverlayClick(event) {
  //   let isMarker = event.overlay.getTitle != undefined;

  //   if (isMarker) {
  //     let title = event.overlay.getTitle();
  //     this.infoWindow.setContent('' + title + '');
  //     this.infoWindow.open(event.map, event.overlay);
  //     event.map.setCenter(event.overlay.getPosition());

  //     this.alert.info(title, 'Marker Selected');
  //   }
  //   else {
  //     this.alert.info('', 'Shape Selected');
  //   }
  // }

  // addMarker() {
  //   this.mapOverlays.push(new google.maps.Marker({ position: { lat: this.selectedPosition.lat(), lng: this.selectedPosition.lng() }, title: this.markerTitle, draggable: this.draggable }));
  //   this.markerTitle = null;
  //   this.dialogVisible = false;
  // }

  // handleDragEnd(event) {
  //   this.alert.info(event.overlay.getTitle(), 'Marker Selected');
  // }

  // initOverlays() {
  //   if (!this.mapOverlays || !this.mapOverlays.length) {
  //     this.mapOverlays = [
  //       new google.maps.Marker({ position: { lat: 36.879466, lng: 30.667648 }, title: "Konyaalti" }),
  //       new google.maps.Marker({ position: { lat: 36.883707, lng: 30.689216 }, title: "Ataturk Park" }),
  //       new google.maps.Marker({ position: { lat: 36.885233, lng: 30.702323 }, title: "Oldtown" }),
  //       new google.maps.Polygon({
  //         paths: [
  //           { lat: 36.9177, lng: 30.7854 }, { lat: 36.8851, lng: 30.7802 }, { lat: 36.8829, lng: 30.8111 }, { lat: 36.9177, lng: 30.8159 }
  //         ], strokeOpacity: 0.5, strokeWeight: 1, fillColor: '#1976D2', fillOpacity: 0.35
  //       }),
  //       new google.maps.Circle({ center: { lat: 36.90707, lng: 30.56533 }, fillColor: '#1976D2', fillOpacity: 0.35, strokeWeight: 1, radius: 1500 }),
  //       new google.maps.Polyline({ path: [{ lat: 36.86149, lng: 30.63743 }, { lat: 36.86341, lng: 30.72463 }], geodesic: true, strokeColor: '#FF0000', strokeOpacity: 0.5, strokeWeight: 2 })
  //     ];
  //   }
  // }

  // zoomIn(map) {
  //   map.setZoom(map.getZoom() + 1);
  // }

  // zoomOut(map) {
  //   map.setZoom(map.getZoom() - 1);
  // }

  // clear() {
  //   this.mapOverlays = [];
  // }
}
