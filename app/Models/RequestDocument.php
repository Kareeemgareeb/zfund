<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestDocument extends Model
{
    protected $fillable = ['assistance_request_id', 'document_type', 'file_path'];
    public function assistanceRequest() { return $this->belongsTo(AssistanceRequest::class); }
}
