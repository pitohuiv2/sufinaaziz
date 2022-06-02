import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InfoKritikComponent } from './info-kritik.component';

const routes: Routes = [{ path: '', component: InfoKritikComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InfoKritikRoutingModule { }
