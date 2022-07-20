@if(Session::has('notify'))
    @if(is_array(Session::get('notify')))
        @php
            $notify = Session::get('notify');
        @endphp
        <script type="text/javascript">
            (function($) {

                // Create notify
                notify('@if(array_key_exists('title', $notify)){{ $notify['title'] }}@endif',
                    '@if(array_key_exists('message', $notify)){{ $notify['message'] }}@endif',
                    '@if(array_key_exists('icon', $notify)){{ $notify['icon'] }}@endif',
                    '@if(array_key_exists('type', $notify)){{ $notify['type'] }}@endif');

            }(jQuery));
        </script>
    @endif
@endif
