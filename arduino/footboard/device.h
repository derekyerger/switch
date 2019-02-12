typedef struct {
  const char *name;
  uint16_t min;
  uint16_t max;
  const char *desc;
} TINFO;

const byte TCMAX = 14;

const char TDNAME0[] PROGMEM = "Sensor count";
const char TDNAME1[] PROGMEM = "Hard press";
const char TDNAME2[] PROGMEM = "Soft press";
const char TDNAME3[] PROGMEM = "Long press";
const char TDNAME4[] PROGMEM = "Sample interval";
const char TDNAME5[] PROGMEM = "Settle time";
const char TDNAME6[] PROGMEM = "Debounce time";
const char TDNAME7[] PROGMEM = "Sample averaging window";
const char TDNAME8[] PROGMEM = "Pressure bias";
const char TDNAME9[] PROGMEM = "Minimum group";
const char TDNAME10[] PROGMEM = "Enable adjust";
const char TDNAME11[] PROGMEM = "Bluetooth";
const char TDNAME12[] PROGMEM = "WiFi power off delay";
const char TDNAME13[] PROGMEM = "Sleep delay";

const char TDDESC0[] PROGMEM = "The number of sensors the device will read from";
const char TDDESC1[] PROGMEM = "The pressure required to trigger a hard press";
const char TDDESC2[] PROGMEM = "The pressure required to trigger a soft press";
const char TDDESC3[] PROGMEM = "The contact time (in milliseconds) required to trigger a long press";
const char TDDESC4[] PROGMEM = "The time (in milliseconds) between each sample";
const char TDDESC5[] PROGMEM = "The number of samples to continue reading before making a verdict";
const char TDDESC6[] PROGMEM = "How long to wait (in ms) after performing an action, before resuming sensing";
const char TDDESC7[] PROGMEM = "The number of past samples to include in the averaging function";
const char TDDESC8[] PROGMEM = "During auto-calibration, keep soft and hard presses separated by this delta";
const char TDDESC9[] PROGMEM = "Minimum number of hard or soft presses required to auto-calibrate";
const char TDDESC10[] PROGMEM = "Enable auto-calibration";
const char TDDESC11[] PROGMEM = "Enable or disable bluetooth";
const char TDDESC12[] PROGMEM = "How many seconds to keep WiFi management on after switching to battery";
const char TDDESC13[] PROGMEM = "How many seconds of inactivity should cause the device to sleep";

const TINFO TDESC[TCMAX] PROGMEM = {
	{ TDNAME0, 1, 2, TDDESC0 },
	{ TDNAME1, 0, 255, TDDESC1 },
	{ TDNAME2, 0, 255, TDDESC2 },
	{ TDNAME3, 1, 10000, TDDESC3 },
	{ TDNAME4, 1, 9, TDDESC4 },
	{ TDNAME5, 1, 255, TDDESC5 },
	{ TDNAME6, 0, 10000, TDDESC6 },
	{ TDNAME7, 1, SAMP, TDDESC7 },
	{ TDNAME8, 10, 100, TDDESC8 },
	{ TDNAME9, 5, 50, TDDESC9 },
	{ TDNAME10, 0, 1, TDDESC10 },
	{ TDNAME11, 0, 1, TDDESC11 },
	{ TDNAME12, 0, 32000, TDDESC12 },
	{ TDNAME13, 0, 32000, TDDESC13 },
};
