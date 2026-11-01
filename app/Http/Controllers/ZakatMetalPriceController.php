<?php
namespace App\Http\Controllers;
use App\Models\ZakatMetalPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ZakatMetalPriceController extends Controller {
 public function latest() {
  $price=ZakatMetalPrice::orderByDesc('price_date')->first();
  return response()->json(['available'=>(bool)$price,'gold'=>$price? (float)$price->gold_price_per_gram:null,
   'silver'=>$price?(float)$price->silver_price_per_gram:null,'date'=>$price?->price_date?->format('Y-m-d')]);
 }
 public function edit() {
  abort_unless(Auth::user()?->hasRole('admin'),403);
  return view('admin.zakat-prices', ['price'=>ZakatMetalPrice::orderByDesc('price_date')->first()]);
 }
 public function update(Request $request) {
  abort_unless(Auth::user()?->hasRole('admin'),403);
  $data=$request->validate(['price_date'=>'required|date|before_or_equal:today',
   'gold_price_per_gram'=>'required|numeric|gt:0|max:999999999',
   'silver_price_per_gram'=>'required|numeric|gt:0|max:999999999']);
  ZakatMetalPrice::updateOrCreate(['price_date'=>$data['price_date']],$data+['updated_by'=>Auth::id()]);
  return redirect()->route('admin.zakat-prices.edit')->with('status','تم حفظ أسعار الذهب والفضة بنجاح');
 }
}
