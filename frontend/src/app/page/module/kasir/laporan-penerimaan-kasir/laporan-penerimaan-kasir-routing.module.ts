import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { LaporanPenerimaanKasirComponent } from './laporan-penerimaan-kasir.component';

const routes: Routes = [{ path: '', component: LaporanPenerimaanKasirComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LaporanPenerimaanKasirRoutingModule { }
