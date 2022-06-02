import { Route } from '@angular/compiler/src/core';
import { Component, AfterViewInit, OnDestroy, Renderer2, OnInit, HostListener } from '@angular/core';
import { NavigationCancel, NavigationEnd, NavigationError, NavigationStart, Router, RouterEvent } from '@angular/router';
import { MessageService, PrimeNGConfig } from 'primeng/api';
import { Subscription } from 'rxjs';
import { AppComponent } from './app.component';
import { ApiService, AuthService, LoaderService } from './service';
import { MessagesModule } from 'primeng/messages';
import { MessageModule } from 'primeng/message';
@Component({
    selector: 'app-main',
    templateUrl: './app.main.component.html',
    // providers: [MessageService]
})
export class AppMainComponent implements AfterViewInit, OnDestroy, OnInit {

    activeTabIndex = -1;

    sidebarActive = false;

    topbarMenuActive: boolean;

    overlayMenuActive: boolean;

    staticMenuDesktopInactive: boolean;

    staticMenuMobileActive: boolean;

    rotateMenuButton: boolean;

    sidebarClick: boolean;

    topbarItemClick: boolean;

    menuButtonClick: boolean;

    activeTopbarItem: any;

    documentClickListener: () => void;

    configActive: boolean;

    configClick: boolean;

    // add
    // add
    isLogin: boolean;
    subscription: Subscription;
    filteredMenu: any[]
    itemMenu: any;
    tanggalGlobal: any
    tanggalSelect: any
    formatCalendar: any
    msgs: any[]
    subscribDataFixed: Subscription
    isSettingDataFixed: boolean
    display: boolean = false
    skeleton: boolean
    namaProfile: string
    showPassword: boolean
    item: any = {}
    disableSave: boolean = false
    namaUser: string
    kelompokUser: string
    namaVendor: string
    constructor(public renderer: Renderer2, private primengConfig: PrimeNGConfig, public app: AppComponent,
        // add
        public authService: AuthService,
        public router: Router,
        private messageService: MessageService,
        private loaderService: LoaderService,
        private apiService: ApiService) {


        this.msgs = []

        // if (localStorage.getItem('lang')) {
        //   this.translate.use(localStorage.getItem('lang'))
        // } else {
        //   this.translate.use('KdProfile=0&KdModulAplikasi=B1&KdVersion=2&KdBahasa=1')
        // }

        if (localStorage.getItem('currentUser')) {
            this.isLogin = true
            let user = JSON.parse(localStorage.getItem('currentUser'))
            this.namaProfile = user.profile.namalengkap
            this.authService.setDataLoginUser(JSON.parse(localStorage.getItem('currentUser')))
            this.item = {
                namaUser: user.namaUser,
            }
            this.namaUser = user.pegawai.namaLengkap
            this.kelompokUser = user.jenisPegawai && user.jenisPegawai.jenispegawai ? user.jenisPegawai.jenispegawai : '-'
            this.namaVendor = user.profile.kodeexternal
        } else if (sessionStorage.getItem('currentUser')) {
            this.isLogin = true
            let user = JSON.parse(sessionStorage.getItem('currentUser'))
            this.namaProfile = user.profile.namalengkap
            this.namaUser = user.pegawai.namaLengkap
            this.item = {
                namaUser: user.namaUser,
            }
            this.kelompokUser = user.jenisPegawai && user.jenisPegawai.jenispegawai ? user.jenisPegawai.jenispegawai : '-'
            this.namaVendor = user.profile.kodeexternal
            this.authService.setDataLoginUser(JSON.parse(sessionStorage.getItem('currentUser')))
        } else {
            this.isLogin = false
        }
        router.events.subscribe((event: RouterEvent) => {
            this.navigationInterceptor(event)
        })

        this.subscription = this.authService.getLoginStatus().subscribe((login: any) => { this.isLogin = login })
        this.subscribDataFixed = this.loaderService.statusDataFixed.subscribe((state: boolean) => {
            this.isSettingDataFixed = state
        })
        // this.terjemah.generateTranslate()
        localStorage.removeItem('tanggalDefault')
        setInterval(() => {
            if (localStorage.getItem('tanggalDefault')) {
                let tgl = new Date(localStorage.getItem('tanggalDefault'))
                this.tanggalGlobal = new Date(tgl.getFullYear(), tgl.getMonth(), tgl.getDate(), tgl.getHours(), tgl.getMinutes(), tgl.getSeconds() + 1)
                localStorage.setItem('tanggalDefault', this.tanggalGlobal)
            } else {
                let tgl = new Date()
                this.tanggalGlobal = new Date(tgl.getFullYear(), tgl.getMonth(), tgl.getDate(), tgl.getHours(), tgl.getMinutes(), tgl.getSeconds() + 1)
                localStorage.setItem('tanggalDefault', this.tanggalGlobal)
            }
        }, 1000);
        this.formatCalendar = this.authService.getFormatTanggal()
    }

    ngOnInit() {
        this.skeleton = false
        this.primengConfig.ripple = true;
    }

    ngAfterViewInit() {
        this.documentClickListener = this.renderer.listen('body', 'click', (event) => {
            if (!this.topbarItemClick) {
                this.activeTopbarItem = null;
                this.topbarMenuActive = false;
            }

            if (!this.menuButtonClick && !this.sidebarClick && (this.overlay || !this.isDesktop())) {
                this.sidebarActive = false;
            }

            if (this.configActive && !this.configClick) {
                this.configActive = false;
            }

            this.configClick = false;
            this.topbarItemClick = false;
            this.sidebarClick = false;
            this.menuButtonClick = false;
        });
    }

    onTabClick(event: Event, index: number) {
        if (this.activeTabIndex === index) {
            this.sidebarActive = !this.sidebarActive;
        } else {
            this.activeTabIndex = index;
            this.sidebarActive = true;
        }

        event.preventDefault();
    }

    closeSidebar(event: Event) {
        this.sidebarActive = false;
        event.preventDefault();
    }

    onSidebarClick(event: Event) {
        this.sidebarClick = true;
    }

    onTopbarMenuButtonClick(event: Event) {
        this.topbarItemClick = true;
        this.topbarMenuActive = !this.topbarMenuActive;

        event.preventDefault();
    }
    popPassword(event: Event) {
        this.showPassword = true
        event.preventDefault();
    }

    onMenuButtonClick(event: Event, index: number) {
        this.menuButtonClick = true;
        this.rotateMenuButton = !this.rotateMenuButton;
        this.topbarMenuActive = false;
        this.sidebarActive = !this.sidebarActive;

        if (this.app.layoutMode === 'overlay') {
            this.overlayMenuActive = !this.overlayMenuActive;
        } else {
            if (this.isDesktop()) {
                this.staticMenuDesktopInactive = !this.staticMenuDesktopInactive;
            } else {
                this.staticMenuMobileActive = !this.staticMenuMobileActive;
            }
        }

        if (this.activeTabIndex < 0) {
            this.activeTabIndex = 0;
        }

        event.preventDefault();
    }

    onTopbarItemClick(event: Event, item) {
        this.topbarItemClick = true;

        if (this.activeTopbarItem === item) {
            this.activeTopbarItem = null;
        } else {
            this.activeTopbarItem = item;
        }

        event.preventDefault();
    }

    onTopbarSearchItemClick(event: Event) {
        this.topbarItemClick = true;

        event.preventDefault();
    }

    onTopbarSubItemClick(event) {
        event.preventDefault();
    }

    onRippleChange(event) {
        this.app.ripple = event.checked;
    }

    onConfigClick(event) {
        this.configClick = true;
    }

    get overlay(): boolean {
        return this.app.layoutMode === 'overlay';
    }

    isDesktop() {
        return window.innerWidth > 1024;
    }

    ngOnDestroy() {
        if (this.documentClickListener) {
            this.documentClickListener();
        }
    }
    // add
    onConfirm() {
        this.messageService.clear('t-logout');

        this.authService.logout()
    }
    onReject() {
        this.messageService.clear('t-logout');
    }
    navigationInterceptor(event: RouterEvent): void {
        if (event instanceof NavigationStart) {
            this.loaderService.show()
        }
        if (event instanceof NavigationEnd) {
            this.loaderService.hide()
        }
        if (event instanceof NavigationCancel) {
            this.loaderService.hide()
        }
        if (event instanceof NavigationError) {
            this.loaderService.hide()
        }
    }

    @HostListener('document:mouseenter', ['$event'])
    onMouseEnter(e) {
        let local = JSON.parse(localStorage.getItem('currentUser'))
        let session = JSON.parse(sessionStorage.getItem('currentUser'))
        if (!local && !session && this.router.url != '/login' && this.router.url != '/bed-online') {
            this.display = true
        } else {
            // console.log('Sudah Logout')
        }
        if (local != null && session != null) {
            if (new Date() > new Date(local.expired)) {
                this.display = true
            }
        }


    }
    keluar() {
        this.display = false
        this.authService.logout()
    }
    logout() {
        this.authService.logout()
        // this.messageService.clear();
        // this.messageService.add({ key: 't-logout', sticky: true, severity: 'warn', summary: 'Yakin Mau Logout', detail: '' });
    }

    getTanggalGlobal() {
        let tgl = new Date(localStorage.getItem('tanggalDefault'))
        this.tanggalSelect = tgl
    }
    setTanggalGlobal() {
        localStorage.setItem('tanggalDefault', this.tanggalSelect)
    }
    resetTanggal() {
        this.tanggalSelect = new Date()
    }
    filterMenu(event: any) {
        let query = event.query;
        let data = this.authService.getListRoute()

        // this.filteredMenu = this.filterMenuItem(query, data);
        this.filteredMenu = data.filter(route => {
            return route.label.toLowerCase().includes(query.toLowerCase())
        })
    }
    onSelectSearchMenu(event: any) {
        this.router.navigate(event.routerLink);
    }
    save() {
        // if (!this.item.old) {
        //     this.messageService.add({ severity: 'warn', summary: 'Password Lama required', detail: '' });
        //     return
        // }
        if (!this.item.new) {
            this.messageService.add({ severity: 'warn', summary: 'Password Baru required', detail: '' });
            return
        }
        if (!this.item.new2) {
            this.messageService.add({ severity: 'warn', summary: 'Konfirmasi Password required', detail: '' });
            return
        }

        if (this.item.new != this.item.new2) {
            this.messageService.add({ severity: 'warn', summary: 'Password tidak sama', detail: '' });
            return
        }
        this.msgs = []
        let data = this.authService.getDataLoginUser()
        let json = {
            'id': data.id,
            'kataSandi': this.item.new2,
            'namaUser': this.item.namaUser,
            'kelompokUser': {
                'id': data.kelompokUser.id
            }
        }
        this.disableSave = true
        this.apiService.postNonMessage('auth/ubah-password', json).subscribe(e => {
            this.messageService.add({ severity: 'success', summary: e.messages, detail: '' });
            // this.showPassword = false
            this.item = {
                namaUser: data.namaUser,
            }
            this.disableSave = false
        }, error => {
            this.disableSave = false
        })
    }
}
