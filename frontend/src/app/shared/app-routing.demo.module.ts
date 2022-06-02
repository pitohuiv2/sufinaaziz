// import { RouterModule } from '@angular/router';
// import { NgModule } from '@angular/core';
// // import { DashboardDemoComponent } from './demo/view/dashboarddemo.component';
// // import { FormLayoutDemoComponent } from './demo/view/formlayoutdemo.component';
// // import { PanelsDemoComponent } from './demo/view/panelsdemo.component';
// // import { OverlaysDemoComponent } from './demo/view/overlaysdemo.component';
// // import { MediaDemoComponent } from './demo/view/mediademo.component';
// // import { MenusDemoComponent } from './demo/view/menusdemo.component';
// // import { MessagesDemoComponent } from './demo/view/messagesdemo.component';
// // import { MiscDemoComponent } from './demo/view/miscdemo.component';
// // import { EmptyDemoComponent } from './demo/view/emptydemo.component';
// // import { ChartsDemoComponent } from './demo/view/chartsdemo.component';
// // import { FileDemoComponent } from './demo/view/filedemo.component';
// // import { DocumentationComponent } from './demo/view/documentation.component';
// import { AppMainComponent } from './app.main.component';
// // import { AppNotfoundComponent } from './demo/pages/app.notfound.component';
// // import { AppErrorComponent } from './demo/pages/app.error.component';
// // import { AppAccessdeniedComponent } from './demo/pages/app.accessdenied.component';
// // import { AppLoginComponent } from './demo/pages/app.login.component';
// // import { InputDemoComponent } from './demo/view/inputdemo.component';
// // import { FloatLabelDemoComponent } from './demo/view/floatlabeldemo.component';
// // import { InvalidStateDemoComponent } from './demo/view/invalidstatedemo.component';
// // import { ButtonDemoComponent } from './demo/view/buttondemo.component';
// // import { TableDemoComponent } from './demo/view/tabledemo.component';
// // import { ListDemoComponent } from './demo/view/listdemo.component';
// // import { TreeDemoComponent } from './demo/view/treedemo.component';
// // import { DisplayComponent } from './utilities/display.component';
// // import { ElevationComponent } from './utilities/elevation.component';
// // import { FlexboxComponent } from './utilities/flexbox.component';
// // import { GridComponent } from './utilities/grid.component';
// // import { IconsComponent } from './utilities/icons.component';
// // import { WidgetsComponent } from './utilities/widgets.component';
// // import { SpacingComponent } from './utilities/spacing.component';
// // import { TypographyComponent } from './utilities/typography.component';
// // import { TextComponent } from './utilities/text.component';
// // import { AppCrudComponent } from './demo/pages/app.crud.component';
// // import { AppCalendarComponent } from './demo/pages/app.calendar.component';
// // import { AppTimelineDemoComponent } from './demo/pages/app.timelinedemo.component';
// // import { AppInvoiceComponent } from './demo/pages/app.invoice.component';
// // import { AppHelpComponent } from './demo/pages/app.help.component';
// import { AuthGuard, LoginGuard } from './guard';
// import { AccessDeniedComponent, DashboardComponent, LoginComponent, NotFoundComponent } from './page';

// @NgModule({
//     imports: [
//         RouterModule.forRoot([
//             { canActivate: [LoginGuard], path: 'login', component: LoginComponent },
//             {
//                 path: '', component: AppMainComponent,
//                 children: [
//                     { canActivate: [AuthGuard], path: '', component: DashboardComponent },
//                     { canActivate: [AuthGuard], path: 'pasien-lama', loadChildren: () => import('./page/module/registrasi/pasien-lama/pasien-lama.module').then(m => m.PasienLamaModule) },
//                     { canActivate: [AuthGuard], path: 'pasien-baru/:noRec/:idPasien/:departemen', loadChildren: () => import('./page/module/registrasi/pasien-baru/pasien-baru.module').then(m => m.PasienBaruModule) },
//                     { canActivate: [AuthGuard], path: 'daftar-registrasi-pasien', loadChildren: () => import('./page/module/registrasi/daftar-registrasi-pasien/daftar-registrasi-pasien.module').then(m => m.DaftarRegistrasiPasienModule) },
//                     { canActivate: [AuthGuard], path: 'registrasi-ruangan/:id', loadChildren: () => import('./page/module/registrasi/registrasi-ruangan/registrasi-ruangan.module').then(m => m.RegistrasiRuanganModule) },
//                     { canActivate: [AuthGuard], path: 'detail-registrasi-pasien', loadChildren: () => import('./page/module/kasir/detail-registrasi-pasien/detail-registrasi-pasien.module').then(m => m.DetailRegistrasiPasienModule) },
//                     { canActivate: [AuthGuard], path: 'daftar-pasien-pulang', loadChildren: () => import('./page/module/kasir/daftar-pasien-pulang/daftar-pasien-pulang.module').then(m => m.DaftarPasienPulangModule) },
//                     { canActivate: [AuthGuard], path: 'daftar-piutang-pasien', loadChildren: () => import('./page/module/kasir/daftar-piutang-pasien/daftar-piutang-pasien.module').then(m => m.DaftarPiutangPasienModule) },
//                     { canActivate: [AuthGuard], path: 'daftar-pasien-dirawat', loadChildren: () => import('./page/module/kasir/daftar-pasien-dirawat/daftar-pasien-dirawat.module').then(m => m.DaftarPasienDirawatModule) },
//                     { canActivate: [AuthGuard], path: 'not-found', component: NotFoundComponent },
//                     { canActivate: [AuthGuard], path: 'access-denied', component: AccessDeniedComponent },
//                     // kalo gak ada page not found
//                     { path: '**', redirectTo: 'not-found' }
//                 ]
//             },

//             //     /*
//             //     * demo comp
//             //     */
//             //     children: [
//             //         { path: '', component: DashboardDemoComponent },
//             //         { path: 'uikit/formlayout', component: FormLayoutDemoComponent },
//             //         { path: 'uikit/floatlabel', component: FloatLabelDemoComponent },
//             //         { path: 'uikit/invalidstate', component: InvalidStateDemoComponent },
//             //         { path: 'uikit/input', component: InputDemoComponent },
//             //         { path: 'uikit/button', component: ButtonDemoComponent },
//             //         { path: 'uikit/table', component: TableDemoComponent },
//             //         { path: 'uikit/list', component: ListDemoComponent },
//             //         { path: 'uikit/tree', component: TreeDemoComponent },
//             //         { path: 'uikit/panel', component: PanelsDemoComponent },
//             //         { path: 'uikit/overlay', component: OverlaysDemoComponent },
//             //         { path: 'uikit/menu', component: MenusDemoComponent },
//             //         { path: 'uikit/media', component: MediaDemoComponent },
//             //         { path: 'uikit/message', component: MessagesDemoComponent },
//             //         { path: 'uikit/misc', component: MiscDemoComponent },
//             //         { path: 'uikit/charts', component: ChartsDemoComponent },
//             //         { path: 'uikit/file', component: FileDemoComponent },
//             //         { path: 'utilities/display', component: DisplayComponent },
//             //         { path: 'utilities/elevation', component: ElevationComponent },
//             //         { path: 'utilities/flexbox', component: FlexboxComponent },
//             //         { path: 'utilities/grid', component: GridComponent },
//             //         { path: 'utilities/icons', component: IconsComponent },
//             //         { path: 'utilities/widgets', component: WidgetsComponent },
//             //         { path: 'utilities/spacing', component: SpacingComponent },
//             //         { path: 'utilities/typography', component: TypographyComponent },
//             //         { path: 'utilities/text', component: TextComponent },
//             //         { path: 'pages/crud', component: AppCrudComponent },
//             //         { path: 'pages/calendar', component: AppCalendarComponent },
//             //         { path: 'pages/timeline', component: AppTimelineDemoComponent },
//             //         { path: 'pages/invoice', component: AppInvoiceComponent },
//             //         { path: 'pages/help', component: AppHelpComponent },
//             //         { path: 'pages/empty', component: EmptyDemoComponent },
//             //         { path: 'documentation', component: DocumentationComponent }
//             //     ]
//             // },
//             // { canActivate: [AuthGuard], path: 'not-found', component: NotFoundComponent },
//             // { canActivate: [AuthGuard], path: 'access-denied', component: AccessDeniedComponent },
//             // // kalo gak ada page not found
//             // { path: '**', redirectTo: 'not-found' }
//             // // {path: 'error', component: AppErrorComponent},
//             // // {path: 'access', component: AppAccessdeniedComponent},
//             // // {path: 'notfound', component: AppNotfoundComponent},
//             // // {path: 'login', component: AppLoginComponent},
//             // // {path: '**', redirectTo: '/notfound'},


//         ], { scrollPositionRestoration: 'enabled' })
//     ],
//     exports: [RouterModule]
// })
// export class AppRoutingModule {
// }
