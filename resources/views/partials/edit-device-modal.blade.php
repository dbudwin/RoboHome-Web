<div class="modal fade" id="edit-device-modal" tabindex="-1" role="dialog" aria-labelledby="edit-device-modal-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" dusk="edit-device-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="edit-device-modal-label">Update Existing Device</h4>
            </div>
            <form id="device-update-form" method="POST" dusk="edit-device-form">
                @method('PUT')
                @include('partials.device-form-body')
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="updateDeviceButton" type="submit" class="btn btn-primary">Update Device</button>
                </div>
            </form>
        </div>
    </div>
</div>
