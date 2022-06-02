import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SismadakComponent } from './sismadak.component';

const routes: Routes = [{ path: '', component: SismadakComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SismadakRoutingModule { }
