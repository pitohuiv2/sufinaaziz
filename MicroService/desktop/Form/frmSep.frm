VERSION 5.00
Object = "{C4847593-972C-11D0-9567-00A0C9273C2A}#8.0#0"; "crviewer.dll"
Begin VB.Form frmSep 
   Caption         =   "Cetak"
   ClientHeight    =   7005
   ClientLeft      =   60
   ClientTop       =   345
   ClientWidth     =   5820
   Icon            =   "frmSep.frx":0000
   LinkTopic       =   "Form1"
   ScaleHeight     =   7005
   ScaleWidth      =   5820
   WindowState     =   2  'Maximized
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
      TabIndex        =   4
      Top             =   480
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
      TabIndex        =   3
      Top             =   480
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
      TabIndex        =   2
      Top             =   480
      Width           =   3015
   End
   Begin CRVIEWERLibCtl.CRViewer CRViewer1 
      Height          =   7000
      Left            =   0
      TabIndex        =   0
      Top             =   0
      Width           =   5800
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
   Begin VB.TextBox txtNamaFormPengirim 
      Height          =   495
      Left            =   3120
      TabIndex        =   1
      Top             =   600
      Width           =   2175
   End
End
Attribute VB_Name = "frmSep"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Dim Report As New crSEP
Dim bolSuppresDetailSection10 As Boolean
Dim ii As Integer
Dim tempPrint1 As String
Dim p As Printer
Dim p2 As Printer
Dim strDeviceName As String
Dim strDriverName As String
Dim strPort As String
Dim adoReport As New ADODB.Command

Private Sub cmdCetak_Click()
    Report.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
    Report.PrintOut False
End Sub

Private Sub CmdOption_Click()
    Report.PrinterSetup Me.hwnd
    CRViewer1.Refresh
End Sub

Private Sub Form_Load()
    Dim p As Printer
    cboPrinter.Clear
    For Each p In Printers
        cboPrinter.AddItem p.DeviceName
    Next
    cboPrinter.Text = GetTxt("Setting.ini", "Printer", "Kwitansi")
End Sub

Private Sub Form_Resize()
    CRViewer1.Top = 0
    CRViewer1.Left = 0
    CRViewer1.Height = ScaleHeight
    CRViewer1.Width = ScaleWidth
End Sub

Private Sub Form_Unload(Cancel As Integer)

    Set frmKwitansi = Nothing
End Sub

Public Sub cetak(nostruk As String, qtyCetak As Integer, idPegawai As String, Ket As String, view As String)

    Set adoReport = New ADODB.Command
    adoReport.ActiveConnection = CN_String
            
    strSQL = " SELECT pd.noregistrasi,pi.norm,pi.tgllahir,jk.jeniskelamin,rp.namaruangan,rp.kodeexternal AS namapoliBpjs, " & _
             " pa.ppkrujukan,pa.norujukan,ap.namapeserta,(CASE WHEN rp.instalasiidfk = 16 THEN 'Rawat Inap' ELSE 'Rawat Jalan'  END) AS jenisrawat,dg.kddiagnosa, " & _
             " (CASE WHEN dg.namadiagnosa IS NULL THEN '-' ELSE dg.namadiagnosa END) AS namadiagnosa, " & _
             " ap.jenispeserta , ap.kdprovider, ap.nmprovider, kls.namakelas, pa.catatan,pa.nosep,pa.tanggalsep,pa.nokepesertaan " & _
             " FROM registrasipasientr AS pd " & _
             " INNER JOIN pasienmt AS pi ON pi.id = pd.normidfk " & _
             " LEFT JOIN jeniskelaminmt AS jk ON jk.id = pi.jeniskelaminidfk " & _
             " LEFT JOIN ruanganmt AS rp ON rp.id = pd.ruanganlastidfk " & _
             " LEFT JOIN pemakaianasuransitr AS pa ON pa.registrasipasienfk = pd.norec " & _
             " LEFT JOIN asuransimt ap ON pa.asuransiidfk = ap.id " & _
             " LEFT JOIN icdxmt dg ON pa.icdxidfk = dg.id " & _
             " LEFT JOIN kelasmt kls ON kls.id = ap.kelasdijaminidfk " & _
             " WHERE pd.noregistrasi = '" & nostruk & "' "
            
    ReadRs strSQL
            
    adoReport.CommandText = strSQL
    adoReport.CommandType = adCmdUnknown
    With Report
            
        .database.AddADOCommand CN_String, adoReport
        
        If Not RS.EOF Then
            .txtNamaRs.SetText strNamaLengkapRs
            .txtnosjp.SetText IIf(IsNull(RS("nosep")), "-", RS("nosep")) 'RS("nosep")
            .txtTglSep.SetText Format(RS("tanggalsep"), "dd/MM/yyyy")
            .txtNomorKartuAskes.SetText IIf(IsNull(RS("nokepesertaan")), "-", RS("nokepesertaan"))
            .txtNamaPasien.SetText IIf(IsNull(RS("namapeserta")), "-", RS("namapeserta")) 'RS("namapeserta")
            .txtkelamin.SetText IIf(IsNull(RS("jeniskelamin")), "-", RS("jeniskelamin")) 'RS("jeniskelamin")
            .txtTanggalLahir.SetText IIf(IsNull(RS("tgllahir")), "-", Format(RS("tgllahir"), "dd/MM/yyyy")) 'Format(RS("tgllahir"), "dd/mm/yyyy")
            .txtTujuan.SetText RS("namapoliBpjs") & " / " & RS("namaruangan")
            .txtAsalRujukan.SetText IIf(IsNull(RS("nmprovider")), "-", RS("nmprovider"))
            .txtPeserta.SetText IIf(IsNull(RS("jenispeserta")), "-", RS("jenispeserta"))
            .txtJenisrawat.SetText IIf(IsNull(RS("jenisrawat")), "-", RS("jenisrawat")) 'RS("jenisrawat")
            .txtnocm2.SetText IIf(IsNull(RS("norm")), "-", RS("norm")) 'RS("nocm")
            .txtdiagnosa.SetText IIf(IsNull(RS("namadiagnosa")), "-", RS("namadiagnosa")) 'RS("namadiagnosa")
            .txtKelasrawat.SetText IIf(IsNull(RS("namakelas")), "-", RS("namakelas")) 'RS("namakelas")
            .txtCatatan.SetText IIf(IsNull(RS("catatan")), "-", RS("catatan"))
            .txtnocm2.SetText IIf(IsNull(RS("norm")), "-", RS("norm"))
            .txtNoPendaftaran2.SetText IIf(IsNull(RS("noregistrasi")), "-", RS("noregistrasi"))
        End If
                
        If view = "false" Then
            Dim strPrinter As String
            strPrinter = GetTxt("Setting.ini", "Printer", "SEP")
            Report.SelectPrinter "winspool", strPrinter, "Ne00:"
            Report.PrintOut False
            Unload Me
        Else
            With CRViewer1
                .ReportSource = Report
                .ViewReport
                .Zoom 1
            End With
            Me.Show
        End If
    End With
Exit Sub
errLoad:
End Sub

