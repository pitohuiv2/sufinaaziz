import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarKirimMenuRoutingModule } from './daftar-kirim-menu-routing.module';
import { DaftarKirimMenuComponent } from './daftar-kirim-menu.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DaftarKirimMenuComponent
  ],
  imports: [
    CommonModule,
    DaftarKirimMenuRoutingModule,
    primeNgModule
  ]
})
export class DaftarKirimMenuModule { }
