<?php

namespace Modules\Product\App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Modules\Media\App\Models\File;
use Modules\Media\App\Models\ModelFile;
use Modules\Media\Trait\HasMedia;
use Modules\Shared\Exception\Exception;
use Modules\Shared\StatusCode\ErrorCode;

class ProductOptionValue extends Model
{
    use HasFactory, HasMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'product_option_id',
        'option_data',
        'option_name'
    ];

    public function saveFile($fileId, $zone, $modelId)
    {
        $file = File::find($fileId);

        if (!$file) {
            throw new Exception('File not found.', ErrorCode::NOT_FOUND);
        }

        DB::table('model_files')->insert([
            'file_id' => $fileId,
            'model_type' => 'Modules\Product\App\Models\ProductOptionValue',
            'model_id' => $modelId,
            'zone' => $zone,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_option_variants')
            ->withPivot('product_variant_id');
    }

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_option_variants');
    }

    public function baseImage()
    {
        return $this->files()->wherePivot('zone', 'baseImage')->first();
    }

    public function additionalImages()
    {
        return $this->files()->wherePivot('zone', 'additionalImage')->get();
    }
    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

}
