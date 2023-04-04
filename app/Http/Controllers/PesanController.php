<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesanController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($id)
    {
        $product = Product::where('id', $id)->first();
   
        return view('pesan.index', compact('product'));
    }
    public function pesan(Request $request, $id)
    {	
    	$product = Product::where('id', $id)->first();
    	$tanggal = Carbon::now();

		$pesanan = new Pesanan;
	    	$pesanan->user_id = Auth::user()->id;
	    	$pesanan->tanggal = $tanggal;
	    	$pesanan->status = 0;
	    	$pesanan->jumlah_harga = 0;
	    	$pesanan->save();
		//simpan ke database pesanan detail
    	$pesanan_baru = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();

		//cek pesanan detail
    	$cek_pesanan_detail = DetailPesanan::where('product_id', $product->id)->where('pesanan_id', $pesanan_baru->id)->first();
    	if(empty($cek_pesanan_detail))
    	{
    		$pesanan_detail = new DetailPesanan;
	    	$pesanan_detail->product_id = $product->id;
	    	$pesanan_detail->pesanan_id = $pesanan_baru->id;
	    	$pesanan_detail->jumlah = $request->jumlah_pesan;
	    	$pesanan_detail->jumlah_harga = $product->harga*$request->jumlah_pesan;
	    	$pesanan_detail->save();
    	}else
		{
    		$pesanan_detail = DetailPesanan::where('product_id', $product->id)->where('pesanan_id', $pesanan_baru->id)->first();

    		$pesanan_detail->jumlah = $pesanan_detail->jumlah+$request->jumlah_pesan;

    		//harga sekarang
    		$harga_pesanan_detail_baru = $product->harga*$request->jumlah_pesan;
	    	$pesanan_detail->jumlah_harga = $pesanan_detail->jumlah_harga+$harga_pesanan_detail_baru;
	    	$pesanan_detail->update();
    	}

    	//jumlah total
    	$pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
    	$pesanan->jumlah_harga = $pesanan->jumlah_harga+$product->jumlah_harga*$request->jumlah_pesan;
    	$pesanan->update();
		return redirect('check-out');
	}
		public function check_out()
		{
			$pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
		$pesanan_details = [];
			if(!empty($pesanan))
			{
				$pesanan_details = DetailPesanan::where('pesanan_id', $pesanan->id)->get();

			}
			
			return view('pesan.check_out', compact('pesanan', 'pesanan_details'));
		}

		public function delete($id)
		{
			$pesanan_detail = DetailPesanan::where('id', $id)->first();

			$pesanan = Pesanan::where('id', $pesanan_detail->pesanan_id)->first();
			$pesanan->jumlah_harga = $pesanan->jumlah_harga-$pesanan_detail->jumlah_harga;
			$pesanan->update();


			$pesanan_detail->delete();
			$pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
			$pesanan_id = $pesanan->id;
			$pesanan->status = 1;
			$pesanan->update();

			$pesanan_details = DetailPesanan::where('pesanan_id', $pesanan_id)->get();
			foreach ($pesanan_details as $pesanan_detail) 
			{
				$product = Product::where('id', $pesanan_detail->product_id)->first();
				$product->stok = $product->stok-$pesanan_detail->jumlah;
				$product->update();
			}
			return view('home');
		}
	}