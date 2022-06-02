import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HasilLaboratoriumComponent } from './hasil-laboratorium.component';

const routes: Routes = [  { path: '', component: HasilLaboratoriumComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class HasilLaboratoriumRoutingModule { }
