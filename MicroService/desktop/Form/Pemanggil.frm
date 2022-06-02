VERSION 5.00
Object = "{248DD890-BB45-11CF-9ABC-0080C7E7B78D}#1.0#0"; "MSWINSCK.OCX"
Begin VB.Form frmPemanggil 
   BackColor       =   &H00805736&
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Pemanggil Antrian"
   ClientHeight    =   6090
   ClientLeft      =   45
   ClientTop       =   390
   ClientWidth     =   5595
   FillColor       =   &H003A0512&
   Icon            =   "Pemanggil.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   ScaleHeight     =   6090
   ScaleWidth      =   5595
   StartUpPosition =   3  'Windows Default
   Begin VB.Frame Frame1 
      BackColor       =   &H00805736&
      Caption         =   "Informasi"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   3375
      Left            =   120
      TabIndex        =   13
      Top             =   2280
      Width           =   5415
      Begin VB.TextBox A1 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   1440
         TabIndex        =   37
         Text            =   "A"
         Top             =   720
         Width           =   735
      End
      Begin VB.TextBox A2 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   2160
         TabIndex        =   36
         Text            =   "0"
         Top             =   720
         Width           =   1575
      End
      Begin VB.TextBox A4 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   3720
         TabIndex        =   35
         Text            =   "0"
         Top             =   720
         Width           =   855
      End
      Begin VB.TextBox B1 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   1440
         TabIndex        =   34
         Text            =   "B"
         Top             =   1080
         Width           =   735
      End
      Begin VB.TextBox B2 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   2160
         TabIndex        =   33
         Text            =   "0"
         Top             =   1080
         Width           =   1575
      End
      Begin VB.TextBox B4 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   3720
         TabIndex        =   32
         Text            =   "0"
         Top             =   1080
         Width           =   855
      End
      Begin VB.TextBox C1 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   1440
         TabIndex        =   31
         Text            =   "C"
         Top             =   1440
         Width           =   735
      End
      Begin VB.TextBox C2 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   2160
         TabIndex        =   30
         Text            =   "0"
         Top             =   1440
         Width           =   1575
      End
      Begin VB.TextBox C4 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   3720
         TabIndex        =   29
         Text            =   "0"
         Top             =   1440
         Width           =   855
      End
      Begin VB.TextBox Text14 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BackColor       =   &H00F8E00A&
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         ForeColor       =   &H00000000&
         Height          =   390
         Left            =   1440
         Locked          =   -1  'True
         TabIndex        =   28
         Text            =   "Jenis"
         Top             =   360
         Width           =   735
      End
      Begin VB.TextBox Text15 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BackColor       =   &H00F8E00A&
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         ForeColor       =   &H00000000&
         Height          =   390
         Left            =   2160
         Locked          =   -1  'True
         TabIndex        =   27
         Text            =   "Sekarang"
         Top             =   360
         Width           =   1575
      End
      Begin VB.TextBox Text16 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BackColor       =   &H00F8E00A&
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   700
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         ForeColor       =   &H00000000&
         Height          =   390
         Left            =   3720
         Locked          =   -1  'True
         TabIndex        =   26
         Text            =   "Sisa"
         Top             =   360
         Width           =   855
      End
      Begin VB.TextBox D1 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   1440
         TabIndex        =   25
         Text            =   "D"
         Top             =   1800
         Width           =   735
      End
      Begin VB.TextBox D2 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   2160
         TabIndex        =   24
         Text            =   "0"
         Top             =   1800
         Width           =   1575
      End
      Begin VB.TextBox D4 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   3720
         TabIndex        =   23
         Text            =   "0"
         Top             =   1800
         Width           =   855
      End
      Begin VB.TextBox E4 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   3720
         TabIndex        =   22
         Text            =   "0"
         Top             =   2160
         Width           =   855
      End
      Begin VB.TextBox E2 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   2160
         TabIndex        =   21
         Text            =   "0"
         Top             =   2160
         Width           =   1575
      End
      Begin VB.TextBox E1 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   1440
         TabIndex        =   20
         Text            =   "E"
         Top             =   2160
         Width           =   735
      End
      Begin VB.TextBox F1 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   1440
         TabIndex        =   19
         Text            =   "F"
         Top             =   2520
         Width           =   735
      End
      Begin VB.TextBox F2 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   2160
         TabIndex        =   18
         Text            =   "0"
         Top             =   2520
         Width           =   1575
      End
      Begin VB.TextBox F4 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   3720
         TabIndex        =   17
         Text            =   "0"
         Top             =   2520
         Width           =   855
      End
      Begin VB.TextBox G1 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   1440
         TabIndex        =   16
         Text            =   "G"
         Top             =   2880
         Width           =   735
      End
      Begin VB.TextBox G2 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   2160
         TabIndex        =   15
         Text            =   "0"
         Top             =   2880
         Width           =   1575
      End
      Begin VB.TextBox G4 
         Alignment       =   2  'Center
         Appearance      =   0  'Flat
         BeginProperty Font 
            Name            =   "Tahoma"
            Size            =   9.75
            Charset         =   0
            Weight          =   400
            Underline       =   0   'False
            Italic          =   0   'False
            Strikethrough   =   0   'False
         EndProperty
         Height          =   390
         Left            =   3720
         TabIndex        =   14
         Text            =   "0"
         Top             =   2880
         Width           =   855
      End
   End
   Begin VB.CommandButton Command2 
      BackColor       =   &H00F8E00A&
      Caption         =   "Panggil Ulang"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   12
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Left            =   3600
      TabIndex        =   12
      Top             =   1680
      Width           =   1815
   End
   Begin VB.CommandButton Command1 
      BackColor       =   &H000FCCF9&
      Caption         =   "Panggil"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   12
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Left            =   1560
      MaskColor       =   &H000FCCF9&
      TabIndex        =   11
      Top             =   1680
      Width           =   1935
   End
   Begin VB.CommandButton Command3 
      Caption         =   ".."
      Height          =   270
      Left            =   5280
      TabIndex        =   9
      Top             =   5760
      Width           =   255
   End
   Begin VB.TextBox Text5 
      Appearance      =   0  'Flat
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Left            =   1560
      TabIndex        =   8
      Top             =   1080
      Width           =   3855
   End
   Begin VB.TextBox Text2 
      Appearance      =   0  'Flat
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Left            =   3600
      TabIndex        =   7
      Text            =   "A"
      Top             =   360
      Width           =   1815
   End
   Begin VB.TextBox Text1 
      Appearance      =   0  'Flat
      Enabled         =   0   'False
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Left            =   1560
      TabIndex        =   6
      Text            =   "1"
      Top             =   360
      Width           =   735
   End
   Begin VB.Timer Timer1 
      Interval        =   1000
      Left            =   80
      Top             =   0
   End
   Begin MSWinsockLib.Winsock ws1 
      Left            =   600
      Top             =   0
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
   End
   Begin VB.TextBox Text3 
      Height          =   495
      Left            =   4320
      TabIndex        =   39
      Top             =   3120
      Width           =   1215
   End
   Begin VB.TextBox Text4 
      Height          =   495
      Left            =   4320
      TabIndex        =   38
      Top             =   3720
      Width           =   1215
   End
   Begin VB.CheckBox Check1 
      BackColor       =   &H00805736&
      Caption         =   "Always be On Top "
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   9
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   -1  'True
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   270
      Left            =   120
      TabIndex        =   10
      Top             =   5400
      Width           =   3255
   End
   Begin VB.Label Label6 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      BackStyle       =   0  'Transparent
      Caption         =   ":"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   375
      Left            =   1200
      TabIndex        =   5
      Top             =   1080
      Width           =   495
   End
   Begin VB.Label Label5 
      BackColor       =   &H00FFFFFF&
      BackStyle       =   0  'Transparent
      Caption         =   "Panggil "
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   495
      Left            =   120
      TabIndex        =   4
      Top             =   1080
      Width           =   1455
   End
   Begin VB.Label Label4 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      BackStyle       =   0  'Transparent
      Caption         =   ":"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   375
      Left            =   3240
      TabIndex        =   3
      Top             =   360
      Width           =   495
   End
   Begin VB.Label Label3 
      BackColor       =   &H00FFFFFF&
      BackStyle       =   0  'Transparent
      Caption         =   "Jenis"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   375
      Left            =   2400
      TabIndex        =   2
      Top             =   360
      Width           =   1215
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      BackStyle       =   0  'Transparent
      Caption         =   ":"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   375
      Left            =   1200
      TabIndex        =   1
      Top             =   360
      Width           =   495
   End
   Begin VB.Label Label1 
      BackColor       =   &H00FFFFFF&
      BackStyle       =   0  'Transparent
      Caption         =   "Loket"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   375
      Left            =   120
      TabIndex        =   0
      Top             =   360
      Width           =   1215
   End
End
Attribute VB_Name = "frmPemanggil"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Dim timtimer As Double

Private Sub Check1_Click()
    If Check1.Value = 1 Then
        Call SetWindowPos(frmPemanggil.hwnd, HWND_TOPMOST, 0, 0, 0, 0, SWP_NOMOVE Or SWP_NOSIZE)
    Else
        Call SetWindowPos(frmPemanggil.hwnd, HWND_NOTOPMOST, 0, 0, 0, 0, SWP_NOMOVE Or SWP_NOSIZE)
    End If
    
End Sub

Private Sub Command1_Click()
If WS1.State <> sckClosed Then WS1.Close
    WS1.Connect Text4.Text, Text3.Text
    If Text5.Text = "" Then
        strSQL = "select norec, noantrian from perjanjianpasientr where  " & _
                 "statuspanggil ='0' and " & _
                 "jenis ='" & Text2.Text & "' and " & _
                 "tanggalreservasi between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "' " & _
                 "order by tanggalreservasi"
    Else
        strSQL = "select norec, noantrian from perjanjianpasientr where  " & _
                 "statuspanggil ='0' and " & _
                 "noantrian ='" & Text5.Text & "' and " & _
                 "jenis ='" & Text2.Text & "' and " & _
                 "tanggalreservasi between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "' " & _
                 "order by tanggalreservasi"
    End If
    Call ReadRs(strSQL)
    If RS.RecordCount <> 0 Then
        RS.MoveFirst
'       rss.MoveNext
        strSQL2 = "update perjanjianpasientr set statuspanggil ='2' where statuspanggil='1' and tempatlahir ='" & Text1.Text & "'"
'        Call SaveJson2(strSQL2)
        Call ReadRs2(strSQL2)
        strSQL3 = "update perjanjianpasientr set statuspanggil ='1',tempatlahir='" & Text1.Text & "' where norec='" & RS!Norec & "'"
'        Call SaveJson2(strSQL3)
        Call ReadRs3(strSQL3)
    Else
        MsgBox "Antrian habis!", vbInformation, "..:."
    End If
    Call infotainment
End Sub

Private Sub Command2_Click()
    If WS1.State <> sckClosed Then WS1.Close
    WS1.Connect Text4.Text, Text3.Text
    
    strSQL = "select norec, noantrian from perjanjianpasientr where  " & _
            "noantrian ='" & Text5.Text & "' and " & _
            "jenis ='" & Text2.Text & "' and " & _
            "tanggalreservasi between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "' " & _
            "order by tanggalreservasi"
    Call ReadRs(strSQL)
'    Call ReadJson(strSQL)
    If RS.RecordCount <> 0 Then
        RS.MoveFirst
        
        strSQL2 = "update perjanjianpasientr set statuspanggil ='2' where statuspanggil='1' and tempatlahir ='" & Text1.Text & "'"
        Call ReadRs2(strSQL2)
        strSQL3 = "update perjanjianpasientr set statuspanggil ='1',tempatlahir='" & Text1.Text & "' where norec='" & RS!Norec & "'"
        Call ReadRs2(strSQL3)
    Else
        MsgBox "Tidak ada antrian!", vbInformation, "..:."
    End If
    Call infotainment
End Sub

Private Sub Command3_Click()
    frmSettingKoneksi.Show vbModal
End Sub

Private Sub Command4_Click()
     If WS1.State <> sckClosed Then WS1.Close
        WS1.Connect Text4.Text, Text3.Text
        
        strSQL = "select norec, noantrian from perjanjianpasientr where  " & _
                "noantrian ='" & Text5.Text & "' and " & _
                "jenis ='" & Text2.Text & "' and " & _
                "tanggalreservasi between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "' " & _
                "order by tanggalreservasi"
        Call ReadRs(strSQL)
        If RS.RecordCount <> 0 Then
            RS.MoveFirst
           strSQL2 = "update perjanjianpasientr set statuspanggil ='2' where statuspanggil='1' and tempatlahir ='" & Text1.Text & "'"
    '        Call SaveJson2(strSQL2)
           Call ReadRs2(strSQL2)
           strSQL3 = "update perjanjianpasientr set statuspanggil ='1',tempatlahir='" & Text1.Text & "' where norec='" & RS!Norec & "'"
    '        Call SaveJson2(strSQL3)
           Call ReadRs4(strSQL3)
        Else
            MsgBox "Tidak ada antrian!", vbInformation, "..:."
        End If
    Call infotainment
End Sub

Private Sub Form_Load()
On Error Resume Next
    Call SetWindowPos(frmPemanggil.hwnd, HWND_TOPMOST, 0, 0, 0, 0, SWP_NOMOVE Or SWP_NOSIZE)
    Text4.Text = GetTxt2("Setting.ini", "Caller", "ipdisplay")
    Text3.Text = GetTxt2("Setting.ini", "Caller", "portcaller")
    Text1.Text = GetTxt2("Setting.ini", "Caller", "loket")
    Text2.Text = GetTxt2("Setting.ini", "Caller", "Jenis")
    Call openConnection
    Call infotainment
End Sub


Private Sub infotainment()
    On Error Resume Next
    timtimer = 1
    strSQL = "select jenis,max(noantrian) AS last from perjanjianpasientr " & _
             "where statuspanggil = '1' AND tanggalreservasi between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "' " & _
             "group by jenis order by jenis "
'    Call ReadJson(strSQL)
    ReadRs strSQL
    strSQL2 = "select jenis, count(noantrian) as sisa from perjanjianpasientr  " & _
             "where statuspanggil = '0' " & _
             "and tanggalreservasi between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "' " & _
             "GROUP BY jenis order by jenis"
'    Call ReadJson2(strSQL2)
    ReadRs2 strSQL2
'    A2 = 0
'    A3 = 0
'    A4 = 0
'
'    B2 = 0
'    B3 = 0
'    B4 = 0
'
'    C2 = 0
'    c3 = 0
'    C4 = 0
'
'    D2 = 0
'    D3 = 0
'    D4 = 0
'
'
'    E2 = 0
'    E4 = 0
'
'    F2 = 0
'    F4 = 0
'
'    G2 = 0
'    G4 = 0
    For i = 0 To RS.RecordCount - 1
        If RS!jenis = A1 Then
            A2 = RS!Last
        End If
        If RS!jenis = B1 Then
            B2 = RS!Last
        End If
        If RS!jenis = c1 Then
            c2 = RS!Last
        End If
        If RS!jenis = D1 Then
            D2 = RS!Last
        End If
        If RS!jenis = E1 Then
            E2 = RS!Last
        End If
        If RS!jenis = F1 Then
            F2 = RS!Last
        End If
        If RS!jenis = G1 Then
            G2 = RS!Last
        End If
        A3 = Val(A2) + 1
        B3 = Val(B2) + 1
        c3 = Val(c2) + 1
        D3 = Val(D2) + 1
        RS.MoveNext
    Next
    For i = 0 To RS2.RecordCount - 1
        If RS2!jenis = A1 Then
            A4 = RS2!sisa
        End If
        If RS2!jenis = B1 Then
            B4 = RS2!sisa
        End If
        If RS2!jenis = c1 Then
            c4 = RS2!sisa
        End If
        If RS2!jenis = D1 Then
            D4 = RS2!sisa
        End If
        If RS2!jenis = E1 Then
            E4 = RS2!sisa
        End If
        If RS2!jenis = F1.Text Then
            F4 = RS2!sisa
        End If
        If RS2!jenis = G1.Text Then
            G4 = RS2!sisa
        End If
        RS2.MoveNext
    Next

        
ea:
End Sub

Private Sub Text2_Change()
    Call infotainment
End Sub

Private Sub Timer1_Timer()
    timtimer = timtimer + 1
    If timtimer = 10 Then
        Call infotainment
        timtimer = 1
    End If
End Sub


