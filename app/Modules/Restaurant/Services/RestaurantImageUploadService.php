<?php

declare(strict_types=1);

namespace App\Modules\Restaurant\Services;

use RuntimeException;

final class RestaurantImageUploadService
{
    private const MAX_BYTES = 5 * 1024 * 1024;

    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function store(array $file, string $category): string
    {
        if (!in_array($category, ['logos', 'covers'], true)) {
            throw new RuntimeException('Invalid restaurant image category.');
        }

        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException($this->uploadErrorMessage($error));
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        $size = (int) ($file['size'] ?? 0);

        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            throw new RuntimeException('Invalid uploaded image.');
        }

        if ($size <= 0 || $size > self::MAX_BYTES) {
            throw new RuntimeException('Image must be between 1 byte and 5 MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpName);

        if (!is_string($mime) || !isset(self::ALLOWED_MIMES[$mime])) {
            throw new RuntimeException('Only JPG, PNG and WebP images are allowed.');
        }

        if (@getimagesize($tmpName) === false) {
            throw new RuntimeException('The uploaded file is not a valid image.');
        }

        $root = dirname(__DIR__, 4);
        $relativeDirectory = 'uploads/restaurants/' . $category;
        $directory = $root . '/public/' . $relativeDirectory;

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Unable to create the restaurant image upload directory.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . self::ALLOWED_MIMES[$mime];
        $destination = $directory . '/' . $filename;

        if (!move_uploaded_file($tmpName, $destination)) {
            throw new RuntimeException('Unable to save the uploaded image.');
        }

        return $relativeDirectory . '/' . $filename;
    }

    public function delete(?string $relativePath): void
    {
        $relativePath = trim((string) $relativePath);
        if ($relativePath === '') {
            return;
        }

        $root = dirname(__DIR__, 4);
        $publicRoot = realpath($root . '/public');
        if ($publicRoot === false) {
            return;
        }

        $candidate = $root . '/public/' . ltrim($relativePath, '/');
        $realFile = realpath($candidate);

        if ($realFile === false || !is_file($realFile)) {
            return;
        }

        $publicRootWithSeparator = rtrim($publicRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!str_starts_with($realFile, $publicRootWithSeparator)) {
            return;
        }

        @unlink($realFile);
    }

    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The uploaded image is too large.',
            UPLOAD_ERR_PARTIAL => 'The image upload was interrupted. Please try again.',
            UPLOAD_ERR_NO_FILE => 'Please select an image.',
            UPLOAD_ERR_NO_TMP_DIR => 'The server is missing its temporary upload directory.',
            UPLOAD_ERR_CANT_WRITE => 'The server could not write the uploaded image.',
            UPLOAD_ERR_EXTENSION => 'The image upload was blocked by a server extension.',
            default => 'The image upload failed.',
        };
    }
}
