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
<center><h3>LAPORAN SENSUS BULANAN PASIEN IGD UMUM
        <br>{{ $dataReport['namabulan'] }}
    </h3></center>
<br>

<table class="custom-table" style="width: 100%;">
    <thead>
        <tr>
            <th rowspan="2" style="text-align: center;width: 45px">No</th>
            <th rowspan="2" style="text-align: center;width: 100px">Tgl Masuk</th>
            <th colspan="3" style="text-align: center;width: 150px">Pasien Masuk</th>
            <th colspan="2" style="text-align: center;width: 100px">Status</th>
            <th rowspan="2" style="text-align: center;width: 220px">Nama</th>
            <th rowspan="2" style="text-align: center;width: 210px">Umur</th>
            <th colspan="2" style="text-align: center;width: 100px">L/P</th>
            <th rowspan="2" style="text-align: center;width: 250px">Alamat (Kp/Desa)</th>
            <th rowspan="2" style="text-align: center;width: 100px">No RM</th>
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
            <th style="text-align: center;width: 50px">L</th>
            <th style="text-align: center;width: 50px">P</th>
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
    @php
        $i=1; $temp=0; $ru =''; $jmlPoli = 0; $jmlRujukan = 0; $jmlDtgSendiri = 0;
        $jmlBpjs = 0; $jmlUmum = 0; $jmlJKL = 0; $jmlJKP = 0;
        $jmlDL = 0; $jmlDP = 0; $jmlOk = 0; $jmlNeuro = 0; $jmlBedah = 0; $jmlIso = 0; $jmlAnak = 0;
        $jmlDiizinkan = 0; $jmlRujuk = 0; $jmlLari = 0; $jmlPulpak = 0;
        $jmlDoa = 0; $jmlMati1 = 0; $jmlMati2 = 0; $jmlMati3 = 0;
    @endphp
    @foreach($dataReport['data'] as $d)
        <?php $jmlPoli = $jmlPoli + (float)$d['poli']; ?>
        <?php $jmlRujukan = $jmlRujukan + (float)$d['rujukan']; ?>
        <?php $jmlDtgSendiri = $jmlDtgSendiri + (float)$d['sendiri']; ?>
        <?php $jmlBpjs = $jmlBpjs + (float)$d['bpjs']; ?>
        <?php $jmlUmum = $jmlUmum + (float)$d['umum']; ?>
        <?php $jmlJKL = $jmlJKL + (float)$d['jk_l']; ?>
        <?php $jmlJKP = $jmlJKP + (float)$d['jk_p']; ?>

        <?php $jmlDL = $jmlDL + (float)$d['dl']; ?>
        <?php $jmlDP = $jmlDP + (float)$d['dp']; ?>
        <?php $jmlOk = $jmlOk + (float)$d['ok']; ?>
        <?php $jmlBedah = $jmlBedah + (float)$d['bedah']; ?>
        <?php $jmlNeuro = $jmlNeuro + (float)$d['neuro']; ?>
        <?php $jmlIso = $jmlIso + (float)$d['iso']; ?>
        <?php $jmlAnak = $jmlAnak + (float)$d['anak']; ?>

        <?php $jmlDiizinkan = $jmlDiizinkan + (float)$d['diizinkan']; ?>
        <?php $jmlRujuk = $jmlRujuk + (float)$d['dirujuk']; ?>
        <?php $jmlLari = $jmlLari + (float)$d['lari']; ?>
        <?php $jmlPulpak = $jmlPulpak + (float)$d['pulpak']; ?>

        <?php $jmlDoa = $jmlDoa + (float)$d['doa']; ?>
        <?php $jmlMati1 = $jmlMati1 + (float)$d['enamjam']; ?>
        <?php $jmlMati2 = $jmlMati2 + (float)$d['duaempatjam']; ?>
        <?php $jmlMati3 = $jmlMati3 + (float)$d['empatdelapanjam']; ?>
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
            <td align="center" style="width: 50px">{{ $d['tglmasuk'] }}</td>
            <td align="center" style="width: 50px">{{ $d['poli'] }}</td>
            <td align="center" style="width: 50px">{{ $d['rujukan'] }}</td>
            <td align="center" style="width: 50px">{{ $d['sendiri'] }}</td>
            <td align="center" style="width: 50px">{{ $d['umum'] }}</td>
            <td align="center" style="width: 50px">{{ $d['bpjs'] }}</td>
            <td align="left" style="width: 2000px">{{ $d['namapasien'] }}</td>
            <td align="center" style="width: 210px">{{ $d['umur'] }}</td>
            <td align="center" style="width: 50px">{{ $d['jk_l'] }}</td>
            <td align="center" style="width: 50px">{{ $d['jk_p'] }}</td>
            <td align="left" style="width: 250px">{{ $d['alamatlengkap'] }}</td>
            <td align="center" style="width: 50px">{{ $d['norm'] }}</td>
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
        <tr>
            <th colspan="2" style="text-align: left;">Jumlah</th>
            <th style="text-align: center;">{{$jmlPoli}}</th>
            <th style="text-align: center;">{{$jmlRujukan}}</th>
            <th style="text-align: center;">{{$jmlDtgSendiri}}</th>
            <th style="text-align: center;">{{$jmlBpjs}}</th>
            <th style="text-align: center;">{{$jmlUmum}}</th>
            <th colspan="2" style="text-align: center;">&nbsp;</th>
            <th style="text-align: center;">{{$jmlJKL}}</th>
            <th style="text-align: center;">{{$jmlJKP}}</th>
            <th colspan="3" style="text-align: center;">&nbsp;</th>
            <th style="text-align: center;">{{$jmlDL}}</th>
            <th style="text-align: center;">{{$jmlDP}}</th>
            <th style="text-align: center;">{{$jmlOk}}</th>
            <th style="text-align: center;">{{$jmlBedah}}</th>
            <th style="text-align: center;">{{$jmlNeuro}}</th>
            <th style="text-align: center;">{{$jmlIso}}</th>
            <th style="text-align: center;">{{$jmlAnak}}</th>

            <th style="text-align: center;">{{$jmlDiizinkan}}</th>
            <th style="text-align: center;">{{$jmlRujuk}}</th>
            <th style="text-align: center;">{{$jmlLari}}</th>
            <th style="text-align: center;">{{$jmlPulpak}}</th>

            <th style="text-align: center;">{{$jmlDoa}}</th>
            <th style="text-align: center;">{{$jmlMati1}}</th>
            <th style="text-align: center;">{{$jmlMati2}}</th>
            <th style="text-align: center;">{{$jmlMati3}}</th>
        </tr>
    </tbody>
</table>
<table style="width: 100%;margin-top: 20px;" border="0">
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
        </tr>
    </tbody>
</table>
<table style="width: 100%;margin-top: 20px;" border="0">
    <tbody>
    <tr>
        <td  style="text-align: center">
            <span style="text-align: center">Mengetahui</span>
        </td>
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
        <td  style="text-align: center">
            <span style="text-align: center">Ka. Sie Keperawatan</span>
        </td>
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
        <td  style="text-align: center">
            <span style="text-align: center">&nbsp;</span>
        </td>
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
            <span style="text-align: center">&nbsp;</span>
        </td>
    </tr>
    <tr>
        <td  style="text-align: center">
            <span style="text-align: center">&nbsp;</span>
        </td>
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
            <span style="text-align: center">&nbsp;</span>
        </td>
    </tr>
    <tr>
        <td  style="text-align: center">
            <span style="text-align: center">&nbsp;</span>
        </td>
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
            <span style="text-align: center">&nbsp;</span>
        </td>
    </tr>
    <tr>
        <td  style="text-align: center">
            <span style="text-align: center">&nbsp;</span>
        </td>
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
            <span style="text-align: center">&nbsp;</span>
        </td>
    </tr>
    <tr>
        <td  style="text-align: center">
            <span style="text-align: center">
                <u>{{$dataReport['kepalainstalasiperawat']}}</u> <br />
                <u>NIP : {{$dataReport['nipkepalainstalasiperawat']}}</u>
            </span>
        </td>
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
            <span style="text-align: center"><u>{{$dataReport['kepalaruanganigd']}}</u></span>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
