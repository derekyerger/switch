typedef struct {
  const char *name;
  const char *var;
  int16_t min;
  int16_t max;
  const char *desc;
} TINFO;

const byte TCMAX = 19;

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
const char TDNAME10[] PROGMEM = "Adjust window";
const char TDNAME11[] PROGMEM = "Floor window";
const char TDNAME12[] PROGMEM = "Enable adjust";
const char TDNAME13[] PROGMEM = "Bluetooth";
const char TDNAME14[] PROGMEM = "SPU Sleep delay";
const char TDNAME15[] PROGMEM = "CM Sleep delay";
const char TDNAME16[] PROGMEM = "Baseline ratio";
const char TDNAME17[] PROGMEM = "Load ratio";
const char TDNAME18[] PROGMEM = "Load point";

const char TDVAR0[] PROGMEM = "sensorCount";
const char TDVAR1[] PROGMEM = "hardPress";
const char TDVAR2[] PROGMEM = "softPress";
const char TDVAR3[] PROGMEM = "longPress";
const char TDVAR4[] PROGMEM = "sampleInterval";
const char TDVAR5[] PROGMEM = "settleTime";
const char TDVAR6[] PROGMEM = "debounceTime";
const char TDVAR7[] PROGMEM = "avgWindow";
const char TDVAR8[] PROGMEM = "pressureBias";
const char TDVAR9[] PROGMEM = "minGroup";
const char TDVAR10[] PROGMEM = "adjustWindow";
const char TDVAR11[] PROGMEM = "floorWindow";
const char TDVAR12[] PROGMEM = "enableAdjust";
const char TDVAR13[] PROGMEM = "bluetooth";
const char TDVAR14[] PROGMEM = "sleepDelay";
const char TDVAR15[] PROGMEM = "wifiSleepDelay";
const char TDVAR16[] PROGMEM = "ratio[0]";
const char TDVAR17[] PROGMEM = "ratio[1]";
const char TDVAR18[] PROGMEM = "loadPressure";

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
const char TDDESC10[] PROGMEM = "Calibration history window";
const char TDDESC11[] PROGMEM = "Floor adjustment window";
const char TDDESC12[] PROGMEM = "Enable auto-calibration";
const char TDDESC13[] PROGMEM = "Enable or disable bluetooth";
const char TDDESC14[] PROGMEM = "How many seconds of inactivity should cause the sensor processor to sleep";
const char TDDESC15[] PROGMEM = "How many seconds of inactivity should cause the web server to sleep";
const char TDDESC16[] PROGMEM = "Sensor 1 vs 2 at baseline";
const char TDDESC17[] PROGMEM = "Sensor 1 vs 2 under load";
const char TDDESC18[] PROGMEM = "Load threshold";

const TINFO TDESC[TCMAX] PROGMEM = {
	{ TDNAME0, TDVAR0, 1, 2, TDDESC0 },
	{ TDNAME1, TDVAR1, 0, 255, TDDESC1 },
	{ TDNAME2, TDVAR2, 0, 255, TDDESC2 },
	{ TDNAME3, TDVAR3, 1, 10000, TDDESC3 },
	{ TDNAME4, TDVAR4, 1, 9, TDDESC4 },
	{ TDNAME5, TDVAR5, 1, 255, TDDESC5 },
	{ TDNAME6, TDVAR6, 0, 10000, TDDESC6 },
	{ TDNAME7, TDVAR7, 1, SAMP, TDDESC7 },
	{ TDNAME8, TDVAR8, 10, 100, TDDESC8 },
	{ TDNAME9, TDVAR9, 1, 50, TDDESC9 },
	{ TDNAME10, TDVAR10, 10, CLLMAX, TDDESC10 },
	{ TDNAME11, TDVAR11, 1, 32000, TDDESC11 },
	{ TDNAME12, TDVAR12, 0, 1, TDDESC12 },
	{ TDNAME13, TDVAR13, 0, 1, TDDESC13 },
	{ TDNAME14, TDVAR14, 0, 32000, TDDESC14 },
	{ TDNAME15, TDVAR15, 30, 32000, TDDESC15 },
	{ TDNAME16, TDVAR16, -100, 100, TDDESC16 },
	{ TDNAME17, TDVAR17, 0, 32000, TDDESC17 },
	{ TDNAME18, TDVAR18, 0, 255, TDDESC18 },
};
