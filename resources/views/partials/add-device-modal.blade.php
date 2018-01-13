<div class="modal fade" id="add-device-modal" tabindex="-1" role="dialog" aria-labelledby="add-device-modal-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" dusk="add-device-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="add-device-modal-label">Add New Device</h4>
            </div>
            <form action="/devices/add" method="POST" dusk="add-device-form">
                @include('partials.device-form-body')
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="addDeviceButton" type="submit" class="btn btn-primary">Add Device</button>
                </div>
            </form>
        </div>
    </div>
</div>
