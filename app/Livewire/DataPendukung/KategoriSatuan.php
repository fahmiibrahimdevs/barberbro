<?php

namespace App\Livewire\DataPendukung;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\KategoriSatuan as ModelsKategoriSatuan;

class KategoriSatuan extends Component
{
    use WithPagination;
    #[Title('Kategori Satuan')]

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'nama_satuan'         => 'required',
        'deskripsi'           => '',
    ];

    public $lengthData = 25;
    public $searchTerm;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;

    public $nama_satuan, $deskripsi;

    public function mount()
    {
        $this->nama_satuan         = '';
        $this->deskripsi           = '';
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = ModelsKategoriSatuan::select('kategori_satuan.id', 'kategori_satuan.nama_satuan')
            ->where(function ($query) use ($search) {
                $query->where('nama_satuan', 'LIKE', $search);
                // $query->orWhere('deskripsi', 'LIKE', $search);
            })
            ->orderBy('id', 'ASC')
            ->paginate($this->lengthData);

        return view('livewire.data-pendukung.kategori-satuan', compact('data'));
    }

    public function store()
    {
        $this->validate();

        ModelsKategoriSatuan::create([
            'nama_satuan'         => $this->nama_satuan,
            'deskripsi'           => $this->deskripsi,
        ]);

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function edit($id)
    {
        $this->isEditing        = true;
        $data = ModelsKategoriSatuan::where('id', $id)->first();
        $this->dataId           = $id;
        $this->nama_satuan      = $data->nama_satuan;
        // $this->deskripsi        = $data->deskripsi;
    }

    public function update()
    {
        $this->validate();

        if ($this->dataId) {
            ModelsKategoriSatuan::findOrFail($this->dataId)->update([
                'nama_satuan'         => $this->nama_satuan,
                // 'deskripsi'           => $this->deskripsi,
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
        ModelsKategoriSatuan::findOrFail($this->dataId)->delete();
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
        $this->isEditing = $mode;
    }

    private function resetInputFields()
    {
        $this->nama_satuan         = '';
        $this->deskripsi           = '';
    }

    public function cancel()
    {
        $this->isEditing       = false;
        $this->resetInputFields();
    }
}
