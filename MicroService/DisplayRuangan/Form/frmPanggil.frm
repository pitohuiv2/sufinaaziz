VERSION 5.00
Begin VB.Form frmPanggil 
   BorderStyle     =   1  'Fixed Single
   ClientHeight    =   915
   ClientLeft      =   45
   ClientTop       =   375
   ClientWidth     =   5040
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   915
   ScaleWidth      =   5040
   StartUpPosition =   3  'Windows Default
   Begin VB.PictureBox Picture1 
      AutoRedraw      =   -1  'True
      BackColor       =   &H00FFFFFF&
      Height          =   525
      Left            =   0
      ScaleHeight     =   31
      ScaleMode       =   3  'Pixel
      ScaleWidth      =   421
      TabIndex        =   1
      TabStop         =   0   'False
      Top             =   1680
      Width           =   6375
   End
   Begin VB.Label lblStatus 
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   9.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   255
      Left            =   240
      TabIndex        =   0
      Top             =   360
      Width           =   2055
   End
End
Attribute VB_Name = "frmPanggil"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False

Public Function Panggil(ByVal QueryText As String) As Byte()
    On Error Resume Next
    Dim Root As JNode
    Dim Param1() As String
    Dim Param2() As String
    Dim Param3() As String
    Dim Param4() As String
    Dim Param5() As String
    Dim Param6() As String
    Dim Param7() As String
    Dim Param8() As String
    Dim arrItem() As String
    
   
    If CN.State = adStateClosed Then Call openConnection
        
    
    If Len(QueryText) > 0 Then
        arrItem = Split(QueryText, "&")
        Param1 = Split(arrItem(0), "=")
        Param2 = Split(arrItem(1), "=")
        Param3 = Split(arrItem(2), "=")
        Param4 = Split(arrItem(3), "=")
        Param5 = Split(arrItem(4), "=")
        Param6 = Split(arrItem(5), "=")
        Select Case Param1(0)
            Case "cek-konek"
                lblStatus.Caption = "Cek"
                Set Root = New JNode
                Root("Status") = "Ok!!"
            
            Case "displaykeun"
                lblStatus.Caption = "Display Poli"
                Dim dispA, dispB, dispC, dispD, dispE, dispF, dispG, dispH, dispI, dispJ As String
'                dispA = GetTxt("Setting.ini", "DisplayTrigger_1", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_1", "PORT")
'                dispB = GetTxt("Setting.ini", "DisplayTrigger_2", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_2", "PORT")
'                dispC = GetTxt("Setting.ini", "DisplayTrigger_3", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_3", "PORT")
'                dispD = GetTxt("Setting.ini", "DisplayTrigger_4", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_4", "PORT")
'                dispE = GetTxt("Setting.ini", "DisplayTrigger_5", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_5", "PORT")
'                dispF = GetTxt("Setting.ini", "DisplayTrigger_6", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_6", "PORT")
'                dispG = GetTxt("Setting.ini", "DisplayTrigger_7", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_7", "PORT")
'                dispH = GetTxt("Setting.ini", "DisplayTrigger_8", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_8", "PORT")
'                dispI = GetTxt("Setting.ini", "DisplayTrigger_9", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_9", "PORT")
'                dispJ = GetTxt("Setting.ini", "DisplayTrigger_10", "IP") & ":" & GetTxt("Setting.ini", "DisplayTrigger_10", "PORT")
                
                Dim tampil As String
                tampil = GetTxt("Setting.ini", "display", "display")
                If LCase(InStr(1, tampil, "FARMASI", vbTextCompare)) Then
                    DisplayFarmasi.Label7.Caption = tampil
'                    If Left(Param2(1), 1) = "N" Then
                        DisplayFarmasi.lblpanggil = Format(Param2(1), "00#")
                        DisplayFarmasi.lblpanggil2 = Param3(1)
'                        Form1280x720Apotik.Label7.Caption = Param4(1)
                        Call DisplayFarmasi.PlaySound(Val(Right(Param2(1), 4)), "F")
                        Call DisplayFarmasi.loadantrianfarmasi
'                    Else
'                        Form1280x720Apotik.Label10.Caption = Format(Param2(1), "00#")
'                        Form1280x720Apotik.Label11.Caption = Param3(1)
''                        Form1280x720Apotik.Label7.Caption = Param4(1)
'                        Call Form1280x720Apotik.PlaySound(Val(Right(Param2(1), 4)), "R")
'                        Call Form1280x720Apotik.loadantrianfarmasi
'                    End If
                Else
'                    If Param1(1) = 1 Then
                        Dim noruangan As String
                        Dim noantrian As String
'                        noruangan = GetTxt("Setting.ini", "nama", "noruangan")
                        Dim ii As Integer
                        
                        strSQL2 = " select dpr.ruanganidfk,dpr.ruanganidfk AS objectruanganfk,dpr.noantrian,ru.noruangan " & _
                                  " from daftarpasienruangantr AS dpr " & _
                                  " inner join ruanganmt AS ru ON ru.id = dpr.ruanganidfk" & _
                                  " where dpr.norec='" & Param2(1) & "' "
                        ReadRs2 strSQL2
                        Dim objectruanganfk As String
                        objectruanganfk = RS2!objectruanganfk
                        noruangan = Trim(RS2!noruangan)
                        noantrian = RS2!noantrian
                        For ii = 0 To 5
                            If Param5(1) = DisplayPoli.Label7(ii).Caption Then
                                DisplayPoli.lblpanggil(ii).Caption = noantrian '
                                DisplayPoli.lblpanggil(ii).ForeColor = vbRed
'                                DisplayPoli.lblpanggil2(ii).Caption = Param3(1)
                                Call DisplayPoli.PlaySound(Val(noantrian), Val(objectruanganfk), "", noruangan)
                            Else
                                DisplayPoli.lblpanggil(ii).ForeColor = vbBlack
                            End If
                        Next
'                        If Param5(1) = DisplayPoli.Label7(0).Caption Then
'                            DisplayPoli.lblpanggil2.Caption = Param3(1)
'                        ElseIf Param1(1) = "2" Then
'                            DisplayPoli.Label13.Caption = Param3(1)
'                        ElseIf Param1(1) = "3" Then
'                            DisplayPoli.Label23.Caption = Param3(1)
'                        ElseIf Param1(1) = "4" Then
'                            DisplayPoli.Label28.Caption = Param3(1)
'                        ElseIf Param1(1) = "5" Then
'                            DisplayPoli.Label34.Caption = Param3(1)
'                        ElseIf Param1(1) = "6" Then
'                            DisplayPoli.Label36.Caption = Param3(1)
'                        End If
'                        If noruangan = "" Then noruangan = 0
                        
'                        strSQL2 = "update daftarpasienruangantr set statusantrian ='2' where statusantrian='1' and ruanganidfk =" & objectruanganfk & ""
'                        Call WriteRs(strSQL2)
'                        strSQL3 = "update daftarpasienruangantr set statusantrian ='1', prefixnoantrian='" & noruangan & "' where norec='" & Param2(1) & "'"
'                        Call WriteRs2(strSQL3)
                    End If
'                    If DisplayPoli.trigg = False Then
'                        Call DisplayPoli.loadAntrian
'                    End If
'                End If
                                                                            
'                Dim strJason As String
'                If Param1(1) = "1" Then
'                    If dispA <> ":" Then
'                        'DoEvents
'                        strJason = GetResponse("http://" & dispA & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                        'DoEvents
'                    End If
'                    If dispB <> ":" Then
'                        'DoEvents
'                        strJason = GetResponse("http://" & dispB & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                        'DoEvents
'                    End If
'                    If dispC <> ":" Then
'                        'DoEvents
'                        strJason = GetResponse("http://" & dispC & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                        'DoEvents
'                    End If
'                    If dispD <> ":" Then
'                        'DoEvents
'                        strJason = GetResponse("http://" & dispD & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                        'DoEvents
'                    End If
'                    If dispE <> ":" Then
'                        'DoEvents
'                        strJason = GetResponse("http://" & dispE & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                    End If
'                    If dispF <> ":" Then
'                        'DoEvents
'                        strJason = GetResponse("http://" & dispF & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                    End If
'                    If dispG <> ":" Then
'                        strJason = GetResponse("http://" & dispG & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                    End If
'                    If dispH <> ":" Then
'                        strJason = GetResponse("http://" & dispH & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                    End If
'                    If dispI <> ":" Then
'                        strJason = GetResponse("http://" & dispI & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                    End If
'                    If dispJ <> ":" Then
'                        strJason = GetResponse("http://" & dispJ & "/printvb/panggil?displaykeun=2&noantri=" & Param2(1) & "&namapasien=" & Param3(1) & "&ruangan=" & Param4(1) & "&ruangan=" & Param5(1) & "&view=" & noruangan & "")
'                    End If
'
'
'                End If
                Set Root = New JNode
                Root("trigger") = "agus.cakram"
            Case Else
                Set Root = New JNode
                Root("Status") = "Error"
        End Select
        
    End If
    
    
    With GossRESTMain.STM
        .Open
        .Type = adTypeText
        .CharSet = "utf-8"
        .WriteText Root.JSON, adWriteChar
        .Position = 0
        .Type = adTypeBinary
        Panggil = .Read(adReadAll)
        .Close
    End With
    If CN.State = adStateOpen Then CN.Close
    Unload Me
    Exit Function
Cetak:
' MsgBox Err.Description
End Function
