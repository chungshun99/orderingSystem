<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = Menu::all();
        return view('management.menu') -> with('menus', $menus);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();

        return view('management.createMenu') -> with('categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request -> validate([
            'menuName' => 'required|unique:menus|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
        ]);

        //if no image is uploaded, use noimage.png
        $imageName = "noimage.png";

        //if image is uploaded
        if ($request -> menuImage) {
            $request -> validate([
                'menuImage' => 'nullable|file|image|mimes:jpeg,png,jpg|max:5000'
            ]);

            $imageName = date('mdYHis').uniqid().'.'.$request -> menuImage -> extension();
            $request -> menuImage -> move(public_path('menu_images'), $imageName);
        }

        //bind data into $menu
        $menu = new Menu();
        $menu -> menuName = $request -> menuName;
        $menu -> price = $request -> price;
        $menu -> menuImage = $imageName;
        $menu -> menuDescription = $request -> menuDescription;
        $menu -> category_id = $request -> category_id;
        $menu -> save();

        //the dot(.) means string concat
        $request -> session()->flash('status', $request -> menuName. 'is saved successfully');

        return redirect('/management/menu');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = Menu::find($id);
        $categories = Category::all();
        return view('management.editMenu') -> with('menu', $menu) -> with('categories', $categories);

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
        //validate data
        $request -> validate([
            'menuName' => 'required|unique:menus|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
        ]);

        $menu = Menu::find($id);
        //validate if image is uploaded
        if ($request -> menuImage) {
            $request -> validate([
                'menuImage' => 'nullable|file|image|mimes:jpeg,png,jpg|max:5000'
            ]);

            if ($menu -> menuImage != "noimage.png") {
                $imageName = $menu -> menuImage;
                unlink(public_path('menu_images').'/'.$imageName);
            }

            $imageName = date('mdYHis').uniqid().'.'.$request -> menuImage -> extension();
            $request -> menuImage -> move(public_path('menu_images'), $imageName);
        } 
        else {
            $imageName = $menu -> menuImage;
        }

        $menu -> menuName = $request -> menuName;
        $menu -> price = $request -> price;
        $menu -> menuImage = $imageName;
        $menu -> menuDescription = $request -> menuDescription;
        $menu -> category_id = $request -> category_id;
        $menu -> save();

        $request -> session() -> flash('status', $request -> menuName. ' is updated successfully.');

        return redirect('/management/menu');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = Menu::find($id);

        if ($menu -> menuImage != 'noimage.png') {
            unlink(public_path('menu_images').'/'.$menu -> menuImage);
        }
        $menuName = $menu -> menuName;
        $menu -> delete();

        Session() -> flash('status', $menuName. ' is deleted successfully.');

        return redirect('/management/menu');
    }
}
