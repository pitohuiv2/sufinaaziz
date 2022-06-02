import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InputTindakanRoutingModule } from './input-tindakan-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { InputTindakanComponent } from './input-tindakan.component';


@NgModule({
  declarations: [
    InputTindakanComponent,

  ],
  imports: [
    CommonModule,
    InputTindakanRoutingModule,
    primeNgModule,
    // HeadPasienComponent
  ],
  exports:[],
  providers:[]

})
export class InputTindakanModule { }
