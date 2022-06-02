VERSION 5.00
Object = "{6BF52A50-394A-11D3-B153-00C04F79FAA6}#1.0#0"; "wmp.dll"
Begin VB.Form displayRegistrasi 
   BorderStyle     =   0  'None
   Caption         =   "Form1"
   ClientHeight    =   11055
   ClientLeft      =   0
   ClientTop       =   0
   ClientWidth     =   20370
   Icon            =   "displayRegistrasi.frx":0000
   LinkTopic       =   "Form1"
   Picture         =   "displayRegistrasi.frx":048A
   ScaleHeight     =   1080
   ScaleMode       =   0  'User
   ScaleWidth      =   1920
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin VB.Timer Timer1 
      Enabled         =   0   'False
      Interval        =   1000
      Left            =   1200
      Top             =   0
   End
   Begin VB.FileListBox File1 
      Height          =   480
      Left            =   0
      TabIndex        =   16
      Top             =   0
      Visible         =   0   'False
      Width           =   1215
   End
   Begin VB.Label lblWs 
      BackStyle       =   0  'Transparent
      Caption         =   "...."
      ForeColor       =   &H00FFFFFF&
      Height          =   375
      Left            =   2400
      TabIndex        =   22
      Top             =   960
      Visible         =   0   'False
      Width           =   4215
   End
   Begin VB.Label c7 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "A-001"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   2175
      Left            =   24600
      TabIndex        =   20
      Top             =   13800
      Width           =   4095
   End
   Begin VB.Label c6 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "A-001"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   2175
      Left            =   20520
      TabIndex        =   19
      Top             =   13800
      Width           =   4095
   End
   Begin VB.Label Label5 
      Alignment       =   2  'Center
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
      Caption         =   "Counter 07"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   24
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   615
      Left            =   24600
      TabIndex        =   18
      Top             =   13200
      Width           =   4095
   End
   Begin VB.Label Label4 
      Alignment       =   2  'Center
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
      Caption         =   "Counter 06"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   24
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   615
      Left            =   20520
      TabIndex        =   17
      Top             =   13200
      Width           =   4095
   End
   Begin VB.Label c5 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "A-001"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   2175
      Left            =   16440
      TabIndex        =   15
      Top             =   13800
      Width           =   4095
   End
   Begin VB.Label c4 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "A-001"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   2175
      Left            =   12360
      TabIndex        =   14
      Top             =   13800
      Width           =   4095
   End
   Begin VB.Label c3 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "A-001"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   2175
      Left            =   8280
      TabIndex        =   13
      Top             =   13800
      Width           =   4095
   End
   Begin VB.Label c2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "A-001"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   2175
      Left            =   4200
      TabIndex        =   12
      Top             =   13800
      Width           =   4095
   End
   Begin VB.Label c1 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "A-001"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   2175
      Left            =   120
      TabIndex        =   11
      Top             =   13800
      Width           =   4095
   End
   Begin VB.Label Label10 
      Alignment       =   2  'Center
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
      Caption         =   "Counter 05"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   24
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   615
      Left            =   16440
      TabIndex        =   10
      Top             =   13200
      Width           =   4095
   End
   Begin VB.Label Label9 
      Alignment       =   2  'Center
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
      Caption         =   "Counter 04"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   24
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   615
      Left            =   12360
      TabIndex        =   9
      Top             =   13200
      Width           =   4095
   End
   Begin VB.Label Label8 
      Alignment       =   2  'Center
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
      Caption         =   "Counter 03"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   24
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   615
      Left            =   8280
      TabIndex        =   8
      Top             =   13200
      Width           =   4095
   End
   Begin VB.Label Label7 
      Alignment       =   2  'Center
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
      Caption         =   "Counter 02"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   24
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   615
      Left            =   4200
      TabIndex        =   7
      Top             =   13200
      Width           =   4095
   End
   Begin VB.Label Label6 
      Alignment       =   2  'Center
      Appearance      =   0  'Flat
      BackColor       =   &H80000005&
      BackStyle       =   0  'Transparent
      BorderStyle     =   1  'Fixed Single
      Caption         =   "Counter 01"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   24
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000080FF&
      Height          =   615
      Left            =   120
      TabIndex        =   6
      Top             =   13200
      Width           =   4095
   End
   Begin VB.Label c0 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "01"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   90
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00404040&
      Height          =   2775
      Left            =   20760
      TabIndex        =   5
      Top             =   7920
      Width           =   7815
   End
   Begin VB.Label q0 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "A-001"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   90
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00404040&
      Height          =   2775
      Left            =   20880
      TabIndex        =   4
      Top             =   3360
      Width           =   7695
   End
   Begin VB.Label Label3 
      BackStyle       =   0  'Transparent
      Caption         =   "No Counter :"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   24
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00404040&
      Height          =   855
      Left            =   20760
      TabIndex        =   3
      Top             =   7080
      Width           =   5775
   End
   Begin VB.Label Label2 
      BackStyle       =   0  'Transparent
      Caption         =   "No Antrian :"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   24
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00404040&
      Height          =   855
      Left            =   20760
      TabIndex        =   2
      Top             =   2160
      Width           =   5775
   End
   Begin VB.Label Label1 
      BackStyle       =   0  'Transparent
      Caption         =   "RS UMUM ATMAJAYA"
      BeginProperty Font 
         Name            =   "Nirmala UI"
         Size            =   35.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00404040&
      Height          =   1575
      Left            =   20760
      TabIndex        =   1
      Top             =   360
      Width           =   7815
   End
   Begin WMPLibCtl.WindowsMediaPlayer WindowsMediaPlayer1 
      Height          =   13560
      Left            =   -120
      TabIndex        =   0
      Top             =   -360
      Width           =   20580
      URL             =   ""
      rate            =   1
      balance         =   0
      currentPosition =   0
      defaultFrame    =   ""
      playCount       =   1
      autoStart       =   -1  'True
      currentMarker   =   0
      invokeURLs      =   -1  'True
      baseURL         =   ""
      volume          =   50
      mute            =   0   'False
      uiMode          =   "none"
      stretchToFit    =   0   'False
      windowlessVideo =   0   'False
      enabled         =   -1  'True
      enableContextMenu=   -1  'True
      fullScreen      =   0   'False
      SAMIStyle       =   ""
      SAMILang        =   ""
      SAMIFilename    =   ""
      captioningID    =   ""
      enableErrorDialogs=   0   'False
      _cx             =   36301
      _cy             =   23918
   End
   Begin VB.Label Label13 
      BackColor       =   &H00808000&
      ForeColor       =   &H00FF0000&
      Height          =   2295
      Left            =   0
      TabIndex        =   21
      Top             =   13080
      Width           =   28695
   End
End
Attribute VB_Name = "displayRegistrasi"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False

Private Declare Function sndPlaySound Lib "winmm.dll" Alias "sndPlaySoundA" (ByVal lpszSoundName As String, ByVal uFlags As Long) As Long
Public Play As Boolean


Private Sub Form_DblClick()
    End
End Sub

Private Sub Form_Load()
    Dim posisi As String
    portanudigeroan = 1
    
    posisi = GetTxt("Setting.ini", "windows", "posisi")
    If posisi = "kanan" Then
        Me.Move Screen.Width, 0, Screen.Width, Screen.Height
    End If
    If posisi = "tengah" Then
        Me.Move 0, 0, Screen.Width, Screen.Height
    End If
'    lblconn.Caption = dbConn
    Timer1.Enabled = True
'    For i = 0 To 9
'        lbl(i).Caption = ""
'    Next
    File1.Path = App.Path & "\video"
    tmt3 = 10
    tmt = 100
    onload = True
    Label1.Caption = App.Path
    tmt3 = 60
'    Frame1.Visible = False
    
'    If SKiri = 1 Then
'        lKiri.Visible = True
'    Else
'        lKiri.Visible = False
'    End If
'
'    If SKanan = 1 Then
'        lKanan.Visible = True
'    Else
'        lKanan.Visible = False
'    End If
    
    sora = GetTxt2("Setting.ini", "Volume", "Video")
    WindowsMediaPlayer1.URL = App.Path & "\video\" & File1.List(0)
    WindowsMediaPlayer1.settings.Volume = sora
    WindowsMediaPlayer1.settings.setMode "loop", True
    reload = True
'    File1.Path = App.Path & "\video"
'    WindowsMediaPlayer1.URL = App.Path & "\video\" & File1.List(0)
'    WindowsMediaPlayer1.settings.Volume = 50
'    WindowsMediaPlayer1.settings.setMode "loop", True
'    Play = False
    
End Sub

Private Sub Form_Unload(Cancel As Integer)
    End
End Sub

'Call loadAntrian
'Call LoadTrigger

Public Sub LoopAntrian()
    
    If Play = False Then GoTo home
    strSQL = "select* from perjanjianpasientr  where statuspanggil ='1' and tanggalreservasi between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "'"
    Call ReadRs(strSQL)
    
    If RS.RecordCount <> 0 Then
        RS.MoveFirst
        For i = 0 To RS.RecordCount - 1
            If RS!tempatlahir = 1 Then
                If C1.Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                C1.Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 1
            End If
            If RS!tempatlahir = 2 Then
                If C2.Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                C2.Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 2
            End If
            If RS!tempatlahir = 3 Then
                If c3.Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                c3.Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 3
            End If
            If RS!tempatlahir = 4 Then
                If C4.Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                C4.Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 4
            End If
            If RS!tempatlahir = 5 Then
                If c5.Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                c5.Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 5
            End If
            If RS!tempatlahir = 6 Then
                If c6.Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                c6.Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 6
            End If
            If RS!tempatlahir = 7 Then
                If c7.Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                c7.Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 7
            End If
            If disada = True Then Call PlaySound(RS!noantrian, UCase(RS!jenis))
            q0.Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
            c0.Caption = RS!tempatlahir

            RS.MoveNext
        Next
    End If
home:

End Sub

Private Sub PlaySound(angka As Integer, jenis As String)
Dim t As Single
Dim belas As Boolean
Dim puluh As Boolean
Dim ratus As Boolean
Dim lbl As String

    If onload = True Then Exit Sub
    lblpanggil = jenis & "-" & Format(angka, "0##")
    lblpanggil2 = "Loket " & loket
'    lbl(loket - 1).BackColor = &H8080FF
'    lbl(loket - 1).ForeColor = &H0
'    lbl(loket - 1).BackStyle = 1
    Timer2.Enabled = True
    Call sndPlaySound(App.Path & "\new_sound\nomor-urut.wav", SND_ASYNC Or SND_NODEFAULT)
    
    t = Timer
    Do
        DoEvents
    Loop Until Timer - t > 2
    
    If jenis = "A" Then Call sndPlaySound(App.Path & "\sound\A.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "B" Then Call sndPlaySound(App.Path & "\sound\B.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "C" Then Call sndPlaySound(App.Path & "\sound\C.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "D" Then Call sndPlaySound(App.Path & "\sound\D.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "E" Then Call sndPlaySound(App.Path & "\sound\E.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "F" Then Call sndPlaySound(App.Path & "\sound\F.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "G" Then Call sndPlaySound(App.Path & "\sound\G.wav", SND_ALIAS Or SND_SYNC)
 
    
    belas = False
    puluh = False
    ratus = False
    
    If angka > 199 And angka < 1000 Then ratus = True
    If angka > 99 And angka < 200 Then Call sndPlaySound(App.Path & "\new_sound\seratus.wav", SND_ALIAS Or SND_SYNC): angka = angka - 100
    If angka > 19 And angka < 100 Then puluh = True
    
    If angka < 20 And angka > 11 Then angka = angka - 10: belas = True
    
    If Len(CStr(angka)) = 2 And angka = 10 Then Call sndPlaySound(App.Path & "\new_sound\sepuluh.wav", SND_ALIAS Or SND_SYNC): GoTo loketttt
    If Len(CStr(angka)) = 2 And angka = 11 Then Call sndPlaySound(App.Path & "\new_sound\sebelas.wav", SND_ALIAS Or SND_SYNC): GoTo loketttt
    If Len(CStr(angka)) = 3 And angka = 100 Then Call sndPlaySound(App.Path & "\new_sound\seratus.wav", SND_ALIAS Or SND_SYNC): GoTo loketttt
    If Len(CStr(angka)) = 4 And angka = 1000 Then Call sndPlaySound(App.Path & "\new_sound\seribu.wav", SND_ALIAS Or SND_SYNC): GoTo loketttt
    
    For i = 1 To Len(CStr(angka))
        If ratus = False And angka > 200 And Val(Mid(angka, 2, 2)) = 10 Then Call sndPlaySound(App.Path & "\new_sound\sepuluh.wav", SND_ALIAS Or SND_SYNC): Exit For
        If ratus = False And angka > 200 And Val(Mid(angka, 2, 2)) = 11 Then Call sndPlaySound(App.Path & "\new_sound\sebelas.wav", SND_ALIAS Or SND_SYNC): Exit For
        If ratus = False And angka > 200 Then
            If Val(Mid(angka, 2, 2)) > 19 And puluh = False Then
                puluh = True
            Else
                puluh = False
            End If
        End If
        If ratus = False And angka > 200 And Val(Mid(angka, 2, 2)) < 20 And Val(Mid(angka, 2, 2)) > 11 Then
            Select Case Val(Mid(angka, 2, 2))
                Case 12
                    Call sndPlaySound(App.Path & "\new_sound\2.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 13
                    Call sndPlaySound(App.Path & "\new_sound\3.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 14
                    Call sndPlaySound(App.Path & "\new_sound\4.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 15
                    Call sndPlaySound(App.Path & "\new_sound\5.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 16
                    Call sndPlaySound(App.Path & "\new_sound\6.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 17
                    Call sndPlaySound(App.Path & "\new_sound\7.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 18
                    Call sndPlaySound(App.Path & "\new_sound\8.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 19
                    Call sndPlaySound(App.Path & "\new_sound\9.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
            End Select
            Exit For
        End If
        Select Case Mid(CStr(angka), i, 1)
           Case 1
               Call sndPlaySound(App.Path & "\new_sound\1.wav", SND_ALIAS Or SND_SYNC)
           Case 2
               Call sndPlaySound(App.Path & "\new_sound\2.wav", SND_ALIAS Or SND_SYNC)
           Case 3
               Call sndPlaySound(App.Path & "\new_sound\3.wav", SND_ALIAS Or SND_SYNC)
           Case 4
               Call sndPlaySound(App.Path & "\new_sound\4.wav", SND_ALIAS Or SND_SYNC)
           Case 5
               Call sndPlaySound(App.Path & "\new_sound\5.wav", SND_ALIAS Or SND_SYNC)
           Case 6
               Call sndPlaySound(App.Path & "\new_sound\6.wav", SND_ALIAS Or SND_SYNC)
           Case 7
               Call sndPlaySound(App.Path & "\new_sound\7.wav", SND_ALIAS Or SND_SYNC)
           Case 8
               Call sndPlaySound(App.Path & "\new_sound\8.wav", SND_ALIAS Or SND_SYNC)
           Case 9
               Call sndPlaySound(App.Path & "\new_sound\9.wav", SND_ALIAS Or SND_SYNC)
        End Select

        If belas = True Then Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
        If puluh = True Then Call sndPlaySound(App.Path & "\new_sound\puluh.wav", SND_ALIAS Or SND_SYNC) ': puluh = False
        If angka > 19 And angka < 100 Then puluh = False
        If ratus = True Then Call sndPlaySound(App.Path & "\new_sound\ratus.wav", SND_ALIAS Or SND_SYNC): ratus = False
belas:
    Next
    
    
loketttt:
    Call sndPlaySound(App.Path & "\sound\loket.wav", SND_ASYNC Or SND_NODEFAULT)
    
    t = Timer
    Do
        DoEvents
    Loop Until Timer - t > 1
    Select Case loket
        Case 1
            Call sndPlaySound(App.Path & "\new_sound\1.wav", SND_ALIAS Or SND_SYNC)
        Case 2
            Call sndPlaySound(App.Path & "\new_sound\2.wav", SND_ALIAS Or SND_SYNC)
        Case 3
            Call sndPlaySound(App.Path & "\new_sound\3.wav", SND_ALIAS Or SND_SYNC)
        Case 4
            Call sndPlaySound(App.Path & "\new_sound\4.wav", SND_ALIAS Or SND_SYNC)
        Case 5
            Call sndPlaySound(App.Path & "\new_sound\5.wav", SND_ALIAS Or SND_SYNC)
        Case 6
            Call sndPlaySound(App.Path & "\new_sound\6.wav", SND_ALIAS Or SND_SYNC)
        Case 7
            Call sndPlaySound(App.Path & "\new_sound\7.wav", SND_ALIAS Or SND_SYNC)
        Case 8
            Call sndPlaySound(App.Path & "\new_sound\8.wav", SND_ALIAS Or SND_SYNC)
        Case 9
            Call sndPlaySound(App.Path & "\new_sound\9.wav", SND_ALIAS Or SND_SYNC)
        Case 10
            Call sndPlaySound(App.Path & "\new_sound\sepuluh.wav", SND_ALIAS Or SND_SYNC)
    End Select
End Sub

Private Sub Timer1_Timer()
    Timer1.Tag = Val(Timer1.Tag) + 1
    If Val(Timer1.Tag) Mod 10 = 0 Then
        LoopAntrian
    End If
    If Timer1.Tag = "100" Then
        Timer1.Enabled = False
        Timer1.Tag = 1
    End If
End Sub
