@extends('app')
@section('content')
    <div class="container h-100">
        <input type="hidden" name="monday" value="{{ __('general.monday') }}">
        <input type="hidden" name="tuesday" value="{{ __('general.tuesday') }}">
        <input type="hidden" name="wednesday" value="{{ __('general.wednesday') }}">
        <input type="hidden" name="thursday" value="{{ __('general.thursday') }}">
        <input type="hidden" name="friday" value="{{ __('general.friday') }}">
        <input type="hidden" name="saturday" value="{{ __('general.saturday') }}">
        <input type="hidden" name="sunday" value="{{ __('general.sunday') }}">
        <input type="hidden" name="rw1" value="{{ __('random.word.1') }}">
        <input type="hidden" name="rw2" value="{{ __('random.word.2') }}">
        <input type="hidden" name="rw3" value="{{ __('random.word.3') }}">
        <input type="hidden" name="rw4" value="{{ __('random.word.4') }}">
        <input type="hidden" name="rw5" value="{{ __('random.word.5') }}">
        <input type="hidden" name="rw6" value="{{ __('random.word.6') }}">
        <input type="hidden" name="rw7" value="{{ __('random.word.7') }}">
        <input type="hidden" name="rw8" value="{{ __('random.word.8') }}">
        <input type="hidden" name="task_edit_failed" value="{{ __('page.homepage.tasks.edit.failed') }}">
        <div class="row justify-content-center h-100 not-selectable">
            @guest
                <div
                    class="col-md-6 text-center @if(is_mobile()) h-50 @else h-100 @endif d-flex justify-content-center align-items-center">
                    @include('snippets.logo', ['width' => 250, 'height' => 250])
                </div>
                <div
                    class="col-md-6 text-center @if(is_mobile()) h-50 @else h-100 @endif d-flex justify-content-center align-items-center">
                    <span class="home-promo-text">{!! trans('page.homepage.promo.text') !!}</span>
                </div>
            @endguest
            @auth
                <div class="today"></div>
                <div class="row justify-content-center h-100">
                    <div class="col-lg-4 col-md-5 col-md-offset-4 col-xs-6">
                        <div class="add-control">
                            <div class="form-group has-feedback">
                                <input type="text" class="form-control add-task"
                                       placeholder="✍️{{ trans('page.homepage.tasks.add') }}"/>
                                <i class="fa fa-plus form-control-feedback add-btn" title="Add item"></i>
                            </div>
                        </div>
                        <p class="no-items text-muted text-center @if(count($tasks) > 0) hidden @endif"><i
                                class="fa fa-ban"></i></p>
                        <ul class="todo-list">
                            @foreach($tasks as $task)
                                @include('snippets.task', ['id' => $task->identifier, 'name' => $task->name, 'description' => $task->description, 'status' => $task->status])
                            @endforeach
                        </ul>
                        <div class="pplaceholder">
                            @include('snippets.pagination', ['tasks' => $tasks])
                        </div>
                        <div class="w-100 text-center mb-3">
                            <a class="refresh text-decoration-none @if(count($tasks) === 0) hidden @endif"
                               href="javascript:void(0);">{{ trans('page.homepage.delete.all.tasks') }}</a>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
@endsection
@auth
    @section('customJS')
        <script src="{{ asset('js/today.js') }}" defer></script>
        <script src="{{ asset('js/tasks.js') }}" defer></script>
    @endsection
@endauth
