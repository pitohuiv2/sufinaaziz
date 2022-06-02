import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PindahPulangRoutingModule } from './pindah-pulang-routing.module';
import { PindahPulangComponent } from './pindah-pulang.component';
import { primeNgModule } from 'src/app/shared/shared.module';
// import { HeadPasienComponent } from 'src/app/page/template/head-pasien/head-pasien.component';


@NgModule({
  declarations: [
    // HeadPasienComponent,
    PindahPulangComponent,
    
  ],
  imports: [
    CommonModule,
    PindahPulangRoutingModule,
    primeNgModule
  ],
  providers:[
    // HeadPasienComponent,
  ]
})
export class PindahPulangModule { }
