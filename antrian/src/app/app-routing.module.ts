import { RouterModule } from '@angular/router';
import { NgModule } from '@angular/core';
import { AppMainComponent } from './app.main.component';

import { AuthGuard, LoginGuard } from './guard';
import { AccessDeniedComponent, DashboardComponent, LoginComponent, NotFoundComponent } from './page';

@NgModule({
    imports: [
        RouterModule.forRoot([
            { canActivate: [LoginGuard], path: 'login', redirectTo: 'antrian' },
            {
                path: '', component: AppMainComponent,
                children: [
                    { path: 'antrian', redirectTo: 'touchscreen' },
                    { path: 'touchscreen', loadChildren: () => import('./page/kiosk/touchscreen/touchscreen.module').then(m => m.TouchscreenModule) },
                    { path: 'touchscreen/self-regis', loadChildren: () => import('./page/kiosk/self-regis/self-regis.module').then(m => m.SelfRegisModule) },
                    { path: 'touchscreen/self-regis/verif-pasien', loadChildren: () => import('./page/kiosk/verifikasi-pasien/verifikasi-pasien.module').then(m => m.VerifikasiPasienModule) },
                    {
                        path: 'touchscreen/self-regis/verif-pasien-bpjs',
                        loadChildren: () => import('./page/kiosk/verifikasi-pasien-bpjs/verifikasi-pasien-bpjs.module').then(m => m.VerifikasiPasienBpjsModule)
                    },
                    {
                        path: 'touchscreen/self-regis/verif-pasien/poli',
                        loadChildren: () => import('./page/kiosk/pilih-poli/pilih-poli.module').then(m => m.PilihPoliModule)
                    },
                    {
                        path: 'touchscreen/checkin',
                        loadChildren: () => import('./page/kiosk/checkin/checkin.module').then(m => m.CheckinModule)
                    },
                    {
                        path: 'info-kritik',
                        loadChildren: () => import('./page/kiosk/info-kritik/info-kritik.module').then(m => m.InfoKritikModule)
                    },
                    { canActivate: [AuthGuard], path: 'not-found', component: NotFoundComponent },
                    { canActivate: [AuthGuard], path: 'access-denied', component: AccessDeniedComponent },

                    { path: '**', redirectTo: 'not-found' }
                ]
            },

        ], { scrollPositionRestoration: 'enabled' })
    ],
    exports: [RouterModule]
})
export class AppRoutingModule {
}
