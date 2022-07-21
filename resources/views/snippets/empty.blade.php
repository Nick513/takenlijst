<li data-id="__id__" id="task___id__" class="task animated flipInX __status__">
    <div class="checkbox">
        <a href="javascript:;" class="toggle options decoration-none" id="menu-toggle__id__">
            <i class="fa fa-solid fa-ellipsis-vertical"></i>
        </a>
        <ul class="menu hidden" data-menu data-menu-toggle="#menu-toggle__id__">
            <li class="option">
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
            <input type="checkbox" />__name__
        </label>
    </div>
</li>
