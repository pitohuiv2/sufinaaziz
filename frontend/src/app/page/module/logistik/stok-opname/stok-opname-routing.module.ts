import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { StokOpnameComponent } from './stok-opname.component';

const routes: Routes = [
  {
    path: '', component: StokOpnameComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class StokOpnameRoutingModule { }
