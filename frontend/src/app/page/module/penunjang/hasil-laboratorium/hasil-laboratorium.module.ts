import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { HasilLaboratoriumRoutingModule } from './hasil-laboratorium-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { HasilLaboratoriumComponent} from './hasil-laboratorium.component';



@NgModule({
  declarations: [
    HasilLaboratoriumComponent
  ],
  imports: [
    CommonModule,
    HasilLaboratoriumRoutingModule,
    primeNgModule
  ],
  providers:[]
})
export class HasilLaboratoriumModule { }
