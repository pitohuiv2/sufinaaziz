import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DaftarSurveyRoutingModule } from './daftar-survey-routing.module';
import { primeNgModule } from 'src/app/shared/shared.module';
import { DaftarSurveyComponent } from './daftar-survey.component';


@NgModule({
  declarations: [
    DaftarSurveyComponent
  ],
  imports: [
    CommonModule,
    DaftarSurveyRoutingModule,
    primeNgModule
  ]
})
export class DaftarSurveyModule { }
