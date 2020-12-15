@if(config('verifications.2fa.required_for_all_users') || config('verifications.2fa.set_per_user_available'))
    @section('bottom-scripts')
        <script type="text/javascript">
            $(function() {
                var template =`
                    @foreach(config('verifications.2fa.generated_attributes') as $attribute)
                        <div class="form-group row align-items-center">
                            <label for="{{ $attribute['name'] }}" class="col-form-label text-md-right">{{ $attribute['label'] }}</label>
                            <input id="{{ $attribute['name'] }}" name="attributes['{{ $attribute['name'] }}']" type="text" class="form-control" placeholder="{{ $attribute['label'] }}">
                        </div>
                    @endforeach()

                    @if(config('verifications.2fa.set_per_user_available'))
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="login_verification" name="attributes['login_verification']">
                            <label class="custom-control-label" for="login_verification">@lang('verifications.turn_on_2fa')</label>
                        </div>
                    @endif
                `;

                $('#profileForm').append(template);
            });
        </script>
    @endsection
@endif
