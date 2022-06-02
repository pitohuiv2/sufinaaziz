import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { OrderResepRoutingModule } from './order-resep-routing.module';
import { OrderResepComponent } from './order-resep.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    OrderResepComponent
  ],
  imports: [
    CommonModule,
    OrderResepRoutingModule,
    primeNgModule
  ]
})
export class OrderResepModule { }
