import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InfoKritikRoutingModule } from './info-kritik-routing.module';

import { InfoKritikComponent } from './info-kritik.component';
import { primeNgModule } from '../../../shared/shared.module';


@NgModule({
  declarations: [InfoKritikComponent],
  imports: [
    CommonModule,
    InfoKritikRoutingModule,
    primeNgModule
  ]
})
export class InfoKritikModule { }
