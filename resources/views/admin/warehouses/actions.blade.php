<div class="btn-group btn-group-sm" role="group">
    <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" 
       class="btn btn-outline-primary" 
       title="@lang('app.edit')">
        <i class="bi bi-pencil"></i>
    </a>
    <button type="button" 
            class="btn btn-outline-danger" 
            onclick="deleteWarehouse({{ $warehouse->id }})"
            title="@lang('app.delete')">
        <i class="bi bi-trash"></i>
    </button>
</div>

<form id="delete-form-{{ $warehouse->id }}" 
      action="{{ route('admin.warehouses.destroy', $warehouse->id) }}" 
      method="POST" 
      style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function deleteWarehouse(id) {
    if (confirm('@lang('app.confirmDelete')')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
