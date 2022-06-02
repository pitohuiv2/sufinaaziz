import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InstalasiRuanganRoutingModule } from './instalasi-ruangan-routing.module';
import { InstalasiRuanganComponent } from './instalasi-ruangan.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
  InstalasiRuanganComponent
  ],
  imports: [
    CommonModule,
    InstalasiRuanganRoutingModule,
    primeNgModule
  ]
})
export class InstalasiRuanganModule { }
