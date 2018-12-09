Attribute VB_Name = "mdlTransparency"
Private Declare Function GetWindowLong Lib "user32" Alias _
    "GetWindowLongA" (ByVal hwnd As Long, ByVal nIndex As Long) As Long
Private Declare Function SetWindowLong Lib "user32" _
    Alias "SetWindowLongA" (ByVal hwnd As Long, ByVal _
    nIndex As Long, ByVal dwNewLong As Long) As Long
Private Declare Function SetLayeredWindowAttributes Lib _
    "user32" (ByVal hwnd As Long, ByVal color As _
    Long, ByVal bAlpha As Byte, _
    ByVal alpha As Long) As Boolean

Private Const WS_EX_LAYERED = &H80000
Private Const GWL_EXSTYLE = (-20)
Private Const LWA_ALPHA = &H2&

Public Sub SetTranslucent(ThehWnd As Long, nTrans As Integer)
On Error GoTo ErrorRtn

   Dim attrib As Long

   'put current GWL_EXSTYLE in attrib

   attrib = GetWindowLong(ThehWnd, GWL_EXSTYLE)

   'change GWL_EXSTYLE to WS_EX_LAYERED - makes a window layered

   SetWindowLong ThehWnd, GWL_EXSTYLE, attrib Or WS_EX_LAYERED

   'Make transparent (RGB value does not have any effect at this

   'time, will in Part 2 of this article)

   SetLayeredWindowAttributes ThehWnd, RGB(0, 0, 0), nTrans, _
                                       LWA_ALPHA
   Exit Sub

ErrorRtn:
MsgBox Err.Description & " Source : " & Err.Source

End Sub



