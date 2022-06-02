import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SettingLoginUserComponent } from './setting-login-user.component';

const routes: Routes = [{ path:'', component:SettingLoginUserComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SettingLoginUserRoutingModule { }
