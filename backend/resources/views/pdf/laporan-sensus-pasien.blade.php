<!DOCTYPE html>
<html>
<head>
    <title>Report</title>
</head>
<body onLoad="window.print()">
<style type="text/css">

    body {
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
        {{$dataReport['namalengkap']}}<br />
        {{$dataReport['alamatlengkap']}}
    </span>
</left>
<hr />
<center><h3>LAPORAN JUMLAH PASIEN POLIKLINIK
        <br>PERIODE : {{ $dataReport['tglAwal'].' - '.$dataReport['tglAkhir'] }}
    </h3></center>

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
    @php $i=1; $temp=0; $ru =''; $jmlL=0; $jmlP = 0; $jmlBaru=0; $jmlLama = 0;
    @endphp
    @foreach($dataReport['data'] as $d)
        <?php $jmlL = $jmlL + (float)$d['jk_l']; ?>
        <?php $jmlP = $jmlP + (float)$d['jk_p']; ?>
        <?php $jmlBaru = $jmlBaru + (float)$d['baru']; ?>
        <?php $jmlLama = $jmlLama + (float)$d['lama']; ?>
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
            <td align="center">{{ $d['norm'] }}</td>
            <td align="left">{{ $d['namapasien'] }}</td>
            <td align="center">{{ $d['jk_l'] }}</td>
            <td align="center">{{ $d['jk_p'] }}</td>
            <td align="center">{{ $d['umur'] }}</td>
            <td align="left">{{ $d['alamatlengkap'] }}</td>
            <td align="center">{{ $d['kelompokpasien'] }}</td>
            <td align="center">{{ $d['baru'] }}</td>
            <td align="center">{{ $d['lama'] }}</td>
            <td align="center">{{ $d['tekanandarah'] }}</td>
            <td align="center">{{ $d['bb_tb'] }}</td>
            <td align="left">{{ $d['keluhan'] }}</td>
            <td align="left">{{ $d['kddiagnosa'] }}</td>
            <td align="left">{{ $d['terapi'] }}</td>
            <td align="center">{{ $d['statuspulang'] }}</td>
        </tr>
    @endforeach
        <tr>
            <th colspan="3" style="width: 100px">Total</th>
            <th align="center" style="width: 50px">{{$jmlL}}</th>
            <th align="center" style="width: 50px">{{$jmlP}}</th>
            <th colspan="3" style="width: 150px">&nbsp;</th>
            <th align="center" style="width: 50px">{{$jmlBaru}}</th>
            <th align="center" style="width: 50px">{{$jmlLama}}</th>
            <th colspan="6">&nbsp;</th>
        </tr>
    </tbody>
</table>
<table style="width: 100%;margin-top: 20px;" border="0">
    <tbody>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td  style="text-align: center">
            <span style="text-align: center">{{ $dataReport['ttd'] }}</span>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td  style="text-align: center">
            <span style="text-align: center">{{ $dataReport['namauser'] }}</span>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
