<!DOCTYPE html>
<html>
<head>
    <title>Laporan Sensus Poliklinik</title>
</head>
<body>
<style type="text/css">

    body {
        height: 1654px;
        width: 2339px;
        /* to centre page on screen*/
        margin-left: auto;
        margin-right: auto;
        color: #333;
        font-family: Tahoma, Helvetica, sans-serif;
    }

    table {
        border-collapse: collapse;
        font-size: 12px;
    }

    p {
        font-size: 13px;
    }

    .custom-table thead {
        background-color: #e1e1e1;
    }

    .custom-table tr > th, .custom-table tr > td {
        border: 1px solid #ccc;
        box-shadow: none;
        padding: 5px;
    }
    .custom-table-no-border thead {
        background-color: #e1e1e1;
    }

    .custom-table-no-border tr > th, .custom-table-no-border tr > td {
        box-shadow: none;
        padding: 5px;
    }

    .text-center {
        text-align: center;
    }

    .top-table {
        margin-bottom: 10px;
    }

    .top-table tr > td {
        padding: 3px 10px;
    }

</style>

<left>
    <span>
        {{$namalengkap}}<br />
        {{$alamatlengkap}}
    </span>
</left>
<hr />
<center>
    <h3>LAPORAN JUMLAH PASIEN POLIKLINIK
        <br>PERIODE : {{ $tglAwal.' - '.$tglAkhir }}
    </h3>
</center>

<br>

<table class="custom-table" style="width: 100%">
    <thead>
    <tr>
        <th rowspan="2" style="width: 45px">No</th>
        <th rowspan="2" style="width: 85px">No RM</th>
        <th rowspan="2" style="width: 180px">Nama Pasien</th>
        <th colspan="2" style="width: 100px">Jenis Kelamin</th>
        <th rowspan="2" style="width: 150px">Umur</th>
        <th rowspan="2" style="width: 250px">Alamat</th>
        <th rowspan="2" style="width: 150px">Cara Bayar</th>
        <th colspan="2" style="width: 120px">Kunjungan</th>
        <th rowspan="2" style="width: 100px">Tekanan Darah</th>
        <th rowspan="2" style="width: 100px">BB/TB</th>
        <th rowspan="2" style="width: 250px">Keluhan</th>
        <th rowspan="2" style="width: 150px">Diagnosa</th>
        <th rowspan="2" style="width: 250px">Therapy</th>
        <th rowspan="2" style="width: 120px">Status Pulang</th>
    </tr>
    <tr>
        <th style="text-align: center;width: 50px">L</th>
        <th style="text-align: center;width: 50px">P</th>
        <th style="text-align: center;width: 60px">Baru</th>
        <th style="text-align: center;width: 60px">Lama</th>
    </tr>
    </thead>
    <tbody>
    @php $i=1; $temp=0; $ru ='';
    @endphp
    @foreach($data as $d)
        @if($ru != $d['namaruangan'])
            @if(isset($d['namaruangan']))
                <tr>
                    <td align="left" colspan="13">
                        <b>{{ $d['namaruangan'] }}</b>
                    </td>
                </tr>
            @else
            @endif
            @php
                $ru =  $d['namaruangan'];
            @endphp
        @endif
        <tr>
            <td align="center">{{ $i++ }}</td>
            <td>{{ $d['norm'] }}</td>
            <td>{{ $d['namapasien'] }}</td>
            <td>{{ $d['jk_l'] }}</td>
            <td>{{ $d['jk_p'] }}</td>
            <td>{{ $d['umur'] }}</td>
            <td>{{ $d['alamatlengkap'] }}</td>
            <td>{{ $d['kelompokpasien'] }}</td>
            <td>{{ $d['baru'] }}</td>
            <td>{{ $d['lam'] }}</td>
            <td>{{ $d['tekanandarah'] }}</td>
            <td>{{ $d['bb_tb'] }}</td>
            <td>{{ $d['keluhan'] }}</td>
            <td>{{ $d['kddiagnosa'] }}</td>
            <td>{{ $d['terapi'] }}</td>
            <td>{{ $d['statuspulang'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
