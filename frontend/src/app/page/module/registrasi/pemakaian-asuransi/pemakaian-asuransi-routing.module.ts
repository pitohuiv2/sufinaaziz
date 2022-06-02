import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PemakaianAsuransiComponent } from './pemakaian-asuransi.component';

const routes: Routes = [
  { path: '', component: PemakaianAsuransiComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PemakaianAsuransiRoutingModule { }
