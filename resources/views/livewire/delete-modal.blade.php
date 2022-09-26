<div>
    <x-adminlte-button label="{{$button_label}}" data-toggle="modal" data-target="#modal-{{$modal_id}}" theme="danger" class="{{$button_class}}"/>
    <x-adminlte-modal id="modal-{{$modal_id}}" title="Confirm delete" size="lg" theme="danger"
    icon="fas fa-trash" v-centered>
        <div class="d-flex justify-content-center align-items-center flex-column ">
            <div>
                <i class="fas fa-trash text-danger fa-6x py-3"></i>
            </div>
            <p>This item "{{$item_name ?? ''}}" and all related records would be deleted</p>
        </div>
        <x-slot name="footerSlot">
            <x-adminlte-button label="close" data-dismiss="modal" theme="secondary" class="mr-auto"/>
            <form action="{{$action}}" method="post">
                @csrf
                @method('delete')
                <x-adminlte-button label="continue with delete" type="submit" theme="danger"/>
            </form>
        </x-slot>
    </x-adminlte-modal>
</div>