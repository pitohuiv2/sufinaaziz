VERSION 5.00
Begin {BD4B4E61-F7B8-11D0-964D-00A0C9273C2A} crRincianBiaya 
   ClientHeight    =   9360
   ClientLeft      =   0
   ClientTop       =   0
   ClientWidth     =   15210
   OleObjectBlob   =   "crRincianBiaya.dsx":0000
End
Attribute VB_Name = "crRincianBiaya"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Sub Section11_Format(ByVal pFormattingInfo As Object)
    Dim X, Y As Double
    X = Round(ucJumlahBill.Value)
    txtPembulatan.SetText Format(X, "##,##0.00")
    X = CDbl(ucDitanggungPerusahaan.Value)
    a.SetText Format(X, "##,##0.#0")
    X = CDbl(ucDitanggungRS.Value)
    b.SetText Format(X, "##,##0.#0")
    X = CDbl(ucDitanggungSendiri.Value)
    c.SetText Format(X, "##,##0.#0")
    X = CDbl(ucSurplusMinusRS.Value)
    d.SetText Format(X, "##,##0.#0")
End Sub

Private Sub Section12_Format(ByVal pFormattingInfo As Object)
    If CDbl(ucDitanggungPerusahaan.Value) = 0 Then
        txtTerbilang.SetText "# " & TerbilangDesimal(Round(ucJumlahBill.Value, 0)) & " #"
    Else
        txtTerbilang.SetText "# " & TerbilangDesimal(Round(ucDitanggungPerusahaan.Value, 0)) & " #"
    End If
End Sub

