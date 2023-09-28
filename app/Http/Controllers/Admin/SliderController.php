<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SlideResource;
use App\Http\Services\Slider\SliderService;
use Illuminate\Http\Request;

class SliderController extends Controller
{

    protected $slider;

    public function __construct(SliderService $slider)
    {
        $this->slider = $slider;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'file' => 'required',
            'url' => 'required',
        ]);
        $paginate = $request->limit ?? 5;
        $data = $request->toArray();
        $slider = $this->slider->insert($data);

        return $slider;
    }

    public function index(Request $request)
    {
        $paginate = $request->limit ?? 5;
        $slider = SlideResource::collection($this->slider->get($request->toArray(),$paginate));
        return $slider;
    }

    public function show(int $id)
    {
        $slider = $this->slider->show($id);
        return $slider;
    }

    public function update(Request $request, int $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'file' => 'required',
            'url' => 'required',
        ]);

        $data = $request->toArray();
        $result = $this->slider->update($data, $id);

        return $result;
    }

    public function destroy(int $id)
    {
        $result = $this->slider->destroy($id);
        if ($result) {
            return response()->json([
                'error' => false,
                'message' => 'Delete Slider Success'
            ]);
        }

        return response()->json(['error' => true]);
    }
}
