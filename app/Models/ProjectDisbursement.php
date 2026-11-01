<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDisbursement extends Model
{
    protected $fillable = ['charitable_project_id', 'assistance_request_id', 'amount', 'voucher_reference'];

    public function project() { return $this->belongsTo(CharitableProject::class, 'charitable_project_id'); }
    public function assistanceRequest() { return $this->belongsTo(AssistanceRequest::class); }
}
