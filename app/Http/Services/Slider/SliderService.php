<?php

namespace App\Http\Services\Slider;

use App\Http\Services\UploadService;
use App\Models\Slider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Traits\Sortable;
use Illuminate\Support\Facades\DB;

class SliderService
{
    protected $uploadService;

    use Sortable;
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    public function insert($data)
    {
        $file = $this->uploadService->uploadFile($data['file'], 'slider');
        $data['file'] = $file['file_path'];
        try {
            DB::beginTransaction();
            $slider = Slider::create([
                'name' => $data['name'],
                'file' => $data['file'],
                'url' => $data['url'],
                'sort_by' => 1,
                'active' => $data['active'],
            ]);
            DB::commit();
            return $slider;
        } catch (\Exception $err) {
            Log::info($err->getMessage());
            DB::rollback();
            return $err->getMessage();
        }
    }

    public function get($data, $paginate)
    {
        return Slider::sort($data)->paginate($paginate);
    }

    public function update($data, $id)
    {
        $file = $this->uploadService->uploadFile($data['file'], 'slider');
        $data['file'] = $file['file_path'];
        try {
            DB::beginTransaction();
            $slider = Slider::find($id);
            $slider->update([
                'name' => $data['name'],
                'file' => $data['file'],
                'url' => $data['url'],
                'sort_by' => 1,
                'active' => $data['active'],
            ]);
            DB::commit();
            return $slider;
        } catch (\Exception $err) {
            Log::info($err->getMessage());
            DB::rollback();
            return $err->getMessage();
        }
    }

    public function destroy(int $id)
    {
        $slider = Slider::find($id);
        if ($slider) {
            try {
                DB::beginTransaction();
                $path = str_replace('storage', 'public', $slider->file);
                Storage::delete($path);
                $slider->delete();
                DB::commit();
                return true;
            } catch (\Exception $err) {
                DB::rollback();
                return $err->getMessage();
            }
        }
        return false;
    }

    public function show($id)
    {
        return Slider::find($id);
    }
}
