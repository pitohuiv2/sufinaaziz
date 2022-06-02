import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DetailTagihanRoutingModule } from './detail-tagihan-routing.module';
import { DetailTagihanComponent } from './detail-tagihan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    DetailTagihanComponent
  ],
  imports: [
    CommonModule,
    DetailTagihanRoutingModule,
    primeNgModule
  ],

})
export class DetailTagihanModule { }
