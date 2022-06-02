import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LaporanKunjunganComponent } from './laporan-kunjungan.component';

const routes: Routes = [
  { path: '', component: LaporanKunjunganComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LaporanKunjunganRoutingModule { }
