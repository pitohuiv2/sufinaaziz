import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { AntrolJadwalDokterRoutingModule } from './antrol-jadwal-dokter-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { AntrolJadwalDokterComponent } from './antrol-jadwal-dokter.component';


@NgModule({
  declarations: [
    AntrolJadwalDokterComponent
  ],
  imports: [
    CommonModule,
    AntrolJadwalDokterRoutingModule,
    primeNgModule
  ]
})
export class AntrolJadwalDokterModule { }
