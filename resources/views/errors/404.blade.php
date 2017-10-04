<html>
    <head>
        <title>RoboHome | Login</title>
        <script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
          /* Error Page Inline Styles */
          body {
            padding-top: 20px;
          }
          /* Layout */
          .jumbotron {
            font-size: 21px;
            font-weight: 200;
            line-height: 2.1428571435;
            color: inherit;
            padding: 10px 0px;
          }
          /* Everything but the jumbotron gets side spacing for mobile-first views */
          .masthead, .body-content, {
            padding-left: 15px;
            padding-right: 15px;
          }
          /* Main marketing message and sign up button */
          .jumbotron {
            text-align: center;
            background-color: transparent;
          }
          .jumbotron .btn {
            font-size: 21px;
            padding: 14px 24px;
          }
          /* Colors */
          .green {color:#5cb85c;}
          .orange {color:#f0ad4e;}
          .red {color:#d9534f;}
        </style>
        <script type="text/javascript">
          function loadDomain() {
            var display = document.getElementById("display-domain");
            display.innerHTML = document.domain;
          }
        </script>
    </head>
    <body>
       <div class="container">
          <div class="jumbotron">
            <h1><i class="glyphicon glyphicon-exclamation-sign"></i></h1>
            <h2>404 Not Found</h2>
            <p class="lead">We couldn't find what you're looking for on <em><span id="display-domain"></span></em>.</p>
            <p><a onclick=javascript:checkSite(); class="btn btn-default btn-lg"><span class="green">Take Me To The Homepage</span></a>
            <script type="text/javascript">
                function checkSite(){
                  var currentSite = window.location.hostname;
                    window.location = "http://" + currentSite;
                }
            </script>
            </p>
          </div>
        </div>
    </body>
</html>
