VERSION 5.00
Begin VB.Form frmSettingKoneksi 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Setting Koneksi"
   ClientHeight    =   3720
   ClientLeft      =   45
   ClientTop       =   375
   ClientWidth     =   5520
   BeginProperty Font 
      Name            =   "Tahoma"
      Size            =   9.75
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "frmSettingKoneksi.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   3720
   ScaleWidth      =   5520
   StartUpPosition =   3  'Windows Default
   Begin VB.CheckBox Check2 
      Caption         =   "Kanan"
      Height          =   255
      Left            =   4320
      TabIndex        =   15
      Top             =   3000
      Width           =   1095
   End
   Begin VB.CheckBox Check1 
      Caption         =   "Kiri"
      Height          =   255
      Left            =   4320
      TabIndex        =   14
      Top             =   2640
      Width           =   1095
   End
   Begin VB.ComboBox cboVol 
      Height          =   360
      Left            =   1560
      TabIndex        =   12
      Top             =   2760
      Width           =   1095
   End
   Begin VB.TextBox Text5 
      Height          =   360
      Left            =   1560
      TabIndex        =   10
      Top             =   2160
      Width           =   3855
   End
   Begin VB.TextBox Text4 
      Height          =   360
      IMEMode         =   3  'DISABLE
      Left            =   1560
      PasswordChar    =   "*"
      TabIndex        =   8
      Top             =   1680
      Width           =   3855
   End
   Begin VB.TextBox Text3 
      Height          =   360
      Left            =   1560
      TabIndex        =   6
      Top             =   1200
      Width           =   3855
   End
   Begin VB.TextBox Text2 
      Height          =   360
      Left            =   1560
      TabIndex        =   4
      Top             =   720
      Width           =   3855
   End
   Begin VB.TextBox Text1 
      Height          =   360
      Left            =   1560
      TabIndex        =   2
      Top             =   240
      Width           =   3855
   End
   Begin VB.CommandButton Command1 
      Caption         =   "Simpan"
      Height          =   375
      Left            =   3480
      TabIndex        =   0
      Top             =   3300
      Width           =   1935
   End
   Begin VB.Label Label8 
      Caption         =   "Suara Antrian :"
      Height          =   375
      Left            =   2760
      TabIndex        =   13
      Top             =   2760
      Width           =   1455
   End
   Begin VB.Label Label7 
      Caption         =   "Volume Video :"
      Height          =   255
      Left            =   120
      TabIndex        =   11
      Top             =   2760
      Width           =   1575
   End
   Begin VB.Label Label5 
      Caption         =   "Database :"
      Height          =   255
      Left            =   120
      TabIndex        =   9
      Top             =   2160
      Width           =   1215
   End
   Begin VB.Label Label4 
      Caption         =   "Password :"
      Height          =   255
      Left            =   120
      TabIndex        =   7
      Top             =   1680
      Width           =   1215
   End
   Begin VB.Label Label3 
      Caption         =   "User Name :"
      Height          =   255
      Left            =   120
      TabIndex        =   5
      Top             =   1200
      Width           =   1215
   End
   Begin VB.Label Label2 
      Caption         =   "Port :"
      Height          =   255
      Left            =   120
      TabIndex        =   3
      Top             =   720
      Width           =   1215
   End
   Begin VB.Label Label1 
      Caption         =   "Host :"
      Height          =   255
      Left            =   120
      TabIndex        =   1
      Top             =   240
      Width           =   1215
   End
End
Attribute VB_Name = "frmSettingKoneksi"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Sub Command1_Click()
    SaveTxt "Setting.ini", "Koneksi", "a", Text1.Text
    SaveTxt "Setting.ini", "Koneksi", "b", Text2.Text
    SaveTxt "Setting.ini", "Koneksi", "c", Text3.Text
    SaveTxt "Setting.ini", "Koneksi", "d", Text4.Text
    SaveTxt "Setting.ini", "Koneksi", "e", Text5.Text
    
    SaveTxt "Setting.ini", "Volume", "video", cboVol.Text
    
    If Check1.Value = Checked Then
        SaveTxt "Setting.ini", "SuaraAntrian", "kiri", 1
    Else
        SaveTxt "Setting.ini", "SuaraAntrian", "kiri", 0
    End If
    
    If Check2.Value = Checked Then
        SaveTxt "Setting.ini", "SuaraAntrian", "kanan", 1
    Else
        SaveTxt "Setting.ini", "SuaraAntrian", "kanan", 0
    End If
    
End Sub

Private Sub Form_Load()
On Error Resume Next
    Text1.Text = GetTxt("Setting.ini", "Koneksi", "host")
    Text2.Text = GetTxt("Setting.ini", "Koneksi", "portdb")
    Text3.Text = GetTxt("Setting.ini", "Koneksi", "user")
    Text4.Text = GetTxt("Setting.ini", "Koneksi", "password")
    Text5.Text = GetTxt("Setting.ini", "Koneksi", "namadb")
    
    cboVol.Text = GetTxt("Setting.ini", "Volume", "video")
    
    If GetTxt("Setting.ini", "SuaraAntrian", "kiri") = 1 Then
        Check1.Value = Checked
    Else
        Check1.Value = Unchecked
    End If
    
    If GetTxt("Setting.ini", "SuaraAntrian", "kanan") = 1 Then
        Check2.Value = Checked
    Else
        Check2.Value = Unchecked
    End If
    
    cboVol.AddItem 0
    cboVol.AddItem 30
    cboVol.AddItem 60
    cboVol.AddItem 100
End Sub

