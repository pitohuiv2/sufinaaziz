import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SettingDataComponent } from './setting-data.component';

const routes: Routes = [{path: '',component:SettingDataComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SettingDataRoutingModule { }
