import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PindahPulangComponent } from './pindah-pulang.component';

const routes: Routes = [{ path: '', component: PindahPulangComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PindahPulangRoutingModule { }
