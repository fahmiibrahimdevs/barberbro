<div>
    <section class='section custom-section'>
        <div class='section-header'>
            <h1>Cash On Bank</h1>
            <div class="tw-ml-auto tw-space-x-1">
                <button class="btn btn-primary" wire:click.prevent='isEditingMode(false, "In")' data-toggle='modal'
                    data-backdrop='static' data-keyboard='false' data-target='#formDataModal'>Tambah Pemasukan</button>
                <button class="btn btn-outline-danger" wire:click.prevent='isEditingMode(false, "Out")'
                    data-toggle='modal' data-backdrop='static' data-keyboard='false' data-target='#formDataModal'>Tambah
                    Pengeluaran</button>
            </div>
        </div>

        <div class='section-body'>
            <div class='card'>
                <h3>Tabel Cash On Bank</h3>
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
                                    <th class='tw-whitespace-nowrap'>User</th>
                                    <th class='tw-whitespace-nowrap'>Keterangan</th>
                                    <th class='tw-whitespace-nowrap'>Kategori</th>
                                    <th class='tw-whitespace-nowrap'>Pemasukan</th>
                                    <th class='tw-whitespace-nowrap'>Pengeluaran</th>
                                    <th class='tw-whitespace-nowrap'>Saldo Akhir</th>
                                    <th class='text-center'><i class='fas fa-cog'></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $counter = 1;
                                $amountIn = 0;
                                $amountOut = 0;
                                $amountBalance = 0;
                                $amountBalanceLast = 0;
                                @endphp
                                @forelse ($data->groupBy('nama_cabang') as $row)
                                <tr>
                                    <td class="tw-text-sm tw-tracking-wider" colspan="10">
                                        <b>Lokasi: {{ $row[0]->nama_cabang }}</b>
                                    </td>
                                </tr>
                                @foreach ($row->groupBy('tanggal') as $item)
                                <tr>
                                    <td class="tw-text-sm tw-tracking-wider" colspan="10">
                                        <b>Tanggal: {{ $item[0]->tanggal }}</b>
                                    </td>
                                </tr>
                                @foreach ($item as $results)
                                <tr class='text-center'>
                                    <td>{{ ($data->currentPage() - 1) * $data->perPage() + $counter++ }}</td>
                                    <td class='tw-whitespace-nowrap text-left'>{{ $results->name }}</td>
                                    <td class='tw-whitespace-nowrap text-left'>{{ $results->keterangan }}</td>
                                    <td class='tw-whitespace-nowrap text-left'>{{ $results->nama_kategori }}</td>
                                    <td class='tw-whitespace-nowrap text-left'>
                                        @if ($results->status == 'In')
                                        @php
                                        $amountIn += $results->jumlah;
                                        @endphp
                                        <span class="text-success">+@money($results->jumlah)</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class='tw-whitespace-nowrap text-left'>
                                        @if ($results->status == 'Out')
                                        @php
                                        $amountOut += $results->jumlah;
                                        @endphp
                                        <span class="text-danger">-@money($results->jumlah)</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class='tw-whitespace-nowrap text-left'>
                                        @php
                                        $amountBalanceLast = last((array)$results->balancing);
                                        @endphp
                                        @money($amountBalanceLast)
                                    </td>
                                    <td class='tw-whitespace-nowrap'>
                                        <button wire:click.prevent='edit({{ $results->id }}, "{{ $results->status }}")'
                                            class='btn btn-primary' data-toggle='modal' data-target='#formDataModal'>
                                            <i class='fas fa-edit'></i>
                                        </button>
                                        <button wire:click.prevent='deleteConfirm({{ $results->id }})'
                                            class='btn btn-danger'>
                                            <i class='fas fa-trash'></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @endforeach
                                @empty
                                <tr>
                                    <td colspan='8' class='text-center'>No data available in the table</td>
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
    </section>

    <div class='modal fade' wire:ignore.self id='formDataModal' aria-labelledby='formDataModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='formDataModalLabel'>
                        @if (!$isEditing && $kategori == "Out")
                        Add Data Pengeluaran
                        @elseif ($isEditing && $kategori == "Out")
                        Edit Data Pengeluaran
                        @elseif (!$isEditing && $kategori == "In")
                        Add Data Pemasukan
                        @elseif ($isEditing && $kategori == "In")
                        Edit Data Pemasukan
                        @endif
                    </h5>
                    <button type='button' wire:click='cancel()' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <form>
                    <div class='modal-body'>
                        <div class='form-group'>
                            <label for='id_cabang'>Cabang Lokasi</label>
                            <select wire:model='id_cabang' id='id_cabang' class='form-control select2'>
                                <option value='' disabled>-- Opsi Pilihan --</option>
                                @foreach ($cabangs as $cabang)
                                <option value='{{ $cabang->id }}'>{{ $cabang->nama_cabang }}</option>
                                @endforeach
                            </select>
                            @error('id_cabang') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                        <div class='form-group'>
                            <label for='tanggal'>Tanggal</label>
                            <input type='date' wire:model='tanggal' id='tanggal' class='form-control'>
                            @error('tanggal') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                        @if ($kategori == "Out")
                        <div class='form-group'>
                            <label for='id_kategori_keuangan'>Kategori Keuangan</label>
                            <select wire:model='id_kategori_keuangan' id='id_kategori_keuangan'
                                class='form-control select2'>
                                <option value='' disabled>-- Opsi Pilihan --</option>
                                @foreach ($keuangans as $keuangan)
                                <option value='{{ $keuangan->id }}'>{{ $keuangan->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('id_kategori_keuangan') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                        @endif
                        <div class='form-group'>
                            <label for='keterangan'>Keterangan</label>
                            <textarea wire:model='keterangan' id='keterangan' class='form-control'
                                style="height: 100px"></textarea>
                            @error('keterangan') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                        <div class='form-group'>
                            <label for='jumlah'>Jumlah</label>
                            <input type='number' wire:model='jumlah' id='jumlah' class='form-control'>
                            @error('jumlah') <span class='text-danger'>{{ $message }}</span> @enderror
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
