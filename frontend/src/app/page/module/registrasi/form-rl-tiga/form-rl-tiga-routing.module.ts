import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { FormRlTigaComponent } from './form-rl-tiga.component';

const routes: Routes = [{ path: '', component: FormRlTigaComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class FormRlTigaRoutingModule { }
