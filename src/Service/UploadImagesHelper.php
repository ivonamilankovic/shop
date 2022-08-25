<?php

namespace App\Service;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadImagesHelper
{
    const PROFILE_PICTURES = '/profile_pictures';

    private string $uploadPaths;

    public function __construct(string $uploadPaths)
    {
        $this->uploadPaths = $uploadPaths;
    }

    public function uploadProfileImage(UploadedFile $uploadedFile): string
    {
        $destination = $this->uploadPaths.self::PROFILE_PICTURES;
        $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newName = Urlizer::urlize($originalName) . '-' .uniqid('', false). '.' . $uploadedFile->guessExtension();

        $uploadedFile->move(
            $destination,
            $newName
        );

        return $newName;
    }

}