VERSION 5.00
Begin VB.Form frmDH 
   BorderStyle     =   4  'Fixed ToolWindow
   Caption         =   "AID Desktop Helper"
   ClientHeight    =   3730
   ClientLeft      =   17300
   ClientTop       =   590
   ClientWidth     =   4180
   Icon            =   "frmDH.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   Picture         =   "frmDH.frx":1085C
   ScaleHeight     =   3730
   ScaleWidth      =   4180
   ShowInTaskbar   =   0   'False
   Begin VB.Timer Timer1 
      Interval        =   100
      Left            =   3000
      Top             =   360
   End
   Begin VB.Menu mnuTray 
      Caption         =   "dropdown tray"
      Visible         =   0   'False
      Begin VB.Menu mnuApplication 
         Caption         =   "Application"
         Enabled         =   0   'False
      End
      Begin VB.Menu mnuSep 
         Caption         =   "-"
      End
      Begin VB.Menu mnuSC 
         Caption         =   "Show Configuration"
      End
      Begin VB.Menu mnuCancel 
         Caption         =   "Cancel"
      End
      Begin VB.Menu mnuExit 
         Caption         =   "Exit"
      End
   End
End
Attribute VB_Name = "frmDH"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
Private Declare Function GetForegroundWindow Lib "user32" () As Long
Private Declare Function GetWindowText Lib "user32" Alias "GetWindowTextA" (ByVal hwnd As Long, ByVal lpString As String, ByVal cch As Long) As Long
Private Declare Function SetWindowPos Lib "user32" (ByVal hwnd As Long, ByVal hWndInsertAfter As Long, ByVal X As Long, ByVal y As Long, ByVal cx As Long, ByVal cy As Long, ByVal wFlags As Long) As Long
Private Declare Function ReleaseCapture Lib "user32" () As Long
Private Declare Function SetCapture Lib "user32" (ByVal hwnd As Long) As Long
Private Declare Function FindWindow Lib "user32" Alias "FindWindowA" (ByVal lpClassName As String, ByVal lpWindowName As String) As Long
Private Declare Function SetForegroundWindow Lib "user32" (ByVal hwnd As Long) As Long
Private Declare Function SetFocus2 Lib "user32" Alias "SetFocus" (ByVal hwnd As Long) As Long
Private Declare Function BringWindowToTop Lib "user32" (ByVal hwnd As Long) As Long

Private Declare Function GetActiveWindow Lib "user32" () As Long
Private Declare Function SendMessage Lib "user32" Alias "SendMessageA" (ByVal hwnd As Long, ByVal wMsg As Long, ByVal wParam As Long, lParam As Any) As Long

Const HWND_BOTTOM = 1
Const HWND_BROADCAST = &HFFFF&
Const HWND_DESKTOP = 0
Const HWND_NOTOPMOST = -2
Const HWND_TOP = 0
Const HWND_TOPMOST = -1
Const SWP_HIDEWINDOW = &H80
Const SWP_NOACTIVATE = &H10
'Public Const SWP_NOCOPYBITS = &H100
Const SWP_NOMOVE = &H2
'Public Const SWP_NOOWNERZORDER = &H200
'Public Const SWP_NOREDRAW = &H8
Const SWP_NOSIZE = &H1
'Public Const SWP_NOZORDER = &H4
Const SWP_SHOWWINDOW = &H40
'Public Const SWP_NOREPOSITION = SWP_NOOWNERZORDER
'Public Const SWP_DRAWFRAME = SWP_FRAMECHANGED


Private Sub Form_Load()
Me.Show
SetTranslucent Me.hwnd, 220
Me.WindowState = 0
AddIcon Me, WM_MOUSEMOVE, "AID Desktop Helper"
End Sub

Private Sub Form_MouseMove(Button As Integer, Shift As Integer, X As Single, y As Single)
If X = WM_LBUTTONDBLCLK Then
  If WindowState = 0 Then WindowState = 1: Me.Hide Else WindowState = 0: Me.Show
End If
Debug.Print X, WM_RBUTTONDOWN, WM_RBUTTONUP
If X = WM_RBUTTONDOWN Or X = WM_RBUTTONUP Or X = 5170 Then
  PopupMenu mnuTray, vbPopupMenuRightButton, , , mnuExit
End If
End Sub

Private Sub Form_Unload(Cancel As Integer)
RemoveIcon Me
End Sub

Private Sub mnuExit_Click()
Unload Me
End Sub

Private Sub Timer1_Timer()
Dim s As String
s = Space(256)
Call GetWindowText(GetForegroundWindow(), s, 256)
nm = Mid(Trim(s), 1, Len(Trim(s)) - 1)
If nm <> "" Then mnuApplication.Caption = nm
If nm = "Chess Titans" Then
    If Me.WindowState <> 0 Then
        Me.WindowState = 0
        Call SetWindowPos(Me.hwnd, HWND_TOPMOST, 0, 0, 0, 0, SWP_NOSIZE Or SWP_NOMOVE Or SWP_SHOWWINDOW)
    End If
    mnuApplication.Enabled = True
Else
    If Me.WindowState <> 1 Then
        Me.WindowState = 1
        Call SetWindowPos(Me.hwnd, HWND_NOTOPMOST, 0, 0, 0, 0, SWP_NOSIZE Or SWP_NOMOVE Or SWP_NOACTIVATE Or SWP_HIDEWINDOW)
    End If

    If nm <> "" Then mnuApplication.Enabled = False
End If
Debug.Print nm
End Sub
