import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CheckinRoutingModule } from './checkin-routing.module';
import { CheckinComponent } from './checkin.component';
import { primeNgModule } from 'src/app/shared/shared.module';
import { MatButtonModule } from '@angular/material/button';
import { MatKeyboardModule } from 'angular-onscreen-material-keyboard';
;


@NgModule({
  declarations: [
    CheckinComponent
  ],
  imports: [
    CommonModule,
    CheckinRoutingModule,
    primeNgModule,
    MatButtonModule,
    MatKeyboardModule,
  ],
})
export class CheckinModule { }
