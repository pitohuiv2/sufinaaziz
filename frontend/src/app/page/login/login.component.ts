import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { first } from 'rxjs/operators';
import { TranslateService } from '@ngx-translate/core';

import { AuthService, ApiService } from '../../service';
import { TranslatorService } from '../../service/translator.service';
import { MessageService } from 'primeng/api';
import { Config } from 'src/app/guard';
import { AlertService } from 'src/app/service/component/alert/alert.service';

@Component({
	selector: 'app-login',
	templateUrl: './login.component.html',
	styleUrls: ['./login.component.css'],
	encapsulation: ViewEncapsulation.None
})
export class LoginComponent implements OnInit {
	// loginForm: FormGroup;
	loading = false;
	submitted = false;
	error = '';
	bahasa: any[];
	loginGagal: string;
	pilihModulAplikasi: boolean = false;
	usernamePasswordNotNull: string;
	d_modul_aplikasi: any[];
	modul_aplikasi: any;
	item: any = {}
	constructor(
		// private formBuilder: FormBuilder,
		private router: Router,
		private authService: AuthService,
		private translate: TranslateService,
		private terjemah: TranslatorService,
		private messageService: MessageService,
		private apiService: ApiService,
		private alert: AlertService) { }

	ngOnInit() {
		this.modul_aplikasi = null

		// this.bahasa = [
		// {
		// 	label:"Bahasa Indonesia",
		// 	value:"id"
		// },
		// {
		// 	label:"English",
		// 	value:"en"
		// },
		// {
		// 	label:"日本",
		// 	value:"ja"
		// }]
		localStorage.clear()
		sessionStorage.clear()
	}

	loginKeun() {
		this.submitted = true;
		if (!this.item.namaUser) {
			this.messageService.add({ key: 't-login', severity: 'error', summary: 'Kesalahan', detail: 'Nama User harus di isi !' });
			this.loading = false;
			return;
		}
		if (!this.item.kataSandi) {
			this.messageService.add({ key: 't-login', severity: 'error', summary: 'Kesalahan', detail: 'Kata Sandi harus di isi !' });
			this.loading = false;
			return;
		}

		this.loading = true;
		this.authService.login(this.item.namaUser, this.item.kataSandi, false)
			.pipe(first())
			.subscribe(
				(data: any) => {
					this.loading = false;

					if (data.id != undefined) {
						let namaDashboard = data.kelompokUser.kodeexternal
						if (namaDashboard != null) {
							this.router.navigate(['dashboard-' + namaDashboard]);
						} else {
							this.router.navigate(['']);
						}


					} else {
						this.d_modul_aplikasi = []
						this.pilihModulAplikasi = true
						this.authService.setDataLoginUser(data)
					}

				}, (error: any) => {
					this.loading = false
				});
	}
	pilihModul(event: any) {
		this.loading = true;
	}
	batal() {
		this.pilihModulAplikasi = false
		this.d_modul_aplikasi = []
		this.item = {}
	}

}
