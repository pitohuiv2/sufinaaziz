import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { KlaimInacbgRoutingModule } from './klaim-inacbg-routing.module';
import { KlaimInacbgComponent } from './klaim-inacbg.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    KlaimInacbgComponent
  ],
  imports: [
    CommonModule,
    KlaimInacbgRoutingModule,
    primeNgModule
  ]
})
export class KlaimInacbgModule { }
