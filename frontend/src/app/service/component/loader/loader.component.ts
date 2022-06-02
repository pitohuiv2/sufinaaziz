import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { LoaderService } from './loader.service';






// @Component({
//     selector: 'angular-loader',
//     templateUrl: 'loader.component.html',
//     styleUrls: ['loader.component.css']
// })

@Component({
    selector: 'app-loader',
    template: '<p-progressBar mode="indeterminate" *ngIf="show" class="indexxx"></p-progressBar>'  ,
    styles: ['.indexxx {' +
    ' position: fixed; width: 100%;opacity: 1; z-index: 10000;  '
    + '}']
})

export class LoaderComp implements OnInit, OnDestroy {

    show = false;

    private subscription: Subscription;

    constructor(private loaderService: LoaderService) { }

    ngOnInit() { 
        
        this.subscription = this.loaderService.getState().subscribe(state => {
            this.show = state.show;
        });
    }

    ngOnDestroy() {
        this.subscription.unsubscribe();
    }
}