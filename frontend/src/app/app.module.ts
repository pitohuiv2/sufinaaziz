import { LOCALE_ID, NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { HashLocationStrategy, LocationStrategy, registerLocaleData } from '@angular/common';
import { AppRoutingModule } from './app-routing.module';

import localId from '@angular/common/locales/id'
import { TranslateModule, TranslateLoader, MissingTranslationHandler, MissingTranslationHandlerParams } from '@ngx-translate/core';
import { TranslateHttpLoader } from '@ngx-translate/http-loader';

// import {AccordionModule} from 'primeng/accordion';
// import {AutoCompleteModule} from 'primeng/autocomplete';
// import {AvatarModule} from 'primeng/avatar';
// import {AvatarGroupModule} from 'primeng/avatargroup';
// import {BadgeModule} from 'primeng/badge';
// import {BreadcrumbModule} from 'primeng/breadcrumb';
// import {ButtonModule} from 'primeng/button';
// import {CalendarModule} from 'primeng/calendar';
// import {CardModule} from 'primeng/card';
// import {CarouselModule} from 'primeng/carousel';
// import {CascadeSelectModule} from 'primeng/cascadeselect';
// import {ChartModule} from 'primeng/chart';
// import {CheckboxModule} from 'primeng/checkbox';
// import {ChipModule} from 'primeng/chip';
// import {ChipsModule} from 'primeng/chips';
// import {CodeHighlighterModule} from 'primeng/codehighlighter';
// import {ConfirmDialogModule} from 'primeng/confirmdialog';
// import {ConfirmPopupModule} from 'primeng/confirmpopup';
// import {ColorPickerModule} from 'primeng/colorpicker';
// import {ContextMenuModule} from 'primeng/contextmenu';
// import {DataViewModule} from 'primeng/dataview';
// import {DialogModule} from 'primeng/dialog';
// import {DividerModule} from 'primeng/divider';
// import {DropdownModule} from 'primeng/dropdown';
// import {FieldsetModule} from 'primeng/fieldset';
// import {FileUploadModule} from 'primeng/fileupload';
// import {FullCalendarModule} from 'primeng/fullcalendar';
// import {GalleriaModule} from 'primeng/galleria';
// import {InplaceModule} from 'primeng/inplace';
// import {InputNumberModule} from 'primeng/inputnumber';
// import {InputMaskModule} from 'primeng/inputmask';
// import {InputSwitchModule} from 'primeng/inputswitch';
// import {InputTextModule} from 'primeng/inputtext';
// import {InputTextareaModule} from 'primeng/inputtextarea';
// import {KnobModule} from 'primeng/knob';
// import {LightboxModule} from 'primeng/lightbox';
// import {ListboxModule} from 'primeng/listbox';
// import {MegaMenuModule} from 'primeng/megamenu';
// import {MenuModule} from 'primeng/menu';
// import {MenubarModule} from 'primeng/menubar';
// import {MessagesModule} from 'primeng/messages';
// import {MessageModule} from 'primeng/message';
// import {MultiSelectModule} from 'primeng/multiselect';
// import {OrderListModule} from 'primeng/orderlist';
// import {OrganizationChartModule} from 'primeng/organizationchart';
// import {OverlayPanelModule} from 'primeng/overlaypanel';
// import {PaginatorModule} from 'primeng/paginator';
// import {PanelModule} from 'primeng/panel';
// import {PanelMenuModule} from 'primeng/panelmenu';
// import {PasswordModule} from 'primeng/password';
// import {PickListModule} from 'primeng/picklist';
// import {ProgressBarModule} from 'primeng/progressbar';
// import {RadioButtonModule} from 'primeng/radiobutton';
// import {RatingModule} from 'primeng/rating';
// import {RippleModule} from 'primeng/ripple';
// import {ScrollPanelModule} from 'primeng/scrollpanel';
// import {ScrollTopModule} from 'primeng/scrolltop';
// import {SelectButtonModule} from 'primeng/selectbutton';
// import {SidebarModule} from 'primeng/sidebar';
// import {SkeletonModule} from 'primeng/skeleton';
// import {SlideMenuModule} from 'primeng/slidemenu';
// import {SliderModule} from 'primeng/slider';
// import {SplitButtonModule} from 'primeng/splitbutton';
// import {SplitterModule} from 'primeng/splitter';
// import {StepsModule} from 'primeng/steps';
// import {TabMenuModule} from 'primeng/tabmenu';
// import {TableModule} from 'primeng/table';
// import {TabViewModule} from 'primeng/tabview';
// import {TagModule} from 'primeng/tag';
// import {TerminalModule} from 'primeng/terminal';
// import {TieredMenuModule} from 'primeng/tieredmenu';
// import {TimelineModule} from 'primeng/timeline';
// import {ToastModule} from 'primeng/toast';
// import {ToggleButtonModule} from 'primeng/togglebutton';
// import {ToolbarModule} from 'primeng/toolbar';
// import {TooltipModule} from 'primeng/tooltip';
// import {TreeModule} from 'primeng/tree';
// import {TreeTableModule} from 'primeng/treetable';
// import {VirtualScrollerModule} from 'primeng/virtualscroller';

// Application Components
import { AppCodeModule } from './app.code.component';
import { AppComponent } from './app.component';
import { AppMainComponent } from './app.main.component';
import { AppConfigComponent } from './app.config.component';
import { AppMenuComponent } from './app.menu.component';
import { AppMenuitemComponent } from './app.menuitem.component';
import { AppSideBarComponent } from './app.sidebar.component';
import { AppSideBarTabContentComponent } from './app.sidebartabcontent.component';
import { AppTopBarComponent } from './app.topbar.component';
import { AppFooterComponent } from './app.footer.component';
import { primeNgModule } from './shared/shared.module';

import { LoginComponent } from './page/login/login.component';
import { DashboardComponent } from './page/dashboard/dashboard.component';
import { NotFoundComponent } from './page/not-found/not-found.component';
import { AccessDeniedComponent } from './page/access-denied/access-denied.component';
import { Config } from './guard';

export function HttpLoaderFactory(http: HttpClient) {
    return new TranslateHttpLoader(http, Config.get().apiBackend + 'language?', '');
    // return new TranslateHttpLoader(http, './assets/i18n/', '.json');
}
export class MyMissingTranslationHandler implements MissingTranslationHandler {
    handle(_params: MissingTranslationHandlerParams) {
        return 'Undefined';
    }
}

registerLocaleData(localId, 'id');


// Application services
import { MenuService } from './app.menu.service';
import { DemoComponent } from './shared/demo.module';
import { demoServices } from './shared/demo.module';
import { PasswordModule } from 'primeng/password';
import { ScrollPanelModule } from 'primeng/scrollpanel';
import { OverlayPanelModule } from 'primeng/overlaypanel';
import { AlertComp } from './service/component/alert/alert.component';
import { HeadPasienComponent } from './page/template/head-pasien/head-pasien.component';
// import { ServiceWorkerModule } from '@angular/service-worker';
import { environment } from 'src/environments/environment';

@NgModule({
    imports: [
        BrowserModule,
        FormsModule,
        PasswordModule,
        ScrollPanelModule,
        OverlayPanelModule,
        AppRoutingModule,
        HttpClientModule,
        BrowserAnimationsModule,
        AppCodeModule,  
        // ServiceWorkerModule.register('/ngsw-worker.js',{enabled:environment.production}),
        // ServiceWorkerModule.register('/ngsw-worker.js', { enabled: environment.production }),
        TranslateModule.forRoot({
            loader: {
                provide: TranslateLoader,
                useFactory: HttpLoaderFactory,
                deps: [HttpClient]
            },
            missingTranslationHandler: { provide: MissingTranslationHandler, useClass: MyMissingTranslationHandler },
            useDefaultLang: false
        }),
    primeNgModule.forRoot(),
        // ServiceWorkerModule.register('ngsw-worker.js', {
        //     enabled: environment.production,
        //     // Register the ServiceWorker as soon as the app is stable
        //     // or after 30 seconds (whichever comes first).
        //     registrationStrategy: 'registerWhenStable:30000'
        // }),
    ],
    declarations: [
        AppComponent,
        AppMainComponent,
        AppConfigComponent,
        AppMenuComponent,
        AppMenuitemComponent,
        AppTopBarComponent,
        AppFooterComponent,
        AppSideBarComponent,
        AppSideBarTabContentComponent,
        DemoComponent,
        LoginComponent,
        DashboardComponent,
        NotFoundComponent,
        AccessDeniedComponent,
        AlertComp,
        // HeadPasienComponent
    ],
    providers: [
        { provide: LocationStrategy, useClass: HashLocationStrategy },
        demoServices,
        MenuService,
        { provide: LOCALE_ID, useValue: 'id' }
    ],

    bootstrap: [AppComponent]
})
export class AppModule { }
