tengo mi proyecto con laravel + mongo usando composer require mongodb/laravel-mongodb

me funciona todo bien

tengo la conexión 

DB_CONNECTION=mongodb
DB_HOST=laravel-mongodb
DB_PORT=27017
DB_DATABASE=database
DB_USERNAME=root
DB_PASSWORD=example

y agregué la connections a config/database.php 

,
        'mongodb' => [
            'driver' => 'mongodb',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 27017),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', ''),
            'password' => env('DB_PASSWORD', ''),
            'options' => [
                'database' => env('DB_AUTHENTICATION_DATABASE', 'admin'),
            ],
        ],


la migración de categorías me la creó bien

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('slug', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};


, y se hecho si ejecuto mi controlador

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Categorias;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class CategoriasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datos = Categorias::orderBy('_id', 'desc')->get();
        return response()->json($datos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), 
        [
            'nombre' => 'required|string|max:100',
        ],
        [
            'nombre.required'=>'El campo Nombre está vacío',
            'nombre.min'=>'El campo Nombre debe tener al menos 5 caracteres', 
        ]);

        // Si la validación falla, devolver los errores
        if ($validator->fails()) {
            return response()->json([
                'estado' => "error",
                "mensaje"=>"Ha ocurrido un error",
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
        }

        // Crear una nueva categoría con los datos validados
        $save = new Categorias();
        $save->nombre = $request->nombre;
        $save->slug = Str::slug($request->nombre, '-');
        $save->save();

        // Devolver una respuesta exitosa con los datos de la categoría creada
        return response()->json([
            'estado' => "ok",
            'mensaje' => 'Se crea registro exitosamente'
        ], Response::HTTP_CREATED); // 201
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $datos = Categorias::findOrFail($id);
        return response()->json($datos);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), 
        [
            'nombre' => 'required|string|max:100',
        ],
        [
            'nombre.required'=>'El campo Nombre está vacío',
            'nombre.min'=>'El campo Nombre debe tener al menos 5 caracteres', 
        ]);

        // Si la validación falla, devolver los errores
        if ($validator->fails()) {
            return response()->json([
                'estado' => "error",
                "mensaje"=>"Ha ocurrido un error",
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
        }
        $save = Categorias::findOrFail($id);
        $save->nombre = $request->nombre;
        $save->slug = Str::slug($request->nombre, '-');
        $save->save();

        // Devolver una respuesta exitosa con los datos de la categoría creada
        return response()->json([
            'estado' => "ok",
            'mensaje' => 'Se modifica registro exitosamente',
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Buscar la categoría por ID
            $categoria = Categorias::findOrFail($id);

            // Eliminar la categoría
            $categoria->delete();

            // Devolver una respuesta exitosa
            return response()->json([
                'estado' => 'ok',
                'mensaje' => 'Categoría eliminada exitosamente',
            ], Response::HTTP_OK); // 200

        } catch (\Exception $e) {
            // Manejar cualquier otro error
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Error al eliminar la categoría: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);  
        }
    }
}


me crea los registros con el objectID típico de mongo 

dicho eso, mi migración publicaciones debería crearla así sin foreaign key proque en mongo eso no aplica, es correcto?

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('slug', 100);
            $table->text('descripcion');
             $table->objectId('categorias_id');
            $table->datetime('fecha')->useCurrent();
            
            $table->index('nombre');
            $table->index('slug');
            $table->index('categorias_id');
            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
