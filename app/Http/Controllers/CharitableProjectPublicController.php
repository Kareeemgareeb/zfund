<?php
namespace App\Http\Controllers;
use App\Models\CharitableProject;
use Illuminate\Http\Request;
class CharitableProjectPublicController extends Controller {
 public function index(Request $request){
  $data=$request->validate(['q'=>'nullable|string|max:100','category'=>'nullable|string|max:60']);
  $query=CharitableProject::where('status','ACTIVE')->whereColumn('collected_amount','<','target_budget')->where('target_budget','>',0);
  if(!empty($data['q']))$query->where('title','like','%'.$data['q'].'%');
  if(!empty($data['category']))$query->where('category',$data['category']);
  $categories=CharitableProject::where('status','ACTIVE')->distinct()->pluck('category')->filter();
  $projects=$query->latest()->paginate(12)->withQueryString();
  return view('projects.index',compact('projects','categories'));
 }
}
