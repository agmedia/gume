<?php

namespace App\Http\Controllers\Back\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Attributes\Attributes;
use App\Models\Back\Catalog\Attributes\AttributesTranslation;
use Illuminate\Http\Request;

class AttributesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('search') && ! empty($request->search)) {
            $attributes = Attributes::query()->where('title', 'LIKE', '%' . $request->search . '%')->paginate(12);
        } else {
            $attributes = Attributes::query()->paginate(12);
        }

        return view('back.catalog.attributes.index', compact('attributes'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('back.catalog.attributes.edit');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attribute = new Attributes();

        $stored = $attribute->validateRequest($request)->create();

        if ($stored) {
            return redirect()->route('attributes.edit', ['attributes' => $stored])->with(['success' => 'Attribute was succesfully saved!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! There was an error saving the attribute.']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Author $author
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Attributes $attributes)
    {
        //dd($attributes->toArray());
        return view('back.catalog.attributes.edit', compact('attributes'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Attributes               $attributes
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attributes $attributes)
    {
        $updated = $attributes->validateRequest($request)->edit();

        if ($updated) {
            return redirect()->route('attributes.edit', ['attributes' => $attributes->id])->with(['success' => 'Attributes was succesfully saved!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! There was an error saving the attribute.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Attributes $attributes)
    {
        $destroyed = Attributes::query()->where('id', $attributes->id)->delete();

        if ($destroyed) {
            return redirect()->route('attributes')->with(['success' => 'Attribute was succesfully deleted!']);
        }

        return redirect()->back()->with(['error' => 'Whoops..! There was an error deleting the attribute .']);
    }
}
