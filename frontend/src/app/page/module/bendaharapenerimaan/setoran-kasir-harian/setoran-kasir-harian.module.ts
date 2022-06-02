import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SetoranKasirHarianRoutingModule } from './setoran-kasir-harian-routing.module';
import { SetoranKasirHarianComponent } from "./setoran-kasir-harian.component";
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    SetoranKasirHarianComponent
  ],
  imports: [
    CommonModule,
    SetoranKasirHarianRoutingModule,
    primeNgModule
  ]
})
export class SetoranKasirHarianModule { }
