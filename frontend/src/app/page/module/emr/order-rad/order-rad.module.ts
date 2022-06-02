import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { OrderRadRoutingModule } from './order-rad-routing.module';
import { OrderRadComponent } from './order-rad.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [OrderRadComponent],
  imports: [
    CommonModule,
    OrderRadRoutingModule,
    primeNgModule
  ]
})
export class OrderRadModule { }
