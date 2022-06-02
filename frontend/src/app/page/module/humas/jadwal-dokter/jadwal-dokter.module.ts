import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { JadwalDokterComponent } from './jadwal-dokter.component';
import { JadwalDokterRoutingModule } from './jadwal-dokter-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    JadwalDokterComponent
  ],
  imports: [
    CommonModule,
    JadwalDokterRoutingModule,
    primeNgModule
  ]
})
export class JadwalDokterModule { }
