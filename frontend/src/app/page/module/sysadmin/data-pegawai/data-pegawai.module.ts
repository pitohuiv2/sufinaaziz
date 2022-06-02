import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { DataPegawaiComponent } from './data-pegawai.component';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DataPegawaiRoutingModule } from './data-pegawai-routing.module';


@NgModule({
  declarations: [
    DataPegawaiComponent
  ],
  imports: [
    CommonModule,
    DataPegawaiRoutingModule,
    primeNgModule
  ]
})
export class DataPegawaiModule { }
