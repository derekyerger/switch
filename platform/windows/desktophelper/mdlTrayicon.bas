Attribute VB_Name = "mdlTrayicon"
Option Explicit
Public Declare Function Shell_NotifyIcon Lib "shell32.dll" Alias "Shell_NotifyIconA" (ByVal dwMessage As Long, lpData As NOTIFYICONDATA) As Long
Public Const NIM_ADD = &H0
Public Const NIM_DELETE = &H2
Public Const NIM_MODIFY = &H1
Public Const NIF_ICON = &H2
Public Const NIF_MESSAGE = &H1
Public Const NIF_TIP = &H4
Public Const WM_LBUTTONDBLCLK = &H203
Public Const WM_LBUTTONDOWN = &H201
Public Const WM_LBUTTONUP = &H202
Public Const WM_RBUTTONDBLCLK = &H206
Public Const WM_RBUTTONDOWN = &H204
Public Const WM_RBUTTONUP = &H205
Public Const WM_MOUSEMOVE = &H200

Public Type NOTIFYICONDATA
        cbSize As Long
        hwnd As Long
        uID As Long
        uFlags As Long
        uCallbackMessage As Long
        hIcon As Long
        szTip As String * 64
End Type

Sub AddIcon(OwnerForm As Form, ReturnMessage As Long, ToolTip As String)
Dim icondata As NOTIFYICONDATA
icondata.cbSize = Len(icondata)
icondata.hwnd = OwnerForm.hwnd
icondata.uID = vbNull
icondata.uFlags = NIF_ICON Or NIF_TIP Or NIF_MESSAGE
icondata.uCallbackMessage = ReturnMessage
icondata.hIcon = OwnerForm.Icon
icondata.szTip = ToolTip & Chr$(0)
Shell_NotifyIcon NIM_ADD, icondata
End Sub

Sub RemoveIcon(OwnerForm As Form)
Dim icondata As NOTIFYICONDATA
icondata.cbSize = Len(icondata)
icondata.hwnd = OwnerForm.hwnd
icondata.uID = vbNull
icondata.uFlags = NIF_ICON Or NIF_TIP Or NIF_MESSAGE
icondata.uCallbackMessage = WM_RBUTTONDBLCLK
icondata.szTip = Chr$(0)
Shell_NotifyIcon NIM_DELETE, icondata
End Sub

Sub ChangeIcon(OwnerForm As Form, ReturnMessage As Long, ToolTip As String)
Dim icondata As NOTIFYICONDATA
icondata.cbSize = Len(icondata)
icondata.hwnd = OwnerForm.hwnd
icondata.uID = vbNull
icondata.uFlags = NIF_ICON Or NIF_MESSAGE Or NIF_TIP
icondata.uCallbackMessage = ReturnMessage
icondata.hIcon = OwnerForm.Icon
icondata.szTip = ToolTip & Chr$(0)
Shell_NotifyIcon NIM_MODIFY, icondata
End Sub
