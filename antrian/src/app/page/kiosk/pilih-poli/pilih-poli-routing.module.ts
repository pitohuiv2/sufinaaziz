import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PilihPoliComponent } from './pilih-poli.component';

const routes: Routes = [
  { path: '', component: PilihPoliComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PilihPoliRoutingModule { }
