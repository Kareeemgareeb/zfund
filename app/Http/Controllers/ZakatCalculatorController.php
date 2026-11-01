<?php
namespace App\Http\Controllers;
use App\Models\Donor;
use App\Models\ZakatCalculation;
use App\Models\ZakatMetalPrice;
use App\Services\ZakatCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class ZakatCalculatorController extends Controller {
 public function __construct(private ZakatCalculationService $service) {}
 public function calculate(Request $request) {
  $data=$request->validate([
   'calculation_type'=>'required|in:GOLD_SILVER,LIVESTOCK,CROPS_AGRICULTURE,CASH_WEALTH',
   'amount'=>'required_if:calculation_type,CASH_WEALTH|nullable|numeric|min:0|max:9999999999',
   'metal_type'=>'required_if:calculation_type,GOLD_SILVER|nullable|in:gold,silver',
   'grams'=>'required_if:calculation_type,GOLD_SILVER|nullable|numeric|min:0|max:9999999999',
   'hawl_completed'=>'required_if:calculation_type,CASH_WEALTH,GOLD_SILVER,LIVESTOCK|nullable|boolean',
   'animal_type'=>'required_if:calculation_type,LIVESTOCK|nullable|in:sheep,cow,camel',
   'count'=>'required_if:calculation_type,LIVESTOCK|nullable|integer|min:0|max:999999999',
   'grazing'=>'required_if:calculation_type,LIVESTOCK|nullable|boolean',
   'kilograms'=>'required_if:calculation_type,CROPS_AGRICULTURE|nullable|numeric|min:0|max:9999999999',
   'irrigation'=>'required_if:calculation_type,CROPS_AGRICULTURE|nullable|in:rain,cost,mixed',
  ]);
  $type=$data['calculation_type'];
  $prices=null;
  if(in_array($type,['CASH_WEALTH','GOLD_SILVER'],true)) {
   $prices=ZakatMetalPrice::orderByDesc('price_date')->first();
   if(!$prices) throw ValidationException::withMessages(['prices'=>'لم يحدد مدير النظام أسعار الذهب والفضة بعد.']);
  }
  $price=$type==='GOLD_SILVER' && ($data['metal_type']??'gold')==='silver' ? (float)($prices?->silver_price_per_gram) : (float)($prices?->gold_price_per_gram);
  $result=match($type) {
   'CASH_WEALTH'=>$this->service->calculateWealth((float)$data['amount'],$price,(bool)$data['hawl_completed']),
   'GOLD_SILVER'=>$this->service->calculatePreciousMetal($data['metal_type'],(float)$data['grams'],$price,(bool)$data['hawl_completed']),
   'LIVESTOCK'=>$this->service->calculateLivestock($data['animal_type'],(int)$data['count'],(bool)$data['hawl_completed'],(bool)$data['grazing']),
   'CROPS_AGRICULTURE'=>$this->service->calculateCrops((float)$data['kilograms'],$data['irrigation']),
  };
  if($prices) {
   $data['price_per_gram_used']=$price;
   $data['price_date']=$prices->price_date->format('Y-m-d');
   $result['price_date']=$data['price_date'];
  }
  $id=null;
  // Livestock dues are animals, not monetary values; do not store as LYD.
  if($type!=='LIVESTOCK') {
   $record=ZakatCalculation::create(['donor_id'=>Auth::check()?Donor::where('user_id',Auth::id())->value('id'):null,
    'calculation_type'=>$type,'input_parameters'=>$data,'computed_zakat_due'=>$result['zakat_due']]);
   $id=$record->id;
  }
  return response()->json(['success'=>true,'calculation_id'=>$id,'results'=>$result]);
 }
}
