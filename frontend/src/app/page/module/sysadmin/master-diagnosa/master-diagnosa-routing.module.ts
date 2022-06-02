import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MasterDiagnosaComponent } from './master-diagnosa.component';

const routes: Routes = [{ path: '', component: MasterDiagnosaComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MasterDiagnosaRoutingModule { }
