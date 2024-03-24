<?php

namespace App\Http\Controllers\Back;

use App\Http\Requests\CategorieMedicamentRequest;
use App\Models\Categorie_medicament;
use App\Repositories\Back\CategorieMedicamentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategorieMedicamentController extends Controller
{
     /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\CategorieMedicamentRepository $repository
     *
     */
    public function __construct(CategorieMedicamentRepository $repository)
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
        $this->repository = $repository;
    }


     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('back.categoriemedicament.index',[
            'datas' => Categorie_medicament::orderBy('id','desc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('back.categoriemedicament.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategorieMedicamentRequest $request)
    {
        // $request->validate([
        //     'serial' => 'required|numeric|max:150'
        // ]);
        $this->repository->store($request);
        return redirect()->route('back.categorimedicament.index')->withSuccess(__('New Category Added Successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Categorie_medicament $category,$id)
    {
        // dd($id);
        $category = Categorie_medicament::findOrFail($id);

        return view('back.categoriemedicament.edit',compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategorieMedicamentRequest $request, Categorie_medicament $category)
    {
        // $request->validate([
        //     'serial' => 'required|numeric|max:150'
        // ]);
        
        $this->repository->update($category, $request);
        return redirect()->route('back.categorimedicament.index')->withSuccess(__('Category Updated Successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Categorie_medicament $category)
    {
       $mgs = $this->repository->delete($category);
       if($mgs['status'] == 1){
        return redirect()->route('back.categoriemedicament.index')->withSuccess($mgs['message']);
       }else{
        return redirect()->route('back.categoriemedicament.index')->withError($mgs['message']);
       }
       
    }

}
