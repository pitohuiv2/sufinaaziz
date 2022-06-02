import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PenyetoranDepositComponent } from './penyetoran-deposit.component';

const routes: Routes = [  { path: '', component: PenyetoranDepositComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PenyetoranDepositRoutingModule { }
