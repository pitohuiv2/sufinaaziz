import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PengkajianBedahDetailComponent } from './pengkajian-bedah-detail.component';

const routes: Routes = [
  { path: '', component: PengkajianBedahDetailComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PengkajianBedahDetailRoutingModule { }
