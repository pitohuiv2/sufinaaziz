import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { OrderResepComponent } from './order-resep.component';

const routes: Routes = [
  { path: '', component: OrderResepComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})

export class OrderResepRoutingModule { }
