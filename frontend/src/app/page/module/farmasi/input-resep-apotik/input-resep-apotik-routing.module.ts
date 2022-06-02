import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputResepApotikComponent } from './input-resep-apotik.component'

const routes: Routes = [{ path: '', component: InputResepApotikComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputResepApotikRoutingModule { }
