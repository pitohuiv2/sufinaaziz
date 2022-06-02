VERSION 5.00
Object = "{C4847593-972C-11D0-9567-00A0C9273C2A}#8.0#0"; "crviewer.dll"
Object = "{248DD890-BB45-11CF-9ABC-0080C7E7B78D}#1.0#0"; "MSWINSCK.OCX"
Begin VB.Form frmLogistik 
   Caption         =   "Logistik"
   ClientHeight    =   7005
   ClientLeft      =   60
   ClientTop       =   405
   ClientWidth     =   5820
   Icon            =   "frmLogistik.frx":0000
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
Attribute VB_Name = "frmLogistik"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
'# VARIABEL CR #'
Dim crCetakBuktiOrderBarang As New crBuktiOrderBarang
Dim crCetakBuktiKirimBarang As New crBuktiKirimBarang
Dim crCetakBuktiPenerimaan As New crBuktiPenerimaanBarang
'# VARIABEL FUNC #'
Dim bolBuktiOrder As Boolean
Dim bolBuktiKirim As Boolean
Dim bolBuktiPenerimaan As Boolean
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
    If bolBuktiOrder = True Then
        crCetakBuktiOrderBarang.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakBuktiOrderBarang.PrintOut False
    ElseIf bolBuktiKirim = True Then
        crCetakBuktiKirimBarang.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakBuktiKirimBarang.PrintOut False
     ElseIf bolBuktiPenerimaan = True Then
        crCetakBuktiPenerimaan.SelectPrinter "winspool", cboPrinter.Text, "Ne00:"
        PrinterNama = cboPrinter.Text
        crCetakBuktiPenerimaan.PrintOut False
    End If
    SaveSetting "SMART", "SettingPrinter", "cboPrinter", PrinterNama
End Sub

Private Sub CmdOption_Click()
    If bolBuktiOrder = True Then
        crCetakBuktiOrderBarang.PrinterSetup Me.hwnd
    ElseIf bolBuktiKirim = True Then
        crCetakBuktiKirimBarang.PrinterSetup Me.hwnd
    ElseIf bolBuktiPenerimaan = True Then
        crCetakBuktiPenerimaan.PrinterSetup Me.hwnd
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
    Set frmLogistik = Nothing
End Sub

Public Sub cetakBuktiOrder(strNoKirim As String, pegawaiMengetahui As String, pegawaiMeminta As String, jabatanMengetahui As String, jabatanMeminta As String, view As String, strUser As String)
On Error GoTo errLoad
On Error Resume Next
Set frmLogistik = Nothing
bolBuktiOrder = True
bolBuktiKirim = False
bolBuktiPenerimaan = False
Dim strSQL As String
Dim pegawai1, pegawai2, pegawai3, nip1, nip2, nip3 As String
    
        With crCetakBuktiOrderBarang
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String
            strSQL = " select so.tglorder,so.noorder,jp.jeniskirim,ru2.namaruangan as ruangantujuan,so.keteranganorder, " & _
                     " ru.namaruangan as ruangan, 'KA. ' || dp.namadepartemen as kepalaBagian, pg.namalengkap,jp.jeniskirim || ' ' || ru.namaruangan || ' Tgl ' || to_char(so.tglorder, 'DD-MM-YYYY HH:MI:SS') AS keteranganorder, " & _
                     " pr.id as idproduk,pr.kdproduk as kdsirs,pr.namaproduk, ss.satuanstandar, op.qtyproduk, so.totalhargasatuan as hargasatuan, (so.totalhargasatuan * op.qtyproduk) as total " & _
                     " from transaksiordertr as so " & _
                     " left join transaksiorderdetailtr as op on op.transaksiorderfk = so.norec " & _
                     " left join pelayananmt as pr on pr.id = op.produkidfk " & _
                     " left join satuanstandarmt as ss on ss.id = pr.satuanstandaridfk " & _
                     " left join jeniskirimmt as jp on jp.id = so.jenispermintaanidfk " & _
                     " left join ruanganmt as ru on ru.id = so.ruanganidfk " & _
                     " left join ruanganmt as ru2 on ru2.id = so.ruangantujuanidfk " & _
                     " left JOIN instalasimt as dp on dp.id = ru.instalasiidfk " & _
                     " left join pegawaimt as pg on pg.id = so.pegawaiorderidfk " & _
                     " where so.norec = '" & strNoKirim & "'"

             ReadRs strSQL
             If pegawaiMengetahui <> "" Then
                 ReadRs4 "SELECT pg.namalengkap,pg.nippns " & _
                         "FROM pegawaimt as pg " & _
                         "WHERE pg.id = '" & pegawaiMengetahui & "'"
                
                If RS4.EOF = False Then
                    pegawai1 = RS4!namalengkap
                    nip1 = "NIP. " & RS4!nippns
                Else
                    pegawai1 = "-"
                    nip1 = "NIP. -"
                End If
            Else
                pegawai1 = "-"
                nip1 = "NIP. -"
            End If
            
            If pegawaiMeminta <> "" Then
                 ReadRs3 "SELECT pg.namalengkap,pg.nippns " & _
                         "FROM pegawai_m as pg " & _
                         "WHERE pg.id = '" & pegawaiMeminta & "'"
                If RS3.EOF = False Then
                    pegawai2 = RS3!namalengkap
                    nip2 = "NIP. " & RS3!nippns
                Else
                    pegawai2 = "-"
                    nip2 = "NIP. -"
                End If
            Else
                pegawai2 = "-"
                nip2 = "NIP. -"
            End If
                                   
             adoReport.CommandText = strSQL
             adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport
             .txtuser.SetText strUser
             .txtNamaRs.SetText UCase$(strNamaLengkapRs)
             .txtAlamatRs.SetText stralmtLengkapRs
             .txtNamaPemerintahan.SetText UCase$(strNamaPemerintah)
             .udtglDok.SetUnboundFieldSource ("{Ado.tglorder}")
             .usNoDok.SetUnboundFieldSource ("{Ado.noorder}")
             .usRuangKirim.SetUnboundFieldSource ("{Ado.ruangan}")
             .usKeterangan.SetUnboundFieldSource ("{Ado.keteranganorder}")
             .usRuangTujuan.SetUnboundFieldSource ("{Ado.ruangantujuan}")
             .usKdBarang.SetUnboundFieldSource ("{ado.idproduk}")
             .usNamaBarang.SetUnboundFieldSource ("{Ado.namaproduk}")
             .usSatuan.SetUnboundFieldSource ("{ado.satuanstandar}")
             .unDiminta.SetUnboundFieldSource ("{Ado.qtyproduk}")
'             .usKdBrgSirs.SetUnboundFieldSource ("{Ado.kdsirs}")
             .ucTotalHarga.SetUnboundFieldSource ("{Ado.total}")
             .txtJabatan.SetText jabatanMengetahui
             .txtKepalaBagian.SetText pegawai1
             .Text73.SetText nip1
             .txtJabPeminta.SetText jabatanMeminta
             .txtPeminta.SetText pegawai2
             .txtNipPeminta.SetText nip2
             
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "Logistik_A4")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakBuktiOrderBarang
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

Public Sub CetakBuktiKirim(strNoKirim As String, pegawaiPenyerah As String, pegawaiMengetahui As String, pegawaiPenerima As String, JabatanPenyerah As String, jabatanMengetahui As String, JabatanPenerima As String, view As String, strUser As String)
'On Error GoTo errLoad
'On Error Resume Next
Set frmLogistik = Nothing
bolBuktiOrder = False
bolBuktiKirim = True
bolBuktiPenerimaan = False
Dim strSQL As String
Dim pegawai1, pegawai2, pegawai3, nip1, nip2, nip3 As String
    
        With crCetakBuktiKirimBarang
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String

            strSQL = " select sk.tglkirim,so.tglorder, sk.nokirim, so.noorder, jp.jeniskirim, ru.namaruangan, " & _
                     " ru.kdruangan  ||  ' - '  ||  ru.namaruangan as ruangankirim,ru2.namaruangan, ru2.kdruangan  ||  ' - '  ||  ru2.namaruangan as ruangantujuan, " & _
                     " pr.id as idproduk,pr.kdproduk as kdsirs,pr.namaproduk, ss.satuanstandar, kp.qtyproduk, kp.qtyorder, kp.hargasatuan, (kp.hargasatuan * kp.qtyproduk) as total, " & _
                     " sk.keteranganlainnyakirim , sp.tglfaktur " & _
                     " from transaksikirimtr as sk " & _
                     " left join transaksikirimdetailtr as kp on kp.transaksikirimfk = sk.norec " & _
                     " left join strukpelayanantr as sp on sp.norec = kp.nostrukterimaidfk " & _
                     " left join transaksiordertr as so on so.norec = sk.transaksiorderfk " & _
                     " left join pelayananmt as pr on pr.id = kp.produkidfk " & _
                     " left join satuanstandarmt as ss on ss.id = pr.satuanstandaridfk " & _
                     " left join jeniskirimmt as jp on jp.id = sk.jenispermintaanidfk " & _
                     " left join ruanganmt as ru on ru.id = sk.ruanganasalidfk " & _
                     " left JOIN instalasimt as dp on dp.id = ru.instalasiidfk " & _
                     " left join ruanganmt as ru2 on ru2.id = sk.ruangantujuanidfk " & _
                     " left JOIN instalasimt as dp2 on dp2.id = ru2.instalasiidfk " & _
                     " where sk.norec = '" & strNoKirim & "' and kp.qtyproduk <> 0 order by pr.namaproduk asc "

             ReadRs strSQL
            If pegawaiPenyerah <> "" Then
            
                ReadRs2 "select pg.namalengkap,pg.nippns " & _
                         "from pegawaimt as pg " & _
                         "where pg.id = '" & pegawaiPenyerah & "'"
                         
                If RS2.EOF = False Then
                    pegawai1 = RS2!namalengkap
                    nip1 = "NIP. " & RS2!nippns
                Else
                    pegawai1 = "-"
                    nip1 = "NIP. -"
                End If
            Else
                    pegawai1 = "-"
                    nip1 = "NIP. -"
            End If
            
            If pegawaiMengetahui <> "" Then
                            
                ReadRs4 "select pg.namalengkap,pg.nippns " & _
                         "from pegawaimt as pg " & _
                         "where pg.id = '" & pegawaiMengetahui & "'"
                         
                If RS4.EOF = False Then
                    pegawai3 = RS4!namalengkap
                    nip3 = "NIP. " & RS4!nippns
                Else
                    pegawai3 = "-"
                    nip3 = "NIP. -"
                End If
            Else
                pegawai3 = "-"
                nip3 = "NIP. -"
            End If
            
            If pegawaiPenerima <> "" Then
                            
                ReadRs3 "select pg.namalengkap,pg.nippns " & _
                         "from pegawaimt as pg " & _
                         "where pg.id = '" & pegawaiPenerima & "'"
                
                If RS3.EOF = False Then
                    pegawai2 = RS3!namalengkap
                    nip2 = "NIP. " & RS3!nippns
                Else
                    pegawai2 = "-"
                    nip2 = "NIP. -"
                End If
            Else
                pegawai2 = "-"
                nip2 = "NIP. -"
            End If
             
           
             adoReport.CommandText = strSQL
             adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport
             .txtNamaRs.SetText UCase$(strNamaLengkapRs)
             .txtNamaPemerintahan.SetText UCase$(strNamaPemerintah)
             .txtAlamatRs.SetText stralmtLengkapRs
             .txtuser.SetText strUser
             .udtglDok.SetUnboundFieldSource ("{Ado.tglkirim}")
             .udTglPermintaan.SetUnboundFieldSource ("{Ado.tglorder}")
             .udTglTerima.SetUnboundFieldSource ("{Ado.tglfaktur}")
             .udTglTerima2.SetUnboundFieldSource ("{Ado.tglfaktur}")
             .usNoDok.SetUnboundFieldSource ("{Ado.nokirim}")
             .usNoPemesanan.SetUnboundFieldSource ("{Ado.noorder}")
             .usRuangKirim.SetUnboundFieldSource ("{Ado.ruangankirim}")
             .usKeterangan.SetUnboundFieldSource ("{Ado.keteranganlainnyakirim}")
             .usRuangTujuan.SetUnboundFieldSource ("{Ado.ruangantujuan}")
             .usKdBarang.SetUnboundFieldSource ("{ado.idproduk}")
             .usNamaBarang.SetUnboundFieldSource ("{Ado.namaproduk}")
             .usSatuan.SetUnboundFieldSource ("{ado.satuanstandar}")
             .ucHarga.SetUnboundFieldSource ("{Ado.hargasatuan}")
             .unDiminta.SetUnboundFieldSource ("{Ado.qtyorder}")
             .unDikirim.SetUnboundFieldSource ("{Ado.qtyproduk}")
             .ucTotalHarga.SetUnboundFieldSource ("{Ado.total}")
             .usKdSirs.SetUnboundFieldSource ("{Ado.kdsirs}")
              .txtJabatan1.SetText JabatanPenyerah
              .txtJabatan2.SetText JabatanPenerima
              .txtJabatan3.SetText jabatanMengetahui
              .txtPegawai1.SetText pegawai1
              .txtPegawai2.SetText pegawai2
              .txtPegawai3.SetText pegawai3
              .txtNIP1.SetText nip1
              .txtNIP2.SetText nip2
              .txtNIP3.SetText nip3
             
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "Logistik_A4")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakBuktiKirimBarang
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

Public Sub CetakBuktiPenerimaan(strNoKirim As String, pegawaiPenyerah As String, pegawaiPenerima As String, pegawaiMengetahui As String, JabatanPenyerah As String, JabatanPenerima As String, jabatanMengetahui As String, view As String, strUser As String)
On Error GoTo errLoad
On Error Resume Next
Set frmLogistik = Nothing
bolBuktiOrder = False
bolBuktiKirim = False
bolBuktiPenerimaan = True
Dim strSQL As String
Dim pegawai1, pegawai2, pegawai3, nip1, nip2, nip3 As String

With crCetakBuktiPenerimaan
            Set adoReport = New ADODB.Command
            adoReport.ActiveConnection = CN_String
            
            strSQL = " SELECT sp.nostruk,sp.nofaktur,to_char(sp.tglstruk, 'DD-MM-YYYY') AS tglstruk,sp.tglspk,to_char(sp.tglfaktur, 'DD-MM-YYYY') AS tglfaktur,to_char(sp.tglkontrak, 'DD-MM-YYYY') AS tglkontrak, " & _
                     " to_char(sr.tglrealisasi,'DD-MM-YYYY') AS tglrealisasi,CASE WHEN ap.asalproduk IS NULL THEN '-' ELSE ap.asalproduk END AS asalproduk, " & _
                     " CASE WHEN rk.namarekanan IS NULL THEN '-' ELSE rk.namarekanan END AS rekanan,pr.id AS idproduk,pr.namaproduk,ss.satuanstandar,CASE WHEN ru.namaruangan IS NULL THEN '-' ELSE ru.kdruangan || ' - ' || ru.namaruangan END AS gudang, " & _
                     " sp.keteranganambil,CASE WHEN sp.nokontrak IS NULL THEN '-' ELSE sp.nokontrak END AS nokontrak,CASE WHEN sp.nosppb IS NULL THEN '-' ELSE sp.nosppb END AS nosppb,spd.qtyproduk,spd.hargasatuan, " & _
                     " spd.persenppn,spd.persendiscount,spd.persenppn,spd.persendiscount,CAST((spd.qtyproduk*spd.hargasatuan) AS FLOAT) as subtotal, " & _
                     " CAST((((spd.persendiscount*spd.hargasatuan)/100)*spd.qtyproduk) AS FLOAT) AS diskon, " & _
                     " CAST((spd.persenppn*((spd.qtyproduk*spd.hargasatuan)-(((spd.persendiscount*spd.hargasatuan)/100)*spd.qtyproduk))/100) AS FLOAT) AS ppn, " & _
                     " CAST(((spd.qtyproduk*spd.hargasatuan)-(((spd.persendiscount*spd.hargasatuan)/100)*spd.qtyproduk))+(spd.persenppn*((spd.qtyproduk*spd.hargasatuan)-(((spd.persendiscount*spd.hargasatuan)/100)*spd.qtyproduk))/100) AS FLOAT) AS total " & _
                     " FROM strukpelayanantr AS sp " & _
                     " LEFT JOIN strukpelayanandetailtr AS spd ON spd.nostrukidfk = sp.norec " & _
                     " LEFT JOIN pegawaimt AS pg ON pg.id = sp.pegawaipenanggungjawabidfk " & _
                     " LEFT JOIN ruanganmt AS ru ON ru.id = sp.ruanganidfk " & _
                     " LEFT JOIN pelayananmt AS pr ON pr.id = spd.produkidfk " & _
                     " LEFT JOIN asalprodukmt AS ap ON ap.id = spd.asalprodukidfk " & _
                     " LEFT JOIN rekananmt AS rk ON rk.id = sp.rekananidfk " & _
                     " LEFT JOIN satuanstandarmt AS ss ON ss.id = spd.satuanstandaridfk " & _
                     " LEFT JOIN transaksirealisasidetailtr AS rr ON rr.penerimaanfk = sp.norec " & _
                     " LEFT JOIN transaksirealisasitr AS sr ON sr.norec = rr.strukrealisasifk " & _
                     " WHERE sp.norec = '" & strNoKirim & "'" & _
                     " GROUP BY sp.nostruk,sp.nofaktur,sp.tglstruk,sp.tglspk,tglfaktur,sp.tglkontrak, " & _
                     " ap.asalproduk,rk.id,rk.namarekanan,ru.kdruangan,ru.namaruangan, " & _
                     " sp.tglstruk,sp.keteranganambil,pr.id,ss.satuanstandar,pr.namaproduk, " & _
                     " spd.qtyproduk,sp.nokontrak,sp.nosppb,sr.tglrealisasi,spd.persenppn, " & _
                     " spd.persendiscount,spd.hargasatuan "

             ReadRs strSQL
             If pegawaiPenerima <> "" Then
            
               ReadRs2 "select pg.namalengkap,pg.nippns " & _
                     "from pegawaimt as pg " & _
                     "where pg.id = '" & pegawaiPenerima & "'"
                If RS2.EOF = False Then
                    pegawai1 = RS2!namalengkap
                    nip1 = "NIP. " & RS2!nippns
                Else
                    pegawai1 = "-"
                    nip1 = "NIP. -"
                End If
            Else
                    pegawai1 = "-"
                    nip1 = "NIP. -"
            End If
            
            If pegawaiMengetahui <> "" Then
                ReadRs4 "select pg.namalengkap,pg.nippns " & _
                     "from pegawaimt as pg " & _
                     "where pg.id = '" & pegawaiMengetahui & "'"
                                            
                If RS4.EOF = False Then
                    pegawai3 = RS4!namalengkap
                    nip3 = "NIP. " & RS4!nippns
                Else
                    pegawai3 = "-"
                    nip3 = "NIP. -"
                End If
            Else
                pegawai3 = "-"
                nip3 = "NIP. -"
            End If
            
            If pegawaiPenyerah <> "" Then
                            
                 ReadRs3 "select pg.namalengkap,pg.nippns " & _
                     "from pegawaimt as pg " & _
                     "where pg.id = '" & pegawaiPenyerah & "'"
               
                If RS3.EOF = False Then
                    pegawai2 = RS3!namalengkap
                    nip2 = "NIP. " & RS3!nippns
                Else
                    pegawai2 = "-"
                    nip2 = "NIP. -"
                End If
            Else
               pegawai2 = "-"
               nip2 = "NIP. -"
            End If
                        
            
             adoReport.CommandText = strSQL
             adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport

             .txtuser.SetText strUser
             .txtNamaRs.SetText strNamaLengkapRs
             .txtAlamatRs.SetText strAlamatRS & ", " & strKodePos & ", " & strNoTlpn & ", " & strNoFax
             .txtTelpFax.SetText strEmail & ", " & strWebSite

             .usTglSPk.SetUnboundFieldSource ("{Ado.tglrealisasi}")
             .usResep.SetUnboundFieldSource ("{Ado.nofaktur}")
             .usPemesanan.SetUnboundFieldSource ("{Ado.nostruk}")
             .usRekanan.SetUnboundFieldSource ("{Ado.rekanan}")
             .usNamaRuangan.SetUnboundFieldSource ("{Ado.gudang}")
             .usSumberDana.SetUnboundFieldSource ("{Ado.asalproduk}")
             .usNoSPK.SetUnboundFieldSource ("{Ado.nosppb}")
             .usNoKontrak.SetUnboundFieldSource ("{Ado.nokontrak}")
             .usTglDokumen.SetUnboundFieldSource ("{Ado.tglfaktur}")
             .udTglOrder.SetUnboundFieldSource ("{Ado.tglrealisasi}")
             .usTglKontrak.SetUnboundFieldSource ("{Ado.tglkontrak}")
             .usKeteranganAmbil.SetUnboundFieldSource ("{Ado.keteranganambil}")
             
             .usKdBarang.SetUnboundFieldSource ("{ado.idproduk}")
             .usNamaBarang.SetUnboundFieldSource ("{Ado.namaproduk}")
             .usSatuan.SetUnboundFieldSource ("{ado.satuanstandar}")
             .ucHarga.SetUnboundFieldSource ("{Ado.hargasatuan}")
             .unqty.SetUnboundFieldSource ("{Ado.qtyproduk}")
             .unDiskon.SetUnboundFieldSource ("{Ado.persendiscount}")
             .unPPN.SetUnboundFieldSource ("{Ado.persenppn}")
             .uucTotal.SetUnboundFieldSource ("{Ado.total}")
             .ucSubTotal.SetUnboundFieldSource ("{Ado.subtotal}")
             .ucTotalDiskon.SetUnboundFieldSource ("{Ado.diskon}")
             .ucTotalPPN.SetUnboundFieldSource ("{Ado.ppn}")
             
             .txtJabatan1.SetText JabatanPenyerah
             .txtJabatan2.SetText JabatanPenerima
             .txtJabatan.SetText jabatanMengetahui
             
             .txtMengetahui.SetText pegawai3
             .txtPenyerahan.SetText pegawai1
             .txtPenerima.SetText pegawai2
             
             .txtNipMengetahui.SetText nip3
             .txtNipPenyerahan.SetText nip1
             .txtNipPenerima.SetText nip2
             
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "Logistik_A4")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = crCetakBuktiPenerimaan
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
