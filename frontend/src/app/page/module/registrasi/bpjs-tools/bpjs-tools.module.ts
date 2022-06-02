import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { BpjsToolsRoutingModule } from './bpjs-tools-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { BpjsToolsComponent } from './bpjs-tools.component';


@NgModule({
  declarations: [BpjsToolsComponent],
  imports: [
    CommonModule,
    BpjsToolsRoutingModule,
    primeNgModule
  ]
})
export class BpjsToolsModule { }
