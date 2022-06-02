import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map } from 'rxjs/operators';
import { Router } from '@angular/router';
import { Config } from '../guard';
import { TranslateService } from '@ngx-translate/core';
import { Observable, Subject } from 'rxjs';
import * as moment from 'moment';
@Injectable({
    providedIn: 'root'
})
export class AuthService {
    constructor(private http: HttpClient, private router: Router, private translate: TranslateService) { }

    private statusLogin = new Subject<boolean>();
    public dataLoginUser: any
    private route: any[];
    private listRoute: any[]

    login(username: string, password: string, rememberMe: boolean) {
        return this.http.post<any>(Config.get().apiBackend + 'service/auth/sign-in', { namaUser: username.trim(), kataSandi: password.trim() })
            .pipe(
                map((user: any) => {
                   if (user.data && user.messages['X-AUTH-TOKEN']) {
                        user.data['X-AUTH-TOKEN'] = user.messages['X-AUTH-TOKEN']
                        var jam = 6
                        var exp = new Date().setTime(new Date().getTime() + (jam*60*60*1000));
                        var expDate = new Date(exp)
                        user.data.expired =  moment(expDate).format('YYYY/MM/DD HH:mm:ss')
                        if (rememberMe) {
                            localStorage.setItem('currentUser', JSON.stringify(user.data));
                        } else {
                            sessionStorage.setItem('currentUser', JSON.stringify(user.data));
                        }

                        this.setDataLoginUser(user.data)
                        this.loginStatus(true)
                    } else {
                        if (rememberMe) {
                            localStorage.setItem('loginPilihModul', JSON.stringify(user.data));
                        } else {
                            sessionStorage.setItem('loginPilihModul', JSON.stringify(user.data));
                        }
                    }
                    return user.data;
                })
            );
    }


    loginModulAplikasi(dataUser: any, rememberMe: boolean) {
        return this.http.post<any>(Config.get().apiBackend + 'service/register/set-modul-aplikasi', dataUser)
            .pipe(
                map((user: any) => {
                    let dataLogin
                    if (user.data && user.data['x-auth-token']) {
                        if (rememberMe) {
                            dataLogin = JSON.parse(localStorage.getItem('loginPilihModul'))
                            dataLogin['x-auth-token'] = user.data['x-auth-token']
                            dataLogin.kdRuangan = user.data.kdRuangan
                            dataLogin.kdDepartemen = user.data.kdDepartemen
                            dataLogin.kdModulAplikasi = user.data.kdModulAplikasi
                            dataLogin.bahasa = user.data.bahasa
                            localStorage.setItem('currentUser', JSON.stringify(dataLogin))
                        } else {
                            dataLogin = JSON.parse(sessionStorage.getItem('loginPilihModul'))
                            dataLogin['x-auth-token'] = user.data['x-auth-token']
                            dataLogin.kdRuangan = user.data.kdRuangan
                            dataLogin.kdDepartemen = user.data.kdDepartemen
                            dataLogin.kdModulAplikasi = user.data.kdModulAplikasi
                            dataLogin.bahasa = user.data.bahasa
                            sessionStorage.setItem('currentUser', JSON.stringify(dataLogin))
                        }
                        this.setDataLoginUser(dataLogin)
                        this.loginStatus(true)
                    }
                    return dataLogin;
                })
            );
    }
    getFormatTanggal() {
        return Config.get()['id']
    }
    flatDataArray(array: any[]) {

        let dataArray = []
        array.forEach((node, _index, _object) => {
            if (node.items) {
                dataArray = dataArray.concat(this.flatDataArray(node.items))
            } else {
                dataArray.push({
                    label: node.label,
                    routerLink: node.routerLink
                })
            }
        })
        return dataArray
    }
    filterListRoute(array: any[]) {
    
        let dataArray = []
        array.forEach((data) => {
            if (data.routerLink != undefined && data.routerLink.length > 0)
                dataArray.push(data.routerLink[0])
        })
        return dataArray
    }
    getListUrl() {
        return this.listRoute
    }
    setListRoute(value: any[]) {
        // debugger
        this.route = this.flatDataArray(value)
        this.listRoute = this.filterListRoute(this.route)
        this.listRoute.push('/')
        this.listRoute.push('/not-found')
        this.listRoute.push('/access-denied')
        // localStorage.setItem('currentUser.role', JSON.stringify(this.listRoute))
    }
    getListRoute() {
        return this.route
    }

    setDataLoginUser(value: any) {
        // debugger
        if (value != null) {
            this.dataLoginUser = value
        } else {
            this.dataLoginUser = null
        }
    }

    getDataLoginUser() {
        return this.dataLoginUser
    }
    getKelompokUser(){
        return this.dataLoginUser.kelompokUser.kelompokUser
    }
    getPegawaiId(){
        return this.dataLoginUser.pegawai.id
    }

    logout() {
        // remove user from local storage to log user out
        localStorage.clear()
        sessionStorage.clear()
        this.setDataLoginUser(null)
        this.loginStatus(false)

        this.router.navigate(['login']);
    }
    loginStatus(value: boolean) {
        this.statusLogin.next(value);
    }

    getLoginStatus(): Observable<any> {
        return this.statusLogin.asObservable();
    }

}
