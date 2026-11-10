<?php

namespace App\Http\Controllers\Admin\Asdreams\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

trait HandlesAsdreamsImageUploads
{
    /**
     * Store an uploaded image in the designated ASDreams upload subdirectory.
     */
    protected function uploadAsdreamsFile(UploadedFile $file, string $subDirectory, string $prefix, ?string $previousPath = null): string
    {
        $cleanDir = trim($subDirectory, '/');
        $destination = public_path('uploads/asdreams/'.$cleanDir);

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true, true);
        }

        $extension = $file->getClientOriginalExtension() ?: 'png';
        $filename = $prefix.'_'.time().'_'.uniqid().'.'.strtolower($extension);
        $file->move($destination, $filename);

        if ($previousPath) {
            $this->deleteAsdreamsUploadedFile($previousPath);
        }

        return 'uploads/asdreams/'.$cleanDir.'/'.$filename;
    }

    /**
     * Delete a previously uploaded ASDreams file while preserving seeded static assets.
     */
    protected function deleteAsdreamsUploadedFile(?string $path): void
    {
        if (empty($path) || ! str_starts_with($path, 'uploads/asdreams/')) {
            return;
        }

        $fullPath = public_path($path);
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}

