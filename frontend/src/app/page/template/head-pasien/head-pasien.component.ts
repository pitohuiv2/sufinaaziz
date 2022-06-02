import { Component, Injectable, OnInit, ViewChild } from '@angular/core';

@Component({
  selector: 'app-head-pasien',
  templateUrl: './head-pasien.component.html',
  styleUrls: ['./head-pasien.component.scss']
})
export class HeadPasienComponent implements OnInit {
  item: any = {
    pasien: {}
  }
  numberss= Array(15).map((x,i)=>i);
  constructor() { }

  ngOnInit(): void {

  }

}
