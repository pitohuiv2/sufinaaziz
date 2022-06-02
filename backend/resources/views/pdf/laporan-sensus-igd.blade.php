<!DOCTYPE html>
<html>
<head>
    <title>Report</title>
</head>
<body onLoad="window.print()" style="background-color: white">
<style type="text/css">
    body {
        background: rgb(204,204,204);
        font-family: Tahoma, Helvetica, sans-serif;
    }
    page[size="A4"] {
        background: white;
        width: 21cm;
        height: 29.7cm;
        display: block;
        margin: 0 auto;
        margin-bottom: 0.5cm;
        box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
    }
    @media print {
        body, page[size="A4"] {
            margin: 0;
            box-shadow: 0;
        }
    /*body {*/
    /*    color: #333;*/
    /*    font-family: Tahoma, Helvetica, sans-serif;*/
    /*}*/

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
<center><h3>LAPORAN SENSUS HARIAN PASIEN IGD UMUM
        <br>PERIODE : {{ $dataReport['tglAwal'].' - '.$dataReport['tglAkhir'] }}
    </h3></center>
<br>

<table class="custom-table" style="width: 100%;">
    <thead>
        <tr>
            <th rowspan="2" style="width: 45px">No</th>
            <th colspan="3" style="text-align: center;width: 150px">Pasien Masuk</th>
            <th colspan="2" style="text-align: center;width: 100px">Status</th>
            <th rowspan="2" style="text-align: center;width: 220px">Nama</th>
            <th rowspan="2" style="text-align: center;width: 210px">Umur</th>
            <th rowspan="2" style="text-align: center;width: 250px">Alamat (Kp/Desa)</th>
            <th rowspan="2" style="text-align: center;width: 200px">Diagnosa</th>
            <th colspan="7" style="text-align: center;width: 350px">Ruang Inap</th>
            <th rowspan="2" style="text-align: center;width: 200px">Diizinkan Pulang  </th>
            <th rowspan="2" style="text-align: center;width: 200px">Rujuk</th>
            <th rowspan="2" style="text-align: center;width: 200px">Lari</th>
            <th rowspan="2" style="text-align: center;width: 200px">Pulpak</th>
            <th colspan="4" style="text-align: center;width: 200px">Mati</th>
        </tr>
        <tr>
            <th style="text-align: center;width: 50px">Poli</th>
            <th style="text-align: center;width: 50px">Rujukan</th>
            <th style="text-align: center;width: 50px">Dtng Sndr </th>
            <th style="text-align: center;width: 50px">Umum</th>
            <th style="text-align: center;width: 50px">BPJS</th>

            <th style="text-align: center;width: 45px">DL</th>
            <th style="text-align: center;width: 45px">DP</th>
            <th style="text-align: center;width: 45px">OK</th>
            <th style="text-align: center;width: 45px">Neuro</th>
            <th style="text-align: center;width: 45px">Bedah</th>
            <th style="text-align: center;width: 45px">ISO</th>
            <th style="text-align: center;width: 45px">R. Anak</th>

            <th style="text-align: center;width: 50px">DOA</th>
            <th style="text-align: center;width: 50px"> < 6 Jam </th>
            <th style="text-align: center;width: 50px">6-48jam</th>
            <th style="text-align: center;width: 50px"> >48 jam</th>
        </tr>
    </thead>
    <tbody>
    @php $i=1; $temp=0; $ru ='';
    @endphp
    @foreach($dataReport['data'] as $d)
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
            <td align="center" style="width: 45px" >{{ $i++ }}</td>
            <td align="center" style="width: 50px">{{ $d['poli'] }}</td>
            <td align="center" style="width: 50px">{{ $d['rujukan'] }}</td>
            <td align="center" style="width: 50px">{{ $d['sendiri'] }}</td>
            <td align="center" style="width: 50px">{{ $d['umum'] }}</td>
            <td align="center" style="width: 50px">{{ $d['bpjs'] }}</td>
            <td align="left" style="width: 2000px">{{ $d['namapasien'] }}</td>
            <td align="center" style="width: 210px">{{ $d['umur'] }}</td>
            <td align="left" style="width: 250px">{{ $d['alamatlengkap'] }}</td>
            <td align="center" style="width: 200px">{{ $d['kddiagnosa'] }}</td>
            <td align="center" style="width: 45px">{{ $d['dl'] }}</td>
            <td align="center" style="width: 45px">{{ $d['dp'] }}</td>
            <td align="center" style="width: 45px">{{ $d['ok'] }}</td>
            <td align="center" style="width: 45px">{{ $d['bedah'] }}</td>
            <td align="center" style="width: 45px">{{ $d['neuro'] }}</td>
            <td align="center" style="width: 45px">{{ $d['iso'] }}</td>
            <td align="center" style="width: 45px">{{ $d['anak'] }}</td>
            <td align="center" style="width: 200px">{{ $d['diizinkan'] }}</td>
            <td align="center" style="width: 200px">{{ $d['dirujuk'] }}</td>
            <td align="center" style="width: 200px">{{ $d['lari'] }}</td>
            <td align="center" style="width: 200px">{{ $d['pulpak'] }}</td>
            <td align="center" style="width: 50px">{{ $d['doa'] }}</td>
            <td align="center" style="width: 50px">{{ $d['enamjam'] }}</td>
            <td align="center" style="width: 50px">{{ $d['duaempatjam'] }}</td>
            <td align="center" style="width: 50px">{{ $d['empatdelapanjam'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<table style="width: 100%;margin-top: 65px;" border="0">
    <tbody>
        <tr>
            <td>Pasien Masuk IGD </td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasienmasuk'] }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>DOA (Death On arrival)</td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasiendoa'] }}</td>
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
            <td>Pasien Pindah ke Ranap </td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasienranap'] }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Pasien dirujuk</td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasienrujuk'] }}</td>
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
                <span style="text-align: center">Kepala Ruangan IGD Umum</span>
            </td>
        </tr>
        <tr>
            <td>Pasien Diizinkan Pulang</td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasiendiizinkan'] }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Pasien Mati 6-48 Jam</td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasienduaempatjam'] }}</td>
        </tr>
        <tr>
            <td>Pasien dirujuk</td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasienrujuk'] }}</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Pasien Mati > 48 Jam</td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasienempatdelapanjam'] }}</td>
        </tr>
        <tr>
            <td>Pasien Lari</td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasienlari'] }}</td>
        </tr>
        <tr>
            <td>Pasien Pulpak</td>
            <td>:</td>
            <td>{{ $dataReport['jumlahpasienpulpak'] }}</td>
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
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td style="text-align: center">
                <span style="text-align: center"><u>{{ $dataReport['kepalaruanganigd'] }}</u></span>
            </td>
        </tr>
    </tbody>
</table>
</body>
</html>
