<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penerimaan Kasir</title>
</head>
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
<body onLoad="window.print()">
<left>
    <table  cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" border="0" width="1240">
        <tr>
            <td style="padding: 10px 30px 0px 30px;text-align: left" width="70%">
                <font style="font-size: 14pt;font-family: Tahoma" color="#000000" face="Tahoma">
                    <span style="text-align: center;text-transform:uppercase">
                        <b>{{$dataReport['namaprofile']}}</b>
                    </span>
                </font><br />
                <font style="font-size: 12pt;font-family: Tahoma" color="#000000" face="Tahoma">
                    {{$dataReport['alamat']}}
                    <p align="right">
                        <img src="{{ asset('img/KabCianjur.png') }}"
                             style="width: 200px" border="0"/>
                    </p>
                </font><br />
                <hr style="margin-top: 6px;border-style: solid" />
            </td>
        </tr>
    </table>
{{--    <h3>{{$dataReport['namaprofile']}}--}}
{{--        <br>{{$dataReport['alamat']}}--}}
{{--    </h3>--}}
</left>
<br>

</body>
</html>
