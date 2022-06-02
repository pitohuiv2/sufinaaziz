VERSION 5.00
Object = "{C4847593-972C-11D0-9567-00A0C9273C2A}#8.0#0"; "crviewer.dll"
Object = "{248DD890-BB45-11CF-9ABC-0080C7E7B78D}#1.0#0"; "MSWINSCK.OCX"
Begin VB.Form frmFarmasi 
   Caption         =   "Farmasi"
   ClientHeight    =   7005
   ClientLeft      =   60
   ClientTop       =   405
   ClientWidth     =   5820
   Icon            =   "frmFarmasi.frx":0000
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
Attribute VB_Name = "frmFarmasi"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
'# VARIABEL CR #'
Dim crCetakAntrianFarmasi As New crAntrianFarmasi
Dim crCetakLabelFarmasi As New crLabelFarmasi
Dim crCetakResep As New crRincianResep
Dim crCetakRincianResepRajal As New crRincianResepRajal
Dim crCetakRincianResepRanap As New crRincianResepRanap
'# VARIABEL FUNC #'
Dim bolNomorAntrian As Boolean
Dim bolLabelFarmasi As Boolean
Dim bolRincianResep As Boolean
Dim bolRincianResepRajal As Boolean
Dim bolRincianResepRanap As Boolean
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
    If bolNomorAntrian = True Then
        crCetakAntrianFarmasi.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakAntrianFarmasi.PrintOut False
    ElseIf bolLabelFarmasi = True Then
        crCetakLabelFarmasi.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakLabelFarmasi.PrintOut False
    ElseIf bolRincianResep = True Then
        crRincianResep.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crRincianResep.PrintOut False
    ElseIf bolRincianResepRajal = True Then
        crCetakRincianResepRajal.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakRincianResepRajal.PrintOut False
    ElseIf bolRincianResepRanap = True Then
        crCetakRincianResepRanap.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakRincianResepRanap.PrintOut False
    End If
    SaveSetting "SMART", "SettingPrinter", "cboPrinter", PrinterNama
End Sub

Private Sub CmdOption_Click()
    If bolNomorAntrian = True Then
        crCetakAntrianFarmasi.PrinterSetup Me.hwnd
    ElseIf bolLabelFarmasi = True Then
        crCetakLabelFarmasi.PrinterSetup Me.hwnd
    ElseIf bolRincianResep = True Then
        crRincianResep.PrinterSetup Me.hwnd
    ElseIf bolRincianResepRajal = True Then
        crCetakRincianResepRajal.PrinterSetup Me.hwnd
    ElseIf bolRincianResepRanap = True Then
        crCetakRincianResepRanap.PrinterSetup Me.hwnd
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
    Set frmFarmasi = Nothing
End Sub
Public Sub cetakAntrianFarmasi(strNorec As String, strNoregis As String, view As String)
On Error GoTo errLoad
On Error Resume Next
Set frmFarmasi = Nothing
bolNomorAntrian = True
bolLabelFarmasi = False
bolRincianResep = False
bolRincianResepRajal = False
bolRincianResepRanap = False
Dim strSQL As String
Dim strSQL2 As String
Dim str1 As String
Dim NamaPetugas As String
Dim alamatRs As String
Dim namaRs As String
Dim Apoteker As String

    If strNorec <> "" Then
        str1 = Replace(strNorec, "\", "/")
    End If
    
    With crCetakAntrianFarmasi
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String
            strSQL = " SELECT pd.noregistrasi,ps.norm AS nocm,ps.tgllahir,ps.namapasien,pd.tglregistrasi,ap.alamatlengkap, " & _
                     " CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END AS jk, " & _
                     " ap.mobilephone2,ru.namaruangan AS ruanganPeriksa,pp.namalengkap AS namadokter, " & _
                     " kp.kelompokpasien , apdp.noantrian, pd.statuspasien, apr.noreservasi, apr.tanggalreservasi " & _
                     " FROM registrasipasientr pd " & _
                     " INNER JOIN pasienmt AS ps ON pd.normidfk = ps.id " & _
                     " LEFT JOIN alamatmt AS ap ON ap.normidfk = ps.id " & _
                     " LEFT JOIN jeniskelaminmt AS jk ON ps.jeniskelaminidfk = jk.id " & _
                     " LEFT JOIN ruanganmt AS ru ON pd.ruanganlastidfk = ru.id " & _
                     " LEFT JOIN pegawaimt AS pp ON pd.pegawaiidfk = pp.id " & _
                     " LEFT JOIN kelompokpasienmt AS kp ON pd.kelompokpasienlastidfk = kp.id " & _
                     " INNER JOIN daftarpasienruangantr AS apdp ON apdp.registrasipasienfk = pd.norec " & _
                     " LEFT JOIN perjanjianpasientr AS apr ON apr.noreservasi = pd.statusschedule " & _
                     " WHERE pd.noregistrasi = '" & strNoregis & "' "

            strSQL2 = "select noantri,jenis,tglresep,noresep FROM antrianapotiktr where noresep = '" & str1 & "'"
                     
            ReadRs3 strSQL2
            
            adoReport.CommandText = strSQL
            adoReport.CommandType = adCmdUnknown
             If RS3.EOF = False Then
                .database.AddADOCommand CN_String, adoReport
'                .usnoantri.SetUnboundFieldSource ("{ado.noantrian}")
                .txtNamaRs.SetText strNamaLengkapRs
                .txtAlamatRs.SetText strAlamatRS & ", " & strKodePos
                .txtTelpFax.SetText strNoTlpn & ", " & strNoFax
                .udtgl.SetUnboundFieldSource ("{ado.tglregistrasi}")
                .usnodft.SetUnboundFieldSource ("{ado.noregistrasi}")
                .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
                .usnmpasien.SetUnboundFieldSource ("{ado.namapasien}")
                .usJK.SetUnboundFieldSource ("{ado.jk}")
                .udTglLahir.SetUnboundFieldSource ("{ado.tgllahir}")
                .usAlamat.SetUnboundFieldSource ("{ado.alamatlengkap}")
                .usNoTelpon.SetUnboundFieldSource ("{ado.mobilephone2}")
                .usPenjamin.SetUnboundFieldSource ("{ado.kelompokpasien}")
                .usruangperiksa.SetUnboundFieldSource ("{ado.ruanganPeriksa}")
                .usnamadokter.SetUnboundFieldSource ("{ado.namadokter}")
                .usStatusPasien.SetUnboundFieldSource ("{ado.statuspasien}")
                .txtAntrian.SetText RS3!jenis & "-" & RS3!NoAntri
             End If
                        
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "AntrianFarmasi")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakAntrianFarmasi
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

Public Sub CetakLabelFarmasi(Norec As String, petugas As String, layanan As String, view As String)
On Error GoTo errLoad
On Error Resume Next
Dim adocmd As New ADODB.Command
Dim strSQL As String
Dim str1, str2 As String
Dim Apoteker As String
Set frmFarmasi = Nothing
bolNomorAntrian = False
bolLabelFarmasi = True
bolRincianResep = False
bolRincianResepRajal = False
bolRincianResepRanap = False
Apoteker = "Apoteker : " & petugas
If Norec <> "" Then
    str1 = " AND sr.norec = '" & Norec & "'"
End If

    If layanan = "LAYANAN" Then
        strSQL = " SELECT DISTINCT ps.norm AS nocm,ps.namapasien,to_char(ps.tgllahir, 'DD-MM-YYYY') AS tgllahir,sr.noresep,to_char(sr.tglresep, 'DD-MM-YYYY') AS tglresep, " & _
                 " pr.namaproduk || ' (' || CAST (pp.jumlah AS VARCHAR) || ')' AS namaproduk,pp.aturanpakai,pp.rke,CASE WHEN alm.alamatlengkap IS NULL THEN '-' ELSE alm.alamatlengkap END AS alamat, " & _
                 " ss.satuanstandar,pp.jumlah,CASE WHEN pp.issiang = 't' THEN 'Siang' ELSE '-' END AS siang,CASE WHEN pp.ispagi = 't' THEN 'Pagi' ELSE '-' END AS pagi, " & _
                 " CASE WHEN pp.ismalam = 't' THEN 'Malam' ELSE '-' END AS malam,CASE WHEN pp.issore = 't' THEN 'Sore' ELSE '-' END AS sore,CASE WHEN pp.keteranganpakai = '' OR pp.keteranganpakai IS NULL THEN '' ELSE pp.keteranganpakai END AS keteranganpakai, " & _
                 " '(' || EXTRACT (YEAR FROM AGE(to_date(to_char(pd.tglregistrasi,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(ps.tgllahir, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || 'Thn ' " & _
                 " || EXTRACT (MONTH FROM AGE(to_date(to_char(pd.tglregistrasi,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(ps.tgllahir, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || ' Bln ' " & _
                 " || EXTRACT (DAY FROM AGE(to_date(to_char(pd.tglregistrasi,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(ps.tgllahir, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || ' Hr)' AS umur, " & _
                 " CASE WHEN pp.tglkadaluarsa IS NULL THEN '' ELSE to_char(pp.tglkadaluarsa,'DD-MM-YYYY HH:mm') END AS tglkadaluarsa, " & _
                 " CASE WHEN sn.satuanresep IS NOT NULL THEN sn.satuanresep ELSE ss.satuanstandar END AS satuanresep " & _
                 " FROM transaksireseptr AS sr " & _
                 " INNER JOIN transaksipasientr AS pp ON pp.strukresepidfk = sr.norec " & _
                 " INNER JOIN pelayananmt AS pr ON pr.id = pp.produkidfk " & _
                 " INNER JOIN daftarpasienruangantr AS apd ON apd.norec = pp.daftarpasienruanganfk " & _
                 " INNER JOIN registrasipasientr AS pd ON pd.norec = apd.registrasipasienfk " & _
                 " INNER JOIN pasienmt AS ps ON ps.id = pd.normidfk " & _
                 " LEFT JOIN alamatmt AS alm ON alm.normidfk = ps.id " & _
                 " LEFT JOIN satuanstandarmt AS ss ON ss.id = pp.satuanviewidfk " & _
                 " LEFT JOIN antrianapotiktr AS aa ON aa.noresep = sr.noresep " & _
                 " LEFT JOIN satuanresepmt AS sn ON sn.id = pp.satuanresepidfk " & _
                 " WHERE pp.jeniskemasanidfk = 2 " & str1
            
         strSQL = strSQL & " UNION ALL SELECT DISTINCT ps.norm AS nocm,ps.namapasien,to_char(ps.tgllahir, 'DD-MM-YYYY') AS tgllahir,sr.noresep,to_char(sr.tglresep, 'DD-MM-YYYY') AS tglresep, " & _
                  " ' Racikan' || ' (' || CAST (((CAST (pp.qtydetailresep AS INTEGER) / CAST (pp.dosis AS INTEGER)) * " & _
                  " CASE WHEN pr.kekuatan IS NOT NULL THEN CAST (pr.kekuatan AS INTEGER) ELSE 1 END) AS VARCHAR) || ')' AS namaproduk, " & _
                  " pp.aturanpakai,pp.rke,CASE WHEN alm.alamatlengkap IS NULL THEN '-' ELSE alm.alamatlengkap  END AS alamat, " & _
                  " CASE WHEN jr.jenisracikan IS NULL THEN '' ELSE jr.jenisracikan END AS satuanstandar, " & _
                  " ((CAST (pp.qtydetailresep AS INTEGER) / CAST (pp.dosis AS INTEGER)) * " & _
                  " CASE WHEN pr.kekuatan IS NOT NULL THEN CAST (pr.kekuatan AS INTEGER) ELSE 1 END) AS jumlah, " & _
                  " CASE WHEN pp.issiang = 't' THEN 'Siang' ELSE '-' END AS siang,CASE WHEN pp.ispagi = 't' THEN 'Pagi' ELSE '-' END AS pagi, " & _
                  " CASE WHEN pp.ismalam = 't' THEN 'Malam' ELSE '-' END AS malam,CASE WHEN pp.issore = 't' THEN 'Sore' ELSE '-' END AS sore, " & _
                  " CASE WHEN pp.keteranganpakai = '' OR pp.keteranganpakai IS NULL THEN '' ELSE pp.keteranganpakai END AS keteranganpakai, " & _
                  " '(' || EXTRACT (YEAR FROM AGE(to_date(to_char(pd.tglregistrasi,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(ps.tgllahir, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || 'Thn ' " & _
                  " || EXTRACT (MONTH FROM AGE(to_date(to_char(pd.tglregistrasi,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(ps.tgllahir, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || ' Bln ' " & _
                  " || EXTRACT (DAY FROM AGE(to_date(to_char(pd.tglregistrasi,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(ps.tgllahir, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || ' Hr)' AS umur, " & _
                  " CASE WHEN pp.tglkadaluarsa IS NULL THEN '' ELSE to_char(pp.tglkadaluarsa,'DD-MM-YYYY HH:mm') END AS tglkadaluarsa, " & _
                  " CASE WHEN sn.satuanresep IS NOT NULL THEN sn.satuanresep ELSE ss.satuanstandar END AS satuanresep " & _
                  " FROM transaksireseptr AS sr INNER JOIN transaksipasientr AS pp ON pp.strukresepidfk = sr.norec " & _
                  " INNER JOIN pelayananmt AS pr ON pr.id = pp.produkidfk INNER JOIN daftarpasienruangantr AS apd ON apd.norec = pp.daftarpasienruanganfk " & _
                  " INNER JOIN registrasipasientr AS pd ON pd.norec = apd.registrasipasienfk " & _
                  " INNER JOIN pasienmt AS ps ON ps.id = pd.normidfk LEFT JOIN alamatmt AS alm ON alm.normidfk = ps.id " & _
                  " LEFT JOIN satuanstandarmt AS ss ON ss.id = pp.satuanviewidfk " & _
                  " LEFT JOIN antrianapotiktr AS aa ON aa.noresep = sr.noresep " & _
                  " LEFT JOIN satuanresepmt AS sn ON sn.id = pp.satuanresepidfk " & _
                  " LEFT JOIN jenisracikanmt as jr on jr.id = pp.jenisobatidfk " & _
                  " WHERE pp.jeniskemasanidfk = 1 " & str1
    Else
        
        strSQL = " SELECT sr.nostruk_intern AS nocm,sr.namapasien_klien AS namapasien,CASE WHEN sr.tglfaktur IS NULL THEN '' ELSE to_char(sr.tglfaktur, 'DD-MM-YYYY') END AS tgllahir, " & _
                 " sr.nostruk AS noresep,to_char(sr.tglstruk, 'DD-MM-YYYY') AS tglresep,pr.namaproduk || ' (' || spd.qtyproduk || ' )' AS namaproduk, " & _
                 " spd.aturanpakai,spd.resepke AS rke,CASE WHEN sr.namatempattujuan IS NULL THEN '-' ELSE sr.namatempattujuan END AS alamat, " & _
                 " CASE WHEN spd.issiang = 't' THEN 'Siang' ELSE '-' END AS siang,CASE WHEN spd.ispagi = 't' THEN 'Pagi' ELSE '-' END AS pagi, " & _
                 " CASE WHEN spd.ismalam = 't' THEN 'Malam' ELSE '-' END AS malam,CASE WHEN spd.issore = 't' THEN 'Sore' ELSE '-' END AS sore, " & _
                 " spd.qtyproduk AS jumlah,ss.satuanstandar,CASE WHEN spd.tglkadaluarsa IS NULL THEN '' ELSE to_char(spd.tglkadaluarsa,'DD-MM-YYYY HH:mm') END AS tglkadaluarsa, " & _
                 " CASE WHEN spd.satuanresepidfk IS NULL THEN ss.satuanstandar ELSE sn.satuanresep END AS satuanresep,'' AS keteranganpakai, " & _
                 " '(' || EXTRACT (YEAR FROM AGE(to_date(to_char(sr.tglstruk,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(sr.tglfaktur, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || 'Thn '" & _
                 " || EXTRACT (MONTH FROM AGE(to_date(to_char(sr.tglstruk,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(sr.tglfaktur, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || ' Bln '" & _
                 " || EXTRACT (DAY FROM AGE(to_date(to_char(sr.tglstruk,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(sr.tglfaktur, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || ' Hr)' AS umur " & _
                 " FROM strukpelayanantr AS sr " & _
                 " INNER JOIN strukpelayanandetailtr AS spd ON sr.norec = spd.nostrukidfk " & _
                 " INNER JOIN satuanstandarmt AS ss ON ss.id = spd.satuanstandaridfk " & _
                 " INNER JOIN pelayananmt AS pr ON pr.id = spd.produkidfk " & _
                 " LEFT JOIN jeniskemasanmt AS jkm ON jkm.id = spd.jeniskemasanidfk " & _
                 " LEFT JOIN satuanresepmt AS sn ON sn.id = spd.satuanresepidfk " & _
                 " WHERE spd.jeniskemasanidfk = 2 " & str1
             
        strSQL = strSQL & " UNION ALL " & _
                 " SELECT sr.nostruk_intern AS nocm,sr.namapasien_klien AS namapasien,CASE WHEN sr.tglfaktur IS NULL THEN '' ELSE to_char(sr.tglfaktur, 'DD-MM-YYYY') END AS tgllahir, " & _
                 " sr.nostruk AS noresep,to_char(sr.tglstruk, 'DD-MM-YYYY') AS tglresep,' Racikan' || ' (' || CAST (((CAST (spd.qtydetailresep AS INTEGER) / CAST (spd.dosis AS INTEGER)) * " & _
                 " CASE WHEN pr.kekuatan IS NOT NULL THEN CAST (pr.kekuatan AS INTEGER) ELSE 1 END) AS VARCHAR) || ')' AS namaproduk, " & _
                 " spd.aturanpakai,spd.resepke AS rke,CASE WHEN sr.namatempattujuan IS NULL THEN '-' ELSE sr.namatempattujuan END AS alamat, " & _
                 " CASE WHEN spd.issiang = 't' THEN 'Siang' ELSE '-' END AS siang,CASE WHEN spd.ispagi = 't' THEN 'Pagi' ELSE '-' END AS pagi, " & _
                 " CASE WHEN spd.ismalam = 't' THEN 'Malam' ELSE '-' END AS malam,CASE WHEN spd.issore = 't' THEN 'Sore' ELSE '-' END AS sore, " & _
                 " ((CAST (spd.qtydetailresep AS INTEGER) / CAST (spd.dosis AS INTEGER)) * " & _
                 " CASE WHEN pr.kekuatan IS NOT NULL THEN CAST (pr.kekuatan AS INTEGER) ELSE 1 END) AS jumlah,CASE WHEN jr.jenisracikan IS NULL THEN '' ELSE jr.jenisracikan END AS satuanstandar,CASE WHEN spd.tglkadaluarsa IS NULL THEN '' ELSE to_char(spd.tglkadaluarsa,'DD-MM-YYYY HH:mm') END AS tglkadaluarsa, " & _
                 " CASE WHEN spd.satuanresepidfk IS NULL THEN ss.satuanstandar ELSE sn.satuanresep END AS satuanresep,'' AS keteranganpakai, " & _
                 " '(' || EXTRACT (YEAR FROM AGE(to_date(to_char(sr.tglstruk,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(sr.tglfaktur, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || 'Thn ' " & _
                 " || EXTRACT (MONTH FROM AGE(to_date(to_char(sr.tglstruk,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(sr.tglfaktur, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || ' Bln ' " & _
                 " || EXTRACT (DAY FROM AGE(to_date(to_char(sr.tglstruk,'YYYY-MM-DD'),'YYYY-MM-DD'),to_date(to_char(sr.tglfaktur, 'YYYY-MM-DD'),'YYYY-MM-DD'))) || ' Hr)' AS umur " & _
                 " FROM strukpelayanantr AS sr " & _
                 " INNER JOIN strukpelayanandetailtr AS spd ON sr.norec = spd.nostrukidfk " & _
                 " INNER JOIN satuanstandarmt AS ss ON ss.id = spd.satuanstandaridfk " & _
                 " INNER JOIN pelayananmt AS pr ON pr.id = spd.produkidfk " & _
                 " LEFT JOIN jeniskemasanmt AS jkm ON jkm.id = spd.jeniskemasanidfk " & _
                 " LEFT JOIN satuanresepmt AS sn ON sn.id = spd.satuanresepidfk " & _
                 " LEFT JOIN jenisracikanmt as jr on jr.id = spd.jeniskemasanidfk " & _
                 " WHERE spd.jeniskemasanidfk = 1 " & str1
        
    End If
     
    ReadRs3 strSQL
    adocmd.CommandText = strSQL
    adocmd.CommandType = adCmdText
    Dim aturan() As String
    With crCetakLabelFarmasi
        .database.AddADOCommand CN_String, adocmd
        If RS3.EOF = False Then
            aturan = Split(LCase(RS3!aturanpakai), "x")
            .txtNamaRs.SetText strNamaLengkapRs
            .txtAlamatRs.SetText strAlamatRS
            .udtTglResep.SetUnboundFieldSource ("{ado.tglresep}")
            .usNoResep.SetUnboundFieldSource ("{ado.noresep}")
            .usNamaPasien.SetUnboundFieldSource ("{ado.namapasien}")
            .udtTglLahir.SetUnboundFieldSource ("{ado.tgllahir}")
            .usNamaProduk.SetUnboundFieldSource ("{ado.namaproduk}")
            .usCaraPakai.SetUnboundFieldSource ("{ado.aturanpakai}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usAlamat.SetUnboundFieldSource ("{ado.alamat}")
            .usaturan.SetUnboundFieldSource ("{ado.aturanpakai}")
            .usss.SetUnboundFieldSource ("{ado.satuanresep}")
            .usWaktuMinumS.SetUnboundFieldSource ("{ado.siang}")
            .usWaktuMinumM.SetUnboundFieldSource ("{ado.malam}")
            .usWaktuMinumP.SetUnboundFieldSource ("{ado.pagi}")
            .usWaktuMinumSr.SetUnboundFieldSource ("{ado.sore}")
            .usKeteranganPakai.SetUnboundFieldSource ("{ado.keteranganpakai}")
            .usUmur.SetUnboundFieldSource ("{ado.umur}")
            .txtTglExp.SetText IIf(IsNull(RS3("tglkadaluarsa")), "-", RS3("tglkadaluarsa"))
            .usTglExp.SetUnboundFieldSource ("{ado.tglkadaluarsa}")
        End If
        If view = "false" Then
            strPrinter1 = GetTxt("Setting.ini", "Printer", "LabelFarmasi")
            .SelectPrinter "winspool", strPrinter1, "Ne00:"
            .PrintOut False
            Unload Me
            Screen.MousePointer = vbDefault
         Else
            With CRViewer1
                .ReportSource = crCetakLabelFarmasi
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

Public Sub cetakResep(strNores As String, strUser As String, strLayanan As String, view As String)
On Error GoTo errLoad
On Error Resume Next
Set frmFarmasi = Nothing
bolNomorAntrian = False
bolLabelFarmasi = True
bolRincianResep = False
bolRincianResepRajal = False
bolRincianResepRanap = False
Dim strSQL As String
    
        With crCetakResep
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String
            
            If strLayanan <> "LAYANAN" Then
            
                 strSQL = " select sp.nostruk AS noresep,'-' AS noregistrasi,sp.nostruk_intern AS nocm,tglfaktur AS tglregistrasi, sp.tglstruk AS tgl, " & _
                          " sp.namapasien_klien AS namapasienjk,pg.namalengkap,'-' AS alergi,sp.noteleponfaks,sp.namatempattujuan AS alamat, " & _
                          " ru.namaruangan,ru.namaruangan AS ruanganpasien,'-' AS kamar,'-' AS bed, sp.namarekanan AS penjamin,'' AS Umur,sp.tglfaktur AS tgllahir, " & _
                          " ((spd.hargasatuan-spd.hargadiscount)*spd.qtyproduk)+spd.hargatambahan AS totalharga,((spd.hargasatuan-spd.hargadiscount)*spd.qtyproduk)+spd.hargatambahan AS totalbiaya, " & _
                          " pr.id AS kdproduk, pr.namaproduk AS namaprodukstandar, spd.qtyproduk AS qtyhrg,spd.qtyproduk AS jumlah, " & _
                          " CASE when spd.hargadiscount isnull then 0 ELSE  spd.hargadiscount * spd.qtyproduk end AS totaldiscound, " & _
                          " spd.resepke AS rke,jkm.jeniskemasan,spd.qtyproduk AS qtydetailresep,spd.qtydetailresep " & _
                          " FROM strukpelayanantr AS sp " & _
                          " INNER JOIN strukpelayanandetailtr AS spd on spd.nostrukidfk = sp.norec " & _
                          " LEFT JOIN pegawaimt AS pg on pg.id = sp.pegawaipenanggungjawabidfk " & _
                          " LEFT JOIN ruanganmt AS ru on ru.id = sp.ruanganidfk " & _
                          " LEFT JOIN pelayananmt AS pr on pr.id = spd.produkidfk " & _
                          " LEFT JOIN jeniskemasanmt AS jkm on jkm.id = spd.jeniskemasanidfk " & _
                          " WHERE sp.norec = '" & strNores & "'"
                          
            Else
            
                strSQL = " SELECT pd.noregistrasi,ps.norm AS nocm,'=' AS umur,ps.namapasien || ' ( ' || CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END || ' )' AS namapasienjk, " & _
                         " kpp.kelompokpasien || ' ( ' || rek.namarekanan || ' ) ' AS penjamin,ps.nohp AS noteleponfaks,al.alamatlengkap AS alamat,ps.tgllahir,pd.tglregistrasi,ru.namaruangan AS ruanganpasien, " & _
                         " '-' AS alergi,sr.noresep,ru2.namaruangan,pp.tglpelayanan AS tgl,pp.rke,pr.id AS kdproduk,pr.namaproduk || ' / ' || sstd.satuanstandar AS namaprodukstandar,pp.jumlah, " & _
                         " CASE WHEN pp.jasa IS NULL THEN 0 ELSE pp.jasa END AS jasa,pp.hargasatuan,pp.jumlah AS qtyhrg,(pp.jumlah * (pp.hargasatuan-(case when pp.hargadiscount is null then 0 else pp.hargadiscount end )) )+case when pp.jasa is null then 0 else pp.jasa end as totalharga, " & _
                         " jnskem.jeniskemasan,pgw.namalengkap,CASE WHEN pp.hargadiscount IS NULL THEN 0 ELSE pp.hargadiscount * pp.jumlah END AS totaldiscound,((pp.jumlah * pp.hargasatuan ) - (CASE when pp.hargadiscount isnull then 0 ELSE  pp.hargadiscount * pp.jumlah end)) + case when  " & _
                         " pp.jasa is null then 0 else pp.jasa end as totalbiaya,pp.qtydetailresep " & _
                         " FROM transaksipasientr AS pp " & _
                         " INNER JOIN daftarpasienruangantr AS apdp ON pp.daftarpasienruanganfk = apdp.norec " & _
                         " INNER JOIN registrasipasientr AS pd ON apdp.registrasipasienfk = pd.norec " & _
                         " INNER JOIN pasienmt AS ps ON pd.normidfk = ps.id " & _
                         " LEFT JOIN alamatmt as al on al.normidfk = ps.id " & _
                         " INNER JOIN pelayananmt AS pr ON pp.produkidfk = pr.id " & _
                         " LEFT JOIN ruanganmt AS ru ON apdp.ruanganidfk = ru.id " & _
                         " INNER JOIN transaksireseptr AS sr ON pp.strukresepidfk = sr.norec " & _
                         " LEFT JOIN ruanganmt AS ru2 ON sr.ruanganidfk = ru2.id " & _
                         " LEFT JOIN jeniskemasanmt AS jnskem ON pp.jeniskemasanidfk = jnskem.id " & _
                         " LEFT JOIN pegawaimt AS pgw ON sr.penulisresepidfk = pgw.id " & _
                         " LEFT JOIN satuanstandarmt AS sstd ON pp.satuanviewidfk = sstd.id " & _
                         " LEFT JOIN jeniskelaminmt AS jk ON ps.jeniskelaminidfk = jk.id " & _
                         " LEFT JOIN kelompokpasienmt AS kpp ON pd.kelompokpasienlastidfk = kpp.id " & _
                         " LEFT JOIN rekananmt as rek on rek.id = pd.rekananidfk " & _
                         " WHERE sr.norec = '" & strNores & "'"
            End If
            
            ReadRs strSQL
            
             adoReport.CommandText = strSQL
             adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport
            .txtNamaRs.SetText UCase$(strNamaLengkapRs)
            .txtAlamatRs.SetText stralmtLengkapRs
             If Not RS.EOF Then
              ReadRs2 " SELECT CAST(km.namakamar AS VARCHAR) || ' / No. ' || CAST(tt.nomorbed AS VARCHAR) AS kamar " & _
                      " FROM registrasipasientr AS pd " & _
                      " INNER JOIN daftarpasienruangantr AS apdp ON apdp.registrasipasienfk = pd.norec " & _
                      " INNER JOIN kamarmt AS km ON km.id = apdp.kamaridfk " & _
                      " INNER JOIN tempattidurmt AS tt ON tt.kamaridfk = km.id " & _
                      " WHERE pd.noregistrasi= '" & (RS("noregistrasi")) & "' LIMIT 1"
    
              .txtnopendaftaran.SetText IIf(IsNull(RS("noregistrasi")), "-", RS("noregistrasi"))
              .txtnocm.SetText IIf(IsNull(RS("nocm")), "-", RS("nocm"))
              .txtnmpasien.SetText IIf(IsNull(RS("namapasienjk")), "-", RS("namapasienjk"))
              .txtNamaRuangan.SetText IIf(IsNull(RS("ruanganpasien")), "-", RS("ruanganpasien"))
              .txtNamaRuanganFarmasi.SetText IIf(IsNull(RS("namaruangan")), "-", RS("namaruangan"))
                 If IsNull(RS("penjamin")) = True Then
                     .txtPenjamin.SetText "-"
                 Else
                     .txtPenjamin.SetText RS("penjamin")
                 End If
                  If RS("umur") = "-" Then
                     .txtUmur.SetText "-"
                  Else
                     .txtUmur.SetText hitungUmur(Format(RS("tgllahir"), "dd/mm/yyyy"), Format(RS("tglregistrasi"), "dd/mm/yyyy"))
                  End If
                  .txtNamaDokter.SetText IIf(IsNull(RS("namalengkap")), "-", RS("namalengkap"))
                  .txtuser.SetText strUser
                 If Left(RS("noresep"), 2) = "OB" Then
                     .txtTglLahir.SetText IIf(IsNull(Format(RS("tgllahir"), "dd/mm/yyyy")), "-", Format(RS("tgllahir"), "dd/mm/yyyy"))
                     .txtTelp2.SetText IIf(IsNull(RS("noteleponfaks")), "-", RS("noteleponfaks"))
                     .txtAl2.SetText IIf(IsNull(RS("alamat")), "-", RS("alamat"))
                     .txtTgl.SetText IIf(IsNull(Format(RS("tgl"), "dd/mm/yyyy HH:mm:ss")), "-", Format(RS("tgl"), "dd/mm/yyyy HH:mm:ss"))
                 Else
                     .txtTglLahir.SetText IIf(IsNull(RS("tgllahir")), "-", RS("tgllahir"))
                     .txtTelp2.SetText IIf(IsNull(RS("noteleponfaks")), "-", RS("noteleponfaks"))
                     .txtAl2.SetText IIf(IsNull(RS("alamat")), "-", RS("alamat"))
                      If Not RS.EOF Then
                          If RS2.RecordCount > 0 Then
                             .txtKamar.SetText IIf(IsNull(RS2("kamar")), "-", RS2("kamar"))
                         End If
                      End If
                     .txtTgl.SetText IIf(IsNull(Format(RS("tgl"), "dd/mm/yyyy HH:mm:ss")), "-", Format(RS("tgl"), "dd/mm/yyyy HH:mm:ss")) 'RS!tgl
                     .txtAlergi.SetText IIf(IsNull(RS("alergi")), "-", RS("alergi"))
     
                 End If
            End If
            
             .usNoResep.SetUnboundFieldSource ("{Ado.noresep}")
             .ucbiayasatuan.SetUnboundFieldSource ("{Ado.totalharga}")
             .uskdproduk.SetUnboundFieldSource ("{Ado.kdproduk}")
             .ustindakan.SetUnboundFieldSource ("{Ado.namaprodukstandar}")
             .usQtyHrg.SetUnboundFieldSource ("{Ado.qtyhrg}")
             .unQtyTotal.SetUnboundFieldSource ("{Ado.jumlah}")
             .ucGrandTotal.SetUnboundFieldSource ("{Ado.totalharga}")
             .undis.SetUnboundFieldSource ("{Ado.totaldiscound}")
             .unTotal.SetUnboundFieldSource ("{Ado.totalbiaya}")
             .unQtyDetail.SetUnboundFieldSource ("{ado.qtydetailresep}")
             .unRacikanKe.SetUnboundFieldSource ("{ado.rke}")
             .usJenisObat.SetUnboundFieldSource ("{ado.jeniskemasan}")
             
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "ResepFarmasi")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakResep
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

Public Sub cetakRincianResepRajal(strNores As String, strUser As String, view As String)
On Error GoTo errLoad
On Error Resume Next
Set frmFarmasi = Nothing
bolNomorAntrian = False
bolLabelFarmasi = False
bolRincianResep = False
bolRincianResepRajal = True
bolRincianResepRanap = False
Dim umur As String
Dim strSQL As String
    
        With crCetakRincianResepRajal
        
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String
                                                
            
                strSQL = " SELECT pd.noregistrasi,ps.norm AS nocm,'=' AS umur,ps.namapasien || ' ( ' || CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END || ' )' AS namapasienjk, " & _
                         " kpp.kelompokpasien || ' ( ' || rek.namarekanan || ' ) ' AS penjamin,ps.nohp AS noteleponfaks,al.alamatlengkap AS alamat,ps.tgllahir,pd.tglregistrasi,ru.namaruangan AS ruanganpasien, " & _
                         " '-' AS alergi,sr.noresep,ru2.namaruangan,pp.tglpelayanan AS tgl,pp.rke,pr.id AS kdproduk,pr.namaproduk || ' / ' || sstd.satuanstandar AS namaprodukstandar,pp.jumlah, " & _
                         " CASE WHEN pp.jasa IS NULL THEN 0 ELSE pp.jasa END AS jasa,pp.hargasatuan,pp.jumlah AS qtyhrg,(pp.jumlah * (pp.hargasatuan-(case when pp.hargadiscount is null then 0 else pp.hargadiscount end )) )+case when pp.jasa is null then 0 else pp.jasa end as totalharga, " & _
                         " jnskem.jeniskemasan,pgw.namalengkap,CASE WHEN pp.hargadiscount IS NULL THEN 0 ELSE pp.hargadiscount * pp.jumlah END AS totaldiscound,((pp.jumlah * pp.hargasatuan ) - (CASE when pp.hargadiscount isnull then 0 ELSE  pp.hargadiscount * pp.jumlah end)) + case when  " & _
                         " pp.jasa is null then 0 else pp.jasa end as totalbiaya,pp.qtydetailresep,sr.tglresep,pp.norec " & _
                         " FROM transaksipasientr AS pp " & _
                         " INNER JOIN daftarpasienruangantr AS apdp ON pp.daftarpasienruanganfk = apdp.norec " & _
                         " INNER JOIN registrasipasientr AS pd ON apdp.registrasipasienfk = pd.norec " & _
                         " INNER JOIN pasienmt AS ps ON pd.normidfk = ps.id " & _
                         " LEFT JOIN alamatmt as al on al.normidfk = ps.id " & _
                         " INNER JOIN pelayananmt AS pr ON pp.produkidfk = pr.id " & _
                         " LEFT JOIN ruanganmt AS ru ON apdp.ruanganidfk = ru.id " & _
                         " INNER JOIN transaksireseptr AS sr ON pp.strukresepidfk = sr.norec " & _
                         " LEFT JOIN ruanganmt AS ru2 ON sr.ruanganidfk = ru2.id " & _
                         " LEFT JOIN jeniskemasanmt AS jnskem ON pp.jeniskemasanidfk = jnskem.id " & _
                         " LEFT JOIN pegawaimt AS pgw ON sr.penulisresepidfk = pgw.id " & _
                         " LEFT JOIN satuanstandarmt AS sstd ON pp.satuanviewidfk = sstd.id " & _
                         " LEFT JOIN jeniskelaminmt AS jk ON ps.jeniskelaminidfk = jk.id " & _
                         " LEFT JOIN kelompokpasienmt AS kpp ON pd.kelompokpasienlastidfk = kpp.id " & _
                         " LEFT JOIN rekananmt as rek on rek.id = pd.rekananidfk " & _
                         " WHERE sr.norec = '" & strNores & "'"
            
            ReadRs strSQL
            
             adoReport.CommandText = strSQL
             adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport
            .txtNamaPemerintahan.SetText UCase$(strNamaPemerintah)
            .txtNamaRs.SetText UCase$(strNamaLengkapRs)
            .txtAlamatRs.SetText stralmtLengkapRs
             If Not RS.EOF Then
                If IsNull(RS("tgllahir")) = True Then
                     umur = ""
                Else
                     umur = " (" & hitungUmur(Format(RS("tgllahir"), "dd/mm/yyyy"), Format(RS("tglregistrasi"), "dd/mm/yyyy")) & " )"
                End If
                 .txtNamaPasien.SetText IIf(IsNull(RS("namapasienjk")), "-", RS("namapasienjk"))
                 .txtTglLahir.SetText IIf(IsNull(RS("tgllahir")), "-", Format(RS("tgllahir"), "dd-mm-yyyy")) & umur
                 .txtAlamatPasien.SetText IIf(IsNull(RS("alamat")), "-", RS("alamat"))
                 .txtRuanganPoli.SetText IIf(IsNull(RS("ruanganpasien")), "-", RS("ruanganpasien"))
                 .txtTglResep.SetText IIf(IsNull(RS("tglresep")), "-", Format(RS("tglresep"), "dd-mm-yyyy"))
            End If
              .usRke.SetUnboundFieldSource ("{Ado.rke}")
              .usNorecPP.SetUnboundFieldSource ("{Ado.norec}")
              .usNamaObat.SetUnboundFieldSource ("{Ado.namaprodukstandar}")
              .ucbiayasatuan.SetUnboundFieldSource ("{Ado.hargasatuan}")
              .unqty.SetUnboundFieldSource ("{Ado.qtyhrg}")
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "ResepFarmasi")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakRincianResepRajal
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

Public Sub cetakRincianResepRanap(strNores As String, strUser As String, view As String)
On Error GoTo errLoad
On Error Resume Next
Set frmFarmasi = Nothing
bolNomorAntrian = False
bolLabelFarmasi = False
bolRincianResep = False
bolRincianResepRajal = False
bolRincianResepRanap = True
Dim umur As String
Dim strSQL As String
    
        With crCetakRincianResepRanap
        
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String
                                                
            
                strSQL = " SELECT pd.noregistrasi,ps.norm AS nocm,'=' AS umur,ps.namapasien || ' ( ' || CASE WHEN jk.id = 1 THEN 'L' WHEN jk.id = 2 THEN 'P' ELSE '-' END || ' )' AS namapasienjk, " & _
                         " kpp.kelompokpasien || ' ( ' || rek.namarekanan || ' ) ' AS penjamin,ps.nohp AS noteleponfaks,al.alamatlengkap AS alamat,ps.tgllahir,pd.tglregistrasi,ru.namaruangan AS ruanganpasien, " & _
                         " '-' AS alergi,sr.noresep,ru2.namaruangan,pp.tglpelayanan AS tgl,pp.rke,pr.id AS kdproduk,pr.namaproduk || ' / ' || sstd.satuanstandar AS namaprodukstandar,pp.jumlah, " & _
                         " CASE WHEN pp.jasa IS NULL THEN 0 ELSE pp.jasa END AS jasa,pp.hargasatuan,pp.jumlah AS qtyhrg,(pp.jumlah * (pp.hargasatuan-(case when pp.hargadiscount is null then 0 else pp.hargadiscount end )) )+case when pp.jasa is null then 0 else pp.jasa end as totalharga, " & _
                         " jnskem.jeniskemasan,pgw.namalengkap,CASE WHEN pp.hargadiscount IS NULL THEN 0 ELSE pp.hargadiscount * pp.jumlah END AS totaldiscound,((pp.jumlah * pp.hargasatuan ) - (CASE when pp.hargadiscount isnull then 0 ELSE  pp.hargadiscount * pp.jumlah end)) + case when  " & _
                         " pp.jasa is null then 0 else pp.jasa end as totalbiaya,pp.qtydetailresep,sr.tglresep,pp.norec,to_char(pp.tglpelayanan, 'DD/MM/YYYY') AS tgllayanan " & _
                         " FROM transaksipasientr AS pp " & _
                         " INNER JOIN daftarpasienruangantr AS apdp ON pp.daftarpasienruanganfk = apdp.norec " & _
                         " INNER JOIN registrasipasientr AS pd ON apdp.registrasipasienfk = pd.norec " & _
                         " INNER JOIN pasienmt AS ps ON pd.normidfk = ps.id " & _
                         " LEFT JOIN alamatmt as al on al.normidfk = ps.id " & _
                         " INNER JOIN pelayananmt AS pr ON pp.produkidfk = pr.id " & _
                         " LEFT JOIN ruanganmt AS ru ON apdp.ruanganidfk = ru.id " & _
                         " INNER JOIN transaksireseptr AS sr ON pp.strukresepidfk = sr.norec " & _
                         " LEFT JOIN ruanganmt AS ru2 ON sr.ruanganidfk = ru2.id " & _
                         " LEFT JOIN jeniskemasanmt AS jnskem ON pp.jeniskemasanidfk = jnskem.id " & _
                         " LEFT JOIN pegawaimt AS pgw ON sr.penulisresepidfk = pgw.id " & _
                         " LEFT JOIN satuanstandarmt AS sstd ON pp.satuanviewidfk = sstd.id " & _
                         " LEFT JOIN jeniskelaminmt AS jk ON ps.jeniskelaminidfk = jk.id " & _
                         " LEFT JOIN kelompokpasienmt AS kpp ON pd.kelompokpasienlastidfk = kpp.id " & _
                         " LEFT JOIN rekananmt as rek on rek.id = pd.rekananidfk " & _
                         " WHERE sr.norec = '" & strNores & "'"
            
            ReadRs strSQL
            
             adoReport.CommandText = strSQL
             adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport
            .txtNamaPemerintahan.SetText UCase$(strNamaPemerintah)
            .txtNamaRs.SetText UCase$(strNamaLengkapRs)
            .txtAlamatRs.SetText stralmtLengkapRs
             If Not RS.EOF Then
                If IsNull(RS("tgllahir")) = True Then
                     umur = ""
                Else
                     umur = " (" & hitungUmur(Format(RS("tgllahir"), "dd/mm/yyyy"), Format(RS("tglregistrasi"), "dd/mm/yyyy")) & " )"
                End If
                 .txtNamaPasien.SetText IIf(IsNull(RS("namapasienjk")), "-", RS("namapasienjk"))
                 .txtTglLahir.SetText IIf(IsNull(RS("tgllahir")), "-", Format(RS("tgllahir"), "dd-mm-yyyy")) & umur
                 .txtAlamatPasien.SetText IIf(IsNull(RS("alamat")), "-", RS("alamat"))
                 .txtRuanganPoli.SetText IIf(IsNull(RS("ruanganpasien")), "-", RS("ruanganpasien"))
'                 .txtTglResep.SetText IIf(IsNull(RS("tglresep")), "-", Format(RS("tglresep"), "dd-mm-yyyy"))
            End If
              .usRke.SetUnboundFieldSource ("{Ado.rke}")
              .usNorecPP.SetUnboundFieldSource ("{Ado.norec}")
              .usNamaObat.SetUnboundFieldSource ("{Ado.namaprodukstandar}")
              .ucbiayasatuan.SetUnboundFieldSource ("{Ado.hargasatuan}")
              .unqty.SetUnboundFieldSource ("{Ado.qtyhrg}")
              .usTglPelayanan.SetUnboundFieldSource ("{Ado.tgllayanan}")
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "ResepFarmasi")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakRincianResepRanap
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
