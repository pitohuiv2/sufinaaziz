import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InstalasiComponent } from './instalasi.component';

const routes: Routes = [
{ path: '', component: InstalasiComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InstalasiRoutingModule { }
