import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PenyetoranDepositRoutingModule } from './penyetoran-deposit-routing.module';
import { PenyetoranDepositComponent } from './penyetoran-deposit.component';
import { primeNgModule } from 'src/app/shared/shared.module';
// import { HeadPasienComponent } from 'src/app/page/template/head-pasien/head-pasien.component';


@NgModule({
  declarations: [
    // HeadPasienComponent,
    PenyetoranDepositComponent,
  ],
  imports: [
    CommonModule,
    PenyetoranDepositRoutingModule,
    primeNgModule
  ],
  // providers:[
  //   HeadPasienComponent,
  // ]
})
export class PenyetoranDepositModule { }
