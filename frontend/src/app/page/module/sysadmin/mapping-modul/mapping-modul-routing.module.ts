import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MappingModulComponent } from './mapping-modul.component';

const routes: Routes = [
  { path: '', component: MappingModulComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MappingModulRoutingModule { }
