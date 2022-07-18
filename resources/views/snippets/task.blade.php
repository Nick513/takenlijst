<li data-id="{{ $id }}" class="task animated flipInX @if($status === 'done') {{ $status }} @endif">
    <div class="checkbox">
        <span class="edit">
            <i class="fa fa-pencil"></i>
        </span>
        <span class="close">
            <i class="fa fa-times"></i>
        </span>
        <label>
            <span class="checkbox-mask"></span>
            <input type="checkbox" />{{ $name }}
        </label>
    </div>
</li>
