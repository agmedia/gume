<?php

namespace App\Http\Controllers\Back\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('search') && ! empty($request->search)) {
            $brands = Brand::where('title', 'like', '%' . $request->search . '%')->paginate(12)->appends(request()->query());
        } else {
            $brands = Brand::paginate(12)->appends(request()->query());
        }

        return view('back.catalog.brand.index', compact('brands'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('back.catalog.brand.edit');
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
        $brand = new Brand();

        $stored = $brand->validateRequest($request)->create();

        if ($stored) {
            $brand->resolveImage($stored);

            return redirect()->route('brands.edit', ['brand' => $stored])->with(['success' => 'Autor je uspješno snimljen!']);
        }

        return redirect()->back()->with(['error' => 'Oops..! Greška prilikom snimanja.']);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Publisher $publisher
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        return view('back.catalog.brand.edit', compact('brand'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Publisher                $publisher
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $updated = $brand->validateRequest($request)->edit();

        if ($updated) {
            $brand->resolveImage($updated);

            return redirect()->route('brands.edit', ['brand' => $updated])->with(['success' => 'Autor je uspješno snimljen!']);
        }

        return redirect()->back()->with(['error' => 'Oops..! Greška prilikom snimanja.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Brand $brand)
    {
        $destroyed = Brand::destroy($brand->id);

        if ($destroyed) {
            return redirect()->route('brands')->with(['success' => 'Autor je uspješno izbrisan!']);
        }

        return redirect()->back()->with(['error' => 'Oops..! Greška prilikom brisanja.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyApi(Request $request)
    {
        if ($request->has('id')) {
            $destroyed = Brand::destroy($request->input('id'));

            if ($destroyed) {
                return response()->json(['success' => 200]);
            }
        }

        return response()->json(['error' => 300]);
    }
}
