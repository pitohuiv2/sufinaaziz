VERSION 5.00
Object = "{C4847593-972C-11D0-9567-00A0C9273C2A}#8.0#0"; "crviewer.dll"
Object = "{248DD890-BB45-11CF-9ABC-0080C7E7B78D}#1.0#0"; "MSWINSCK.OCX"
Begin VB.Form frmRegistrasi 
   Caption         =   "Registrasi"
   ClientHeight    =   7005
   ClientLeft      =   60
   ClientTop       =   405
   ClientWidth     =   5820
   Icon            =   "frmRegistrasi.frx":0000
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
Attribute VB_Name = "frmRegistrasi"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
'# VARIABEL CR #'
Dim crBuktiPendaftaran As New crAntrianPendaftaran
Dim crTracetr As New crCetakTracer
Dim crCetakBlangkoBpjs As New crBlangkoBpjs
Dim crCetakBuktiLayanan As New crBuktiLayanan
Dim crCetakLabelPasien As New crLabelPasien
Dim crCetakGelangPasien As New crGelangPasien
Dim crCetakSuratLembarIdentitasPasien As New crSuratLembarIdentitasPasien
Dim crPernyataanRanap As New crSuratPernyataanRanap
Dim reportSumList As New crSummaryList
Dim TriageIgd As New crHeaderTriage
Dim suratJampersal As New crSuratPernyataanJampersal
Dim crFormulirRajal As New crFormulirRawatJalan
'# VARIABEL FUNC #'
Dim bolBuktiPendaftaran As Boolean
Dim bolTracer As Boolean
Dim bolBlangkoBpjs As Boolean
Dim bolBuktiLayanan As Boolean
Dim bolLabelPasien As Boolean
Dim bolGelangPasien As Boolean
Dim bolLembarIdentitasPasien As Boolean
Dim bolPernyataanRanap As Boolean
Dim bolSummaryList As Boolean
Dim bolTriageIgd As Boolean
Dim bolSuratJampersal As Boolean
Dim bolFormulirRajal As Boolean
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
    If bolBuktiPendaftaran = True Then
        crBuktiPendaftaran.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crBuktiPendaftaran.PrintOut False
    ElseIf bolTracer = True Then
        crTracetr.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crTracetr.PrintOut False
    ElseIf bolBlangkoBpjs = True Then
        crCetakBlangkoBpjs.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakBlangkoBpjs.PrintOut False
    ElseIf bolBuktiLayanan = True Then
        crCetakBuktiLayanan.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakBuktiLayanan.PrintOut False
    ElseIf bolLabelPasien = True Then
        crCetakLabelPasien.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakLabelPasien.PrintOut False
    ElseIf bolGelangPasien = True Then
        crCetakGelangPasien.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakGelangPasien.PrintOut False
    ElseIf bolLembarIdentitasPasien = True Then
        crSuratLembarIdentitasPasien.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crSuratLembarIdentitasPasien.PrintOut False
    ElseIf bolPernyataanRanap = True Then
        crPernyataanRanap.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crPernyataanRanap.PrintOut False
    ElseIf bolSummaryList = True Then
        reportSumList.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        reportSumList.PrintOut False
    ElseIf bolTriageIgd = True Then
        TriageIgd.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        TriageIgd.PrintOut False
    ElseIf bolSuratJampersal = True Then
        suratJampersal.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        suratJampersal.PrintOut False
    ElseIf bolFormulirRajal = True Then
        crFormulirRajal.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crFormulirRajal.PrintOut False
    End If
    SaveSetting "SMART", "SettingPrinter", "cboPrinter", PrinterNama
End Sub

Private Sub CmdOption_Click()
    If bolBuktiPendaftaran = True Then
        crBuktiPendaftaran.PrinterSetup Me.hwnd
    ElseIf bolTracer = True Then
        crTracetr.PrinterSetup Me.hwnd
    ElseIf bolBlangkoBpjs = True Then
        crCetakBlangkoBpjs.PrinterSetup Me.hwnd
    ElseIf bolBuktiLayanan = True Then
        crCetakBuktiLayanan.PrinterSetup Me.hwnd
    ElseIf bolLabelPasien = True Then
        crCetakLabelPasien.PrinterSetup Me.hwnd
    ElseIf bolGelangPasien = True Then
        crCetakGelangPasien.PrinterSetup Me.hwnd
    ElseIf bolLembarIdentitasPasien = True Then
        crSuratLembarIdentitasPasien.PrinterSetup Me.hwnd
    ElseIf bolPernyataanRanap = True Then
        crPernyataanRanap.PrinterSetup Me.hwnd
    ElseIf bolSummaryList = True Then
        reportSumList.PrinterSetup Me.hwnd
    ElseIf bolTriageIgd = True Then
        TriageIgd.PrinterSetup Me.hwnd
    ElseIf bolSuratJampersal = True Then
        suratJampersal.PrinterSetup Me.hwnd
    ElseIf bolFormulirRajal = True Then
        crFormulirRajal.PrinterSetup Me.hwnd
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
    Set frmRegistrasi = Nothing
End Sub

Public Sub cetakAntrianPendaftaran(strNorec As String, petugas As String, view As String)
On Error GoTo errLoad
Set frmRegistrasi = Nothing
Dim strSQL As String
bolBuktiPendaftaran = True
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False
Dim NamaPetugas As String
Dim alamatRs As String
Dim namaRs As String
Dim hari As String
Dim Tgl As String
Dim umur As String
    If petugas <> "" Then
        NamaPetugas = petugas
    Else
        NamaPetugas = "-"
    End If
    
    With crBuktiPendaftaran
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String

            strSQL = " SELECT pd.noregistrasi,ps.norm AS nocm,ps.tgllahir,ps.namapasien,pd.tglregistrasi, " & _
                     " CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '' END AS jk, " & _
                     " ap.mobilephone2,ru.namaruangan AS ruanganPeriksa, " & _
                     " pp.namalengkap AS namadokter,kp.kelompokpasien,ru.noruangan,apdp.noantrian AS noantri,ru.noruangan || '-0' || apdp.noantrian AS noantrian," & _
                     " pd.statuspasien,apr.noreservasi,apr.tanggalreservasi, " & _
                     " CASE WHEN pg1.namalengkap IS NULL THEN '' ELSE pg1.namalengkap END AS dokter, " & _
                     " pl.namaproduk,(tp.jumlah*(tp.hargasatuan-CASE WHEN tp.hargadiscount IS NULL THEN 0 ELSE tp.hargadiscount END+ " & _
                     " CASE WHEN tp.jasa IS NULL THEN 0 ELSE tp.jasa END)) AS harga, " & _
                     " ap.alamatlengkap || ' Kelurahan : ' || ap.namadesakelurahan || ' Kecamatan : ' || ap.namakecamatan || ' Kabupaten : ' || ap.namakotakabupaten || ' Provinsi : ' || " & _
                     " CASE WHEN pv.namapropinsi IS NULL THEN '' ELSE pv.namapropinsi END || ' Kode Pos : ' || ap.kodepos AS alamatlengkap " & _
                     " FROM registrasipasientr pd " & _
                     " INNER JOIN daftarpasienruangantr apdp ON apdp.registrasipasienfk = pd.norec AND apdp.ruanganidfk = pd.ruanganlastidfk " & _
                     " LEFT JOIN transaksipasientr AS tp ON tp.daftarpasienruanganfk = apdp.norec " & _
                     " INNER JOIN pasienmt ps ON pd.normidfk = ps.id " & _
                     " LEFT JOIN alamatmt ap ON ap.normidfk = ps.id " & _
                     " LEFT JOIN jeniskelaminmt jk ON ps.jeniskelaminidfk = jk.id " & _
                     " INNER JOIN ruanganmt ru ON pd.ruanganlastidfk = ru.id " & _
                     " LEFT JOIN pegawaimt pp ON pd.pegawaiidfk = pp.id " & _
                     " INNER JOIN kelompokpasienmt kp ON pd.kelompokpasienlastidfk = kp.id " & _
                     " LEFT JOIN perjanjianpasientr AS apr ON apr.noreservasi = pd.statusschedule " & _
                     " LEFT JOIN pegawaimt AS pg1 ON pg1.id = pd.dokterpemeriksaidfk " & _
                     " LEFT JOIN pelayananmt AS pl ON pl.id = tp.produkidfk " & _
                     " LEFT JOIN provinsimt AS pv ON pv.id = ap.provinsiidfk " & _
                     " WHERE pd.noregistrasi = '" & strNorec & "' "
                                 
            ReadRs strSQL
            If RS.EOF = False Then
                hari = getHari(RS!tglRegistrasi)
                Tgl = Format(RS!tglRegistrasi, "DD/MM/YYYY HH:mm")
                umur = Format(RS!tgllahir, "DD-MM-YYYY") & "/" & hitungUmur(Format(RS!tgllahir, "yyyy/MM/dd"), Format(RS!tglRegistrasi, "yyyy/MM/dd"))
            End If
            adoReport.CommandText = strSQL
            adoReport.CommandType = adCmdUnknown
            
            .database.AddADOCommand CN_String, adoReport
            .txtNamaRs.SetText strNamaRS
            .txtAlamatRs.SetText strAlamatRS & ", " & strKodePos
            .txtTelp.SetText strNoTlpn & " " & strNoFax
            .txttglRegistrasi.SetText hari + ", " + Tgl
            .usnoantri.SetUnboundFieldSource ("{ado.noantrian}")
            .udtgl.SetUnboundFieldSource ("{ado.tglregistrasi}")
            .usnodft.SetUnboundFieldSource ("{ado.noregistrasi}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usnmpasien.SetUnboundFieldSource ("{ado.namapasien}")
            .usJK.SetUnboundFieldSource ("{ado.jk}")
            ' .udTglLahir.SetUnboundFieldSource ("{ado.tgllahir}")
'            .usAlamat.SetUnboundFieldSource ("{ado.alamatlengkap}")
'            .usNoTelpon.SetUnboundFieldSource ("{ado.mobilephone2}")
'            .usDokter.SetUnboundFieldSource ("{ado.dokter}")
            .usPenjamin.SetUnboundFieldSource ("{ado.kelompokpasien}")
            .usruangperiksa.SetUnboundFieldSource ("{ado.ruanganPeriksa}")
'            .usNamaDokter.SetUnboundFieldSource ("{ado.namadokter}")
            .tglreservasi.SetUnboundFieldSource ("{ado.tanggalreservasi}")
            .usStatusPasien.SetUnboundFieldSource ("{ado.statuspasien}")
            .usNamaTindakan.SetUnboundFieldSource ("{ado.namaproduk}")
            .ucHargaTindakan.SetUnboundFieldSource ("{ado.harga}")
            .usStatusPasien.SetUnboundFieldSource ("{ado.statuspasien}")
            .txtPetugas.SetText NamaPetugas
            .usAlamat.SetUnboundFieldSource ("{ado.alamatlengkap}")
            .txtUmur.SetText umur

            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "BuktiPendaftaran")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crBuktiPendaftaran
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
     
    End With
Exit Sub
errLoad:

    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub cetakTracer(strNorec As String, view As String)
On Error GoTo errLoad
Set frmRegistrasi = Nothing
bolBuktiPendaftaran = False
bolTracer = True
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolPernyataanRanap = False
bolLembarIdentitasPasien = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False
Dim strSQL As String
Dim namaKomputer As String
namaKomputer = Winsock1.LocalHostName
    With crTracetr
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String
            
            strSQL = " SELECT pd.noregistrasi,ps.norm AS nocm,ps.tgllahir,ps.namapasien,pd.tglregistrasi, " & _
                     " CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '' END,jk.jeniskelamin AS jk, " & _
                     " ap.alamatlengkap,ap.mobilephone2,ru.namaruangan AS ruanganPeriksa, " & _
                     " pp.namalengkap AS namadokter,kp.kelompokpasien,apdp.noantrian,pd.statuspasien,ps.namaayah " & _
                     " FROM registrasipasientr pd " & _
                     " INNER JOIN pasienmt ps ON pd.normidfk = ps.id " & _
                     " LEFT JOIN alamatmt ap ON ap.normidfk = ps.id " & _
                     " LEFT JOIN jeniskelaminmt jk ON ps.jeniskelaminidfk = jk.id " & _
                     " INNER JOIN ruanganmt ru ON pd.ruanganlastidfk = ru.id " & _
                     " LEFT JOIN pegawaimt pp ON pd.pegawaiidfk = pp.id " & _
                     " INNER JOIN kelompokpasienmt kp ON pd.kelompokpasienlastidfk = kp.id " & _
                     " INNER JOIN daftarpasienruangantr apdp ON apdp.registrasipasienfk = pd.norec" & _
                     " WHERE pd.noregistrasi ='" & strNorec & "' "
            
            adoReport.CommandText = strSQL
            adoReport.CommandType = adCmdUnknown
            
            .database.AddADOCommand CN_String, adoReport
'            .usnoantri.SetUnboundFieldSource ("{ado.noantrian}")
'            .udtgl.SetUnboundFieldSource ("{ado.tglregistrasi}")
'            .usnodft.SetUnboundFieldSource ("{ado.noregistrasi}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usnmpasien.SetUnboundFieldSource ("{ado.namapasien}")
'            .usJK.SetUnboundFieldSource ("{ado.jk}")
            .usStatusPasien.SetUnboundFieldSource ("{ado.statuspasien}")
            .udtTglReg.SetUnboundFieldSource ("{ado.tglregistrasi}")
            .usnamadokter.SetUnboundFieldSource ("{ado.namadokter}")
            .usNamaKel.SetUnboundFieldSource ("{ado.namaayah}")
            .usruangperiksa.SetUnboundFieldSource ("{ado.ruanganPeriksa}")
            .namaPc.SetText namaKomputer
            If view = "false" Then
               

                strPrinter1 = GetTxt("Setting.ini", "Printer", "Tracer")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crTracetr
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
     
    End With
Exit Sub
errLoad:

    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub cetakBlangkoBpjs(strNorec As String, petugas As String, view As String)
On Error GoTo errLoad
Set frmRegistrasi = Nothing
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = True
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False
Dim strSQL As String
Dim str As String
Dim i As Integer
Dim StrNamaPetugas As String
Dim StrNip As String
Dim qty As String
qty = 2
If petugas <> "" Then
    ReadRs2 "select namalengkap, nippns from pegawaimt where id = '" & petugas & "' limit 1"
    If RS2.EOF = False Then
       StrNamaPetugas = RS2!namalengkap
       If IsNull(RS2!nippns) = True Then
         StrNip = "-"
       Else
         StrNip = RS2!nippns
      End If
    Else
       StrNamaPetugas = "-"
       StrNip = "-"
    End If
Else
   StrNamaPetugas = "-"
   StrNip = "-"
End If

    With crCetakBlangkoBpjs
        Set adoReport = New ADODB.Command
        adoReport.ActiveConnection = CN_String
        strSQL = " SELECT pi.namapasien,(CASE WHEN alm.alamatlengkap IS NULL THEN '-' ELSE alm.alamatlengkap   END) AS alamatlengkap, " & _
                 " pa.nosep,pa.tanggalsep,pa.nokepesertaan,to_char(pd.tglregistrasi,'DD-MM-YYYY') AS tglregistrasi, " & _
                 " pi.norm AS nocm,pd.noregistrasi,pa.norujukan,ap.namapeserta,to_char(pi.tgllahir, 'DD-MM-YYYY') AS tgllahir, " & _
                 " jk.jeniskelamin,rp.namaruangan,rp.kdinternal AS namapolibpjs,pa.ppkrujukan, " & _
                 " (CASE  WHEN rp.instalasiidfk = 16 THEN 'R. Inap' ELSE 'R. Jalan' END) AS jenisrawat, " & _
                 " dg.kddiagnosa,(CASE WHEN dg.namadiagnosa IS NULL THEN ''   ELSE dg.namadiagnosa END) AS namadiagnosa, " & _
                 " ap.jenispeserta,ap.kdprovider,ap.nmprovider,pa.catatan,(CASE WHEN rp.instalasiidfk = 16 THEN " & _
                 " kls.namakelas ELSE '-' END) AS namakelas,ap.notelpmobile,pa.penjaminlaka, " & _
                 " (CASE WHEN pa.penjaminlaka = '1' THEN 'Jasa Raharja PT' WHEN pa.penjaminlaka = '2' THEN " & _
                 " 'BPJS Ketenagakerjaan' WHEN pa.penjaminlaka = '3' THEN 'TASPEN PT' WHEN pa.penjaminlaka = '4' THEN " & _
                 " 'ASABRI PT' ELSE '-' END) AS penjaminlakalantas,pa.prolanisprb,CASE WHEN pi.noasuransilain IS NULL THEN " & _
                 " '-' ELSE pi.noasuransilain END AS noasuransilain,pi.hubungankeluargapj " & _
                 " FROM registrasipasientr AS pd " & _
                 " LEFT JOIN pemakaianasuransitr AS pa ON pa.registrasipasienfk = pd.norec " & _
                 " LEFT JOIN asuransimt ap ON pa.asuransiidfk = ap.id " & _
                 " LEFT JOIN pasienmt pi ON pi.id = pd.normidfk " & _
                 " LEFT JOIN jeniskelaminmt jk ON jk.id = pi.jeniskelaminidfk " & _
                 " LEFT JOIN ruanganmt rp ON rp.id = pd.ruanganlastidfk " & _
                 " LEFT JOIN icdxmt dg ON pa.icdxidfk = dg.id " & _
                 " LEFT JOIN kelasmt kls ON kls.id = ap.kelasdijaminidfk " & _
                 " LEFT JOIN alamatmt AS alm ON alm.normidfk = pi.id " & _
                 " WHERE pd.noregistrasi ='" & strNorec & "' "
                     
             ReadRs strSQL
             adoReport.CommandText = strSQL
             adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport
             If Not RS.EOF Then
               .txtNamaRs.SetText strNamaLengkapRs
               .txtNamaPemerintahan.SetText strNamaPemerintah
               .txtAlamat.SetText strAlamatRS & ", " & strKodePos
               .txtTelpFax.SetText strNoTlpn & " " & strNoFax
               .txtPengajuan.SetText "Direktur " & strNamaRS
               .txtWebEmail.SetText strEmail & " " & strWebSite
               .txtNamaPasien.SetText RS!namapasien
               .txtNamaPeserta.SetText RS!namapasien
               .txtTglLahir.SetText RS!tgllahir
               .txtTanggalLahir.SetText RS!tgllahir
               .txtJenisKelamin1.SetText RS!jeniskelamin
               .txtkelamin.SetText RS!jeniskelamin
               .txtnocm2.SetText RS!nocm
               .tglRegistrasi.SetText RS!tglRegistrasi
               .txtNomorKartuBpjs.SetText RS!noasuransilain
               .txtDiagnosa.SetText "..................................."
               .txtAlamatRumah.SetText RS!alamatlengkap
               .txtHubunganKeluarga.SetText IIf(IsNull(RS("hubungankeluargapj")), "-", RS("hubungankeluargapj"))
               .txtPtgasJamkes.SetText StrNamaPetugas
               .txtNip.SetText "Nip. " & StrNip
             End If

            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "BlangkoBpjs")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
'                .PrintOut False, 2, , 1, 1
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakBlangkoBpjs
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
     
    End With
Exit Sub
errLoad:

    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub cetakBuktiLayananRuangan(strNorec As String, strPegawai As String, strIdRuangan As String, view As String)
'On Error GoTo errLoad
Set frmRegistrasi = Nothing
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = True
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False
Dim strSQL As String
Dim umur As String
Dim StrFilter As String

    strSQL = ""
    StrFilter = ""
    If Left(strIdRuangan, 14) = "ORDERRADIOLOGI" Then
        strIdRuangan = Replace(strIdRuangan, "ORDERRADIOLOGI", "")
        StrFilter = " AND apdp.norec = '" & strIdRuangan & "' "
    Else
        If strIdRuangan <> "" Then StrFilter = " AND ru2.id = '" & strIdRuangan & "' "
    End If
    StrFilter = StrFilter ' & " and pro.id <> 402611 ORDER BY tp.tglpelayanan "
    With crCetakBuktiLayanan

            strSQL = " SELECT pd.noregistrasi || ' / ' || ps.norm AS nocm,ps.tgllahir,to_char(ps.tgllahir, 'DD-MM-YYYY') AS tglkelahiran, " & _
                     " ps.namapasien || ' ( ' || CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END || ' )' AS namapasien,to_char(apdp.tglregistrasi,'DD-MM-YYYY') AS tglregis,  " & _
                     " apdp.tglregistrasi,ru2.namaruangan || CASE WHEN kmr.namakamar IS NULL THEN ' ' ELSE 'kmr : '|| kmr.namakamar END || 'kls : ' || ks.namakelas AS ruanganperiksa,ru.namaruangan AS ruangakhir,  " & _
                     " (SELECT pg.namalengkap FROM pegawaimt AS pg INNER JOIN petugaspelaksanatr AS p3 ON p3.pegawaiidfk = pg.id Where p3.transaksipasienfk = tp.Norec And p3.jenispetugaspeidfk = 4 LIMIT 1) AS namadokter, " & _
                     " kp.kelompokpasien,tp.produkidfk,pro.namaproduk,tp.jumlah,CASE WHEN tp.hargasatuan IS NULL THEN tp.hargajual ELSE tp.hargasatuan END AS hargasatuan,(CASE WHEN tp.hargadiscount IS NULL THEN 0 ELSE tp.hargadiscount END) * tp.jumlah AS diskon, " & _
                     " tp.hargasatuan * tp.jumlah AS total,ar.asalrujukan,CASE WHEN rek.namarekanan IS NULL THEN '-' ELSE rek.namarekanan END AS namapenjamin,kmr.namakamar,pg1.namalengkap AS dktrdpjp,alm.alamatlengkap,ps.nohp " & _
                     " FROM registrasipasientr AS pd " & _
                     " INNER JOIN pasienmt AS ps ON pd.normidfk = ps.id " & _
                     " LEFT JOIN jeniskelaminmt AS jk ON ps.jeniskelaminidfk = jk.id " & _
                     " LEFT JOIN kelompokpasienmt AS kp ON pd.kelompokpasienlastidfk = kp.id " & _
                     " LEFT JOIN daftarpasienruangantr AS apdp ON apdp.registrasipasienfk = pd.norec " & _
                     " LEFT JOIN ruanganmt AS ru ON pd.ruanganasalidfk = ru.id " & _
                     " LEFT JOIN transaksipasientr AS tp ON tp.daftarpasienruanganfk = apdp.norec " & _
                     " LEFT JOIN pegawaimt AS pp ON apdp.pegawaiidfk = pp.id " & _
                     " LEFT JOIN pelayananmt AS pro ON tp.produkidfk = pro.id " & _
                     " LEFT JOIN kelasmt AS ks ON apdp.kelasidfk = ks.id " & _
                     " LEFT JOIN rujukanasalmt AS ar ON apdp.asalrujukanidfk = ar.id " & _
                     " LEFT JOIN rekananmt AS rek ON rek.id = pd.rekananidfk " & _
                     " LEFT JOIN kamarmt AS kmr ON apdp.kamaridfk = kmr.id " & _
                     " INNER JOIN ruanganmt AS ru2 ON ru2.id = apdp.ruanganidfk " & _
                     " LEFT JOIN pegawaimt AS pg1 ON pg1.id = pd.pegawaiidfk " & _
                     " LEFT JOIN alamatmt AS alm ON alm.normidfk = ps.id " & _
                     " WHERE pd.noregistrasi ='" & strNorec & "'" & StrFilter
                     
            ReadRs strSQL
'           ReadJson strSQL
            
            adoReport.CommandText = strSQL
            adoReport.CommandType = adCmdUnknown
            
            
            .database.AddADOCommand CN_String, adoReport
'           .database.SetDataSource rs
'           .database.SetDataSource = rs
            If RS.BOF Then
                .txtUmur.SetText "-"
            Else
                .txtUmur.SetText UCase(IIf(IsNull(RS("tglKelahiran")), "-", RS("tglKelahiran"))) & " (" & hitungUmur(Format(RS!tgllahir, "yyyy/MM/dd"), Format(RS!tglRegistrasi, "yyyy/MM/dd")) & ")"
            End If
            .txtNamaRs.SetText UCase$(strNamaLengkapRs)
            .txtAlamat.SetText stralmtLengkapRs
            .txtNamaKota.SetText strNamaKota & ", "
            .udtgl.SetUnboundFieldSource ("{ado.tglregistrasi}")
            .usNoRegistrasi.SetUnboundFieldSource ("{ado.nocm}")
            .usnmpasien.SetUnboundFieldSource ("{ado.namapasien}")
            .usUnitLayanan.SetUnboundFieldSource ("{ado.ruanganperiksa}")
            .usTipe.SetUnboundFieldSource ("{ado.kelompokpasien}")
            .usRujukan.SetUnboundFieldSource ("{ado.asalrujukan}")
'            .usruangperiksa.SetUnboundFieldSource ("{ado.ruangakhir}")
'            .usKamar.SetUnboundFieldSource ("if isnull({ado.namakamar}) then "" - "" else {ado.namakamar} ")
'            .usKelas.SetUnboundFieldSource ("if isnull({ado.namakelas}) then "" - "" else {ado.namakelas} ") '("{ado.namakelas}")
            .usPenjamin.SetUnboundFieldSource ("{ado.namapenjamin}")
            .usDokter.SetUnboundFieldSource ("{ado.namadokter}")
            .usNoHp.SetUnboundFieldSource ("{ado.nohp}")
            
            .usQty.SetUnboundFieldSource ("{ado.jumlah}")
            .usPelayanan.SetUnboundFieldSource ("{ado.namaproduk}") '
            .ucTarif.SetUnboundFieldSource ("{ado.hargasatuan}")
            .ucDiskon.SetUnboundFieldSource ("{ado.diskon}")
            .usJumlah.SetUnboundFieldSource ("{ado.jumlah}")
            .unQtyN.SetUnboundFieldSource ("{ado.jumlah}")
            .usDokterDpjp.SetUnboundFieldSource ("{ado.dktrdpjp}")
            .usAlamat.SetUnboundFieldSource ("{ado.alamatlengkap}")
'           .ucTotal.SetUnboundFieldSource ("{ado.total}")
            .txtuser.SetText strPegawai
            
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "BuktiLayanan")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakBuktiLayanan
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
     
    End With
Exit Sub
errLoad:
    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub cetakLabelPasien(strNorec As String, qty As String, view As String)
On Error GoTo errLoad
Set frmRegistrasi = Nothing
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = True
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False
Dim strSQL As String
Dim i As Integer
Dim str As String
Dim jml As Integer
Dim Barcode As Image
Dim filename As String

    With crCetakLabelPasien
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String
            strSQL = " SELECT pd.noregistrasi,pd.tglregistrasi,pm.norm AS nocm,'*' || pm.norm || '*' AS nocmbarcode,UPPER (pm.namapasien) AS namapasien, " & _
                     " jk.jeniskelamin AS jk,CASE WHEN alm.alamatlengkap IS NOT NULL THEN UPPER (alm.alamatlengkap) ELSE '' END AS alamat, " & _
                     " to_char(pm.tgllahir, 'DD-MM-YYYY') AS tgllahir,EXTRACT ( YEAR FROM AGE(pd.tglregistrasi,pm.tgllahir)) || ' Thn ' || ' / ' || " & _
                     " to_char(pm.tgllahir,'DD / MM / YYYY') || ' / (' || UPPER (CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END) || ')' AS umur, " & _
                     " to_char(pd.tglregistrasi, 'HH24:MI') || ' | ' || to_char(pd.tglregistrasi,'DD-MM-YYYY') AS tglregis " & _
                     " FROM registrasipasientr pd " & _
                     " INNER JOIN pasienmt pm ON pd.normidfk = pm.id " & _
                     " INNER JOIN jeniskelaminmt jk ON jk.id = pm.jeniskelaminidfk " & _
                     " LEFT JOIN alamatmt AS alm ON alm.normidfk = pm.id " & _
                     " LEFT JOIN ruanganmt AS ru ON ru.id = pd.ruanganlastidfk " & _
                     " LEFT JOIN instalasimt AS dep ON dep.id = ru.instalasiidfk " & _
                     " where pd.noregistrasi ='" & strNorec & "' "
            ReadRs2 strSQL
            
            str = ""
            If Val(qty) - 1 = 0 Then
                adoReport.CommandText = strSQL
             Else
                For i = 1 To Val(qty) - 1
                    str = strSQL & " union all " & str
                Next
                
                adoReport.CommandText = str & strSQL
           
           End If
           
            adoReport.CommandType = adCmdUnknown
            
            
            .database.AddADOCommand CN_String, adoReport
            If RS2.EOF = False Then
                .usNamaPasien.SetUnboundFieldSource ("{ado.namapasien}")
                .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
                .txtTglLahir.SetText IIf(IsNull(RS2("umur")), "", RS2("umur"))
                .txtAlamatPasien.SetText IIf(IsNull(RS2("alamat")), "", RS2("alamat"))
            End If
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "LabelPasien")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakLabelPasien
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
     
    End With
Exit Sub
errLoad:

    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub cetak_KartuPasien(strNocm As String)
On Error GoTo errLoad
Dim prn As Printer
Set frmRegistrasi = Nothing
Dim strPrinter As String
    
    strSQL = " SELECT ps.namapasien || ' ( ' || CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END || ' ) ' AS namapasien, " & _
             " ps.norm AS nocm,ps.tgllahir,ps.namaayah,CASE WHEN ps.namakeluarga IS NULL THEN '-' ELSE ps.namakeluarga END AS namakeluarga, " & _
             " ps.namasuamiistri,ps.jeniskelaminidfk,ps.statusperkawinanidfk,CASE WHEN alm.alamatlengkap IS NULL THEN '' ELSE alm.alamatlengkap END AS alamat " & _
             " FROM pasienmt ps LEFT JOIN jeniskelaminmt jk ON jk.id = ps.jeniskelaminidfk " & _
             " LEFT JOIN alamatmt AS alm ON alm.normidfk = ps.id " & _
             " WHERE ps.norm = '" & strNocm & "' "
      
    ReadRs strSQL
      
    strPrinter = GetTxt("Setting.ini", "Printer", "KartuPasien")
    If Printers.Count > 0 Then
        For Each prn In Printers
            If prn.DeviceName = strPrinter Then
                Set Printer = prn
                Exit For
            End If
        Next prn
    End If
    
    Dim msg As String
    Dim ayah As String
    Dim ayah2 As String
    Dim umur As String
    Dim splt() As String
    
    If IsNull(RS!statusperkawinanidfk) <> 1 Then
        If RS!statusperkawinanidfk = 2 Then 'kawin
            ayah = IIf(IsNull(RS!namakeluarga) = True, "", RS!namakeluarga)
        Else
            ayah = IIf(IsNull(RS!namakeluarga) = True, "", RS!namakeluarga)
        End If
    Else
        If RS!statusperkawinanidfk = 2 Then 'kawin
            ayah = ""
        Else
            ayah = IIf(IsNull(RS!namakeluarga) = True, "", RS!namakeluarga)
        End If
    End If
    If ayah <> "" Then
        splt = Split(ayah, " ")
        ayah = splt(0)
    End If
    If IsNull(RS!tgllahir) = True Then
        umur = ""
    Else
        umur = hitungUmur(Format(RS!tgllahir, "dd-MMM-yyyy"), Format(Now, "yyyy-MM-dd"))
    End If
    ayah2 = IIf(IsNull(RS!tgllahir) = True, "", Format(RS!tgllahir, "dd-MMM-yyyy"))
    Printer.FontName = "Tahoma"
    Printer.FontSize = 8
    Printer.Print ""
    Printer.Print ""
    Printer.Print ""
    Printer.Print ""
    Printer.FontBold = True
'    Printer.fontSize = 8
'    Printer.Print "                                                    " & Left(ayah, 17)
    Printer.FontSize = 8
    Printer.Print "               " & RS!nocm
    Printer.Print "               " & Left(RS!namapasien, 17)
    Printer.Print "               " & umur
    Printer.Print "               " & Left(RS!alamat, 25)
    Printer.FontSize = 8
'    Printer.Print "                                                    " & ayah2
'    Printer.Print ""
    
    Printer.FontBold = False
'    Printer.FontName = "Free 3 of 9 Extended" '"Bar-Code 39"
'    Printer.fontSize = 27 '20
'    Printer.CurrentX = 2900
'    Printer.CurrentY = 2250
'    Call make128(RS!nocm)
'    Printer.PaintPicture Picture1.Image, 2500, 2250
'
'    Printer.FontBold = True
'    Printer.FontName = "Tahoma"
'    Printer.FontSize = 17
'    Printer.CurrentX = 300
'    Printer.CurrentY = 2550
'    Printer.Print RS!nocm
    Printer.EndDoc
    
'     PrintFrontSideOnly strPrinter, "", "", msg, RS!nocm, RS!namapasien, ayah, ayah2
    
   Exit Sub
   
errLoad:
    MsgBox Err.Number & " " & Err.Description
End Sub

Private Sub make128(angka As Double)
Dim X As Long, Y As Long, pos As Long
Dim Bardata As String
Dim Cur As String
Dim CurVal As Long
Dim chksum As Long
Dim temp As String
Dim bc(106) As String
    'code 128 is basically the ASCII chr set.
    '4 element sizes : 1=narrowest, 4=widest
    bc(0) = "212222" '<SPC>
    bc(1) = "222122" '!
    bc(2) = "222221" '"
    bc(3) = "121223" '#
    bc(4) = "121322" '$
    bc(5) = "131222" '%
    bc(6) = "122213" '&
    bc(7) = "122312" ''
    bc(8) = "132212" '(
    bc(9) = "221213" ')
    bc(10) = "221312" '*
    bc(11) = "231212" '+
    bc(12) = "112232" ',
    bc(13) = "122132" '-
    bc(14) = "122231" '.
    bc(15) = "113222" '/
    bc(16) = "123122" '0
    bc(17) = "123221" '1
    bc(18) = "223211" '2
    bc(19) = "221132" '3
    bc(20) = "221231" '4
    bc(21) = "213212" '5
    bc(22) = "223112" '6
    bc(23) = "312131" '7
    bc(24) = "311222" '8
    bc(25) = "321122" '9
    bc(26) = "321221" ':
    bc(27) = "312212" ';
    bc(28) = "322112" '<
    bc(29) = "322211" '=
    bc(30) = "212123" '>
    bc(31) = "212321" '?
    bc(32) = "232121" '@
    bc(33) = "111323" 'A
    bc(34) = "131123" 'B
    bc(35) = "131321" 'C
    bc(36) = "112313" 'D
    bc(37) = "132113" 'E
    bc(38) = "132311" 'F
    bc(39) = "211313" 'G
    bc(40) = "231113" 'H
    bc(41) = "231311" 'I
    bc(42) = "112133" 'J
    bc(43) = "112331" 'K
    bc(44) = "132131" 'L
    bc(45) = "113123" 'M
    bc(46) = "113321" 'N
    bc(47) = "133121" 'O
    bc(48) = "313121" 'P
    bc(49) = "211331" 'Q
    bc(50) = "231131" 'R
    bc(51) = "213113" 'S
    bc(52) = "213311" 'T
    bc(53) = "213131" 'U
    bc(54) = "311123" 'V
    bc(55) = "311321" 'W
    bc(56) = "331121" 'X
    bc(57) = "312113" 'Y
    bc(58) = "312311" 'Z
    bc(59) = "332111" '[
    bc(60) = "314111" '\
    bc(61) = "221411" ']
    bc(62) = "431111" '^
    bc(63) = "111224" '_
    bc(64) = "111422" '`
    bc(65) = "121124" 'a
    bc(66) = "121421" 'b
    bc(67) = "141122" 'c
    bc(68) = "141221" 'd
    bc(69) = "112214" 'e
    bc(70) = "112412" 'f
    bc(71) = "122114" 'g
    bc(72) = "122411" 'h
    bc(73) = "142112" 'i
    bc(74) = "142211" 'j
    bc(75) = "241211" 'k
    bc(76) = "221114" 'l
    bc(77) = "413111" 'm
    bc(78) = "241112" 'n
    bc(79) = "134111" 'o
    bc(80) = "111242" 'p
    bc(81) = "121142" 'q
    bc(82) = "121241" 'r
    bc(83) = "114212" 's
    bc(84) = "124112" 't
    bc(85) = "124211" 'u
    bc(86) = "411212" 'v
    bc(87) = "421112" 'w
    bc(88) = "421211" 'x
    bc(89) = "212141" 'y
    bc(90) = "214121" 'z
    bc(91) = "412121" '{
    bc(92) = "111143" '|
    bc(93) = "111341" '}
    bc(94) = "131141" '~
    bc(95) = "114113" '<DEL>        *not used in this sub
    bc(96) = "114311" 'FNC 3        *not used in this sub
    bc(97) = "411113" 'FNC 2        *not used in this sub
    bc(98) = "411311" 'SHIFT        *not used in this sub
    bc(99) = "113141" 'CODE C       *not used in this sub
    bc(100) = "114131" 'FNC 4       *not used in this sub
    bc(101) = "311141" 'CODE A      *not used in this sub
    bc(102) = "411131" 'FNC 1       *not used in this sub
    bc(103) = "211412" 'START A     *not used in this sub
    bc(104) = "211214" 'START B
    bc(105) = "211232" 'START C     *not used in this sub
    bc(106) = "2331112" 'STOP

    Picture1.Cls
'    If Text1.Text = "" Then Exit Sub
    pos = 20
    Bardata = angka 'Text1.Text

    'Check for invalid characters, calculate check sum & build temp string
    For X = 1 To Len(Bardata)
        Cur = Mid$(Bardata, X, 1)
        If Cur < " " Or Cur > "~" Then
            Picture1.Print "Invalid Character(s)"
            Exit Sub
        End If
        CurVal = Asc(Cur) - 32
        temp = temp + bc(CurVal)
        chksum = chksum + CurVal * X
    Next
    
    'Add start, stop & check characters
    chksum = (chksum + 104) Mod 103
    temp = bc(104) & temp & bc(chksum) & bc(106)

    'Generate Barcode
    For X = 1 To Len(temp)
        If X Mod 2 = 0 Then
                'SPACE
                pos = pos + (Val(Mid$(temp, X, 1))) + 1
        Else
                'BAR
                For Y = 1 To (Val(Mid$(temp, X, 1)))
                    Picture1.Line (pos, 1)-(pos, 58 - 0 * 8)
                    pos = pos + 1
                Next
        End If
    Next

    'Add Label?
'    If Check1(1).Value Then
'        Picture1.CurrentX = 30 + Len(Bardata) * (3 + 1 * 2) 'kinda center
'        Picture1.CurrentY = 50
'        Picture1.Print Bardata;
'    End If
End Sub

Public Sub cetakGelangPasien(strNorec As String, qty As String, view As String)
On Error GoTo errLoad
Set frmRegistrasi = Nothing
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = True
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False
Dim strSQL As String
Dim i As Integer
Dim str As String
Dim jml As Integer
Dim Barcode As Image
Dim filename As String
Dim jk As String


    With crCetakGelangPasien
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String
            strSQL = " SELECT pd.noregistrasi,pd.tglregistrasi,ps.norm AS nocm,CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END AS reportdisplay, " & _
                     " UPPER (ps.namapasien) || '' || CASE WHEN jk.id = 1 THEN '(L)' WHEN jk.id = 2 THEN '(P)' ELSE '' END AS namapasiens, " & _
                     " CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END AS jk,UPPER (alm.alamatlengkap) AS alamat,to_char(ps.tgllahir, 'DD-MM-YYYY') AS tgllahirs,ps.tgllahir, " & _
                     " CASE WHEN pg.namalengkap IS NULL THEN '-' ELSE pg.namalengkap END AS namadokter,'*' || ps.norm || '*' AS barcode,EXTRACT (YEAR FROM AGE( pd.tglregistrasi,ps.tgllahir)) || ' Thn ' " & _
                     " || EXTRACT (MONTH FROM AGE(pd.tglregistrasi,ps.tgllahir)) || ' Bln ' || EXTRACT (DAY FROM   AGE(pd.tglregistrasi,ps.tgllahir)) || ' Hr' AS umur " & _
                     " FROM registrasipasientr pd " & _
                     " INNER JOIN pasienmt ps ON pd.normidfk = ps.id " & _
                     " INNER JOIN jeniskelaminmt jk ON jk.id = ps.jeniskelaminidfk " & _
                     " LEFT JOIN pegawaimt AS pg ON pg.id = pd.pegawaiidfk " & _
                     " LEFT JOIN alamatmt AS alm ON alm.normidfk = ps.id" & _
                     " WHERE pd.noregistrasi ='" & strNorec & "' "
            
            jml = qty - 1
            ReadRs2 strSQL
            If RS2.BOF Then
                jk = ""
            Else
                jk = RS2!reportdisplay
            End If
            str = ""
            If Val(qty) - 1 = 0 Then
                adoReport.CommandText = strSQL
             Else
                For i = 1 To Val(qty) - 1
                    str = strSQL & " union all " & str
                Next
                
                adoReport.CommandText = str & strSQL
           
           End If
                adoReport.CommandType = adCmdUnknown
                .database.AddADOCommand CN_String, adoReport
                .usNamaPasien.SetUnboundFieldSource ("{ado.namapasiens}")
                .usTglLahir.SetUnboundFieldSource ("{ado.tgllahirs}")
                '.usBarcode.SetUnboundFieldSource ("{ado.barcode}")
                '.usUmur.SetUnboundFieldSource ("{ado.umur}")
                .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "GelangPasien")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakGelangPasien
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
     
    End With
Exit Sub
errLoad:

    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub CetakLembarIdentitasPasien(nocm As String, noreg As String, Carabayar As String, petugas As String, view As String)
On Error GoTo errLoad
On Error Resume Next
Set frmRegistrasi = Nothing
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = True
bolPernyataanRanap = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False
Dim adocmd As New ADODB.Command
Dim strNocm As String
Dim strUmur As String
Dim strcaraBayar As String
Dim strPetugas As String
Dim strPenanggungjawab As String
Dim tglRegis As String
Dim JamRegis As String

strNocm = ""
If nocm <> "" Then
    strNocm = nocm
Else
    strNocm = ""
End If
'
'If umur <> "" Then
'    strUmur = umur
'Else
'    strUmur = "0"
'End If

If petugas <> "" Then
    strPetugas = petugas
Else
    strPetugas = "-"
End If

If Carabayar <> "" Then
    strcaraBayar = Carabayar
Else
    strcaraBayar = "-"
End If

         strSQL = " SELECT pm.norm AS nocm,pm.namapasien,pm.tempatlahir,to_char(pm.tgllahir, 'DD-MM-YYYY') AS tgllahir, " & _
                  " CASE WHEN sp.statusperkawinan IS NULL THEN '-' ELSE sp.statusperkawinan END AS statusperkawinan,CASE WHEN ag.agama IS NULL THEN '-' ELSE ag.agama END AS agama, " & _
                  " CASE WHEN pg2.namalengkap IS NULL THEN '-' ELSE pg2.namalengkap END AS namauser,CASE WHEN pk.pekerjaan IS NULL THEN '-' ELSE pk.pekerjaan END AS pekerjaan, " & _
                  " alm.alamatlengkap,alm.namadesakelurahan,alm.kecamatan,alm.kotakabupaten,prop.namapropinsi,alm.kodepos,pm.alamatktr,CASE WHEN pm.nohp IS NULL THEN '-' ELSE pm.nohp END AS notelepon, " & _
                  " CASE WHEN pm.noidentitas IS NULL THEN '-' ELSE pm.noidentitas END AS noidentitas,CASE WHEN gd.golongandarah IS NULL THEN '-' ELSE   gd.golongandarah END AS golongandarah, " & _
                  " CASE WHEN pen.pendidikan IS NULL THEN '-' ELSE pen.pendidikan END AS pendidikan, " & _
                  " kn.NAME AS kebangsaan,CASE WHEN pm.noasuransilain IS NOT NULL THEN pm.noasuransilain WHEN pm.nobpjs IS NOT NULL THEN pm.nobpjs ELSE '-' END AS noasuransilain, " & _
                  " jk.jeniskelamin,pm.penanggungjawab,pm.hubungankeluargapj,pm.pekerjaanpenangggungjawab,pm.ktppenanggungjawab,pm.alamatrmh AS almtpenanggungjawab,pm.namaayah,pm.namaibu, " & _
                  " pm.namasuamiistri,pm.teleponpenanggungjawab,pm.bahasa,pm.alamatdokterpengirim,pm.dokterpengirim,pm.jeniskelaminpenanggungjawab,pm.umurpenanggungjawab,ru.namaruangan,pg.namalengkap,kp.kelompokpasien,EXTRACT (YEAR FROM AGE(pd.tglregistrasi,pm.tgllahir)) || ' Tahun/Year ' " & _
                  " || EXTRACT (MONTH FROM AGE(pd.tglregistrasi,pm.tgllahir)) || ' Bulan/Month ' || EXTRACT (DAY FROM AGE(pd.tglregistrasi,pm.tgllahir)) || ' Hari/Day' AS umur " & _
                  " FROM registrasipasientr AS pd " & _
                  " INNER JOIN pasienmt AS pm ON pm.id = pd.normidfk " & _
                  " LEFT JOIN statusperkawinanmt AS sp ON sp.id = pm.statusperkawinanidfk " & _
                  " LEFT JOIN agamamt AS ag ON ag.id = pm.agamaidfk " & _
                  " LEFT JOIN pekerjaanmt AS pk ON pk.id = pm.pekerjaanidfk " & _
                  " LEFT JOIN pendidikanmt AS pen ON pen.id = pm.pendidikanidfk " & _
                  " LEFT JOIN alamatmt AS alm ON alm.normidfk = pm.id " & _
                  " LEFT JOIN provinsimt AS prop ON prop.id = alm.provinsiidfk " & _
                  " LEFT JOIN golongandarahmt AS gd ON gd.id = pm.golongandarahidfk " & _
                  " LEFT JOIN jeniskelaminmt AS jk ON jk.id = pm.jeniskelaminidfk " & _
                  " LEFT JOIN ruanganmt AS ru ON ru.id = pd.ruanganlastidfk LEFT JOIN pegawaimt AS pg ON pg.id = pd.dokterpemeriksaidfk " & _
                  " LEFT JOIN kelompokpasienmt AS kp ON kp.id = pd.kelompokpasienlastidfk LEFT JOIN loggingusertr AS lg ON lg.noreff = pd.norec " & _
                  " AND lg.jenislog = 'Pendaftaran Pasien' LEFT JOIN loginuser_s AS lu ON lu.id = lg.loginuserfk LEFT JOIN pegawaimt AS pg2 ON pg2.id = lu.objectpegawaifk  " & _
                  " LEFT JOIN kebangsaanmt AS kn ON kn.id = pm.kebangsaanidfk " & _
                  " where pd.noregistrasi = '" & noreg & "' and pd.aktif = true and pd.koders = '" & stridRs & "' limit 1 "
    
    ReadRs2 strSQL
    adocmd.CommandText = strSQL
    adocmd.CommandType = adCmdText
    With crCetakSuratLembarIdentitasPasien
        .database.AddADOCommand CN_String, adocmd
        If Not RS2.EOF Then
            .txtNamaRs.SetText UCase$(strNamaLengkapRs)
            .txtAlamatRs.SetText stralmtLengkapRs
            .txtnocm.SetText IIf(IsNull(RS2("nocm")), "", RS2("nocm"))
            .txtNamaPasien.SetText IIf(IsNull(RS2("namapasien")), "", RS2("namapasien"))
            .txtNamaPasangan.SetText IIf(IsNull(RS2("namasuamiistri")), "", RS2("namasuamiistri"))
            .txtKelompokPasien.SetText IIf(IsNull(RS2("kelompokpasien")), "", RS2("kelompokpasien"))
            .txtNoAsuransi.SetText IIf(IsNull(RS2("noasuransilain")), "", RS2("noasuransilain"))
            .txtJenisKelamin.SetText IIf(IsNull(RS2("jeniskelamin")), "", RS2("jeniskelamin"))
            .txtTempatLahir.SetText IIf(IsNull(RS2("tempatlahir")), "", RS2("tempatlahir"))
            .txtTglLahir.SetText IIf(IsNull(RS2("tgllahir")), "", RS2("tgllahir"))
            .txtUmur.SetText IIf(IsNull(RS2("umur")), "", RS2("umur"))
            .txtAlamatPasien.SetText IIf(IsNull(RS2("alamatlengkap")), "", RS2("alamatlengkap"))
            .txtProvinsi.SetText IIf(IsNull(RS2("namapropinsi")), "", RS2("namapropinsi"))
            .txtKotaKab.SetText IIf(IsNull(RS2("kotakabupaten")), "", RS2("kotakabupaten"))
            .txtKecamatan.SetText IIf(IsNull(RS2("kecamatan")), "", RS2("kecamatan"))
            .txtKelurahan.SetText IIf(IsNull(RS2("namadesakelurahan")), "", RS2("namadesakelurahan"))
            .txtNotelepon.SetText IIf(IsNull(RS2("notelepon")), "", RS2("notelepon"))
            .txtStatusPerkawinan.SetText IIf(IsNull(RS2("statusperkawinan")), "", RS2("statusperkawinan"))
            .txtPendidikan.SetText IIf(IsNull(RS2("pendidikan")), "", RS2("pendidikan"))
            .txtKewarganegaraan.SetText IIf(IsNull(RS2("kebangsaan")), "", RS2("kebangsaan"))
            .txtGolDarah.SetText IIf(IsNull(RS2("golongandarah")), "", RS2("golongandarah"))
            .txtAgama.SetText IIf(IsNull(RS2("agama")), "", RS2("agama"))
            .txtPekerjaan.SetText IIf(IsNull(RS2("pekerjaan")), "", RS2("pekerjaan"))
            .txtNoIdentitas.SetText IIf(IsNull(RS2("noidentitas")), "", RS2("noidentitas"))
            .txtuser.SetText IIf(IsNull(RS2("namauser")), "", RS2("namauser"))
        End If
        
        If view = "false" Then
            Dim strPrinter As String
            strPrinter = GetTxt("Setting.ini", "Printer", "LembarIdentitas")
            .SelectPrinter "winspool", strPrinter, "Ne00:"
            .PrintOut False
            Unload Me
        Else
             With CRViewer1
                .ReportSource = crCetakSuratLembarIdentitasPasien
                .ViewReport
                .Zoom 1
            End With
            Me.Show
            Screen.MousePointer = vbDefault
        End If
        
    End With
Exit Sub
errLoad:
End Sub

Public Sub CetakPersetujuanRanap(strNoreg As String, strUser As String, view As String)
On Error GoTo errLoad
Dim adocmd As New ADODB.Command
Set frmRegistrasi = Nothing
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = True
bolSummaryList = False
bolSuratJampersal = False
bolTriageIgd = False
bolFormulirRajal = False
Dim strSQL As String
Dim idPegawai As String
Dim noreg As String
Dim User As String
Dim UmurPasien As String
Set frmRegistrasi = Nothing
If strNoreg <> "" Then
    noreg = strNoreg
Else
    noreg = ""
End If

If strUser <> "" Then
    User = strUser
Else
    User = " - "
End If

    With crPernyataanRanap
        
        Set adoReport = New ADODB.Command
        adoReport.ActiveConnection = CN_String
              
        strSQL = " SELECT pm.namapasien,pm.notelepon,pm.norm AS nocm,alm.alamatlengkap,pk.pekerjaan, " & _
                 " CASE WHEN pm.penanggungjawab IS NULL THEN pm.namapasien ELSE pm.penanggungjawab END AS penanggungjawab, " & _
                 " pm.umurpenanggungjawab,pm.pekerjaanpenangggungjawab,pm.umurpenanggungjawab,pm.teleponpenanggungjawab, " & _
                 " pm.tgllahir,CASE WHEN pm.hubungankeluargapj IS NULL THEN 'Pasien' ELSE pm.hubungankeluargapj END AS hubungankeluargapj, " & _
                 " CASE WHEN pm.alamatrmh IS NULL THEN alm.alamatlengkap ELSE pm.alamatrmh END AS alamatrmh " & _
                 " FROM registrasipasientr AS pd " & _
                 " LEFT JOIN pasienmt AS pm ON pm.id = pd.normidfk " & _
                 " LEFT JOIN pekerjaanmt AS pk ON pk.id = pm.pekerjaanidfk " & _
                 " LEFT JOIN jeniskelaminmt AS jk ON jk.id = pm.jeniskelaminidfk " & _
                 " LEFT JOIN alamatmt AS alm ON alm.normidfk = pm.id " & _
                 " WHERE pd.noregistrasi = '" & noreg & "' LIMIT 1 "

       ReadRs strSQL
        
        adoReport.CommandText = strSQL
        adoReport.CommandType = adCmdUnknown
        .database.AddADOCommand CN_String, adoReport
        .txtNamaPemerintahan.SetText UCase$(strNamaPemerintah)
        .txtNamaRs.SetText UCase$(strNamaLengkapRs)
        .txtNamaRs2.SetText strNamaLengkapRs
        .txtAlamatRs.SetText stralmtLengkapRs
        .txtNamaKota.SetText strNamaKota & ", "
        .txtNomorSurat.SetText "       /RSUD/       /" & Format(Now, "YYYY")
        If Not RS.EOF Then
            .txtNamaPasien.SetText IIf(IsNull(RS("namapasien")), "", RS("namapasien"))
            If IsNull(RS!tgllahir) Then
               .txtUmuP.SetText "-"
               UmurPasien = "-"
            Else
               .txtUmuP.SetText hitungUmur(Format(RS!tgllahir, "yyyy/MM/dd"), Format(Now, "yyyy/MM/dd"))
               UmurPasien = hitungUmur(Format(RS!tgllahir, "yyyy/MM/dd"), Format(Now, "yyyy/MM/dd"))
            End If
            .txtAlamatP.SetText IIf(IsNull(RS("alamatlengkap")), "", RS("alamatlengkap"))
            .txtPekerjaanP.SetText IIf(IsNull(RS("pekerjaan")), "", RS("pekerjaan"))
            .txtPenanggungJawab.SetText IIf(IsNull(RS("penanggungjawab")), "", RS("penanggungjawab"))
            .txtUmurPenanggungJawab.SetText IIf(IsNull(RS("umurpenanggungjawab")), UmurPasien, RS("umurpenanggungjawab")) & " Tahun"
            .txtAlamatPenanggungJawab.SetText IIf(IsNull(RS("alamatrmh")), "", RS("alamatrmh"))
            .txtPekerjaanPenanggungJawab.SetText IIf(IsNull(RS("pekerjaanpenangggungjawab")), "", RS("pekerjaanpenangggungjawab"))
        End If

        If view = "false" Then
            strPrinter1 = GetTxt("Setting.ini", "Printer", "PernyataanRawatInap")
            .SelectPrinter "winspool", strPrinter1, "Ne00:"
            .PrintOut False
            Unload Me
            Screen.MousePointer = vbDefault
         Else
            With CRViewer1
                .ReportSource = crPernyataanRanap
                .ViewReport
                .Zoom 1
            End With
            Me.Show
            Screen.MousePointer = vbDefault
        End If
    End With
Exit Sub
errLoad:
End Sub

Public Sub cetakSummaryList(strNorec As String, view As String)
On Error GoTo errLoad
Set frmRegistrasi = Nothing
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = True
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False
Dim strSQL As String

    With reportSumList
            Set adoReport = New ADODB.Command
             adoReport.ActiveConnection = CN_String
            
            strSQL = " SELECT ps.norm AS nocm,ps.namapasien,ps.namaayah,CASE WHEN ps.namakeluarga IS NULL THEN '-' ELSE ps.namakeluarga END AS namakeluarga, " & _
                     " ps.tempatlahir,ps.tgllahir,jk.jeniskelamin,ps.noidentitas,ag.agama,pk.pekerjaan,kb.name AS kebangsaan, " & _
                     " CASE WHEN al.alamatlengkap IS NULL THEN '-' ELSE al.alamatlengkap END AS alamatlengkap, " & _
                     " CASE WHEN al.kotakabupaten IS NULL THEN '-' ELSE al.kotakabupaten END AS kotakabupaten, " & _
                     " CASE WHEN al.kecamatan IS NULL THEN '-' ELSE al.kecamatan END AS kecamatan, " & _
                     " CASE WHEN al.namadesakelurahan IS NULL THEN '-' ELSE al.namadesakelurahan END AS namadesakelurahan, " & _
                     " ps.notelepon AS mobilephone1,sp.statusperkawinan " & _
                     " FROM pasienmt AS ps " & _
                     " LEFT JOIN jeniskelaminmt jk ON jk.id = ps.jeniskelaminidfk " & _
                     " LEFT JOIN alamatmt al ON ps.id = al.normidfk " & _
                     " LEFT JOIN agamamt ag ON ps.agamaidfk = ag.id " & _
                     " LEFT JOIN pekerjaanmt pk ON pk.id = ps.pekerjaanidfk " & _
                     " LEFT JOIN kebangsaanmt kb ON kb.id = ps.kebangsaanidfk " & _
                     " LEFT JOIN statusperkawinanmt sp ON sp.id = ps.statusperkawinanidfk " & _
                     " WHERE ps.norm = '" & strNorec & "' "
            
            ReadRs strSQL
                
            adoReport.CommandText = strSQL
            adoReport.CommandType = adCmdUnknown
            
            .database.AddADOCommand CN_String, adoReport
            .txtNamaRs.SetText strNamaLengkapRs
            .txtAlamatRs.SetText stralmtLengkapRs
            If RS.BOF Then
                .txtUmur.SetText "-"
            Else
                .txtUmur.SetText hitungUmur(Format(RS!tgllahir, "yyyy/MM/dd"), Format(Now, "yyyy/MM/dd"))
            End If
            .txtlTglLahir.SetText Format(RS!tgllahir, "yyyy/MM/dd")
            .usNamaPasien.SetUnboundFieldSource ("{ado.namapasien}")
            .usNamaKeuarga.SetUnboundFieldSource ("{ado.namakeluarga}")
            .udTglLahir.SetUnboundFieldSource ("{ado.tglLahir}")
            .usJK.SetUnboundFieldSource ("{ado.jeniskelamin}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usAlamat.SetUnboundFieldSource ("{ado.alamatlengkap}")
            .usKota.SetUnboundFieldSource ("{ado.kotakabupaten}")
                      
           .usKel.SetUnboundFieldSource ("{ado.namadesakelurahan}")
           .usKec.SetUnboundFieldSource ("{ado.kecamatan}")
           .usHp.SetUnboundFieldSource ("{ado.mobilephone1}")
           .usTL.SetUnboundFieldSource ("{ado.tempatlahir}")
           .usAgama.SetUnboundFieldSource ("{ado.agama}")
           .usKebgsaan.SetUnboundFieldSource ("{ado.kebangsaan}")
           .usPekerjaan.SetUnboundFieldSource ("{ado.pekerjaan}")
           .usStatusPerkawinan.SetUnboundFieldSource ("{ado.statusperkawinan}")
           .usKatp.SetUnboundFieldSource ("{ado.noidentitas}")
           
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "SummaryList")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = reportSumList
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
     
    End With
Exit Sub
errLoad:
    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub cetakTriage(strNorec As String, view As String)
On Error GoTo errLoad
Set frmRegistrasi = Nothing
Dim strSQL As String
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = True
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False

    With TriageIgd
            Set adoReport = New ADODB.Command
             adoReport.ActiveConnection = CN_String

            strSQL = " SELECT pd.noregistrasi,ps.tgllahir, ps.namapasien,ps.norm AS nocm,to_char(pd.tglregistrasi,'yyyy/MM/dd') as tglregistrasi, to_char(pd.tglregistrasi,'HH:MI') as jamregistrasi,ps.tgllahir, jk.jeniskelamin,alm.alamatlengkap,ps.nohp " & _
                     " from registrasipasientr as pd " & _
                     " join pasienmt as ps on pd.normidfk = ps.id " & _
                     " left join alamatmt as alm on alm.normidfk=ps.id " & _
                     " LEFT JOIN jeniskelaminmt as jk on jk.id= ps.jeniskelaminidfk " & _
                     " WHERE pd.noregistrasi='" & strNorec & "' "

            ReadRs strSQL
            adoReport.CommandText = strSQL
            adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport

            If RS.BOF Then
                .txtUmur.SetText "-"
            Else
                .txtUmur.SetText hitungUmur(Format(RS!tgllahir, "yyyy/MM/dd"), Format(Now, "yyyy/MM/dd"))
            End If
            .txtUmur.SetText Format(RS!tgllahir, "yyyy  MM  dd")
            .usNamaPasien.SetUnboundFieldSource ("{ado.namapasien}")
            .udTglLahir.SetUnboundFieldSource ("{ado.tgllahir}")
            .usJK.SetUnboundFieldSource ("{ado.jeniskelamin}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usAlamat.SetUnboundFieldSource ("{ado.alamatlengkap}")
            .usJamRegis.SetUnboundFieldSource ("{ado.jamregistrasi}")
            .usTglRegis.SetUnboundFieldSource ("{ado.tglregistrasi}")
            .usHp.SetUnboundFieldSource ("{ado.nohp}")

            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "TriageIGD")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = TriageIgd
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If

    End With
Exit Sub
errLoad:
    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub cetakSuratJampersal(strNorec As String, view As String)
On Error GoTo errLoad
Set frmRegistrasi = Nothing
Dim strSQL As String
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = True
bolFormulirRajal = False

    With suratJampersal
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String

            strSQL = " SELECT CASE WHEN ps.penanggungjawab IS NULL THEN ps.namapasien ELSE ps.penanggungjawab END AS penanggungjawab, " & _
                     " CASE WHEN ps.hubungankeluargapj IS NULL THEN 'Pasien' ELSE ps.hubungankeluargapj END AS hubungankeluargapj, " & _
                     " CASE WHEN ps.alamatrmh IS NULL THEN alm.alamatlengkap ELSE ps.alamatrmh END AS alamatrmh,ps.jeniskelaminpenanggungjawab, " & _
                     " CASE WHEN ps.ktppenanggungjawab IS NULL THEN ps.noidentitas ELSE ps.ktppenanggungjawab END AS ktppenanggungjawab " & _
                     " from registrasipasientr AS rp " & _
                     " INNER JOIN pasienmt AS ps ON ps.id = rp.normidfk " & _
                     " LEFT JOIN alamatmt AS alm ON alm.normidfk = ps.id " & _
                     " WHERE rp.aktif = true AND rp.noregistrasi = '" & strNorec & "' "

            ReadRs strSQL
            adoReport.CommandText = strSQL
            adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport
            
            .txtNamaPemerintahan.SetText UCase$(strNamaPemerintah)
            .txtNamaRs.SetText UCase$(strNamaLengkapRs)
            .txtAlamatRs.SetText stralmtLengkapRs
            .txtNamaKota.SetText strNamaKota & ","
            .txtNamaRs2.SetText strNamaLengkapRs
            If RS.EOF = False Then
                .txtNomorSurat.SetText ".........../RS/JAMPERSAL/" & Format(Now, "YYYY")
                .txtPenanggungJawab.SetText IIf(IsNull(RS("penanggungjawab")), "", RS("penanggungjawab"))
                .txtAlamat.SetText IIf(IsNull(RS("alamatrmh")), "", RS("alamatrmh"))
                .txtHubPasien.SetText IIf(IsNull(RS("hubungankeluargapj")), "", RS("hubungankeluargapj"))
                .txtJenisKelamin.SetText IIf(IsNull(RS("jeniskelaminpenanggungjawab")), "", RS("jeniskelaminpenanggungjawab"))
                .txtAlamat.SetText IIf(IsNull(RS("alamatrmh")), "", RS("alamatrmh"))
                .txtNoIdentitas.SetText IIf(IsNull(RS("ktppenanggungjawab")), "", RS("ktppenanggungjawab"))
                .txtTandaTangan.SetText IIf(IsNull(RS("penanggungjawab")), "", RS("penanggungjawab"))
            End If

            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "suratJampersal")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = suratJampersal
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If

    End With
Exit Sub
errLoad:
    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub cetakAntrianPendaftaranOnline(strNorec As String, petugas As String, view As String)
On Error GoTo errLoad
Set frmRegistrasi = Nothing
Dim strSQL As String
bolBuktiPendaftaran = True
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = False
Dim NamaPetugas As String
Dim alamatRs As String
Dim namaRs As String
Dim hari As String
Dim Tgl As String
    If petugas <> "" Then
        NamaPetugas = petugas
    Else
        NamaPetugas = "-"
    End If
    
    With crBuktiPendaftaran
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String

            strSQL = " SELECT pd.noregistrasi,ps.norm AS nocm,ps.tgllahir,ps.namapasien,pd.tglregistrasi, " & _
                     " CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '' END AS jk,ap.alamatlengkap,ap.mobilephone2, " & _
                     " ru.namaruangan AS ruanganPeriksa,pp.namalengkap AS namadokter, " & _
                     " kp.kelompokpasien,apdp.noantrian,pd.statuspasien,apr.noreservasi, " & _
                     " apr.tanggalreservasi,CASE WHEN pg1.namalengkap IS NULL THEN '' ELSE pg1.namalengkap END AS dokter " & _
                     " FROM registrasipasientr pd " & _
                     " INNER JOIN pasienmt ps ON pd.normidfk = ps.id " & _
                     " LEFT JOIN alamatmt ap ON ap.normidfk = ps.id " & _
                     " LEFT JOIN jeniskelaminmt jk ON ps.jeniskelaminidfk = jk.id " & _
                     " INNER JOIN ruanganmt ru ON pd.ruanganlastidfk = ru.id " & _
                     " LEFT JOIN pegawaimt pp ON pd.pegawaiidfk = pp.id " & _
                     " INNER JOIN kelompokpasienmt kp ON pd.kelompokpasienlastidfk = kp.id " & _
                     " INNER JOIN daftarpasienruangantr apdp ON apdp.registrasipasienfk = pd.norec " & _
                     " LEFT JOIN perjanjianpasientr AS apr ON apr.noreservasi = pd.statusschedule " & _
                     " LEFT JOIN pegawaimt AS pg1 ON pg1.id = pd.dokterpemeriksaidfk " & _
                     " WHERE   pd.noregistrasi = '" & strNorec & "' "
                                 
            ReadRs strSQL
            If RS.EOF = False Then
                hari = getHari(RS!tglRegistrasi)
                Tgl = Format(RS!tglRegistrasi, "DD/MM/YYYY HH:mm")
            End If
            adoReport.CommandText = strSQL
            adoReport.CommandType = adCmdUnknown
            
            .database.AddADOCommand CN_String, adoReport
            .txtNamaRs.SetText strNamaRS
            .txtAlamatRs.SetText strAlamatRS & ", " & strKodePos
            .txtTelp.SetText strNoTlpn & " " & strNoFax
            .txttglRegistrasi.SetText hari + ", " + Tgl
            .usnoantri.SetUnboundFieldSource ("{ado.noantrian}")
            .udtgl.SetUnboundFieldSource ("{ado.tglregistrasi}")
            .usnodft.SetUnboundFieldSource ("{ado.noregistrasi}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usnmpasien.SetUnboundFieldSource ("{ado.namapasien}")
            .usJK.SetUnboundFieldSource ("{ado.jk}")
            .usPenjamin.SetUnboundFieldSource ("{ado.kelompokpasien}")
            .usruangperiksa.SetUnboundFieldSource ("{ado.ruanganPeriksa}")
            .tglreservasi.SetUnboundFieldSource ("{ado.tanggalreservasi}")
            .usStatusPasien.SetUnboundFieldSource ("{ado.statuspasien}")
            .txtPetugas.SetText NamaPetugas

            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "BuktiPendaftaran")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crBuktiPendaftaran
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
     
    End With
Exit Sub
errLoad:

    MsgBox Err.Number & " " & Err.Description
End Sub

Public Sub cetakFormulirRajal(strNorec As String, view As String)
'On Error GoTo errLoad
Set frmRegistrasi = Nothing
Dim strSQL As String
bolBuktiPendaftaran = False
bolTracer = False
bolBlangkoBpjs = False
bolBuktiLayanan = False
bolLabelPasien = False
bolGelangPasien = False
bolLembarIdentitasPasien = False
bolPernyataanRanap = False
bolSummaryList = False
bolTriageIgd = False
bolSuratJampersal = False
bolFormulirRajal = True
Dim NamaPetugas As String
Dim alamatRs As String
Dim namaRs As String
Dim keterangan As String
Dim Soap As String
Dim umur As String
Dim dpjp As String
    
    With crFormulirRajal
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String

            strSQL = " SELECT pn.namapasien || ' (' || CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END || ') ' AS namapasien, " & _
                     " rg.tglregistrasi,rg.noregistrasi,pn.norm,pn.tempatlahir,pn.tgllahir,CASE WHEN pn.namaayah IS NULL THEN '' ELSE pn.namaayah END || '/' || " & _
                     " CASE WHEN pn.namasuamiistri IS NULL THEN '' ELSE pn.namasuamiistri END AS namaayah,ag.agama,pd.pendidikan,pk.pekerjaan, " & _
                     " CASE WHEN pn.notelepon IS NULL THEN '' ELSE pn.notelepon END || '/' || " & _
                     " CASE WHEN pn.nohp IS NULL THEN '' ELSE pn.nohp END AS nohp, " & _
                     " alm.alamatlengkap || ' Kel. ' || CASE WHEN alm.namadesakelurahan IS NULL THEN '' ELSE alm.namadesakelurahan END || ' Kec. ' " & _
                     " || CASE WHEN alm.namakecamatan IS NULL THEN '' ELSE alm.namakecamatan END || ' Kab. ' " & _
                     " || CASE WHEN alm.namakotakabupaten IS NULL THEN '' ELSE alm.namakotakabupaten END " & _
                     " || ' Prov. ' || CASE WHEN alm.provinsiidfk IS NULL THEN ' ' ELSE prov.namapropinsi END AS alamatlengkap " & _
                     " FROM registrasipasientr AS rg " & _
                     " INNER JOIN pasienmt AS pn ON pn.id = rg.normidfk " & _
                     " LEFT JOIN jeniskelaminmt AS jk ON jk.id = pn.jeniskelaminidfk " & _
                     " LEFT JOIN pendidikanmt AS pd ON pd.id = pn.pendidikanidfk " & _
                     " LEFT JOIN pekerjaanmt AS pk ON pk.id = pn.pekerjaanidfk " & _
                     " LEFT JOIN agamamt AS ag ON ag.id = pn.agamaidfk " & _
                     " LEFT JOIN alamatmt AS alm ON alm.normidfk = pn.id " & _
                     " LEFT JOIN provinsimt AS prov ON prov.id = alm.provinsiidfk " & _
                     " WHERE rg.noregistrasi = '" & strNorec & "' "
                     
            ReadRs2 " SELECT sp.tglinput,CASE WHEN sp.s IS NULL THEN 'S : ' ELSE 'S : ' || sp.s || '*' END || ' ' || " & _
                    " CASE WHEN sp.o IS NULL THEN 'O : ' ELSE 'O : ' || sp.o || '*' END || ' ' || CASE WHEN sp.a IS NULL THEN 'A : ' ELSE 'A : ' || sp.a || '*' END || ' ' || " & _
                    " CASE WHEN sp.p IS NULL THEN 'P : ' ELSE 'P : ' || sp.p || '*' END || ' ' AS soap, " & _
                    " CASE WHEN pg.namalengkap IS NULL THEN '' ELSE pg.namalengkap END dpjp " & _
                    " FROM registrasipasientr AS rg " & _
                    " INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = rg.norec " & _
                    " INNER JOIN soaptr AS sp ON sp.daftarpasienruanganfk = dpr.norec " & _
                    " LEFT JOIN pegawaimt AS pg ON pg.id = dpr.pegawaiidfk " & _
                    " WHERE rg.noregistrasi = '" & strNorec & "' "
                    
            Dim i, j, k As Integer
            i = 0
            j = 0
            k = 0
            Dim tglinput As String
            Dim resep As String
            Dim diagnosa As String
            If RS2.EOF = False Then
                dpjp = IIf(IsNull(RS2("dpjp")), "", RS2("dpjp"))
                keterangan = IIf(IsNull(RS2("soap")), "", RS2("soap"))
                Soap = Replace(keterangan, "*", vbCrLf)
                For i = 0 To RS2.RecordCount - 1
                    tglinput = tglinput & " " & vbCrLf & RS2!tglinput
                    RS2.MoveNext
                Next
            End If
            
            ReadRs3 " select pro.namaproduk FROM daftarpasienruangantr as apd " & _
                    " INNER JOIN registrasipasientr AS rg ON rg.norec = apd.registrasipasienfk " & _
                    " INNER JOIN transaksireseptr as sr on sr.daftarpasienruanganfk = apd.norec " & _
                    " INNER JOIN transaksipasientr as pp on pp.strukresepidfk = sr.norec " & _
                    " INNER JOIN pelayananmt as pro on pro.id = pp.produkidfk " & _
                    " WHERE rg.noregistrasi = '" & strNorec & "' "
            
            If RS3.EOF = False Then
                For j = 0 To RS3.RecordCount - 1
                    resep = resep & " " & vbCrLf & RS3!namaproduk
                    RS3.MoveNext
                Next
            End If
            
            ReadRs4 " SELECT icd.kddiagnosa || '-' || icd.namadiagnosa AS diagnosa " & _
                    " FROM registrasipasientr AS rg " & _
                    " INNER JOIN daftarpasienruangantr AS dpr ON dpr.registrasipasienfk = rg.norec " & _
                    " INNER JOIN diagnosapasienicdxtr AS dp ON dp.daftarpasienruanganfk = dpr.norec " & _
                    " INNER JOIN icdxmt AS icd ON icd.id = dp.icdxidfk " & _
                    " WHERE rg.noregistrasi = '" & strNorec & "' "
            If RS4.EOF = False Then
                For k = 0 To RS4.RecordCount - 1
                    diagnosa = diagnosa & " " & vbCrLf & RS4!diagnosa
                    RS4.MoveNext
                Next
            End If

            ReadRs strSQL
            If RS.EOF = False Then
                umur = hitungUmur(Format(RS!tgllahir, "yyyy/MM/dd"), Format(RS!tglRegistrasi, "yyyy/MM/dd"))
            Else
                umur = "-"
            End If
            adoReport.CommandText = strSQL
            adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport
            .txtNamaRs.SetText UCase$(strNamaLengkapRs)
            .txtNamaPasien.SetText IIf(IsNull(RS("namapasien")), "", RS("namapasien"))
            .txtTTL.SetText IIf(IsNull(RS("tempatlahir")), "", RS("tempatlahir")) & " / " & IIf(IsNull(RS("tgllahir")), "", Format(RS!tgllahir, "dd-MM-yyyy")) & " Umur : " & umur
            .txtNmaSuami.SetText IIf(IsNull(RS("namaayah")), "", RS("namaayah"))
            .txtAgama.SetText IIf(IsNull(RS("agama")), "", RS("agama"))
            .txtPendidikan.SetText IIf(IsNull(RS("pendidikan")), "", RS("pendidikan"))
            .txtPekerjaan.SetText IIf(IsNull(RS("pekerjaan")), "", RS("pekerjaan"))
            .txtTelp.SetText IIf(IsNull(RS("nohp")), "", RS("nohp"))
            .txtAlamat.SetText IIf(IsNull(RS("alamatlengkap")), "", RS("alamatlengkap"))
            .txtNorm.SetText IIf(IsNull(RS("norm")), "", RS("norm"))
            .txtSoap.SetText Soap
            .txtTglInput.SetText tglinput
            .txtObat.SetText resep
            .txtDpjp.SetText dpjp
            .txtDiagnosa.SetText diagnosa
            
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "FormulirRawatJalan")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crFormulirRajal
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
    End With
Exit Sub
errLoad:

    MsgBox Err.Number & " " & Err.Description
End Sub

