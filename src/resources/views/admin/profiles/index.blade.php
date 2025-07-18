@extends('layouts.admin')
@section('content')
@can('profile_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.profiles.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.profile.title_singular') }}
            </a>
        </div>
    </div>
@endcan

<div class="card">
    <div class="card-header">
        {{ trans('cruds.profile.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable datatable-Profile">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.profile.fields.id') }}</th>
                        <th>{{ trans('cruds.profile.fields.image') }}</th>
                        <th>{{ trans('cruds.profile.fields.user_id') }}</th>
                        <th>{{ trans('cruds.profile.fields.nama_lengkap') }}</th>
                        <th>{{ trans('cruds.profile.fields.nomor_telepon') }}</th>
                        <th>{{ trans('cruds.profile.fields.jenis_kelamin') }}</th>
                        <th>{{ trans('cruds.profile.fields.tanggal_lahir') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profiles as $profile)
                        <tr data-entry-id="{{ $profile->id }}">
                            <td></td>
                            <td>{{ $profile->id ?? '' }}</td>
                            <td>
                                @if($profile->image)
                                    <a href="{{ $profile->image->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $profile->image->getUrl('thumb') }}" alt="{{ $profile->nama_lengkap }}" />
                                    </a>
                                @endif
                            </td>
                            <td>{{ $profile->user->name ?? $profile->user->email ?? '' }}</td>
                            <td>{{ $profile->nama_lengkap ?? '' }}</td>
                            <td>{{ $profile->nomor_telepon ?? '' }}</td>
                            <td>{{ App\Models\Profile::JENIS_KELAMIN_SELECT[$profile->jenis_kelamin] ?? '' }}</td>
                            <td>{{ $profile->tanggal_lahir ?? '' }}</td>
                            <td>
                                @can('profile_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.profiles.show', $profile->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('profile_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.profiles.edit', $profile->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('profile_delete')
                                    <form action="{{ route('admin.profiles.destroy', $profile->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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

        @can('profile_delete')
        let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('admin.profiles.massDestroy') }}",
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

        let table = $('.datatable-Profile:not(.ajaxTable)').DataTable({ buttons: dtButtons })

        $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    });
</script>
@endsection
