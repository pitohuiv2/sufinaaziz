import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { KlaimInacbgComponent } from './klaim-inacbg.component';

const routes: Routes = [{
  path: '', component: KlaimInacbgComponent
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class KlaimInacbgRoutingModule { }
