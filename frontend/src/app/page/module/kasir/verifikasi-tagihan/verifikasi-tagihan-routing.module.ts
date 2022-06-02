import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { VerifikasiTagihanComponent } from './verifikasi-tagihan.component';

const routes: Routes = [  { path: '', component: VerifikasiTagihanComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VerifikasiTagihanRoutingModule { }
