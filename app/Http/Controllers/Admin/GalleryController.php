<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Repositories\InterfacesBag\Gallery;

class GalleryController extends Controller
{
    protected $gallery;

    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    public function index(Request $request)
    {
        $fillable = [
            'keywords',
            'title',
            'weight',
            'page',
            'per_page_num',
            'orderby',
            'order'
        ];
        $resp = $this->gallery->index($request->only($fillable));

        return Response::display($resp);
    }

    public function store(Request $request)
    {
        $fillable = [
            'title',
            'keywords',
            'file',
            'describes',
            'index_id',
            'tags',
            'weight'
        ];
        $resp = $this->gallery->create($request->only($fillable));

        return Response::display($resp);
    }

    public function show($id)
    {
        $resp = $this->gallery->show($id);

        return Response::display($resp);
    }

    public function update(Request $request, $id)
    {
        $fillable = [
            'title',
            'keywords',
            'file',
            'describes',
            'drop_image_ids',
            'index_id',
            'tags',
            'weight'
        ];
        $resp = $this->gallery->update($id, $request->only($fillable));

        return Response::display($resp);
    }


    public function destroy($id)
    {
        $resp = $this->gallery->delete($id);

        return Response::display($resp);
    }
}