import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MasterRekananComponent } from './master-rekanan.component';
import { MasterRekananRoutingModule } from './master-rekanan-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    MasterRekananComponent
  ],
  imports: [
    CommonModule,
    MasterRekananRoutingModule,
    primeNgModule
  ]
})
export class MasterRekananModule { }
