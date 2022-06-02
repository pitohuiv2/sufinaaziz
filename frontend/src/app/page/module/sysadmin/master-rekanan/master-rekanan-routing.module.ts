import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MasterRekananComponent } from './master-rekanan.component';
const routes: Routes = [
  { path: '', component: MasterRekananComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MasterRekananRoutingModule { }
