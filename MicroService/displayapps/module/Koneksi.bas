Attribute VB_Name = "Koneksi"
Public CN As New ADODB.Connection
Public rs As New ADODB.Recordset
Public RS2 As New ADODB.Recordset
Public strSQL As String
Public strSQL2 As String
Public serverBackEnd As String

Public StatusCN As String

Public SKanan As String
Public SKiri As String

Public Sub openConnection()
On Error GoTo NoConn
 Dim host, port, username, password, database As String
   host = GetTxt("Setting.ini", "Koneksi", "a")
   port = GetTxt("Setting.ini", "Koneksi", "b")
   username = GetTxt("Setting.ini", "Koneksi", "c")
   password = GetTxt("Setting.ini", "Koneksi", "d")
   database = GetTxt("Setting.ini", "Koneksi", "e")
 
    With CN
        If .State = adStateOpen Then Exit Sub
        .CursorLocation = adUseClient
        
        CN_String = "DRIVER={PostgreSQL Unicode};" & _
                    "SERVER=" & host & ";" & _
                    "port=" & port & ";" & _
                    "DATABASE=" & database & ";" & _
                    "UID=" & username & ";" & _
                    "PWD=" & password & "": StatusCN = host
                
'       SQLSERVER
'       CN_String = "Provider=SQLOLEDB;Password=" & password & ";DataTypeCompatibility=80;Persist Security Info=True;" & _
'                            "User ID=" & username & "" & _
'                            ";Initial Catalog=" & database & "" & _
'                            ";Data Source=" & host & ""
        .ConnectionString = CN_String
        .ConnectionTimeout = 10
        .Open

        If CN.State = adStateOpen Then
        '    Connected sucsessfully"
        Else
            MsgBox "Koneksi ke database error, hubungi administrator !" & vbCrLf & Err.Description & " (" & Err.Number & ")"
            frmSettingKoneksi.Show vbModal
        End If
    End With
    

    Exit Sub
NoConn:
    MsgBox "Koneksi ke database error, ganti nama Server dan nama Database", vbCritical, "Validasi"
    frmSettingKoneksi.Show vbModal
End Sub
Sub Main()
   SKanan = 1
   SKiri = 1
   SKanan = GetTxt("Setting.ini", "SuaraAntrian", "kanan")
   SKiri = GetTxt("Setting.ini", "SuaraAntrian", "kiri")
    Call openConnection
    If CN.State = adStateOpen Then
        DisplayAntrian.Show
    Else
        End
    End If
End Sub
Public Function ReadRs(sql As String)
  Set rs = Nothing
  rs.Open sql, CN, adOpenStatic, adLockReadOnly
End Function

Public Function ReadRs2(sql As String)
    If CN.State = adStateClosed Then Call openConnection
  Set RS2 = Nothing
  RS2.Open sql, CN, adOpenStatic, adLockReadOnly
End Function

Public Function ReadJson(sql As String)
    Dim p As Object
    Dim strJason As String
    Set rs = Nothing
    strJason = GetResponse(serverBackEnd & "/desktopservice/get-data-for-rs?strsql=" + sql)
'    strJason = GetResponse(serverBackEnd & url & "?strsql=" + sql)
    Set p = JSON.parse(strJason)
    Set rs = Nothing
    If Val(p.Item("RecordCount")) = 0 Then Exit Function
'    MsgBox p.Item("RecordCount")
'    MsgBox p.Item("data").Item(1).Item("norec")
'    For Each Pp In p.Item("data").Item(1)
'        Debug.Print Pp
'        Debug.Print p.Item("data").Item(1).Item(Pp)
'    Next
   
   'cek type data
   'vartype(p.Item("data").Item(1).Item("objectruanganfk"))
'    Dim oRecs As Recordset
'    Dim RSA As New ADODB.Recordset
    Dim ff As Variant
    Dim cc As Variant
    Dim fff()  As String
    Dim ccc()  As String
    Dim i As Integer
    Dim ii As Integer
    Dim jmlKolom As Integer
     
'    rss.CursorLocation = adUseClient
    With rs.Fields
        i = 0
        For Each Pp In p.Item("data").Item(1)
            .Append Pp, adVarChar, 100
            i = i + 1
'            Debug.Print Pp
'            Debug.Print p.Item("data").Item(1).Item(Pp)
        Next
        jmlKolom = i
'       .Append "Field1", adInteger
'       .Append "Field2", adVarChar, 20
'       .Append "Field3", adDBTimeStamp
    End With
    rs.Open 'it opens as adOpenStatic, adLockBatchOptimistic
    For ii = 1 To Val(p.Item("RecordCount"))
        
        ReDim ff(1 To jmlKolom)
        i = 1
        For Each Pp In p.Item("data").Item(ii)
            ff(i) = Pp
            i = i + 1
        Next
         
        
        i = 1
        ReDim cc(1 To jmlKolom)
        For Each Pp In p.Item("data").Item(ii)
            If IsNull(p.Item("data").Item(ii).Item(Pp)) = False Then
                cc(i) = p.Item("data").Item(ii).Item(Pp)
            Else
                cc(i) = " "
            End If
            i = i + 1
        Next
        rs.AddNew
        For i = 1 To jmlKolom
            rs(ff(i)) = cc(i)
        Next
        rs.Update
        rs.MoveFirst
    Next
End Function
Public Function ReadJson2(sql As String)
    Dim p As Object
    Dim strJason As String
    strJason = GetResponse(serverBackEnd & "/desktopservice/get-data-for-rs?strsql=" + sql)
'    strJason = GetResponse(serverBackEnd & url & "?strsql=" + sql)
    Set p = JSON.parse(strJason)
    Set RS2 = Nothing
    If Val(p.Item("RecordCount")) = 0 Then Exit Function
'    MsgBox p.Item("RecordCount")
'    MsgBox p.Item("data").Item(1).Item("norec")
'    For Each Pp In p.Item("data").Item(1)
'        Debug.Print Pp
'        Debug.Print p.Item("data").Item(1).Item(Pp)
'    Next
   
   'cek type data
   'vartype(p.Item("data").Item(1).Item("objectruanganfk"))
'    Dim oRecs As Recordset
'    Dim RSA As New ADODB.Recordset
    Dim ff As Variant
    Dim cc As Variant
    Dim fff()  As String
    Dim ccc()  As String
    Dim i As Integer
    Dim ii As Integer
    Dim jmlKolom As Integer
     
'    rss.CursorLocation = adUseClient
    With RS2.Fields
        i = 0
        For Each Pp In p.Item("data").Item(1)
            .Append Pp, adVarChar, 100
            i = i + 1
'            Debug.Print Pp
'            Debug.Print p.Item("data").Item(1).Item(Pp)
        Next
        jmlKolom = i
'       .Append "Field1", adInteger
'       .Append "Field2", adVarChar, 20
'       .Append "Field3", adDBTimeStamp
    End With
    RS2.Open 'it opens as adOpenStatic, adLockBatchOptimistic
    For ii = 1 To Val(p.Item("RecordCount"))
        
        ReDim ff(1 To jmlKolom)
        i = 1
        For Each Pp In p.Item("data").Item(ii)
            ff(i) = Pp
            i = i + 1
        Next
         
        
        i = 1
        ReDim cc(1 To jmlKolom)
        For Each Pp In p.Item("data").Item(ii)
            If IsNull(p.Item("data").Item(ii).Item(Pp)) = False Then
                cc(i) = p.Item("data").Item(ii).Item(Pp)
            Else
                cc(i) = " "
            End If
            i = i + 1
        Next
        RS2.AddNew
        For i = 1 To jmlKolom
            RS2(ff(i)) = cc(i)
        Next
        RS2.Update
        RS2.MoveFirst
    Next
End Function


