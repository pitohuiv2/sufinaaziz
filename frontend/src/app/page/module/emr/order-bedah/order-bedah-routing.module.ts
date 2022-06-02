import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { OrderBedahComponent } from './order-bedah.component';

const routes: Routes = [
  {
    path: '', component: OrderBedahComponent,
    children: [
      { path: 'pengkajian-bedah-detail/:namaEMR/:nomorEMR', loadChildren: () => import('../pengkajian-bedah-detail/pengkajian-bedah-detail.module').then(m => m.PengkajianBedahDetailModule) }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class OrderBedahRoutingModule { }
