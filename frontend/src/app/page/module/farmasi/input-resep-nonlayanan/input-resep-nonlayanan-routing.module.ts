import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputResepNonlayananComponent } from './input-resep-nonlayanan.component';

const routes: Routes = [
  { path: '', component: InputResepNonlayananComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputResepNonlayananRoutingModule { }
