import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { VRujukanComponent } from './v-rujukan.component';

const routes: Routes = [{ path: '', component: VRujukanComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VRujukanRoutingModule { }
