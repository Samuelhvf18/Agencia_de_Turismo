<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoriaTour extends Model
{
    use HasFactory;

    protected $table = 'categorias_tour';
    public $timestamps = false; // La tabla no tiene created_at/updated_at por defecto

    protected $fillable = [
        'nom',
        'rta_amg',
        'des',
        'ico',
    ];

    public static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (empty($model->rta_amg)) {
                $model->rta_amg = Str::slug($model->nom);
            }
        });
    }

    public function tours()
    {
        return $this->hasMany(Tour::class, 'cat_id');
    }
}