import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { SettingDataRoutingModule } from './setting-data-routing.module';
import { primeNgModule } from '../../../../shared/shared.module';
import { SettingDataComponent } from './setting-data.component';


@NgModule({
  declarations: [SettingDataComponent],
  imports: [
    CommonModule,
    SettingDataRoutingModule,
    primeNgModule
  ]
})
export class SettingDataModule { }
