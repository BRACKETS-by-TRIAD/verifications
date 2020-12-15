@if(config('verifications.2fa.required_for_all_users') || config('verifications.2fa.set_per_user_available'))
    @section('bottom-scripts')
        <script type="text/javascript">
            $(function() {
                var template =`
                    @foreach(config('verifications.2fa.generated_attributes') as $attribute)
                        <div class="form-group row align-items-center">
                            <label for="{{ $attribute['name'] }}" class="col-form-label text-md-right">{{ $attribute['label'] }}</label>
                                <input id="{{ $attribute['name'] }}" name="{{ $attribute['name'] }}" type="text" class="form-control" placeholder="{{ $attribute['label'] }}">
                        </div>
                    @endforeach()
                `;

                $('#profileForm').append(template);
            });
        </script>
    @endsection
@endif
