VERSION 5.00
Object = "{6BF52A50-394A-11D3-B153-00C04F79FAA6}#1.0#0"; "wmp.dll"
Object = "{248DD890-BB45-11CF-9ABC-0080C7E7B78D}#1.0#0"; "MSWINSCK.OCX"
Object = "{5E9E78A0-531B-11CF-91F6-C2863C385E30}#1.0#0"; "MSFLXGRD.OCX"
Begin VB.Form DisplayFarmasi 
   BorderStyle     =   0  'None
   Caption         =   "&H00FFFFFF&"
   ClientHeight    =   11520
   ClientLeft      =   0
   ClientTop       =   0
   ClientWidth     =   20490
   BeginProperty Font 
      Name            =   "Tahoma"
      Size            =   15.75
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   LinkTopic       =   "Form1"
   Picture         =   "DisplayFarmasi.frx":0000
   ScaleHeight     =   546.133
   ScaleMode       =   0  'User
   ScaleWidth      =   971.852
   ShowInTaskbar   =   0   'False
   StartUpPosition =   3  'Windows Default
   Begin MSFlexGridLib.MSFlexGrid MSFlexGrid1 
      Height          =   5250
      Left            =   12480
      TabIndex        =   44
      Top             =   20520
      Width           =   14700
      _ExtentX        =   25929
      _ExtentY        =   9260
      _Version        =   393216
      Appearance      =   0
      BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
         Name            =   "Tahoma"
         Size            =   14.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
   End
   Begin MSWinsockLib.Winsock wst4 
      Left            =   8520
      Top             =   5040
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
   End
   Begin MSWinsockLib.Winsock wst3 
      Left            =   8040
      Top             =   5040
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
   End
   Begin MSWinsockLib.Winsock wst2 
      Left            =   7680
      Top             =   6600
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
   End
   Begin MSWinsockLib.Winsock wst1 
      Left            =   7200
      Top             =   6600
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
   End
   Begin MSWinsockLib.Winsock WS1 
      Left            =   720
      Top             =   9840
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin VB.Timer Timer4 
      Interval        =   1000
      Left            =   1200
      Top             =   7080
   End
   Begin VB.Timer Timer3 
      Interval        =   100
      Left            =   840
      Top             =   7080
   End
   Begin VB.Timer Timer2 
      Enabled         =   0   'False
      Interval        =   1000
      Left            =   840
      Top             =   3600
   End
   Begin VB.Timer Timer1 
      Enabled         =   0   'False
      Interval        =   500
      Left            =   480
      Top             =   3600
   End
   Begin MSWinsockLib.Winsock WS2 
      Left            =   1200
      Top             =   9840
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock WS3 
      Left            =   1680
      Top             =   9840
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock WS4 
      Left            =   2160
      Top             =   9840
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock WS5 
      Left            =   3120
      Top             =   9840
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock WS6 
      Left            =   5040
      Top             =   9840
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock ws7 
      Left            =   3600
      Top             =   9840
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock ws8 
      Left            =   4080
      Top             =   9840
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock ws9 
      Left            =   4560
      Top             =   9840
      _ExtentX        =   741
      _ExtentY        =   741
      _Version        =   393216
      RemotePort      =   1000
      LocalPort       =   1000
   End
   Begin MSWinsockLib.Winsock ws10 
      Left            =   2640
      Top             =   9840
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
      Left            =   7200
      TabIndex        =   6
      Top             =   8640
      Visible         =   0   'False
      Width           =   2535
   End
   Begin MSFlexGridLib.MSFlexGrid MSFlexGrid2 
      Height          =   7440
      Left            =   11235
      TabIndex        =   45
      Top             =   2760
      Width           =   8850
      _ExtentX        =   15610
      _ExtentY        =   13123
      _Version        =   393216
      Appearance      =   0
      BeginProperty Font {0BE35203-8F91-11CE-9DE3-00AA004BB851} 
         Name            =   "Tahoma"
         Size            =   14.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
   End
   Begin VB.Label Label11 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      Caption         =   "AS--"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   27.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00000000&
      Height          =   1500
      Left            =   1200
      TabIndex        =   43
      Top             =   24240
      Width           =   9975
   End
   Begin VB.Label Label10 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      Caption         =   "R--"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   72
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   3000
      Left            =   1200
      TabIndex        =   42
      Top             =   21240
      Width           =   9975
   End
   Begin VB.Label Label9 
      Alignment       =   2  'Center
      BackColor       =   &H00000000&
      Caption         =   "Racikan"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   26.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   750
      Left            =   1200
      TabIndex        =   41
      Top             =   20520
      Width           =   9975
   End
   Begin VB.Label Label8 
      Alignment       =   2  'Center
      BackColor       =   &H00000000&
      Caption         =   "Antrian Farmasi"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   26.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   750
      Left            =   480
      TabIndex        =   40
      Top             =   2760
      Width           =   10542
   End
   Begin VB.Label lblJam 
      Alignment       =   2  'Center
      BackColor       =   &H00000000&
      Caption         =   "11:11:11"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   50.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   1350
      Left            =   12480
      TabIndex        =   38
      Top             =   17400
      Width           =   14700
   End
   Begin VB.Label lblpanggil2 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      Caption         =   "AS--"
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
      Height          =   1800
      Left            =   480
      TabIndex        =   37
      Top             =   8400
      Width           =   10542
   End
   Begin VB.Label lblpanggil 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      Caption         =   "F--"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   72
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   4957
      Left            =   480
      TabIndex        =   36
      Top             =   3480
      Width           =   10542
   End
   Begin VB.Label Label5 
      Alignment       =   2  'Center
      BackColor       =   &H00000000&
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
      Height          =   975
      Left            =   480
      TabIndex        =   35
      Top             =   480
      Width           =   19713
   End
   Begin VB.Label Label7 
      Alignment       =   2  'Center
      BackColor       =   &H00000000&
      Caption         =   "FARMASI"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   36
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00FFFFFF&
      Height          =   975
      Left            =   480
      TabIndex        =   34
      Top             =   1440
      Width           =   19710
   End
   Begin WMPLibCtl.WindowsMediaPlayer WindowsMediaPlayer1 
      Height          =   7560
      Left            =   9240
      TabIndex        =   33
      Top             =   17640
      Visible         =   0   'False
      Width           =   8700
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
      _cx             =   15346
      _cy             =   13335
   End
   Begin VB.Label Label15 
      BackStyle       =   0  'Transparent
      Caption         =   "@epicteam"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   9.75
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   -1  'True
         Strikethrough   =   0   'False
      EndProperty
      Height          =   255
      Left            =   27720
      TabIndex        =   32
      Top             =   0
      Width           =   1335
   End
   Begin VB.Label lbl2 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      BackStyle       =   0  'Transparent
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   35.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   1215
      Index           =   0
      Left            =   480
      TabIndex        =   7
      Top             =   15840
      Width           =   7695
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
      Height          =   975
      Index           =   10
      Left            =   20400
      TabIndex        =   30
      Top             =   16200
      Width           =   4095
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
      Height          =   975
      Index           =   4
      Left            =   16320
      TabIndex        =   14
      Top             =   16080
      Width           =   4095
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H008080FF&
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
      ForeColor       =   &H00FFFFFF&
      Height          =   1215
      Index           =   6
      Left            =   24480
      TabIndex        =   26
      Top             =   16080
      Width           =   4455
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H008080FF&
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
      ForeColor       =   &H00FFFFFF&
      Height          =   1215
      Index           =   5
      Left            =   20400
      TabIndex        =   15
      Top             =   16080
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
      ForeColor       =   &H00FFFFFF&
      Height          =   1215
      Index           =   0
      Left            =   0
      TabIndex        =   0
      Top             =   16920
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
      ForeColor       =   &H00FFFFFF&
      Height          =   1215
      Index           =   1
      Left            =   4080
      TabIndex        =   1
      Top             =   16920
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
      ForeColor       =   &H00FFFFFF&
      Height          =   1215
      Index           =   2
      Left            =   8160
      TabIndex        =   2
      Top             =   16920
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
      ForeColor       =   &H00FFFFFF&
      Height          =   1215
      Index           =   3
      Left            =   12240
      TabIndex        =   3
      Top             =   16920
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
      ForeColor       =   &H00FFFFFF&
      Height          =   1215
      Index           =   4
      Left            =   16320
      TabIndex        =   4
      Top             =   16920
      Width           =   4095
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
      Height          =   975
      Index           =   1
      Left            =   4200
      TabIndex        =   11
      Top             =   16080
      Width           =   3855
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
      Height          =   975
      Index           =   2
      Left            =   8160
      TabIndex        =   12
      Top             =   16080
      Width           =   3855
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
      Height          =   975
      Index           =   3
      Left            =   12240
      TabIndex        =   13
      Top             =   16080
      Width           =   3735
   End
   Begin VB.Label runText 
      BackColor       =   &H00FFFFFF&
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
      Left            =   28560
      TabIndex        =   16
      Top             =   15660
      Width           =   39960
   End
   Begin VB.Label Label4 
      BackColor       =   &H00FFFFFF&
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
      TabIndex        =   29
      Top             =   11460
      Width           =   28860
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H008080FF&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   50.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   1215
      Index           =   7
      Left            =   15600
      TabIndex        =   25
      Top             =   4920
      Width           =   5055
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H008080FF&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   50.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   1215
      Index           =   8
      Left            =   14520
      TabIndex        =   24
      Top             =   7320
      Width           =   5055
   End
   Begin VB.Label lbl 
      Alignment       =   2  'Center
      BackColor       =   &H008080FF&
      BackStyle       =   0  'Transparent
      Caption         =   "1.999"
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   50.25
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   1215
      Index           =   9
      Left            =   14760
      TabIndex        =   23
      Top             =   7200
      Width           =   5055
   End
   Begin VB.Label Label2 
      BackStyle       =   0  'Transparent
      Caption         =   "Loket 7 :"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   24.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Index           =   9
      Left            =   15360
      TabIndex        =   22
      Top             =   6000
      Visible         =   0   'False
      Width           =   2655
   End
   Begin VB.Label Label2 
      BackStyle       =   0  'Transparent
      Caption         =   "Loket 8 :"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   24.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Index           =   8
      Left            =   16920
      TabIndex        =   21
      Top             =   5280
      Visible         =   0   'False
      Width           =   2655
   End
   Begin VB.Label Label2 
      BackStyle       =   0  'Transparent
      Caption         =   "Loket 9 :"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   24.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Index           =   7
      Left            =   17400
      TabIndex        =   20
      Top             =   7440
      Visible         =   0   'False
      Width           =   2655
   End
   Begin VB.Label Label2 
      BackStyle       =   0  'Transparent
      Caption         =   "Loket 10 :"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   24.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Index           =   6
      Left            =   16680
      TabIndex        =   19
      Top             =   7920
      Visible         =   0   'False
      Width           =   2655
   End
   Begin VB.Label lblWs 
      BackStyle       =   0  'Transparent
      Caption         =   "...."
      Height          =   495
      Left            =   -7680
      TabIndex        =   17
      Top             =   10680
      Visible         =   0   'False
      Width           =   4215
   End
   Begin VB.Image pic 
      Height          =   9495
      Left            =   1560
      Stretch         =   -1  'True
      Top             =   12840
      Width           =   11415
   End
   Begin VB.Label lbl2 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      BackStyle       =   0  'Transparent
      BeginProperty Font 
         Name            =   "Trebuchet MS"
         Size            =   35.25
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   1215
      Index           =   1
      Left            =   10560
      TabIndex        =   8
      Top             =   13680
      Width           =   8055
   End
   Begin VB.Label Label3 
      BackStyle       =   0  'Transparent
      Caption         =   "Label3"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   12
         Charset         =   0
         Weight          =   400
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      ForeColor       =   &H00C0C0C0&
      Height          =   495
      Left            =   15480
      TabIndex        =   18
      Top             =   7560
      Width           =   3975
   End
   Begin VB.Label lblconn 
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
      Left            =   16200
      TabIndex        =   5
      Top             =   6960
      Visible         =   0   'False
      Width           =   3015
   End
   Begin VB.Label lKiri 
      BackColor       =   &H00404040&
      Height          =   255
      Left            =   28920
      TabIndex        =   28
      Top             =   0
      Visible         =   0   'False
      Width           =   255
   End
   Begin VB.Label lKanan 
      BackColor       =   &H00404040&
      Height          =   255
      Left            =   28920
      TabIndex        =   27
      Top             =   120
      Visible         =   0   'False
      Width           =   255
   End
   Begin VB.Image Image7 
      Height          =   1950
      Left            =   20400
      Top             =   15720
      Width           =   4500
   End
   Begin VB.Image Image6 
      Height          =   1950
      Left            =   16320
      Top             =   15720
      Width           =   4500
   End
   Begin VB.Image Image5 
      Height          =   1950
      Left            =   12240
      Top             =   16680
      Width           =   4500
   End
   Begin VB.Image Image4 
      Height          =   1950
      Left            =   8160
      Top             =   16200
      Width           =   4500
   End
   Begin VB.Image Image3 
      Height          =   1950
      Left            =   4080
      Top             =   17160
      Width           =   4500
   End
   Begin VB.Image Image2 
      Height          =   1950
      Left            =   0
      Top             =   17040
      Width           =   4500
   End
   Begin VB.Image Image8 
      Height          =   1950
      Left            =   24480
      Top             =   15720
      Width           =   4500
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
      Height          =   975
      Index           =   0
      Left            =   120
      TabIndex        =   10
      Top             =   16080
      Width           =   3975
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
      Height          =   735
      Index           =   5
      Left            =   24480
      TabIndex        =   31
      Top             =   16200
      Width           =   4095
   End
   Begin VB.Label Label1 
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
      Height          =   255
      Left            =   2280
      TabIndex        =   9
      Top             =   5040
      Width           =   5295
   End
   Begin VB.Label Label6 
      Alignment       =   2  'Center
      BackColor       =   &H00FFFFFF&
      Caption         =   "--"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   99.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   2775
      Left            =   -2760
      TabIndex        =   39
      Top             =   9960
      Visible         =   0   'False
      Width           =   2655
   End
   Begin VB.Image Image1 
      Height          =   2235
      Left            =   1200
      Top             =   480
      Width           =   9825
   End
End
Attribute VB_Name = "DisplayFarmasi"
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
'A sound event�s file is loaded in RAM. The parameter specified by pszSound must point to an image of a sound in memory.
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
Dim jenisantri As String
Dim igrid As Integer



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
'On Error GoTo hell
    Dim posisi As String
    portanudigeroan = 1
    
    posisi = GetTxt("Setting.ini", "windows", "posisi")
    Label7 = "FARMASI" 'GetTxt("Setting.ini", "nama", "ruangan")
'    jenisantri = GetTxt("Setting.ini", "nama", "jenis")
    Label6 = jenisantri
    If posisi = "kanan" Then
        Me.Move Screen.Width, 0, Screen.Width, Screen.Height
    End If
    If posisi = "center" Then
        Me.Move 0, 0, Screen.Width, Screen.Height
    End If
    lblconn.Caption = dbConn
    Timer1.Enabled = True
    For I = 0 To 9
        lbl(I).Caption = ""
    Next
    File1.Path = App.Path & "\video"
    tmt3 = 10
    tmt = 100
'    onload = True
    Label1.Caption = App.Path
    tmt3 = 60
'    Frame1.Visible = False

    SKanan = 1
    SKiri = 1
    SKanan = GetTxt("Setting.ini", "SuaraAntrian", "Kanan")
    SKiri = GetTxt("Setting.ini", "SuaraAntrian", "Kiri")
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
'    If GetSetting("Antrian", "Video", "suara") = "ON" Then
'        sora = 70
'    Else
'        sora = 0
'    End If
    
    sora = 0 'GetTxt("Setting.ini", "Volume", "Video")
    

'    ##DIRECT SHOW
'    DS_top = GetSetting("Antrian", "Video", "top") '0  '136
'    DS_left = GetSetting("Antrian", "Video", "left") '312
'    DS_width = GetSetting("Antrian", "Video", "width") '761
'    DS_height = GetSetting("Antrian", "Video", "height") '633
'    Fullscreen_Enabled = False
'    vdeo = 0
'    DirectShow_Load_Media App.Path & "\video\" & File1.List(0)
'    DirectShow_Play
'    DirectShow_Volume sora
'    pic.Visible = False
'   ##END DIRECT SHOW

    WindowsMediaPlayer1.URL = App.Path & "\video\" & File1.List(0)
    WindowsMediaPlayer1.settings.Volume = sora
'   WindowsMediaPlayer1.settings.mute = False
'   WindowsMediaPlayer1.URL = App.Path & "\tst.wpl"
    WindowsMediaPlayer1.settings.setMode "loop", True
    
    Call OpenPortWinsock
    onload = False
'    Call loadAntrian
    
    '@IPComputer,@Port,@Loket,@StatusEnabled
'    strSQL = "delete from AntrianIpPort where Loket='1'"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "delete from AntrianIpPort where Loket='2'"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "delete from AntrianIpPort where Loket='3'"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "delete from AntrianIpPort where Loket='4'"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "delete from AntrianIpPort where Loket='5'"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "delete from AntrianIpPort where Loket='6'"
'    Call msubRecFO(rs, strSQL)
'
'    strSQL = "insert into AntrianIpPort values ('" & WS1.LocalIP & "','2001','1','1')"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "insert into AntrianIpPort values ('" & WS1.LocalIP & "','2002','2','1')"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "insert into AntrianIpPort values ('" & WS1.LocalIP & "','2003','3','1')"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "insert into AntrianIpPort values ('" & WS1.LocalIP & "','2004','4','1')"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "insert into AntrianIpPort values ('" & WS1.LocalIP & "','2005','5','1')"
'    Call msubRecFO(rs, strSQL)
'    strSQL = "insert into AntrianIpPort values ('" & WS1.LocalIP & "','2006','6','1')"
'    Call msubRecFO(rs, strSQL)
'    MsgBox "3"
    Call loadantrianfarmasi
    
    Exit Sub
    
hell:
'    frmSettingKoneksi.Show
End Sub
Public Sub loadantrianfarmasi()
    igrid = 0
    'Set Profile RS
    Call openConnection
    
'    With MSFlexGrid1
'        .Cols = 4
'        .Rows = 1
'        .ColWidth(0) = 100
'        .ColWidth(1) = 2400
'        .ColWidth(2) = 4700
'        .ColWidth(3) = 2000
'        .TextMatrix(0, 1) = "No Antri"
'        .TextMatrix(0, 2) = "Nama Pasien"
'        .TextMatrix(0, 3) = "Status"
'    End With
    
    With MSFlexGrid2
        .Cols = 4
        .Rows = 1
        .ColWidth(0) = 100
        .ColWidth(1) = 2400
        .ColWidth(2) = 4700
        .ColWidth(3) = 2000
        .TextMatrix(0, 1) = "No Antri"
        .TextMatrix(0, 2) = "Nama Pasien"
        .TextMatrix(0, 3) = "Status"
    End With
strSQL = "select * from antrianapotiktr " & _
            "where tglresep between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "' " & _
            "and status in ('2','3','4','5') order by noantri"
    Call ReadRs(strSQL)
    Dim statusS As String
    If RS.RecordCount <> 0 Then
        For I = 0 To RS.RecordCount - 1
            If RS!Status = 2 Then statusS = "VERIFIKASI"
            If RS!Status = 3 Then statusS = "PRODUKSI"
            If RS!Status = 4 Then statusS = "PACKAGING"
            If RS!Status = 5 Then statusS = "SELESAI"
'            If RS!jenis = "R" Then
'                MSFlexGrid1.Rows = MSFlexGrid1.Rows + 1
'                MSFlexGrid1.TextMatrix(MSFlexGrid1.Rows - 1, 1) = "R-" & RS!NoAntri
'                MSFlexGrid1.TextMatrix(MSFlexGrid1.Rows - 1, 2) = RS!keterangan
'                MSFlexGrid1.TextMatrix(MSFlexGrid1.Rows - 1, 3) = statusS
'                MSFlexGrid1.ColAlignment(1) = vbCenter
'                MSFlexGrid1.ColAlignment(2) = vbAlignRight
'            Else
                MSFlexGrid2.Rows = MSFlexGrid2.Rows + 1
                MSFlexGrid2.TextMatrix(MSFlexGrid2.Rows - 1, 1) = "F-" & RS!NoAntri
                MSFlexGrid2.TextMatrix(MSFlexGrid2.Rows - 1, 2) = RS!keterangan
                MSFlexGrid2.TextMatrix(MSFlexGrid2.Rows - 1, 3) = statusS
                MSFlexGrid2.ColAlignment(1) = vbCenter
                MSFlexGrid2.ColAlignment(2) = vbAlignRight
'            End If
            RS.MoveNext
        Next
    End If
End Sub


Private Sub LoadTrigger()
Dim a1, a2, b1, b2, c1, c2, d1, d2 As String

    a1 = GetTxt("Setting.ini", "DisplayTrigger_1", "IP")
    b1 = GetTxt("Setting.ini", "DisplayTrigger_2", "IP")
    c1 = GetTxt("Setting.ini", "DisplayTrigger_3", "IP")
    d1 = GetTxt("Setting.ini", "DisplayTrigger_4", "IP")
    
    a2 = GetTxt("Setting.ini", "DisplayTrigger_1", "PORT")
    b2 = GetTxt("Setting.ini", "DisplayTrigger_2", "PORT")
    c2 = GetTxt("Setting.ini", "DisplayTrigger_3", "PORT")
    d2 = GetTxt("Setting.ini", "DisplayTrigger_4", "PORT")
    
    If a1 <> "" And a2 <> "" Then
        If wst1.State <> sckClosed Then wst1.Close
        wst1.Connect a1, a2
    End If
    
    If b1 <> "" And b2 <> "" Then
        If wst2.State <> sckClosed Then wst2.Close
        wst2.Connect b1, b2
    End If
    
    
    If c1 <> "" And c2 <> "" Then
        If wst3.State <> sckClosed Then wst3.Close
        wst3.Connect c1, c2
    End If
    
    
    If d1 <> "" And d2 <> "" Then
        If wst4.State <> sckClosed Then wst4.Close
        wst4.Connect d1, d2
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
On Error GoTo asepic
    tmt = tmt + 1
    tmt4 = tmt4 + 1
    If tmt4 > 5 Then
    'If Val(Format(Now(), "ss")) Mod 5 = 0 Then
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
'    lblJam.Caption = Format(Now(), "hh:nn:ss")
'    If Val(Format(Now(), "ss")) Mod 10 = 0 Then
'        'pic.Picture = File1.Path & "\File1.Tag"
'        pic.Picture = LoadPicture(File1.Path & "\" & File1.List(Val(File1.Tag)))
'        File1.Tag = Val(File1.Tag) + 1
'        If Val(File1.Tag) > File1.ListCount - 1 Then File1.Tag = 0
'    End If
'    On Error GoTo Error_Handler
'    Label3.Caption = DirectShow_Position.CurrentPosition & "/" & DirectShow_Position.StopTime
'    If DirectShow_Position.CurrentPosition >= DirectShow_Position.StopTime Then
'            'DirectShow_Position.CurrentPosition = 0
'        vdeo = vdeo + 1
'        If vdeo > File1.ListCount - 1 Then vdeo = 0
'        DirectShow_Load_Media App.Path & "\video\" & File1.List(vdeo)
''    DirectShow_Loop
'        DirectShow_Play
'        DirectShow_Volume 0
'    End If
asepic:
End Sub

Private Sub OpenPortWinsock()
'    Timer1.Enabled = False
    
    If WS1.State <> 0 Then WS1.Close
    WS1.LocalPort = 2001
    WS1.Listen
    
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
    
    If WS1.State <> 0 Then WS1.Close
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
    If WS1.State <> sckClosed Then
        WS1.Close
    End If
'    lblWs.Visible = True
    WS1.Accept requestID
    WS1.SendData "OK"
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
'    lblWs.Visible = True
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
'    lblWs.Visible = True
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
'    lblWs.Visible = True
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
'    lblWs.Visible = True
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
'    lblWs.Visible = True
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
On Error Resume Next
Dim disada As Boolean
    
    If CN.State = adStateClosed Then CN.Open
'    If reload = False Then Exit Sub
'    reload = True
    lblWs.Visible = True
    'Set RS = Nothing
    strSQL = "select* from antrianapotiktr where statuspanggil ='1' and tglresep between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "'"
    Call ReadRs(strSQL)
    For I = 0 To 6
        lbl(I).ForeColor = vbWhite
        lbl(I).BackColor = &H3F3400
    Next
'    Frame1.Visible = True
    
    Timer2.Enabled = True
    If RS.RecordCount <> 0 Then
        RS.MoveFirst
        For I = 0 To RS.RecordCount - 1
'            If rs!JenisPasien = "BPJS" Then
'                jenisAntrian = 1
'            Else
'                jenisAntrian = 2
'            End If
            If RS!tempatlahir = 1 Then
                If lbl(0).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(0).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(0).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 1 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 1"
'                End If
                loket = 1
            End If
            If RS!tempatlahir = 2 Then
                If lbl(1).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(1).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(1).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 2 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 2"
'                End If
                loket = 2
            End If
            If RS!tempatlahir = 3 Then
                If lbl(2).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(2).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(2).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 3 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 3"
'                End If
                loket = 3
            End If
            If RS!tempatlahir = 4 Then
                If lbl(3).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(3).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(3).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 4 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 4"
'                End If
                loket = 4
            End If
            If RS!tempatlahir = 5 Then
                If lbl(4).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(4).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(4).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 5 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 5"
'                End If
                loket = 5
            End If
            If RS!tempatlahir = 6 Then
                If lbl(5).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(4).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(5).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 6 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 6"
'                End If
                loket = 6
            End If
            If RS!tempatlahir = 7 Then
                If lbl(6).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(4).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(6).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 7 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 7"
'                End If
                loket = 7
            End If
            If RS!tempatlahir = 8 Then
                If lbl(7).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(4).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(7).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 8 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 8"
'                End If
                loket = 8
            End If
            If RS!tempatlahir = 9 Then
                If lbl(8).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(4).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(8).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 9 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 9"
'                End If
                loket = 9
            End If
            If RS!tempatlahir = 10 Then
                If lbl(9).Caption <> RS!jenis & "-" & Format(RS!noantrian, "0##") Then disada = True
                'lbl(4).Caption = rs!JenisPasien & " : " & Format(rs!NoAntrian, "0##")
                lbl(9).Caption = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                If portanudigeroan = 10 Then
'                    lblpanggil = RS!jenis & "-" & Format(RS!noantrian, "0##")
'                    lblpanggil2 = "Loket 10"
'                End If
                loket = 10
            End If
            
            'If RS!tempatlahir >= 7 And SKiri = 1 Then
                If disada = True Then Call PlaySound(RS!noantrian, UCase(RS!jenis))
            'End If
            'If RS!tempatlahir <= 6 And SKanan = 1 Then
             '   If disada = True Then Call PlaySound(RS!noantrian, UCase(RS!jenis))
            'End If
            disada = False
            RS.MoveNext
        Next
    End If
    'select * from antrianapotik_t;
    
    
    strSQL = "select * from antrianapotiktr " & _
            "where tglresep between '" & Format(Now(), "yyyy-mm-dd 00:00") & "' and '" & Format(Now(), "yyyy-mm-dd 23:59") & "'" & _
            "and status=5"
    Call ReadRs(strSQL)
    If RS.RecordCount <> 0 Then
        For I = 0 To RS.RecordCount - 1
'            If RS!jenis = "R" Then
'                MSFlexGrid1.Rows = MSFlexGrid1.Rows + 1
'                MSFlexGrid1.TextMatrix(MSFlexGrid1.Rows - 1, 1) = "R-" & RS!NoAntri
'                MSFlexGrid1.TextMatrix(MSFlexGrid1.Rows - 1, 2) = RS!keterangan
'                MSFlexGrid1.ColAlignment(1) = vbCenter
'                MSFlexGrid1.ColAlignment(2) = vbAlignRight
'            Else
                MSFlexGrid2.Rows = MSFlexGrid2.Rows + 1
                MSFlexGrid2.TextMatrix(MSFlexGrid2.Rows - 1, 1) = "F-" & RS!NoAntri
                MSFlexGrid2.TextMatrix(MSFlexGrid2.Rows - 1, 2) = RS!keterangan
                MSFlexGrid2.ColAlignment(1) = vbCenter
                MSFlexGrid2.ColAlignment(2) = vbAlignRight
'            End If
            RS.MoveNext
        Next
    End If
    
    onload = False
    If Timer1.Enabled = False Then Timer1.Enabled = True
    If CN.State = adStateOpen Then CN.Close
End Sub

Public Sub PlaySound(angka As Integer, jenis As String)
Dim t As Single
Dim belas As Boolean
Dim puluh As Boolean
Dim ratus As Boolean

Dim suaraantrian As String
    
'    suaraantrian = GetTxt("Setting.ini", "nama", "suaraantrian")
'    If suaraantrian = "off" Then Exit Sub
    If onload = True Then Exit Sub
'    lblpanggil = jenis & "-" & Format(angka, "0##")
'    lblpanggil2 = "Loket " & loket
'    lbl(loket - 1).BackColor = &H8080FF
'    lbl(loket - 1).ForeColor = &H0
'    lbl(loket - 1).BackStyle = 1
'    Timer2.Enabled = True
    Timer2.Enabled = True
    Call sndPlaySound(App.Path & "\sound\bel.wav", SND_ASYNC Or SND_NODEFAULT)
    
    t = Timer
    Do
        DoEvents
    Loop Until Timer - t > 2
    Call sndPlaySound(App.Path & "\sound\nomor-urut.wav", SND_ASYNC Or SND_NODEFAULT)
    
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
'    If jenis = "G" Then Call sndPlaySound(App.Path & "\sound\G.wav", SND_ALIAS Or SND_SYNC)
'    If jenis = "H" Then Call sndPlaySound(App.Path & "\sound\H.wav", SND_ALIAS Or SND_SYNC)
'    If jenis = "I" Then Call sndPlaySound(App.Path & "\sound\I.wav", SND_ALIAS Or SND_SYNC)
'    If jenis = "J" Then Call sndPlaySound(App.Path & "\sound\J.wav", SND_ALIAS Or SND_SYNC)
'    If jenis = "K" Then Call sndPlaySound(App.Path & "\sound\K.wav", SND_ALIAS Or SND_SYNC)
'    If jenis = "L" Then Call sndPlaySound(App.Path & "\sound\L.wav", SND_ALIAS Or SND_SYNC)
'    If jenis = "N" Then Call sndPlaySound(App.Path & "\sound\N.wav", SND_ALIAS Or SND_SYNC)
'    If jenis = "R" Then Call sndPlaySound(App.Path & "\sound\R.wav", SND_ALIAS Or SND_SYNC)
    
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
    If angka > 99 And angka < 200 Then Call sndPlaySound(App.Path & "\sound\seratus.wav", SND_ALIAS Or SND_SYNC): angka = angka - 100
    If angka > 19 And angka < 100 Then puluh = True
    
    If angka < 20 And angka > 11 Then angka = angka - 10: belas = True
    
    If Len(CStr(angka)) = 2 And angka = 10 Then Call sndPlaySound(App.Path & "\sound\sepuluh.wav", SND_ALIAS Or SND_SYNC): GoTo loketttt
    If Len(CStr(angka)) = 2 And angka = 11 Then Call sndPlaySound(App.Path & "\sound\sebelas.wav", SND_ALIAS Or SND_SYNC): GoTo loketttt
    If Len(CStr(angka)) = 3 And angka = 100 Then Call sndPlaySound(App.Path & "\sound\seratus.wav", SND_ALIAS Or SND_SYNC): GoTo loketttt
    If Len(CStr(angka)) = 4 And angka = 1000 Then Call sndPlaySound(App.Path & "\sound\seribu.wav", SND_ALIAS Or SND_SYNC): GoTo loketttt
    
    For I = 1 To Len(CStr(angka))
        If ratus = False And angka > 200 And Val(Mid(angka, 2, 2)) = 10 Then Call sndPlaySound(App.Path & "\sound\sepuluh.wav", SND_ALIAS Or SND_SYNC): Exit For
        If ratus = False And angka > 200 And Val(Mid(angka, 2, 2)) = 11 Then Call sndPlaySound(App.Path & "\sound\sebelas.wav", SND_ALIAS Or SND_SYNC): Exit For
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
                    Call sndPlaySound(App.Path & "\sound\2.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 13
                    Call sndPlaySound(App.Path & "\sound\3.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 14
                    Call sndPlaySound(App.Path & "\sound\4.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 15
                    Call sndPlaySound(App.Path & "\sound\5.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 16
                    Call sndPlaySound(App.Path & "\sound\6.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 17
                    Call sndPlaySound(App.Path & "\sound\7.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 18
                    Call sndPlaySound(App.Path & "\sound\8.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
                Case 19
                    Call sndPlaySound(App.Path & "\sound\9.wav", SND_ALIAS Or SND_SYNC)
                    Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
            End Select
            Exit For
        End If
        Select Case Mid(CStr(angka), I, 1)
           Case 1
               Call sndPlaySound(App.Path & "\sound\1.wav", SND_ALIAS Or SND_SYNC)
           Case 2
               Call sndPlaySound(App.Path & "\sound\2.wav", SND_ALIAS Or SND_SYNC)
           Case 3
               Call sndPlaySound(App.Path & "\sound\3.wav", SND_ALIAS Or SND_SYNC)
           Case 4
               Call sndPlaySound(App.Path & "\sound\4.wav", SND_ALIAS Or SND_SYNC)
           Case 5
               Call sndPlaySound(App.Path & "\sound\5.wav", SND_ALIAS Or SND_SYNC)
           Case 6
               Call sndPlaySound(App.Path & "\sound\6.wav", SND_ALIAS Or SND_SYNC)
           Case 7
               Call sndPlaySound(App.Path & "\sound\7.wav", SND_ALIAS Or SND_SYNC)
           Case 8
               Call sndPlaySound(App.Path & "\sound\8.wav", SND_ALIAS Or SND_SYNC)
           Case 9
               Call sndPlaySound(App.Path & "\sound\9.wav", SND_ALIAS Or SND_SYNC)
        End Select
        

'        If ratus = False And angka > 200 Then
'            If Val(Mid(angka, 2, 2)) < 20 And Val(Mid(angka, 2, 2)) > 11 Then belas = True
'        End If

        If belas = True Then Call sndPlaySound(App.Path & "\sound\belas.wav", SND_ALIAS Or SND_SYNC)
        If puluh = True Then Call sndPlaySound(App.Path & "\sound\puluh.wav", SND_ALIAS Or SND_SYNC) ': puluh = False
        If angka > 19 And angka < 100 Then puluh = False
        If ratus = True Then Call sndPlaySound(App.Path & "\sound\ratus.wav", SND_ALIAS Or SND_SYNC): ratus = False
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
'    Call sndPlaySound(App.Path & "\sound\loket.wav", SND_ASYNC Or SND_NODEFAULT)
    
    t = Timer
    Do
        DoEvents
    Loop Until Timer - t > 1
'    Select Case loket
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
'    End Select
End Sub

Private Sub Timer2_Timer()
On Error GoTo as_epic
    lbl(KedipLoket).FontBold = Not lbl(KedipLoket).FontBold
    tmt2 = tmt2 + 1
    If tmt2 > 10 Then
        Timer2.Enabled = False
        For I = 0 To 9
            lbl(I).BackColor = &H8000000F
            lbl(I).BackStyle = 0
        Next
        tmt2 = 0
        lblWs.Visible = False
'        Frame1.Visible = False
    End If
as_epic:
End Sub

Private Sub Timer3_Timer()
    If runText.Left < 0 - runText.Width Then runText.Left = 1768 'Screen.Width
    runText.Move runText.Left - 30
    
End Sub

Private Sub Timer4_Timer()
    On Error GoTo Error_Handler

'    lblJam.Caption = Format(Now(), "hh:nn:ss")
    Label5.Caption = Format(Now(), "dddd, dd MMM yyyy") & " " & Format(Now(), "hh:nn:ss")
    
    If InStr(1, Label5.Caption, "Monday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Monday", "Senin")
    If InStr(1, Label5.Caption, "Tuesday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Tuesday", "Selasa")
    If InStr(1, Label5.Caption, "Wednesday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Wednesday", "Rabu")
    If InStr(1, Label5.Caption, "Thursday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Thursday", "Kamis")
    If InStr(1, Label5.Caption, "Friday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Friday", "Jumat")
    If InStr(1, Label5.Caption, "Saturday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Saturday", "Sabtu")
    If InStr(1, Label5.Caption, "Sunday", vbTextCompare) > 0 Then Label5.Caption = Replace(Label5.Caption, "Sunday", "Minggu")
    
    igrid = igrid + 1
    If igrid > 15 Then
        igrid = 0
        Call loadantrianfarmasi
    End If
    
    
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



