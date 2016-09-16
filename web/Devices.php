<html>
    <head>
        <title>RoboHome | Devices</title>
        <script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
        <script>
            function toggleOutlet(topic, message) {
                $.ajax({url: "extensions/MQTTPublisher.php?topic=" + topic + "&message=" + message, success: function(result) {
                    alert(topic + " was turned " + message);
                }});
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
        <script src="scripts/loginWithAmazonScript.js" type="text/javascript"></script>
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
                                <?php
                                    require("extensions/Credentials.php");
                                    require("extensions/DatabaseConnection.php");

                                    $cookie_name = "amazon_Login_state_cache";
                                    
                                    if (isset($_COOKIE[$cookie_name])) {
                                        $data = json_decode($_COOKIE[$cookie_name], true);
                                        $accessToken = $data["access_token"];

                                        //Verify that the access token belongs to us
                                        $c = curl_init("https://api.amazon.com/auth/o2/tokeninfo?access_token=" . urlencode($accessToken));
                                        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
                                        
                                        $r = curl_exec($c);
                                        curl_close($c);
                                        $d = json_decode($r);
                                        
                                        if ($d->aud != $AMAZON_TOKEN) {
                                            //The access token does not belong to us
                                            header("HTTP/1.1 404 Not Found");
                                            echo "Page not found";
                                            exit;
                                        }
                                        
                                        //Exchange the access token for user profile
                                        $c = curl_init("https://api.amazon.com/user/profile");
                                        curl_setopt($c, CURLOPT_HTTPHEADER, array("Authorization: bearer " . $accessToken));
                                        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
                                        
                                        $r = curl_exec($c);
                                        curl_close($c);
                                        $d = json_decode($r); 

                                        $sql = sprintf("SELECT COUNT(UserID) FROM Users WHERE UserID = '%s'", $d->user_id);

                                        $database = new Database();
                                        $result = $database->select($sql);
                                        
                                        if (!count($result) == 1) {
                                            $sql = sprintf("INSERT INTO Users (Name, Email, UserID) VALUES ('%s', '%s', '%s')", $d->name, $d->email, $d->user_id);
                                            
                                            if ($conn->query($sql) === TRUE) {
                                                echo "New record created successfully";
                                            } else {
                                                echo sprintf("Error:  %s<br>%s", $sql, $conn->error);
                                            }
                                        }
                                ?>
                                    <a id="Logout" href="#" id="LoginWithAmazon"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Logout</a>
                                <?php
                                    }
                                ?>
                                <script type="text/javascript">
                                    document.getElementById("Logout").onclick = function() {
                                        amazon.Login.logout();
                                        window.location = "index.html";
                                    };
                                </script>
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                             <div class="panel-title">
                                 <?php echo sprintf("%s's", $d->name); ?> Controllable Devices
                             </div>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-condensed">
                                <tr>
                                    <td class="col-xs-2">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Edit Device">
                                                <span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>
                                                <li><a href="#"><span class="glyphicon glyphicon-remove"></span> Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="col-xs-5">Downstairs Living Room Light Test 1234</td>
                                    <td class="col-xs-5">
                                        <div class="btn-group" role="group" aria-label="Device Controls">
                                            <button type="button" class="btn btn-primary" onclick="toggleOutlet('EtekcityOutlet1', 'On');">On</button>
                                            <button type="button" class="btn btn-primary" onclick="toggleOutlet('EtekcityOutlet1', 'Off');">Off</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Edit Device">
                                                <span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#"><span class="glyphicon glyphicon-pencil"></span> Edit</a></li>
                                                <li><a href="#"><span class="glyphicon glyphicon-remove"></span> Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>Entry Way Light</td>
                                    <td>
                                        <div class="btn-group" role="group" aria-label="Device Controls">
                                            <button type="button" class="btn btn-primary" onclick="toggleOutlet('EtekcityOutlet2', 'On');">On</button>
                                            <button type="button" class="btn btn-primary" onclick="toggleOutlet('EtekcityOutlet2', 'Off');">Off</button>
                                        </div>
                                    </td>
                                </tr>
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
                                        <div class="modal-body">
                                            <form>
                                                <div class="form-group row">
                                                    <label for="device-name-input" class="col-xs-4 col-form-label">Device Name</label>
                                                    <div class="col-xs-8">
                                                        <input class="form-control" type="text" placeholder="e.x. Living Room Light" id="device-name-input">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="device-on-number-input" class="col-xs-4 col-form-label">On Code</label>
                                                    <div class="col-xs-8">
                                                        <input class="form-control" type="number" value="0" id="device-on-number-input">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="device-off-number-input" class="col-xs-4 col-form-label">Off Code</label>
                                                        <div class="col-xs-8">
                                                        <input class="form-control" type="number" value="0" id="device-off-number-input">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Add Device</button>
                                        </div>
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