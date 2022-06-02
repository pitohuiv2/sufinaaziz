import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PasienLamaComponent } from './pasien-lama.component';

const routes: Routes = [
  { path: '', component: PasienLamaComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PasienLamaRoutingModule { }
