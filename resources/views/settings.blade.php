@extends('layouts.app')
@section('content')
    <div class="container h-100">
        <h1 class="mb-3"><i class="fa-solid fa-cog fa-sm"></i> {{ __('Settings') }}</h1>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="javascript:void(0);" id="nav-general-tab" data-bs-toggle="tab" data-bs-target="#nav-general" type="button" role="tab" aria-controls="nav-general" aria-selected="true">{{ __('General') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="javascript:void(0);" id="nav-api-tab" data-bs-toggle="tab" data-bs-target="#nav-api" type="button" role="tab" aria-controls="nav-api" aria-selected="true">{{ __('API') }}</a>
            </li>
        </ul>
        <div class="tab-content my-3" id="settings-tabcontent">
            <div class="tab-pane show active" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab">

                <div class="card settings-card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('page.settings.general.amount.title') }}</h5>
                        <!--
                        <div class="card-toggle">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                            </div>
                        </div>
                        -->
                        <div class="card-select">
                            <select class="form-select" name="amountOfTasks">
                                @for ($i = 3; $i < 12; $i++)
                                    <option @if($user['settings'] !== null && json_decode($user['settings'], true)['tasks']['amount'] === $i+1) selected @endif value="{{ $i+1 }}">{{ $i+1 }}</option>
                                @endfor
                            </select>
                        </div>
                        <p class="card-text">{{ __('page.settings.general.amount.content') }}</p>
                    </div>
                </div>

            </div>
            <div class="tab-pane" id="nav-api" role="tabpanel" aria-labelledby="nav-api-tab">
                <b>{{ __('page.settings.api.information') }}</b>{{ explode('|', Session::get('auth.apitoken'))[1] }}
            </div>
        </div>
    </div>
@endsection
