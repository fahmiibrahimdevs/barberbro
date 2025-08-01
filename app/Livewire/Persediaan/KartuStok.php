<?php

namespace App\Livewire\Persediaan;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Persediaan;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

class KartuStok extends Component
{
    use WithPagination;
    #[Title('Kartu Stok')]

    protected $paginationTheme = 'bootstrap';

    public $lengthData = 25;
    public $searchTerm;
    public $previousSearchTerm = '';

    public function mount() {}

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $data = DB::table('persediaan')->select('tanggal', 'nama_item', 'nama_cabang', 'persediaan.keterangan', 'persediaan.status', 'qty', DB::raw("SUM(CASE WHEN persediaan.status = 'Out' THEN -qty ELSE qty END) OVER(PARTITION BY persediaan.id_cabang, persediaan.id_produk ORDER BY tanggal ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS balancing"))
            ->join('produk', 'produk.id', 'persediaan.id_produk')
            ->join('cabang_lokasi', 'cabang_lokasi.id', 'persediaan.id_cabang')
            ->where(function ($query) use ($search) {
                $query->where('tanggal', 'LIKE', $search);
                $query->orWhere('produk.nama_item', 'LIKE', $search);
                $query->orWhere('persediaan.keterangan', 'LIKE', $search);
            })
            ->whereBetween('persediaan.tanggal', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->orderBy('tanggal', 'ASC')
            ->paginate($this->lengthData);

        return view('livewire.persediaan.kartu-stok', compact('data'));
    }

    private function searchResetPage()
    {
        if ($this->searchTerm !== $this->previousSearchTerm) {
            $this->resetPage();
        }

        $this->previousSearchTerm = $this->searchTerm;
    }
}
