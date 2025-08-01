<?php

namespace App\Livewire\Keuangan;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use App\Services\GlobalDataService;
use Illuminate\Support\Facades\Auth;
use App\Models\Kasbon as ModelsKasbon;

class Kasbon extends Component
{
    use WithPagination;
    #[Title('Kasbon')]

    protected $paginationTheme = 'bootstrap';
    protected $globalDataService;

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'id_karyawan'         => 'required',
        'jumlah'              => 'required',
        'keterangan'          => 'required',
        'tgl_pengajuan'       => 'required',
        'status'              => '',
        'tgl_disetujui'       => '',
        'id_disetujui'        => '',
        'metode_input'        => '',
    ];

    public $lengthData = 25;
    public $searchTerm;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;
    public $karyawans;
    public $id_karyawan, $jumlah, $keterangan, $tgl_pengajuan, $status, $tgl_disetujui, $id_disetujui, $metode_input;

    public function mount(GlobalDataService $globalDataService)
    {
        $this->globalDataService = $globalDataService;
        $this->karyawans = $this->globalDataService->getKaryawans();
        $this->id_disetujui        = Auth::user()->id;

        $this->resetInputFields();
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = DB::table('kasbon')->select('kasbon.id', 'u1.name as nama_karyawan', 'kasbon.jumlah', 'kasbon.keterangan', 'kasbon.tgl_pengajuan', 'kasbon.status', 'kasbon.metode_input', 'u2.name as disetujui_oleh', 'nama_cabang')
            ->leftJoin('daftar_karyawan', 'kasbon.id_karyawan', '=', 'daftar_karyawan.id')
            ->leftJoin('users as u1', 'daftar_karyawan.id_user', '=', 'u1.id')
            ->leftJoin('users as u2', 'kasbon.id_disetujui', '=', 'u2.id')
            ->leftJoin('cabang_lokasi', 'daftar_karyawan.id_cabang', '=', 'cabang_lokasi.id')
            ->whereBetween('kasbon.tgl_pengajuan', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->where(function ($query) use ($search) {
                $query->where('u1.name', 'LIKE', $search);
                $query->orWhere('u2.name', 'LIKE', $search);
            })
            ->orderBy('id', 'ASC')
            ->paginate($this->lengthData);

        return view('livewire.keuangan.kasbon', compact('data'));
    }

    public function store()
    {
        $this->validate();

        ModelsKasbon::create([
            'id_karyawan'         => $this->id_karyawan,
            'jumlah'              => $this->jumlah,
            'keterangan'          => $this->keterangan,
            'tgl_pengajuan'       => $this->tgl_pengajuan,
            'status'              => $this->status,
            'tgl_disetujui'       => $this->tgl_disetujui,
            'id_disetujui'        => $this->id_disetujui,
            'metode_input'        => $this->metode_input,
        ]);

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function edit($id)
    {
        $this->initSelect2();
        $this->isEditing        = true;
        $data = ModelsKasbon::where('id', $id)->first();
        $this->dataId           = $id;
        $this->id_karyawan      = $data->id_karyawan;
        $this->jumlah           = $data->jumlah;
        $this->keterangan       = $data->keterangan;
        $this->tgl_pengajuan    = $data->tgl_pengajuan;
        $this->status           = $data->status;
        $this->tgl_disetujui    = $data->tgl_disetujui;
        $this->metode_input     = $data->metode_input;
    }

    public function update()
    {
        $this->validate();

        if ($this->dataId) {
            ModelsKasbon::findOrFail($this->dataId)->update([
                'id_karyawan'         => $this->id_karyawan,
                'jumlah'              => $this->jumlah,
                'keterangan'          => $this->keterangan,
                'tgl_pengajuan'       => $this->tgl_pengajuan,
                'status'              => $this->status,
                'tgl_disetujui'       => $this->tgl_disetujui,
                'id_disetujui'        => $this->id_disetujui,
                'metode_input'        => $this->metode_input,
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
        ModelsKasbon::findOrFail($this->dataId)->delete();
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

    public function isEditingMode($mode)
    {
        $this->initSelect2();
        $this->isEditing = $mode;
    }

    private function resetInputFields()
    {
        $this->id_karyawan         = '';
        $this->jumlah              = '0';
        $this->keterangan          = '-';
        $this->tgl_pengajuan       = date('Y-m-d');
        $this->tgl_disetujui       = date('Y-m-d');
        $this->status              = 'disetujui';
        $this->metode_input        = 'manual';
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
        $this->initSelect2();
    }
}
