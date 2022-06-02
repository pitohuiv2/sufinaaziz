import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { RekamMedisComponent } from './rekam-medis.component';

const routes: Routes = [
  {
    path: '', component: RekamMedisComponent,
    children: [
        { path: 'order-lab', loadChildren: () => import('../order-lab/order-lab.module').then(m => m.OrderLabModule) },
        { path: 'order-rad', loadChildren: () => import('../order-rad/order-rad.module').then(m => m.OrderRadModule) },
        { path: 'order-bedah', loadChildren: () => import('../order-bedah/order-bedah.module').then(m => m.OrderBedahModule) },
        { path: 'riwayat-registrasi', loadChildren: () => import('../riwayat-registrasi/riwayat-registrasi.module').then(m => m.RiwayatRegistrasiModule) },
        { path: 'input-tindakan', loadChildren: () => import('../input-tindakan/input-tindakan.module').then(m => m.InputTindakanModule) },
        { path: 'input-diagnosa', loadChildren: () => import('../input-diagnosa/input-diagnosa.module').then(m => m.InputDiagnosaModule) },
        { path: 'konsultasi-dokter', loadChildren: () => import('../konsultasi-dokter/konsultasi-dokter.module').then(m => m.KonsultasiDokterModule) },
        { path: 'emr-detail', loadChildren: () => import('../emr-detail/emr-detail.module').then(m => m.EmrDetailModule) },
        { path: 'cppt', loadChildren: () => import('../cppt/cppt.module').then(m => m.CpptModule) },
        { path: 'order-resep', loadChildren: () => import('../order-resep/order-resep.module').then(m => m.OrderResepModule) },
        { path: 'rencana-kontrol', loadChildren: () => import('../rencana-kontrol/rencana-kontrol.module').then(m => m.RencanaKontrolModule) },
        { path: 'input-alkes-ruangan', loadChildren: () => import('../input-alkes-ruangan/input-alkes-ruangan.module').then(m => m.InputAlkesRuanganModule) },
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class RekamMedisRoutingModule { }
