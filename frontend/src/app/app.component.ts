import { Component, HostListener, HostBinding } from '@angular/core';
import { NavigationCancel, NavigationEnd, NavigationError, NavigationStart, Router, RouterEvent, RouterOutlet } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { MessageService, PrimeNGConfig } from 'primeng/api';
import { Subscription } from 'rxjs';
import {
  trigger,
  state,
  style,
  animate,
  transition,
  // ...
} from '@angular/animations';
import { routerAnimation } from './app.animations';
// import { SwPush } from '@angular/service-worker';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
  animations: [routerAnimation()],
})
export class AppComponent {
  // layoutMode = 'static';
  // theme = 'green';
  layoutMode = 'overlay';
  theme = 'indigo';
  inputStyle = 'outlined';
  ripple: boolean;
  readonly VAPID_PUBLIC_KEY = "BGcdHXVX7W1wRNvqdppcz0oWin-F8FAyyCvbwxOcgi-wRTmt6RDyjVTzDD70CzGZ9lTviIyWTkMbaWfuKQY0kBE";

  constructor(private primengConfig: PrimeNGConfig,
    // readonly swPush: SwPush,


  ) {
    // this.swPush.notificationClicks.subscribe(event => {
    //   console.log('Received notification: ', event);
    //   const url = event.notification.data.url;
    //   window.open(url, '_blank');
    // });
  }

  ngOnInit() {
    this.primengConfig.ripple = true;
    this.ripple = true;
  }

  public getRouteAnimation(outlet: RouterOutlet) {
    const res =
      outlet.activatedRouteData.num === undefined
        ? -1
        : outlet.activatedRouteData.num;

    return res;
  }
}
