import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SiklusMenuRoutingModule } from './siklus-menu-routing.module';
import { SiklusMenuComponent } from './siklus-menu.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    SiklusMenuComponent
  ],
  imports: [
    CommonModule,
    SiklusMenuRoutingModule,
    primeNgModule
  ]
})
export class SiklusMenuModule { }
