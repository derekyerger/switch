/* Copyright (c) 2018 by Derek Yerger. All Rights Reserved. */

var locMapImg = { 10: '<img class="tico" src="bar.svg"><img class="tico" src="bar.svg"><img class="tico" src="i0.svg">',
	11: '<img class="tico" src="bar.svg"><img class="tico" src="bar.svg"><img class="tico" src="i1.svg">',
	12: '<img class="tico" src="bar.svg"><img class="tico" src="bar.svg"><img class="tico" src="i2.svg">',
	20: '<img class="tico" src="i0.svg"><img class="tico" src="bar.svg"><img class="tico" src="bar.svg">',
	21: '<img class="tico" src="i1.svg"><img class="tico" src="bar.svg"><img class="tico" src="bar.svg">',
	22: '<img class="tico" src="i2.svg"><img class="tico" src="bar.svg"><img class="tico" src="bar.svg">',
	30: '<img class="tico" src="bar.svg"><img class="tico" src="i0.svg"><img class="tico" src="bar.svg">',
	31: '<img class="tico" src="bar.svg"><img class="tico" src="i1.svg"><img class="tico" src="bar.svg">',
	32: '<img class="tico" src="bar.svg"><img class="tico" src="i2.svg"><img class="tico" src="bar.svg">',

};

var keyMap = { "^" : "Ctrl+",
	"+": "Shift+",
	"%": "Alt+",
	"&": "Command+",
	"|": "Return",
	"~": "Escape",
	"_": "Backspace",
	"!": "Tab",
	"A": "Caps Lock",
	"B": "F1",
	"C": "F2",
	"D": "F3",
	"E": "F4",
	"F": "F5",
	"G": "F6",
	"H": "F7",
	"I": "F8",
	"J": "F9",
	"K": "F10",
	"L": "F11",
	"M": "F12",
	"Q": "Insert",
	"R": "Home",
	"S": "Page Up",
	"T": "Delete",
	"U": "End",
	"V": "Page Down",
	"W": "&rarr;",
	"X": "&larr;",
	"Y": "&darr;",
	"Z": "&uarr;",
	"`": "Delay 250ms",
};

var winClip = {
	"Cut": "^x",
	"Copy": "^c",
	"Paste": "^v",
	"Undo": "^z",
	"Redo": "^y",
	"Select All": "^a",
};

var platformMap = {

	"*": {
		"Custom Keyboard Input": "%%",
		"Navigation": {
			"Left Arrow": "X",
			"Right Arrow": "W",
			"Up Arrow": "Z",
			"Down Arrow": "Y",
			"Enter": "|",
			"Tab": "!",
			"Home": "R",
			"End": "U",
			"Page Up": "S",
			"Page Down": "V",
			"Escape": "~",
		},
		"Editing": {
			"Insert": "Q",
			"Delete": "T",
			"Caps Lock": "A",
			"Backspace": "_",
		},
		"Function Keys": {
			"F1": "B",
			"F2": "C",
			"F3": "D",
			"F4": "E",
			"F5": "F",
			"F6": "G",
			"F7": "H",
			"F8": "I",
			"F9": "J",
			"F10": "K",
			"F11": "L",
			"F12": "M",
		},
		"Web Browsing": {
			"Navigation": {
				"Page Down": "V",
				"Page Up": "S",
				"Top of Page": "R",
				"Select Next Link": "!",
				"Follow Link": "|",
				"Go Back": "%X",
				"Go to Page": "^t`https://",
			},
			"Tabbed Browsing": {
				"Previous Tab": "^+!",
				"Next Tab": "^!",
				"New Tab": "^t",
				"Close Tab": "^E",
			}
		},
	},
	"Apple OS-X/iOS": {
		"Clipboard and Undo": {
			"Cut": "&x",
			"Copy": "&c",
			"Paste": "&v",
			"Undo": "&z",
			"Redo": "^&z",
			"Select All": "&a",
		},
		"Window Management": {
			"Next Window": "&!",
			"Previous Window": "&+!",
			"Show Desktop": "L",
			"Task Manager": "^&q",
		},
		"Desktop Applications": {
			"File menu": "^Cf",
			"Edit menu": "^Ce",
			"View menu": "^Cv",
		},
	},
	"Android": {
		"Clipboard and Undo": winClip,
	},
	"Linux Gnome": {
		"Clipboard and Undo": winClip,
		"Window Management": {
			"Next Window": "%!",
			"Previous Window": "%+!",
		},
		"Desktop Applications": {
			"File menu": "%f",
			"Edit menu": "%e",
			"View menu": "%v",
		},
	},
	"Linux KDE": {
		"Clipboard and Undo": winClip,
		"Window Management": {
			"Next Window": "%!",
			"Previous Window": "%+!",
		},
		"Desktop Applications": {
			"File menu": "%f",
			"Edit menu": "%e",
			"View menu": "%v",
		},
	},
	"Microsoft Windows": {
		"Clipboard and Undo": winClip,
		"Window Management": {
			"Show Desktop": "&d",
			"Task Manager": "^+~",
			"Next Window": "%!",
			"Previous Window": "%+!",
		},
		"Desktop Applications": {
			"File menu": "%f",
			"Edit menu": "%e",
			"View menu": "%v",
		},
		"Power Management": {
			"Sleep": "&WW|",
			"Shut Down": "&xuu",
			"Restart": "&xur",
		},
		"Launch Program": {
			"Start Firefox": "&r``firefox|",
			"Start Notepad": "&r``notepad|",
		},
	},
};

var actionMap = platformMap['*']; /* All the actions we can assign */

var helpMap = [ /* Help sequence */
	{
		"a": 1,
		"e": 'fetchPage("Status");',
	}, {
		"p": "bottom",
		"#mStatus": "The Status screen shows the current usage of the device. As inputs are detected, this window shows what was detected"
	}, {
		"#sImg": "Inputs will be displayed here. An animation and pop-up shows the input",
	}, {
		"#sList": "The last ten inputs are listed here",
	}, {
		"a": 1,
		"e": 'fetchPage("Configuration");',
		"er": 'fetchPage("Status");',
	}, {
		"er": '$("#ddLocation").dropdown("toggle");',
		"p": "bottom",
		"#mConfiguration": "This is the main configuration window. The device is configured on this screen"
	}, {
		"e": '$("#ddLocation").dropdown("toggle");',
		"er": '$("#ddLocation").dropdown("toggle");$("#ddImpulse").dropdown("toggle");',
		"#ddLocation": "To assign an action to a specific input, first choose where on the device the action will be assigned",
	}, {
		"e": '$("#ddLocation").dropdown("toggle");$("#ddImpulse").dropdown("toggle");ddSet("ddLocation", "Left");',
		"er": '$("#ddImpulse").dropdown("toggle");',
		"#ddImpulse": "Following this, choose the type of input to assign to",
	}, {
		"e": '$("#ddImpulse").dropdown("toggle");ddSet("ddImpulse", "Soft Tap");',
		"er": 'spinnerEnd()',
		"#ddCapture": "Alternatively, click Capture to choose based on the next input from the device",
	}, {
		"e": 'spinnerStart()',
		".fa-stack": "The progress spinner shows until the device detects an input"
	}, {
		"e": 'spinnerEnd()',
		"p": "bottom",
		"#ddAssignment": "This box displays the current action assigned to the chosen input",
	}, {
		"er": '$("#actionDlg").modal("hide");',
		"p": "bottom",
		"#ddAction": "Click this button to choose an action to assign to this input",
	}, {
		"e": 'buildAction();$("#actionDlg").modal("show");',
		"t": 1
	}, {
		"#as1": "Now, choose a category",
		"#dd1": "In this case, web browsing will be chosen",
	}, {
		"e": "nextAction(1, 'Web Browsing');",
		"#dd0": "Now, choose a subcategory to see more options",
	}, {
		"e": "nextAction(2, 'Navigation');",
		"#dd5": "This option corresponds to making the browser go back a page",
	}, {
		"e": "nextAction(3, 'Go Back');",
		"#keyRep": "These are the keystrokes sent to the computer that cause the action to be performed",
		"#asSave": "Click Save Changes to reassign the input to this action",
	}, {
		"e": '$("#actionDlg").modal("hide");',
		"er": 'ddSet("ddLocation", "Left");ddSet("ddImpulse", "Soft Tap");$("#actionDlg").modal("show");',
		"t": 1
	}, {
		"#ddSave": "Configuration changes are lost on power loss. Click this button to commit them to memory",
	}, {
		"#bluetooth": "Click this toggle to choose which interface to send actions through",
	}, {
		"a": 1,
		"e": 'fetchPage("Templates");',
		"er": 'fetchPage("Configuration");',
	}, {
		"#ccCard": "Each card listed on this screen contains a preconfigured template of commonly used tasks in an activity",
		"p": "left",
		"#ccActivity": "The activity title is shown in this space, with a short description below it",
	}, {
		"p": "top",
		"#cci0": "This light arrow corresponds to soft tap, and is assigned to the left side",
		"p2": "right",
		"#cci1": "This weighted arrow corresponds to a hard tap on the right",
		"p3": "bottom",
		"#cci2": "A roundabout arrow corresponds to press-and-hold",
	}, {
		"#ccActivate": "Click this button to configure the device as shown",
		"#ccAssign": "After installing the Desktop Helper application, a template may be assigned to a specific application",
	}, {
		"a": 1,
		"e": 'fetchPage("User Guide");',
		"er": 'fetchPage("Templates");',
	}, {
		"#um": "For additional information, refer to the user manual",
	}
];
