<?php

namespace App\Traits;

use App\Support\Image\Filters\Fit;
use Exception;
use Holly\Support\Helper;
use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait ImageStorage
{
    use AssetHelper;

    /**
     * Store image file for the given attribute.
     *
     * @param  mixed  $file
     * @param  string  $attribute
     * @return string|null  The stored path
     */
    protected function storeImageFile($file, $attribute)
    {
        if ($file instanceof UploadedFile && ! $file->isValid()) {
            return;
        }

        try {
            $image = app('image')
                ->make($file)
                ->filter($this->getImageFilter($attribute))
                ->encode(
                    $this->getImageFormat($attribute),
                    $this->getImageQuality($attribute)
                );
        } catch (Exception $e) {
            return;
        }

        $path = trim($this->getImageDirectory($attribute), '/').'/'.
            md5((string) $image).Helper::fileExtensionForMimeType($image->mime());

        if ($this->getFilesystem($attribute)->put($path, (string) $image)) {
            return $path;
        }
    }

    /**
     * Get image filter for the given attribute.
     *
     * @see http://image.intervention.io/api/filter
     *
     * @param  string  $attribute
     */
    protected function getImageFilter($attribute)
    {
        return (new Fit)->width($this->getImageSize($attribute));
    }

    /**
     * Get the disk name of Filesystem for the given attribute.
     *
     * @param  string|null  $attribute
     * @return string
     */
    protected function getFilesystemDisk($attribute = null)
    {
        return 'public';
    }

    /**
     * Get image format for the given attribute.
     *
     * @see http://image.intervention.io/api/encode
     *
     * @param  string  $attribute
     * @return string|null
     */
    protected function getImageFormat($attribute)
    {
    }

    /**
     * Get image quality for the given attribute.
     *
     * @see http://image.intervention.io/api/encode
     *
     * @param  string  $attribute
     * @return int
     */
    protected function getImageQuality($attribute)
    {
        return 90;
    }

    /**
     * Get image size for the given attribute.
     *
     * @param  string  $attribute
     * @return int
     */
    protected function getImageSize($attribute)
    {
        if (defined($constant = 'static::'.strtoupper($attribute).'_SIZE')) {
            return constant($constant);
        }

        return 1024;
    }

    /**
     * Get image directory for the given attribute.
     *
     * @param  string  $attribute
     * @return string
     */
    protected function getImageDirectory($attribute)
    {
        return 'images/'.date('Y/m');
    }
}
