import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarSoComponent } from './daftar-so.component';

const routes: Routes = [{ path: '', component: DaftarSoComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarSoRoutingModule { }
