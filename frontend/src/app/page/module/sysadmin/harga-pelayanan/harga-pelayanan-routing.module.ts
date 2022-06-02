import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HargaPelayananComponent } from './harga-pelayanan.component';

const routes: Routes = [{ path: '', component: HargaPelayananComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class HargaPelayananRoutingModule { }
