import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { VPesertaComponent } from './v-peserta.component';

const routes: Routes = [{ path: '', component: VPesertaComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class VPesertaRoutingModule { }
