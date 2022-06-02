import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputAlkesRuanganComponent } from './input-alkes-ruangan.component';

const routes: Routes = [
  { path: '', component: InputAlkesRuanganComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputAlkesRuanganRoutingModule { }
