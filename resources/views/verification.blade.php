<!doctype html>
<html lang="sk|en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@lang('brackets/verifications::verifications.verification_code')</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">

    <style>
        body, html {
            font-family: "DejaVu Sans", sans-serif;
            padding: 50px;
            margin: 0;
        }

        .card-header {
            background-color: white;
            padding-top: 16px;
        }

        .card-header h1 {
            color: #333d5d;
            font-size: 25px;
        }

        .card-header p {
            color: #b9bbc6;
            font-size: 12px;
            margin-top: 15px;
            margin-bottom: 0;
        }

        small {
            font-size: 11px;
            color: #b9bbc6;
            line-height: 1.4;
            display: inline-block;
        }

        .submit-group {
            margin-top: 30px;
        }

        button#resendCodeBtn {
            padding: 0 16px;
        }

        .input-group-text {
            background-color: white;
        }

        .icon-th {
            color: #757680;
            top: 15px;
            font-size: 22px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row align-items-center justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-uppercase">
                    <h1>@lang('brackets/verifications::verifications.verification_code')</h1>
                    <p>@lang('brackets/verifications::verifications.verification_code_subtitle')</p>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ \Illuminate\Support\Facades\URL::route('brackets/verifications/verify') }}" novalidate>
                        @csrf
                        <div>
                            <div class="form-group">
                                <label for="code">@lang('brackets/verifications::verifications.verification_code')</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="icon-th"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" id="code" name="code" placeholder="{{ trans('brackets/verifications::verifications.verification_code') }}">
                                </div>
                            </div>
                            <small>@lang('brackets/verifications::verifications.verification_code_subtitle2', ['channel' => 'telefónne číslo', 'contact' => '+421 *** *** *68'])</small>

                            <div class="form-group submit-group">
                                <input type="hidden" name="redirectToUrl" value="{{ $redirectToUrl }}">
                                <input type="hidden" name="action_name" value="{{ $action_name }}">
                                <button type="submit" class="btn btn-primary btn-block btn-spinner">
                                    @lang('brackets/verifications::verifications.verify')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <small>@lang('brackets/verifications::verifications.didnt_receive_code')</small>
                    <button type="button" id="resendCodeBtn" class="btn btn-outline-secondary pull-right" data-toggle="tooltip" title="{{ trans('brackets/verifications::verifications.send_new_code') }}">
                        <i class="icon-repeat"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $("#resendCodeBtn").click(function() {
            //TODO
            console.log('clicked');
            $.ajax({
                type: "POST",
                url: {{ \Illuminate\Support\Facades\URL::route('brackets/verifications/resend') }},
                success: function() {
                    alert("code successfuly resent");
                },
                dataType: "json"
            });

            {{--$.get({{ \Illuminate\Support\Facades\URL::route('brackets/verifications/verify') }}, function(res) {--}}
            {{--    alert( "success" );--}}
            {{--});--}}
            // .done(function() {
            //     alert( "second success" );
            // })
            //     .fail(function() {
            //         alert( "error" );
            //     })
            //     .always(function() {
            //         alert( "finished" );
            //     });
        });
    });
</script>
</body>
</html>
