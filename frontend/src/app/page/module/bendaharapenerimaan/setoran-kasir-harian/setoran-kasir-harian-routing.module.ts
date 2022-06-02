import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { SetoranKasirHarianComponent } from "./setoran-kasir-harian.component";

const routes: Routes = [{ path: '', component: SetoranKasirHarianComponent }];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class SetoranKasirHarianRoutingModule { }
