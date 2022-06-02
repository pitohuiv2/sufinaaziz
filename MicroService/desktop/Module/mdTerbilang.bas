Attribute VB_Name = "mdTerbilang"
Public Function TERBILANG(x As Double) As String
Dim tampung As Double
Dim teks As String
Dim bagian As String
Dim I As Integer
Dim tanda As Boolean
 
Dim letak(5)
letak(1) = "RIBU "
letak(2) = "JUTA "
letak(3) = "MILYAR "
letak(4) = "TRILYUN "
 
If (x < 0) Then
    TERBILANG = ""
Exit Function
End If
 
If (x = 0) Then
    TERBILANG = "NOL"
Exit Function
End If
 
If (x < 2000) Then
    tanda = True
End If
teks = ""
 
If (x >= 1E+15) Then
    TERBILANG = "NILAI TERLALU BESAR"
Exit Function
End If
 
For I = 4 To 1 Step -1
    tampung = Int(x / (10 ^ (3 * I)))
    If (tampung > 0) Then
        bagian = ratusan(tampung, tanda)
        teks = teks & bagian & letak(I)
    End If
    x = x - tampung * (10 ^ (3 * I))
Next
 
teks = teks & ratusan(x, False)
TERBILANG = teks & " RUPIAH"
End Function
 
Function ratusan(ByVal y As Double, ByVal flag As Boolean) As String
Dim tmp As Double
Dim bilang As String
Dim bag As String
Dim j As Integer
 
Dim angka(9)
angka(1) = "SE"
angka(2) = "DUA "
angka(3) = "TIGA "
angka(4) = "EMPAT "
angka(5) = "LIMA "
angka(6) = "ENAM "
angka(7) = "TUJUH "
angka(8) = "DELAPAN "
angka(9) = "SEMBILAN "
 
Dim posisi(2)
posisi(1) = "PULUH "
posisi(2) = "RATUS "
 
bilang = ""
For j = 2 To 1 Step -1
    tmp = Int(y / (10 ^ j))
    If (tmp > 0) Then
        bag = angka(tmp)
        If (j = 1 And tmp = 1) Then
            y = y - tmp * 10 ^ j
            If (y >= 1) Then
                posisi(j) = "BELAS "
            Else
                angka(y) = "SE"
            End If
            bilang = bilang & angka(y) & posisi(j)
            ratusan = bilang
            Exit Function
        Else
            bilang = bilang & bag & posisi(j)
    End If
End If
y = y - tmp * 10 ^ j
Next
 
If (flag = False) Then
    angka(1) = "SATU "
End If
bilang = bilang & angka(y)
ratusan = bilang
End Function

Public Function hitungUmurNew(dateOfBird As String, fromData As String) As String
    If dateOfBird <> "" Then
        dateOfBird = Format(dateOfBird, "yyyy-MM-dd")
        Dim rsau As New ADODB.Recordset
        rsau.Open "DECLARE @givenDate datetime, @tempDate datetime, @years INT, @months INT, @days INT  " & _
                  "SET @givenDate = '" & dateOfBird & "'" & _
                  "SELECT @tempDate = @givenDate " & _
                  "SELECT @years = DATEDIFF(yy, @tempDate, GETDATE()) - CASE   WHEN (MONTH (@givenDate) > MONTH (GETDATE()))   OR (MONTH (@givenDate) = MONTH (GETDATE())AND DAY (@givenDate) > DAY (GETDATE())) THEN 1 ELSE   0   END  " & _
                  "SELECT @tempDate = DATEADD(yy, @years, @tempDate)   " & _
                  "SELECT @months = DATEDIFF(m, @tempDate, GETDATE()) - CASE WHEN DAY (@givenDate) > DAY (GETDATE()) THEN 1 ELSE 0   END  " & _
                  "SELECT @tempDate = DATEADD(m, @months, @tempDate)  " & _
                  "SELECT @days = DATEDIFF(d, @tempDate, GETDATE()) " & _
                  "SELECT @years, @months, @days ", CN, adOpenStatic, adLockReadOnly
        
        Dim str As String
            
        hitungUmurNew = CStr(rsau(0)) + " thn " + CStr(rsau(1)) + " bln " + CStr(rsau(2)) + " hari"
    Else
        hitungUmurNew = ""
    End If


End Function
Public Function hitungUmur(dateOfBird As String, fromData As String) As String
'    If dateOfBird <> "" Then
'        dateOfBird = Format(dateOfBird, "yyyy-MM-dd HH:mm:ss")
'        Dim rsau As New ADODB.Recordset
'        rsau.Open "DECLARE @date datetime, @tmpdate datetime, @years int, @months int, @days int " & _
'                  "SELECT @date = '" & dateOfBird & "'" & _
'                  "SELECT @tmpdate = @date " & _
'                  "SELECT @years = DATEDIFF(yy, @tmpdate, GETDATE()) - CASE WHEN (MONTH(@date) > MONTH(GETDATE())) OR (MONTH(@date) = MONTH(GETDATE()) AND DAY(@date) > DAY(GETDATE())) THEN 1 ELSE 0 END " & _
'                  "SELECT @tmpdate = DATEADD(yy, @years, @tmpdate) " & _
'                  "SELECT @months = DATEDIFF(m, @tmpdate, GETDATE()) - CASE WHEN DAY(@date) > DAY(GETDATE()) THEN 1 ELSE 0 END " & _
'                  "SELECT @tmpdate = DATEADD(m, @months, @tmpdate) " & _
'                  "SELECT @days = DATEDIFF(d, @tmpdate, GETDATE()) " & _
'                  "SELECT @years, @months, @days", CN, adOpenStatic, adLockReadOnly
'
'        Dim str As String
'        hitungUmur = CStr(rsau(0)) + " thn " + CStr(rsau(1)) + " bln " + CStr(rsau(2)) + " hari"
'    Else
'        hitungUmur = ""
'    End If
    If dateOfBird <> "" Then
        dateOfBird = Format(dateOfBird, "yyyy-MM-dd")
        Dim rsau As New ADODB.Recordset
        rsau.Open "select age(timestamp '" & dateOfBird & "') as umur", CN, adOpenStatic, adLockReadOnly
        
'        ReadRs5 "select age(timestamp '" & dateOfBird & "') as umur"
        '6 years 4 mons 29 days
        Dim str As String
        
        str = rsau!umur
        
        str = Replace(str, "years", "Thn")
        str = Replace(str, "year", "Thn")
        str = Replace(str, "mons", "Bln")
        str = Replace(str, "mon", "Bln")
        str = Replace(str, "days", "Hr")
        str = Replace(str, "day", "Hr")
        
        hitungUmur = str
    Else
        hitungUmur = ""
    End If

End Function

Public Function hitungUmurTahun(dateOfBird As Date, fromData As Date) As String
    hitungUmurTahun = DateDiff("yyyy", dateOfBird, fromData) & " thn"
End Function

Public Function TerbilangDesimal(InputCurrency As String, Optional MataUang As String = "rupiah") As String
 Dim strInput As String
 Dim strBilangan As String
 Dim strPecahan As String
   On Error GoTo Pesan
   Dim strValid As String, huruf As String * 1
   Dim I As Integer
   InputCurrency = Replace(InputCurrency, ",", "")
   InputCurrency = Replace(InputCurrency, ".", ",")
   strValid = "1234567890,"
   For I% = 1 To Len(InputCurrency)
     huruf = Chr(Asc(Mid(InputCurrency, I%, 1)))
     If InStr(strValid, huruf) = 0 Then
       Set AngkaTerbilang = Nothing
       MsgBox "Harus karakter angka!", _
              vbCritical, "Karakter Tidak Valid"
       Exit Function
     End If
   Next I%
 
 If InputCurrency = "" Then Exit Function
 If Len(Trim(InputCurrency)) > 15 Then GoTo Pesan
 
 strInput = CStr(InputCurrency) 'Konversi ke string
 'Periksa apakah ada tanda "," jika ya berarti pecahan
 If InStr(1, strInput, ",", vbBinaryCompare) Then
       
  strBilangan = Left(strInput, InStr(1, strInput, _
                ",", vbBinaryCompare) - 1)
  'strBilangan = Right(strInput, InStr(1, strInput, _
  '              ".", vbBinaryCompare) - 2)
  strPecahan = Trim(Right(strInput, Len(strInput) - Len(strBilangan) - 1))
   
  If MataUang <> "" Then
       
  If CLng(Trim(strPecahan)) > 99 Then
     strInput = Format(Round(CDbl(strInput), 2), "#0.00")
     strPecahan = Format((Right(strInput, Len(strInput) - Len(strBilangan) - 1)), "00")
    End If
     
    If Len(Trim(strPecahan)) = 1 Then
'       strInput = Format(Round(CDbl(strInput), 2), _
'                  "#0.00")
       strPecahan = Format((Right(strInput, _
          Len(strInput) - Len(strBilangan) - 1)), "00")
    End If
     
    If CLng(Trim(strPecahan)) = 0 Then
    TerbilangDesimal = (KonversiBilangan(strBilangan) & " " & KonversiBilangan(strPecahan))
 Else
  TerbilangDesimal = (KonversiBilangan(strBilangan) & " koma " & KonversiBilangan(strPecahan) & "")
    End If
  Else
    TerbilangDesimal = (KonversiBilangan(strBilangan) & "koma " & KonversiPecahan(strPecahan))
  End If
   
 Else
    TerbilangDesimal = (KonversiBilangan(strInput))
  End If
  
    TerbilangDesimal = UCase(TerbilangDesimal & " rupiah")
 Exit Function
Pesan:
  TerbilangDesimal = "(maksimal 15 digit)"
End Function

'Fungsi ini untuk mengkonversi nilai pecahan (setelah 'angka 0)
Private Function KonversiPecahan(strAngka As String) As String
Dim I%, strJmlHuruf$, Urai$, Kar$
 If strAngka = "" Then Exit Function
    strJmlHuruf = Trim(strAngka)
    Urai = ""
    Kar = ""
    For I = 1 To Len(strJmlHuruf)
      'Tampung setiap satu karakter ke Kar
      Kar = Mid(strAngka, I, 1)
      Urai = Urai & Kata(CInt(Kar))
    Next I
    KonversiPecahan = Urai
End Function
'Fungsi ini untuk menterjemahkan setiap satu angka ke 'kata
Private Function Kata(angka As Byte) As String
   Select Case angka
          Case 1: Kata = "satu "
          Case 2: Kata = "dua "
          Case 3: Kata = "tiga "
          Case 4: Kata = "empat "
          Case 5: Kata = "lima "
          Case 6: Kata = "enam "
          Case 7: Kata = "tujuh "
          Case 8: Kata = "delapan "
          Case 9: Kata = "sembilan "
          Case 0: Kata = "nol "
   End Select
End Function
'Ini untuk mengkonversi nilai bilangan sebelum pecahan
Private Function KonversiBilangan(strAngka As String) As String
Dim strJmlHuruf$, intPecahan As Integer, strPecahan$, Urai$, Bil1$, strTot$, Bil2$
 Dim x, y, z As Integer
 
 If strAngka = "" Then Exit Function
    strJmlHuruf = Trim(strAngka)
    x = 0
    y = 0
    Urai = ""
    While (x < Len(strJmlHuruf))
      x = x + 1
      strTot = Mid(strJmlHuruf, x, 1)
      y = y + Val(strTot)
      z = Len(strJmlHuruf) - x + 1
      Select Case Val(strTot)
      'Case 0
       '   Bil1 = "NOL "
      Case 1
          If (z = 1 Or z = 7 Or z = 10 Or z = 13) Then
              Bil1 = "satu "
          ElseIf (z = 4) Then
              If (x = 1) Then
                  Bil1 = "se"
              Else
                  Bil1 = "satu "
              End If
          ElseIf (z = 2 Or z = 5 Or z = 8 Or z = 11 Or z = 14) Then
              x = x + 1
              strTot = Mid(strJmlHuruf, x, 1)
              z = Len(strJmlHuruf) - x + 1
              Bil2 = ""
              Select Case Val(strTot)
              Case 0
                  Bil1 = "sepuluh "
              Case 1
                  Bil1 = "sebelas "
              Case 2
                  Bil1 = "dua belas "
              Case 3
                  Bil1 = "tiga belas "
              Case 4
                  Bil1 = "empat belas "
              Case 5
                  Bil1 = "lima belas "
              Case 6
                  Bil1 = "enam belas "
              Case 7
                  Bil1 = "tujuh belas "
              Case 8
                  Bil1 = "delapan belas "
              Case 9
                  Bil1 = "sembilan belas "
              End Select
          Else
              Bil1 = "se"
          End If
       
      Case 2
          Bil1 = "dua "
      Case 3
          Bil1 = "tiga "
      Case 4
          Bil1 = "empat "
      Case 5
          Bil1 = "lima "
      Case 6
          Bil1 = "enam "
      Case 7
          Bil1 = "tujuh "
      Case 8
          Bil1 = "delapan "
      Case 9
          Bil1 = "sembilan "
      Case Else
          Bil1 = ""
      End Select
        
      If (Val(strTot) > 0) Then
         If (z = 2 Or z = 5 Or z = 8 Or z = 11 Or z = 14) Then
            Bil2 = "puluh "
         ElseIf (z = 3 Or z = 6 Or z = 9 Or z = 12 Or z = 15) Then
            Bil2 = "ratus "
         Else
            Bil2 = ""
         End If
      Else
         Bil2 = ""
      End If
      If (y > 0) Then
          Select Case z
          Case 4
              Bil2 = Bil2 + "ribu "
              y = 0
          Case 7
              Bil2 = Bil2 + "juta "
              y = 0
          Case 10
              Bil2 = Bil2 + "milyar "
              y = 0
          Case 13
              Bil2 = Bil2 + "trilyun "
              y = 0
          End Select
      End If
      Urai = Urai + Bil1 + Bil2
  Wend
  KonversiBilangan = Urai
End Function

Public Function getHari(strTgl As String) As String
    Dim Tgl, t As String
    Tgl = Format(strTgl, "yyyy-MM-dd")
    t = Weekday(strTgl, vbSunday)
    If t = "1" Then
        hari = "Minggu"
    ElseIf t = "2" Then
        hari = "Senin"
    ElseIf t = "3" Then
        hari = "Selasa"
    ElseIf t = "4" Then
        hari = "Rabu"
    ElseIf t = "5" Then
        hari = "Kamis"
    ElseIf t = "6" Then
        hari = "Jumat"
    ElseIf t = "7" Then
        hari = "Sabtu"
    End If
    getHari = hari
End Function
Public Function getBulan(strTgl As Date) As String
    Dim m, bulan As String
    m = Format(strTgl, "MM")
    If m = "01" Then
        bulan = "Januari"
    ElseIf m = "02" Then
        bulan = "Februari"
    ElseIf m = "03" Then
        bulan = "Maret"
    ElseIf m = "04" Then
        bulan = "April"
    ElseIf m = "05" Then
        bulan = "Mei"
    ElseIf m = "06" Then
        bulan = "Juni"
    ElseIf m = "07" Then
        bulan = "Juli"
    ElseIf m = "08" Then
        bulan = "Agustus"
    ElseIf m = "09" Then
        bulan = "September"
    ElseIf m = "10" Then
        bulan = "Oktober"
    ElseIf m = "11" Then
        bulan = "November"
    ElseIf m = "12" Then
        bulan = "Desember"
    End If
    getBulan = bulan
End Function

