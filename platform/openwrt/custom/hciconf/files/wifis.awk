/^BSS/ { 
	if (ssid) { print bss " " psk " " ssid }
	psk = "None"; ssid = ""; bss = substr($0, 5, 17); }
/PSK/ { psk = "WPA" }
/802.1X/ { ssid = "" }
/SSID: ./ { ssid = substr($0, index($0, $2)) }
