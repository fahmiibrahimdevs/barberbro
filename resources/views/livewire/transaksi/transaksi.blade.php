<div>
    <section class="section custom-section">
        <div class="section-header">
            <h1>Transaksi</h1>
        </div>

        <div class="section-body">
            <div class="card">
                <h3>Table Transaksi</h3>
                <div class="card-body">
                    <div class="show-entries">
                        <p class="show-entries-show">Show</p>
                        <select wire:model.live="lengthData" id="length-data">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                            <option value="500">500</option>
                        </select>
                        <p class="show-entries-entries">Entries</p>
                    </div>
                    <div class="search-column">
                        <p>Search: </p><input type="search" wire:model.live.debounce.750ms="searchTerm" id="search-data"
                            placeholder="Search here..." class="form-control" value="">
                    </div>
                    <div class="table-responsive tw-max-h-96 no-scrollbar">
                        <table class="tw-w-full tw-table-auto">
                            <thead class="tw-sticky tw-top-0">
                                <tr class="tw-text-gray-700">
                                    <th width="6%" class="text-center">No</th>
                                    <th class="tw-whitespace-nowrap">No. Transaksi</th>
                                    <th class="tw-whitespace-nowrap">Nama Pelanggan</th>
                                    <th class="tw-whitespace-nowrap">Total</th>
                                    <th class="text-center"><i class="fas fa-cog"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data->groupBy('nama_cabang') as $row)
                                <tr>
                                    <td class="tw-text-sm tw-tracking-wider" colspan="10">
                                        <b>Lokasi: {{ $row[0]['nama_cabang'] }}</b>
                                    </td>
                                </tr>
                                @foreach ($row as $result)
                                <tr>
                                    <td class="text-center">{{ $loop->index + 1 }}</td>
                                    <td>{{ $result['no_transaksi'] }}</td>
                                    <td class='tw-whitespace-nowrap text-left tw-flex tw-items-center'>
                                        <img src="{{ asset('assets/stisla/img/avatar/avatar-1.png') }}"
                                            class="tw-rounded-full tw-w-8 tw-h-8 tw-mr-3">
                                        {{ $result['nama_pelanggan'] }}
                                    </td>
                                    <td>{{ $result['total_akhir'] }}</td>
                                    <td class="text-center">
                                        <button wire:click.prevent="edit({{ $result['id'] }})" class="btn btn-primary"
                                            data-toggle="modal" data-target="#formDataModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click.prevent="deleteConfirm({{ $result['id'] }})"
                                            class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Not data available in the table</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-5 px-3">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
        <button wire:click.prevent="isEditingMode(false)" class="btn-modal" data-toggle="modal" data-backdrop="static"
            data-keyboard="false" data-target="#formDataModal">
            <i class="far fa-plus"></i>
        </button>
    </section>
    <div class="modal fade" data-backdrop="static" wire:ignore.self id="formDataModal"
        aria-labelledby="formDataModalLabel" aria-hidden="true">
        <div class='modal-dialog tw-w-full tw-m-0 sm:tw-w-auto sm:tw-m-[1.75rem_auto]'>
            <div class='modal-content tw-rounded-none lg:tw-rounded-md'>
                <div class="modal-header tw-px-4 lg:tw-px-6 tw-sticky tw-top-[0] tw-bg-white tw-z-50">
                    <h5 class="modal-title" id="formDataModalLabel">{{ $isEditing ? 'Edit Data' : 'Tambah Transaksi' }}
                    </h5>
                    <button type="button" wire:click="cancel()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body tw-px-4 lg:tw-px-6">
                        <div class='form-group'>
                            <label for='id_pelanggan'>Pelanggan (Opsional)</label>
                            {{-- <span>{{ $id_pelanggan }}</span> --}}
                            <div wire:ignore>
                                <select wire:model='id_pelanggan' id='id_pelanggan' class='form-control select2'>
                                    @foreach ($pelanggans as $pelanggan)
                                    <option value='{{ $pelanggan->id }}'>{{ $pelanggan->nama_pelanggan }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('id_pelanggan') <span class='text-danger'>{{ $message }}</span>
                            @enderror
                        </div>
                        <label
                            class="tw-text-sm tw-font-semibold tw-text-[#34395e] tw-tracking-[0.5px] -tw-mt-4">Produk</label>
                        {{-- @for ($i = 1; $i <= 5; $i++) --}}
                        <div id="table-produk" class="tw-text-[#34395e] tw-tracking-[0.5px] tw-text-xs tw-mt-2">
                            <div class="tw-flex tw-justify-between tw-font-semibold tw-items-center">
                                <p>Gatsby Fiber Pomade Glossy Finish 80G</p>
                                <p class="text-primary tw-text-sm">Rp 35.000</p>
                            </div>
                            <div class="tw-flex tw-justify-between tw-mb-2">
                                <div class="tw-whitespace-nowrap">
                                    <p>1 x Rp 40.000</p>
                                    <p>Produk Barbershop</p>
                                </div>
                                <div class="tw-flex tw-items-center tw-space-x-3">
                                    <i class="far fa-trash tw-text-lg text-danger tw-ml-auto"></i>
                                    {{-- <input type="number" class="form-control tw-w-1/4 tw-text-center tw-ml-auto">
                                    <i class="far fa-plus-circle tw-text-lg text-primary tw-ml-auto"></i> --}}
                                </div>
                            </div>
                            <div id="accordion">
                                <div class="accordion">
                                    <div class="accordion-header tw-py-1 tw-text-center" role="button"
                                        data-toggle="collapse" data-target="#panel-body-1">
                                        <i class="fas fa-angle-down"></i>
                                    </div>
                                    <div class="accordion-body collapse tw-px-0" id="panel-body-1"
                                        data-parent="#accordion">
                                        <div class="tw-flex tw-justify-between">
                                            <p>Diskon</p>
                                            <p class="text-danger">-Rp 5.000</p>
                                        </div>
                                        <div class="tw-flex tw-justify-between">
                                            <p>Karyawan</p>
                                            <p>Muhammad Ikhwanul Muslim</p>
                                        </div>
                                        <div class="tw-flex tw-justify-between">
                                            <p>Komisi (50%)</p>
                                            <p>Rp 20.000</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="tw-bg-gray-50">
                        </div>
                        {{-- @endfor --}}
                        <center class="tw-py-5">
                            <button wire:click.prevent="initSelectTwo()" class="btn btn-primary tw-text-center"
                                data-toggle="modal" data-target="#formDataProduk">Pilih Produk</button>
                        </center>
                        <div class="form-group">
                            <label for="catatan">Catatan (Opsional)</label>
                            <textarea wire:model="catatan" id="catatan" class="form-control"
                                style="height: 100px"></textarea>
                        </div>
                        <hr class="tw-bg-gray-200">
                        <div class="tw-mt-4">
                            <div class="tw-flex tw-justify-between tw-items-center">
                                <p>Sub Total</p>
                                <p>Rp 0</p>
                            </div>
                            <div class="tw-flex tw-justify-between tw-items-center">
                                <p>Total Diskon</p>
                                <p>-Rp 0</p>
                            </div>
                            <div class="tw-flex tw-justify-between tw-items-center">
                                <p>Total Komisi Karyawan</p>
                                <p>Rp 0</p>
                            </div>
                        </div>
                    </div>
                    <div
                        class="modal-footer tw-sticky tw-bottom-[0] tw-bg-white tw-z-50 tw-px-2 tw-flex tw-justify-between tw-items-center">
                        <div class="tw-text-sm tw-text-[#34395e] tw-tracking-[0.5px]">
                            <p class="">Total</p>
                            <p class="tw-font-semibold tw-text-lg">Rp 0</p>
                        </div>
                        <button type="submit" wire:click.prevent="{{ $isEditing ? 'update()' : 'store()' }}"
                            wire:loading.attr="disabled"
                            class="btn btn-primary tw-bg-blue-500 form-control tw-py-2">Bayar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="formDataProduk" aria-labelledby="formDataProdukLabel"
        aria-hidden="true">
        <div class='modal-dialog tw-w-full tw-m-0 sm:tw-w-auto sm:tw-m-[1.75rem_auto]'>
            <div class='modal-content tw-rounded-none lg:tw-rounded-md'>
                <div class="modal-header tw-px-4 lg:tw-px-6 tw-sticky tw-top-[0] tw-bg-white tw-z-50">
                    <h5 class="modal-title" id="formDataProdukLabel">Pilih Produk</h5>
                    <button type="button" wire:click="cancel()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body tw-px-4 lg:tw-px-6">
                        <input type="search" wire:model.live.debounce.750ms="searchProduk" class="form-control tw-mb-5"
                            placeholder="Cari nama produk">
                        @foreach ($produks as $produk)
                        <button wire:click.prevent="initSelectTwo()" data-toggle="modal" data-target="#formCartProduk"
                            class="tw-w-full  tw-text-[#34395e] tw-tracking-[0.5px]">
                            <div class="tw-flex tw-justify-between tw-text-sm tw-items-start">
                                <p class="tw-font-semibold tw-text-xs tw-text-left tw-leading-5">
                                    {{ $produk->nama_item }}</p>
                                <p class="tw-whitespace-nowrap tw-font-semibold tw-ml-5 -tw-mt-1 text-primary">Rp
                                    {{ number_format($produk->harga_jasa,0,',','.') }}</p>
                            </div>
                            <div class="tw-text-left tw-flex tw-justify-between">
                                <p class="tw-text-gray-500 tw-text-xs">{{ $produk->deskripsi }}</p>
                                <p class="tw-text-gray-500 tw-text-xs">{{ $produk->nama_kategori }}</p>
                            </div>
                        </button>
                        <hr class="tw-bg-gray-50 tw-my-3">
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" data-backdrop="static" wire:ignore.self id="formCartProduk"
        aria-labelledby="formCartProdukLabel" aria-hidden="true">
        <div class='modal-dialog tw-w-full tw-m-0 sm:tw-w-auto sm:tw-m-[1.75rem_auto]'>
            <div class='modal-content tw-rounded-none lg:tw-rounded-md'>
                <div class="modal-header tw-px-4 lg:tw-px-6 tw-sticky tw-top-[0] tw-bg-white tw-z-50">
                    <h5 class="modal-title" id="formCartProdukLabel">Add Cart Produk</h5>
                    <button type="button" wire:click="cancel()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body tw-px-4 lg:tw-px-6 tw-text-sm tw-text-[#34395e] tw-tracking-[0.5px]">
                        <div class="form-group">
                            <label for="id_karyawan">Karyawan</label>
                            <div wire:ignore>
                                <select wire:model="id_karyawan" id="id_karyawan" class="form-control select2">
                                    <option value="" disabled>-- Opsi Pilihan --</option>
                                    @foreach ($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}">{{ $karyawan->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="tw-flex tw-justify-between tw-text-sm">
                            <p class="tw-font-semibold">Jasa Potong Rambut</p>
                            <p class="tw-font-semibold text-primary">Rp 40.000</p>
                        </div>
                        <div class="tw-flex tw-justify-between tw-text-sm">
                            <p class="tw-text-gray-500">Pampodour</p>
                            <p class="tw-text-gray-500">Jasa Barbershop</p>
                        </div>
                        <div class="tw-flex tw-space-x-10 tw-justify-center tw-my-5">
                            <button wire:click.prevent="decrementJumlah()" class="btn btn-danger">-</button>
                            <p class="tw-text-2xl tw-font-semibold">{{ $jumlah }}</p>
                            <button wire:click.prevent="incrementJumlah()" class="btn btn-primary">+</button>
                        </div>
                        <div class="form-group">
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-2 form-group">
                                <label>Diskon</label>
                                <label class="custom-switch">
                                    <input type="checkbox" wire:key="{{ rand() }}" wire:model.live="isPersentase"
                                        name="custom-switch-checkbox" class="custom-switch-input">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">Persentase</span>
                                </label>
                            </div>
                            {{ $input_diskon }}
                            @if ($isPersentase)
                            <div class="input-group">
                                <input type="number" wire:model.lazy="input_diskon"
                                    class="form-control phone-number tw-text-right">
                                <div class="input-group-prepend">
                                    <div class="input-group-text tw-rounded-r-md">
                                        %
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        Rp
                                    </div>
                                </div>
                                <input type="number" wire:model.lazy="input_diskon" class="form-control phone-number">
                            </div>
                            @endif
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <p>Sub Total</p>
                            <p>Rp 40.000</p>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <p>Diskon</p>
                            <p>Rp 5.000</p>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <p>Total Harga</p>
                            <p>Rp 35.000</p>
                        </div>
                        <div class="tw-flex tw-justify-between">
                            <p>Komisi</p>
                            <p>Rp 20.000</p>
                        </div>
                    </div>
                    <div class="tw-px-6 tw-pb-6">
                        <div class="row">
                            <div class="col-6">
                                <button type="button" wire:click="cancel()"
                                    class="btn btn-secondary form-control tw-bg-gray-300"
                                    data-dismiss="modal">Cancel</button>
                            </div>
                            <div class="col-6">
                                <button type="submit" wire:click.prevent="{{ $isEditing ? 'update()' : 'store()' }}"
                                    wire:loading.attr="disabled"
                                    class="btn btn-primary form-control tw-bg-blue-500">OK</button>
                            </div>
                        </div>
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
                dropdownParent: $("#formDataProduk")
                dropdownParent: $("#formCartProduk")
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
