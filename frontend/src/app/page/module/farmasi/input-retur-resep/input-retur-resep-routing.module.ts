import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputReturResepComponent } from './input-retur-resep.component'

const routes: Routes = [
  { path: '', component: InputReturResepComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputReturResepRoutingModule { }
