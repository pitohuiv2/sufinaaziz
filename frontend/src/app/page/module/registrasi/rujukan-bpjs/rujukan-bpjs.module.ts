import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RujukanBpjsRoutingModule } from './rujukan-bpjs-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { RujukanBpjsComponent } from './rujukan-bpjs.component';


@NgModule({
  declarations: [RujukanBpjsComponent],
  imports: [
    CommonModule,
    RujukanBpjsRoutingModule,
    primeNgModule
  ]
})
export class RujukanBpjsModule { }
