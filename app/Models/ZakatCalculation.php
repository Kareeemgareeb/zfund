<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZakatCalculation extends Model
{
    protected $fillable = ['donor_id', 'calculation_type', 'input_parameters', 'computed_zakat_due'];

    protected function casts(): array {
        return ['input_parameters' => 'array'];
    }

    public function donor() { return $this->belongsTo(Donor::class); }
}
