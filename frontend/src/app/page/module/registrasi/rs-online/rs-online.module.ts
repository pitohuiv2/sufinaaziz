import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RsOnlineRoutingModule } from './rs-online-routing.module';

import { RsOnlineComponent } from './rs-online.component';
import { primeNgModule } from 'src/app/shared/shared.module';
@NgModule({
  declarations: [
    RsOnlineComponent,
  ],
  imports: [
    CommonModule,
    RsOnlineRoutingModule,
    primeNgModule
  ]
})
export class RsOnlineModule { }
