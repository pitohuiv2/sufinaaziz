VERSION 5.00
Begin VB.Form routes 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "RE-ROUTE"
   ClientHeight    =   915
   ClientLeft      =   45
   ClientTop       =   375
   ClientWidth     =   1890
   Icon            =   "routes.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   915
   ScaleWidth      =   1890
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
      Caption         =   "Desktop Routes"
      BeginProperty Font 
         Name            =   "Tahoma"
         Size            =   9.75
         Charset         =   0
         Weight          =   700
         Underline       =   0   'False
         Italic          =   0   'False
         Strikethrough   =   0   'False
      EndProperty
      Height          =   735
      Left            =   480
      TabIndex        =   0
      Top             =   120
      Width           =   1095
   End
End
Attribute VB_Name = "routes"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False

Public Function desktop(ByVal QueryText As String) As Byte()
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
    Dim Param9() As String
    Dim Param10() As String
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
        Param7 = Split(arrItem(6), "=")
        Param8 = Split(arrItem(7), "=")
        Param9 = Split(arrItem(8), "=")
        Param10 = Split(arrItem(9), "=")
        Select Case Param1(0)
            Case "re-routes"
                lblStatus.Caption = "Cek"
                Set Root = New JNode
                Root("Status") = "Ok!!"
            '# FARMASI #'
            Case "cetak-buktiantrianfarmasi"
                Param3 = Split(arrItem(2), "=")
                Call frmFarmasi.cetakAntrianFarmasi(Param2(1), Param3(1), Param4(1))
                Set Root = New JNode
                Root("Status") = "Cetak Nomor Antrian Farmasi!!"
                Root("by") = "epc"
            Case "cetak-LabelFarmasi"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                Call frmFarmasi.CetakLabelFarmasi(Param2(1), Param3(1), Param4(1), Param5(1))
                Set Root = New JNode
                Root("Status") = "Cetak Label Farmasi"
                Root("by") = "epc"
            Case "cetak-resep"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                Call frmFarmasi.cetakResep(Param2(1), Param3(1), Param4(1), Param5(1))
                Set Root = New JNode
                Root("Status") = "Cetak Resep!!"
                Root("by") = "epc"
            Case "cetak-rincian-resep-rajal"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                Call frmFarmasi.cetakRincianResepRajal(Param2(1), Param3(1), Param4(1))
                Set Root = New JNode
                Root("Status") = "Cetak Resep!!"
                Root("by") = "epc"
            Case "cetak-rincian-resep-ranap"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                Call frmFarmasi.cetakRincianResepRanap(Param2(1), Param3(1), Param4(1))
                Set Root = New JNode
                Root("Status") = "Cetak Resep!!"
                Root("by") = "epc"
            '# END FARMASI #'
            '# GIZI #'
            Case "cetak-label-gizi"
                Call frmGizi.CetakLabelGizi(Param2(1), Param3(1), Param4(1))
                Set Root = New JNode
                Root("Status") = "Cetak Label Gizi"
                Root("by") = "epc"
            '# END GIZI #'
            '# KASIR #'
            Case "cetak-kwitansi"
                Call frmKwitansi.CetakUlangJenisKuitansi(Param2(1), Param3(1), Param4(1))
                'Call frmKwitansi.CetakUlangJenisKuitansi(Param2(1), Param3(1), Param4(1), Param5(1), Param6(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            'http://127.0.0.1:3885/desk/routes?cetak-kwitansi=1&nostruk=9d53afd0-c38b-11eb-ab26-1f018773&qty=1&idpegawai=Dr.%20Administrator,%20Ph.D.&ket=&view=true
            Case "cetak-RincianBiaya"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                Param6 = Split(arrItem(5), "=")
                Call frmKasir.CetakRincianDetail(Param2(1), (Param3(1)), Param4(1))
                Set Root = New JNode
                Root("Status") = "Cetak Rincian Biaya"
                Root("by") = "epc"
            Case "cetak-RekapRincianBiaya"
                Param4 = Split(arrItem(3), "=")
                Call frmKasir.CetakRekapRincian(Param2(1), (Param3(1)), Param4(1))
                Set Root = New JNode
                Root("Status") = "Cetak Rincian Biaya Rekap"
                Root("by") = "epc"
            Case "cetak-RincianBiayaObatAlkes"
                Param4 = Split(arrItem(3), "=")
                Call frmKasir.CetakRincianBiayaObat(Param2(1), (Param3(1)), Param4(1))
                Set Root = New JNode
                Root("Status") = "Cetak Rincian Obat"
                Root("by") = "epc"
            Case "cetak-laporan-penerimaan-kasir"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                Param6 = Split(arrItem(5), "=")
                Param7 = Split(arrItem(6), "=")
                Param8 = Split(arrItem(7), "=")
                Param9 = Split(arrItem(8), "=")
                Call frmKasir.CetakLaporanKasir(Param2(1), Param3(1), Param4(1), Param5(1), Param6(1), Param7(1), Param8(1), Param9(1))
                Set Root = New JNode
                Root("Status") = "Cetak Rincian Obat"
                Root("by") = "epc"
            '# END KASIR #'
            '# LOGISTIK #'
            Case "cetak-bukti-order"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                Param6 = Split(arrItem(5), "=")
                Param7 = Split(arrItem(6), "=")
                Param8 = Split(arrItem(7), "=")
                Call frmLogistik.cetakBuktiOrder(Param2(1), Param3(1), (Param4(1)), Param5(1), Param6(1), Param7(1), Param8(1))
                Set Root = New JNode
                Root("Status") = "Cetak Bukti Order !!"
                Root("by") = "epc"
            Case "cetak-bukti-kirim"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                Param6 = Split(arrItem(5), "=")
                Param7 = Split(arrItem(6), "=")
                Param8 = Split(arrItem(7), "=")
                Param9 = Split(arrItem(8), "=")
                Param10 = Split(arrItem(9), "=")
                Call frmLogistik.CetakBuktiKirim(Param2(1), Param3(1), (Param4(1)), Param5(1), Param6(1), Param7(1), Param8(1), Param9(1), Param10(1))
                Set Root = New JNode
                Root("Status") = "Cetak Bukti Kirim !!"
                Root("by") = "epc"
            Case "cetak-bukti-penerimaan"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                Param6 = Split(arrItem(5), "=")
                Param7 = Split(arrItem(6), "=")
                Param8 = Split(arrItem(7), "=")
                Param9 = Split(arrItem(8), "=")
                Param10 = Split(arrItem(9), "=")
                Call frmLogistik.CetakBuktiPenerimaan(Param2(1), Param3(1), (Param4(1)), Param5(1), Param6(1), Param7(1), Param8(1), Param9(1), Param10(1))
                Set Root = New JNode
                Root("Status") = "Cetak Bukti Kirim !!"
                Root("by") = "epc"
            '# END LOGISTIK #'
            '# REGISTRASI - KIOS-K #'
            Case "cetak-antrian-pendaftaran"
                Call frmRegistrasi.cetakAntrianPendaftaran(Param2(1), Param3(1), Param4(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-antrian-online"
                Call frmRegistrasi.cetakAntrianPendaftaranOnline(Param2(1), Param3(1), Param4(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-sep"
                Call frmSep.cetak(Param2(1), Val(Param3(1)), Param4(1), Param5(1), Param6(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
                'http://127.0.0.1:3885/desk/routes?cetak-sep=1&nostruk=9d53afd0-c38b-11eb-ab26-1f018773&qty=1&idpegawai=Dr.%20Administrator,%20Ph.D.&ket=&view=true
            Case "cetak-antrian"
                Call antrian(Param2(1), Val(Param1(1)))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-tracer"
                Call frmRegistrasi.cetakTracer(Param2(1), Param3(1))
                'http://127.0.0.1:3885/desk/routes?cetak-sep=1&nostruk=9d53afd0-c38b-11eb-ab26-1f018773&qty=1&idpegawai=Dr.%20Administrator,%20Ph.D.&ket=&view=true
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-blangko-bpjs"
                Param4 = Split(arrItem(3), "=")
                Call frmRegistrasi.cetakBlangkoBpjs(Param2(1), Param3(1), Param4(1))
                'http://127.0.0.1:3885/desk/routes?cetak-sep=1&nostruk=9d53afd0-c38b-11eb-ab26-1f018773&qty=1&idpegawai=Dr.%20Administrator,%20Ph.D.&ket=&view=true
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-buktilayanan"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                lblStatus.Caption = "Cetak Bukti Layanan Ruangan"
                Call frmRegistrasi.cetakBuktiLayananRuangan(Param2(1), Param3(1), Param4(1), Param5(1))
                'http://127.0.0.1:3885/desk/routes?cetak-sep=1&nostruk=9d53afd0-c38b-11eb-ab26-1f018773&qty=1&idpegawai=Dr.%20Administrator,%20Ph.D.&ket=&view=true
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-kartu-pasien"
                Param5 = Split(arrItem(4), "=")
                Param6 = Split(arrItem(5), "=")
                Param7 = Split(arrItem(6), "=")
                Call frmRegistrasi.cetak_KartuPasien(Param2(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-labelpasien"
                Param4 = Split(arrItem(3), "=")
                Call frmRegistrasi.cetakLabelPasien(Param2(1), Param3(1), Param4(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-gelangpasien"
                Param4 = Split(arrItem(3), "=")
                lblStatus.Caption = "Cetak Gelang Pasien"
                Call frmRegistrasi.cetakGelangPasien(Param2(1), Param3(1), Param4(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-lembar-identitas"
                Param5 = Split(arrItem(4), "=")
                Param6 = Split(arrItem(5), "=")
                lblStatus.Caption = "Cetak Identitas Pasien"
                Call frmRegistrasi.CetakLembarIdentitasPasien(Param2(1), Param3(1), Param4(1), Param5(1), Param6(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-surat-pernyataan-ranap"
                Param4 = Split(arrItem(3), "=")
                Param5 = Split(arrItem(4), "=")
                lblStatus.Caption = "Cetak Surat Pernyataan Ranap"
                Call frmRegistrasi.CetakPersetujuanRanap(Param2(1), Param3(1), Param4(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
             Case "cetak-summarylist"
                Call frmRegistrasi.cetakSummaryList(Param2(1), Param3(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
             Case "cetak-triage"
                Call frmRegistrasi.cetakTriage(Param2(1), Param3(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-surat-jampersal"
                Call frmRegistrasi.cetakSuratJampersal(Param2(1), Param3(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            Case "cetak-formulir-rajal"
                Call frmRegistrasi.cetakFormulirRajal(Param2(1), Param3(1))
                Set Root = New JNode
                Root("Status") = "Sedang Dicetak!!"
                Root("by") = "epc"
            '# END REGISTRASI - KIOS-K #'
            
            Case "call-antrian"
                displayRegistrasi.Timer1.Enabled = True
                Set Root = New JNode
                Root("Status") = "Sedang di panggil!!"
                
                
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
        Pendaftaran = .Read(adReadAll)
        .Close
    End With
    If CN.State = adStateOpen Then CN.Close
    Unload Me
    Exit Function
cetak:
' MsgBox Err.Description
End Function

Private Sub antrian(strNorec As String, jumlahCetak As Integer)
On Error Resume Next
    Dim prn As Printer
    Dim strPrinter As String
    
    Dim NoAntri As String
    Dim jmlAntrian As Integer
    Dim jenis As String
    Dim Ruangan As String
    
    Set RS = Nothing
    RS.Open "select * from perjanjianpasientr where norec ='" & strNorec & "'", CN, adOpenStatic, adLockReadOnly
      NoAh = RS!noantrian
    If Len(NoAh) = 1 Then
        NoAh = "00" & RS!noantrian
    ElseIf Len(NoAh) = 2 Then
        NoAh = "0" & RS!noantrian
    End If
       
    NoAntri = RS!jenis & "-" & NoAh
    jenis = RS!jenis
    Set RS = Nothing
    RS.Open "select count(noantrian) as jmlAntri from perjanjianpasientr where jenis ='" & jenis & "' and " & _
            "statuspanggil='0' and tanggalreservasi between '" & Format(Now(), "YYYY/mm/dd" & " 00:00") & "' and '" & Format(Now(), "YYYY/mm/dd" & " 23:59") & "' ", CN, adOpenStatic, adLockReadOnly
    jmlAntrian = RS(0)
         
    If jenis = "A" Then
        Ruangan = "UMUM"
    ElseIf jenis = "B" Then
        Ruangan = "BPJS"
    End If
    
    strPrinter = GetTxt("Setting.ini", "Printer", "Receipt")
    If Printers.Count > 0 Then
        For Each prn In Printers
            If prn.DeviceName = strPrinter Then
                Set Printer = prn
                Exit For
            End If
        Next prn
    End If
    
    For i = 1 To jumlahCetak
        Printer.FontSize = 10
        Printer.FontBold = True
        Printer.Print strNamaRS
        Printer.FontSize = 18
        Printer.FontBold = False
        Printer.FontSize = 10
        Printer.Print strAlamatRS
'        Printer.Print "Kabupaten Bandung 40394"
        Printer.Print strNoTlpn & "," & strNoFax ' "Telp. 022 - 555555, Fax. 022 - 666666"
        Printer.Print "___________________________________"
        Printer.Print ""
        Printer.Print "Tanggal : " & Format(Now(), "yyyy MM dd hh:mm")
        Printer.Print ""
        Printer.FontSize = 14
        Printer.FontBold = True
        Printer.Print "Nomor Antrian : "
        Printer.FontSize = 30
        Printer.Print "       " & NoAntri
        Printer.FontBold = False
        Printer.FontSize = 12
        Printer.Print ""
        Printer.Print Ruangan
        Printer.FontSize = 10
        Printer.Print ""
        Printer.Print " Silahkan menunggu nomor Anda dipanggil"
        Printer.Print "    Antrian yang belum dipanggil " & jmlAntrian & " orang"
        
        Printer.EndDoc
    Next
End Sub


