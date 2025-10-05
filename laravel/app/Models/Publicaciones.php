<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use App\Models\Categorias;
class Publicaciones extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'publicaciones';
    public $timestamps=false;
 
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'categorias_id', // Asegúrate de que esté aquí
        'fecha'
    ];
    // Relación con categorías (si necesitas referenciar)
    public function categoria()
    {
        return $this->belongsTo(Categorias::class, 'categorias_id');
    }
}