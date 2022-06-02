import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { HasilLaboratoriumRevRoutingModule } from './hasil-laboratorium-rev-routing.module';
import { HasilLaboratoriumRevComponent } from './hasil-laboratorium-rev.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [HasilLaboratoriumRevComponent],
  imports: [
    CommonModule,
    HasilLaboratoriumRevRoutingModule,
    primeNgModule
  ]
})
export class HasilLaboratoriumRevModule { }
