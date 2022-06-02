@extends('template.template3')
@section('css')
    <style>
        .pcoded .pcoded-inner-content {
            padding: 10px 0 10px 0;
        }

        .class-icon {
            font-size: 50px;

            padding: 5px;
            border-radius: 5px;
            background: #bcbcbc8a;
        }

        .pad {
            padding-top: 3rem;
        }

        @media (min-width: 992px) {
            .pad {
                padding-top: 1.8rem;
            }
        }

        .modal-lg .kons {
            width: 1140px;
        }

        @media only screen and (max-width: 575px) {
            .latest-update-card .card-block .latest-update-box .update-meta {
                z-index: 2;
                min-width: 0;
                text-align: left !important;
                margin-bottom: 15px;
                border-top: 1px solid #f1f1f1;
                padding-top: 15px;
            }
        }

    </style>
@endsection
@section('content-body')
    <a href="javascript:" id="return-to-top"><i class="fa fa-chevron-up"></i></a>
    <div class="page-wrapper pad" style="margin-top:20px" ng-controller="dashboardPelayananCtrl">
        <div class="page-body">
            <div class="card">
                <div class="card-header">
                    <h5>Dashboard Pelayanan</h5>
                </div>
                <div class="card-block tab-icon  panels-wells">
                    <form action="{!! route("show_page", ["role" => $_SESSION['role'], "pages" => $r->pages ]) !!}"
                          method="get">
                        <div class="row">
                            <div class="col-lg-7">
                            </div>
                            <div class="col-lg-2" style="margin-top: 5px">
                                {{--                                <md-datepicker  md-placeholder="Enter date" md-open-on-focus></md-datepicker>--}}
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><i class="ti-calendar"></i></span>
                                    <input type="text" id="tglawal" name="tglawal" class="date-custom form-control"
                                           value="{{request()->get("tglawal")}}">
                                </div>

                            </div>
                            <div class="col-lg-2" style="margin-top: 5px">
                                {{--                                <md-datepicker ng-model="ctrl.myDate" md-placeholder="Enter date" md-open-on-focus></md-datepicker>--}}
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><i class="ti-calendar"></i></span>
                                    <input type="text" id="tglakhir" name="tglakhir" class="date-custom form-control"
                                           value="{{request()->get("tglakhir")}}">
                                </div>
                            </div>
                            <div class="col-lg-1" style="margin-top: 5px">
                                <button class="btn btn-success  btn-outline-success" type="submit">
                                    <i class="icofont icofont-search"></i>Search
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-xl-12" style="margin-top: 10px">
                                <div class="card">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading bg-danger" style="background: #8632fb">
                                            Sebaran Alamat By Pasien Lama
                                        </div>
                                        <div class="panel-body">
                                            <div class="row" style="padding:10px">
                                                <div class="col-lg-7 col-md-12">
                                                    <div id="mapPasien" class="set-map"></div>
                                                </div>
                                                <div class="col-lg-5 col-md-12">
                                                    <div class="card">
                                                        <div class="card-header" style="padding: .75rem 1.25rem;">
                                                            <h5> 10 Besar Alamat By Pasien Lama</h5>
                                                        </div>
                                                        <div class="card-block">

                                                            <div id="chartAlamatPasien"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <ul class="nav nav-tabs md-tabs " role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#home7" role="tab"><i
                                                class="fa fa-user"></i>Pengunjung</a>
                                        <div class="slide"></div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#profile7" role="tab"><i
                                                class="fa fa-group"></i>Kunjungan</a>
                                        <div class="slide"></div>
                                    </li>

                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content card-block">


                                    <div class="tab-pane active" id="home7" role="tabpanel">
                                        <div class="row">
                                            @php
                                                $totalP = 0;
                                            @endphp
                                            @foreach($res['pengunjung'] as $k)
                                                @php
                                                    $totalP = $totalP + (float)$k->jumlah;
                                                @endphp

                                                <div class="col-lg-3 col-xs-12">
                                                    <div class="card {!! $k->warna !!} order-card"
                                                         style="cursor: pointer"
                                                         onclick="klikDetails({!! $k->id !!},'{!! $k->namadepartemen !!}','pengunjung')">
                                                        <div class="card-block">
                                                            <h6>{!! $k->namadepartemen !!}</h6>
                                                            <h2>{!! $k->jumlah !!}</h2>
                                                            <p class="m-b-0"><i
                                                                    class="feather icon-arrow-up m-l-10"></i></p>
                                                            <i class="card-icon {!!  $k->icon !!}"></i>
                                                        </div>
                                                    </div>
                                                </div>


                                            @endforeach
                                            <div class="col-lg-3 col-xs-12">
                                                <div class="card total order-card">
                                                    <div class="card-block">
                                                        <h6>Total Pasien</h6>
                                                        <h2>{!! $totalP !!}</h2>
                                                        <p class="m-b-0"><i class="feather icon-arrow-up m-l-10"></i>
                                                        </p>
                                                        <i class="card-icon icofont icofont-chart-histogram"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="profile7" role="tabpanel">
                                        <div class="row">
                                            @php
                                                $totalP2 = 0;
                                            @endphp
                                            @foreach($res['kunjungan'] as $k)
                                                @php
                                                    $totalP2 = $totalP2 + (float)$k['jumlah'];
                                                @endphp

                                                <div class="col-lg-3 col-xs-12">
                                                    <div class="card {!! $k['warna'] !!} order-card"
                                                         style="cursor: pointer"
                                                         onclick="klikDetails({!! $k['id'] !!},'{!! $k['namadepartemen'] !!}','kunjungan')">
                                                        <div class="card-block">
                                                            <h6>{!! $k['namadepartemen'] !!}</h6>
                                                            <h2>{!! $k['jumlah'] !!}</h2>
                                                            <p class="m-b-0"><i
                                                                    class="feather icon-arrow-up m-l-10"></i></p>
                                                            <i class="card-icon {!!  $k['icon'] !!}"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="col-lg-3 col-xs-12">
                                                <div class="card total order-card">
                                                    <div class="card-block">
                                                        <h6>Total Kunjungan</h6>
                                                        <h2>{!! $totalP2 !!}</h2>
                                                        <p class="m-b-0"><i class="feather icon-arrow-up m-l-10"></i>
                                                        </p>
                                                        <i class="card-icon icofont icofont-prescription"></i>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <div class="card">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading bg-danger">
                                             Pengunjung Rawat Jalan H-21 s/d H+7
                                        </div>
                                        <div class="panel-body">
                                            <div id="chartTrend"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-success">
                                        <div class="panel-heading bg-success">
                                            Daftar Registrasi Pasien Menurut Cara Daftar
                                        </div>
                                        <div class="panel-body">
                                            <div class="tab-content card-block">
                                                <div class="tab-pane active" id="home1" role="tabpanel">
                                                    <div id="chartJenisPenjadwalanPie"></div>
                                                </div>
                                                <div class="tab-pane" id="profile1" role="tabpanel">
                                                    <div id="chartJenisPenjadwalanLine"></div>
                                                </div>
                                            </div>
                                            <ul class="nav nav-tabs tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab"
                                                       href="#home1" role="tab">Donut Chart</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#profile1" role="tab">
                                                        Grafik Chart</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading bg-primary">
                                           Detail Cara Daftar
                                        </div>

                                        <div class="panel-body">
                                            <div class="tab-content card-block">
                                                <div class="tab-pane active" id="rajal" role="tabpanel">
                                                    <div id="chartCaraDaftarRajal"></div>
                                                </div>
                                                <div class="tab-pane" id="ranap" role="tabpanel">
                                                    <div id="chartCaraDaftarRanap"></div>
                                                </div>
                                                <div class="tab-pane" id="rehab" role="tabpanel">
                                                    <div id="chartCaraDaftarRehab"></div>
                                                </div>
                                                <div class="tab-pane" id="igd" role="tabpanel">
                                                    <div id="chartCaraDaftarIgd"></div>
                                                </div>
                                                <div class="tab-pane" id="lab" role="tabpanel">
                                                    <div id="chartCaraDaftarLab"></div>
                                                </div>
                                                <div class="tab-pane" id="rad" role="tabpanel">
                                                    <div id="chartCaraDaftarRad"></div>
                                                </div>
                                            </div>
                                            <ul class="nav nav-tabs tabs " role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab"
                                                       href="#rajal" role="tab">Rawat Jalan</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab"
                                                       href="#ranap" role="tab">
                                                        Rawat Inap</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab"
                                                       href="#rehab" role="tab">
                                                        IRM </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab"
                                                       href="#igd" role="tab">
                                                        IGD</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab"
                                                       href="#lab" role="tab">
                                                        Laboratorium</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab"
                                                       href="#rad" role="tab">
                                                        Radiologi</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-warning">
                                        <div class="panel-heading  bg-warning">
                                            Informasi Pengunjung Rawat Jalan Detail
                                        </div>
                                        <div class="panel-body" style="height: 480px;overflow-x: auto;">
                                            <div class="row" style="padding:10px;">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table  table-striped table-sm table-styling"
                                                               id="t_Kedatangan"
                                                               style="width:100%">
                                                            <thead>
                                                            <tr>
                                                                <th>Poli</th>
                                                                <th>Total</th>
                                                                <th>Belum Diperiksa</th>
                                                                <th>Diperiksa</th>
                                                                <th>Batal</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @forelse($res['info_kedatangan'] as $i => $d)
                                                                <tr>
                                                                    <td>{{ $d['namaruangan'] }}</td>
                                                                    <td>{{ $d['total'] }}</td>
                                                                    <td>{{ $d['belumperiksa'] }}</td>
                                                                    <td>{{ $d['diperiksa'] }}</td>
                                                                    <td>{{ $d['batalregistrasi'] }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="6" style="text-align: center">Data
                                                                        Tidak ada
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-info">
                                        <div class="panel-heading bg-info">
                                            Pengunjung Berdasarkan Jenis Pasien
                                        </div>
                                        <div class="panel-body">
                                            <div class="tab-content card-block">
                                                <div class="tab-pane active" id="all_jenis" role="tabpanel">
                                                    <div id="chartKunjungnJenisPasien"></div>
                                                </div>
                                                <div class="tab-pane" id="detail_dep" role="tabpanel">
                                                    <div id="chartDetailKelompokPasien"></div>
                                                </div>
                                            </div>
                                            <ul class="nav nav-tabs tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab"
                                                       href="#all_jenis" role="tab">Semua</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#detail_dep" role="tab">
                                                        Detail Per-Departemen</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="card">
                                    <div class="panel panel-default">
                                        <div class="panel-heading bg-default">
                                            <span
                                                style="color:black"> Pemakaian Tempat Tidur Berdasarkan Kategori Umur</span>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row" style="padding: 10px">
                                                @foreach($res['tt_usia'] as $d)
                                                    <div class="col-md-12 col-lg-2">
                                                        <div class="card bg-c-pink text-white widget-visitor-card"
                                                             style="margin-bottom:20px">
                                                            <div class="card-block-small text-center" style="padding:0">
                                                                <span style="font-size: 2rem;">{!! $d['jml'] !!}</span>
                                                                <h5 style="font-size:15px">{!! $d['name'] !!}</h5>
                                                                <span style="font-size: 10px"
                                                                      class="m-b-0">{!! $d['umur'] !!}</span>
                                                                <i class="icofont icofont-social-aim"></i>
                                                                {{--                                                                <img src="{!! asset($d['img']) !!}" style="width: 60px">--}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading bg-primary">
                                            Pasien Rawat Inap By Ruangan
                                        </div>
                                        <div class="panel-body">
                                            @php
                                                $res['ranap'] = App\Http\Controllers\MainController::getKunjunganRuanganRawatInap(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile']);
                                            @endphp
                                            <div id="chartKunjunganRanap"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading bg-yellow-gradient">
                                            Pasien Rawat Inap By Jenis Kelamin
                                        </div>
                                        <div class="panel-body">
                                            @php
                                                $res['ranapjk'] = App\Http\Controllers\MainController::getRanapByJK(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile']);
                                            @endphp
                                            <div id="chartRanapJK"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-info">
                                        <div class="panel-heading bg-info">
                                            Informasi Kedatangan Menurut Jenis Pelayanan
                                        </div>
                                        <div class="panel-body">
                                            @php
                                                $res['jenis_pel'] = App\Http\Controllers\MainController::getKunjunganPerJenisPelayanan(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile']);
                                            @endphp
                                            <div id="chartJenisPelayanan"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading bg-danger">
                                            10 Besar Asal Perujuk Pasien BPJS
                                        </div>
                                        <div class="panel-body">
                                            @php
                                                $res['perujuk_bpjs'] = App\Http\Controllers\MainController::getTopTenAsalPerujukBPJS(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile']);
                                            @endphp
                                            <div id="chart10PerujukBpjs"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-success">
                                        <div class="panel-heading bg-success">
                                            10 Besar Diagnosa
                                        </div>
                                        <div class="panel-body">
                                            @php
                                                $res['toptendiag'] = App\Http\Controllers\MainController::getTopTenDiagnosa(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile']);
                                            @endphp
                                            <div id="chart10Diagnosa"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-info">
                                        <div class="panel-heading bg-info">
                                            10 Besar Dokter Penanggung Jawab
                                        </div>
                                        <div class="panel-body">
                                            @php
                                                $res['toptendpjp'] = App\Http\Controllers\MainController::getTopTenDPJP(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile']);
                                            @endphp
                                            <div id="chartTopDPJP"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="card">
                                    <div class="panel panel-default">
                                        <div class="panel-heading bg-default">
                                            <span style="color:black">   10 Besar Pasien Rawat Jalan PerPoli</span>
                                        </div>
                                        <div class="panel-body">
                                            @php
                                                $res['toptenpoli'] = App\Http\Controllers\MainController::getTopTenPoli(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile']);
                                            @endphp
                                            <div id="chartTopPoli"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--                            <div class="col-lg-12 col-md-12">--}}
                            {{--                                <div class="card">--}}
                            {{--                                    <div class="panel panel-pink">--}}
                            {{--                                        <div class="panel-heading bg-c-pink">--}}
                            {{--                                            <span style="color:white">Demografi Diagnosa</span>--}}
                            {{--                                        </div>--}}
                            {{--                                        <div class="panel-body">--}}
                            {{--                                            <div class="row" style="padding:10px">--}}
                            {{--                                                <div class="col-lg-7 col-md-12">--}}
                            {{--                                                    <div class="row">--}}
                            {{--                                                        <div class="col-lg-6 col-md-12">--}}
                            {{--                                                            <select id="comboDiagnosa" class="form-control cbo-custom"--}}
                            {{--                                                                    name="diagnosa">--}}
                            {{--                                                                <option value="">-- Filter Diagnosa --</option>--}}
                            {{--                                                                @foreach($res['toptendiag'] as $k)--}}
                            {{--                                                                    <option--}}
                            {{--                                                                        {!! isset($res['toptendiag']) && count($res['toptendiag'])> 0?$res['toptendiag'][0]['kd'] == $k['kd'] :null? 'selected' : '' !!}--}}
                            {{--                                                                        value='{{ $k['kd'] }}'> {{ $k['kddiagnosa'] }}</option>--}}
                            {{--                                                                @endforeach--}}
                            {{--                                                            </select>--}}
                            {{--                                                        </div>--}}
                            {{--                                                        <div class="col-lg-10 col-md-12" style="height: 400px ;">--}}
                            {{--                                                            <div id="world-map-markers" class="set-map"></div>--}}
                            {{--                                                        </div>--}}
                            {{--                                                        <div class="col-lg-2 col-md-12 custList"--}}
                            {{--                                                             style="height: 400px !important; overflow: auto;">--}}
                            {{--                                                            <table id="tableDemog">--}}
                            {{--                                                                <thead>--}}
                            {{--                                                                <tr>--}}
                            {{--                                                                    <th>--}}
                            {{--                                                                        Kecamatan--}}
                            {{--                                                                    </th>--}}
                            {{--                                                                </tr>--}}
                            {{--                                                                </thead>--}}
                            {{--                                                                <tbody id="tBodyDemog">--}}
                            {{--                                                                </tbody>--}}
                            {{--                                                            </table>--}}
                            {{--                                                        </div>--}}
                            {{--                                                    </div>--}}
                            {{--                                                </div>--}}
                            {{--                                                <div class="col-lg-5 col-md-12">--}}
                            {{--                                                    <div class="card">--}}
                            {{--                                                        <div class="card-header" style="padding: .75rem 1.25rem;">--}}
                            {{--                                                            <h5> 10 Besar Diagnosa Per-Wilayah</h5>--}}
                            {{--                                                        </div>--}}
                            {{--                                                        <div class="card-block">--}}
                            {{--                                                            @php--}}
                            {{--                                                                $res['diagnosakec'] = App\Http\Controllers\MainController::getTopTenDiagnosa(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile'],16);--}}
                            {{--                                                            @endphp--}}
                            {{--                                                            <div id="chartDiagnosaKec"></div>--}}
                            {{--                                                        </div>--}}
                            {{--                                                    </div>--}}
                            {{--                                                </div>--}}
                            {{--                                            </div>--}}
                            {{--                                        </div>--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}

                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-warning">
                                        <div class="panel-heading  bg-c-kuning">
                                            Ketersediaan Tempat Tidur Per Kelas
                                        </div>
                                        @php
                                            $res['tt'] = App\Http\Controllers\MainController::getKetersediaanTempatTidurPerkelas(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile']);
                                        @endphp
                                        <div class="panel-body">
                                            <div class="row" style="padding:10px;">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table  table-striped table-sm table-styling"
                                                               style="width:100%">
                                                            {{--                                                            id="t_Bed"--}}
                                                            <thead>
                                                            <tr>
                                                                <th>Kelas</th>
                                                                <th>Jumlah</th>
                                                                <th>Kosong</th>
                                                                <th>Terpakai</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @php
                                                                $jml = 0;
                                                                $ksng = 0;
                                                                $pakai = 0;
                                                            @endphp
                                                            @forelse($res['tt'] as $i => $d)
                                                                @php
                                                                    $jml = (float)$d->jml +$jml;
                                                                    $ksng = (float)$d->kosong +$ksng;
                                                                    $pakai = (float)$d->terpakai +$pakai;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $d->namakelas }}</td>
                                                                    <td style="text-align: center">{{ $d->jml }}</td>
                                                                    <td style="text-align: center">{{ $d->terpakai }}</td>
                                                                    <td style="text-align: center">{{ $d->kosong }}</td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <td colspan="4" style="text-align: center">Data
                                                                        Tidak ada
                                                                    </td>
                                                                </tr>
                                                            @endforelse
                                                            <tr style="background:rgba(0,0,0,.3);">
                                                                <td>JUMLAH</td>
                                                                <td style="text-align: center">{{$jml}}</td>
                                                                <td style="text-align: center">{{$pakai}}</td>
                                                                <td style="text-align: center">{{$ksng}}</td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="card">
                                    <div class="panel panel-default">
                                        <div class="panel-heading bg-purple">
                                            Data Kegiatan Pelayanan Rumah Sakit
                                        </div>
                                        <div class="panel-body">
                                            @php
                                                $bor = App\Http\Controllers\MainController::getBorLosToi(request()->get("tglawal"),request()->get("tglakhir"),$_SESSION['kdProfile']);
                                            @endphp
                                            <div class="row" style="padding: 10px">
                                                <div class="col-md-12 col-xl-4">

                                                    {{--                                                    <div class="card"     style="background: rgba(69, 90, 100, 0.08);">--}}
                                                    {{--                                                        <div class="card-block">--}}
                                                    {{--                                                            <div class="row align-items-center">--}}
                                                    {{--                                                                <div class="col-8">--}}
                                                    {{--                                                                    <h4 class="text-c-yellow f-w-600">{!! $bor[0]['bor'] !!}</h4>--}}
                                                    {{--                                                                    <h6 class="text-muted m-b-0">BOR</h6>--}}
                                                    {{--                                                                </div>--}}
                                                    {{--                                                                <div class="col-4 text-right">--}}
                                                    {{--                                                                    <i class="icofont icofont-bed f-28"></i>--}}
                                                    {{--                                                                </div>--}}
                                                    {{--                                                            </div>--}}
                                                    {{--                                                        </div>--}}
                                                    {{--                                                        <div class="card-footer bg-c-yellow">--}}
                                                    {{--                                                            <div class="row align-items-center">--}}
                                                    {{--                                                                <div class="col-9"></div>--}}
                                                    {{--                                                                <div class="col-3 text-right">--}}
                                                    {{--                                                                    <i class="feather icon-trending-up text-white f-16"></i>--}}
                                                    {{--                                                                </div>--}}
                                                    {{--                                                            </div>--}}
                                                    {{--                                                        </div>--}}
                                                    {{--                                                    </div>--}}
                                                    <div class="card widget-card-1"
                                                         style="background: rgba(69, 90, 100, 0.08);">
                                                        <div class="card-block-small">
                                                            <i class="icofont icofont-bed bg-c-blue card1-icon"
                                                               style="padding-right:0"></i>
                                                            <span class="text-c-blue f-w-600">BOR</span>
                                                            <h4>{!! $bor[0]['bor'] !!}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xl-4">
                                                    <div class="card widget-card-1"
                                                         style="background: rgba(69, 90, 100, 0.08);">
                                                        <div class="card-block-small">
                                                            <i class="icofont icofont-prescription bg-c-pink card1-icon"
                                                               style="padding-right:0"></i>
                                                            <span class="text-c-pink f-w-600">ALOS</span>
                                                            <h4>{!! $bor[0]['alos'] !!}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xl-4">
                                                    <div class="card widget-card-1"
                                                         style="background: rgba(69, 90, 100, 0.08);">
                                                        <div class="card-block-small">
                                                            <i class="icofont icofont-disabled bg-c-green card1-icon"
                                                               style="padding-right:0"></i>
                                                            <span class="text-c-green f-w-600">TOI</span>
                                                            <h4>{!! $bor[0]['toi'] !!}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xl-4">
                                                    <div class="card widget-card-1"
                                                         style="background: rgba(69, 90, 100, 0.08);">
                                                        <div class="card-block-small">
                                                            <i class="icofont icofont-first-aid bg-c-1 card1-icon"
                                                               style="padding-right:0"></i>
                                                            <span class="text-c-kuning f-w-600">BTO</span>
                                                            <h4>{!! $bor[0]['bto'] !!}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xl-4">
                                                    <div class="card widget-card-1"
                                                         style="background: rgba(69, 90, 100, 0.08);">
                                                        <div class="card-block-small">
                                                            <i class="icofont icofont-operation-theater bg-c-merah card1-icon"
                                                               style="padding-right:0"></i>
                                                            <span class="text-c-merah f-w-600">NDR</span>
                                                            <h4>{!! $bor[0]['ndr'] !!}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xl-4">
                                                    <div class="card widget-card-1"
                                                         style="background: rgba(69, 90, 100, 0.08);">
                                                        <div class="card-block-small">
                                                            <i class="icofont icofont-pulse bg-c-ungu card1-icon"
                                                               style="padding-right:0"></i>
                                                            <span class="text-c-ungu f-w-600">GDR</span>
                                                            <h4>{!! $bor[0]['gdr'] !!}</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKunjungan" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span id="titleModalKun"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="load_kunjungan">
                </div>

                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/jvectormap/indonesia-adm1.js') }}"></script>
    <script>

        angular.controller('dashboardPelayananCtrl', function ($scope, $http, httpService) {
            var map = L.map('mapPasien').setView([-1.4006881, 118.5828712], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://pcuresijemari.com">P-CURE NEO</a> Copyright © 2021. All Rights Reserved.',
                // zoomOffset: -1,
                // maxZoom: 15,
            }).addTo(map);
            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();
            var kdProfile = "{!! $_SESSION['kdProfile'] !!}"
            httpService.get('get-kota-kab-map').then(function (e) {
                for (var i = 0; i < e.data.length; i++) {
                    const elem = e.data[i]

                    L.marker([elem.lat, elem.lng]).addTo(map)
                        .bindPopup("<b>" + elem.namakotakabupaten + "</b><br>" + elem.jumlah + ' Pasien')
                        .openPopup();

                }
            })
        })
        $(document).ready(function () {
            $('.js-example-basic-single').select2()
        });
        var APP_URL = {!! json_encode(url('/')) !!}
        setChartTrend()
        setChartAlamat()
        // setChartPenjadwalan()
        setChartPerjenis()
        setChartJenisPel()
        setChartPerujuk()
        setChartRanap()
        setChartJK()
        setChartDiag()
        //komen chart diag
        // getKecamatan(4)
        // setChartDiagKec(null)

        setChartTopDPJP()
        setChartTopPoli()

        function setChartTrend() {
            let trend =  @json($res['trend_kunjungan']);
            ;
            // //debugger
            let data1 = []
            let data2 = []
            let data3 = []
            let data4 = []
            let categories = []
            // for (let i in trend.datasets) {
            //     const element = trend.datasets[i]
            //
            //     if(element.label=='Rawat Jalan'){
            //         for (let x in element.data) {
            //             const el = element.data[x]
            //             data1.push({
            //                 y: parseFloat(el),
            //                 color: colorNyieun()[x]
            //             });
            //         }
            //     }
            //     if(element.label=='Rawat Inap'){
            //         for (let x in element.data) {
            //             const el = element.data[x]
            //             data2.push({
            //                 y: parseFloat(el),
            //                 color: '#34ebae'
            //             });
            //         }
            //     }
            //     if(element.label=='Gawat Darurat'){
            //         for (let x in element.data) {
            //             const el = element.data[x]
            //             data3.push({
            //                 y: parseFloat(el),
            //                 color: 'rgb(255, 206, 86)'
            //             });
            //         }
            //     }
            //
            //
            // }
            for (let i in trend) {
                data1.push({
                    y: parseFloat(trend[i].totalterdaftar),
                    color: colorNyieun2()[i]
                });
            }
            for (let i in trend) {
                data2.push({
                    y: parseFloat(trend[i].diperiksa),
                    // color: this.colorNyieun[i]
                    color: 'yellow'//this.colorNyieun[i]
                });
            }
            for (let i in trend) {
                data3.push({
                    y: parseFloat(trend[i].belumperiksa),
                    color: 'red'//this.colorNyieun[i]
                });
            }
            for (let i in trend) {
                data4.push({
                    y: parseFloat(trend[i].batalregistrasi),
                    color: 'black'//this.colorNyieun[i]
                });
            }
            for (let i in trend) {
                categories.push(trend[i].tanggal);
            }
            ;
            // categories =trend.labels
            // console.log(data1)
            Highcharts.chart('chartTrend', {
                chart: {
                    zoomType: 'x',
                    spacingRight: 20
                },
                title: {
                    text: ''
                },

                xAxis: {
                    categories: categories,
                    crosshair: true,
                    // type: 'datetime',
                    //  maxZoom: 24 * 3600 * 1000, // fourteen days
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Pasien'
                    }
                },
                tooltip: {
                    shared: true
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1,
                    // backgroundColor:undefined
                },
                plotOptions: {
                    area: {
                        fillColor: {
                            linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                            stops: [
                                [0, Highcharts.getOptions().colors[0]],
                                // [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                [1, Highcharts.Color(Highcharts.getOptions().colors[0])]
                            ]
                        },
                        lineWidth: 1,
                        marker: {
                            enabled: true
                        },
                        shadow: false,
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        threshold: null
                    },
                    column: {
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: colors()[1],

                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',');
                            }
                        },
                        showInLegend: true
                    },
                    //line: {
                    //    cursor: 'pointer',

                    //    dataLabels: {
                    //        enabled: true,
                    //        color: colors[0],
                    //        style: {
                    //            fontWeight: 'bold'
                    //        },
                    //        formatter: function () {
                    //            return Highcharts.numberFormat(this.y, 0, '.', ',');
                    //        }
                    //    },
                    //    showInLegend: true
                    //}
                },
                credits: {
                    enabled: false
                },

                series: [{
                    type: 'column',
                    name: 'Total Terdaftar',
                    data: data1,

                },
                    {
                        type: 'line',
                        color: 'yellow',//this.colorNyieun[i]
                        name: 'Sudah Diperiksa',
                        data: data2,
                    },
                    {
                        type: 'line',
                        name: 'Belum Diperiksa',
                        color: 'red',//this.colorNyieun[i]
                        data: data3,
                    },
                    {
                        type: 'line',
                        name: 'Batal Registrasi',
                        color: 'black',
                        data: data4,
                    }
                ]

            })

        }

        function setChartPenjadwalan() {
            // chart jenis penjadwalan pie

            //{{-- this.dataPenjadwalan = @json($res['jenis_penjadwalan']); --}}
            let series = [];
            let categories = [];
            let loopIndex = 0;
            let dataPie = [];
            let ranap = [];
            let rajal = [];
            let rehab = [];
            let igd = [];
            let lab = [];
            let rad = [];
            for (let i in this.dataPenjadwalan.data) {
                categories.push(this.dataPenjadwalan.data[i].keterangan);
                let dataz2 = [];
                let dataRajal = [];
                let dataRanap = [];
                let dataRehab = [];
                let dataIGD = [];
                let dataLab = [];
                let dataRad = [];
                dataz2.push({
                    y: parseFloat(this.dataPenjadwalan.data[i].jumlah),
                    color: this.colors[i]
                });
                dataRajal.push({
                    y: parseFloat(this.dataPenjadwalan.data[i].rawatjalan),
                    color: this.colors[i]
                });
                dataRanap.push({
                    y: parseFloat(this.dataPenjadwalan.data[i].rawat_inap),
                    color: this.colors[i]
                });
                dataRehab.push({
                    y: parseFloat(this.dataPenjadwalan.data[i].rehab_medik),
                    color: this.colors[i]
                });
                dataIGD.push({
                    y: parseFloat(this.dataPenjadwalan.data[i].igd),
                    color: this.colors[i]
                });
                dataLab.push({
                    y: parseFloat(this.dataPenjadwalan.data[i].laboratorium),
                    color: this.colors[i]
                });
                dataRad.push({
                    y: parseFloat(this.dataPenjadwalan.data[i].radiologi),
                    color: this.colors[i]
                });
                // asupkeun kabeh data

                dataPie.push([
                    this.dataPenjadwalan.data[i].keterangan,
                    parseFloat(this.dataPenjadwalan.data[i].jumlah)
                ]);
                series.push({
                    name: this.dataPenjadwalan.data[i].keterangan,
                    data: dataz2
                });
                rajal.push({
                    name: this.dataPenjadwalan.data[i].keterangan,
                    data: dataRajal
                });
                ranap.push({
                    name: this.dataPenjadwalan.data[i].keterangan,
                    data: dataRanap
                });
                igd.push({
                    name: this.dataPenjadwalan.data[i].keterangan,
                    data: dataIGD
                });
                rehab.push({
                    name: this.dataPenjadwalan.data[i].keterangan,
                    data: dataRehab
                });
                lab.push({
                    name: this.dataPenjadwalan.data[i].keterangan,
                    data: dataLab
                });
                rad.push({
                    name: this.dataPenjadwalan.data[i].keterangan,
                    data: dataRad
                });
                //end  asupkeun kabeh data

            }
            // console.log(dataPie);
            Highcharts.chart('chartJenisPenjadwalanPie', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45
                    }
                },
                title: {
                    text: ''
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },
                plotOptions: {
                    pie: {
                        innerSize: 100,
                        depth: 45,
                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },

                series: [{
                    name: 'Jumlah Kunjungan',
                    data: dataPie
                    // [
                    //     ['Bananas', 8],
                    //     ['Kiwi', 3],
                    //     ['Mixed nuts', 1],
                    //     ['Oranges', 6],
                    //     ['Apples', 8],
                    //     ['Pears', 4],
                    //     ['Clementines', 4],
                    //     ['Reddish (bag)', 1],
                    //     ['Grapes (bunch)', 1]
                    // ]
                }]
            })

            // line
            Highcharts.chart('chartJenisPenjadwalanLine', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: ['Jumlah'],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },

                series: series,
                legend: {
                    borderRadius: 5,
                    borderWidth: 1,
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },
                // responsive: {
                //     rules: [{
                //         condition: {
                //             maxWidth: 500
                //         },
                //         chartOptions: {
                //             legend: {
                //                 layout: 'horizontal',
                //                 align: 'center',
                //                 verticalAlign: 'bottom'
                //             }
                //         }
                //     }]
                // }
            })
            // rawat jalan
            Highcharts.chart('chartCaraDaftarRajal', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: ['Jumlah'],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },

                series: rajal,
                legend: {
                    borderRadius: 5,
                    borderWidth: 1,
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

            })
            // end rajal
            // rawat inap
            Highcharts.chart('chartCaraDaftarRanap', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: ['Jumlah'],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },

                series: ranap,
                legend: {
                    borderRadius: 5,
                    borderWidth: 1,
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

            })
            // end ranap
            // rehab
            Highcharts.chart('chartCaraDaftarRehab', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: ['Jumlah'],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },

                series: rehab,
                legend: {
                    borderRadius: 5,
                    borderWidth: 1,
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

            })
            // end rehab
            // igd
            Highcharts.chart('chartCaraDaftarIgd', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: ['Jumlah'],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },

                series: igd,
                legend: {
                    borderRadius: 5,
                    borderWidth: 1,
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

            })
            // end igd
            // lab
            Highcharts.chart('chartCaraDaftarLab', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: ['Jumlah'],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },

                series: lab,
                legend: {
                    borderRadius: 5,
                    borderWidth: 1,
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

            })
            // end lab
            // rad
            Highcharts.chart('chartCaraDaftarRad', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: ['Jumlah'],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    column: {
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },

                series: rad,
                legend: {
                    borderRadius: 5,
                    borderWidth: 1,
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle'
                },

            })
            // end rad

            // end pie
        }

        function setChartAlamat() {

            this.chartAlamat =  @json($res['alamat']);


            let series = [];
            let jumlah = 0;
            let categories = []
            for (let i in this.chartAlamat) {
                categories.push(this.chartAlamat[i].namakotakabupaten);
                let dataz2 = [];
                dataz2.push({
                    y: parseFloat(this.chartAlamat[i].jumlah),
                    color: colorNyieun2()[i]
                });
                jumlah = jumlah + parseFloat(this.chartAlamat[i].jumlah);

                series.push({
                    name: this.chartAlamat[i].namakotakabupaten,
                    data: dataz2
                });


            }
            Highcharts.chart('chartAlamatPasien', {
                chart: {
                    type: 'column',
                },

                title: {
                    text: ''
                },
                xAxis: {
                    categories: ["Jumlah " + jumlah],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Pasien'
                    }
                },
                plotOptions: {
                    column: {
                        // url:"#",
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],

                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },
                tooltip: {
                    formatter: function () {
                        let point = this.point,
                            s = this.x + ':' + Highcharts.numberFormat(this.y, 0, '.', ',') + '<br/>';
                        return s;

                    }

                },
                series: series,
                exporting: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },
                colors:colorNyieun2()

            })

        }

        function setChartPerjenis() {
            this.datachartKunjungnJenisPasien =  @json($res['kunjungan_perjenispasien']);

            let dataz = [];
            let slice = true;
            let jmlPasien = 0;
            for (let i in this.datachartKunjungnJenisPasien.dataAll) {
                // let sum = _.reduce( this.datachartKunjungnJenisPasien[i],
                //     function (memo, num) {
                //         return memo + Number(num.SumPatient);
                //     }, 0);
                dataz.push({
                    name: this.datachartKunjungnJenisPasien.dataAll[i].kelompokpasien,
                    y: parseFloat(this.datachartKunjungnJenisPasien.dataAll[i].jumlah),
                    sliced: slice,
                    selected: slice,
                    color: colorNyieun2()[i]
                });
                slice = false;
                jmlPasien = jmlPasien + parseFloat(this.datachartKunjungnJenisPasien.dataAll[i].jumlah)
            }
            Highcharts.chart('chartKunjungnJenisPasien', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: '',

                },
                // tooltip: {
                //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                // },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function () {
                                return this.percentage.toFixed(2) + ' %';
                            }
                        },
                        showInLegend: true
                    },

                },
                credits: {
                    text: 'Total Pasien ' + jmlPasien,
                    // enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },
                series: [{
                    type: 'pie',
                    name: 'Persentase Kunjungan Pasien',
                    // colorByPoint: true,
                    data: dataz

                }]
            })

            let categoriesss = [];
            let bpjs = [];
            let asuransi = [];
            let umum = [];
            let perusahaan = [];
            let perjanjian = [];
            for (let i in this.datachartKunjungnJenisPasien.data) {
                categoriesss.push(this.datachartKunjungnJenisPasien.data[i].namadepartemen);
                bpjs.push(
                    parseFloat(this.datachartKunjungnJenisPasien.data[i].jmlBPJS)
                );
                asuransi.push(
                    parseFloat(this.datachartKunjungnJenisPasien.data[i].jmlAsuransiLain)
                );
                umum.push(
                    parseFloat(this.datachartKunjungnJenisPasien.data[i].jmlUmum)
                );
                perusahaan.push(
                    parseFloat(this.datachartKunjungnJenisPasien.data[i].jmlPerusahaan)
                );
                perjanjian.push(
                    parseFloat(this.datachartKunjungnJenisPasien.data[i].jmlPerjanjian)
                );
            }
            Highcharts.chart('chartDetailKelompokPasien', {
                chart: {
                    type: 'column'
                },

                title: {
                    text: ''
                },

                xAxis: {
                    categories: categoriesss//['REGULER', 'EKSEKUTIF']
                },

                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: 'Jumlah Pasien'
                    }
                },

                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '<br/>' +
                            'Total: ' + this.point.stackTotal;
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: colors()[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',');
                            }
                        },
                        showInLegend: true
                    }
                },
                // plotOptions: {
                //     column: {
                //         stacking: 'normal'
                //     }
                // },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    reversed: false,
                    borderRadius: 5,
                    borderWidth: 1
                },
                series: [
                    {
                        name: 'BPJS',
                        data: bpjs
                    },
                    {
                        name: 'Umum/Pribadi',
                        data: umum
                    },
                    {
                        name: 'Perusahaan',
                        data: perusahaan
                    },
                    {
                        name: 'Asuransi Lain',
                        data: asuransi
                    },
                    {
                        name: 'Perjanjian',
                        data: perjanjian
                    }

                ]
            })
        }

        function setChartJenisPel() {
            this.dataJenisPelayanan =  @json($res['jenis_pel']);
            let categories = [];
            let dataz2 = [];
            let dataz1 = [];
            for (let i in this.dataJenisPelayanan.data) {
                categories.push(this.dataJenisPelayanan.data[i].namadepartemen);
                dataz2.push({
                    y: parseFloat(this.dataJenisPelayanan.data[i].reguler),
                    color: colorNyieun2()[7]
                });
                dataz1.push({
                    y: parseFloat(this.dataJenisPelayanan.data[i].eksekutif),
                    color: colorNyieun2()[13]
                });
            }
            Highcharts.chart('chartJenisPelayanan', {
                chart: {
                    type: 'column'
                },

                title: {
                    text: ''
                },

                xAxis: {
                    categories: categories//['REGULER', 'EKSEKUTIF']
                },

                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: 'Jumlah Pasien'
                    }
                },

                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br/>' +
                            this.series.name + ': ' + this.y + '<br/>' +
                            'Total: ' + this.point.stackTotal;
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: colors()[1],
                            style: {
                                fontWeight: 'none'
                            },
                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',');
                            }
                        },
                        showInLegend: true
                    }
                },
                // plotOptions: {
                //     column: {
                //         stacking: 'normal'
                //     }
                // },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    reversed: false,
                    borderRadius: 5,
                    borderWidth: 1
                },
                series: [
                    {
                        name: 'REGULER',
                        data: dataz2,
                        color: colorNyieun2()[7]
                    },
                    {
                        name: 'EKSEKUTIF',
                        data: dataz1,
                        color: colorNyieun2()[13]
                    }

                ]
            })
        }

        function setChartPerujuk() {
            this.data10PerujukBpjs = @json($res['perujuk_bpjs']);

            let pie1 = 2;
            let series = [];
            let categories = [];
            let loopIndex = 0;
            for (let i in this.data10PerujukBpjs) {
                categories.push(this.data10PerujukBpjs[i].ppkrujukan);
                let dataz2 = [];
                dataz2.push({
                    y: parseFloat(this.data10PerujukBpjs[i].jumlah),
                    color: colorNyieun2()[i]
                });
                if (loopIndex < 10)
                    series.push({
                        name: this.data10PerujukBpjs[i].ppkrujukan,
                        data: dataz2
                    });
                loopIndex++;

            }
            Highcharts.chart('chart10PerujukBpjs', {
                chart: {
                    type: 'column',
                },

                title: {
                    text: ''
                },
                xAxis: {
                    categories: ["Jumlah "],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                plotOptions: {
                    column: {
                        // url:"#",
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],

                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },
                tooltip: {
                    formatter: function () {
                        let point = this.point,
                            s = this.x + ':' + Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien <br/>';
                        return s;

                    }
                    // headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    // pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    //     '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
                    // footerFormat: '</table>',
                    // shared: true,
                    // useHTML: true
                },
                series: series,
                exporting: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },

            })
        }

        function setChartTopDPJP() {
            //debugger
            this.dataTopDPJP = @json($res['toptendpjp']);

            let series = [];
            let categories = [];
            let loopIndex = 0;
            for (let i in this.dataTopDPJP) {
                categories.push(this.dataTopDPJP[i].nama);
                let dataz2 = [];
                dataz2.push({
                    y: parseFloat(this.dataTopDPJP[i].jml),
                    color: colorNyieun2()[i]
                });
                if (loopIndex < 10)
                    series.push({
                        name: this.dataTopDPJP[i].nama,
                        data: dataz2,
                        color: colorNyieun2()[i]
                    });
                loopIndex++;

            }
            Highcharts.chart('chartTopDPJP', {
                chart: {
                    type: 'column',
                },

                title: {
                    text: ''
                },
                xAxis: {
                    categories: ["Jumlah "],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                plotOptions: {
                    column: {
                        // url:"#",
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],

                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },
                tooltip: {
                    formatter: function () {
                        let point = this.point,
                            s = this.x + ':' + Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien <br/>';
                        return s;

                    }

                },
                series: series,
                exporting: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },

            })
        }

        function setChartTopPoli() {

            this.dataTopPoli = @json($res['toptenpoli']);

            let series = [];
            let categories = [];
            let loopIndex = 0;
            for (let i in this.dataTopPoli) {
                categories.push(this.dataTopPoli[i].nama);
                let dataz2 = [];
                dataz2.push({
                    y: parseFloat(this.dataTopPoli[i].jml),
                    color: colorNyieun2()[i]
                });
                if (loopIndex < 10)
                    series.push({
                        name: this.dataTopPoli[i].nama,
                        data: dataz2,
                        color: colorNyieun2()[i]
                    });
                loopIndex++;

            }
            Highcharts.chart('chartTopPoli', {
                chart: {
                    type: 'column',
                },

                title: {
                    text: ''
                },
                xAxis: {
                    categories: ["Jumlah "],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                plotOptions: {
                    column: {
                        // url:"#",
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],

                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },
                tooltip: {
                    formatter: function () {
                        let point = this.point,
                            s = this.x + ':' + Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien <br/>';
                        return s;

                    }

                },
                series: series,
                exporting: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },

            })
        }

        function setChartRanap() {
            this.dataKunjunganRanap =@json($res['ranap']);

            let series = [];
            let jumlah = 0;
            let categories = []
            for (let i in this.dataKunjunganRanap) {
                categories.push(this.dataKunjunganRanap[i].namaruangan);
                let dataz2 = [];
                dataz2.push({
                    y: parseFloat(this.dataKunjunganRanap[i].jumlah),
                    color: colorNyieun2()[i]
                });
                jumlah = jumlah + parseFloat(this.dataKunjunganRanap[i].jumlah);
                // if (loopIndex > 0)
                series.push({
                    name: this.dataKunjunganRanap[i].namaruangan,
                    data: dataz2
                });
                // loopIndex++;

            }
            Highcharts.chart('chartKunjunganRanap', {
                chart: {
                    type: 'column',
                },

                title: {
                    text: ''
                },
                xAxis: {
                    categories: ["Jumlah " + jumlah],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Jumlah Pasien'
                    }
                },
                plotOptions: {
                    column: {
                        // url:"#",
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],

                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },
                tooltip: {
                    formatter: function () {
                        let point = this.point,
                            s = this.x + ':' + Highcharts.numberFormat(this.y, 0, '.', ',') + '<br/>';
                        return s;

                    }

                },
                series: series,
                exporting: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },

            })
        }

        function setChartJK() {

            this.dataChartRanapJK =  @json($res['ranapjk']);

            let dataz = [];
            let slice = true;
            let jmlPasien = 0;
            for (let i in this.dataChartRanapJK) {

                dataz.push({
                    name: this.dataChartRanapJK[i].jeniskelamin,
                    y: parseFloat(this.dataChartRanapJK[i].jumlah),
                    sliced: slice,
                    selected: slice,
                    color: colorNyieun2()[i]
                });
                slice = false;
                jmlPasien = jmlPasien + parseFloat(this.dataChartRanapJK[i].jumlah)
            }
            Highcharts.chart('chartRanapJK', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: '',

                },
                // tooltip: {
                //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                // },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            formatter: function () {
                                return this.percentage.toFixed(2) + ' %';
                            }
                        },
                        showInLegend: true
                    },

                },
                credits: {
                    text: 'Total Pasien ' + jmlPasien,
                    // enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },
                series: [{
                    type: 'pie',
                    name: 'Persentase Kunjungan Pasien',
                    // colorByPoint: true,
                    data: dataz

                }]
            })
        }

        function setChartDiag() {
            this.data10Diagnosa = @json($res['toptendiag']);
            if (this.data10Diagnosa.length > 0) {
                getDataMap(this.data10Diagnosa[0].kd, this.data10Diagnosa[0].namadiagnosa)
            }

            let pie1 = 2;
            let series = [];
            let categories = [];
            let loopIndex = 0;
            for (let i in this.data10Diagnosa) {
                categories.push(this.data10Diagnosa[i].kddiagnosa);
                let dataz2 = [];
                dataz2.push({
                    y: parseFloat(this.data10Diagnosa[i].jumlah),
                    color: colorNyieun2()[i]
                });
                if (loopIndex < 10)
                    series.push({
                        name: this.data10Diagnosa[i].kddiagnosa,
                        data: dataz2
                    });
                loopIndex++;

            }
            Highcharts.chart('chart10Diagnosa', {
                chart: {
                    type: 'column',
                },

                title: {
                    text: '-'
                },
                xAxis: {
                    categories: ["Jumlah "],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                plotOptions: {
                    column: {
                        // url:"#",
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],

                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },
                tooltip: {
                    formatter: function () {
                        let point = this.point,
                            s = this.x + ':' + Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien <br/>';
                        return s;

                    }
                    // headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    // pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    //     '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
                    // footerFormat: '</table>',
                    // shared: true,
                    // useHTML: true
                },
                series: series,
                exporting: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },

            })
        }

        function setChartDiagKec(data) {
            if (data == null)
                this.data10Diagnosa = ''; // {{-- @json($res['diagnosakec']);--}}
            else
            this.data10Diagnosa = data

            // console.log(this.data10Diagnosa)
            let pie1 = 2;
            let series = [];
            let categories = [];
            let loopIndex = 0;
            for (let i in this.data10Diagnosa) {
                categories.push(this.data10Diagnosa[i].kddiagnosa);
                let dataz2 = [];
                dataz2.push({
                    y: parseFloat(this.data10Diagnosa[i].jumlah),
                    color: colors()[i]
                });
                if (loopIndex < 10)
                    series.push({
                        name: this.data10Diagnosa[i].kddiagnosa,
                        data: dataz2
                    });
                loopIndex++;

            }
            Highcharts.chart('chartDiagnosaKec', {
                chart: {
                    type: 'column',
                },

                title: {
                    text: '-'
                },
                xAxis: {
                    categories: ["Jumlah "],
                    labels: {
                        align: 'center',
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Kunjungan Pasien'
                    }
                },
                plotOptions: {
                    column: {
                        // url:"#",
                        cursor: 'pointer',

                        dataLabels: {
                            enabled: true,
                            color: this.colors[1],

                            formatter: function () {
                                return Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien';
                            }
                        },
                        showInLegend: true
                    }
                },
                tooltip: {
                    formatter: function () {
                        let point = this.point,
                            s = this.x + ':' + Highcharts.numberFormat(this.y, 0, '.', ',') + ' Pasien <br/>';
                        return s;

                    }
                    // headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    // pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    //     '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
                    // footerFormat: '</table>',
                    // shared: true,
                    // useHTML: true
                },
                series: series,
                exporting: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: true,
                    borderRadius: 5,
                    borderWidth: 1
                },

            })
        }


        $("#comboDiagnosa").on('select2:select', function () {
            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();
            var selectedDiagnosa = $(this).select2('data')
            var kddiagnosa = selectedDiagnosa[0].id
            var namaDiagnosa = selectedDiagnosa[0].text
            var arrNama = namaDiagnosa.split('-')

            $.ajax({
                type: 'GET',
                url: APP_URL + '/get-diagnosa-bykode-byrsaddress/' + kddiagnosa,
                cache: false,
                data: {tglawal: tglawal, tglakhir: tglakhir},
                success: function (respond) {
                    let nama = ''
                    if (arrNama.length > 0) {
                        nama = arrNama[1]
                    }
                    setMapData(respond, kddiagnosa, nama);
                }
            });
        })

        function getDataMap(kddiagnosa, namadiagnosa) {
            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();

            $.ajax({
                type: 'GET',
                url: APP_URL + '/get-diagnosa-bykode-byrsaddress/' + kddiagnosa,
                cache: false,
                data: {tglawal: tglawal, tglakhir: tglakhir},
                success: function (respond) {
                    // dataMapAwal = respond
                    setMapData(respond, kddiagnosa, namadiagnosa)
                }
            });

            // setMapLeaflet(kddiagnosa)
        }

        function getKecamatan(code) {
            $.ajax({
                type: 'GET',
                url: APP_URL + '/get-kec-by-prov',
                cache: false,
                data: {kdmap: code},
                success: function (responsd) {

                    $('#tBodyDemog').empty();
                    var trHTML = "";

                    $.each(responsd, function (i, item) {
                        trHTML += "<tr><td class='klikKec' data-kd-kec='" + item.kecid + "'  style='cursor:pointer' onclick='clickTR(" + item.kecid + ")' >" + item.namakecamatan
                            + "</td></tr> ";
                    });
                    $('#tableDemog').append(trHTML);
                }
            });
        }

        function clickTR(code) {
            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();

            $.ajax({
                type: 'GET',
                url: APP_URL + '/get-top-diagnosa-by-kec',
                cache: false,
                data: {id: code, tglawal: tglawal, tglakhir: tglakhir},
                success: function (res) {
                    setChartDiagKec(res)
                }
            });
        }

        function setMapData(dataSource, kddiagnosa, namadiagnosa) {
            $('#comboDiagnosa').val(kddiagnosa).trigger('change');

            $('#world-map-markers').empty();
            var gdpData2 = []
            if (dataSource.length != undefined && dataSource.length > 0) {
                dataSource.forEach(function (item, index) {
                    gdpData2[item.kdmap] = item.jumlah;
                })
            } else {
                gdpData2 = dataSource
            }

            $('#world-map-markers').vectorMap({
                map: 'indonesia-adm1_merc',
                backgroundColor: 'transparent',
                normalizeFunction: 'polynomial',
                hoverOpacity: 0.9,
                hoverColor: true,
                onRegionClick: function (e, code) {
                    if (gdpData2[code] != undefined) {
                        getKecamatan(code)
                    } else {
                        getKecamatan(code)
                        // alert('Data Tidak ada')
                        // return
                    }
                },

                regionStyle: {
                    initial: {
                        fill: 'rgba(210, 214, 222, 1)',
                        'fill-opacity': 0.7,
                        stroke: 'none',
                        'stroke-width': 0,
                        'stroke-opacity': 1
                    },
                    hover: {
                        'fill-opacity': 0.6,
                        cursor: 'pointer'
                    },
                    selected: {
                        fill: "#2A3F52"
                    }
                },
                series: {
                    regions: [{
                        values: gdpData2,
                        scale: ['#c7b3b3', '#c99f9f', '#c98585', '#fcacac', '#fa9393', '#fa6969', '#fa4848', '#f72323', '#990a00'],
                        normalizeFunction: 'polynomial'
                    }]
                },
                onRegionTipShow: function (e, el, code) {
                    var jml = 0
                    if (gdpData2[code] != undefined) {
                        jml = gdpData2[code]
                    }
                    el.html(el.html() + ' (' + kddiagnosa + '-' + namadiagnosa + ' : ' + jml + ')');
                }
            });
        }

        function klikDetails(kode, title, jenis) {
            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();
            $.ajax({
                type: 'GET',
                url: APP_URL + '/get-detail-kunjungan-pasien',
                data: {tglawal: tglawal, tglakhir: tglakhir, id: kode, jenis: jenis, title: title},
                cache: false,
                success: function (respond) {
                    document.getElementById("titleModalKun").innerHTML = title
                    $('#modalKunjungan').modal("show");
                    $("#load_kunjungan").html(respond);
                }
            })
        }

        function getDataMapPasien(kddiagnosa, namadiagnosa) {
            var tglawal = $("#tglawal").val();
            var tglakhir = $("#tglakhir").val();

            $.ajax({
                type: 'GET',
                url: APP_URL + '/get-diagnosa-bykode-byrsaddress/' + kddiagnosa,
                cache: false,
                data: {tglawal: tglawal, tglakhir: tglakhir},
                success: function (respond) {
                    // dataMapAwal = respond
                    setMapData(respond, kddiagnosa, namadiagnosa)
                }
            });

            // setMapLeaflet(kddiagnosa)
        }

        getDataMapPasien()

        function setMapPasienLama(dataSource, kddiagnosa, namadiagnosa) {
            $('#comboDiagnosa').val(kddiagnosa).trigger('change');

            $('#world-map-markers').empty();
            var gdpData2 = []
            if (dataSource.length != undefined && dataSource.length > 0) {
                dataSource.forEach(function (item, index) {
                    gdpData2[item.kdmap] = item.jumlah;
                })
            } else {
                gdpData2 = dataSource
            }

            $('#world-map-markers').vectorMap({
                map: 'indonesia-adm1_merc',
                backgroundColor: 'transparent',
                normalizeFunction: 'polynomial',
                hoverOpacity: 0.9,
                hoverColor: true,
                onRegionClick: function (e, code) {
                    if (gdpData2[code] != undefined) {
                        getKecamatan(code)
                    } else {
                        getKecamatan(code)
                        // alert('Data Tidak ada')
                        // return
                    }
                },

                regionStyle: {
                    initial: {
                        fill: 'rgba(210, 214, 222, 1)',
                        'fill-opacity': 0.7,
                        stroke: 'none',
                        'stroke-width': 0,
                        'stroke-opacity': 1
                    },
                    hover: {
                        'fill-opacity': 0.6,
                        cursor: 'pointer'
                    },
                    selected: {
                        fill: "#2A3F52"
                    }
                },
                series: {
                    regions: [{
                        values: gdpData2,
                        scale: ['#c7b3b3', '#c99f9f', '#c98585', '#fcacac', '#fa9393', '#fa6969', '#fa4848', '#f72323', '#990a00'],
                        normalizeFunction: 'polynomial'
                    }]
                },
                onRegionTipShow: function (e, el, code) {
                    var jml = 0
                    if (gdpData2[code] != undefined) {
                        jml = gdpData2[code]
                    }
                    el.html(el.html() + ' (' + kddiagnosa + '-' + namadiagnosa + ' : ' + jml + ')');
                }
            });
        }
    </script>

@endsection
