import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BedOnlineRoutingModule } from './bed-online-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { BedOnlineComponent } from './bed-online.component';


@NgModule({
  declarations: [
    BedOnlineComponent
  ],
  imports: [
    CommonModule,
    BedOnlineRoutingModule,
    primeNgModule
  ]
})
export class BedOnlineModule { }
