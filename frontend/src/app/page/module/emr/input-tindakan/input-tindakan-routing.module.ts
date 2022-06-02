import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { InputTindakanComponent } from './input-tindakan.component';

const routes: Routes = [  
  { path: '', component: InputTindakanComponent },
];


@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class InputTindakanRoutingModule { }
