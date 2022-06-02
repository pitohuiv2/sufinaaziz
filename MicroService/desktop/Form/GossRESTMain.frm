VERSION 5.00
Begin VB.Form GossRESTMain 
   BorderStyle     =   4  'Fixed ToolWindow
   Caption         =   "deskSupport"
   ClientHeight    =   615
   ClientLeft      =   45
   ClientTop       =   690
   ClientWidth     =   1290
   Icon            =   "GossRESTMain.frx":0000
   LinkTopic       =   "GossRESTMain"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   OLEDropMode     =   1  'Manual
   ScaleHeight     =   615
   ScaleWidth      =   1290
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin PSv1.Gossamer Gossamer1 
      Left            =   3960
      Top             =   1560
      _ExtentX        =   741
      _ExtentY        =   741
   End
   Begin VB.Label Label2 
      Caption         =   "."
      Height          =   375
      Left            =   2880
      TabIndex        =   1
      Top             =   0
      Width           =   375
   End
   Begin VB.Label Label1 
      Caption         =   "@rockettazz"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   9.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   -1  'True
         Strikethrough   =   0   'False
      EndProperty
      Height          =   255
      Left            =   0
      OLEDropMode     =   1  'Manual
      TabIndex        =   0
      Top             =   360
      Width           =   3135
   End
   Begin VB.Menu mnFile 
      Caption         =   "File"
      Begin VB.Menu mnuSettingPrinter 
         Caption         =   "Printer Option"
      End
      Begin VB.Menu mnuKoneksi 
         Caption         =   "Koneksi"
      End
      Begin VB.Menu fgdgdfg 
         Caption         =   "Version 20171116.1"
      End
      Begin VB.Menu Pemanggil 
         Caption         =   "Pemanggil"
      End
      Begin VB.Menu asdasdasdsa 
         Caption         =   "-"
      End
      Begin VB.Menu mnuExit 
         Caption         =   "Exit"
      End
   End
End
Attribute VB_Name = "GossRESTMain"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Option Explicit

'Initialized during Form_Load via SanitizeInit, used during Sanitize:
Private Const FROM_CHARS As String = "[]\[\]\'\;\%\_\#"
Private Const TO_CHARS As String = "[[]]\[[]\[]]\['']\\[%]\[_]\[#]"
Private FromChars() As String
Private ToChars() As String

Dim nid As NOTIFYICONDATA ' deklarasi variable

Public STM As ADODB.Stream
Private LogFile As Integer
Private graphicSDKVersion   As String
Private prnSDKVersion       As String

Public urlLengkap As String
Dim fso As New FileSystemObject


Private Sub SanitizeInit()
    FromChars = Split(FROM_CHARS, "\")
    ToChars = Split(TO_CHARS, "\")
End Sub

Private Function Sanitize(ByVal Arg As String) As String
    'Attempt to "sanitize" argument Arg against SQL Injection errors.
    Dim i As Long
    
    For i = 0 To UBound(FromChars)
        Arg = Replace$(Arg, FromChars(i), ToChars(i))
    Next
    Sanitize = Arg
End Function
 
Private Sub TerminateProcess(app_exe As String)
    Dim Process As Object
    For Each Process In GetObject("winmgmts:").ExecQuery("Select Name from Win32_Process Where Name = '" & app_exe & "'")
        Process.Terminate
    Next
End Sub

Private Sub DisplayRegistrasi_Click()
    DisplayAntrian.Show vbModal
End Sub

Private Sub Form_Initialize()
On Error Resume Next
Dim msg As String
    
    fgdgdfg.Caption = App.Comments
    
    GossRESTDB.InitializeDB
    'GetGraphicsDllVersion graphicSDKVersion
    
    If graphicSDKVersion <> "" Then
        msg = "Graphics: " & graphicSDKVersion & "; "
    End If
        
    ' Gets printer dll version
    '     and if present enables the magnetic encoding frame
    
'    GetPrinterDllVersion prnSDKVersion
'
'    If prnSDKVersion <> "" Then
'        msg = msg & "Printer: " & prnSDKVersion & "; "
'    End If
    
    ' Displays dll versions
     Label2.ToolTipText = msg
    
End Sub

Private Sub Form_Load()
    Call openConnection
    Label1.ToolTipText = StatusCN
    SanitizeInit
'    Dim egi As String
    Set STM = New ADODB.Stream
    
    LogFile = FreeFile(0)
    
    Open UCase(fso.GetDriveName(App.Path)) & "/log.txt" For Append As #LogFile
    Gossamer1.StartListening
    
    'Set INISIASI RS
    Call openConnection
    
    Dim rsProfile As New ADODB.Recordset
       
    strSQL = "select id AS idrs, namaexternal, namapemerintahan, namalengkap, alamatlengkap, fixedphone, faksimile, website, kodepos, alamatemail, namakota from profile_m where  statusenabled='t'"
    
    rsProfile.Open strSQL, CN, adOpenStatic, adLockReadOnly

    stridRs = IIf(IsNull(rsProfile("idrs")), "0", rsProfile("idrs"))
    strNamaRS = IIf(IsNull(rsProfile("namaexternal")), IIf(IsNull(rsProfile("namalengkap")), "-", rsProfile("namalengkap")), rsProfile("namaexternal"))
    strNamaLengkapRs = IIf(IsNull(rsProfile("namalengkap")), "-", rsProfile("namalengkap"))
    strAlamatRS = IIf(IsNull(rsProfile("alamatlengkap")), "-", rsProfile("alamatlengkap"))
    strNoTlpn = "Tlpn. " + IIf(IsNull(rsProfile("fixedphone")), "-", rsProfile("fixedphone"))
    strNoFax = "Fax. " + IIf(IsNull(rsProfile("faksimile")), "-", rsProfile("faksimile"))
    strNamaPemerintah = IIf(IsNull(rsProfile("namapemerintahan")), "-", rsProfile("namapemerintahan"))
    strWebSite = "Website " + IIf(IsNull(rsProfile("website")), "-", rsProfile("website"))
    strKodePos = IIf(IsNull(rsProfile("kodepos")), "-", rsProfile("kodepos"))
    strEmail = "Email. " + IIf(IsNull(rsProfile("alamatemail")), "-", rsProfile("alamatemail"))
    strNamaKota = IIf(IsNull(rsProfile("namakota")), "-", rsProfile("namakota"))
    stralmtLengkapRs = strAlamatRS + " Kode Pos : " + strKodePos + " " + strNoTlpn + " " + strNoFax + " " + strEmail
    
'   Show
    WindowState = vbHide
    Call minimize_to_tray
    If CN.State = adStateOpen Then CN.Close
End Sub

Private Sub Form_Unload(Cancel As Integer)
    Gossamer1.StopListening
    
    Close #LogFile
    'To Tray
    Shell_NotifyIcon NIM_DELETE, nid
    If CN.State = adStateOpen Then CN.Close
End Sub
Private Sub Form_MouseMove(Button As Integer, Shift As Integer, X As Single, Y As Single)
    Dim msg As Long
    Dim sFilter As String
    
    msg = X / Screen.TwipsPerPixelX
    Select Case msg
        Case WM_LBUTTONDOWN
            Me.Show ' tampilkan form
            Shell_NotifyIcon NIM_DELETE, nid
        Case WM_LBUTTONUP
        
        Case WM_LBUTTONDBLCLK
        
        Case WM_RBUTTONDOWN
        
        Case WM_RBUTTONUP
'            Me.Show
'            Shell_NotifyIcon NIM_DELETE, nid
            PopupMenu mnFile
        Case WM_RBUTTONDBLCLK
    End Select
End Sub

Sub minimize_to_tray()
    Me.Hide
    nid.cbSize = Len(nid)
    nid.hwnd = Me.hwnd
    nid.uId = vbNull
    nid.uFlags = NIF_ICON Or NIF_TIP Or NIF_MESSAGE
    nid.uCallBackMessage = WM_MOUSEMOVE
    nid.hIcon = Me.Icon
    nid.szTip = "rocketTazz" & vbNullChar
    Shell_NotifyIcon NIM_ADD, nid
End Sub

Private Sub Gossamer1_DynamicRequest( _
    ByVal Method As String, _
    ByVal URI As String, _
    ByVal Params As String, _
    ByVal ReqHeaders As Collection, _
    ByRef RespStatus As Single, _
    ByRef RespStatusText As String, _
    ByRef RespMIME As String, _
    ByRef RespExtraHeaders As String, _
    ByRef RespBody() As Byte, _
    ByVal ClientIndex As Integer)
    On Error Resume Next
    
    Dim ErrNumber As Long
    Dim ErrDescription As String
    
    If Method = "GET" Then
'    If Method = "POST" Then
        'We'll assume URI = "/query" but we'll take any as "/query" in this program.
        On Error Resume Next

        Dim Itm As Variant
        For Each Itm In ReqHeaders
            Debug.Print Itm(0), Itm(1)
        Next Itm
        urlLengkap = URI
        If URI = "\desk\routes" Then RespBody = routes.desktop(Gossamer1.URLDecode(Params))
       
        If Err Then
            ErrNumber = Err.Number
            ErrDescription = Err.Description
            On Error GoTo 0
            RespStatus = 500
            RespStatusText = "Internal Server Error"
            Print #LogFile, _
                  "Error "; _
                  CStr(ErrNumber); _
                  " (&H"; Right$("0000000" & Hex$(ErrNumber), 8); ") "; _
                  ErrDescription
            Exit Sub
        End If
        On Error GoTo 0
        RespStatus = 200
        RespStatusText = "ok"
        RespMIME = "application/json" '; charset=utf-8"
        RespExtraHeaders = "Allow: GET" & vbCrLf
        'RespExtraHeaders = "Host: 172.16.16.14:8200"
    Else
        RespStatus = 405
        RespStatusText = "Method Not Allowed"
        RespExtraHeaders = "Allow: GET" & vbCrLf
        
    End If
End Sub

Private Sub Gossamer1_LogEvent(ByVal GossEvent As GossEvent, ByVal ClientIndex As Integer)
    With GossEvent
        Print #LogFile, _
              Format$(.Timestamp, "YYYY-MM-DD HH:NN:SS, "); _
              CStr(ClientIndex); ", "; _
              ", "; _
              CStr(.EventType); ", "; _
              CStr(.EventSubtype); ", "; _
              .Method; ", "; _
              .IP + ":3885" + Replace(urlLengkap, "\", "/") + .Text
    End With
End Sub
Private Function Query(ByVal QueryText As String) As Byte()
'    Const QUERY_TEMPLATE As String = _
'            "SELECT * FROM [Movies] " _
'          & "WHERE " _
'          & "[Title] = '$1$' OR " _
'          & "[Title] LIKE '$1$[ ,.:?]%' OR " _
'          & "[Title] LIKE '%[ ,.:?]$1$' OR " _
'          & "[Title] LIKE '%[ ,.:?]$1$[ ,.:?]%' OR " _
'          & "[Initials1] = '$2$' OR " _
'          & "[Initials2] = '$2$' " _
'          & "ORDER BY [Title] ASC"
'    Dim ActualQuery As String
    Dim Root As JNode
'    Dim RecordIndex As Long
'    Dim FieldIndex As Long
    
'    If Len(QueryText) = 0 Then
'        'Shouldn't match anything, just get an empty Recordset so
'        'we have the field names for headings:
'        ActualQuery = Replace$(Replace$(QUERY_TEMPLATE, _
'                                        "$2$", _
'                                        vbFormFeed), _
'                               "$1$", _
'                               vbFormFeed)
'    Else
'        ActualQuery = Replace$(Replace$(QUERY_TEMPLATE, _
'                                        "$2$", _
'                                        GossRESTDB.AsInitials(QueryText)), _
'                               "$1$", _
'                               Sanitize(QueryText))
'    End If
'    Debug.Print ActualQuery
'    With RS
'        .Open ActualQuery, , , , adCmdText
'        Set Root = New JNode
'        Root("Perusahaan") = "Jasamedika"
'        Set Root("Pegawai") = New JNode
'        Root("Pegawai")(0) = "Riko"
'        Root("Pegawai")(1) = "Agus"
'        For FieldIndex = 0 To .Fields.Count - 1
'            Root("ColumnNames")(FieldIndex) = .Fields(FieldIndex).Name
'        Next
'        If RS.RecordCount > 0 Then
'            Set Root("Rows") = New JNode
'            Root("Rows").MakeArray
'            Do Until .EOF
'                Set Root("Rows")(RecordIndex) = New JNode
'                Root("Rows")(RecordIndex).MakeArray
'                For FieldIndex = 0 To .Fields.Count - 1
'                    Root("Rows")(RecordIndex).Value(FieldIndex) = .Fields(FieldIndex).Value
'                Next
'                .MoveNext
'                RecordIndex = RecordIndex + 1
'            Loop
'        End If
'        .Close
'    End With

If Len(QueryText) > 0 Then
    
    If QueryText = "cetak" Then
        Set Root = New JNode
        'MsgBox "adasdasd", vbInformation, "CETAK SERVICE"
        Root("Status") = "Sukses Dicetak"
'        Set Root("Pegawai") = New JNode
'        Root("Pegawai")(0) = "Riko"
'        Root("Pegawai")(1) = "Agus"
    Else
        Set Root = New JNode
        Root("Status") = "Error"
    End If
End If
    With STM
        .Open
        .Type = adTypeText
        .CharSet = "utf-8"
        .WriteText Root.JSON, adWriteChar
        .Position = 0
        .Type = adTypeBinary
        Query = .Read(adReadAll)
        .Close
    End With
End Function

Private Sub Label1_Click()
    Call minimize_to_tray
End Sub

Private Sub mnuExit_Click()
    Shell_NotifyIcon NIM_DELETE, nid
    End
End Sub

Private Sub mnuKoneksi_Click()
    frmSettingKoneksi.Show vbModal
End Sub

Private Sub mnuSettingPrinter_Click()
    frmSettingPrinter.Show vbModal
End Sub

'    Public LogFile As Integer
'    LogFile = FreeFile(0)
'
'    Open "E:/txt.txt" For Append As #LogFile
'  sql
'    For i = 0 To 100
'        Print #LogFile, "|" & "024040400415582003KD" & "|,|" & "AS@EPIC" & "|,|" & i & " neng ecie|;"
'    Next
'  sql

Private Sub Picture1_Click()

End Sub

Private Sub Pemanggil_Click()
    frmPemanggil.Show vbModal
End Sub
