<div class="flash-message">
    @foreach (['danger', 'warning', 'success', 'info'] as $message)
        @if (Session::has('alert-' . $message))
            @if ($message == 'danger')
                <div class="alert alert-danger" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Error:</span>
                    {{ Session::get('alert-' . $message) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @elseif ($message == 'warning')
                <div class="alert alert-warning" role="alert">
                    <span class="glyphicon glyphicon-flag" aria-hidden="true"></span>
                    <span class="sr-only">Warning:</span>
                    {{ Session::get('alert-' . $message) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @elseif ($message == 'success')
                <div class="alert alert-success" role="alert">
                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                    <span class="sr-only">Success:</span>
                    {{ Session::get('alert-' . $message) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @elseif ($message == 'info')
                <div class="alert alert-info" role="alert">
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    <span class="sr-only">Info:</span>
                    {{ Session::get('alert-' . $message) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </div>
            @endif
        @endif
    @endforeach
</div>
