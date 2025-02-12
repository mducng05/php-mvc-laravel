<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Page;

class PageController extends Controller
{
    public function index(Request $request)
    {   
        $pages = Page::latest();

        if ($request->keyword != '') {
            $pages = Page::where('name' , 'like' ,'%'.$request->keyword.'%');
        }

        $pages = $pages->paginate(10);
        return view('admin.pages.list' ,[
            'pages' => $pages,
        ]);
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all() , [
            'name' => 'required',
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $page = new Page;
        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();


        $message = 'Page added successfully!';
        session()->flash('success',$message);

        return response()->json([
                'status' => true,
                'message' => $message,
            ]);

    }

    public function edit($id)
    {
        $pages = Page::find($id);

        if($pages == null)
        {
            $message = 'Page not found!';
            session()->flash('error' , $message);
            return redirect()->route('pages.index');
        }

        return view('admin.pages.edit' , [
            'pages' => $pages,
        ]); 
    }

    public function update(Request $request , $id)
    {
        $pages = Page::find($id);

        if($pages == null)
        {
            $message = 'Page not found !';
            session()->flash('error' , $message);
            return redirect()->route('pages.index');

            return response()->json([
                'status' => true,
                'message' => $message,

            ]);
        }
        $validator = Validator::make($request->all() , [
            'name' => 'required',
            'slug' => 'required',
        ]);

        if($validator->passes())
        {
            $pages->name = $request->name;
            $pages->slug = $request->slug;
            $pages->content = $request->content;
            $pages->save();

            $message = 'Update Page successfully!';
            session()->flash('success' , $message);
            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);

        }

    }

    public function destroy($id)
    {
        $pages = Page::find($id);

        if($pages == null)
        {
            $message = 'Page not found!';
            session()->flash('error' , $message);
            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        }

        $pages->delete();
        $message = 'Page delete successfully!';
        session()->flash('success' , $message);
        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }
}
