<div class="popup" data-id="{{ $id }}">
    <table class="mb-3">
        <h3 class="mb-3">{{ __('modal.tasks.edit') }}<!-- <small>(ID: {{ $id }})</small> --></h3>
        <tr>
            <td class="left">{{ __('modal.tasks.id') }}:</td>
            <td class="right">
                <input type="text" name="identifier" class="form-control" value="{{ $id }}" readonly="">
            </td>
        </tr>
        <tr>
            <td class="left">{{ __('modal.tasks.name') }}:</td>
            <td class="right">
                <input type="text" name="name" class="form-control" value="{{ $name }}">
            </td>
        </tr>
        <tr>
            <td class="left">{{ __('modal.tasks.description') }}:</td>
            <td class="right">
                <textarea rows="4" name="description" class="form-control no-resize">{{ $description }}</textarea>
            </td>
        </tr>
        <tr>
            <td class="left">{{ __('modal.tasks.status') }}:</td>
            <td class="right">
                <select name="status" class="form-select">
                    <option value="new">{{ __('modal.tasks.status.options.new') }}</option>
                    <option value="done" @if($status === 'done') selected @endif>{{ __('modal.tasks.status.options.done') }}</option>
                </select>
            </td>
        </tr>
    </table>
    <button class="savePopup btn btn-success">{{ __('general.save') }}</button>
    <button class="closePopup btn btn-danger">{{ __('general.close') }}</button>
</div>
