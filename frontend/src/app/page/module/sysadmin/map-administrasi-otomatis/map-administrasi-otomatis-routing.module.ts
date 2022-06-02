import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MapAdministrasiOtomatisComponent } from './map-administrasi-otomatis.component';

const routes: Routes = [
  { path: '', component: MapAdministrasiOtomatisComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MapAdministrasiOtomatisRoutingModule { }
