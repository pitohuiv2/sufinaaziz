import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PengkajianBedahDetailRoutingModule } from './pengkajian-bedah-detail-routing.module';
import { PengkajianBedahDetailComponent } from './pengkajian-bedah-detail.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    PengkajianBedahDetailComponent
  ],
  imports: [
    CommonModule,
    PengkajianBedahDetailRoutingModule,
    primeNgModule
  ]
})
export class PengkajianBedahDetailModule { }
