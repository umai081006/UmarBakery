<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload a file to Cloudinary, with a fallback to local storage.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string URL of the uploaded image
     */
    public function upload(UploadedFile $file, string $folder = 'umar-bakery'): string
    {
        $cloudName = config('cloudinary.cloud_name');
        
        // If Cloudinary is not configured or is default placeholder, save locally
        if (empty($cloudName) || $cloudName === 'your_cloud_name') {
            return $this->uploadLocal($file, $folder);
        }

        try {
            $uploaded = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);
            return $uploaded->getSecurePath();
        } catch (Exception $e) {
            Log::warning('Cloudinary upload failed, falling back to local storage: ' . $e->getMessage());
            return $this->uploadLocal($file, $folder);
        }
    }

    /**
     * Upload to local public folder.
     */
    protected function uploadLocal(UploadedFile $file, string $folder): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/' . $folder);
        
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        $file->move($destinationPath, $filename);
        
        return '/uploads/' . $folder . '/' . $filename;
    }

    /**
     * Delete an image from Cloudinary (optional/best effort).
     */
    public function delete(?string $url): void
    {
        if (empty($url) || str_starts_with($url, '/uploads/')) {
            // Delete local file if exists
            if ($url) {
                $filePath = public_path($url);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            return;
        }

        try {
            // Extract public ID from Cloudinary URL
            // e.g. https://res.cloudinary.com/cloud_name/image/upload/v1234567/folder/public_id.jpg
            $pathParts = explode('/', parse_url($url, PHP_URL_PATH));
            $publicIdWithExt = end($pathParts);
            $publicId = pathinfo($publicIdWithExt, PATHINFO_FILENAME);
            
            // If inside folder, get the folder path too
            $folderIndex = array_search('upload', $pathParts);
            if ($folderIndex !== false && count($pathParts) > $folderIndex + 2) {
                // Skip 'upload', skip version (usually starts with 'v')
                $subParts = array_slice($pathParts, $folderIndex + 2);
                array_pop($subParts); // remove filename
                if (!empty($subParts)) {
                    $publicId = implode('/', $subParts) . '/' . $publicId;
                }
            }

            cloudinary()->destroy($publicId);
        } catch (Exception $e) {
            Log::warning('Cloudinary delete failed: ' . $e->getMessage());
        }
    }
}
