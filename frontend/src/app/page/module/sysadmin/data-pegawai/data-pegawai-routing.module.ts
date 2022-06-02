import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DataPegawaiComponent } from './data-pegawai.component';
const routes: Routes = [{ path: '', component: DataPegawaiComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DataPegawaiRoutingModule { }
