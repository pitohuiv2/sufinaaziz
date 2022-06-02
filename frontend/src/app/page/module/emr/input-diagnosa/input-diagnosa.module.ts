import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputDiagnosaRoutingModule } from './input-diagnosa-routing.module';
import { InputDiagnosaComponent } from './input-diagnosa.component';
import { primeNgModule } from 'src/app/shared/shared.module';
// import { HeadPasienComponent } from 'src/app/page/template/head-pasien/head-pasien.component';


@NgModule({
  declarations: [
    // HeadPasienComponent,    
    InputDiagnosaComponent
  ],
  imports: [
    CommonModule,
    InputDiagnosaRoutingModule,
    primeNgModule,
  ],
  exports:[],
  providers:[]

})
export class InputDiagnosaModule { }
