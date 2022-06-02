import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarTriasePasienRoutingModule } from './daftar-triase-pasien-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarTriasePasienComponent} from './daftar-triase-pasien.component';

@NgModule({
  declarations: [
    DaftarTriasePasienComponent
  ],
  imports: [
    CommonModule,
    DaftarTriasePasienRoutingModule,
    primeNgModule
  ]
})
export class DaftarTriasePasienModule { }
