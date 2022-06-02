import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { KonsultasiDokterComponent } from './konsultasi-dokter.component';

const routes: Routes = [{ path: '', component: KonsultasiDokterComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class KonsultasiDokterRoutingModule { }
