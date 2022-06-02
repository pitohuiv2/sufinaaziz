import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MapPaketComponent } from './map-paket.component';

const routes: Routes = [{path:'',component:MapPaketComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MapPaketRoutingModule { }
