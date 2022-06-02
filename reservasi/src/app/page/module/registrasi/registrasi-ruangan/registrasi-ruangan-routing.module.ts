import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { RegistrasiRuanganComponent } from './registrasi-ruangan.component';

const routes: Routes = [
  { path: '', component: RegistrasiRuanganComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RegistrasiRuanganRoutingModule { }
