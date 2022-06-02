import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { RujukanBpjsComponent } from './rujukan-bpjs.component';

const routes: Routes = [{ path:'',component:RujukanBpjsComponent}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RujukanBpjsRoutingModule { }
