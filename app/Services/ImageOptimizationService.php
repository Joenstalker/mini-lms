<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ImageOptimizationService
{
    /**
     * Maximum width for optimized images
     */
    const MAX_WIDTH = 1200;

    /**
     * Maximum height for optimized images
     */
    const MAX_HEIGHT = 1600;

    /**
     * Quality for JPEG/WebP compression (0-100)
     */
    const JPEG_QUALITY = 85;
    const WEBP_QUALITY = 80;

    /**
     * Optimize and store an image
     */
    public static function optimizeImage($file, string $directory = 'covers'): ?string
    {
        if (!$file) {
            return null;
        }

        try {
            // Generate unique filename
            $filename = self::generateFilename($file);
            
            // Get the file extension
            $extension = $file->getClientOriginalExtension();
            
            // Create directory if not exists
            $path = storage_path("app/public/{$directory}");
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // For external URLs, return the URL directly
            if (filter_var($file, FILTER_VALIDATE_URL)) {
                return self::optimizeExternalImage($file, $directory, $filename);
            }

            // Handle uploaded file
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                return self::handleUploadedFile($file, $directory, $filename);
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Image optimization failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle uploaded file optimization
     */
    private static function handleUploadedFile($file, string $directory, string $filename): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Store original if it's already optimized
        $path = $file->storeAs($directory, $filename, 'public');
        
        // Convert to WebP if possible
        if (extension_loaded('gd')) {
            self::convertToWebP(storage_path("app/public/{$path}"), $extension);
        }

        return $path;
    }

    /**
     * Optimize external image URL
     */
    private static function optimizeExternalImage(string $url, string $directory, string $filename): string
    {
        // Try to download and optimize external images
        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
            ]);
            
            $response = $client->get($url);
            $content = $response->getBody()->getContents();
            
            // Save to storage
            $path = "{$directory}/{$filename}.webp";
            Storage::disk('public')->put($path, $content);
            
            return $path;
        } catch (\Exception $e) {
            // Return original URL if optimization fails
            return $url;
        }
    }

    /**
     * Convert image to WebP format
     */
    public static function convertToWebP(string $imagePath, string $originalExtension = 'jpg'): ?string
    {
        if (!file_exists($imagePath)) {
            return null;
        }

        $extension = strtolower($originalExtension);
        $webpPath = preg_replace('/\.' . $extension . '$/', '.webp', $imagePath);

        if ($extension === 'webp') {
            return $webpPath;
        }

        try {
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($imagePath);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($imagePath);
                    break;
                default:
                    return null;
            }

            if (!$image) {
                return null;
            }

            // Resize if needed
            $image = self::resizeImage($image);

            // Save as WebP
            imagewebp($image, $webpPath, self::WEBP_QUALITY);
            imagedestroy($image);

            // Optionally remove original
            // unlink($imagePath);

            return $webpPath;
        } catch (\Exception $e) {
            \Log::error('WebP conversion failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Resize image if it exceeds maximum dimensions
     */
    private static function resizeImage($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= self::MAX_WIDTH && $height <= self::MAX_HEIGHT) {
            return $image;
        }

        // Calculate new dimensions maintaining aspect ratio
        $ratio = min(self::MAX_WIDTH / $width, self::MAX_HEIGHT / $height);
        $newWidth = (int) ($width * $ratio);
        $newHeight = (int) ($height * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }

    /**
     * Generate unique filename
     */
    private static function generateFilename($file): string
    {
        $hash = md5(time() . uniqid());
        return $hash;
    }

    /**
     * Get image URL with fallback
     */
    public static function getImageUrl(?string $path): string
    {
        if (empty($path)) {
            return asset('images/book-placeholder.webp');
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    /**
     * Delete image file
     */
    public static function deleteImage(?string $path): bool
    {
        if (empty($path) || filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        try {
            $fullPath = storage_path('app/public/' . $path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            // Also try WebP version
            $webpPath = preg_replace('/\.(jpg|jpeg|png|gif)$/', '.webp', $fullPath);
            if (file_exists($webpPath)) {
                unlink($webpPath);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate responsive image srcset
     */
    public static function generateSrcSet(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        // For external URLs, return original
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $baseUrl = asset('storage/' . $path);
        
        // Generate srcset for different sizes
        $sizes = [400, 800, 1200];
        $srcset = [];
        
        foreach ($sizes as $size) {
            $srcset[] = "{$baseUrl}?w={$size} {$size}w";
        }
        
        return implode(', ', $srcset);
    }
}
