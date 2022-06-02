import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { OrderBedahRoutingModule } from './order-bedah-routing.module';
import { OrderBedahComponent } from './order-bedah.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    OrderBedahComponent
  ],
  imports: [
    CommonModule,
    OrderBedahRoutingModule,
    primeNgModule
  ]
})
export class OrderBedahModule { }
