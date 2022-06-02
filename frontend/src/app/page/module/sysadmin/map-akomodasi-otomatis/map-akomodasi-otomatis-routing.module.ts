import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MapAkomodasiOtomatisComponent } from './map-akomodasi-otomatis.component';

const routes: Routes = [{ path: '', component: MapAkomodasiOtomatisComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MapAkomodasiOtomatisRoutingModule { }
