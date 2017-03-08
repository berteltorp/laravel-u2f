<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript" src="{!! asset('js/u2f/u2f.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/u2f/app.js') !!}"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


<div class="container" style="margin-top:30px">
    <div class="col-md-6 col-md-offset-3">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title">{{ trans('u2f::messages.register.title') }}</h1>
            </div>
            <div class="panel-body" style="padding: 5px">

                <div class="alert alert-danger" role="alert" id="error" style="display: none"></div>
                <div class="alert alert-success" role="alert" id="success" style="display: none">
                    {{ trans('u2f::messages.success') }}
                </div>

                <div align="center">
                    <img src="https://ssl.gstatic.com/accounts/strongauth/Challenge_2SV-Gnubby_graphic.png" alt=""/>
                </div>

                <h3>
                    {{ trans('u2f::messages.insertKey') }}
                </h3>

                <p>
                    {{ trans('u2f::messages.buttonAdvise') }}
                    <br>
                    {{ trans('u2f::messages.noButtonAdvise') }}
                </p>
            </div>
        </div>
    </div>
</div>



{!! Form::open(array('route' => 'u2f.register', 'id' => 'form')) !!}
    {!! Form::hidden('register', '', ['id' => 'register']) !!}
{!! Form::close() !!}

<script type="text/javascript">
    var sigs = {!! json_encode($currentKeys) !!};
    var req = {!! json_encode($registerData) !!};

    var errors = {
        1: "{{ trans('u2f::errors.other_error') }}",
        2: "{{ trans('u2f::errors.bad_request') }}",
        3: "{{ trans('u2f::errors.configuration_unsupported') }}",
        4: "{{ trans('u2f::errors.device_ineligible') }}",
        5: "{{ trans('u2f::errors.timeout') }}"
    };

    u2fClient.register(req, sigs, errors);
</script>
</body>
</html>
