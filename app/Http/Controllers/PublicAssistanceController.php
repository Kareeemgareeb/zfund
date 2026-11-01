<?php
namespace App\Http\Controllers;
use App\Models\AssistanceRequest;
use Illuminate\Http\Request;
class PublicAssistanceController extends Controller {
 public function index(Request $request){
  $data=$request->validate(['q'=>'nullable|string|max:100','category'=>'nullable|string|max:60']);
  $query=AssistanceRequest::where('status','APPROVED');
  if(!empty($data['q'])) {
   $term = trim($data['q']);
   $matchingCategories = array_keys(array_filter(['ORPHAN_SPONSORSHIP'=>'كفالة الأيتام','MARRIAGE_AID'=>'مساعدة الزواج','FOOD_BASKET'=>'السلة الغذائية','MEDICAL_AID'=>'مساعدة علاجية','EMERGENCY_DEBT'=>'سداد دين','GENERAL_RELIEF'=>'إعانة عامة'], fn($label)=>mb_stripos($label,$term)!==false));
   $query->where(function($q) use($term,$matchingCategories) {
    $q->where('request_code','like','%'.str_replace(['%','_'],['\\%','\\_'],$term).'%');
    if($matchingCategories) $q->orWhereIn('category',$matchingCategories);
   });
  }
  if(!empty($data['category']))$query->where('category',$data['category']);
  $requests=$query->withSum(['transactions as collected_amount'=>fn($q)=>$q->where('status','SUCCESSFUL')],'amount')->latest()->paginate(12)->withQueryString();
  return view('assistance.public_index',compact('requests'));
 }
}
