import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ReservasiComponent } from './reservasi.component';

const routes: Routes = [
  { path: '', component: ReservasiComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ReservasiRoutingModule { }
