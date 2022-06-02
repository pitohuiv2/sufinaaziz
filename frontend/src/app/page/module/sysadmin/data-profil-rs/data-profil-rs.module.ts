import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { DataProfilRsRoutingModule } from './data-profil-rs-routing.module';
import { DataProfilRsComponent } from './data-profil-rs.component';
import { primeNgModule } from 'src/app/shared/shared.module';

@NgModule({
  declarations: [
    DataProfilRsComponent
  ],
  imports: [
    CommonModule,
    DataProfilRsRoutingModule,
    primeNgModule
  ]
})
export class DataProfilRsModule { }
