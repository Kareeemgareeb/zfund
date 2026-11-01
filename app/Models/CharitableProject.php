<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharitableProject extends Model
{

    public function getCategoryLabelAttribute(): string
    {
        return ['ORPHAN_SPONSORSHIP'=>'كفالة الأيتام','MARRIAGE_AID'=>'مساعدة الزواج','MOSQUE_CONSTRUCTION'=>'بناء وصيانة المساجد','FOOD_BASKET_DISTRIBUTION'=>'توزيع السلال الغذائية','MEDICAL_SUPPORT'=>'الدعم العلاجي والعمليات','EDUCATION_SUPPORT'=>'دعم التعليم والطلاب','WATER_PROJECTS'=>'مشاريع المياه والآبار','HOUSING_REPAIR'=>'ترميم المساكن','WINTER_RELIEF'=>'كسوة الشتاء','EMERGENCY_RELIEF'=>'الإغاثة الطارئة','RAMADAN_MEALS'=>'إفطار الصائم','SCHOOL_SUPPLIES'=>'الحقائب والمستلزمات المدرسية','DISABILITY_SUPPORT'=>'دعم ذوي الإعاقة','SMALL_BUSINESSES'=>'تمويل المشاريع الصغيرة','COMMUNITY_SERVICES'=>'الخدمات المجتمعية','OTHER'=>'مشروع خيري آخر'][$this->category] ?? str_replace('_', ' ', (string) $this->category);
    }
    protected $fillable = ['title', 'description', 'category', 'target_budget', 'collected_amount', 'disbursed_amount', 'status'];

    protected $casts = [
        'target_budget' => 'decimal:2',
        'collected_amount' => 'decimal:2',
        'disbursed_amount' => 'decimal:2',
    ];

    public function transactions() { return $this->hasMany(Transaction::class, 'charitable_project_id'); }
    public function disbursements() { return $this->hasMany(ProjectDisbursement::class); }
}
