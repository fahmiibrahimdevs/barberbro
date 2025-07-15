<?php

namespace App\Livewire\DataPendukung;

use App\Models\KategoriPengeluaran as ModelsKategoriPengeluaran;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

class KategoriPengeluaran extends Component
{
    use WithPagination;
    #[Title('Kategori Pengeluaran')]

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'nama_kategori'       => 'required',
        'deskripsi'           => '',
    ];

    public $lengthData = 25;
    public $searchTerm;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;

    public $nama_kategori, $deskripsi;

    public function mount()
    {
        $this->nama_kategori       = '';
        $this->deskripsi           = '';
    }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = ModelsKategoriPengeluaran::select('kategori_pengeluaran.id', 'kategori_pengeluaran.nama_kategori')
            ->where(function ($query) use ($search) {
                $query->where('nama_kategori', 'LIKE', $search);
                // $query->orWhere('deskripsi', 'LIKE', $search);
            })
            ->orderBy('id', 'ASC')
            ->paginate($this->lengthData);

        return view('livewire.data-pendukung.kategori-pengeluaran', compact('data'));
    }

    public function store()
    {
        $this->validate();

        ModelsKategoriPengeluaran::create([
            'nama_kategori'       => $this->nama_kategori,
            'deskripsi'           => $this->deskripsi,
        ]);

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function edit($id)
    {
        $this->isEditing        = true;
        $data = ModelsKategoriPengeluaran::where('id', $id)->first();
        $this->dataId           = $id;
        $this->nama_kategori    = $data->nama_kategori;
        // $this->deskripsi        = $data->deskripsi;
    }

    public function update()
    {
        $this->validate();

        if ($this->dataId) {
            ModelsKategoriPengeluaran::findOrFail($this->dataId)->update([
                'nama_kategori'       => $this->nama_kategori,
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
        ModelsKategoriPengeluaran::findOrFail($this->dataId)->delete();
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
        $this->nama_kategori       = '';
        $this->deskripsi           = '';
    }

    public function cancel()
    {
        $this->isEditing       = false;
        $this->resetInputFields();
    }
}
