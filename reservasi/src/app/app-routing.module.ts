import { RouterModule } from '@angular/router';
import { NgModule } from '@angular/core';
import { AppMainComponent } from './app.main.component';

import { AuthGuard, LoginGuard } from './guard';
import { AccessDeniedComponent, DashboardComponent, LoginComponent, NotFoundComponent } from './page';

@NgModule({
    imports: [
        RouterModule.forRoot([
            { canActivate: [LoginGuard], path: 'login', redirectTo: 'reservasi'},
            {
                path: '', component: AppMainComponent,
                children: [
                    { canActivate: [AuthGuard], path: '', component: DashboardComponent },
                    {
                        path: 'reservasi',
                        loadChildren: () => import('./page/module/reservasi/reservasi.module').then(m => m.ReservasiModule)
                    },
                    //Registrasi
                    {
                        canActivate: [AuthGuard], path: 'pasien-lama',
                        loadChildren: () => import('./page/module/registrasi/pasien-lama/pasien-lama.module').then(m => m.PasienLamaModule)
                    },
                    {
                        canActivate: [AuthGuard], path: 'pasien-baru/:noRec/:idPasien/:departemen',
                        loadChildren: () => import('./page/module/registrasi/pasien-baru/pasien-baru.module').then(m => m.PasienBaruModule)
                    },
                    {
                        canActivate: [AuthGuard], path: 'daftar-registrasi-pasien',
                        loadChildren: () => import('./page/module/registrasi/daftar-registrasi-pasien/daftar-registrasi-pasien.module').then(m => m.DaftarRegistrasiPasienModule)
                    },
                    {
                        canActivate: [AuthGuard], path: 'registrasi-ruangan/:id',
                        loadChildren: () => import('./page/module/registrasi/registrasi-ruangan/registrasi-ruangan.module').then(m => m.RegistrasiRuanganModule)
                    },

                    { canActivate: [AuthGuard], path: 'not-found', component: NotFoundComponent },
                    { canActivate: [AuthGuard], path: 'access-denied', component: AccessDeniedComponent },
                    // kalo gak ada page not found
                    { path: '**', redirectTo: 'not-found' }
                ]
            },

            //     /*
            //     * demo comp
            //     */
            //     children: [
            //         { path: '', component: DashboardDemoComponent },
            //         { path: 'uikit/formlayout', component: FormLayoutDemoComponent },
            //         { path: 'uikit/floatlabel', component: FloatLabelDemoComponent },
            //         { path: 'uikit/invalidstate', component: InvalidStateDemoComponent },
            //         { path: 'uikit/input', component: InputDemoComponent },
            //         { path: 'uikit/button', component: ButtonDemoComponent },
            //         { path: 'uikit/table', component: TableDemoComponent },
            //         { path: 'uikit/list', component: ListDemoComponent },
            //         { path: 'uikit/tree', component: TreeDemoComponent },
            //         { path: 'uikit/panel', component: PanelsDemoComponent },
            //         { path: 'uikit/overlay', component: OverlaysDemoComponent },
            //         { path: 'uikit/menu', component: MenusDemoComponent },
            //         { path: 'uikit/media', component: MediaDemoComponent },
            //         { path: 'uikit/message', component: MessagesDemoComponent },
            //         { path: 'uikit/misc', component: MiscDemoComponent },
            //         { path: 'uikit/charts', component: ChartsDemoComponent },
            //         { path: 'uikit/file', component: FileDemoComponent },
            //         { path: 'utilities/display', component: DisplayComponent },
            //         { path: 'utilities/elevation', component: ElevationComponent },
            //         { path: 'utilities/flexbox', component: FlexboxComponent },
            //         { path: 'utilities/grid', component: GridComponent },
            //         { path: 'utilities/icons', component: IconsComponent },
            //         { path: 'utilities/widgets', component: WidgetsComponent },
            //         { path: 'utilities/spacing', component: SpacingComponent },
            //         { path: 'utilities/typography', component: TypographyComponent },
            //         { path: 'utilities/text', component: TextComponent },
            //         { path: 'pages/crud', component: AppCrudComponent },
            //         { path: 'pages/calendar', component: AppCalendarComponent },
            //         { path: 'pages/timeline', component: AppTimelineDemoComponent },
            //         { path: 'pages/invoice', component: AppInvoiceComponent },
            //         { path: 'pages/help', component: AppHelpComponent },
            //         { path: 'pages/empty', component: EmptyDemoComponent },
            //         { path: 'documentation', component: DocumentationComponent }
            //     ]
            // },
            // { canActivate: [AuthGuard], path: 'not-found', component: NotFoundComponent },
            // { canActivate: [AuthGuard], path: 'access-denied', component: AccessDeniedComponent },
            // // kalo gak ada page not found
            // { path: '**', redirectTo: 'not-found' }
            // // {path: 'error', component: AppErrorComponent},
            // // {path: 'access', component: AppAccessdeniedComponent},
            // // {path: 'notfound', component: AppNotfoundComponent},
            // // {path: 'login', component: AppLoginComponent},
            // // {path: '**', redirectTo: '/notfound'},


        ], { scrollPositionRestoration: 'enabled' })
    ],
    exports: [RouterModule]
})
export class AppRoutingModule {
}
