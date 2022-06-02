import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AntrolAntreanComponent } from './antrol-antrean.component';

const routes: Routes = [{ path: '', component: AntrolAntreanComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AntrolAntreanRoutingModule { }
