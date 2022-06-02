import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SettingLoginUserRoutingModule } from './setting-login-user-routing.module';
import { SettingLoginUserComponent } from './setting-login-user.component';
import { primeNgModule } from 'src/app/shared/shared.module';


@NgModule({
  declarations: [
    SettingLoginUserComponent
  ],
  imports: [
    CommonModule,
    SettingLoginUserRoutingModule,
    primeNgModule
  ]
})
export class SettingLoginUserModule { }
