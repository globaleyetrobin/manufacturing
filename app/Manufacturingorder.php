<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DB;

class Manufacturingorder extends Model
{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Combines Category and sub-category
     *
     * @param int $business_id
     * @return array
     */
  

    public static function forDropdown($business_id)
    {
		
		
        $materials = Manufacturingorder::where('business_id', $business_id)
                           
                     ->pluck('title', 'id');
				
        return $materials;
    }
}
