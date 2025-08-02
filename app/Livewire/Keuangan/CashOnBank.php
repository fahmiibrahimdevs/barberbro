<?php

namespace App\Livewire\Keuangan;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Keuangan;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use App\Services\GlobalDataService;
use Illuminate\Support\Facades\Auth;

class CashOnBank extends Component
{
    use WithPagination;
    #[Title('Cash On Bank')]

    protected $paginationTheme = 'bootstrap';
    protected $globalDataService;

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'id_cabang'           => '',
        'id_user'             => '',
        'tanggal'             => 'required',
        'id_kategori_keuangan' => 'required',
        'jumlah'              => 'required',
        'status'              => '',
    ];

    public $lengthData = 25;
    public $searchTerm;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;
    /*
     * @var \Illuminate\Support\Collection
     */
    public $cabangs, $keuangans;
    public $id_cabang, $id_user, $tanggal, $keterangan, $id_kategori_keuangan, $jumlah, $status, $kategori;

    public function mount(GlobalDataService $globalDataService)
    {
        $this->globalDataService = $globalDataService;
        $this->cabangs = $this->globalDataService->getCabangs();
        $this->id_cabang = $this->globalDataService->getCabangs()->first()->id ?? '';
        $this->keuangans            = [];
        $this->id_user              = Auth::user()->id;
        $this->tanggal              = date('Y-m-d');
        $this->keterangan           = '-';
        $this->jumlah               = '0';
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = DB::table('keuangan')->select('keuangan.*', 'cabang_lokasi.nama_cabang', 'kategori_keuangan.nama_kategori', 'users.name', DB::raw("SUM(CASE WHEN keuangan.status = 'Out' THEN -jumlah ELSE jumlah END) OVER(PARTITION BY keuangan.id_cabang ORDER BY id ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS balancing"))
            ->join('cabang_lokasi', 'cabang_lokasi.id', '=', 'keuangan.id_cabang')
            ->join('kategori_keuangan', 'kategori_keuangan.id', '=', 'keuangan.id_kategori_keuangan')
            ->join('users', 'users.id', '=', 'keuangan.id_user')
            ->where(function ($query) use ($search) {
                $query->where('keterangan', 'LIKE', $search);
            })
            ->whereBetween('keuangan.tanggal', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->orderBy('id', 'DESC')
            ->paginate($this->lengthData);

        return view('livewire.keuangan.cash-on-bank', compact('data'));
    }

    public function store()
    {
        $this->validate();

        if ($this->id_kategori_keuangan == "1") {
            $this->keterangan = $this->keterangan == "-" ? "Pemasukan Transfer tanggal " . $this->tanggal : $this->keterangan;
        }

        Keuangan::create([
            'id_cabang'            => $this->id_cabang,
            'id_user'              => $this->id_user,
            'tanggal'              => $this->tanggal,
            'keterangan'           => $this->keterangan,
            'id_kategori_keuangan' => $this->id_kategori_keuangan,
            'jumlah'               => $this->jumlah,
            'status'               => $this->kategori,
        ]);

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function edit(GlobalDataService $globalDataService, $id, $kategori)
    {
        $this->initSelect2();
        $this->isEditing        = true;
        $data = Keuangan::where('id', $id)->first();
        $this->dataId           = $id;
        $this->tanggal          = $data->tanggal;
        $this->keterangan       = $data->keterangan;
        $this->jumlah           = $data->jumlah;
        $this->kategori         = $kategori;
        if ($kategori == "In") {
            $this->keuangans = $globalDataService->getKategoriKeuangan()->where('kategori', 'Pemasukan')->get(); // Assuming you want to set a default value based on the category
            $this->id_kategori_keuangan = $this->keuangans->first()->id ?? ''; // Set default to first item if exists
        } else {
            $this->keuangans = $globalDataService->getKategoriKeuangan()->where('kategori', 'Pengeluaran')->get(); // Assuming you want to set a default value based on the category
            $this->id_kategori_keuangan = $this->keuangans->first()->id ?? ''; // Set default to first item if exists
        }
    }

    public function update()
    {
        $this->validate();

        if ($this->dataId) {
            if ($this->id_kategori_keuangan == "1") {
                $this->keterangan = $this->keterangan == "-" ? "Pemasukan Transfer tanggal " . $this->tanggal : $this->keterangan;
            }

            Keuangan::findOrFail($this->dataId)->update([
                'id_user'             => $this->id_user,
                'tanggal'             => $this->tanggal,
                'keterangan'          => $this->keterangan,
                'id_kategori_keuangan'         => $this->id_kategori_keuangan,
                'jumlah'              => $this->jumlah,
            ]);

            $this->dispatchAlert('success', 'Success!', 'Data updated successfully.');
            $this->dataId = null;
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
        Keuangan::findOrFail($this->dataId)->delete();
        $this->dispatchAlert('success', 'Success!', 'Data deleted successfully.');
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

    public function isEditingMode(GlobalDataService $globalDataService, $mode, $kategori)
    {
        $this->isEditing = $mode;
        $this->kategori = $kategori;
        if ($kategori == "In") {
            $this->keuangans = $globalDataService->getKategoriKeuangan()->where('kategori', 'Pemasukan')->get(); // Assuming you want to set a default value based on the category
            $this->id_kategori_keuangan = $this->keuangans->first()->id ?? ''; // Set default to first item if exists
        } else {
            $this->keuangans = $globalDataService->getKategoriKeuangan()->where('kategori', 'Pengeluaran')->get(); // Assuming you want to set a default value based on the category
            $this->id_kategori_keuangan = $this->keuangans->first()->id ?? ''; // Set default to first item if exists
            // dd($this->id_kategori_keuangan);
        }
        $this->initSelect2();
    }

    private function resetInputFields()
    {
        $this->keterangan          = '-';
        $this->id_kategori_keuangan = '';
        $this->jumlah              = '0';
    }

    public function cancel()
    {
        $this->isEditing       = false;
        $this->resetInputFields();
    }

    public function initSelect2()
    {
        $this->dispatch('initSelect2');
    }

    public function updated()
    {
        $this->dispatch('initSelect2');
    }
}
