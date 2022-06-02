import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarSurveyComponent } from './daftar-survey.component';

const routes: Routes = [{ path: '', component: DaftarSurveyComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarSurveyRoutingModule { }
