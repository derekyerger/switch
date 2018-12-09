	<div id="banner" class="page-header">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h1>Device Configuration</h1>
			</div>
		</div>
	</div>
	<div class="bs-docs-section">
		<div class="row">
			<div class="col-lg-10 col-md-9 col-sm-8">
				<h2>Advanced Configuration</h2>
				<p>These settings assist in further tuning the device to the specific needs of the user.</p>
			</div>
			<div class="col-lg-2 col-md-3 col-sm-4 text-right">
				<button type="button" class="btn btn-primary" onclick="fetchPage('Configuration');">Basic</button>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">

				<div class="form-group row">
					<div class="col">
						<label for="softPress">Soft Tap default</label>
						<input type="number" class="form-control" id="softPress" name="softPress" aria-describedby="softPressHelp" placeholder="Soft Tap" required>
						<small id="softPressHelp" class="form-text text-muted">Threshold above which an input is considered. This setting is loaded after a power loss, and adjusted when auto-calibration is enabled</small>
					</div>
					<div class="col">
						<label for="hardPress">Hard Tap default</label>
						<input type="number" class="form-control" id="hardPress" name="hardPress" aria-describedby="hardPressHelp" placeholder="Hard Tap" required>
						<small id="hardPressHelp" class="form-text text-muted">Threshold above which to consider a tap hard. This setting is stored and adjusted similarly to soft tap</small>
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col">
						<label for="longPress">Long Press default</label>
						<input type="number" class="form-control" id="longPress" name="longPress" aria-describedby="longPressHelp" placeholder="Long Press" required>
						<small id="longPressHelp" class="form-text text-muted">Duration of contact after which an input is considered a press-and-hold. This is not adjusted by auto-calibration</small>
					</div>
					<div class="col">
						<label for="sampleInterval">Sample Interval (ms)</label>
						<input type="number" class="form-control" id="sampleInterval" name="sampleInterval" aria-describedby="sampleIntervalHelp" placeholder="Sample Interval" required>
						<small id="sampleIntervalHelp" class="form-text text-muted">How often to read the sensors</small>
					</div>
				</div>

				<div class="form-group row">
					<div class="col">
						<label for="settleTime">Settle Time (samples)</label>
						<input type="number" class="form-control" id="settleTime" name="settleTime" aria-describedby="settleTimeHelp" placeholder="Settle Time" required>
						<small id="settleTimeHelp" class="form-text text-muted">How many sample intervals should elapse with no input before an input is acted on</small>
					</div>
					<div class="col">
						<label for="debounceTime">Debounce Time (ms)</label>
						<input type="number" class="form-control" id="debounceTime" name="debounceTime" aria-describedby="debounceTimeHelp" placeholder="Debounce Time" required>
						<small id="debounceTimeHelp" class="form-text text-muted">How long to ignore inputs after an input is processed</small>
					</div>
				</div>

				<div class="form-group row">
					<div class="col">
						<label for="avgWindow">Sample Averaging Window</label>
						<input type="number" class="form-control" id="avgWindow" name="avgWindow" aria-describedby="avgWindowHelp" placeholder="Sample Averaging Window" required>
						<small id="avgWindowHelp" class="form-text text-muted">The size of the rolling average window, between 1 and 50</small>
					</div>
					<div class="col">
						<label for="pressureBias">Pressure Bias</label>
						<input type="number" class="form-control" id="pressureBias" name="pressureBias" aria-describedby="pressureBiasHelp" placeholder="Pressure Bias" required>
						<small id="pressureBiasHelp" class="form-text text-muted">Minimum difference between soft and hard press thresholds</small>
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col">
						<label for="minGroup">Minimum Group</label>
						<input type="number" class="form-control" id="minGroup" name="minGroup" aria-describedby="minGroupHelp" placeholder="Minimum Group" required>
						<small id="minGroupHelp" class="form-text text-muted">Minimum group size of detected readings required to auto-calibrate</small>
					</div>
					<div class="col">
						<label for="enableAdjust">Auto Calibration</label><br>
						<div id="enableAdjust" class="btn-group btn-group-toggle" data-toggle="buttons">
							<label class="btn btn-secondary">
								<input type="radio" name="enableAdjust" id="enableAdjust1" autocomplete="off" value="1"> Enable
							</label>
							<label class="btn btn-secondary">
								<input type="radio" name="enableAdjust" id="enableAdjust0" autocomplete="off" value="0"> Disable
							</label>
						</div>
						<small id="enableAdjustHelp" class="form-text text-muted">Use collected data about input pressure to automatically adjust thresholds for soft and hard tap</small>
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col">
						<label for="batterySave">Battery Saver</label>
						<input type="number" class="form-control" id="batterySave" name="batterySave" aria-describedby="batterySaveHelp" placeholder="Battery Saver" required>
						<small id="batterySaveHelp" class="form-text text-muted">When powered by battery, turn off the WiFi management to conserve power below this battery level. Set to 100 to always turn off when on battery</small>
					</div>
					<div class="col">
						<label for="delBond">Delete Bond</label><br>
						<button type="button" class="btn btn-primary" id="delBond" onclick="delBond();">Clear Bond</button>
						<small id="delBondHelp" class="form-text text-muted">Delete bluetooth bonding/pairing association</small>
					</div>
				</div>
				<button type="button" class="btn btn-primary" onclick="doAjax('commit');">Save</button>
				<button type="button" class="btn btn-outline-secondary" onclick="fetchPage('Advanced');");">Reset</button>
				<button type="button" class="btn btn-danger" onclick="fetchPage('Debug');">Debugging info</button>
				<button type="button" class="btn btn-danger" onclick="doAjax('reset');">Reboot Device</button>
			</div>
		</div>
	</div>

