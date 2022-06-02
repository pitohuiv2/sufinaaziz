import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { RsOnlineComponent } from './rs-online.component';

const routes: Routes = [{ path:'',component:RsOnlineComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RsOnlineRoutingModule { }
