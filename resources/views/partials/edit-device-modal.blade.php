<div class="modal fade" id="editDeviceModal" tabindex="-1" role="dialog" aria-labelledby="editDeviceModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editDeviceModalLabel">Device Information</h4>
            </div>
            <form id="device-update-form" method="POST">
                @include('partials.device-form-body')
            </form>
        </div>
    </div>
</div>
