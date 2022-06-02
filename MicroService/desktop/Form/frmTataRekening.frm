VERSION 5.00
Begin VB.Form frmTataRekening 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "Tata Rekening"
   ClientHeight    =   1290
   ClientLeft      =   45
   ClientTop       =   375
   ClientWidth     =   4260
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   1290
   ScaleWidth      =   4260
   StartUpPosition =   3  'Windows Default
End
Attribute VB_Name = "frmTataRekening"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Public Function TataRekening(ByVal QueryText As String) As Byte()
On Error GoTo errLoad
    Dim Root As JNode
    Dim Param1() As String
    Dim Param2() As String
    Dim arrItem() As String
    If Len(QueryText) > 0 Then
        arrItem = Split(QueryText, "&")
        Param1 = Split(arrItem(0), "=")
        Param2 = Split(arrItem(1), "=")
        Select Case Param1(0)
            Case "cetak"
'                lblStatus.Caption = "Cetak Antrian"
'                Call CETAK_ANTRIAN(Param2(1), Val(Param1(1)))
'                Set Root = New JNode
'                Root("Status") = "Sedang Dicetak!!"
                
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
        CetakAntrian = .Read(adReadAll)
        .Close
    End With
    Unload Me
    Exit Function
    
errLoad:
End Function

Private Sub CETAK_ANTRIAN(strNorec As String, jumlahCetak As Integer)
On Error GoTo errLoad
    Dim prn As Printer
    Dim strPrinter As String
    
    Dim NoAntri As String
    Dim jmlAntrian As Integer
    Dim jenis As String
    
    Set RS = Nothing
    RS.Open "select * from antrianpasienregistrasi_t where norec ='" & strNorec & "'", CN, adOpenStatic, adLockReadOnly
    NoAntri = RS!jenis & "-" & RS!noantrian
    jenis = RS!jenis
    Set RS = Nothing
    RS.Open "select count(noantrian) as jmlAntri from antrianpasienregistrasi_t where jenis ='" & jenis & "' and statuspanggil='0'", CN, adOpenStatic, adLockReadOnly
    jmlAntrian = RS(0)
    
    strPrinter = GetTxt("Setting.ini", "Printer", "CetakAntrian")
    'GetSetting("Jasamedika Service", "CetakAntrian", "Printer")
    If Printers.count > 0 Then
        For Each prn In Printers
            If prn.DeviceName = strPrinter Then
                Set Printer = prn
                Exit For
            End If
        Next prn
    End If
    
    For i = 1 To jumlahCetak
        'MsgBox "CETAK"
        Printer.fontSize = 10
        Printer.Print "     RUMAH SAKIT ANAK DAN BUNDA"
        Printer.fontSize = 18
        Printer.FontBold = True
        Printer.Print "      HARAPAN KITA"
        Printer.FontBold = False
        Printer.fontSize = 10
        Printer.Print "   Jl. S. Parman Kav.87, Slipi, Jakarta Barat"
        Printer.Print "      Telp. 021-5668286, 021-5668284"
        Printer.Print "      Fax.  021-5601816, 021-5673832"
        Printer.Print "___________________________________"
        Printer.Print ""
        Printer.Print "Tanggal :" & Format(Now(), "yyyy MM dd hh:mm")
        Printer.Print ""
        Printer.fontSize = 14
        Printer.FontBold = True
        Printer.Print "Nomor Antrian Anda : "
        Printer.fontSize = 30
        Printer.Print "       " & NoAntri
        Printer.FontBold = False
        Printer.fontSize = 10
        Printer.Print ""
        Printer.Print " Silahkan menunggu nomor Anda dipanggil"
        Printer.Print "    Antrian yang belum dipanggil " & jmlAntrian & " orang"
        
        Printer.EndDoc
    Next
    
    Exit Sub
    
errLoad:
End Sub



