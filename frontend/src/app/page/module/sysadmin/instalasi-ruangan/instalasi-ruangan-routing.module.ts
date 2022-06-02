import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InstalasiRuanganComponent } from './instalasi-ruangan.component';

const routes: Routes = [{ path:'', component:InstalasiRuanganComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InstalasiRuanganRoutingModule { }
