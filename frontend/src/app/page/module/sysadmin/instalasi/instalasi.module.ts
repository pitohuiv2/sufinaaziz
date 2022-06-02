import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { InstalasiRoutingModule } from './instalasi-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';//nambah ieu
import { InstalasiComponent } from './instalasi.component';//nambah ieu

@NgModule({
  declarations: [
  InstalasiComponent
  ],
  imports: [
    CommonModule,
    InstalasiRoutingModule,
    primeNgModule
  ]
})
export class InstalasiModule { }
