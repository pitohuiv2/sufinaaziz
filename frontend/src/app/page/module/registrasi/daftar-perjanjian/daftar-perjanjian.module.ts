import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarPerjanjianRoutingModule } from './daftar-perjanjian-routing.module';
import { DaftarPerjanjianComponent } from './daftar-perjanjian.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [DaftarPerjanjianComponent],
  imports: [
    CommonModule,
    DaftarPerjanjianRoutingModule,
    primeNgModule
  ]
})
export class DaftarPerjanjianModule { }
