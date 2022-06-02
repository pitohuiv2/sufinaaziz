import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DaftarSetoranKasirComponent } from "./daftar-setoran-kasir.component";

const routes: Routes = [
  { path: '', component: DaftarSetoranKasirComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DaftarSetoranKasirRoutingModule { }
