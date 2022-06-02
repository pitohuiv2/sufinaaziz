import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RincianPenunjangRoutingModule } from './rincian-penunjang-routing.module';
import { RincianPenunjangComponent } from './rincian-penunjang.component';
import { primeNgModule } from 'src/app/shared/shared.module';
// import { HeadPasienComponent } from 'src/app/page/template/head-pasien/head-pasien.component';

@NgModule({
  declarations: [
    // HeadPasienComponent,    
    RincianPenunjangComponent
  ],
  imports: [
    CommonModule,
    RincianPenunjangRoutingModule,
    primeNgModule
  ],
  providers:[]
})
export class RincianPenunjangModule { }
