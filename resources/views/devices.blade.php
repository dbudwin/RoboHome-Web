<html>
    <head>
        <title>RoboHome | Devices</title>
        <script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
        <script>
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
                    @component('partials.flash-messages')
                    @endcomponent
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
                                                    <li><a href="/devices/edit/{{ $device->id }}"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>
                                                    <li><a href="/devices/delete/{{ $device->id }}"><span class="glyphicon glyphicon-remove"></span> Delete</a></li>
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
                                        <td class="col-xs-5 pull-right">
                                            <div class="btn-group" role="group" aria-label="Device Controls">
                                                <button type="button" class="btn btn-primary" onclick="controlDevice('turnon', '{{ $device->id }}');">On</button>
                                                <button type="button" class="btn btn-primary" onclick="controlDevice('turnoff', '{{ $device->id }}');">Off</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="panel-footer">
                            <button type="button" class="btn btn-default" aria-label="Add Device" data-toggle="modal" data-target="#deviceModal">
                                <span class="glyphicon glyphicon-plus"></span> Add Device
                            </button>
                            <div class="modal fade" id="deviceModal" tabindex="-1" role="dialog" aria-labelledby="deviceModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="deviceModalLabel">Device Information</h4>
                                        </div>
                                        <form action="/devices/add" method="POST">
                                            {{ csrf_field() }}
                                            <div class="modal-body">
                                                <div class="form-group row">
                                                    <label for="name" class="col-xs-4 col-form-label">Device Name</label>
                                                    <div class="col-xs-8">
                                                        <input class="form-control" type="text" placeholder="e.x. Living Room Light" name="name" required="true" maxlength="50">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="description" class="col-xs-4 col-form-label">Device Description</label>
                                                    <div class="col-xs-8">
                                                        <input class="form-control" type="text" placeholder="e.x. Light in corner of downstairs living room" name="description" required="true" maxlength="100">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="onCode" class="col-xs-4 col-form-label">On Code</label>
                                                    <div class="col-xs-8">
                                                        <input class="form-control" type="number" value="0" name="onCode" required="true">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="offCode" class="col-xs-4 col-form-label">Off Code</label>
                                                        <div class="col-xs-8">
                                                        <input class="form-control" type="number" value="0" name="offCode" required="true">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="pulseLength" class="col-xs-4 col-form-label">Pulse Length</label>
                                                        <div class="col-xs-8">
                                                        <input class="form-control" type="number" value="184" name="pulseLength" required="true">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Add Device</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
