import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DataProfilRsComponent } from './data-profil-rs.component';

const routes: Routes = [
  { path: '', component: DataProfilRsComponent }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DataProfilRsRoutingModule { }
