import { Observable, throwError } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { Injectable } from '@angular/core';
import {
	HttpInterceptor,
	HttpRequest,
	HttpResponse,
	HttpHandler,
	HttpEvent,
	HttpErrorResponse,
	HttpHeaders
} from '@angular/common/http';
import { MessageService } from 'primeng/api';
import { TranslatorService } from './translator.service';

@Injectable({
	providedIn: 'root'
})
export class HeaderInterceptorService implements HttpInterceptor {
	constructor(public messageService: MessageService, public translate: TranslatorService) { }
	intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {

		let dataLoginUser: any

		if (localStorage.getItem('currentUser')) {
			dataLoginUser = JSON.parse(localStorage.getItem('currentUser'))
		} else if (sessionStorage.getItem('currentUser')) {
			dataLoginUser = JSON.parse(sessionStorage.getItem('currentUser'))
		} else {
			dataLoginUser = false
		}
		if (dataLoginUser) {
			req = req.clone(
				{
					headers: new HttpHeaders({
						'X-AUTH-TOKEN': dataLoginUser["X-AUTH-TOKEN"],
						'kdProfile': dataLoginUser.kdProfile,
						'KdUser': dataLoginUser.id,
						'Content-Type': 'application/json',
						'Accept': 'application/json'
					})
				});

		}

		return next.handle(req).pipe(
			map((event: HttpEvent<any>) => {
				if (event instanceof HttpResponse) {
					// console.log('event--->>>', event);
				}
				return event;
			}),
			catchError((error: HttpErrorResponse) => {
				if (error.status == 0) {
					this.messageService.add({ key: 't-notif', severity: 'error', summary: 'Kesalahan', detail: 'Koneksi Server Terputus' });
				} else if (error.status == 401) {
					this.messageService.add({ key: 't-notif', severity: 'error', summary: 'Kesalahan', detail: 'Sesi berakhir' });
				} else if (error.status == 404) {
					this.messageService.add({ key: 't-notif', severity: 'error', summary: 'Kesalahan', detail: error.message });
				} else if (error.status == 500) {
					if (error.error.message) {
						this.messageService.add({ key: 't-notif', sticky: true, severity: 'error', summary: 'Kesalahan', detail: error.error.message });
					} else {
						this.messageService.add({ key: 't-notif', severity: 'error', summary: 'Kesalahan', detail: 'Terjadi kesalahan pada server' });
					}
				} else if (error.status == 400) {
					this.messageService.add({ key: 't-notif', severity: 'error', summary: 'Kesalahan', detail: error.error.messages });
				} else {
					console.log('error--->>>', error);
					this.messageService.add({ key: 't-notif', severity: 'error', summary: 'Info', detail: 'Terjadi Kesalahan' });
				}

				return throwError(error);
			}))
	}
}
