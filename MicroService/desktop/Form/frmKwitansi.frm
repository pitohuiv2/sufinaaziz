VERSION 5.00
Object = "{C4847593-972C-11D0-9567-00A0C9273C2A}#8.0#0"; "crviewer.dll"
Begin VB.Form frmKwitansi 
   Caption         =   "Kwitansi"
   ClientHeight    =   7005
   ClientLeft      =   60
   ClientTop       =   345
   ClientWidth     =   5820
   Icon            =   "frmKwitansi.frx":0000
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
Attribute VB_Name = "frmKwitansi"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Dim Report As New crKwitansi
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

Public Sub CetakUlangJenisKuitansi(nostruk As String, namaPegawai As String, view As String)

Dim strKet As Boolean
Dim NamaKasir As String

    strKet = True
    
    Set frmKwitansi = Nothing
    Set Report = New crKwitansi
    ReadRs " SELECT CASE WHEN rp.noregistrasi IS NULL THEN '-' ELSE rp.noregistrasi END AS noregistrasi, " & _
           " CASE WHEN ps.namapasien IS NULL THEN sp.namapasien_klien ELSE ps.namapasien END AS namapasien, " & _
           " sbp.totaldibayar,sbp.keteranganlainnya,CASE WHEN ps.namapasien IS NULL THEN sp.namapasien_klien ELSE ps.namapasien END || '/' || " & _
           " CASE WHEN ps.norm IS NULL THEN sp.nostruk_intern ELSE ps.norm END || '/' || " & _
           " CASE WHEN rp.noregistrasi IS NULL THEN '-' ELSE rp.noregistrasi END || '-' || " & _
           " CASE WHEN ru.namaruangan IS NULL THEN '' ELSE ru.namaruangan END AS pasien, " & _
           " CASE WHEN ru.namaruangan IS NULL THEN '' ELSE ru.namaruangan END AS namaruangan, " & _
           " pg.namalengkap,CASE WHEN ps.norm IS NULL THEN sp.nostruk_intern ELSE ps.norm END norm, " & _
           " CASE WHEN rp.tglregistrasi IS NULL THEN sp.tglstruk ELSE rp.tglregistrasi END AS tglregistrasi, " & _
           " CASE WHEN rp.tglpulang IS NULL THEN NULL ELSE rp.tglpulang END AS tglpulang, " & _
           " to_char(sbp.tglsbm, 'DD-MM-YYYY') AS tanggal,sbp.nosbm " & _
           " FROM strukbuktipenerimaantr AS sbp " & _
           " INNER JOIN strukpelayanantr AS sp ON sp.norec = sbp.nostrukidfk " & _
           " LEFT JOIN registrasipasientr AS rp ON rp.norec = sp.registrasipasienfk " & _
           " LEFT JOIN pasienmt AS ps ON ps.id = rp.normidfk " & _
           " LEFT JOIN ruanganmt AS ru ON ru.id = rp.ruanganlastidfk " & _
           " INNER JOIN pegawaimt AS pg ON pg.id = sbp.pegawaipenerimaidfk " & _
           " WHERE sbp.norec ='" & nostruk & "'"
    
    Dim i As Integer
    Dim jumlahDuit As Double
        
    For i = 0 To RS.RecordCount - 1
        jumlahDuit = jumlahDuit + CDbl(RS!totaldibayar)
        RS.MoveNext
        
    Next
    RS.MoveFirst

    With Report
        .txtNamaPemerintahan.SetText UCase$(strNamaPemerintah)
        .txtNamaRs.SetText UCase$(strNamaLengkapRs)
        .txtAlamatRs.SetText stralmtLengkapRs
        .txtNamaKota.SetText strNamaKota & ","
        If Not RS.EOF Then
            
            .txtRegistrasi.SetText RS("noregistrasi")
            .txtIVN.SetText RS("nosbm")
            .txtTerimaDari.SetText UCase(RS("namapasien"))
            .txtKeterangan.SetText UCase(RS("keteranganlainnya")) 'UCase("Pembayaran Biaya Layanan " & RS("namaruangan"))
            .txtRp.SetText "Rp. " & Format(jumlahDuit, "##,##0.00")
            .txtTerbilang.SetText TerbilangDesimal(CStr(jumlahDuit))
            .txtpasien.SetText RS("pasien")
'            .txtRuangan.SetText UCase(RS("namaruangan"))
            .txtNoCM2.SetText RS("norm")
    
            .txtPrintTglBKM.SetText RS("tanggal")
            .txtKasir.SetText RS("namalengkap")
            .txtDesc.SetText UCase("KET  : " & RS("norm") & "/ " & RS("namapasien") & "/ " & RS("noregistrasi"))
            .txtPetugasCetak.SetText RS("namalengkap")
            
            If view = "false" Then
                Dim strPrinter As String
'
                strPrinter = GetTxt("Setting.ini", "Printer", "Struk")
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
        End If
    End With
Exit Sub
errLoad:
End Sub

