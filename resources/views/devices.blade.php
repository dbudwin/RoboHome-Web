<html>
    <head>
        <title>RoboHome | Devices</title>
        <script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function() {
                $('#editDeviceModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);

                    var deviceId = button.data('device-id');
                    var deviceName = button.data('device-name');
                    var deviceDescription = button.data('device-description');
                    var deviceOnCode = button.data('device-on-code');
                    var deviceOffCode = button.data('device-off-code');
                    var devicePulseLength = button.data('device-pulse-length');

                    var modal = $(this);

                    modal.find('#device-update-form').attr('action', '/devices/update/' + deviceId)
                    modal.find('#device-name-input').val(deviceName);
                    modal.find('#device-description-input').val(deviceDescription);
                    modal.find('#device-on-code-input').val(deviceOnCode);
                    modal.find('#device-off-code-input').val(deviceOffCode);
                    modal.find('#device-pulse-length-input').val(devicePulseLength);
                })
            });

            function controlDevice(action, id) {
                $.ajax({
                    type: "POST",
                    url: "/devices/" + action + "/" + id
                });
            }
        </script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div id="amazon-root"></div>
        <script src="{{ URL::asset('js/loginWithAmazonScript.js') }}" type="text/javascript"></script>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <nav class="navbar navbar-default" role="navigation">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                                 <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                            </button><a class="navbar-brand" href="#">RoboHome</a>
                        </div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav navbar-right">
                                <li>
                                    <a id="Logout" href="#" id="LoginWithAmazon"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Logout</a>
                                    <script type="text/javascript">
                                        document.getElementById("Logout").onclick = function() {
                                            amazon.Login.logout();
                                            window.location = "logout";
                                        };
                                    </script>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    @include('partials.flash-messages')
                    <div class="panel panel-default">
                        <div class="panel-heading">
                             <div class="panel-title">
                                 {{ $name }}'s Controllable Devices
                             </div>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-condensed">
                                @foreach ($devices as $device)
                                    <tr>
                                        <td class="col-xs-1">
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Edit Device">
                                                    <span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="#editDeviceModal" aria-label="Edit Device" data-toggle="modal" data-target="#editDeviceModal" data-device-id="{{ $device->id }}" data-device-name="{{ $device->name }}" data-device-description="{{ $device->description }}" @foreach($device->htmlDataAttributesForSpecificDeviceProperties() as $property) {{ $property }} @endforeach>
                                                            <span class="glyphicon glyphicon-pencil"></span> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="/devices/delete/{{ $device->id }}">
                                                            <span class="glyphicon glyphicon-remove"></span> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td class="col-xs-6">
                                            <div>
                                                {{ $device->name }}
                                            </div>
                                            <div>
                                                <small>{{ $device->description }}</small>
                                            </div>
                                        </td>
                                        <td class="col-xs-5">
                                            <div class="btn-group pull-right" role="group" aria-label="Device Controls">
                                                <button type="button" class="btn btn-primary" onclick="controlDevice('turnon', '{{ $device->id }}');">On</button>
                                                <button type="button" class="btn btn-primary" onclick="controlDevice('turnoff', '{{ $device->id }}');">Off</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="panel-footer">
                            <button type="button" class="btn btn-default" aria-label="Add Device" data-toggle="modal" data-target="#addDeviceModal">
                                <span class="glyphicon glyphicon-plus"></span> Add Device
                            </button>
                            @include('partials.add-device-modal')
                            @include('partials.edit-device-modal')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
