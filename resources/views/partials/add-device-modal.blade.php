<div class="modal fade" id="addDeviceModal" tabindex="-1" role="dialog" aria-labelledby="addDeviceModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addDeviceModalLabel">Device Information</h4>
            </div>
            <form action="/devices/add" method="POST">
                @include('partials.device-form-body')
            </form>
        </div>
    </div>
</div>
