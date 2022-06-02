import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { OrderLabRoutingModule } from './order-lab-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { OrderLabComponent } from './order-lab.component';


@NgModule({
  declarations: [OrderLabComponent],
  imports: [
    CommonModule,
    OrderLabRoutingModule,
    primeNgModule
  ]
})
export class OrderLabModule { }
