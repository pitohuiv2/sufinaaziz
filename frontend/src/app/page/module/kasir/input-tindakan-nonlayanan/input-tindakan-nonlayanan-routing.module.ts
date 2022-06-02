import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputTindakanNonlayananComponent } from './input-tindakan-nonlayanan.component';

const routes: Routes = [
  { path: '', component: InputTindakanNonlayananComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputTindakanNonlayananRoutingModule { }
