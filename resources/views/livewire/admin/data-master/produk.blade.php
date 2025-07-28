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
                            placeholder='nama item, deskripsi' class='form-control'>
                    </div>
                    <div class='table-responsive tw-max-h-[670px] no-scrollbar'>
                        <table class='tw-w-full tw-table-auto'>
                            <thead class='tw-sticky tw-top-0'>
                                <tr class='tw-text-gray-700'>
                                    <th width='6%' class='text-center'>No</th>
                                    <th width='80%' class='tw-whitespace-nowrap'>Nama Kategori</th>
                                    {{-- <th class='tw-whitespace-nowrap'>Kode Item</th> --}}
                                    <th width='3%' class='tw-whitespace-nowrap tw-text-right'>Harga</th>
                                    <th width='3%' class='tw-whitespace-nowrap tw-text-right'>Stock</th>
                                    <th width='3%' class='tw-whitespace-nowrap tw-text-right'>Satuan</th>
                                    <th width='10%' class='tw-whitespace-nowrap tw-text-right'>Komisi (%)</th>
                                    {{-- <th class='tw-whitespace-nowrap'>Harga Pokok</th> --}}
                                    {{-- <th class='tw-whitespace-nowrap'>Harga Jual</th> --}}
                                    <th width='10%' class='text-center'><i class='fas fa-cog'></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $result)
                                <tr class='text-center'>
                                    <td class='tw-whitespace-nowrap'>
                                        {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                    <td class='text-left tw-flex tw-items-center'>
                                        <img src="{{ asset('assets/stisla/img/example-image-50.jpg') }}"
                                            class="tw-rounded-lg tw-w-16 tw-h-16 tw-object-cover tw-mr-3">
                                        <div class="tw--mt-1">
                                            <p class="tw-font-bold">{{ $result->nama_kategori }}</p>
                                            <p class="tw-leading-5 tw-whitespace-nowrap tw-text-gray-600">
                                                {{ $result->nama_item }}</p>
                                            <p class="tw-leading-5 tw-text-xs tw-text-gray-400">
                                                {{ $result->deskripsi }}</p>
                                        </div>
                                    </td>
                                    {{-- <td class='tw-whitespace-nowrap text-left'>{{ $result->kode_item }}</td> --}}
                                    <td class='tw-pl-20 text-right'>@money($result->harga_jasa)</td>
                                    <td class='tw-whitespace-nowrap text-right'>
                                        @if ($result->nama_kategori == "Produk Barbershop" || $result->nama_kategori
                                        == "Produk Umum")
                                        {{ $result->stock }},00
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class='tw-whitespace-nowrap text-right'>{{ $result->nama_satuan }}</td>
                                    <td class='tw-whitespace-nowrap text-right'>
                                        @if ($result->nama_kategori == "Produk Barbershop")
                                        {{ $result->komisi }}%
                                        @elseif($result->nama_kategori == "Produk Umum")
                                        -
                                        @else
                                        <i title="Jumlah karyawan yang dapat komisi">
                                            {{ $komisi_data[$result->id] ?? 0 }} dari
                                            {{ $total_karyawan[$result->id_cabang] ?? 0 }} capster
                                        </i>
                                        @endif
                                    </td>
                                    {{-- <td class='tw-whitespace-nowrap text-left'>{{ $result->harga_pokok }}</td>
                                    --}}
                                    {{-- <td class='tw-whitespace-nowrap text-left'>{{ $result->harga_jual }}</td>
                                    --}}
                                    <td class='tw-whitespace-nowrap'>
                                        <button wire:click.prevent='edit({{ $result->id }})' class='btn btn-primary'
                                            data-toggle='modal' data-target='#formDataModal'>
                                            <i class='fas fa-edit'></i>
                                        </button>
                                        <button wire:click.prevent='deleteConfirm({{ $result->id }})'
                                            class='btn btn-danger'>
                                            <i class='fas fa-trash'></i>
                                        </button>
                                    </td>
                                </tr>
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
        @if ($karyawans->isNotEmpty())
        <button wire:click.prevent='isEditingMode(false)' class='btn-modal' data-toggle='modal' data-backdrop='static'
            data-keyboard='false' data-target='#formDataModal'>
            <i class='far fa-plus'></i>
        </button>
        @endif
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
                    <div class='modal-body tw-p-0'>
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item tw-pl-4 tw-pt-4 lg:tw-pl-6" role="presentation" wire:ignore>
                                <button class="nav-link active" id="pills-1-tab" data-toggle="pill"
                                    data-target="#pills-1" type="button" role="tab" aria-controls="pills-1"
                                    aria-selected="true">Form Produk</button>
                            </li>
                            @if (!$isKomisi)
                            <li class="nav-item tw-pt-4" role="presentation" wire:ignore>
                                <button class="nav-link" id="pills-2-tab" data-toggle="pill" data-target="#pills-2"
                                    type="button" role="tab" aria-controls="pills-2" aria-selected="false">Set Komisi
                                    (%)</button>
                            </li>
                            @endif
                        </ul>
                        <div class="tab-content" id="pills-tabContent" wire:ignore.self>
                            <div class="tab-pane fade show active tw-px-4 lg:tw-px-6" id="pills-1" role="tabpanel"
                                aria-labelledby="pills-1-tab">
                                @if ($isEditing)
                                <div class='form-group'>
                                    <label for='nama_item'>Nama Item</label>
                                    <input type='text' wire:model='nama_item' id='nama_item' class='form-control'>
                                    @error('nama_item') <span class='text-danger'>{{ $message }}</span>
                                    @enderror
                                </div>
                                @else
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class='form-group'>
                                            <label for='id_kategori'>Kategori</label>
                                            {{-- <span>{{ $id_kategori }}</span> --}}
                                            <div wire:ignore>
                                                <select wire:model='id_kategori' id='id_kategori'
                                                    class='form-control select2'>
                                                    @foreach ($kategoris as $kategori)
                                                    <option value='{{ $kategori->id }}'>{{ $kategori->nama_kategori }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('id_kategori') <span class='text-danger'>{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class='form-group'>
                                            <label for='nama_item'>Nama Item</label>
                                            <input type='text' wire:model='nama_item' id='nama_item'
                                                class='form-control'>
                                            @error('nama_item') <span class='text-danger'>{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endif
                                {{-- <div class='form-group'>
                                    <label for='kode_item'>Kode Item</label>
                                    <input type='text' wire:model='kode_item' id='kode_item' class='form-control'>
                                    @error('kode_item') <span class='text-danger'>{{ $message }}</span> @enderror
                            </div> --}}

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class='form-group'>
                                        <label for='harga_jasa'>Harga</label>
                                        <input type='number' wire:model='harga_jasa' id='harga_jasa'
                                            class='form-control'>
                                        @error('harga_jasa') <span class='text-danger'>{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class='form-group'>
                                        <label for='id_satuan'>Satuan</label>
                                        {{-- <span>{{ $id_satuan }}</span> --}}
                                        {{-- <div wire:ignore> --}}
                                        <select wire:model='id_satuan' id='id_satuan' class='form-control select2'>
                                            @foreach ($satuans as $satuan)
                                            <option value='{{ $satuan->id }}'>{{ $satuan->nama_satuan }}</option>
                                            @endforeach
                                        </select>
                                        {{-- </div> --}}
                                        @error('id_satuan') <span class='text-danger'>{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                            @if ($isKomisi && !$isProdukUmum)
                            <div class='form-group'>
                                <label for='komisi'>Komisi (%)</label>
                                <input type='number' wire:model='komisi' id='komisi' class='form-control'>
                                @error('komisi') <span class='text-danger'>{{ $message }}</span> @enderror
                            </div>
                            @endif
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
                    <div class="tab-pane fade" id="pills-2" role="tabpanel" aria-labelledby="pills-2-tab">
                        {{-- <pre><code>{{ json_encode($komisi_karyawan, JSON_PRETTY_PRINT) }}</code></pre> --}}
                        <table class="table-responsive tw-w-full tw-table-auto">
                            <thead>
                                <tr class="tw-whitespace-nowrap">
                                    <th width="5%">No</th>
                                    <th width="90%">Nama Karyawan</th>
                                    <th width="5%">Komisi (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($karyawans as $karyawan)
                                <tr wire:key="komisi-{{ $karyawan->id }}">
                                    <td class="tw-whitespace-nowrap">{{ $loop->index + 1 }}</td>
                                    <td>{{ $karyawan->name }}</td>
                                    <td>
                                        <input type="number" step="1" wire:model="komisi_karyawan.{{ $karyawan->id }}"
                                            class="form-control tw-ml-auto tw-text-right tw-w-full">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
                // console.log(id, data)
                @this.set(id, data);
            });
        });
    })

    window.addEventListener('setBackNavs', event => {
        $(document).ready(function () {
            setTimeout(() => {
                $('#pills-1-tab').addClass('active')
                $('#pills-2-tab').removeClass('active')
                $('#pills-1').addClass('active show')
                $('#pills-2').removeClass('active show')
            }, 500);
        });
    })

</script>
@endpush
