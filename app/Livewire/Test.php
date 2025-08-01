<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Test extends Component
{
    public $id_cabang, $id_transaksi;
    public $cabang, $transaksi, $detailTransaksis;

    public function mount()
    {
        $this->id_cabang    = Auth::user()->id_cabang;
        $this->id_transaksi = 1;

        $this->cabang = DB::table('cabang_lokasi')->select('id', 'nama_cabang', 'alamat', 'no_telp', 'email')->where('id', $this->id_cabang)->first();

        $this->transaksi = DB::table('transaksi AS t')->select('t.tanggal', 't.no_transaksi', 'p.nama_pelanggan', 't.total_sub_total', 't.total_diskon', 't.total_akhir', 't.jumlah_dibayarkan', 't.kembalian')
            ->join('daftar_pelanggan AS p', 'p.id', '=', 't.id_pelanggan')
            ->where('t.id', $this->id_transaksi)
            ->get();

        $this->detailTransaksis = DB::table('detail_transaksi AS dt')->select('dt.nama_item', 'dt.deskripsi_item', 'dt.jumlah', 'dt.harga', 'dt.diskon', 'dt.sub_total', 'dt.total_harga')
            ->where('dt.id_transaksi', $this->id_transaksi)
            ->get();

        // dd($this->transaksi);
    }

    public function render()
    {
        return view('livewire.test')->layout('components.layouts.test');
    }
}
