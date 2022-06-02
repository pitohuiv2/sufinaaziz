import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { MasterRuanganComponent } from './master-ruangan.component';

const routes: Routes = [{ path: '', component: MasterRuanganComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class MasterRuanganRoutingModule { }
