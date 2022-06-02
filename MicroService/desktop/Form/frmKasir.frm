VERSION 5.00
Object = "{C4847593-972C-11D0-9567-00A0C9273C2A}#8.0#0"; "crviewer.dll"
Object = "{248DD890-BB45-11CF-9ABC-0080C7E7B78D}#1.0#0"; "MSWINSCK.OCX"
Begin VB.Form frmKasir 
   Caption         =   "Kasir"
   ClientHeight    =   7005
   ClientLeft      =   60
   ClientTop       =   405
   ClientWidth     =   5820
   Icon            =   "frmKasir.frx":0000
   LinkTopic       =   "Form1"
   ScaleHeight     =   7005
   ScaleWidth      =   5820
   WindowState     =   2  'Maximized
   Begin MSWinsockLib.Winsock Winsock1 
      Left            =   5040
      Top             =   6240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
   End
   Begin VB.CommandButton cmdOption 
      Caption         =   "Option"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   315
      Left            =   4920
      TabIndex        =   3
      Top             =   460
      Width           =   975
   End
   Begin VB.CommandButton cmdCetak 
      Caption         =   "Cetak"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   315
      Left            =   3960
      TabIndex        =   2
      Top             =   460
      Width           =   975
   End
   Begin VB.ComboBox cboPrinter 
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   315
      Left            =   960
      TabIndex        =   1
      Top             =   460
      Width           =   3015
   End
   Begin CRVIEWERLibCtl.CRViewer CRViewer1 
      Height          =   7005
      Left            =   0
      TabIndex        =   0
      Top             =   0
      Visible         =   0   'False
      Width           =   5805
      DisplayGroupTree=   -1  'True
      DisplayToolbar  =   -1  'True
      EnableGroupTree =   0   'False
      EnableNavigationControls=   -1  'True
      EnableStopButton=   -1  'True
      EnablePrintButton=   -1  'True
      EnableZoomControl=   -1  'True
      EnableCloseButton=   -1  'True
      EnableProgressControl=   -1  'True
      EnableSearchControl=   -1  'True
      EnableRefreshButton=   -1  'True
      EnableDrillDown =   -1  'True
      EnableAnimationControl=   -1  'True
      EnableSelectExpertButton=   -1  'True
      EnableToolbar   =   -1  'True
      DisplayBorder   =   -1  'True
      DisplayTabs     =   -1  'True
      DisplayBackgroundEdge=   -1  'True
      SelectionFormula=   ""
      EnablePopupMenu =   -1  'True
      EnableExportButton=   -1  'True
      EnableSearchExpertButton=   -1  'True
      EnableHelpButton=   -1  'True
   End
   Begin VB.PictureBox Picture1 
      AutoRedraw      =   -1  'True
      BackColor       =   &H00FFFFFF&
      Height          =   525
      Left            =   -600
      ScaleHeight     =   31
      ScaleMode       =   3  'Pixel
      ScaleWidth      =   421
      TabIndex        =   4
      TabStop         =   0   'False
      Top             =   1080
      Width           =   6375
   End
End
Attribute VB_Name = "frmKasir"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
'# VARIABEL CR #'
Dim crRincianBiayaDetail As New crRincianBiaya
Dim crRincianBiayaRekap As New crRekapRincianBiaya
Dim crRincianBiayaObat As New crRincianObat
Dim printLaporanPenerimaanKasir As New crLaporanPenerimaanKasir
'# VARIABEL FUNC #'
Dim bolRincianBiayaDetail As Boolean
Dim bolRekapRincian As Boolean
Dim bolRincianObat As Boolean
Dim bolLaporanPenerimaanKasir As Boolean
'# COMPONENT #'
Dim strPrinter As String
Dim strPrinter1 As String
Dim PrinterNama As String
Dim ii As Integer
Dim tempPrint1 As String
Dim p As Printer
Dim p2 As Printer
Dim strDeviceName As String
Dim strDriverName As String
Dim strPort As String
Dim adoReport As New ADODB.Command

Private Sub cmdCetak_Click()
    If cboPrinter.Text = "" Then MsgBox "Printer belum dipilih", vbInformation, ".: Information": Exit Sub
    If bolRincianBiayaDetail = True Then
        crRincianBiayaDetail.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crRincianBiayaDetail.PrintOut False
    ElseIf bolRekapRincian = True Then
        crRincianBiayaRekap.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crRincianBiayaRekap.PrintOut False
    ElseIf bolRincianObat = True Then
        crRincianBiayaObat.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crRincianBiayaObat.PrintOut False
    ElseIf bolLaporanPenerimaanKasir = True Then
        printLaporanPenerimaanKasir.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        printLaporanPenerimaanKasir.PrintOut False
    End If
    SaveSetting "SMART", "SettingPrinter", "cboPrinter", PrinterNama
End Sub

Private Sub CmdOption_Click()
    If bolRincianBiayaDetail = True Then
        crRincianBiayaDetail.PrinterSetup Me.hwnd
    ElseIf bolRekapRincian = True Then
        crRincianBiayaRekap.PrinterSetup Me.hwnd
    ElseIf bolRincianObat = True Then
        crRincianBiayaObat.PrinterSetup Me.hwnd
    ElseIf bolLaporanPenerimaanKasir = True Then
        printLaporanPenerimaanKasir.PrinterSetup Me.hwnd
    End If
    CRViewer1.Refresh
End Sub

Private Sub Form_Load()
    Dim p As Printer
    cboPrinter.Clear
    For Each p In Printers
        cboPrinter.AddItem p.DeviceName
    Next
    strPrinter = strPrinter1
    cboPrinter.Text = GetSetting("SMART", "SettingPrinter", "cboPrinter")
End Sub

Private Sub Form_Resize()
    CRViewer1.Top = 0
    CRViewer1.Left = 0
    CRViewer1.Height = ScaleHeight
    CRViewer1.Width = ScaleWidth
End Sub

Private Sub Form_Unload(Cancel As Integer)
    Set frmKasir = Nothing
End Sub

Public Sub CetakRincianDetail(strNoregistrasi As String, strIdPegawai As String, view As String)
On Error GoTo errLoad
On Error Resume Next
Set frmKasir = Nothing
bolRincianBiayaDetail = True
bolRekapRincian = False
bolRincianObat = False
bolLaporanPenerimaanKasir = False
Dim adocmd As New ADODB.Command
Dim StrFilter As String
StrFilter = ""
    
            
    strSQL = " SELECT tb.*, CASE WHEN tb.totalharusdibayar = tb.totaldibayar THEN 0 WHEN tb.totalharusdibayar <> tb.totaldibayar THEN " & _
             " CAST (tb.totalharusdibayar AS FLOAT) + CAST (tb.totaldibayar AS FLOAT) ELSE CAST (tb.totalharusdibayar AS FLOAT) " & _
             " END AS bayarkeun,CASE WHEN tb.totalharusdibayar IS NULL THEN CAST (0 AS FLOAT) ELSE CAST (tb.totalharusdibayar AS FLOAT) END AS bayar, " & _
             " CASE WHEN tb.totaldibayar IS NULL THEN CAST (0 AS FLOAT) ELSE CAST (tb.totaldibayar AS FLOAT) END AS sudahbayar, " & _
             " CASE WHEN pp.iskronis = TRUE AND tb.tipepasien = 'BPJS' THEN (((tb.jumlah * 7) / 30) * tb.hargajual) ELSE tb.total END AS totals " & _
             " FROM temp_billingtr AS tb " & _
             " INNER JOIN transaksipasientr AS pp ON pp.norec = tb.norec_pp " & _
             " WHERE tb.noregistrasi='" & strNoregistrasi & "' " & _
             " AND tb.tglpelayanan is not null AND tb.namaproduk not in ('Biaya Administrasi','Biaya Materai') ORDER BY tglpelayanan,namaproduk "

    ReadRs " SELECT CASE WHEN pa.nosep IS NULL THEN '-' ELSE pa.nosep END AS sep,pd.kelompokpasienlastidfk AS Kelompokpasien " & _
           " FROM pemakaianasuransitr AS pa LEFT JOIN registrasipasientr AS pd ON pd.norec = pa.registrasipasienfk " & _
           " WHERE pd.noregistrasi = '" & strNoregistrasi & "' "
            
    ReadRs2 " SELECT SUM(hargajual) AS totalDeposit FROM registrasipasientr AS pd " & _
            " INNER JOIN daftarpasienruangantr AS apd ON apd.registrasipasienfk = pd.norec " & _
            " INNER JOIN transaksipasientr pp on pp.daftarpasienruanganfk = apd.norec " & _
            " WHERE pd.noregistrasi = '" & strNoregistrasi & "' AND pp.produkidfk=402611 "
    
    ReadRs3 " SELECT SUM(ppd.hargadiscount) AS hargadiscount,ppd.komponenhargaidfk " & _
            " FROM registrasipasientr AS pd " & _
            " INNER JOIN daftarpasienruangantr apd ON apd.registrasipasienfk = pd.norec " & _
            " INNER JOIN transaksipasientr AS pp ON pp.daftarpasienruanganfk = apd.norec " & _
            " INNER JOIN transaksipasiendetailtr AS ppd ON  ppd.transaksipasienfk = pp.norec " & _
            " WHERE pd.noregistrasi = '" & strNoregistrasi & "' AND pp.produkidfk<>402611 " & _
            " GROUP BY ppd.komponenhargaidfk "
              
    Dim TotalDiskonMedis  As Double
    Dim TotalDiskonUmum  As Double
    Dim i As Integer
    
    If RS3.EOF = False Then
        For i = 0 To RS3.RecordCount - 1
            If RS3!komponenhargaidfk = 35 Then TotalDiskonMedis = TotalDiskonMedis + CDbl(IIf(IsNull(RS3!hargadiscount), 0, RS3!hargadiscount))
            If RS3!komponenhargaidfk <> 35 Then TotalDiskonUmum = TotalDiskonUmum + CDbl(IIf(IsNull(RS3!hargadiscount), 0, RS3!hargadiscount))
            RS3.MoveNext
        Next
    End If
    
    Dim TotalDeposit As Double
    TotalDeposit = IIf(IsNull(RS2(0)), 0, RS2(0))
    
    adocmd.CommandText = strSQL
    adocmd.CommandType = adCmdText
        
    With crRincianBiayaDetail
        .database.AddADOCommand CN_String, adocmd
            .txtNamaRs.SetText strNamaLengkapRs
            .txtAlamatRs.SetText strAlamatRS & ", " & strKodePos & ", " & strNoTlpn & ", " & strNoFax
            .txtWebEmail.SetText strEmail & ", " & strWebSite
            .txtNamaKota.SetText strNamaKota & ", "
            .usNoRegistrasi.SetUnboundFieldSource ("{ado.noregistrasi}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usNamaPasien.SetUnboundFieldSource ("{ado.namapasienjk}")
            .usRuangan.SetUnboundFieldSource ("{ado.unit}")
            .usKamar.SetUnboundFieldSource IIf(IsNull("{ado.namakamar}") = True, "-", ("{ado.namakamar}"))
            .usKelasH.SetUnboundFieldSource ("{ado.namakelaspd}")
            .usDokterPJawab.SetUnboundFieldSource ("{ado.dokterpj}")
            .udTglMasuk.SetUnboundFieldSource ("{ado.tglregistrasi}")
            .udTglPlng.SetUnboundFieldSource IIf(IsNull("{ado.tglpulang}") = True, "-", ("{ado.tglpulang}"))
            .usPenjamin.SetUnboundFieldSource IIf(IsNull("{ado.namarekanan}") = True, ("-"), ("{ado.namarekanan}"))
            .usTipe.SetUnboundFieldSource ("{ado.tipepasien}")
            .usJenisProduk.SetUnboundFieldSource ("{ado.jenisprodukmaster}")
            .udtanggal.SetUnboundFieldSource ("{ado.tglpelayanan}")
            .usTglPelayanan.SetUnboundFieldSource ("{ado.tglpelayanan}")
            .usLayanan.SetUnboundFieldSource ("{ado.namaproduk}")
            .usKelas.SetUnboundFieldSource ("{ado.namakelas}")
            .usDokter.SetUnboundFieldSource ("{ado.dokter}")
            .unqty.SetUnboundFieldSource ("{ado.jumlah}")
            .ucTarif.SetUnboundFieldSource ("{ado.hargajual}")
            .ucDiskon.SetUnboundFieldSource ("{ado.diskon}")
            .ucTotal.SetUnboundFieldSource ("{ado.total}")
            .ucJmlahTlahDBayar.SetUnboundFieldSource ("{ado.sudahbayar}")
            .usRuanganTindakan.SetUnboundFieldSource ("{ado.ruangantindakan}")
            .usNoStruk.SetUnboundFieldSource ("{ado.nobilling}")
            .ucDepartemen.SetUnboundFieldSource ("{ado.objectdepartemenfk}")
            .usNorecPP.SetUnboundFieldSource ("{ado.norec_pp}")
            .usPenulisResep.SetUnboundFieldSource ("{ado.penulisresep}")

'            If strNoStruk = "TOTALTOTALTOTAL" Then
'                TotalDeposit = 0
'                .Section11.Suppress = True
'            Else
'                .Section11.Suppress = False
'            End If
            
            If RS.EOF = False Then
                If RS!KelompokPasien = 2 Or RS!KelompokPasien = 4 Then
                    .txtNoSep.SetText RS!sep
                Else
                    .txtNoSep.Suppress = True
                    .txtLblSep.Suppress = True
                    .txtLblSep2.Suppress = True
                End If
            Else
                .txtNoSep.Suppress = True
                .txtLblSep.Suppress = True
                .txtLblSep2.Suppress = True
            End If
            
            .ucDeposit.SetUnboundFieldSource (TotalDeposit)
            .ucDiskonJasaMedis.SetUnboundFieldSource (TotalDiskonMedis)
            .ucDiskonUmum.SetUnboundFieldSource (TotalDiskonUmum)
            .ucDitanggungPerusahaan.SetUnboundFieldSource ("{ado.totalprekanan}")
            .ucDitanggungRS.SetUnboundFieldSource ("0")
            .ucSurplusMinusRS.SetUnboundFieldSource ("0")
            .usUser.SetUnboundFieldSource ("{ado.user}")
            .txtVersi.SetText App.Comments
            .txtUser.SetText UCase(strIdPegawai)
            
            If view = "false" Then
                Dim strPrinter As String
                strPrinter = GetTxt("Setting.ini", "Printer", "RincianBiayaDetail")
                .SelectPrinter "winspool", strPrinter, "Ne00:"
                .PrintOut False
                Unload Me
            Else
                With CRViewer1
                    .ReportSource = crRincianBiayaDetail
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
            End If
    End With
Exit Sub
errLoad:
End Sub

Public Sub CetakRekapRincian(strNoregistrasi As String, strIdPegawai As String, view As String)
On Error GoTo errLoad
On Error Resume Next
Set frmKasir = Nothing
bolRincianBiayaDetail = False
bolRekapRincian = True
bolRincianObat = False
bolLaporanPenerimaanKasir = False
Dim adocmd As New ADODB.Command
Dim StrFilter As String
Dim kamar As String
Dim kelas As String
StrFilter = ""
     strSQL = " SELECT '-' AS norec_pp ,* FROM (SELECT x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit, " & _
              " x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,'Tindakan Perawatan Rawat Inap' AS namaproduk,'-' AS penulisresep,'-' AS jenisproduk, " & _
              " SUM (x.jumlah) AS jumlah,SUM (x.total) AS total,'-' AS namakamar,x.tipepasien,x.totalharusdibayar,x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser,'RAWAT INAP' AS GROUPING  " & _
              " FROM(SELECT tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan, " & _
              " tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar,tb.totalprekanan, " & _
              " tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,dp.namadepartemen,USER AS namauser " & _
              " FROM temp_billingtr AS tb LEFT JOIN ruanganmt AS ru ON ru.namaruangan = tb.ruangantindakan LEFT JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk " & _
              " WHERE noregistrasi = '" & strNoregistrasi & "' AND tglpelayanan IS NOT NULL AND namaproduk NOT IN ('Biaya Administrasi','Biaya Materai') " & _
              " AND dp.id IN (2,6) AND tb.jenisproduk <> 'RAWAT INAP DAN PERAWATAN KHUSUS (AKOMODASI)' " & _
              " GROUP BY tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang, " & _
              " tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien, " & _
              " tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal , tb.total_kelasasal, dp.namadepartemen " & _
              " ) AS x GROUP BY x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.tipepasien,x.totalharusdibayar,x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser "
    
     strSQL = strSQL & " UNION ALL SELECT x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,'Akomodasi Rawat Inap' AS namaproduk,'-' AS penulisresep, " & _
              " '-' AS jenisproduk,SUM (x.jumlah) AS jumlah,SUM (x.total) AS total,'-' AS namakamar,x.tipepasien,x.totalharusdibayar,x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser,'RAWAT INAP' AS GROUPING FROM(SELECT tb.tglstruk,tb.nobilling,tb.nokwitansi, " & _
              " tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar, " & _
              " tb.tipepasien,tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,dp.namadepartemen,USER AS namauser " & _
              " FROM temp_billingtr AS tb LEFT JOIN ruanganmt AS ru ON ru.namaruangan = tb.ruangantindakan LEFT JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk WHERE noregistrasi = '" & strNoregistrasi & "' AND tglpelayanan IS NOT NULL AND namaproduk NOT IN ('Biaya Administrasi','Biaya Materai') " & _
              " AND dp.id IN (2,6) AND tb.jenisproduk = 'RAWAT INAP DAN PERAWATAN KHUSUS (AKOMODASI)' GROUP BY tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan, " & _
              " tb.ruangantindakan,tb.namaproduk,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal, " & _
              " dp.namadepartemen) AS x GROUP BY x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.tipepasien,x.totalharusdibayar,x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser "
    
     strSQL = strSQL & " UNION ALL SELECT x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.namadepartemen AS namaproduk,'-' AS penulisresep,'-' AS jenisproduk,SUM (x.jumlah) AS jumlah,SUM (x.total) AS total,'-' AS namakamar,x.tipepasien,x.totalharusdibayar, " & _
              " x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser,'Rawat Jalan' AS GROUPING FROM(SELECT tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan, " & _
              " tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,  " & _
              " tb.total_kelasasal,dp.namadepartemen,USER AS namauser FROM temp_billingtr AS tb LEFT JOIN ruanganmt AS ru ON ru.namaruangan = tb.ruangantindakan LEFT JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk WHERE noregistrasi = '" & strNoregistrasi & "' AND tglpelayanan IS NOT NULL AND namaproduk NOT IN ('Biaya Administrasi','Biaya Materai') " & _
              " AND dp.id IN (1) GROUP BY tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon, " & _
              " tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,dp.namadepartemen) AS x " & _
              " GROUP BY x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.tipepasien,x.totalharusdibayar,x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser "
    
    strSQL = strSQL & " UNION ALL SELECT x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.namadepartemen AS namaproduk,'-' AS penulisresep,'-' AS jenisproduk,SUM (x.jumlah) AS jumlah,SUM (x.total) AS total,'-' AS namakamar, " & _
             " x.tipepasien,x.totalharusdibayar,x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser,'IGD' AS GROUPING FROM(SELECT tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan, " & _
             " tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,dp.namadepartemen,USER AS namauser " & _
             " FROM temp_billingtr AS tb LEFT JOIN ruanganmt AS ru ON ru.namaruangan = tb.ruangantindakan LEFT JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk WHERE noregistrasi = '" & strNoregistrasi & "' AND tglpelayanan IS NOT NULL AND namaproduk NOT IN ('Biaya Administrasi','Biaya Materai') AND dp.id IN (3) " & _
             " GROUP BY tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar, " & _
             " tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,dp.namadepartemen) AS x GROUP BY x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.tipepasien,x.totalharusdibayar, " & _
             " x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser "
    
    strSQL = strSQL & " UNION ALL SELECT x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.namadepartemen AS namaproduk,'-' AS penulisresep,'-' AS jenisproduk,SUM (x.jumlah) AS jumlah,SUM (x.total) AS total,'-' AS namakamar, " & _
             " x.tipepasien,x.totalharusdibayar,x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser,'RADIOLOGI' AS GROUPING FROM(SELECT tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan, " & _
             " tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,dp.namadepartemen,USER AS namauser " & _
             " FROM temp_billingtr AS tb LEFT JOIN ruanganmt AS ru ON ru.namaruangan = tb.ruangantindakan LEFT JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk WHERE noregistrasi = '" & strNoregistrasi & "' AND tglpelayanan IS NOT NULL AND namaproduk NOT IN ('Biaya Administrasi','Biaya Materai') AND dp.id IN (5) " & _
             " GROUP BY tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar, " & _
             " tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,dp.namadepartemen) AS x GROUP BY x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.tipepasien,x.totalharusdibayar, " & _
             " x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser "
             
     strSQL = strSQL & " UNION ALL SELECT x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.namadepartemen AS namaproduk,'-' AS penulisresep,'-' AS jenisproduk,SUM (x.jumlah) AS jumlah,SUM (x.total) AS total,'-' AS namakamar, " & _
             " x.tipepasien,x.totalharusdibayar,x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser,'LABORATORIUM' AS GROUPING FROM(SELECT tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan, " & _
             " tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,dp.namadepartemen,USER AS namauser " & _
             " FROM temp_billingtr AS tb LEFT JOIN ruanganmt AS ru ON ru.namaruangan = tb.ruangantindakan LEFT JOIN instalasimt AS dp ON dp.id = ru.instalasiidfk WHERE noregistrasi = '" & strNoregistrasi & "' AND tglpelayanan IS NOT NULL AND namaproduk NOT IN ('Biaya Administrasi','Biaya Materai') AND dp.id IN (4) " & _
             " GROUP BY tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar, " & _
             " tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,dp.namadepartemen) AS x GROUP BY x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,x.tipepasien,x.totalharusdibayar, " & _
             " x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser "
             
    strSQL = strSQL & " UNION ALL SELECT x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,'Instalasi Farmasi' AS namadepartemen,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.namakelaspd,'Pemakaian Obat & Alkes' AS namaproduk,'-' AS penulisresep,'-' AS jenisproduk,0 AS jumlah,SUM (x.total) AS total,'-' AS namakamar,x.tipepasien,x.totalharusdibayar,x.totalprekanan, " & _
             " x.totalppenjamin,x.totalbiayatambahan,x.namauser,'FARMASI' AS GROUPING FROM (SELECT tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep,tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien, " & _
             " tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal,'Instalasi Farmasi' AS namadepartemen,USER AS namauser FROM temp_billingtr AS tb " & _
             " WHERE tb.noregistrasi = '" & strNoregistrasi & "' AND tb.tglpelayanan IS NOT NULL AND tb.namaproduk NOT IN ('Biaya Administrasi','Biaya Materai') AND tb.jenisprodukmaster = 'Jenis Barang Farmasi' GROUP BY tb.tglstruk,tb.nobilling,tb.nokwitansi,tb.noregistrasi,tb.nocm,tb.namapasienjk,tb.unit,tb.objectdepartemenfk,tb.namakelas,tb.dokterpj,tb.tglregistrasi,tb.tglpulang,tb.namarekanan,tb.tglpelayanan,tb.ruangantindakan,tb.namaproduk,tb.penulisresep, " & _
             " tb.jenisproduk,tb.jumlah,tb.hargajual,tb.diskon,tb.total,tb.namakamar,tb.tipepasien,tb.totalharusdibayar,tb.totalprekanan,tb.totalppenjamin,tb.totalbiayatambahan,tb.namakelaspd,tb.nama_kelasasal,tb.hargajual_kelasasal,tb.total_kelasasal) AS x " & _
             " GROUP BY x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.tipepasien,x.totalharusdibayar,x.totalprekanan,x.totalppenjamin,x.totalbiayatambahan,x.namauser,x.namakelaspd) AS T "

    ReadRs " SELECT CASE WHEN pa.nosep IS NULL THEN '-' ELSE pa.nosep END AS sep,pd.kelompokpasienlastidfk AS Kelompokpasien " & _
           " FROM pemakaianasuransitr AS pa LEFT JOIN registrasipasientr AS pd ON pd.norec = pa.registrasipasienfk " & _
           " WHERE pd.noregistrasi = '" & strNoregistrasi & "' "
            
    ReadRs2 " SELECT SUM(hargajual) AS totalDeposit FROM registrasipasientr AS pd " & _
            " INNER JOIN daftarpasienruangantr AS apd ON apd.registrasipasienfk = pd.norec " & _
            " INNER JOIN transaksipasientr pp on pp.daftarpasienruanganfk = apd.norec " & _
            " WHERE pd.noregistrasi = '" & strNoregistrasi & "' AND pp.produkidfk=402611 "
    
    ReadRs3 " SELECT SUM(ppd.hargadiscount) AS hargadiscount,ppd.komponenhargaidfk " & _
            " FROM registrasipasientr AS pd " & _
            " INNER JOIN daftarpasienruangantr apd ON apd.registrasipasienfk = pd.norec " & _
            " INNER JOIN transaksipasientr AS pp ON pp.daftarpasienruanganfk = apd.norec " & _
            " INNER JOIN transaksipasiendetailtr AS ppd ON  ppd.transaksipasienfk = pp.norec " & _
            " WHERE pd.noregistrasi = '" & strNoregistrasi & "' AND pp.produkidfk<>402611 " & _
            " GROUP BY ppd.komponenhargaidfk "
    
    ReadRs4 "select namakamar,namakelaspd from temp_billingtr where noregistrasi = '" & strNoregistrasi & "' limit 1"
    If RS4.EOF = False Then
        kamar = RS4!namakamar
        kelas = RS4!namakelaspd
    Else
        kamar = "-"
        kelas = "-"
    End If
                
    Dim TotalDiskonMedis  As Double
    Dim TotalDiskonUmum  As Double
    Dim i As Integer
    
    
    For i = 0 To RS3.RecordCount - 1
        If RS3!komponenhargaidfk = 35 Then TotalDiskonMedis = TotalDiskonMedis + CDbl(IIf(IsNull(RS3!hargadiscount), 0, RS3!hargadiscount))
        If RS3!komponenhargaidfk <> 35 Then TotalDiskonUmum = TotalDiskonUmum + CDbl(IIf(IsNull(RS3!hargadiscount), 0, RS3!hargadiscount))
        RS3.MoveNext
    Next
    
    Dim TotalDeposit As Double
    TotalDeposit = IIf(IsNull(RS2(0)), 0, RS2(0))
   
    adocmd.CommandText = strSQL
    adocmd.CommandType = adCmdText
        
    With crRincianBiayaRekap
        .database.AddADOCommand CN_String, adocmd
            .txtNamaRs.SetText strNamaLengkapRs
            .txtAlamatRs.SetText strAlamatRS & ", " & strKodePos & ", " & strNoTlpn & ", " & strNoFax
            .txtWebEmail.SetText strEmail & ", " & strWebSite
            .txtNamaKota.SetText strNamaKota & ", "
            .usNoRegistrasi.SetUnboundFieldSource ("{ado.noregistrasi}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usNamaPasien.SetUnboundFieldSource ("{ado.namapasienjk}")
            .usRuangan.SetUnboundFieldSource ("{ado.unit}")
            .usKelasH.SetUnboundFieldSource ("{ado.namakelaspd}")
            .usDokterPJawab.SetUnboundFieldSource ("{ado.dokterpj}")
            .udTglMasuk.SetUnboundFieldSource ("{ado.tglregistrasi}")
            .udTglPlng.SetUnboundFieldSource IIf(IsNull("{ado.tglpulang}") = True, "-", ("{ado.tglpulang}"))
            .usPenjamin.SetUnboundFieldSource IIf(IsNull("{ado.namarekanan}") = True, ("-"), ("{ado.namarekanan}"))
            .usTipe.SetUnboundFieldSource ("{ado.tipepasien}")
            .usNorecPP.SetUnboundFieldSource ("{ado.norec_pp}")
            .usPenulisResep.SetUnboundFieldSource ("{ado.penulisresep}")
            .usLayanan.SetUnboundFieldSource ("{ado.namaproduk}")
            .unqty.SetUnboundFieldSource ("{ado.jumlah}")
            .usJmlQty.SetUnboundFieldSource ("{ado.jumlah}")
            .ucTotal.SetUnboundFieldSource ("{ado.total}")
            .usRuanganTindakan.SetUnboundFieldSource ("{ado.grouping}")
            .usNoStruk.SetUnboundFieldSource ("{ado.nobilling}")
            .txtKamar.SetText kamar
            .txtKls.SetText kelas
            .Text30.SetText UCase(strIdPegawai)
            .Section11.Suppress = False
            
            If RS.EOF = False Then
                If RS!KelompokPasien = 2 Or RS!KelompokPasien = 4 Then
                    .txtNoSep.SetText RS!sep
                Else
                    .txtNoSep.Suppress = True
                    .txtLblSep.Suppress = True
                    .txtLblSep2.Suppress = True
                End If
            Else
                .txtNoSep.Suppress = True
                .txtLblSep.Suppress = True
                .txtLblSep2.Suppress = True
            End If
            
            .ucDeposit.SetUnboundFieldSource (TotalDeposit)
            .ucDiskonJasaMedis.SetUnboundFieldSource (TotalDiskonMedis)
            .ucDiskonUmum.SetUnboundFieldSource (TotalDiskonUmum)
            .ucDitanggungPerusahaan.SetUnboundFieldSource ("{ado.totalprekanan}")
            .ucDitanggungRS.SetUnboundFieldSource ("0")
            .ucDitanggungSendiri.SetUnboundFieldSource ("{ado.totalharusdibayar}")
            .ucSurplusMinusRS.SetUnboundFieldSource ("0")
            .txtVersi.SetText App.Comments
            .txtUser.SetText UCase(strIdPegawai)
                            
            If view = "false" Then
                Dim strPrinter As String
                strPrinter = GetTxt("Setting.ini", "Printer", "RincianBiayaRekap")
                .SelectPrinter "winspool", strPrinter, "Ne00:"
                .PrintOut False
                Unload Me
            Else
                With CRViewer1
                    .ReportSource = crRincianBiayaRekap
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
            End If
    End With
Exit Sub
errLoad:
End Sub

Public Sub CetakRincianBiayaObat(strNoregistrasi As String, strIdPegawai As String, view As String)
'On Error GoTo errLoad
'On Error Resume Next
Set frmKasir = Nothing
bolRincianBiayaDetail = False
bolRekapRincian = False
bolRincianObat = True
bolLaporanPenerimaanKasir = False
Dim adocmd As New ADODB.Command
Dim StrFilter As String
StrFilter = ""
    
  strSQL = " SELECT x.norec_pp,x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.objectdepartemenfk, " & _
           " x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.ruangan AS ruangantindakan,SUBSTRING (x.namaproduk, 5, 255) AS namaproduk, " & _
           " '-' AS penulisresep,'-' AS jenisproduk,x.dokter,SUM (x.jumlah) AS jumlah,x.hargajual,SUM (x.total) AS total,x.namakamar,x.tipepasien, " & _
           " x.totalharusdibayar , x.totalprekanan, x.totalppenjamin, x.totalbiayatambahan, x.namauser, x.namakelaspd " & _
           " FROM(SELECT *, USER AS namauser,SUBSTRING (ruangantindakan, 1, 10) AS ruangan FROM temp_billingtr WHERE noregistrasi = '" & strNoregistrasi & "' " & _
           " AND tglpelayanan IS NOT NULL AND namaproduk NOT IN ('Biaya Administrasi','Biaya Materai') AND penulisresep IS NOT NULL " & _
           " ) AS x GROUP BY x.norec_pp,x.tglstruk,x.nobilling,x.nokwitansi,x.noregistrasi,x.nocm,x.namapasienjk,x.unit,x.objectdepartemenfk, " & _
           " x.dokterpj,x.tglregistrasi,x.tglpulang,x.namarekanan,x.ruangan,x.dokter,x.namakamar,x.tipepasien,x.totalharusdibayar,x.totalprekanan, " & _
           " x.totalppenjamin,x.totalbiayatambahan,x.namauser,x.hargajual,x.namaproduk,x.namakelaspd "
    
   ReadRs " SELECT CASE WHEN pa.nosep IS NULL THEN '-' ELSE pa.nosep END AS sep,pd.kelompokpasienlastidfk AS Kelompokpasien " & _
           " FROM pemakaianasuransitr AS pa LEFT JOIN registrasipasientr AS pd ON pd.norec = pa.registrasipasienfk " & _
           " WHERE pd.noregistrasi = '" & strNoregistrasi & "' "
            
   ReadRs2 " SELECT SUM(hargajual) AS totalDeposit FROM registrasipasientr AS pd " & _
           " INNER JOIN daftarpasienruangantr AS apd ON apd.registrasipasienfk = pd.norec " & _
           " INNER JOIN transaksipasientr pp on pp.daftarpasienruanganfk = apd.norec " & _
           " WHERE pd.noregistrasi = '" & strNoregistrasi & "' AND pp.produkidfk=402611 "
    
   ReadRs3 " SELECT SUM(ppd.hargadiscount) AS hargadiscount,ppd.komponenhargaidfk " & _
           " FROM registrasipasientr AS pd " & _
           " INNER JOIN daftarpasienruangantr apd ON apd.registrasipasienfk = pd.norec " & _
           " INNER JOIN transaksipasientr AS pp ON pp.daftarpasienruanganfk = apd.norec " & _
           " INNER JOIN transaksipasiendetailtr AS ppd ON  ppd.transaksipasienfk = pp.norec " & _
           " WHERE pd.noregistrasi = '" & strNoregistrasi & "' AND pp.produkidfk<>402611 " & _
           " GROUP BY ppd.komponenhargaidfk "
           
   Dim TotalDiskonMedis  As Double
   Dim TotalDiskonUmum  As Double
   Dim i As Integer
        
   For i = 0 To RS3.RecordCount - 1
        If RS3!komponenhargaidfk = 35 Then TotalDiskonMedis = TotalDiskonMedis + CDbl(IIf(IsNull(RS3!hargadiscount), 0, RS3!hargadiscount))
        If RS3!komponenhargaidfk <> 35 Then TotalDiskonUmum = TotalDiskonUmum + CDbl(IIf(IsNull(RS3!hargadiscount), 0, RS3!hargadiscount))
        RS3.MoveNext
   Next
    
   Dim TotalDeposit As Double
   TotalDeposit = IIf(IsNull(RS2(0)), 0, RS2(0))
   adocmd.CommandText = strSQL
   adocmd.CommandType = adCmdText
        
   With crRincianBiayaObat
        .database.AddADOCommand CN_String, adocmd
            .txtNamaRs.SetText strNamaLengkapRs
            .txtAlamatRs.SetText strAlamatRS & ", " & strKodePos & ", " & strNoTlpn & ", " & strNoFax
            .txtWebEmail.SetText strEmail & ", " & strWebSite
            .usNoRegistrasi.SetUnboundFieldSource ("{ado.noregistrasi}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usNamaPasien.SetUnboundFieldSource ("{ado.namapasienjk}")
            .usRuangan.SetUnboundFieldSource ("{ado.unit}")
            .usKamar.SetUnboundFieldSource IIf(IsNull("{ado.namakamar}") = True, "-", ("{ado.namakamar}"))
            .usDokterPJawab.SetUnboundFieldSource ("{ado.dokterpj}")
            .udTglMasuk.SetUnboundFieldSource ("{ado.tglregistrasi}")
            .udTglPlng.SetUnboundFieldSource IIf(IsNull("{ado.tglpulang}") = True, "-", ("{ado.tglpulang}"))
            .usPenjamin.SetUnboundFieldSource IIf(IsNull("{ado.namarekanan}") = True, ("-"), ("{ado.namarekanan}"))
            .usTipe.SetUnboundFieldSource ("{ado.tipepasien}")
            .usLayanan.SetUnboundFieldSource ("{ado.namaproduk}")
            .unqty.SetUnboundFieldSource ("{ado.jumlah}")
            .usJmlQty.SetUnboundFieldSource ("{ado.jumlah}")
            .ucHargaSatuan.SetUnboundFieldSource ("{ado.hargajual}")
            .ucTotal.SetUnboundFieldSource ("{ado.total}")
            .usRuanganTindakan.SetUnboundFieldSource ("{ado.ruangantindakan}")
            .usNoStruk.SetUnboundFieldSource ("{ado.nobilling}")
            .ucDepartemen.SetUnboundFieldSource ("{ado.objectdepartemenfk}")
            .usNorecPP.SetUnboundFieldSource ("{ado.norec_pp}")
            .usPenulisResep.SetUnboundFieldSource ("{ado.penulisresep}")
            .usKelasH.SetUnboundFieldSource ("{ado.namakelaspd}")
            .Section11.Suppress = False
            
            If RS.EOF = False Then
                If RS!KelompokPasien = 2 Or RS!KelompokPasien = 4 Then
                    .txtNoSep.SetText RS!sep
                Else
                    .txtNoSep.Suppress = True
                    .txtLblSep.Suppress = True
                    .txtLblSep2.Suppress = True
                End If
            Else
                .txtNoSep.Suppress = True
                .txtLblSep.Suppress = True
                .txtLblSep2.Suppress = True
            End If
            
            .ucDeposit.SetUnboundFieldSource (TotalDeposit)
            .ucDiskonJasaMedis.SetUnboundFieldSource (TotalDiskonMedis)
            .ucDiskonUmum.SetUnboundFieldSource (TotalDiskonUmum)
            .ucDitanggungPerusahaan.SetUnboundFieldSource ("{ado.totalprekanan}")
            .ucDitanggungRS.SetUnboundFieldSource ("0")
            .ucDitanggungSendiri.SetUnboundFieldSource ("{ado.totalharusdibayar}")
            .ucSurplusMinusRS.SetUnboundFieldSource ("0")
            .usUser.SetUnboundFieldSource ("{ado.namauser}")
            .txtVersi.SetText App.Comments
            .txtUser.SetText UCase(strIdPegawai)
            If view = "false" Then
                Dim strPrinter As String
                strPrinter = GetTxt("Setting.ini", "Printer", "RincianObatAlkes")
                .SelectPrinter "winspool", strPrinter, "Ne00:"
                .PrintOut False
                Unload Me
            Else
                With CRViewer1
                    .ReportSource = crRincianBiayaObat
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
            End If
   End With
Exit Sub
errLoad:
End Sub

Public Sub CetakLaporanKasir(idKasir As String, tglAwal As String, tglAkhir As String, idCarabayar As String, idRuangan As String, Carabayar As String, Ruangan As String, view As String)
'On Error GoTo errLoad
'On Error Resume Next
Set frmKasir = Nothing
bolRincianBiayaDetail = False
bolRekapRincian = False
bolRincianObat = False
bolLaporanPenerimaanKasir = True
Dim adocmd As New ADODB.Command
Dim str1 As String
Dim str2 As String
Dim str3 As String
Dim str4 As String
Dim str5 As String
Dim nmaKasir As String
Dim aingmacan As String
Dim i As Integer
i = 0
If idKasir <> "" Then
    str1 = " and pg.id in (" & idKasir & ") "
    ReadRs "SELECT namalengkap FROM pegawaimt WHERE id in (" & idKasir & ")"
    If RS.EOF = False Then
        For i = 0 To RS.RecordCount - 1
            aingmacan = aingmacan & ", " & RS!namalengkap
            RS.MoveNext
        Next
    End If
    nmaKasir = Replace(aingmacan, ",", "", 1, 1)
Else
    str1 = ""
End If
If idRuangan <> "" Then
    str2 = " and ru.id = " & idRuangan & " "
End If
If idCarabayar <> "" Then
    If idCarabayar = "99" Then
       str3 = " AND sbmc.objectcarabayarfk = idCaraBayar "
    Else
       str3 = ""
    End If
End If
    strSQL = " SELECT x.tipepasien,x.ruangan,SUM(x.totalprekanan)+SUM(x.totalharusdibayar)+SUM(x.tanggunganrs)+SUM(x.pembebasan)+SUM(x.administrasi) AS banyak, " & _
             " SUM(x.totalprekanan) AS totalprekanan,SUM(x.tanggunganrs) AS tanggunganrs,SUM(x.pembebasan) AS pembebasan,SUM(x.totalharusdibayar) AS totalharusdibayar, " & _
             " SUM(x.administrasi) AS administrasi,SUM(x.totaldibayar) AS totaldibayar,( " & _
             " SUM(x.totalharusdibayar)+SUM(x.tanggunganrs)+SUM(x.pembebasan)+SUM(x.administrasi))-SUM(x.totaldibayar) AS sisa " & _
             " FROM(SELECT CASE WHEN sp.registrasipasienfk IS NULL THEN 'Umum/Tunai' ELSE kp.kelompokpasien END AS tipepasien, " & _
             " CASE WHEN sp.registrasipasienfk IS NULL THEN ru1.namaruangan ELSE ru.namaruangan END AS ruangan, " & _
             " sum(sbm.totaldibayar),CASE WHEN sp.totalprekanan IS NULL THEN 0 ELSE sp.totalprekanan END AS totalprekanan,0 AS tanggunganrs, " & _
             " sp.totalharusdibayar,0 AS pembebasan,0 AS administrasi,sbm.totaldibayar,CASE WHEN sbm.totalsisapiutang IS NULL THEN 0 " & _
             " ELSE sbm.totalsisapiutang END AS totalsisapiutang " & _
             " FROM strukpelayanantr AS sp " & _
             " INNER JOIN strukbuktipenerimaantr AS sbm ON sbm.norec = sp.nosbmlastidfk " & _
             " LEFT JOIN registrasipasientr AS rg ON rg.norec = sp.registrasipasienfk " & _
             " LEFT JOIN kelompokpasienmt AS kp ON kp.id = rg.kelompokpasienlastidfk " & _
             " LEFT JOIN ruanganmt AS ru ON ru.id = rg.ruanganlastidfk " & _
             " LEFT JOIN ruanganmt AS ru1 ON ru1.id = sp.ruanganidfk " & _
             " LEFT JOIN pegawaimt AS pg ON pg.id = sbm.pegawaipenerimaidfk " & _
             " Where sbm.aktif = True And sp.aktif = True " & _
             " AND sbm.tglsbm BETWEEN '" & tglAwal & "' AND '" & tglAkhir & "' " & _
             " AND (sp.iskembalideposit IS NULL OR sp.iskembalideposit = false) " & _
             str1 & str2 & str3 & _
             " GROUP BY sp.registrasipasienfk,kp.kelompokpasien,ru.namaruangan,ru1.namaruangan, " & _
             " sbm.totaldibayar,sp.totalprekanan,sp.totalharusdibayar,sbm.totaldibayar, " & _
             " sbm.totalsisapiutang ) AS x " & _
             " GROUP BY x.tipepasien,x.ruangan "
             
    adocmd.CommandText = strSQL
    adocmd.CommandType = adCmdText
    With printLaporanPenerimaanKasir
        .database.AddADOCommand CN_String, adocmd
        .txtNamaRs.SetText UCase$(strNamaLengkapRs)
        .txtAlamat.SetText stralmtLengkapRs
        .txtNamaKasir.SetText nmaKasir
        .txtCaraBayar.SetText Carabayar
        .txtRuangPeriksa.SetText Ruangan
        .txtPeriode.SetText tglAwal & " s/d " & tglAkhir
        .usRuangan.SetUnboundFieldSource ("{ado.ruangan}")
        .usDeskripsi.SetUnboundFieldSource ("{ado.tipepasien}")
        .ucBanyak.SetUnboundFieldSource ("{ado.banyak}")
        .ucPiutangPenjamin.SetUnboundFieldSource ("{ado.totalprekanan}")
        .ucTanggungRs.SetUnboundFieldSource ("{ado.tanggunganrs}")
        .ucPembebasan.SetUnboundFieldSource ("{ado.pembebasan}")
        .ucHarusbayar.SetUnboundFieldSource ("{ado.totalharusdibayar}")
        .ucJmlBayar.SetUnboundFieldSource ("{ado.totaldibayar}")
        .ucSisa.SetUnboundFieldSource ("{ado.sisa}")
        .ucAdmin.SetUnboundFieldSource ("{ado.administrasi}")
        If view = "false" Then
            Dim strPrinter As String
            strPrinter = GetTxt("Setting.ini", "Printer", "A4_single")
            .SelectPrinter "winspool", strPrinter, "Ne00:"
            .PrintOut False
            Unload Me
        Else
            With CRViewer1
                .ReportSource = printLaporanPenerimaanKasir
                .ViewReport
                .Zoom 1
            End With
            Me.Show
        End If
   End With
Exit Sub
errLoad:
End Sub

