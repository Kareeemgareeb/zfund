<div x-data="sirteZakatCalculator()" x-init="loadPrices()" dir="rtl" class="space-y-5 text-right">
<div class="grid grid-cols-2 md:grid-cols-4 gap-2">
 <template x-for="tab in tabs" :key="tab.key"><button type="button" @click="type=tab.key;result=null;error=''" :class="type===tab.key?'bg-emerald-100 border-emerald-700 text-emerald-900':'bg-white border-slate-200'" class="border-2 rounded-xl px-2 py-3 text-sm font-bold" x-text="tab.label"></button></template>
</div>
<div class="rounded-lg bg-slate-50 border border-slate-200 px-3 py-2 text-sm text-slate-700" x-show="type==='CASH_WEALTH'||type==='GOLD_SILVER'">
 <span x-show="prices.available">آخر أسعار أدخلها مدير النظام: <b x-text="prices.date"></b> — الذهب <b x-text="prices.gold"></b> د.ل/جم، الفضة <b x-text="prices.silver"></b> د.ل/جم.</span>
 <span x-show="!prices.available">لم يُدخل مدير النظام أسعار الذهب والفضة بعد.</span>
</div>
<form @submit.prevent="calculate()" class="space-y-4">
 <template x-if="type==='CASH_WEALTH'"><div class="space-y-3">
  <label class="block text-sm font-bold">المبلغ النقدي المدخر (د.ل)<input type="number" min="0" step="any" required x-model="form.amount" class="block w-full mt-1 rounded-lg border-gray-300"></label>
  <label class="block text-sm font-bold">سعر جرام الذهب المعتمد (د.ل)<input type="number" readonly :value="prices.gold??''" class="block w-full mt-1 rounded-lg bg-gray-100 border-gray-300"></label>
  <label class="flex items-center gap-2 text-sm"><input type="checkbox" x-model="form.hawl_completed"> أؤكد مرور حول قمري على المال المستوفي للنصاب</label>
 </div></template>
 <template x-if="type==='GOLD_SILVER'"><div class="space-y-3">
  <label class="block text-sm font-bold">المعدن<select x-model="form.metal_type" class="block w-full mt-1 rounded-lg border-gray-300"><option value="gold">الذهب الخالص (85 جرامًا)</option><option value="silver">الفضة الخالصة (595 جرامًا)</option></select></label>
  <label class="block text-sm font-bold">الوزن الخالص بالجرام<input type="number" min="0" step="any" required x-model="form.grams" class="block w-full mt-1 rounded-lg border-gray-300"></label>
  <label class="block text-sm font-bold">سعر جرام المعدن المختار (د.ل)<input type="number" readonly :value="form.metal_type==='gold'?(prices.gold??''):(prices.silver??'')" class="block w-full mt-1 rounded-lg bg-gray-100 border-gray-300"></label>
  <label class="flex items-center gap-2 text-sm"><input type="checkbox" x-model="form.hawl_completed"> أؤكد مرور الحول القمري</label>
 </div></template>
 <template x-if="type==='LIVESTOCK'"><div class="space-y-3">
  <label class="block text-sm font-bold">صنف الأنعام<select x-model="form.animal_type" class="block w-full mt-1 rounded-lg border-gray-300"><option value="sheep">الغنم والماعز</option><option value="cow">البقر</option><option value="camel">الإبل</option></select></label>
  <label class="block text-sm font-bold">عدد الرؤوس<input type="number" min="0" step="1" required x-model="form.count" class="block w-full mt-1 rounded-lg border-gray-300"></label>
  <label class="flex items-center gap-2 text-sm"><input type="checkbox" x-model="form.hawl_completed"> أؤكد مرور الحول القمري</label>
  <label class="flex items-center gap-2 text-sm"><input type="checkbox" x-model="form.grazing"> الأنعام سائمة (ترعى غالب العام)</label>
 </div></template>
 <template x-if="type==='CROPS_AGRICULTURE'"><div class="space-y-3">
  <label class="block text-sm font-bold">وزن المحصول (كجم)<input type="number" min="0" step="any" required x-model="form.kilograms" class="block w-full mt-1 rounded-lg border-gray-300"></label>
  <label class="block text-sm font-bold">طريقة الري<select x-model="form.irrigation" class="block w-full mt-1 rounded-lg border-gray-300"><option value="rain">مطر أو أنهار بلا كلفة – 10%</option><option value="cost">ري بكلفة – 5%</option><option value="mixed">مختلط بالتساوي – 7.5%</option></select></label>
  <p class="text-xs text-gray-600">النصاب التقريبي 653 كجم، ويحتاج نوع المحصول إلى تحقق شرعي.</p>
 </div></template>
 <div x-show="error" x-text="error" class="p-3 bg-red-50 text-red-700 rounded-lg text-sm" style="display:none"></div>
 <button type="submit" :disabled="loading || ((type==='CASH_WEALTH'||type==='GOLD_SILVER')&&!prices.available)" class="w-full bg-emerald-700 text-white font-bold py-3 rounded-xl disabled:opacity-50"><span x-text="loading?'جارٍ الحساب...':'احسب الزكاة الآن'"></span></button>
</form>
<div x-show="result" style="display:none" class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl space-y-3">
 <h4 class="font-bold text-lg">نتيجة الحساب</h4>
 <p x-show="result&&!result.is_eligible" class="font-bold text-slate-700">لا يظهر مقدار واجب وفق البيانات والشروط المدخلة.</p>
 <p x-show="result&&result.is_eligible" class="font-bold text-emerald-900">الواجب: <span x-text="result?.zakat_due"></span> <span x-text="result?.currency"></span></p>
 <p x-show="result&&result.zakat_due_grams!==undefined">الواجب وزنًا: <span x-text="result?.zakat_due_grams"></span> جرام</p>
 <p class="text-sm" x-text="result?.breakdown"></p>
 <button x-show="result && result.is_eligible" type="button" disabled aria-disabled="true" title="خدمة الدفع الإلكتروني قيد التطوير، ولا يتم تحصيل أي مبلغ حاليًا" class="bg-blue-100 text-blue-800 border border-blue-200 px-6 py-3 rounded-xl cursor-not-allowed font-bold">ادفع الآن — قريبًا</button>
</div>
<p class="text-xs text-gray-500">الحاسبة تقديرية وتحتاج القواعد الشرعية إلى اعتماد الجهة المختصة. زر الدفع غير مفعل ولا يجري أي تحصيل.</p>
</div>
<script>
function sirteZakatCalculator(){return {
 type:'CASH_WEALTH',tabs:[{key:'CASH_WEALTH',label:'الأموال النقدية'},{key:'GOLD_SILVER',label:'الذهب والفضة'},{key:'LIVESTOCK',label:'الأنعام'},{key:'CROPS_AGRICULTURE',label:'الزروع والثمار'}],
 prices:{available:false,gold:null,silver:null,date:null},loading:false,error:'',result:null,
 form:{amount:null,metal_type:'gold',grams:null,hawl_completed:false,animal_type:'sheep',count:null,grazing:false,kilograms:null,irrigation:'rain'},
 async loadPrices(){try{const r=await axios.get('{{ route('zakat.prices') }}');this.prices=r.data;}catch(e){this.prices.available=false;}},
 async calculate(){this.loading=true;this.error='';this.result=null;
 try{const payload={calculation_type:this.type};
 if(this.type==='CASH_WEALTH')Object.assign(payload,{amount:this.form.amount,hawl_completed:this.form.hawl_completed});
 if(this.type==='GOLD_SILVER')Object.assign(payload,{metal_type:this.form.metal_type,grams:this.form.grams,hawl_completed:this.form.hawl_completed});
 if(this.type==='LIVESTOCK')Object.assign(payload,{animal_type:this.form.animal_type,count:this.form.count,hawl_completed:this.form.hawl_completed,grazing:this.form.grazing});
 if(this.type==='CROPS_AGRICULTURE')Object.assign(payload,{kilograms:this.form.kilograms,irrigation:this.form.irrigation});
 const r=await axios.post('/api/zakat/calculate',payload);this.result=r.data.results;
 }catch(e){const errors=e?.response?.data?.errors;this.error=errors?Object.values(errors).flat().join('، '):(e?.response?.data?.message||'تعذر إجراء الحساب.');}finally{this.loading=false;}
 }
};}
</script>
