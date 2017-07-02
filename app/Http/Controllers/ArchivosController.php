<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\archivo;
use Storage;


class ArchivosController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


   	public function index()
   	{
        return view('archivo');

   	}
    public function create()
    {
        //
    }

    public function mostrar()
    {

      $archivos = archivo::all();
      return view('archivo')->with(['archivo'=> $archivos]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) //Almacener los datos
    {

      $this->validate($request, [ //Si hay un error, devueve la vista
        'descripcion' => 'required',
        'direccion' => 'required'
      ]);

      $archivos = new archivo();
      $archivos->descripcion = $request->descripcion;

      $file = $request->file('direccion');  //Se guarda el contenido del request de tipo file en una variable

      $file_route = time().'_'.$file->getClientOriginalName(); //Se guarda la ruta, se utiliza el time para que ningun archivo se llame igual

      Storage::disk('archivosGuardados')->put($file_route, file_get_contents($file->getRealPath() )); //Usar el estorage para ingresarle la ruta y el contenido

      $archivos->direccion = $file_route; // Al modelo de noticias asignar la ruta creada

      if( $archivos->save())
      {
        return back()->with('msj','Datos guardados correctamente');
      }
      else
      {
        return back()->with('errormsj','Error, los datos no se guardaron');
      }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        //dd($id); Es de prueba
        $archivos = archivo::find($id);
        return view('home')->with(['edit'=>true,'archivo'=> $archivos]);  //edit es una variable que se evalúa en home para solo mostrar el formulario.
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        //dd($request->titulo);
        $this->validate($request, [ //Si hay un error, devueve la vista
        'descripcion' => 'required',
        'direccion' => 'required'
      ]);

        $archivos = archivo::find($id);
        $archivos = new archivo();
        $archivos->descripcion = $request->descripcion;

        $file = $request->file('direccion');  //Se guarda el contenido del request de tipo file en una variable
        $file_route = time().'_'.$file->getClientOriginalName(); //Se guarda la ruta, se utiliza el time para que ningun archivo se llame igual

        Storage::disk('archivosGuardados')->put($file_route, file_get_contents($file->getRealPath() )); //Usar el estorage para ingresarle la ruta y el contenido
        Storage::disk('archivosGuardados')->delete($request->file);

        $archivos->direccion = $file_route; // Al modelo de noticias asignar la ruta creada

        if($archivos->save())
        {
          return redirect('home');
        }
        else
        {
          return back()->with('errormsj','Error, los datos no se guardaron');
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
      archivo::destroy($id);
      return back();
    }

    public function descargar($file){
      $archivo = public_path().'/archivosGuardados/'.$file;
      return response()->download($archivo);
    }

}