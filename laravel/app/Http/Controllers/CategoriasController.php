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
            // Verificar si la categoría está siendo usada en publicaciones
            $publicacionesCount = \App\Models\Publicaciones::where('categorias_id', $id)->count();
            
            if ($publicacionesCount > 0) {
                return response()->json([
                    'estado' => 'error',
                    'mensaje' => 'No se puede eliminar la categoría porque está siendo usada en ' . $publicacionesCount . ' publicación(es)',
                    'publicaciones_count' => $publicacionesCount
                ], Response::HTTP_CONFLICT); // 409 Conflict
            }
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
