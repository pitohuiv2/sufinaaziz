import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { RencanaKontrolComponent } from './rencana-kontrol.component';

const routes: Routes = [{ path: '', component: RencanaKontrolComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RencanaKontrolRoutingModule { }
