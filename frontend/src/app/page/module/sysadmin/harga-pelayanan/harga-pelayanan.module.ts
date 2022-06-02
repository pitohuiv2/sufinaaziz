import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { HargaPelayananRoutingModule } from './harga-pelayanan-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { HargaPelayananComponent } from './harga-pelayanan.component';
import { NgxMaskModule } from 'ngx-mask';


@NgModule({
  declarations: [
    HargaPelayananComponent
  ],
  imports: [
    CommonModule,
    HargaPelayananRoutingModule,
    primeNgModule,
    NgxMaskModule.forRoot(),
  ]
})
export class HargaPelayananModule { }
