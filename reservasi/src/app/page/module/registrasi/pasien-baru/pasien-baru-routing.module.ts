import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PasienBaruComponent } from './pasien-baru.component';

const routes: Routes = [
  { path: '', component: PasienBaruComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PasienBaruRoutingModule { }
