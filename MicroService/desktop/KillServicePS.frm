VERSION 5.00
Begin VB.Form Form1 
   Caption         =   "Form1"
   ClientHeight    =   1050
   ClientLeft      =   60
   ClientTop       =   405
   ClientWidth     =   4920
   Icon            =   "KillServicePS.frx":0000
   LinkTopic       =   "Form1"
   ScaleHeight     =   1050
   ScaleWidth      =   4920
   StartUpPosition =   3  'Windows Default
End
Attribute VB_Name = "Form1"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Sub Form_Load()
    Dim oShell: Set oShell = CreateObject("WScript.Shell")
    ' Kill '
    oShell.Run "taskkill /im PrintService.exe", , True
    Unload Me
    End
End Sub
