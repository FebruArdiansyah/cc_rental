@extends('layouts.admin')
@section('content')
@can('pesanan_item_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.pesananitems.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.pesananitem.title_singular') }}
            </a>
        </div>
    </div>
@endcan

<div class="card">
    <div class="card-header">
        {{ trans('cruds.pesananitem.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable datatable-PesananItem">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.pesananitem.fields.id') }}</th>
                        <th>{{ trans('cruds.pesananitem.fields.obat_id') }}</th>
                        <th>{{ trans('cruds.pesananitem.fields.pesanan_id') }}</th>
                        <th>{{ trans('cruds.pesananitem.fields.qty') }}</th>
                        <th>{{ trans('cruds.pesananitem.fields.total') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pesananitems as $pesananitem)
                        <tr data-entry-id="{{ $pesananitem->id }}">
                            <td></td>
                            <td>{{ $pesananitem->id ?? '' }}</td>
                            <td>{{ $pesananitem->obat->nama_obat ?? '' }}</td>
                            <td>{{ $pesananitem->pesanan->nomor_pesanan ?? '' }}</td>
                            <td>{{ $pesananitem->qty ?? '' }}</td>
                            <td>{{ number_format($pesananitem->total, 2, ',', '.') ?? '' }}</td>
                            <td>
                                @can('pesanan_item_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.pesananitems.show', $pesananitem->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('pesanan_item_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.pesananitems.edit', $pesananitem->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('pesanan_item_delete')
                                    <form action="{{ route('admin.pesananitems.destroy', $pesananitem->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        @method('DELETE')
                                        @csrf
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

        @can('pesanan_item_delete')
        let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('admin.pesananitems.massDestroy') }}",
            className: 'btn-danger',
            action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                    return $(entry).data('entry-id')
                });

                if (ids.length === 0) {
                    alert('{{ trans('global.datatables.zero_selected') }}')
                    return
                }

                if (confirm('{{ trans('global.areYouSure') }}')) {
                    $.ajax({
                        headers: {'x-csrf-token': _token},
                        method: 'POST',
                        url: config.url,
                        data: { ids: ids, _method: 'DELETE' }
                    }).done(function () { location.reload() })
                }
            }
        }
        dtButtons.push(deleteButton)
        @endcan

        $.extend(true, $.fn.dataTable.defaults, {
            orderCellsTop: true,
            order: [[1, 'desc']],
            pageLength: 25,
        });

        let table = $('.datatable-PesananItem:not(.ajaxTable)').DataTable({ buttons: dtButtons })

        $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    });
</script>
@endsection
