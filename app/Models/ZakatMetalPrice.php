<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ZakatMetalPrice extends Model {
    protected $fillable = ['gold_price_per_gram','silver_price_per_gram','price_date','updated_by'];
    protected function casts(): array { return ['price_date'=>'date:Y-m-d','gold_price_per_gram'=>'decimal:4','silver_price_per_gram'=>'decimal:4']; }
}
