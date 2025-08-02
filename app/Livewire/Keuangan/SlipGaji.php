<?php

namespace App\Livewire\Keuangan;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DetailSlipGaji;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use App\Services\GlobalDataService;
use Illuminate\Support\Facades\Crypt;
use App\Models\SlipGaji as ModelsSlipGaji;

class SlipGaji extends Component
{
    use WithPagination;
    #[Title('Slip Gaji')]

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'periode_mulai'       => 'required',
        'periode_selesai'     => 'required',
        'id_karyawan'         => 'required',
        'total_tunjangan'     => '',
        'total_potongan'      => '',
        'total_gaji'          => '',
        'status'              => 'required',
    ];

    public $lengthData = 25;
    public $searchTerm;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;
    public $karyawans;
    /**
     * @var \Illuminate\Support\Collection
     */
    public $komisi_transaksi = [], $total_komisi = 0, $data_kasbon = [], $total_kasbon = 0;
    public $tunjangans = [], $potongans = [];
    public $periode_mulai, $periode_selesai, $id_karyawan, $total_tunjangan, $total_potongan, $total_gaji, $status, $nama_karyawan;

    public function mount(GlobalDataService $globalDataService)
    {
        $this->karyawans = $globalDataService->getKaryawans();
        $this->periode_mulai       = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->periode_selesai     = Carbon::now()->endOfMonth()->format('Y-m-d');

        $this->resetInputFields();
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = ModelsSlipGaji::select('slip_gaji.id', 'slip_gaji.periode_mulai', 'slip_gaji.periode_selesai', 'slip_gaji.total_gaji', 'slip_gaji.status', 'users.name as nama_karyawan', 'cabang_lokasi.nama_cabang')
            ->join('daftar_karyawan', 'slip_gaji.id_karyawan', '=', 'daftar_karyawan.id')
            ->join('cabang_lokasi', 'cabang_lokasi.id', 'daftar_karyawan.id_cabang')
            ->join('users', 'daftar_karyawan.id_user', '=', 'users.id')
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', $search);
            })
            ->orderBy('id', 'ASC')
            ->orderBy('status', 'DESC')
            ->paginate($this->lengthData);

        return view('livewire.keuangan.slip-gaji', compact('data'));
    }

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $slip = ModelsSlipGaji::create([
                'periode_mulai'   => $this->periode_mulai,
                'periode_selesai' => $this->periode_selesai,
                'id_karyawan'     => $this->id_karyawan,
                'total_tunjangan' => $this->total_tunjangan,
                'total_potongan'  => $this->total_potongan,
                'total_gaji'      => $this->total_gaji,
                'status'          => $this->status,
            ]);

            // Tunjangan
            $tunjanganInsert = [];
            foreach ($this->tunjangans as $item) {
                if (!empty($item['nama_komponen']) && $item['jumlah'] > 0) {
                    $tunjanganInsert[] = [
                        'id_slip_gaji'  => $slip->id,
                        'nama_komponen' => $item['nama_komponen'],
                        'jumlah'        => $item['jumlah'],
                        'tipe'          => 'tunjangan',
                    ];
                }
            }
            if (count($tunjanganInsert)) {
                DB::table('detail_slip_gaji')->insert($tunjanganInsert);
            }

            // Potongan
            $potonganInsert = [];
            foreach ($this->potongans as $item) {
                if (!empty($item['nama_komponen']) && $item['jumlah'] > 0) {
                    $potonganInsert[] = [
                        'id_slip_gaji'  => $slip->id,
                        'nama_komponen' => $item['nama_komponen'],
                        'jumlah'        => $item['jumlah'],
                        'tipe'          => 'potongan',
                    ];
                }
            }
            if (count($potonganInsert)) {
                DB::table('detail_slip_gaji')->insert($potonganInsert);
            }

            DB::commit();
            $this->dispatchSwalSlipGaji($slip->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatchAlert('error', 'Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            report($e);
        }
    }

    public function edit($id)
    {
        $this->initSelect2();
        $this->isEditing = true;
        $data = DB::table('slip_gaji')->where('id', $id)->first();
        $this->dataId = $id;
        $this->periode_mulai  = $data->periode_mulai;
        $this->periode_selesai  = $data->periode_selesai;
        $this->id_karyawan  = $data->id_karyawan;
        $this->total_tunjangan  = $data->total_tunjangan;
        $this->total_potongan  = $data->total_potongan;
        $this->total_gaji  = $data->total_gaji;
        $this->status  = $data->status;

        $this->tunjangans = DB::table('detail_slip_gaji')
            ->where('id_slip_gaji', $id)
            ->where('tipe', 'tunjangan')
            ->select('nama_komponen', 'jumlah')
            ->get()
            ->map(function ($item) {
                return [
                    'nama_komponen' => $item->nama_komponen,
                    'jumlah' => $item->jumlah,
                ];
            })->toArray();

        $this->potongans = DB::table('detail_slip_gaji')
            ->where('id_slip_gaji', $id)
            ->where('tipe', 'potongan')
            ->select('nama_komponen', 'jumlah')
            ->get()
            ->map(function ($item) {
                return [
                    'nama_komponen' => $item->nama_komponen,
                    'jumlah' => $item->jumlah,
                ];
            })->toArray();
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            ModelsSlipGaji::findOrFail($this->dataId)->update([
                'periode_mulai'   => $this->periode_mulai,
                'periode_selesai' => $this->periode_selesai,
                'total_tunjangan' => $this->total_tunjangan,
                'total_potongan'  => $this->total_potongan,
                'total_gaji'      => $this->total_gaji,
                'status'          => $this->status,
            ]);

            DB::table('detail_slip_gaji')->where('id_slip_gaji', $this->dataId)->delete();

            // Insert ulang tunjangan
            $tunjanganInsert = [];
            foreach ($this->tunjangans as $item) {
                if (!empty($item['nama_komponen']) && $item['jumlah'] > 0) {
                    $tunjanganInsert[] = [
                        'id_slip_gaji'  => $this->dataId,
                        'nama_komponen' => $item['nama_komponen'],
                        'jumlah'        => $item['jumlah'],
                        'tipe'          => 'tunjangan',
                    ];
                }
            }
            if (count($tunjanganInsert)) {
                DB::table('detail_slip_gaji')->insert($tunjanganInsert);
            }

            // Insert ulang potongan
            $potonganInsert = [];
            foreach ($this->potongans as $item) {
                if (!empty($item['nama_komponen']) && $item['jumlah'] > 0) {
                    $potonganInsert[] = [
                        'id_slip_gaji'  => $this->dataId,
                        'nama_komponen' => $item['nama_komponen'],
                        'jumlah'        => $item['jumlah'],
                        'tipe'          => 'potongan',
                    ];
                }
            }
            if (count($potonganInsert)) {
                DB::table('detail_slip_gaji')->insert($potonganInsert);
            }

            DB::commit();
            $this->dispatchSwalSlipGaji($this->dataId);
            $this->resetInputFields();
            $this->tunjangans = [];
            $this->potongans = [];
            $this->dataId = null;
            $this->isEditing = false;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatchAlert('error', 'Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
            report($e);
        }
    }

    public function deleteConfirm($id)
    {
        $this->dataId = $id;
        $this->dispatch('swal:confirm', [
            'type'      => 'warning',
            'message'   => 'Are you sure?',
            'text'      => 'If you delete the data, it cannot be restored!'
        ]);
    }

    public function delete()
    {
        DB::beginTransaction();
        try {
            ModelsSlipGaji::findOrFail($this->dataId)->delete();
            DetailSlipGaji::where('id_slip_gaji', $this->dataId)->delete();

            DB::commit();
            $this->dispatchAlert('success', 'Success!', 'Data deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatchAlert('error', 'Gagal!', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function addTunjangan()
    {
        $this->initSelect2();
        $this->tunjangans[] = ['nama_komponen' => '', 'jumlah' => 0];
    }

    public function removeTunjangan($index)
    {
        $this->initSelect2();
        unset($this->tunjangans[$index]);
        $this->tunjangans = array_values($this->tunjangans); // reindex
    }

    public function addPotongan()
    {
        $this->initSelect2();
        $this->potongans[] = ['nama_komponen' => '', 'jumlah' => 0];
    }

    public function removePotongan($index)
    {
        $this->initSelect2();
        unset($this->potongans[$index]);
        $this->potongans = array_values($this->potongans); // reindex
    }

    public function review()
    {
        // 
        $this->initSelect2();
        $this->validate([
            'id_karyawan' => 'required',
        ], [
            'id_karyawan.required' => 'Karyawan wajib dipilih.',
        ]);

        $this->nama_karyawan = $this->karyawans->firstWhere('id', $this->id_karyawan)->name ?? '';

        $this->komisi_transaksi = DB::table('transaksi')
            ->select('transaksi.no_transaksi', 'transaksi.tanggal', 'detail_transaksi.komisi_nominal')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.id_transaksi')
            ->where('detail_transaksi.id_karyawan', $this->id_karyawan)
            ->whereBetween('transaksi.tanggal', [Carbon::parse($this->periode_mulai)->startOfDay(), Carbon::parse($this->periode_selesai)->endOfDay()])
            ->where('transaksi.status', 'lunas')
            ->get();

        $this->total_kasbon = DB::table('kasbon')
            ->where('id_karyawan', $this->id_karyawan)
            ->whereBetween('tgl_disetujui', [
                $this->periode_mulai,
                $this->periode_selesai
            ])
            ->where('status', 'disetujui')
            ->sum('jumlah');

        $this->total_komisi = $this->komisi_transaksi->sum('komisi_nominal');

        $this->total_tunjangan = collect($this->tunjangans)->sum('jumlah');
        $this->total_potongan = collect($this->potongans)->sum('jumlah') + $this->total_kasbon;

        $this->total_gaji = $this->total_komisi + $this->total_tunjangan - $this->total_potongan;

        // dd($this->komisi_transaksi);

        $this->dispatch('open-review-modal');
    }

    public function updatingLengthData()
    {
        $this->resetPage();
    }

    private function searchResetPage()
    {
        if ($this->searchTerm !== $this->previousSearchTerm) {
            $this->resetPage();
        }

        $this->previousSearchTerm = $this->searchTerm;
    }

    private function dispatchAlert($type, $message, $text)
    {
        $this->dispatch('swal:modal', [
            'type'      => $type,
            'message'   => $message,
            'text'      => $text
        ]);

        $this->resetInputFields();
    }

    public function isEditingMode($mode)
    {
        $this->initSelect2();
        $this->isEditing = $mode;
    }

    private function resetInputFields()
    {
        $this->id_karyawan         = '';
        $this->total_tunjangan     = '0';
        $this->total_potongan      = '0';
        $this->total_gaji          = '0';
        $this->status              = 'final';
    }

    public function cancel()
    {
        $this->initSelect2();
        $this->isEditing       = false;
        // $this->resetInputFields();
    }

    public function cancelReview()
    {
        $this->initSelect2();
        // $this->resetInputFields();
    }

    public function initSelect2()
    {
        $this->dispatch('initSelect2');
    }

    public function updated()
    {
        $this->initSelect2();
    }

    private function dispatchSwalSlipGaji($idSlipGaji)
    {
        $this->dispatch('swal:slipgaji', [
            'idSlipGaji'  => Crypt::encrypt($idSlipGaji), // enkripsi ID,
            'message'     => 'Berhasil disimpan!',
            'text'        => 'Apakah kamu ingin mencetak slip gaji sekarang?',
        ]);
    }
}
