import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SiklusMenuComponent } from './siklus-menu.component';

const routes: Routes = [{
  path: '', component: SiklusMenuComponent
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SiklusMenuRoutingModule { }
