@extends('layouts.app')
@section('content')
<div class="container h-100">
    <input type="hidden" name="monday" value="{{ trans('general.monday') }}">
    <input type="hidden" name="tuesday" value="{{ trans('general.tuesday') }}">
    <input type="hidden" name="wednesday" value="{{ trans('general.wednesday') }}">
    <input type="hidden" name="thursday" value="{{ trans('general.thursday') }}">
    <input type="hidden" name="friday" value="{{ trans('general.friday') }}">
    <input type="hidden" name="saturday" value="{{ trans('general.saturday') }}">
    <input type="hidden" name="sunday" value="{{ trans('general.sunday') }}">
    <input type="hidden" name="rw1" value="{{ trans('random.word.1') }}">
    <input type="hidden" name="rw2" value="{{ trans('random.word.2') }}">
    <input type="hidden" name="rw3" value="{{ trans('random.word.3') }}">
    <input type="hidden" name="rw4" value="{{ trans('random.word.4') }}">
    <input type="hidden" name="rw5" value="{{ trans('random.word.5') }}">
    <input type="hidden" name="rw6" value="{{ trans('random.word.6') }}">
    <input type="hidden" name="rw7" value="{{ trans('random.word.7') }}">
    <input type="hidden" name="rw8" value="{{ trans('random.word.8') }}">
    <div class="row justify-content-center h-100 not-selectable">
        @guest
            <div class="col-md-6 text-center @if(is_mobile()) h-50 @else h-100 @endif d-flex justify-content-center align-items-center">
                <span class="home-promo-text">{!! trans('homepage.promo.text') !!}</span>
            </div>
            <div class="col-md-6 text-center @if(is_mobile()) h-50 @else h-100 @endif d-flex justify-content-center align-items-center">
                @include('snippets.logo', ['width' => 250, 'height' => 250])
            </div>
        @endguest
        @auth
            <div class="today"></div>
            <div class="row justify-content-center h-100">
                <div class="col-lg-4 col-md-5 col-md-offset-4 col-xs-6">
                    <div class="add-control">
                        <div class="form-group has-feedback">
                            <input type="text" class="form-control add-task" placeholder="✍️{{ trans('homepage.tasks.add') }}"/>
                            <i class="fa fa-plus form-control-feedback add-btn" title="Add item"></i>
                        </div>
                    </div>
                    <p class="no-items text-muted text-center hidden"><i class="fa fa-ban"></i></p>
                    <ul class="todo-list"></ul>
                    <div class="w-100 text-center mb-3">
                        <a class="refresh text-decoration-none hidden" href="javascript:void(0);">{{ trans('homepage.delete.all.tasks') }}</a>
                    </div>
                </div>
            </div>
        @endauth
        <!--
        <div class="col-md-8">
            @if(session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
        </div>
        -->
    </div>
</div>
@endsection
@auth
    @section('customJS')
        <script src="{{ asset('js/today.js') }}" defer></script>
        <script src="{{ asset('js/tasks.js') }}" defer></script>
    @endsection
@endauth
