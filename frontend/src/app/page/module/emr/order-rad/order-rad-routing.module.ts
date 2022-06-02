import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { OrderRadComponent } from './order-rad.component';

const routes: Routes = [{ path: '', component: OrderRadComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class OrderRadRoutingModule { }
