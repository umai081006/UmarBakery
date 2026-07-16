<?php

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

if (!function_exists('cloudinary')) {
    function cloudinary()
    {
        return new class {
            protected $cloudinary;

            public function __construct()
            {
                $this->cloudinary = new Cloudinary(
                    Configuration::instance([
                        'cloud' => [
                            'cloud_name' => config('cloudinary.cloud_name'),
                            'api_key'    => config('cloudinary.api_key'),
                            'api_secret' => config('cloudinary.api_secret'),
                        ],
                        'url' => [
                            'secure' => true,
                        ],
                    ])
                );
            }

            public function upload($file, $options = [])
            {
                $result = $this->cloudinary->uploadApi()->upload($file, $options);
                
                return new class($result) {
                    protected $result;

                    public function __construct($result)
                    {
                        $this->result = $result;
                    }

                    public function getSecurePath()
                    {
                        return $this->result['secure_url'];
                    }

                    public function getPublicId()
                    {
                        return $this->result['public_id'];
                    }
                };
            }

            public function destroy($publicId, $options = [])
            {
                return $this->cloudinary->uploadApi()->destroy($publicId, $options);
            }
        };
    }
}
