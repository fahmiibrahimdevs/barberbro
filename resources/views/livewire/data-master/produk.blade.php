<div>
    <section class='section custom-section'>
        <div class='section-header'>
            <h1>Produk</h1>
        </div>

        <div class='section-body'>
            <div class='card'>
                <h3>Tabel Produk</h3>
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
                    <div class='table-responsive tw-max-h-[670px] no-scrollbar'>
                        <table class='tw-w-full tw-table-auto'>
                            <thead class='tw-sticky tw-top-0'>
                                <tr class='tw-text-gray-700'>
                                    <th width='6%' class='text-center'>No</th>
                                    <th width='60%' class='tw-whitespace-nowrap'>Nama Kategori</th>
                                    {{-- <th class='tw-whitespace-nowrap'>Kode Item</th> --}}
                                    <th width='10%' class='tw-whitespace-nowrap tw-text-right'>Harga</th>
                                    <th width='10%' class='tw-whitespace-nowrap tw-text-right'>Stock</th>
                                    <th width='10%' class='tw-whitespace-nowrap tw-text-center'>Satuan</th>
                                    {{-- <th class='tw-whitespace-nowrap'>Harga Pokok</th> --}}
                                    {{-- <th class='tw-whitespace-nowrap'>Harga Jual</th> --}}
                                    <th width='10%' class='text-center'><i class='fas fa-cog'></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data->groupBy('nama_cabang') as $row)
                                <tr>
                                    <td class="tw-text-sm tw-tracking-wider" colspan="6">
                                        <b>Lokasi: {{ $row[0]['nama_cabang'] }}</b>
                                    </td>
                                </tr>
                                @foreach ($row as $result)
                                <tr class='text-center'>
                                    <td class='tw-whitespace-nowrap'>{{ $loop->index + 1 }}</td>
                                    <td class=' text-left tw-flex tw-items-center'>
                                        <img src="{{ asset('assets/stisla/img/example-image-50.jpg') }}"
                                            class="tw-rounded-lg tw-w-16 tw-h-16 tw-object-cover tw-mr-3">
                                        <div class="tw--mt-1">
                                            <p class="tw-font-bold">{{ $result['nama_kategori'] }}</p>
                                            <p class="tw-leading-5 tw-text-gray-500">{{ $result['nama_item'] }}</p>
                                            <p>{{ $result['deskripsi'] }}</p>
                                        </div>
                                    </td>
                                    {{-- <td class='tw-whitespace-nowrap text-left'>{{ $result['kode_item'] }}</td> --}}
                                    <td class='tw-whitespace-nowrap text-right'>
                                        Rp{{ number_format($result['harga_jasa'], 0, ',', '.') }}</td>
                                    <td class='tw-whitespace-nowrap text-right'>{{ $result['stock'] }}</td>
                                    <td class='tw-whitespace-nowrap text-center'>{{ $result['nama_satuan'] }}</td>
                                    {{-- <td class='tw-whitespace-nowrap text-left'>{{ $result['harga_pokok'] }}</td>
                                    --}}
                                    {{-- <td class='tw-whitespace-nowrap text-left'>{{ $result['harga_jual'] }}</td>
                                    --}}
                                    <td class='tw-whitespace-nowrap'>
                                        <button wire:click.prevent='edit({{ $result['id'] }})' class='btn btn-primary'
                                            data-toggle='modal' data-target='#formDataModal'>
                                            <i class='fas fa-edit'></i>
                                        </button>
                                        <button wire:click.prevent='deleteConfirm({{ $result['id'] }})'
                                            class='btn btn-danger'>
                                            <i class='fas fa-trash'></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @empty
                                <tr>
                                    <td colspan='6' class='text-center'>No data available in the table</td>
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

    <div class='modal fade' wire:ignore.self id='formDataModal' aria-labelledby='formDataModalLabel' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='formDataModalLabel'>{{ $isEditing ? 'Edit Data' : 'Add Data' }}</h5>
                    <button type='button' wire:click='cancel()' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <form>
                    <div class='modal-body'>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class='form-group'>
                                    <label for='id_cabang'>Cabang Lokasi</label>
                                    {{-- <span>{{ $id_cabang }}</span> --}}
                                    <div wire:ignore>
                                        <select wire:model='id_cabang' id='id_cabang' class='form-control select2'>
                                            @foreach ($cabangs as $cabang)
                                            <option value='{{ $cabang->id }}'>{{ $cabang->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('id_cabang') <span class='text-danger'>{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class='form-group'>
                                    <label for='id_kategori'>Kategori</label>
                                    {{-- <span>{{ $id_kategori }}</span> --}}
                                    <div wire:ignore>
                                        <select wire:model='id_kategori' id='id_kategori' class='form-control select2'>
                                            @foreach ($kategoris as $kategori)
                                            <option value='{{ $kategori->id }}'>{{ $kategori->nama_kategori }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('id_kategori') <span class='text-danger'>{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label for='kode_item'>Kode Item</label>
                            <input type='text' wire:model='kode_item' id='kode_item' class='form-control'>
                            @error('kode_item') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                        <div class='form-group'>
                            <label for='nama_item'>Nama Item</label>
                            <input type='text' wire:model='nama_item' id='nama_item' class='form-control'>
                            @error('nama_item') <span class='text-danger'>{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class='form-group'>
                                    <label for='harga_jasa'>Harga</label>
                                    <input type='number' wire:model='harga_jasa' id='harga_jasa' class='form-control'>
                                    @error('harga_jasa') <span class='text-danger'>{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class='form-group'>
                                    <label for='id_satuan'>Satuan</label>
                                    {{-- <span>{{ $id_satuan }}</span> --}}
                                    <div wire:ignore>
                                        <select wire:model='id_satuan' id='id_satuan' class='form-control select2'>
                                            @foreach ($satuans as $satuan)
                                            <option value='{{ $satuan->id }}'>{{ $satuan->nama_satuan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('id_satuan') <span class='text-danger'>{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- <div class='form-group'>
                            <label for='harga_pokok'>Harga Pokok</label>
                            <input type='number' wire:model='harga_pokok' id='harga_pokok' class='form-control'>
                            @error('harga_pokok') <span class='text-danger'>{{ $message }}</span> @enderror
                    </div>
                    <div class='form-group'>
                        <label for='harga_jual'>Harga Jual</label>
                        <input type='number' wire:model='harga_jual' id='harga_jual' class='form-control'>
                        @error('harga_jual') <span class='text-danger'>{{ $message }}</span> @enderror
                    </div> --}}
                    <div class='form-group'>
                        <label for='deskripsi'>Deskripsi</label>
                        <textarea wire:model='deskripsi' id='deskripsi' class='form-control'
                            style='height: 100px !important;'></textarea>
                        @error('deskripsi') <span class='text-danger'>{{ $message }}</span> @enderror
                    </div>
                    <div class='form-group'>
                        <label for='gambar'>Gambar</label>
                        <input type='file' wire:model='gambar' id='gambar' class='form-control'>
                        @error('gambar') <span class='text-danger'>{{ $message }}</span> @enderror
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
            $('.select2').select2({
                dropdownParent: $("#formDataModal")
            });

            $('.select2').on('change', function (e) {
                var id = $(this).attr('id');
                var data = $(this).select2("val");
                console.log(id, data)
                @this.set(id, data);
            });
        });
    })

</script>
@endpush
