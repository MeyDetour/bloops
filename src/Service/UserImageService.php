<?php

namespace App\Service;

use App\Repository\ImageRepository;

class UserImageService
{
    private ImageRepository $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function getUserImage(int $userId)
    {
        return $this->imageRepository->findImageByUserId($userId);
    }
}
