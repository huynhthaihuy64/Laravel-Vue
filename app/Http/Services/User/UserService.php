<?php

namespace App\Http\Services\User;

use App\Http\Services\UploadService;
use App\Models\User;
use App\Models\UserAlbum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserService
{
    protected $uploadService;
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    public function update($data, $id)
    {
        $userOrigin = User::find($id);
        try {
            DB::beginTransaction();
            if (isset($data['avatar'])) {
                $avatar = $this->uploadService->uploadFile($data['avatar'], 'avatar');
                $avatar_path = $avatar['file_path'];
            } else {
                $avatar_path = $userOrigin->avatar;
            }

            if (isset($data['images']) && !empty($data['images'])) {
                $uploadFiles = [];
                foreach ($data['images'] as $file) {
                    $uploadFiles[] = $this->uploadService->uploadFile($file, 'album/' . $id);
                }
                if (!$uploadFiles || empty($uploadFiles)) {
                    throw new BadRequestException(__('messages.upload-file.failed'));
                }
                foreach ($uploadFiles as $file) {
                    $userOrigin->userAlbums()->save(new UserAlbum([
                        "name" => $file['file_name'],
                        "path" => $file['file_path']
                    ]));
                }
            }

            if (isset($data['gender']) && !empty($data['gender'])) {
                $gender = $data['gender'] === 'Male' ? 0 : 1;
            }
            $result = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'avatar' => $avatar_path,
                'department' => $data['department'],
                'birthday' => $data['birthday'],
                'address' => $data['address'],
                'gender' => $gender,
            ];
            $userOrigin->update($result);
            if (isset($data['removeFiles'])) {
                foreach ($data['removeFiles'] as $file) {
                    $this->removeFile($file);
                }
            }
            if (!$userOrigin) {
                return response()->json(__('messages.user.update.failed'));
            }
            DB::commit();
            return $userOrigin;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollback();
            return $e->getMessage();
        }
    }

    /**
     * removeFile
     *
     * @param  int $id
     */
    public function removeFile(int $id)
    {
        try {
            DB::beginTransaction();
            $postFile = UserAlbum::find($id);
            File::delete(public_path($postFile['path']));
            $postFile->delete();
            DB::commit();
            return $postFile;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
