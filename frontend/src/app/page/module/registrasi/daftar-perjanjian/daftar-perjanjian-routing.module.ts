import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarPerjanjianComponent } from './daftar-perjanjian.component';

const routes: Routes = [{ path: '', component: DaftarPerjanjianComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarPerjanjianRoutingModule { }
