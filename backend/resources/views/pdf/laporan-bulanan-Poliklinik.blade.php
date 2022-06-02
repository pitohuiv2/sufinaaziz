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
<center><h3>LAPORAN BULANAN POLIKLINIK
        <br>{{ $dataReport['namaBulan'] }}
    </h3></center>

<br>

<table class="custom-table" style="width: 100%">
    <thead>
    <tr>
        <th rowspan="2" style="width: 45px">No</th>
        <th rowspan="2" style="width: 150px">Kode Penyakit</th>
        <th rowspan="2" style="width: 210px">Nama Penyakit</th>
        <th colspan="2" style="width: 100px">Status Pasien</th>
        <th colspan="2" style="width: 120px">Cara Bayar</th>
        <th colspan="5" style="width: 250px">Klasifikasi Berdasarkan Usia</th>
        <th colspan="2" style="width: 100px">Jenis Kelamin</th>
        <th colspan="3" style="width: 150px">Cara Keluar</th>
        <th rowspan="2" style="width: 120px">Jumlah</th>
    </tr>
    <tr>
        <th style="text-align: center;width: 50px">B</th>
        <th style="text-align: center;width: 50px">L</th>
        <th style="text-align: center;width: 60px">Umum</th>
        <th style="text-align: center;width: 60px">Bpjs</th>
        <th style="text-align: center;width: 50px">0-1 Th</th>
        <th style="text-align: center;width: 50px">2 Th - 10 Th</th>
        <th style="text-align: center;width: 50px">11 th- 17 th</th>
        <th style="text-align: center;width: 50px">18 Th-60 Th</th>
        <th style="text-align: center;width: 50px">> 60 Th</th>
        <th style="text-align: center;width: 50px">L</th>
        <th style="text-align: center;width: 50px">P</th>
        <th style="text-align: center;width: 50px">RJK</th>
        <th style="text-align: center;width: 50px">Rawat</th>
        <th style="text-align: center;width: 50px">Pulang</th>
    </tr>
    </thead>
    <tbody>
    @php
        $i=1; $temp=0; $ru =''; $jmlL=0; $jmlP = 0; $jmlBaru=0; $jmlLama = 0;
        $jmlUmum = 0; $jmlBpjs = 0;
        $jmlU=0; $jmlU1 = 0; $jmlU2=0; $jmlU3 = 0; $jmlU4 = 0;
        $jmlRJK=0; $jmlRawat = 0; $jmlPulang=0; $jmlAll = 0;
    @endphp
    @foreach($dataReport['data'] as $d)
        <?php $jmlL = $jmlL + (float)$d->laki; ?>
        <?php $jmlP = $jmlP + (float)$d->cwe; ?>
        <?php $jmlBaru = $jmlBaru + (float)$d->baru; ?>
        <?php $jmlLama = $jmlLama + (float)$d->lama; ?>
        <?php $jmlUmum = $jmlUmum + (float)$d->umum; ?>
        <?php $jmlBpjs = $jmlBpjs + (float)$d->bpjs; ?>
        <?php $jmlU = $jmlU + (float)$d->nolsatu; ?>
        <?php $jmlU1 = $jmlU1 + (float)$d->duasepuluh; ?>
        <?php $jmlU2 = $jmlU2 + (float)$d->tujuhbelas; ?>
        <?php $jmlU3 = $jmlU3 + (float)$d->delapanbelas; ?>
        <?php $jmlU4 = $jmlU4 + (float)$d->enampuluh; ?>
        <?php $jmlRJK = $jmlRJK + (float)$d->rujuk; ?>
        <?php $jmlRawat = $jmlRawat + (float)$d->dirawat; ?>
        <?php $jmlPulang = $jmlPulang + (float)$d->pulang; ?>
        <?php $jmlAll = $jmlAll + (float)$d->jmldg; ?>
        <tr>
            <td align="center">{{ $i++ }}</td>
            <td align="left">{{ $d->kddiagnosa }}</td>
            <td align="left">{{ $d->namadiagnosa }}</td>
            <td align="center">{{ $d->baru }}</td>
            <td align="center">{{ $d->lama }}</td>
            <td align="center">{{ $d->umum }}</td>
            <td align="center">{{ $d->bpjs }}</td>
            <td align="center">{{ $d->nolsatu }}</td>
            <td align="center">{{ $d->duasepuluh }}</td>
            <td align="center">{{ $d->tujuhbelas }}</td>
            <td align="center">{{ $d->delapanbelas }}</td>
            <td align="center">{{ $d->enampuluh }}</td>
            <td align="center">{{ $d->laki }}</td>
            <td align="center">{{ $d->cwe }}</td>
            <td align="center">{{ $d->rujuk }}</td>
            <td align="center">{{ $d->dirawat }}</td>
            <td align="center">{{ $d->pulang }}</td>
            <td align="center">{{ $d->jmldg }}</td>
        </tr>
    @endforeach
        <tr>
            <th colspan="3" style="width: 100px">Total</th>
            <th align="center" style="width: 50px">{{$jmlBaru}}</th>
            <th align="center" style="width: 50px">{{$jmlLama}}</th>
            <th align="center" style="width: 50px">{{$jmlUmum}}</th>
            <th align="center" style="width: 50px">{{$jmlBpjs}}</th>
            <th align="center" style="width: 50px">{{$jmlU}}</th>
            <th align="center" style="width: 50px">{{$jmlU1}}</th>
            <th align="center" style="width: 50px">{{$jmlU2}}</th>
            <th align="center" style="width: 50px">{{$jmlU3}}</th>
            <th align="center" style="width: 50px">{{$jmlU4}}</th>
            <th align="center" style="width: 50px">{{$jmlL}}</th>
            <th align="center" style="width: 50px">{{$jmlP}}</th>
            <th align="center" style="width: 50px">{{$jmlRJK}}</th>
            <th align="center" style="width: 50px">{{$jmlRawat}}</th>
            <th align="center" style="width: 50px">{{$jmlPulang}}</th>
            <th align="center" style="width: 50px">{{$jmlAll}}</th>
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
