<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Categorias;
use App\Models\Publicaciones;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PublicacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datos = Publicaciones::with('categoria')
        ->orderBy('fecha', 'desc')
        ->get();
    
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
            'nombre' => 'required|string|max:100|unique:publicaciones,nombre',
            'descripcion' => 'required|string',
            'categorias_id' => 'required|string|exists:categorias,_id',
        ],
        [
            'nombre.required' => 'El campo Nombre está vacío',
            'nombre.max' => 'El campo Nombre no debe exceder 100 caracteres',
            'nombre.unique' => 'El nombre de la publicación ya existe', // Mensaje para la validación unique
            'descripcion.required' => 'El campo Descripción está vacío',
            'categorias_id.required' => 'La categoría es requerida',
            'categorias_id.exists' => 'La categoría seleccionada no existe',
        ]);

        // Si la validación falla, devolver los errores
        if ($validator->fails()) {
            return response()->json([
                'estado' => "error",
                "mensaje" => "Ha ocurrido un error",
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
        }

        // Verificar que la categoría existe (validación adicional)
        $categoria = Categorias::find($request->categorias_id);
        if (!$categoria) {
            return response()->json([
                'estado' => "error",
                'mensaje' => 'La categoría especificada no existe',
            ], Response::HTTP_BAD_REQUEST);
        }
        

        // Crear una nueva publicación con los datos validados
        $save = new Publicaciones();
        $save->nombre = $request->nombre;
        $save->slug = Str::slug($request->nombre, '-');
        $save->descripcion = $request->descripcion;
        $save->categorias_id = $request->categorias_id; // ¡ESTA LÍNEA FALTABA!
        $save->fecha = now(); // Agregar la fecha actual
        
        $save->save();

        // Devolver una respuesta exitosa
        return response()->json([
            'estado' => "ok",
            'mensaje' => 'Se crea registro exitosamente',
        ], Response::HTTP_CREATED); // 201
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
        $datos = Publicaciones::with('categoria')->findOrFail($id);
        
        return response()->json($datos);
        
    } catch (\Exception $e) {
        return response()->json([
            'estado' => 'error',
            'mensaje' => 'Publicación no encontrada'
        ], Response::HTTP_NOT_FOUND);
    }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Buscar la publicación
            $publicacion = Publicaciones::findOrFail($id);

            // Validar los datos de entrada
            $validator = Validator::make($request->all(), 
            [
                'nombre' => 'required|string|max:100',
                'descripcion' => 'required|string',
                'categorias_id' => 'required|string|exists:categorias,_id',
            ],
            [
                'nombre.required' => 'El campo Nombre está vacío',
                'nombre.max' => 'El campo Nombre no debe exceder 100 caracteres',
                'descripcion.required' => 'El campo Descripción está vacío',
                'categorias_id.required' => 'La categoría es requerida',
                'categorias_id.exists' => 'La categoría seleccionada no existe',
            ]);

            // Si la validación falla, devolver los errores
            if ($validator->fails()) {
                return response()->json([
                    'estado' => "error",
                    "mensaje" => "Ha ocurrido un error en la validación",
                    'errors' => $validator->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Verificar que la categoría existe
            $categoria = Categorias::find($request->categorias_id);
            if (!$categoria) {
                return response()->json([
                    'estado' => "error",
                    'mensaje' => 'La categoría especificada no existe',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Actualizar la publicación
            $publicacion->nombre = $request->nombre;
            $publicacion->slug = Str::slug($request->nombre, '-');
            $publicacion->descripcion = $request->descripcion;
            $publicacion->categorias_id = $request->categorias_id;
            // No actualizamos la fecha para mantener la fecha original de creación
            $publicacion->save();

            // Devolver una respuesta exitosa
            return response()->json([
                'estado' => "ok",
                'mensaje' => 'Se modifica registro exitosamente'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Publicación no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Buscar la publicación
            $publicacion = Publicaciones::findOrFail($id);

            // Eliminar la publicación
            $publicacion->delete();

            // Devolver una respuesta exitosa
            return response()->json([
                'estado' => 'ok',
                'mensaje' => 'Se elimina registro exitosamente',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Error al eliminar la publicación: ' . $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
