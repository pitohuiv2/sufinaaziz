import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DetailTagihanComponent } from './detail-tagihan.component';

const routes: Routes = [{ path: '', component: DetailTagihanComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DetailTagihanRoutingModule { }
