import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { VRencanaKontrolComponent } from './v-rencana-kontrol.component';

const routes: Routes = [{path:'',component:VRencanaKontrolComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VRencanaKontrolRoutingModule { }
