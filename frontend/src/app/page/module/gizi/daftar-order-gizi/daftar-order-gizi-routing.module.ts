import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarOrderGiziComponent } from './daftar-order-gizi.component';

const routes: Routes = [{
  path: '', component: DaftarOrderGiziComponent
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarOrderGiziRoutingModule { }
