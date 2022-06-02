VERSION 5.00
Begin VB.Form frmSettingPrinter 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Printer Option"
   ClientHeight    =   960
   ClientLeft      =   45
   ClientTop       =   375
   ClientWidth     =   6975
   BeginProperty Font 
      Name            =   "Tahoma"
      Size            =   9.75
      Charset         =   0
      Weight          =   400
      Underline       =   0   'False
      Italic          =   0   'False
      Strikethrough   =   0   'False
   EndProperty
   Icon            =   "frmSettingPrinter.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   960
   ScaleWidth      =   6975
   StartUpPosition =   3  'Windows Default
   Begin VB.CommandButton Command1 
      Caption         =   "Simpan"
      Height          =   735
      Left            =   5880
      TabIndex        =   4
      Top             =   120
      Width           =   975
   End
   Begin VB.ComboBox Combo2 
      Height          =   360
      Left            =   1560
      TabIndex        =   3
      Top             =   480
      Width           =   4215
   End
   Begin VB.ComboBox Combo1 
      Height          =   360
      Left            =   1560
      TabIndex        =   1
      Top             =   120
      Width           =   4215
   End
   Begin VB.Label Label2 
      Caption         =   "Printer"
      Height          =   255
      Left            =   120
      TabIndex        =   2
      Top             =   480
      Width           =   1215
   End
   Begin VB.Label Label1 
      Caption         =   "Ukuran Kertas"
      Height          =   255
      Left            =   120
      TabIndex        =   0
      Top             =   120
      Width           =   1215
   End
End
Attribute VB_Name = "frmSettingPrinter"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Sub Combo1_Change()
    On Error Resume Next
    Combo2.Text = ""
    Combo2.Text = GetTxt("Setting.ini", "Printer", Combo1.Text)
End Sub

Private Sub Combo1_Click()
    Call Combo1_Change
End Sub

Private Sub Command1_Click()
    SaveTxt "Setting.ini", "Printer", Combo1.Text, Combo2.Text
End Sub

Private Sub Form_Load()
    For Each prn In Printers
        Combo2.AddItem prn.DeviceName
    Next prn
    Combo1.AddItem "A4_single"
    Combo1.AddItem "A4_continues"
    Combo1.AddItem "A5_single"
    Combo1.AddItem "A5_ccontinues"
    Combo1.AddItem "SEP"
    Combo1.AddItem "Struk"
    Combo1.AddItem "Receipt"
    Combo1.AddItem "KartuPasien"
    Combo1.AddItem "Tracer"
    Combo1.AddItem "LabelPasien"
    Combo1.AddItem "GelangPasien"
    Combo1.AddItem "BuktiPendaftaran"
    Combo1.AddItem "BlangkoBpjs"
    Combo1.AddItem "BuktiLayanan"
    Combo1.AddItem "LembarIdentitas"
    Combo1.AddItem "PernyataanRawatInap"
    Combo1.AddItem "SummaryList"
    Combo1.AddItem "TriageIGD"
    Combo1.AddItem "RincianBiayaDetail"
    Combo1.AddItem "RincianBiayaRekap"
    Combo1.AddItem "RincianObatAlkes"
    Combo1.AddItem "AntrianFarmasi"
    Combo1.AddItem "LabelFarmasi"
    Combo1.AddItem "ResepFarmasi"
    Combo1.AddItem "LabelGizi"
    Combo1.AddItem "suratJampersal"
    Combo1.AddItem "FormulirRawatJalan"
End Sub

