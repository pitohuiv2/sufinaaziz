import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarKirimMenuComponent } from './daftar-kirim-menu.component';

const routes: Routes = [
  {
    path: '', component: DaftarKirimMenuComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarKirimMenuRoutingModule { }
