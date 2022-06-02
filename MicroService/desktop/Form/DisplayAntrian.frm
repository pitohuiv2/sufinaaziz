VERSION 5.00
Object = "{6BF52A50-394A-11D3-B153-00C04F79FAA6}#1.0#0"; "wmp.dll"
Object = "{248DD890-BB45-11CF-9ABC-0080C7E7B78D}#1.0#0"; "MSWINSCK.OCX"
Begin VB.Form DisplayAntrian 
   BackColor       =   &H003A0512&
   BorderStyle     =   0  'None
   Caption         =   "runText"
   ClientHeight    =   11940
   ClientLeft      =   0
   ClientTop       =   90
   ClientWidth     =   20835
   BeginProperty Font 
      Name            =   "Tahoma"
      Size            =   15.75
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "DisplayAntrian.frx":0000
   LinkTopic       =   "Form1"
   Picture         =   "DisplayAntrian.frx":048A
   ScaleHeight     =   1080
   ScaleMode       =   0  'User
   ScaleWidth      =   1920
   ShowInTaskbar   =   0   'False
   Begin VB.Timer Timer4 
      Interval        =   1000
      Left            =   6720
      Top             =   240
   End
   Begin VB.Timer Timer3 
      Interval        =   100
      Left            =   6240
      Top             =   240
   End
   Begin VB.Timer Timer2 
      Enabled         =   0   'False
      Interval        =   1000
      Left            =   5760
      Top             =   240
   End
   Begin VB.Timer Timer1 
      Enabled         =   0   'False
      Interval        =   500
      Left            =   5280
      Top             =   240
   End
   Begin MSWinsockLib.Winsock WS1 
      Left            =   7320
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock WS2 
      Left            =   7800
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock WS3 
      Left            =   8280
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock WS4 
      Left            =   8760
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock WS5 
      Left            =   9240
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock WS6 
      Left            =   9720
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock ws7 
      Left            =   10200
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock ws8 
      Left            =   10680
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock ws9 
      Left            =   11160
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock ws10 
      Left            =   11640
      Top             =   240
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin VB.FileListBox File1 
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   1455
      Left            =   4920
      TabIndex        =   18
      Top             =   3960
      Visible         =   0   'False
      Width           =   2535
   End
   Begin VB.Image Image1 
      Height          =   1905
      Left            =   5280
      Picture         =   "DisplayAntrian.frx":2321E
      Top             =   1200
      Width           =   9990
   End
   Begin VB.Label runText 
      BackColor       =   &H00FFFFFF&
      Caption         =   ":: SELAMAT DATANG ::"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   15.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Left            =   0
      TabIndex        =   42
      Top             =   13245
      Width           =   20835
   End
   Begin VB.Image pic 
      Height          =   6135
      Left            =   5280
      Stretch         =   -1  'True
      Top             =   240
      Width           =   9983
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   5
      Left            =   15840
      TabIndex        =   35
      Top             =   960
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   6
      Left            =   15840
      TabIndex        =   36
      Top             =   3120
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   7
      Left            =   15840
      TabIndex        =   37
      Top             =   5280
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   8
      Left            =   15840
      TabIndex        =   38
      Top             =   7440
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   9
      Left            =   15840
      TabIndex        =   39
      Top             =   9840
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   0
      Left            =   600
      TabIndex        =   6
      Top             =   960
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   1
      Left            =   600
      TabIndex        =   7
      Top             =   3120
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   2
      Left            =   600
      TabIndex        =   8
      Top             =   5400
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   4
      Left            =   600
      TabIndex        =   17
      Top             =   9720
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H003F3400&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   60
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H000FCCF9&
      Height          =   1215
      Index           =   3
      Left            =   600
      TabIndex        =   9
      Top             =   7560
      Width           =   4095
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 4"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   3
      Left            =   1200
      TabIndex        =   4
      Top             =   6720
      Width           =   3015
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 3"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   2
      Left            =   1200
      TabIndex        =   3
      Top             =   4560
      Width           =   3015
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   3
      Left            =   600
      TabIndex        =   21
      Top             =   6765
      Width           =   4125
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   2
      Left            =   600
      TabIndex        =   19
      Top             =   4575
      Width           =   4125
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 2"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   1
      Left            =   1200
      TabIndex        =   2
      Top             =   2400
      Width           =   3015
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   1
      Left            =   600
      TabIndex        =   20
      Top             =   2400
      Width           =   4125
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 10"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   9
      Left            =   16200
      TabIndex        =   31
      Top             =   8895
      Width           =   3615
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   6
      Left            =   15840
      TabIndex        =   34
      Top             =   9000
      Width           =   4125
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 9"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   8
      Left            =   16440
      TabIndex        =   30
      Top             =   6720
      Width           =   3015
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   8
      Left            =   15840
      TabIndex        =   26
      Top             =   6778
      Width           =   4125
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 8"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   7
      Left            =   16440
      TabIndex        =   29
      Top             =   4560
      Width           =   3015
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   7
      Left            =   15840
      TabIndex        =   25
      Top             =   4617
      Width           =   4125
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 7"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   6
      Left            =   16440
      TabIndex        =   28
      Top             =   2400
      Width           =   3015
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   5
      Left            =   15840
      TabIndex        =   33
      Top             =   2425
      Width           =   4125
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 5"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   4
      Left            =   1200
      TabIndex        =   16
      Top             =   8880
      Width           =   3015
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   4
      Left            =   600
      TabIndex        =   22
      Top             =   8940
      Width           =   4125
   End
   Begin VB.Label lKanan 
      BackColor       =   &H003A0512&
      Height          =   255
      Left            =   20640
      TabIndex        =   41
      Top             =   0
      Visible         =   0   'False
      Width           =   255
   End
   Begin VB.Label lKiri 
      BackColor       =   &H003A0512&
      Height          =   255
      Left            =   0
      TabIndex        =   40
      Top             =   0
      Visible         =   0   'False
      Width           =   255
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   8
      Left            =   15840
      Picture         =   "DisplayAntrian.frx":23A67
      Top             =   7560
      Width           =   4125
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   9
      Left            =   15840
      Picture         =   "DisplayAntrian.frx":25AE5
      Top             =   9779
      Width           =   4125
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   6
      Left            =   15840
      Picture         =   "DisplayAntrian.frx":27B63
      Top             =   3203
      Width           =   4125
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   5
      Left            =   15840
      Picture         =   "DisplayAntrian.frx":29BE1
      Top             =   1011
      Width           =   4125
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 6"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   5
      Left            =   16440
      TabIndex        =   27
      Top             =   120
      Width           =   3015
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   10
      Left            =   15840
      TabIndex        =   32
      Top             =   240
      Width           =   4125
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   7
      Left            =   15840
      Picture         =   "DisplayAntrian.frx":2BC5F
      Top             =   5400
      Width           =   4125
   End
   Begin VB.Label lblWs 
      BackStyle       =   0  'Transparent
      Caption         =   "...."
      ForeColor       =   &H00FFFFFF&
      Height          =   375
      Left            =   5280
      TabIndex        =   24
      Top             =   -120
      Visible         =   0   'False
      Width           =   4215
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   4
      Left            =   600
      Picture         =   "DisplayAntrian.frx":2DCDD
      Top             =   9720
      Width           =   4125
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   2
      Left            =   600
      Picture         =   "DisplayAntrian.frx":2FD5B
      Top             =   5355
      Width           =   4125
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   1
      Left            =   600
      Picture         =   "DisplayAntrian.frx":31DD9
      Top             =   3165
      Width           =   4125
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   0
      Left            =   600
      Picture         =   "DisplayAntrian.frx":33E57
      Top             =   975
      Width           =   4125
   End
   Begin VB.Label Label2 
      Alignment       =   2  'Center
      BackStyle       =   0  'Transparent
      Caption         =   "LOKET 1"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   930
      Index           =   0
      Left            =   1200
      TabIndex        =   1
      Top             =   195
      Width           =   3015
   End
   Begin VB.Label Label7 
      BackColor       =   &H000FCCF9&
      Height          =   780
      Index           =   0
      Left            =   600
      TabIndex        =   0
      Top             =   240
      Width           =   4125
   End
   Begin WMPLibCtl.WindowsMediaPlayer WindowsMediaPlayer1 
      Height          =   4680
      Left            =   5280
      TabIndex        =   15
      Top             =   6499
      Width           =   9975
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
      stretchToFit    =   -1  'True
      windowlessVideo =   -1  'True
      enabled         =   -1  'True
      enableContextMenu=   -1  'True
      fullScreen      =   0   'False
      SAMIStyle       =   ""
      SAMILang        =   ""
      SAMIFilename    =   ""
      captioningID    =   ""
      enableErrorDialogs=   0   'False
      _cx             =   17595
      _cy             =   8255
   End
   Begin VB.Label lblpanggil2 
      Alignment       =   2  'Center
      BackColor       =   &H00FFE20B&
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   32.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   855
      Left            =   5280
      TabIndex        =   14
      Top             =   5520
      Width           =   9975
   End
   Begin VB.Label lblpanggil 
      Alignment       =   2  'Center
      BackColor       =   &H00FFE20B&
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   72
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   1725
      Left            =   5280
      TabIndex        =   13
      Top             =   3840
      Width           =   9975
   End
   Begin VB.Label Label14 
      Alignment       =   2  'Center
      BackColor       =   &H003A0512&
      Caption         =   "Antrian Terakhir"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   20.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H8000000B&
      Height          =   495
      Left            =   5280
      TabIndex        =   12
      Top             =   3360
      Width           =   9975
   End
   Begin VB.Label lblJam 
      Alignment       =   2  'Center
      BackColor       =   &H0027ACCD&
      Caption         =   "11:11:11"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   18
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   495
      Left            =   5280
      TabIndex        =   11
      Top             =   720
      Width           =   9983
   End
   Begin VB.Label Label5 
      Alignment       =   2  'Center
      BackColor       =   &H0027ACCD&
      Caption         =   "Monday, 10 Sept 2019"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   18
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   495
      Left            =   5280
      TabIndex        =   10
      Top             =   240
      Width           =   9983
   End
   Begin VB.Label Label1 
      BackColor       =   &H003A0512&
      Caption         =   "create by EA"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   9.75
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   315
      Left            =   6360
      TabIndex        =   5
      Top             =   9360
      Width           =   3495
   End
   Begin VB.Label lblconn 
      BackStyle       =   0  'Transparent
      Caption         =   "Label1"
      BeginProperty Font 
         Name            =   "MS Sans Serif"
         Size            =   8.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   1215
      Left            =   6120
      TabIndex        =   23
      Top             =   6840
      Visible         =   0   'False
      Width           =   3015
   End
   Begin VB.Image Image2 
      Height          =   1425
      Index           =   3
      Left            =   600
      Picture         =   "DisplayAntrian.frx":35ED5
      Top             =   7560
      Width           =   4125
   End
End
Attribute VB_Name = "DisplayAntrian"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Const SND_APPLICATION = &H80
'The sound is played using an application-specific association
Const SND_ALIAS = &H10000
'The pszSound parameter is a system-event alias in the registry or the WIN.INI file.
'Do not use with either SND_FILENAME or SND_RESOURCE.
Const SND_ALIAS_ID = &H110000
'The pszSound parameter is a predefined sound identifier.
Const SND_ASYNC = &H1
'The sound is played asynchronously and PlaySound returns immediately after beginning the sound. To terminate an asynchronously played waveform sound, call PlaySound with pszSound set to NULL.
Const SND_FILENAME = &H20000
'The pszSound parameter is a filename
Const SND_LOOP = &H8
'The sound plays repeatedly until PlaySound is called again with the pszSound parameter set to NULL. You must also specify the SND_ASYNC flag to indicate an asynchronous sound event
Const SND_MEMORY = &H4
'A sound event’s file is loaded in RAM. The parameter specified by pszSound must point to an image of a sound in memory.
Const SND_NODEFAULT = &H2
'No default sound event is used. If the sound cannot be found, PlaySound returns silently without playing the default sound.
Const SND_NOSTOP = &H10
'The specified sound event will yield to another sound event that is already playing. If a sound cannot be played because the resource needed to generate that sound is busy playing another sound, the function immediately returns FALSE without playing the requested sound. If this flag is not specified, PlaySound attempts to stop the currently playing sound so that the device can be used to play the new sound.
Const SND_NOWAIT = &H2000
'If the driver is busy, return immediately without playing the sound.
Const SND_PURGE = &H40
'Sounds are to be stopped for the calling task. If pszSound is not NULL, all instances of the specified sound are stopped. If pszSound is NULL, all sounds that are playing on behalf of the calling task are stopped. You must also specify the instance handle to stop SND_RESOURCE events.
Const SND_RESOURCE = &H40004 'The pszSound parameter is a resource identifier; hmod must identify the instance that contains the resource.
Const SND_SYNC = &H0
'Synchronous playback of a sound event. PlaySound returns after the sound event completes.

Private Declare Function sndPlaySound Lib "winmm.dll" Alias "sndPlaySoundA" (ByVal lpszSoundName As String, ByVal uFlags As Long) As Long
Private lngFormWidth As Long
Private lngFormHeight As Long
Private Declare Function waveOutGetNumDevs Lib "winmm.dll" () As Long
Dim tmt As Integer
Dim tmt2 As Integer
Dim tmt3 As Integer
Dim tmt4 As Integer
Dim onload As Boolean
Dim loket As Integer
Dim KedipLoket As Integer
Dim jenisAntrian As Integer
Dim TimeToRefresh As Integer
Dim vdeo As Integer
Dim reload As Boolean
Dim sora As Integer
Dim portanudigeroan As Integer



Private Sub File1_DblClick()
'    For i = 0 To File1.ListCount - 1
'        Debug.Print File1.List(i)
'    Next
End Sub

Private Sub Form_DblClick()
    'frmSetServer.Show
    End
End Sub

Private Sub Form_KeyDown(KeyCode As Integer, Shift As Integer)
'Debug.Print KeyCode
If KeyCode = 112 Then frmSetServer.Show: Unload Me
End Sub

Private Sub Form_Load()
On Error GoTo hell
    Dim posisi As String
    Dim Ctl As Control
    Dim screenwidth, screenheight As Single
     'Tempatkan dimensi form dalam variabel
    lngFormWidth = ScaleWidth
    lngFormHeight = ScaleHeight
    portanudigeroan = 1
    
    screenwidth = Screen.Width \ Screen.TwipsPerPixelX
    screenheight = Screen.Height \ Screen.TwipsPerPixelY
    
    posisi = GetTxt2("Setting.ini", "windows", "posisi")
    If posisi = "kanan" Then
        Me.Move Screen.Width, 0, Screen.Width, Screen.Height
    End If
    If posisi = "tengah" Then
        Me.Move 0, 0, Screen.Width, Screen.Height
    End If
    lblconn.Caption = dbConn
    Timer1.Enabled = True
    For i = 0 To 9
        lbl(i).Caption = ""
    Next
    File1.Path = App.Path & "\video"
    tmt3 = 10
    tmt = 100
    onload = True
    Label1.Caption = App.Path
    tmt3 = 60
'    Frame1.Visible = False
    
    If SKiri = 1 Then
        lKiri.Visible = True
    Else
        lKiri.Visible = False
    End If
    
    If SKanan = 1 Then
        lKanan.Visible = True
    Else
        lKanan.Visible = False
    End If
    
    sora = GetTxt2("Setting.ini", "Volume", "Video")
    WindowsMediaPlayer1.URL = App.Path & "\video\" & File1.List(0)
    WindowsMediaPlayer1.settings.Volume = sora
    WindowsMediaPlayer1.settings.setMode "loop", True
    reload = True
    
    Call OpenPortWinsock
    Call loadAntrian
    Exit Sub
    
hell:
End Sub

Private Sub LoadTrigger()
Dim A1, A2, B1, B2, C1, C2, D1, D2 As String

    A1 = GetTxt2("Setting.ini", "DisplayTrigger_1", "IP")
    B1 = GetTxt2("Setting.ini", "DisplayTrigger_2", "IP")
    C1 = GetTxt2("Setting.ini", "DisplayTrigger_3", "IP")
    D1 = GetTxt2("Setting.ini", "DisplayTrigger_4", "IP")
    
    A2 = GetTxt2("Setting.ini", "DisplayTrigger_1", "PORT")
    B2 = GetTxt2("Setting.ini", "DisplayTrigger_2", "PORT")
    C2 = GetTxt2("Setting.ini", "DisplayTrigger_3", "PORT")
    D2 = GetTxt2("Setting.ini", "DisplayTrigger_4", "PORT")
    
    If A1 <> "" And A2 <> "" Then
        If wst1.State <> sckClosed Then wst1.Close
        wst1.Connect A1, A2
    End If
    
    If B1 <> "" And B2 <> "" Then
        If wst2.State <> sckClosed Then wst2.Close
        wst2.Connect B1, B2
    End If
    
    
    If C1 <> "" And C2 <> "" Then
        If wst3.State <> sckClosed Then wst3.Close
        wst3.Connect C1, C2
    End If
    
    
    If D1 <> "" And D2 <> "" Then
        If wst4.State <> sckClosed Then wst4.Close
        wst4.Connect D1, D2
    End If
End Sub

Private Sub Label1_DblClick()
'    WindowsMediaPlayer1.Controls.Next
End Sub

Private Sub Label2_Click(Index As Integer)
    frmSettingKoneksi.Show
End Sub

Private Sub lblJam_DblClick()
    frmSettingKoneksi.Show
End Sub

Private Sub Timer1_Timer()
On Error GoTo ea
    tmt = tmt + 1
    tmt4 = tmt4 + 1
    If tmt4 > 5 Then
        Call loadAntrian
        'tmt = 0
        tmt4 = 0
    End If
    If tmt > 100 Then
        Timer1.Enabled = False
        tmt = 0
        reload = True
        lblWs.Visible = False
        Call OpenPortWinsock
    End If
ea:
End Sub

Private Sub OpenPortWinsock()
'    Timer1.Enabled = False
    If ws1.State <> 0 Then ws1.Close
    ws1.LocalPort = 2001
    ws1.Listen
    
    If WS2.State <> 0 Then WS2.Close
    WS2.LocalPort = 2002
    WS2.Listen
    
    If WS3.State <> 0 Then WS3.Close
    WS3.LocalPort = 2003
    WS3.Listen
    
    If WS4.State <> 0 Then WS4.Close
    WS4.LocalPort = 2004
    WS4.Listen

    If WS5.State <> 0 Then WS5.Close
    WS5.LocalPort = 2005
    WS5.Listen

    If WS6.State <> 0 Then WS6.Close
    WS6.LocalPort = 2006
    WS6.Listen
    
     If ws7.State <> 0 Then ws7.Close
    ws7.LocalPort = 2007
    ws7.Listen
    
     If ws8.State <> 0 Then ws8.Close
    ws8.LocalPort = 2008
    ws8.Listen
    
     If ws9.State <> 0 Then ws9.Close
    ws9.LocalPort = 2009
    ws9.Listen
    
     If ws10.State <> 0 Then ws10.Close
    ws10.LocalPort = 2010
    ws10.Listen
End Sub

Private Sub ClosePortWinsock()
'    Timer1.Enabled = False
    
    If ws1.State <> 0 Then ws1.Close
    'WS1.LocalPort = 2001
    'WS1.Listen
    
    If WS2.State <> 0 Then WS2.Close
    'WS2.LocalPort = 2002
    'WS2.Listen
    
    If WS3.State <> 0 Then WS3.Close
    'WS3.LocalPort = 2003
    'WS3.Listen
    
    If WS4.State <> 0 Then WS4.Close
    'WS4.LocalPort = 2004
    'WS4.Listen

    If WS5.State <> 0 Then WS5.Close
    'WS5.LocalPort = 2005
    'WS5.Listen

    If WS6.State <> 0 Then WS6.Close
    
    If ws7.State <> 0 Then ws7.Close
    If ws8.State <> 0 Then ws8.Close
    If ws9.State <> 0 Then ws9.Close
    If ws10.State <> 0 Then ws10.Close
    'WS6.LocalPort = 2006
    'WS6.Listen
    
End Sub


Private Sub ws1_ConnectionRequest(ByVal requestID As Long)
    If ws1.State <> sckClosed Then
        ws1.Close
    End If
    lblWs.Visible = True
    ws1.Accept requestID
    ws1.SendData "OK"
    portanudigeroan = 1
    
    Call ClosePortWinsock
    'WS1.Close
'    WS1.LocalPort = 2001
'    WS1.Listen
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub

Private Sub ws2_ConnectionRequest(ByVal requestID As Long)
    If WS2.State <> sckClosed Then
        WS2.Close
    End If
    lblWs.Visible = True
    WS2.Accept requestID
    WS2.SendData "OK"
    portanudigeroan = 2
    
    Call ClosePortWinsock
    'WS2.Close
'    WS2.LocalPort = 2002
'    WS2.Listen
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub

Private Sub ws3_ConnectionRequest(ByVal requestID As Long)
    If WS3.State <> sckClosed Then
        WS3.Close
    End If
    lblWs.Visible = True
    WS3.Accept requestID
    WS3.SendData "OK"
    portanudigeroan = 3
    
    Call ClosePortWinsock
'    WS3.Close
'    WS3.LocalPort = 2003
'    WS3.Listen
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub

Private Sub ws4_ConnectionRequest(ByVal requestID As Long)
    If WS4.State <> sckClosed Then
        WS4.Close
    End If
    lblWs.Visible = True
    WS4.Accept requestID
    WS4.SendData "OK"
    portanudigeroan = 4
    
    Call ClosePortWinsock
'    WS4.Close
'    WS4.LocalPort = 2004
'    WS4.Listen
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub

Private Sub ws5_ConnectionRequest(ByVal requestID As Long)
    If WS5.State <> sckClosed Then
        WS5.Close
    End If
    lblWs.Visible = True
    WS5.Accept requestID
    WS5.SendData "OK"
    portanudigeroan = 5
    
    Call ClosePortWinsock
'    WS5.Close
'    WS5.LocalPort = 2005
'    WS5.Listen
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub

Private Sub ws6_ConnectionRequest(ByVal requestID As Long)
    If WS6.State <> sckClosed Then
        WS6.Close
    End If
    lblWs.Visible = True
    WS6.Accept requestID
    WS6.SendData "OK"
    portanudigeroan = 6
    
    Call ClosePortWinsock
'    WS6.Close
'    WS6.LocalPort = 2006
'    WS6.Listen
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub

Private Sub ws7_ConnectionRequest(ByVal requestID As Long)
    If ws7.State <> sckClosed Then
        ws7.Close
    End If
    ws7.Accept requestID
    ws7.SendData "OK"
    portanudigeroan = 7
    
    Call ClosePortWinsock
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub
Private Sub ws8_ConnectionRequest(ByVal requestID As Long)
    If ws8.State <> sckClosed Then
        ws8.Close
    End If
    ws8.Accept requestID
    ws8.SendData "OK"
    portanudigeroan = 8
    
    Call ClosePortWinsock
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub
Private Sub ws9_ConnectionRequest(ByVal requestID As Long)
    If ws9.State <> sckClosed Then
        ws9.Close
    End If
    ws9.Accept requestID
    ws9.SendData "OK"
    portanudigeroan = 9
    
    Call ClosePortWinsock
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub
Private Sub ws10_ConnectionRequest(ByVal requestID As Long)
    If ws10.State <> sckClosed Then
        ws10.Close
    End If
    ws10.Accept requestID
    ws10.SendData "OK"
    portanudigeroan = 10
    
    Call ClosePortWinsock
    
    lblWs.Tag = "1"
    If reload = False Then Exit Sub
    Call loadAntrian
    Call LoadTrigger
End Sub

Private Sub lblconn_DblClick()
    End
End Sub

Private Sub loadAntrian()
'On Error Resume Next
Dim disada As Boolean
    
    If reload = False Then Exit Sub
    lblWs.Visible = True

    strSQL = "select* from perjanjianpasientr  where statuspanggil ='1' and tanggalreservasi between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "'"
    Call ReadRs(strSQL)
    For i = 0 To 9
        lbl(i).ForeColor = vbBlue
'        lbl(i).BackColor = &H3F3400
    Next
    
    Timer2.Enabled = True
    If RS.RecordCount <> 0 Then
        RS.MoveFirst
        For i = 0 To RS.RecordCount - 1
            If RS!tempatlahir = 1 Then
                If lbl(0).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(0).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 1
            End If
            If RS!tempatlahir = 2 Then
                If lbl(1).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(1).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 2
            End If
            If RS!tempatlahir = 3 Then
                If lbl(2).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(2).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 3
            End If
            If RS!tempatlahir = 4 Then
                If lbl(3).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(3).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 4
            End If
            If RS!tempatlahir = 5 Then
                If lbl(4).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(4).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 5
            End If
            If RS!tempatlahir = 6 Then
                If lbl(5).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(5).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 6
            End If
            If RS!tempatlahir = 7 Then
                If lbl(6).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(6).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 7
            End If
            If RS!tempatlahir = 8 Then
                If lbl(7).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(7).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 8
            End If
            If RS!tempatlahir = 9 Then
                If lbl(8).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(8).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 9
            End If
            If RS!tempatlahir = 10 Then
                If lbl(9).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                lbl(9).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
                loket = 10
            End If
            
                If disada = True Then Call PlaySound(RS!noantrian, UCase(RS!jenis))
            disada = False
            RS.MoveNext
        Next
    End If
    onload = False
    If Timer1.Enabled = False Then Timer1.Enabled = True
'    If CN.State = adStateOpen Then CN.Close
End Sub

Private Sub PlaySound(angka As Integer, jenis As String)
Dim t As Single
Dim belas As Boolean
Dim puluh As Boolean
Dim ratus As Boolean

    If onload = True Then Exit Sub
    lblpanggil = jenis & "-" & Format(angka, "0##")
    lblpanggil2 = "Loket " & loket
'    lbl(loket - 1).BackColor = &H8080FF
    lbl(loket - 1).ForeColor = &H0
'    lbl(loket - 1).BackStyle = 1
    Timer2.Enabled = True
    Call sndPlaySound(App.Path & "\new_sound\nomor-urut.wav", SND_ASYNC Or SND_NODEFAULT)
    
    t = Timer
    Do
        DoEvents
    Loop Until Timer - t > 2
    
    If jenis = "A" Then Call sndPlaySound(App.Path & "\new_sound\A.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "B" Then Call sndPlaySound(App.Path & "\new_sound\B.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "C" Then Call sndPlaySound(App.Path & "\new_sound\C.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "D" Then Call sndPlaySound(App.Path & "\new_sound\D.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "E" Then Call sndPlaySound(App.Path & "\new_sound\E.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "F" Then Call sndPlaySound(App.Path & "\new_sound\F.wav", SND_ALIAS Or SND_SYNC)
    If jenis = "G" Then Call sndPlaySound(App.Path & "\new_sound\G.wav", SND_ALIAS Or SND_SYNC)
    
'    If jenis = "A" Then Call sndPlaySound(App.Path & "\sound\A.mp3", SND_ALIAS Or SND_SYNC)
'    If jenis = "B" Then Call sndPlaySound(App.Path & "\sound\B.mp3", SND_ALIAS Or SND_SYNC)
'    If jenis = "C" Then Call sndPlaySound(App.Path & "\sound\C.mp3", SND_ALIAS Or SND_SYNC)
'    If jenis = "D" Then Call sndPlaySound(App.Path & "\sound\D.mp3", SND_ALIAS Or SND_SYNC)
'    If jenis = "E" Then Call sndPlaySound(App.Path & "\sound\E.mp3", SND_ALIAS Or SND_SYNC)
    

'    t = Timer
'    Do
'        DoEvents
'    Loop Until Timer - t > 1
    
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
            'If Val(Mid(angka, 2, 2)) < 20 And Val(Mid(angka, 2, 2)) > 11 Then belas = True:
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
        

'        If ratus = False And angka > 200 Then
'            If Val(Mid(angka, 2, 2)) < 20 And Val(Mid(angka, 2, 2)) > 11 Then belas = True
'        End If

        If belas = True Then Call sndPlaySound(App.Path & "\new_sound\belas.wav", SND_ALIAS Or SND_SYNC)
        If puluh = True Then Call sndPlaySound(App.Path & "\new_sound\puluh.wav", SND_ALIAS Or SND_SYNC) ': puluh = False
        If angka > 19 And angka < 100 Then puluh = False
        If ratus = True Then Call sndPlaySound(App.Path & "\new_sound\ratus.wav", SND_ALIAS Or SND_SYNC): ratus = False
belas:
    Next
    
    
'    Select Case angka
'        Case 1
'            Call sndPlaySound(App.Path & "\sound\satu.wav", SND_ALIAS Or SND_SYNC)
'        Case 2
'            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
'        Case 3
'            Call sndPlaySound(App.Path & "\sound\tiga.wav", SND_ALIAS Or SND_SYNC)
'        Case 4
'            Call sndPlaySound(App.Path & "\sound\empat.wav", SND_ALIAS Or SND_SYNC)
'        Case 5
'            Call sndPlaySound(App.Path & "\sound\lima.wav", SND_ALIAS Or SND_SYNC)
'        Case 6
'            Call sndPlaySound(App.Path & "\sound\enam.wav", SND_ALIAS Or SND_SYNC)
'        Case 7
'            Call sndPlaySound(App.Path & "\sound\tujuh.wav", SND_ALIAS Or SND_SYNC)
'        Case 8
'            Call sndPlaySound(App.Path & "\sound\delapan.wav", SND_ALIAS Or SND_SYNC)
'        Case 9
'            Call sndPlaySound(App.Path & "\sound\sembilan.wav", SND_ALIAS Or SND_SYNC)
'        Case 10
'            Call sndPlaySound(App.Path & "\sound\sepuluh.wav", SND_ALIAS Or SND_SYNC)
'        Case 11
'            Call sndPlaySound(App.Path & "\sound\sebelas.wav", SND_ALIAS Or SND_SYNC)
'        Case 12
'            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
'            Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
'        Case 13
'            Call sndPlaySound(App.Path & "\sound\tiga.wav", SND_ALIAS Or SND_SYNC)
'            Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
'        Case 14
'            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
'            Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
'        Case 15
'            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
'            Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
'        Case 16
'            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
'            Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
'        Case 17
'            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
'            Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
'        Case 18
'            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
'            Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
'        Case 19
'            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
'            Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
'        Case 20
'            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
'            Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
'     End Select
     
     
'1000-turun.wav
'100-turun.wav
'10-turun.wav
'11-turun.wav
'anak.wav
'Antrian.wav
'antrian2.wav
'belas.wav
'belas -turun.wav
'dalam -turun.wav
'delapan.wav
'dua.wav
'empat.wav
'enam.wav
'ke poli.wav
'kosong.wav
'lima.wav
'loket.wav
'LoketPendaftaran.wav
'nol.wav
'nomor -urut.wav
'puluh.wav
'puluh -turun.wav
'ratus.wav
'ratus -turun.wav
'ribu -turun.wav
'Satu.wav
'se.wav
'sebelas.wav
'sembilan.wav
'sepuluh.wav
'seratus.wav
'seribu.wav
'tiga.wav
'tujuh.wav
   
    
    
    
'    t = Timer
'    Do
'        DoEvents
'    Loop Until Timer - t > 1
loketttt:
    Call sndPlaySound(App.Path & "\sound\loket.wav", SND_ASYNC Or SND_NODEFAULT)
    
    t = Timer
    Do
        DoEvents
    Loop Until Timer - t > 1
    Select Case loket
        Case 1
            Call sndPlaySound(App.Path & "\sound\satu.wav", SND_ALIAS Or SND_SYNC)
        Case 2
            Call sndPlaySound(App.Path & "\sound\dua.wav", SND_ALIAS Or SND_SYNC)
        Case 3
            Call sndPlaySound(App.Path & "\sound\tiga.wav", SND_ALIAS Or SND_SYNC)
        Case 4
            Call sndPlaySound(App.Path & "\sound\empat.wav", SND_ALIAS Or SND_SYNC)
        Case 5
            Call sndPlaySound(App.Path & "\sound\lima.wav", SND_ALIAS Or SND_SYNC)
        Case 6
            Call sndPlaySound(App.Path & "\sound\enam.wav", SND_ALIAS Or SND_SYNC)
        Case 7
            Call sndPlaySound(App.Path & "\sound\tujuh.wav", SND_ALIAS Or SND_SYNC)
        Case 8
            Call sndPlaySound(App.Path & "\sound\delapan.wav", SND_ALIAS Or SND_SYNC)
        Case 9
            Call sndPlaySound(App.Path & "\sound\sembilan.wav", SND_ALIAS Or SND_SYNC)
        Case 10
            Call sndPlaySound(App.Path & "\sound\sepuluh.wav", SND_ALIAS Or SND_SYNC)
    End Select
End Sub

Private Sub Timer2_Timer()
On Error GoTo as_epic
    lbl(KedipLoket).FontBold = Not lbl(KedipLoket).FontBold
    tmt2 = tmt2 + 1
    If tmt2 > 10 Then
        Timer2.Enabled = False
        For i = 0 To 9
'            lbl(i).BackColor = &H8000000F
            lbl(i).BackStyle = 0
        Next
        tmt2 = 0
        lblWs.Visible = False
'        Frame1.Visible = False
    End If
as_epic:
End Sub

Private Sub Timer3_Timer()
    If runText.Left < 0 - runText.Width Then runText.Left = 1368 'Screen.Width
    runText.Move runText.Left - 30
    
End Sub

Private Sub Timer4_Timer()
    On Error GoTo Error_Handler

    lblJam.Caption = Format(Now(), "hh:nn:ss")
    Label5.Caption = Format(Now(), "dddd, dd MMM yyyy")
    
    If InStr(1, Label5.Caption, "Monday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Monday", "Senin")
    If InStr(1, Label5.Caption, "Tuesday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Tuesday", "Selasa")
    If InStr(1, Label5.Caption, "Wednesday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Wednesday", "Rabu")
    If InStr(1, Label5.Caption, "Thursday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Thursday", "Kamis")
    If InStr(1, Label5.Caption, "Friday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Friday", "Juma'ah")
    If InStr(1, Label5.Caption, "Saturday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Saturday", "Sabtu")
    If InStr(1, Label5.Caption, "Sunday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Sunday", "Minggu")
    
    
    
'    If WindowsMediaPlayer1.Controls.currentPosition + 20 > WindowsMediaPlayer1.currentMedia.duration Then
'        vdeo = vdeo + 1
'        If vdeo > File1.ListCount - 1 Then vdeo = 0
'        If File1.List(vdeo) <> "Thumbs.db" Then
'            WindowsMediaPlayer1.URL = App.Path & "\video\" & File1.List(vdeo)
'        End If
'    End If
'    Label1.Caption = WindowsMediaPlayer1.Controls.currentPosition
Error_Handler:
End Sub
