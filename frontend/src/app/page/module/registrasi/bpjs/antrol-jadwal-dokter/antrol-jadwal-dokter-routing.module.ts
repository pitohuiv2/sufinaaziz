import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AntrolJadwalDokterComponent } from './antrol-jadwal-dokter.component';

const routes: Routes = [{path:'',component:AntrolJadwalDokterComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AntrolJadwalDokterRoutingModule { }
