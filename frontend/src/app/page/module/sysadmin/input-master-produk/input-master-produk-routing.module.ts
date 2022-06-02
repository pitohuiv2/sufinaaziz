import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputMasterProdukComponent } from "./input-master-produk.component";

const routes: Routes = [
  { path: '', component: InputMasterProdukComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputMasterProdukRoutingModule { }
