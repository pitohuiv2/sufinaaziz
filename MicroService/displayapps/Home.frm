VERSION 5.00
Begin VB.Form Home 
   BorderStyle     =   4  'Fixed ToolWindow
   Caption         =   "DisplayApp"
   ClientHeight    =   1035
   ClientLeft      =   150
   ClientTop       =   495
   ClientWidth     =   2400
   Icon            =   "Home.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   1035
   ScaleWidth      =   2400
   ShowInTaskbar   =   0   'False
   StartUpPosition =   2  'CenterScreen
   Begin VB.Label Label1 
      Caption         =   "@VANDRIAN"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   18
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   495
      Left            =   0
      TabIndex        =   0
      Top             =   480
      Width           =   2295
   End
   Begin VB.Menu file 
      Caption         =   "File"
      Index           =   0
      Begin VB.Menu mnuSetting 
         Caption         =   "Setting"
         Shortcut        =   {F2}
      End
      Begin VB.Menu mnuDisplay 
         Caption         =   "Display"
         Shortcut        =   {F4}
      End
      Begin VB.Menu mnuCaller 
         Caption         =   "Caller"
         Shortcut        =   {F3}
      End
   End
End
Attribute VB_Name = "Home"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Dim nid As NOTIFYICONDATA ' deklarasi variable
Sub minimize_to_tray()
    Me.Hide
    nid.cbSize = Len(nid)
    nid.hwnd = Me.hwnd
    nid.uId = vbNull
    nid.uFlags = NIF_ICON Or NIF_TIP Or NIF_MESSAGE
    nid.uCallBackMessage = WM_MOUSEMOVE
    nid.hIcon = Me.Icon
    nid.szTip = "DisplayApps" & vbNullChar
    Shell_NotifyIcon NIM_ADD, nid
End Sub
Private Sub Form_Load()
    Call Main
    'Show
    WindowState = vbHide
    Call minimize_to_tray
    If CN.State = adStateOpen Then CN.Close
End Sub
    
Private Sub Label1_Click()
    Unload Me
    End
End Sub
