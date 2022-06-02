import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputKabKotaComponent } from './input-kab-kota.component';


const routes: Routes = [{ path:'', component:InputKabKotaComponent}];


@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputKabKotaRoutingModule { }
