import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { JadwalDokterComponent } from './jadwal-dokter.component';

const routes: Routes = [{ path: '', component: JadwalDokterComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class JadwalDokterRoutingModule { }
