import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputProvinsiComponent } from './input-provinsi.component';


const routes: Routes = [{ path:'', component:InputProvinsiComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputProvinsiRoutingModule { }

