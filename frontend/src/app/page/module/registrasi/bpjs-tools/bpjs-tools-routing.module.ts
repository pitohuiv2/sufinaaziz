import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { BpjsToolsComponent } from './bpjs-tools.component';
import { BpjsToolsModule } from './bpjs-tools.module';

const routes: Routes = [{
  path: '',
  component: BpjsToolsComponent,
  children: [
    { path: 'v-peserta', loadChildren: () => import('../bpjs/v-peserta/v-peserta.module').then(m => m.VPesertaModule) },
    { path: 'v-referensi', loadChildren: () => import('../bpjs/v-referensi/v-referensi.module').then(m => m.VReferensiModule) },
    { path: 'v-sep', loadChildren: () => import('../bpjs/v-sep/v-sep.module').then(m => m.VSepModule) },
    { path: 'v-rujukan', loadChildren: () => import('../bpjs/v-rujukan/v-rujukan.module').then(m => m.VRujukanModule) },
    { path: 'v-rencana-kontrol', loadChildren: () => import('../bpjs/v-rencana-kontrol/v-rencana-kontrol.module').then(m => m.VRencanaKontrolModule) },
    { path: 'v-monitoring', loadChildren: () => import('../bpjs/v-monitoring/v-monitoring.module').then(m => m.VMonitoringModule) },
    { path: 'v-lpk', loadChildren: () => import('../bpjs/v-lpk/v-lpk.module').then(m => m.VLpkModule) },
    { path: 'v-prb', loadChildren: () => import('../bpjs/v-prb/v-prb.module').then(m => m.VPrbModule) },
    { path: 'v-bed', loadChildren: () => import('../bpjs/v-bed/v-bed.module').then(m => m.VBedModule) },


    { path: 'antrol-jadwal-dokter', loadChildren: () => import('../bpjs/antrol-jadwal-dokter/antrol-jadwal-dokter.module').then(m => m.AntrolJadwalDokterModule) },
    { path: 'antrol-antrean', loadChildren: () => import('../bpjs/antrol-antrean/antrol-antrean.module').then(m => m.AntrolAntreanModule) },
    { path: 'antrol-dashboard', loadChildren: () => import('../bpjs/antrol-dashboard/antrol-dashboard.module').then(m => m.AntrolDashboardModule) },

  ]
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class BpjsToolsRoutingModule { }
