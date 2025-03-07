<div>
    <div class="block-header p-0 mb-2" wire:ignore>
        <h3 class="block-title">Lista dodanih atributa</h3>
        <a class="btn btn-success btn-sm" href="javascript:void(0);" wire:click="addItem()">
            <i class="far fa-fw fa-plus-square"></i><span class="d-none d-sm-inline ml-1">Dodaj atribut</span>
        </a>
    </div>

    <table class="table table-striped table-borderless table-vcenter">
        <thead class="thead-light">
        <tr>
            <th class="font-size-sm">Atribut</th>
            <th class="font-size-sm" style="width:30%">Vrijednost</th>
            <th class="text-right font-size-sm" style="width:10%">Akcije</th>
        </tr>
        </thead>
        <tbody>


        @foreach ($items as $key => $item)
            <tr>
                <input type="hidden" name="item[{{ $key }}][id]" wire:model="items.{{ $key }}.id">
                <td>
                    <select class="js-select2 form-control form-control-sm form-select-solid" id="select-{{ $key }}" wire:model="items.{{ $key }}.id" name="product_attributes[{{ $key }}][id]" style="width: 100%;" data-placeholder="Odaberite atribut">
                        <option></option>
                        @foreach ($values as $id => $value)
                            <option value="{{ $id }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <span class="font-size-sm"> <input type="text" class="form-control form-control-sm" wire:model="items.{{ $key }}.value" name="product_attributes[{{ $key }}][value]"></span>
                </td>
                <td class="text-right font-size-sm">
                    <a href="javascript:void();" wire:click="deleteItem({{ $key }})" class="btn btn-sm btn-alt-danger"><i class="fa fa-fw fa-trash-alt"></i></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@push('product_scripts')
    <script>

    </script>
@endpush