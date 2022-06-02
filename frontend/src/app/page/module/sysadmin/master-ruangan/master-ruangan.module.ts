import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { MasterRuanganRoutingModule } from './master-ruangan-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { MasterRuanganComponent } from './master-ruangan.component';


@NgModule({
  declarations: [MasterRuanganComponent],
  imports: [
    CommonModule,
    MasterRuanganRoutingModule,
    primeNgModule
  ]
})
export class MasterRuanganModule { }
