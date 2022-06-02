Attribute VB_Name = "Koneksi"

Public CN As New ADODB.Connection
Public RS As New ADODB.Recordset
Public RS2 As New ADODB.Recordset
Public RS3 As New ADODB.Recordset
Public RS4 As New ADODB.Recordset
Public RS5 As New ADODB.Recordset
Public CN_String As String
Public strSQL As String
Public strSQL1 As String
Public strSQL2 As String
Public strSQL3 As String
Public strSQL4 As String
Public strSQL5 As String


'Setting DATA RS
Public strNamaRS As String
Public strAlamatRS As String
Public strNoTlpn As String
Public strNamaPemerintah As String
Public strWebSite As String
Public strKodePos As String
Public stralmtLengkapRs As String
Public strEmail As String
Public strNamaKota As String
Public strNoFax As String
Public strNamaLengkapRs As String
Public stridRs As String

Public StatusCN As String
Public Sub openConnection()
' On Error GoTo NoConn
 Dim host, Port, username, password, database As String
   host = GetTxt2("Setting.ini", "Koneksi", "a")
   Port = GetTxt2("Setting.ini", "Koneksi", "b")
   username = GetTxt2("Setting.ini", "Koneksi", "c")
   password = GetTxt2("Setting.ini", "Koneksi", "d")
   database = GetTxt2("Setting.ini", "Koneksi", "e")
   CN_String = "DRIVER={PostgreSQL Unicode};" & _
               "SERVER=" & host & ";" & _
               "port=" & Port & ";" & _
               "DATABASE=" & database & ";" & _
               "UID=" & username & ";" & _
               "PWD=" & password & "": StatusCN = host
    With CN
        If .State = adStateOpen Then Exit Sub
        .CursorLocation = adUseClient
        
        '.ConnectionString = "DRIVER={PostgreSQL Unicode};" & _
                            "SERVER=192.168.12.1;" & _
                            "port=5432;" & _
                            "DATABASE=rsab_hk_production;" & _
                            "UID=postgres;" & _
                            "PWD=root": StatusCN = "192.168.12.1"
'        .ConnectionString = "DRIVER={PostgreSQL Unicode};" & _
'                            "SERVER=" & host & ";" & _
'                            "port=" & Port & ";" & _
'                            "DATABASE=" & database & ";" & _
'                            "UID=" & username & ";" & _
'                            "PWD=" & password & "": StatusCN = host
        .ConnectionString = CN_String
        .ConnectionTimeout = 10
        .Open

        If CN.State = adStateOpen Then
        '    Connected sucsessfully"
        Else
            MsgBox "Koneksi ke database error, hubungi administrator !" & vbCrLf & Err.Description & " (" & Err.Number & ")"
            End
            frmSettingKoneksi.Show vbModal
        End If
    End With
    
    If GetTxt2("Setting.ini", "app", "modul") = "pemanggil" Then
        frmPemanggil.Show
    End If
    Exit Sub
NoConn:
    MsgBox "Koneksi ke database error, ganti nama Server dan nama Database", vbCritical, "Validasi"
'    frmSettingKoneksi.Show vbModal
End Sub

Public Function ReadRs(sql As String)
  Set RS = Nothing
  RS.Open sql, CN, adOpenStatic, adLockReadOnly
End Function

Public Function ReadRs2(sql As String)
  If CN.State = adStateClosed Then Call openConnection
  Set RS2 = Nothing
  RS2.Open sql, CN, adOpenStatic, adLockReadOnly
End Function
Public Function ReadRs3(sql As String)
  Set RS3 = Nothing
  RS3.Open sql, CN, adOpenStatic, adLockReadOnly
End Function
Public Function ReadRs4(sql As String)
  Set RS4 = Nothing
  RS4.Open sql, CN, adOpenStatic, adLockReadOnly
End Function
Public Function ReadRs5(sql As String)
  Set RS5 = Nothing
  RS5.Open sql, CN, adOpenStatic, adLockReadOnly
End Function
Public Function WriteRs(sql As String)
  Set RS = Nothing
  RS.Open sql, CN, adOpenStatic, adLockOptimistic
End Function
Public Function WriteRs2(sql As String)
  Set RS2 = Nothing
  RS2.Open sql, CN, adOpenStatic, adLockOptimistic
End Function
Public Function tempSQLWebService(sql As String) As String
  Set myMSXML = CreateObject("Microsoft.XmlHttp")
    myMSXML.Open "GET", "http://localhost:8200/service/transaksi/temp/save-sql-from-vb6?sql=" + sql, False
    myMSXML.SetRequestHeader "Content-Type", "application/json"
    myMSXML.SetRequestHeader "X-AUTH-TOKEN", "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJhZG1pbi5sb2dpc3RpayJ9.amsHnk5s4cv1LvsIWY_fbq0NHBMomRQLUaY62GyvJm2QW0QCwgHxkYeRS918nGyhh6ovGr7Id4R_9JKQ3c66kA"
    myMSXML.Send
    tempSQLWebService = myMSXML.ResponseText
End Function
Function getNewNumber(tableName As String, fieldName As String, Keys As String)
Dim newKode As String
    ReadRs "select count(" & fieldName & ") from " & tableName
    newKode = Keys & 1
    If RS.RecordCount <> 0 Then
        newKode = Keys & (Val(RS(0)) + 1)
    End If
    getNewNumber = newKode
End Function

