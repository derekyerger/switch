/* Copyright (c) 2018 by Derek Yerger. All Rights Reserved. */

var spinHandle = null;
var programming = '';
var deviceData = '';
var helpSeq, oldNavBrand, helpHandle = null;
var pingCt = 0;
var lastCmds = [];
var actionStack;
var rev = false;
var ajaxRetFn;
var ws;
var beginner;

function spinnerStart() {
	$('.progress-overlay').show();
}

function spinnerEnd() {
	$('.progress-overlay').hide();
}

function doText(e) {
	var i=$('#keys');
	i.val(i.val()+e);
}

function getPart() {
	var curSensor = locS[$("#ddLocation").text()];
	var curImpulse = pS[$("#ddImpulse").text()];

	var s = 0;
	var a = "";
	var z = programming;
	var pl = "";
	for (l in z) {
		if (s == 0) {
			s++;
			if (z[l] != curSensor) s = 9;
		} else if (s == 1) {
			s++;
			if (z[l] != curImpulse) s = 9;
		} else if (s == 2) {
			if (z[l] == ";" && pl != "\\") break;
			a = a + z[l];
		} else if (z[l] == ";" && pl != "\\") s = 0;
		if (pl == "\\" && z[l] == "\\")
			pl = "";
		else pl = z[l];
	}
	return a;
}

function setPart(newKeys) {
	$("#actionDlg").modal("hide");
	if (newKeys == "%%")
		newKeys = $("#keys").val().replace(";", "\\;");
	if (newKeys == "") newKeys=";";

	var curSensor = locS[$("#ddLocation").text()];
	var curImpulse = pS[$("#ddImpulse").text()];

	var s = 0;
	var f = 0;
	var a = "";
	var z = programming;
	for (l in z) {
		if (s == 0 && z[l] == ";") continue;
		if (s != 2) a = a + z[l];
		if (s == 0) {
			s++;
			if (z[l] != curSensor) s = 9;
		} else if (s == 1) {
			s++;
			if (z[l] != curImpulse)
				s = 9;
			else {
				a = a + newKeys + "\;";
				f = 1;
			}
		} else if (z[l] == ";" && pl != "\\") s = 0;
		pl = z[l];
	}
	if (!f) a = a + curSensor + curImpulse + newKeys + "\;";

	programming = a;

	retr("save", programming);
	ddSet("none", "");
}

function template(pstring) {
	programming = pstring;
	retr("save", programming);
	swal({
		title: "Template applied",
		text: "The selected template has been applied to the device.",
		type: "success",
		showCancelButton: true,
		cancelButtonClass: "btn-lime",
		cancelButtonText: "Customize"},
	function(x) {
		if (!x) retr("page", "Home.assignments");
	});
}

$(document).ready(function() {
	$('.navbar-collapse').click('li', function() {
		$('.navbar-collapse').collapse('hide');
	});
	programming = decodeProg()['programming'];
	var port = '7001';
	if (/altdevs.net/.test(window.location.hostname)) port = '7101';
	setupPing(port);
	$('[data-toggle="tooltip"]').tooltip();
});

function setupPing(port) {
	try { /* Use websockets to receive pushed pings if possible */
		var proto = "ws";
		if (port > 7100) proto = "wss";
		if (!ws) ws = new WebSocket(proto + "://" + window.location.hostname + ":" + port + "/tty");
		ws.onmessage = function(msg) { ping(msg.data); };
	} catch (e) { /* Fall back to AJAX polling */
		doListen();
	}
}

function delBond() {
	swal({
		title: "Bluetooth Bonding",
		text: "Are you sure you want to remove any associations to a paired device?",
		type: "warning",
		showCancelButton: true,
		cancelButtonClass: "btn-lime",
		closeOnConfirm: false},
	function(x) {
		if (x) {
			retr("delbond");
			ajaxRetFn = 'swal("Removed Bonding", "Any stored bonding information has been removed.", "success");';
		}
	});
}

function retr(cmd, data) {
	clearTimeout(spinHandle);
	spinHandle = setTimeout(spinnerStart, 150); /* Only start spinner if we're taking too long */
	var txObj = { f: cmd, d: data };
	$.post( "/index.php", txObj)
		.done(function(rxObj) {
			clearTimeout(spinHandle);
			spinnerEnd();

			if (cmd == "page") {
				if ($(".navbar-toggle:visible").length && $(".page-sidebar-toggled").length)
					$('.navbar-toggle').click();

				unping();
				if (data != "Home.status" && (pressureSlider.length > 0 || pressureChart !== null)) {
					retr("stopMonitor", null);
					pressureSlider = [];
					pressureChart = null;
				}
			}
			
			if (cmd == "get") {
				
				ddSet("ddLocation", locMap[ rxObj[1] ]);
				ddSet("ddImpulse", pMap[ rxObj[2] ]);

			} else if (cmd == "getHelp") {

				if ($("#cancelHelp").length == 0) return;

				if (rxObj[2] == 2) {
					cancelHelp();
					return;
				}

				rev = (rxObj[1] == 2);

				if (rxObj[1] == 2)
					helpSeq(data - (data == 1 ? 0 : 1));
				else if (rxObj[1] == 1) helpSeq(data + 1);
				else {
					retr("getHelp", data);
					clearTimeout(spinHandle);
				}

			} else if (cmd == "save" && $("#ddProfile").length && $("#ddProfile").html() != "Profile") {

				retr("saveProfile", { name: $("#ddProfile").html(), data: programming });

			} else eval(rxObj);
			
			if (cmd == "page") $(".content button:first").focus();

			if (ajaxRetFn && cmd != "getHelp") {
				var t = ajaxRetFn;
				ajaxRetFn = null;
				eval(t);
			}
	});
}

var pingHandle = null;
var pressureSlider = [];
var pressureChart = null;
var pressureCount = 0;
var softP;
var hardP;
var doPlot;

function ping(a) {
	if (a.substr(0,1) == ">") { /* Light up the visual */
		a = a.substr(1);
		if ($(".responsive-device").length) {
			/* Visual exploding ping animation */
			$(".responsive-device").append('<div id="ping' + ++pingCt + '" class="dot dot' + a[0] + '"></div>');
			
			/* Tooltip */
			clearTimeout(pingHandle);
			$(".dot").tooltip("hide");
			$("#ping" + pingCt).attr('data-original-title', pMap[ a[1] ]).attr('data-trigger', 'manual').attr('data-placement', "bottom").attr('data-animation', 'true').tooltip("show");
			
			/* Remove after 3s */
			pingHandle = setTimeout(unping, 3000);
		}
		
		/* Update list of commands */
		lastCmds.push(a);
		if (lastCmds.length > 10) lastCmds.shift();
		populateLastCmds();

		if ($('.note h4:contains("Blank device detected")').parents('.note').hide(300).length) {
			/* Welcome mode */
			retr("component", "command-history");
			setTimeout(function() {
				$('.note h4:contains("Blank device detected")').parents('.note').remove();
				$("#ping" + pingCt).attr('data-original-title', pMap[ a[1] ]).attr('data-trigger', 'manual').attr('data-placement', "bottom").attr('data-animation', 'true').tooltip("show");
				beginner = true;
			}, 300);
		}
	} else if (a.substr(0,1) == "!") { /* Display pressure */
		if (pressureSlider.length > 0) {
			pressureSlider[0].setStart(parseInt(a.substr(1,2), 16)/255*100);
			if (a.length > 3) {
				pressureSlider[1].setStart(parseInt(a.substr(3,2), 16)/255*100);
				$('.slider-wrapper.blue').show();
			}
		} else if ((chart = $('#pressure-chart')).length == 1) {
			if (pressureChart === null) {
				if (a.length > 3) pressureChart = [
					{ label: 'Left', color: COLOR_GREEN, lines: {show:true}, data: []  },
					{ label: 'Left Peak', color: COLOR_GREEN, points: {show:true}, data: []  },
					{ label: 'Right', color: COLOR_BLUE, lines: {show:true}, data: []  },
					{ label: 'Right Peak', color: COLOR_BLUE, points: {show:true}, data: []  },
				];
				else pressureChart = [{ label: 'Sensor', color: COLOR_GREEN, data: []  }];
			}
			pressureChart[0]['data'].push([pressureCount, parseInt(a.substr(1,2), 16)])
			if (pressureChart[0]['data'].length > 40) {
				c = pressureChart[0]['data'].shift();
				while (pressureChart[1]['data'].length > 0 && pressureChart[1]['data'][0][0] <= c[0]) pressureChart[1]['data'].shift()
			}
			if (a.length > 3) {
				pressureChart[2]['data'].push([pressureCount, parseInt(a.substr(3,2), 16)])
				if (pressureChart[2]['data'].length > 40) {
					c = pressureChart[2]['data'].shift();
					while (pressureChart[3]['data'].length > 0 && pressureChart[3]['data'][0][0] <= c[0]) pressureChart[3]['data'].shift()
				}
				
			}
			pressureCount++;
			doPlot();
		}
	} else if (a.substr(0,1) == "@") { /* Impulse */
		if ((chart = $('#pressure-chart')).length == 1) {
			pressureChart[1]['data'].push([pressureCount, parseInt(a.substr(1,2), 16)])
		}
	} else if (a.substr(0,1) == "#") { /* Impulse */
		if ((chart = $('#pressure-chart')).length == 1) {
			pressureChart[3]['data'].push([pressureCount, parseInt(a.substr(1,2), 16)])
		}
	} else if (a.substr(0,1) == "^") { /* Adjust pressures */
		if ((chart = $('#pressure-chart')).length == 1) {
			softP = parseInt(a.substr(1,2), 16);
			hardP = parseInt(a.substr(3,2), 16);
		}
	} else if (a == "Z") {
		swal("Sleep", "The device has gone to sleep. Press and hold it for 8 seconds to wake up.", "warning");
	} else if (a == "P") {
		$('.gritter-item-wrapper').remove();
		$.gritter.add({
			title: 'Running on battery',
			text: 'The device is now running on battery, and will go to sleep after a period of inactivity.',
			image: 'img/battery.png',
			sticky: false,
			time: ''
		});
	} else if (a == "p") {
		$('.gritter-item-wrapper').remove();
		$.gritter.add({
			title: 'Charging',
			text: 'The device is now externally powered, and the battery is charging.',
			image: 'img/charging.png',
			sticky: false,
			time: ''
		});
	}

}

function populateLastCmds() {
	if (!$(".responsive-device-txt").length) return;
	var s = "";
	for (var c in lastCmds) {
		actionStack = [];
		var m = seekAction(actionMap, lastCmds[c].substr(2));
		if (m || lastCmds[c].substr(2) !== ";") {
			//s = "<p>" + locMapImg[ lastCmds[c][0] + lastCmds[c][1] ] + " " + (m ? m : friendlyKeys(lastCmds[c].substr(2, -1)) ) + "</p>" + s;
			s = '<div class="btn-group"><p>' + locMapImg[ lastCmds[c][0] + lastCmds[c][1] ] +
			' &nbsp; ' + (m ? '<button class="btn disabled btn-outline-light btn-xs">' + m + '</button>' : friendlyKeys(lastCmds[c].substr(2))) + 
			'<button onclick="javascript:proxyAssign(\'' + lastCmds[c][0] + lastCmds[c][1] + '\');" class="btn btn-lime btn-xs">Reassign</button></p></div><br/>' + s;
		} else {
			s = '<div class="btn-group"><p>' + locMapImg[ lastCmds[c][0] + lastCmds[c][1] ] +
			' &nbsp; <button class="btn disabled btn-outline-secondary btn-xs">Unassigned</button><button onclick="javascript:proxyAssign(\'' + lastCmds[c][0] + lastCmds[c][1] + '\');" class="btn btn-primary btn-xs">Assign to action</button></p></div><br/>' + s;
		}
	}
	$(".responsive-device-txt").html(s);
}

function proxyAssign(cmd) {
	// if (beginner) $('.panel-title:contains("Device visual")').parents('.panel').children('.panel-body').hide(300);
	if (!$('.panel-title:contains("Input assignment")').length) retr('component', 'input-assignment');
	setTimeout(function() {
		if (beginner) $('.panel-title:contains("Input assignment")')[0].scrollIntoView();
		ddSet("ddLocation", locMap[ cmd[0] ]);
		ddSet("ddImpulse", pMap[ cmd[1] ]);
		$("#actionDlg").modal("show");
	}, 300);
}

function unping() {
	clearTimeout(pingHandle);
	$(".dot").tooltip("hide");
}

function doListen() {
	$.post( "/index.php", { f: "poll" })
		.done(function(rxObj) {
			if (rxObj) {
				ping(rxObj);
				
			}
			if ($(".responsive-device-txt").html(s).length) setTimeout(doListen, 1000);
	});
}

function get() {
	retr("get", null);
}

function save() {
	retr("commit", null);
}

function decodeProg() {
	var r = {};
	deviceData.split("&").forEach(function(k) {
		k = k.split("=");
		r[k[0]] = decodeURIComponent(k[1] || '');
	});
	return r;
}

function popClicks() {
	/* Generate keymaps */
	var ol = $("#keyboard")[0].offsetLeft;
	var ot = $("#keyboard")[0].offsetTop;
	var sf = $("#keyboard")[0].width / 650;

	var gen = '';
	var rows = { 8: [ 38.3, "~BCDEFGHIJKLM  QT" ],
			 42: [ 42, "\~1234567890-\+_R" ],
			 84: [ 42, "!qwertyuiop[]\\\\S" ],
			126: [ 42, "Aasdfghjkl;\'|V" ],
			167: [ 42, "+zxcvbnm,./+ZU" ],
			208: [ 42, "^&&% % ^XYW" ] };
	var keyMap = {};
	for (var top in rows) {
		var row = rows[top];
		var width = row[0];
		var rowMap = [];
		var key;
		for (var p = 0; p < row[1].length; p++) {
			if ((k = row[1].substr(p, 1)) == "\\") {
				key = "\\" + row[1].substr(p, 2);
				if (key == "\\\\\\") key += "\\";
				p++;
			} else key = k;
			if (!rowMap[key])
				rowMap[key] = width;
			else
				rowMap[key + '\''] = width;
		}
		keyMap[top] = rowMap;
	}
	keyMap[42]["_"] = keyMap[84]["!"] = 60;
	keyMap[126]["A"] = 72;
	keyMap[126]["|"] = 74;
	keyMap[167]["+"] = 80;
	keyMap[167]['+\''] = 65;
	keyMap[208][" "] = 228;
	for (var top in keyMap) {
		var rowMap = keyMap[top];
		var left = 0;
		for (var key in rowMap) {
			var width = rowMap[key];
			// if (preg_match("/^.'$/", $key) === 1) $key = substr($key, 0, 1);
			gen += '<a href="javascript:void(0);" onclick="doText(\'' + key + '\');">' + 
				"<div class='box' style='top:" + (top*sf+ot) + "px;left:" +
				(left*sf+ol) + "px;width:" + width*sf + "px;height:" + 40*sf + "px'></div></a>";
			left += width;
		}
	}
	$("#keymap").html(gen);
}

function activateElt(p) {
	switch (p) {
		case "visual":
			populateLastCmds();
			break;

		case "assignment":
			$("#actionDlg").on("shown.bs.modal", function() {
				if ($(".ddActs").length == 1) $("#as1").dropdown("toggle");
				popClicks();
			});
			break;
		
		case "interface":
			$("#bluetooth label").removeClass("active");
			$("#bluetooth input[value=" + decodeProg()['bluetooth'] + "]").parent().addClass("active");
			$("#bluetooth input").on("change", function(e) {
				clearTimeout(spinHandle);
				spinHandle = setTimeout(spinnerStart, 150); /* Only start spinner if we're taking too long */
				$.post("/tweak.php", {pk: encodeId(e.target.name), value: e.target.value})
					.done(function(rxObj) {
						clearTimeout(spinHandle);
						spinnerEnd();
						swal("Interface switched", "The output interface has been changed to " + (e.target.value === "1" ? "Bluetooth" : "USB"), "success");
				});
			});
			break;

		case "clisettings":
			$("#clientDlg").on("shown.bs.modal", function() {
				var v = $("#clientDlg").data();
				$("#cli_ssid").val( v.s );
				$("#key").val('');
			});
			break;

		case "apsettings":
			$("#apDlg").on("shown.bs.modal", function() {
				$("#ssid").val( $("#currentAP").val() );
				$("#key").val( $("#currentPSK").val() );
			});
			break;
	}
	
	$('[data-toggle="tooltip"]').tooltip()
}

function encodeId(id) {
	var ids = {};
	ids['sensorCount'] = "0";
	ids['hardPress'] = "1";
	ids['softPress'] = "2";
	ids['longPress'] = "3";
	ids['sampleInterval'] = "4";
	ids['settleTime'] = "5";
	ids['debounceTime'] = "6";
	ids['avgWindow'] = "7";
	ids['pressureBias'] = "8";
	ids['minGroup'] = "9";
	ids['enableAdjust'] = "10";
	ids['bluetooth'] = "11";
	ids['batterySave'] = "12";
	ids['programming'] = "13";
	return ids[id];
}

function ddSet(id, txt) {
	$("#" + id).html(txt);
	switch (id) {
		case "ddPlatform":
			actionMap = $.extend({}, platformMap['*'], platformMap[txt]);
			/* no break because the dialog muust be rebuilt with the new options */

		default:
			if ( $("#ddLocation").html() != "Location"
				&& $("#ddImpulse").html() != "Impulse") {
				$("#ddAction").removeClass("disabled");
				$('#ddAction').attr("data-toggle", "modal");
				$("#popupKbd").hide();
				/* Find current assignment */

				var ca = getPart();
				if (ca) {
					actionStack = [];
					var cn = seekAction(actionMap, ca);
					var c = 0;
					buildAction();
					if (!actionStack.length) {
						nextAction(1, "Custom Keyboard Input");
						$("#popupKbd").show();
						popClicks();
						$("#keys").val(ca);
					} else while (actionStack.length)
						nextAction(++c, actionStack.pop());

					$("#ddAssignment").removeClass("btn-outline-danger disabled").addClass("btn-info").text(cn ? cn : "Custom Key Sequence").attr("data-toggle", "modal");
				} else {
					$("#ddAssignment").addClass("btn-outline-danger disabled").removeClass("btn-info").text("Unassigned").removeAttr("data-toggle");
					buildAction();
				}
			}
	}
}

function seekAction(set, keys) {
	if (typeof set != "object")
		return set == keys;
	
	var j;
	for (var k in set)
		if (j = seekAction(set[k], keys)) {
			actionStack.push(k);
			return typeof j == "string" ? j : k;
		}

	return false;
}

function buildAction() {
	var t, v=0;
	/* Top level */
	t = '<div class="dropright"><button class="btn btn-info dropdown-toggle btn-row" type="button" id="as1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"/>' +
		'<div class="dropdown-menu ddActs" aria-labelledby="as1">';
	for (var m in actionMap)
		t += '<a id="dd' + v++ + '" class="dropdown-item" href="javascript:void(0)" onclick="nextAction(1, \'' + m + '\');">' + m + '</a>';
	t += "</div></div>"
	$("#actionSelect").html(t);
	$(".dropdown-toggle").dropdown();
	$(document).on('click', '.ddActs', function (e) {
		e.stopPropagation();
	});
}

function nextAction(c, a) {
	var r, s, t = '<div class="row"><div class="col-lg-10 col-md-9 col-sm-8"><div class="btn-group">', u, v=0;
	u = actionMap;
	$("#popupKbd").hide();

	for (r = 1; r <= c; r++)
		if (s = (r == c ? a : $("#as" + r).html())) {
			t += '<div class="dropright"><button class="btn btn-outline-light dropdown-toggle btn-sm" type="button" id="as' + r + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
				s + '</button><div class="dropdown-menu ddActs" aria-labelledby="as' + r + '">';
			for (var m in u)
				t += '<a class="dropdown-item" href="javascript:void(0)" onclick="nextAction(' + r + ', \'' + m + '\');">' + m + '</a>';
			t += "</div></div>"
			u = u[s];
		} else break;
	if (typeof u == "object") {

		t += '<div class="dropright"><button class="btn btn-info dropdown-toggle" type="button" id="as' + ++c + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"/>' +
			'<div class="dropdown-menu ddActs" aria-labelledby="as' + c + '">';
		for (var m in u)
			t += '<a id="dd' + v++ + '" class="dropdown-item" href="javascript:void(0)" onclick="nextAction(' + c + ', \'' + m + '\');">' + m + '</a>';
		t += "</div></div></div></div></div>"
		$("#asSave").removeClass("btn-info").addClass("disabled btn-primary");

	} else {
		if (u == "%%" ) {
			t += '<button type="button" class="btn btn-info btn-sm btn-assign ddActs" id="as' + ++c + '" onclick="setPart(\'' + u + '\');">' +
				'Assign</button></div></div></div>';
			$("#popupKbd").show();
			$("#keys").val("");
			popClicks();
		} else
			t += '<button type="button" class="btn btn-info btn-sm btn-assign ddActs" id="as' + ++c + '" onclick="setPart(\'' + u + '\');">' +
				'Assign</button></div></div></div><div class="row"><div id="keyRep" class="col-lg-10 col-md-9 col-sm-8">' +
				"<p>Keys: " + friendlyKeys(u) + "</p></div></div>";
		$("#asSave").removeClass("disabled btn-primary").addClass("btn-info");
	}

	$("#actionSelect").html(t);
	$(".dropdown-toggle").dropdown();
	if (typeof u == "object")
		$("#as" + c).dropdown("toggle");

}

function friendlyKeys(k) {
	var t = "";
	for (var i in k) {
		var n = k[i];
		if (keyMap[n]) {
			if (keyMap[n].substr(keyMap[n].length - 1) == "+")
				n = "<kbd>" + keyMap[n].substr(0, keyMap[n].length - 1) + "</kbd> + ";
			else n = "<kbd>" + keyMap[n] + "</kbd>";
		} else n = "<kbd>" + n + "</kbd> ";
		t += n;
	}
	return t;
}

function helpSeq(id) {
	var placement;
	if (id == 0) rev = false;

	clearTimeout(helpHandle);
	$(".lastHelpTooltip").tooltip("hide").removeClass("lastHelpTooltip");

	if (!helpMap[id]) {
		cancelHelp();
		return;
	}
	
	if ($("#cancelHelp").length == 0) {
		oldNavBrand = $(".navbar-brand").html();
		$(".navbar-brand").html('<button id="cancelHelp" type="button" class="btn btn-outline-warning" onclick="cancelHelp();">Cancel Help</button>');
		$("#cancelHelp").focus();
	}

	for (var a in helpMap[id])
		switch (a) {
			case 'a': /* do on ajax return */
				ajaxRetFn = "helpSeq(" + (id + (rev ? -1 : 1)) + ");"
				break;

			case 'er': /* exec immediately */
				if (rev) eval(helpMap[id][a]);
				break;

			case 'e': /* exec immediately */
				if (!rev) eval(helpMap[id][a]);
				break;

			case 't':
				break;

			case 'p':
			case 'p2':
			case 'p3':
				placement = helpMap[id][a];
				break;

			default: /* Apply tooltip */
				if (placement) $(a).attr('data-placement', placement);
				$(a).attr('data-original-title', helpMap[id][a]).attr('data-animation', 'true').attr('data-trigger', 'manual').addClass("lastHelpTooltip").tooltip({"template":'<div class="tooltip tooltip-help" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'}).tooltip("show");
		}

	if (!helpMap[id]['a']) {
		if (helpMap[id]['t'])
			helpHandle = setTimeout(helpSeq.bind(null, id + (rev ? -1 : 1)), helpMap[id]['t'] * 1000);
		else retr("getHelp", id);

		clearTimeout(spinHandle);
	}
}

function cancelHelp() {
	clearTimeout(helpHandle);
	$(".lastHelpTooltip").tooltip("hide").removeClass("lastHelpTooltip");
	$(".navbar-brand").html(oldNavBrand);
}

function debugStuff(msg) {
	if (msg.data[0] != "c") return;
	var t = "";
	var p = msg.data.split(";");
	var q = p[0].split(",");
	t += "<p><font color='#666'><br>Replace " + q[0].substr(1) + " with value " + q[1] + " at position " + q[2] + "</font><br>";
	for (var z in p[1])
		if (z % 2 === 1)
			switch (p[1][z-1] + p[1][z]) {
				case "ss":
					t += "<font color='orange'>(softP)</font> ";
					break;
				case "hh":
					t += "<font color='orange'>(hardP)</font> ";
					break;
				default:
					t += parseInt(p[1][z-1] + p[1][z], 16) + " ";
			}

	q = p[2].split(",");
	t += "<br><font color='#ccc'>Medians:<br>softP <--- (n=" + q[0];
	if (q[1]) t += ") x͂=" + q[1];
	else t += ")";
	
	t += " ---> hardP <--- (n=" + q[2];
	if (q[3]) t += ") x͂=" + q[3];
	else t += ")";

	t += "</font><br><h3>";

	if (p[3]) {
		q = p[3].split(",");
		t += "Set softP to " + q[0] + " and hardP to " + q[1];
	} else {
		if (decodeProg()['enableAdjust'] == "1") t += "Insufficient data to adjust";
		else t += "Calibration disabled";
	}
	$(".responsive-debug").html(t + "</h3></p>"); // + $(".responsive-debug").html());
}

function profileAdd() {
	if ($("#profName").val() == "") {
		window.alert("Please enter a unique identifier");
		return;
	}
	retr("saveProfile", { name: $("#profName").val(), data: programming });
	$("#profileSel").append('<a class="dropdown-item" href="javascript:void(0)" onclick="retr(\'getProfile\', \'' + $("#profName").val() + '\');">' + $("#profName").val() + '</a>');
	ddSet("ddProfile", $("#profName").val());
	$("#profName").val('');
}

function profileRemove() {
	if ($("#ddProfile").html() == "Profile") return;
	
	retr("saveProfile", { name: $("#ddProfile").html(), data: "" });
	$("#profileSel a:contains('" + $("#ddProfile").html() + "')").remove();
	ddSet("ddProfile", "Profile");
}

function calibrate() {
	swal({
		title: "Calibration",
		text: "Entering calibration mode will clear any temporary configuration. Continue?",
		type: "warning",
		showCancelButton: true,
		cancelButtonClass: "btn-lime"},
	function(x) {
		if (x) retr("page", "Calibration");
	});
}

var findDeviceTimer;
var findDeviceNonce;

function findDevice(nonce) {
	findDeviceNonce = nonce;
	findDeviceTimer = setInterval(function() {
		$.post("https://api.altdevs.net/do.php", { nonce: findDeviceNonce })
			.done(function(rxObj) {
				if (rxObj) {
					window.location.assign('http://' + rxObj + '/');
					clearTimeout(findDeviceTimer);
				}
			});
		}, 10000);
}
