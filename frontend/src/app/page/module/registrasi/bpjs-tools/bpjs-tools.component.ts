import { Component, OnInit } from '@angular/core';
import { MenuItem, } from 'primeng/api';

import { ActivatedRoute, Router } from '@angular/router';
@Component({
  selector: 'app-bpjs-tools',
  templateUrl: './bpjs-tools.component.html',
  styleUrls: ['./bpjs-tools.component.scss']
})
export class BpjsToolsComponent implements OnInit {
  items: MenuItem[];
  constructor(
    private activeRoute: ActivatedRoute,
    private router: Router,
  ) {

  }

  ngOnInit(): void {
    this.items = [
      {
        label: 'Vclaim',
        icon: 'pi pi-fw pi-file',
        items: [
          {
            label: 'Referensi',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('v-referensi');
            }
          },
          {
            label: 'Peserta',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('v-peserta');
            }
          },
          {
            label: 'SEP',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('v-sep');
            }
          },
          {
            label: 'Rujukan',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('v-rujukan');
            }
          },
          {
            label: 'Rencana Kontrol',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('v-rencana-kontrol');
            }
          },
          {
            label: 'Monitoring',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('v-monitoring');
            }
          },
          {
            label: 'LPK',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('v-lpk');
            }
          },
          {
            label: 'PRB',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('v-prb');
            }
          }
        ]
      },

      {
        label: 'Aplicares',
        icon: 'pi pi-fw pi-file',
        items: [
          {
            label: 'Ketersediaan Kamar',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('v-bed');
            }
          },
        ]
      },

      {
        label: 'Antrean',
        icon: 'pi pi-fw pi-pencil',
        items: [
          {
            label: 'Jadwal Dokter',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('antrol-jadwal-dokter');
            }
          },
          {
            label: 'Antrean',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('antrol-antrean');
            }
          },
          {
            label: 'Dashboard',
            icon: 'pi pi-fw pi-chevron-circle-right',
            command: () => {
              this.selectMenu('antrol-dashboard');
            }
          }
        ]
      }
    ]
  }
  selectMenu(event) {
    this.router.navigate([event], { relativeTo: this.activeRoute });
  }


}
