<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use App\Models\Publicaciones;
class Categorias extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'categorias';
    public $timestamps = false;

     // Relación con publicaciones
    public function publicaciones()
    {
        return $this->hasMany(Publicaciones::class, 'categorias_id');
    }
}
