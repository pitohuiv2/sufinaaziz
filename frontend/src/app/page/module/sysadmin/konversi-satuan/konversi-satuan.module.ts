import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { primeNgModule } from 'src/app/shared/shared.module';
import { KonversiSatuanComponent } from './konversi-satuan.component';
import { KonversiSatuanRoutingModule } from './konversi-satuan-routing.module';
import { NgxMaskModule } from 'ngx-mask';

@NgModule({
  declarations: [
    KonversiSatuanComponent
  ],
  imports: [
    CommonModule,
    KonversiSatuanRoutingModule,
    NgxMaskModule.forRoot(),
    primeNgModule
  ]
})
export class KonversiSatuanModule { }
