import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MasterProdukComponent } from './master-produk.component';

const routes: Routes = [{ path: '', component: MasterProdukComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MasterProdukRoutingModule { }
