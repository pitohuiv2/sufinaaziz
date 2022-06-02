import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { OrderLabComponent } from './order-lab.component';

const routes: Routes = [{ path: '', component: OrderLabComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class OrderLabRoutingModule { }
