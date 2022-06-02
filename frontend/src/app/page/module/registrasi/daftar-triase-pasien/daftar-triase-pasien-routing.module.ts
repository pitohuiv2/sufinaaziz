import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarTriasePasienComponent } from './daftar-triase-pasien.component';

const routes: Routes = [  { path: '', component: DaftarTriasePasienComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarTriasePasienRoutingModule { }
