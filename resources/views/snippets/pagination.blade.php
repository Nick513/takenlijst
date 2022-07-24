<div class="todo-pagination text-center" style="width: fit-content; margin: 0 auto;">
    {!! str_replace("api/tasks/links", "", $tasks->onEachSide(0)->withQueryString()->links('vendor.pagination.custom')) !!}
</div>
