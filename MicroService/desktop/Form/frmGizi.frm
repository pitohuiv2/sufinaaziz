VERSION 5.00
Object = "{C4847593-972C-11D0-9567-00A0C9273C2A}#8.0#0"; "crviewer.dll"
Begin VB.Form frmGizi 
   Caption         =   "Gizi"
   ClientHeight    =   7005
   ClientLeft      =   60
   ClientTop       =   345
   ClientWidth     =   5820
   Icon            =   "frmGizi.frx":0000
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
Attribute VB_Name = "frmGizi"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit
Dim Report As New crLabelGizi
Dim bolSuppresDetailSection10 As Boolean
Dim ii As Integer
Dim strPrinter As String
Dim strPrinter1 As String
Dim PrinterNama As String
Dim p As Printer
Dim p2 As Printer
Dim strDeviceName As String
Dim strDriverName As String
Dim strPort As String
Dim tempPrint1 As String
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
    Set frmGizi = Nothing
End Sub

Public Sub CetakLabelGizi(Noregistrasi As String, qty As String, view As String)
'On Error GoTo errLoad
'On Error Resume Next
Dim strSQL As String
Dim i As Integer
Dim str As String
Dim jml As Integer
Dim arr As String
Dim aingmacan As String

    If Noregistrasi <> "" Then
        str = " op.norec in (" & Right(Noregistrasi, Len(Noregistrasi) - 1) & ")"
    End If

    With Report
            Set adoReport = New ADODB.Command
             adoReport.ActiveConnection = CN_String
            
            strSQL = " SELECT pd.noregistrasi,sk.nokirim,sk.qtyproduk AS qtykirim,op.keteranganlainnya AS keteranganlainnyakirim, " & _
                     " op.qtyprodukinuse AS cc,op.jumlah AS volume,pd.tglregistrasi,ps.namapasien || ' [' || CASE WHEN jk.id = 1 THEN 'L' WHEN  jk.id = 2 THEN 'P' ELSE '-' END || ']' AS namapasien, " & _
                     " ps.norm AS nocm,ru.namaruangan AS ruanganasal,jw.jeniswaktu,jd.jenisdiet,op.qtyproduk,kls.namakelas,kd.kategorydiet,to_char(so.tglorder, 'DD-MM-YYYY') AS tglorder, " & _
                     " CASE WHEN jw.jeniswaktu = 'Pagi' THEN '09:00' WHEN jw.jeniswaktu = 'Siang' THEN '14:00' WHEN jw.jeniswaktu = 'Sore' THEN '19:00' " & _
                     " WHEN jw.jeniswaktu = 'Snack Pagi' THEN '12:00' WHEN jw.jeniswaktu = 'Snack Sore' THEN '19:00' END AS jammakan, " & _
                     " jd.jenisdiet || ', ' || CASE WHEN op.keteranganlainnya IS NULL THEN '-' ELSE op.keteranganlainnya END AS keterangan, " & _
                     " to_char(ps.tgllahir, 'DD-MM-YYYY') || '(' || EXTRACT (YEAR FROM AGE(pd.tglregistrasi,ps.tgllahir)) || ' Thn ' " & _
                     " || EXTRACT (MONTH FROM AGE(pd.tglregistrasi,ps.tgllahir)) || ' Bln ' " & _
                     " || EXTRACT (DAY FROM AGE(pd.tglregistrasi,ps.tgllahir)) || ' Hr' || ' )' AS umur,op.arrjenisdiet, " & _
                     " COALESCE (kd.kategorydiet, '-') || ',' || (SELECT string_agg (jm.jenisdiet, ', ') AS arrjenisdietket " & _
                     " FROM jenisdietmt jm WHERE jm. ID :: VARCHAR IN (SELECT UNNEST (string_to_array(op.arrjenisdiet, ',')))) AS arrjenisdietket " & _
                     " FROM transaksiorderdetailtr AS op " & _
                     " INNER JOIN registrasipasientr AS pd ON pd.norec = op.registrasipasienfk " & _
                     " INNER JOIN ruanganmt AS ru ON ru.id = op.ruanganidfk " & _
                     " INNER JOIN pasienmt AS ps ON ps.id = op.normidfk " & _
                     " LEFT JOIN jeniskelaminmt AS jk ON jk.id = ps.jeniskelaminidfk " & _
                     " INNER JOIN transaksiordertr AS so ON so.norec = op.transaksiorderfk " & _
                     " LEFT JOIN transaksikirimtr AS sk ON sk.norec = op.transaksikirimfk " & _
                     " INNER JOIN jeniswaktumt AS jw ON jw.id = op.jeniswaktuidfk " & _
                     " LEFT JOIN jenisdietmt AS jd ON jd.id = op.jenisdietidfk " & _
                     " INNER JOIN kategorydietmt AS kd ON kd.id = op.kategorydietidfk " & _
                     " LEFT JOIN kelasmt AS kls ON kls.id = op.kelasidfk " & _
                     " WHERE " & str

             ReadRs strSQL
             If RS.EOF = False Then
                arr = RS!arrjenisdiet
             Dim strDate
             str = ""
             If Val(qty) - 1 = 0 Then
                 adoReport.CommandText = strSQL
              Else
                 For i = 1 To Val(qty) - 1
                     str = strSQL & " UNION ALL " & str
                 Next
                 
                 adoReport.CommandText = str & strSQL
            End If
                  
            adoReport.CommandType = adCmdUnknown
            .database.AddADOCommand CN_String, adoReport
            .usNamaPasien.SetUnboundFieldSource ("{ado.namapasien}")
            .usNoCm.SetUnboundFieldSource ("{ado.nocm}")
            .usTglLahir.SetUnboundFieldSource ("{ado.umur}")
            .usNamaRuangan.SetUnboundFieldSource ("{ado.ruanganasal}")
            .usKeterangan.SetUnboundFieldSource ("{ado.keteranganlainnyakirim}")
            .usDiit.SetUnboundFieldSource ("{ado.arrjenisdietket}")
            
            If view = "false" Then
                strPrinter1 = GetTxt("Setting.ini", "Printer", "LabelGizi")
                .SelectPrinter "winspool", strPrinter1, "Ne00:"
                .PrintOut False
                Unload Me
                Screen.MousePointer = vbDefault
             Else
                With CRViewer1
                    .ReportSource = Report
                    .ViewReport
                    .Zoom 1
                End With
                Me.Show
                Screen.MousePointer = vbDefault
            End If
      End If
    End With
Exit Sub
errLoad:
End Sub

