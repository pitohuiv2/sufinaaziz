import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { BedOnlineComponent } from './bed-online.component';

const routes: Routes = [{ path: '', component: BedOnlineComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BedOnlineRoutingModule { }
