Attribute VB_Name = "Shorcut"
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
