<li data-id="{{ $id }}" data-name="{{ $name }}" data-description="{{ $description }}" data-status="{{ $status }}" id="task_{{ $id }}" class="task @if($status === 'done') danger @endif">
    <div class="checkbox">
        <a href="javascript:;" class="toggle options decoration-none" id="menu-toggle{{ $id }}">
            <i class="fa fa-solid fa-ellipsis-vertical"></i>
        </a>
        <ul class="menu hidden" data-menu data-menu-toggle="#menu-toggle{{ $id }}">
            <li class="option" data-edit>
                <div>
                    <i class="fa fa-pencil" title="{{ __('general.edit') }}" aria-hidden="true"></i>
                    <p>{{ __('general.edit') }}</p>
                </div>
            </li>
            <li class="option" data-delete>
                <div>
                    <i class="fa fa-trash-alt" title="{{ __('general.delete') }}" aria-hidden="true"></i>
                    <p>{{ __('general.delete') }}</p>
                </div>
            </li>
        </ul>
        <label>
            <span class="checkbox-mask"></span>
            <input type="checkbox" />{{ $name }}
        </label>
    </div>
</li>
