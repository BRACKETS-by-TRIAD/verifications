@extends('brackets/admin-ui::admin.layout.master')

@section('title', trans('verifications.verification_code'))

@section('content')
    <div class="container" id="app">
        <div class="row align-items-center justify-content-center auth">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-block">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('brackets/verifications/verify') }}" novalidate>
                            {{ csrf_field() }}
                            <div class="auth-header">
                                <h1 class="auth-title">@lang('verifications.verification_code')</h1>
                                <p class="auth-subtitle">@lang('verifications.verification_code_subtitle')</p>
                            </div>
                            <div class="auth-body">
                                @include('brackets/admin-auth::admin.auth.includes.messages')
                                <div class="form-group">
                                    <label for="password">@lang('verifications.verification_code')</label>
                                    <div class="input-group input-group--custom">
                                        <div class="input-group-addon"><i class="input-icon input-icon--lock"></i></div>
                                        <input type="password" class="form-control" id="code" name="code" placeholder="{{ trans('verifications.verification_code') }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="hidden" name="remember" value="1">
                                    <button type="submit" class="btn btn-primary btn-block btn-spinner">
                                        @lang('verifications.verify')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
