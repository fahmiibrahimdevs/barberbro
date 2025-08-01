<div>
    <section class='section custom-section'>
        <div class='section-header'>
            <h1>Kasbon</h1>
        </div>

        <div class='section-body'>
            <div class='card'>
                <h3>Tabel Kasbon</h3>
                <div class='card-body'>
                    <div class='show-entries'>
                        <p class='show-entries-show'>Show</p>
                        <select wire:model.live='lengthData' id='length-data'>
                            <option value='25'>25</option>
                            <option value='50'>50</option>
                            <option value='100'>100</option>
                            <option value='250'>250</option>
                            <option value='500'>500</option>
                        </select>
                        <p class='show-entries-entries'>Entries</p>
                    </div>
                    <div class='search-column'>
                        <p>Search: </p><input type='search' wire:model.live.debounce.750ms='searchTerm' id='search-data'
                            placeholder='Search here...' class='form-control'>
                    </div>
                    <div class='table-responsive tw-max-h-96 no-scrollbar'>
                        <table class='tw-w-full tw-table-auto'>
                            <thead class='tw-sticky tw-top-0'>
                                <tr class='tw-text-gray-700'>
                                    <th width='6%' class='text-center'>No</th>
                                    <th class='tw-whitespace-nowrap'>Tgl Pengajuan</th>
                                    <th class='tw-whitespace-nowrap'>Nama Karyawan</th>
                                    <th class='tw-whitespace-nowrap'>Jumlah</th>
                                    <th width="30%" class='tw-whitespace-nowrap'>Keterangan</th>
                                    <th width="5%" class='tw-whitespace-nowrap'>Status</th>
                                    <th class='text-center'><i class='fas fa-cog'></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data->groupBy('nama_cabang') as $result)
                                <tr>
                                    <td class="tw-text-sm tw-tracking-wider" colspan="10">
                                        <b>Lokasi: {{ $result[0]->nama_cabang }}</b>
                                    </td>
                                </tr>
                                @foreach ($result as $row)
                                <tr class='text-center'>
                                    <td class='tw-whitespace-nowrap'>{{ $loop->index + 1 }}</td>
                                    <td class='tw-whitespace-nowrap text-left'>{{ $row->tgl_pengajuan }}</td>
                                    <td class='tw-whitespace-nowrap text-left'>{{ $row->nama_karyawan }}</td>
                                    <td class='tw-whitespace-nowrap text-left'>@money($row->jumlah)</td>
                                    <td class='tw-whitespace-nowrap text-left'>{{ $row->keterangan }}</td>
                                    <td class='tw-whitespace-nowrap text-left'>
                                        @if ($row->status == "pending")
                                        <div
                                            class="tw-bg-orange-100 tw-text-orange-600 tw-tracking-[0.5px] tw-text-xs tw-font-semibold tw-py-1 tw-px-2 tw-rounded-md">
                                            Pending</div>
                                        @elseif ($row->status == "disetujui")
                                        <div
                                            class="tw-bg-green-100 tw-text-green-600 tw-tracking-[0.5px] tw-text-xs tw-font-semibold tw-py-1 tw-px-2 tw-rounded-md">
                                            Disetujui oleh <b>{{ $row->disetujui_oleh }}</b></div>
                                        @elseif ($row->status == "ditolak")
                                        <div
                                            class="tw-bg-red-100 tw-text-red-600 tw-tracking-[0.5px] tw-text-xs tw-font-semibold tw-py-1 tw-px-2 tw-rounded-md">
                                            Ditolak</div>
                                        @endif
                                    </td>
                                    <td class='tw-whitespace-nowrap'>
                                        <button wire:click.prevent='edit({{ $row->id }})' class='btn btn-primary'
                                            data-toggle='modal' data-target='#formDataModal'>
                                            <i class='fas fa-edit'></i>
                                        </button>
                                        <button wire:click.prevent='deleteConfirm({{ $row->id }})'
                                            class='btn btn-danger'>
                                            <i class='fas fa-trash'></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @empty
                                <tr>
                                    <td colspan='10' class='text-center'>No data available in the table</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class='mt-5 px-3'>
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
        <button wire:click.prevent='isEditingMode(false)' class='btn-modal' data-toggle='modal' data-backdrop='static'
            data-keyboard='false' data-target='#formDataModal'>
            <i class='far fa-plus'></i>
        </button>
    </section>

    <div class='modal fade' data-backdrop="static" wire:ignore.self id='formDataModal'
        aria-labelledby='formDataModalLabel' aria-hidden='true'>
        <div class='modal-dialog tw-w-full tw-m-0 sm:tw-w-auto sm:tw-m-[1.75rem_auto]'>
            <div class='modal-content tw-rounded-none lg:tw-rounded-md'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='formDataModalLabel'>{{ $isEditing ? 'Edit Data' : 'Add Data' }}</h5>
                    <button type='button' wire:click='cancel()' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <form>
                    <div class='modal-body tw-px-4 lg:tw-px-6'>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class='form-group'>
                                    <label for='tgl_pengajuan'>Tanggal Pengajuan</label>
                                    <input type='date' wire:model='tgl_pengajuan' id='tgl_pengajuan'
                                        class='form-control'>
                                    @error('tgl_pengajuan') <span class='text-danger'>{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class='form-group'>
                                    <label for='tgl_disetujui'>Tanggal Disetujui</label>
                                    <input type='date' wire:model='tgl_disetujui' id='tgl_disetujui'
                                        class='form-control'>
                                    @error('tgl_disetujui') <span class='text-danger'>{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label for='id_karyawan'>Nama Karyawan</label>
                            <select wire:model='id_karyawan' id='id_karyawan' class='form-control select2'>
                                <option value='' disabled>-- Opsi Pilihan --</option>
                                @foreach ($karyawans->groupBy('nama_cabang') as $karyawan)
                                <optgroup label="{{ $karyawan[0]->nama_cabang }}">
                                    @foreach ($karyawan as $item)
                                    <option value='{{ $item->id }}'>{{ $item->name }}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                            @error('id_karyawan') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                        <div class='form-group'>
                            <label for='jumlah'>Jumlah</label>
                            <input type='number' wire:model='jumlah' id='jumlah' class='form-control'>
                            @error('jumlah') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                        <div class='form-group'>
                            <label for='keterangan'>Keterangan</label>
                            <textarea wire:model='keterangan' id='keterangan' class='form-control'
                                style='height: 100px !important;'></textarea>
                            @error('keterangan') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                        <div class='form-group'>
                            <label for='status'>Status</label>
                            <select wire:model='status' id='status' class='form-control select2'>
                                <option value='pending'>Pending</option>
                                <option value='disetujui'>Disetujui</option>
                                <option value='ditolak'>Ditolak</option>
                            </select>
                            @error('status') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' wire:click='cancel()' class='btn btn-secondary tw-bg-gray-300'
                            data-dismiss='modal'>Close</button>
                        <button type='submit' wire:click.prevent='{{ $isEditing ? 'update()' : 'store()' }}'
                            wire:loading.attr='disabled' class='btn btn-primary tw-bg-blue-500'>Save Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('general-css')
<link href="{{ asset('assets/midragon/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@push('js-libraries')
<script src="{{ asset('/assets/midragon/select2/select2.full.min.js') }}"></script>
@endpush

@push('scripts')
<script>
    window.addEventListener('initSelect2', event => {
        $(document).ready(function () {
            $('.select2').select2();

            $('.select2').on('change', function (e) {
                var id = $(this).attr('id');
                var data = $(this).select2("val");
                @this.set(id, data);
            });
        });
    })

</script>
@endpush
