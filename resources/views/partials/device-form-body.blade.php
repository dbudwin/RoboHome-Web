{{ csrf_field() }}
<div class="modal-body">
    <div class="form-group row">
        <label for="name" class="col-xs-4 col-form-label">Device Name</label>
        <div class="col-xs-8">
            <input id="device-name-input" class="form-control" type="text" placeholder="e.x. Living Room Light" name="name" required="true" maxlength="50">
        </div>
    </div>
    <div class="form-group row">
        <label for="description" class="col-xs-4 col-form-label">Device Description</label>
        <div class="col-xs-8">
            <input id="device-description-input" class="form-control" type="text" placeholder="e.x. Light in corner of downstairs living room" name="description" required="true" maxlength="100">
        </div>
    </div>
    <div class="form-group row">
        <label for="on_code" class="col-xs-4 col-form-label">On Code</label>
        <div class="col-xs-8">
            <input id="device-on-code-input" class="form-control" type="number" value="0" placeholder="0" name="on_code" required="true">
        </div>
    </div>
    <div class="form-group row">
        <label for="off_code" class="col-xs-4 col-form-label">Off Code</label>
        <div class="col-xs-8">
            <input id="device-off-code-input" class="form-control" type="number" value="0" placeholder="0" name="off_code" required="true">
        </div>
    </div>
    <div class="form-group row">
        <label for="pulse_length" class="col-xs-4 col-form-label">Pulse Length</label>
        <div class="col-xs-8">
            <input id="device-pulse-length-input" class="form-control" type="number" value="184" placeholder="184" name="pulse_length" required="true">
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Update Device</button>
</div>
