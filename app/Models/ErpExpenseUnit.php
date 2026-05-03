<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpExpenseUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'created_by',
        'status',
    ];

    public static function getUnit($name)
    {
        $unit = self::where('name', $name)->first();
        if (!$unit) {
            $unit = self::create([
                'name' => $name,
                'slug' => strtolower(str_replace(' ', '-', $name)),
                'created_by' => \Auth::user()->id,
                'status' => 1,
            ]);
        }
        return $unit;
    }
}
