<?php

declare(strict_types=1);

namespace App\Modules\Members\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Authorization;
use App\Modules\Members\Services\MemberService;
use App\Modules\Families\Services\FamilyService;
use Throwable;
use RuntimeException;

final class MemberController extends Controller
{
    private MemberService $members;

    private FamilyService $families;

    private Authorization $authorization;

    /** Maximum accepted profile-photo size: 2 MB. */
    private const PHOTO_MAX_BYTES = 2097152;

    /** Allowed image MIME types and their safe extensions. */
    private const PHOTO_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct()
    {
        $this->members = new MemberService();

        $this->families = new FamilyService();

        $this->authorization = new Authorization();
    }

    /**
     * GET /members
     */
    public function index(): void
    {
        try {

            $this->authorization->authorize(
                'members.view'
            );

            $tenantId = (int) Auth::tenantId();

            $members = $this->members->all(
                $tenantId
            );

            $this->view(
                'Members.index',
                [
                    'title'   => 'Members',
                    'members' => $members,
                ]
            );

        } catch (Throwable $e) {

            flash(
                'error',
                $e->getMessage()
            );

            redirect('/dashboard');
        }
    }

    /**
     * GET /members/create
     */
    public function create(): void
    {
        try {

            $this->authorization->authorize(
                'members.create'
            );

            $this->view(
                'Members.create',
                [
                    'title' => 'Add Member',
                ]
            );

        } catch (Throwable $e) {

            flash(
                'error',
                $e->getMessage()
            );

            redirect('/members');
        }
    }

    /**
     * POST /members
     */
    public function store(): void
    {
        $uploadedPhoto = null;

        try {

            $this->authorization->authorize(
                'members.create'
            );

            $data = $_POST;

            $data['tenant_id'] = (int) Auth::tenantId();

            $data['created_by'] = Auth::id();

            $uploadedPhoto = $this->preparePhotoUpload(
                $data['tenant_id']
            );

            if ($uploadedPhoto !== null) {
                $data['photo'] = $uploadedPhoto;
            }

            $this->members->create(
                $data
            );

            flash(
                'success',
                'Member added successfully.'
            );

            redirect('/members');

        } catch (Throwable $e) {

            if ($uploadedPhoto !== null) {
                $this->deleteStoredPhoto(
                    $uploadedPhoto
                );
            }

            $this->view(
                'Members.create',
                [
                    'title' => 'Add Member',
                    'error' => $e->getMessage(),
                    'old'   => $_POST,
                ]
            );
        }
    }

    /**
     * GET /members/{id}
     */
    public function show(
        int $id
    ): void {
        try {

            $this->authorization->authorize(
                'members.view'
            );

            $tenantId = (int) Auth::tenantId();

            $member = $this->members->find(
                $tenantId,
                $id
            );

            if (!$member) {
                abort(404);
            }

            /*
            |--------------------------------------------------------------------------
            | Member Login
            |--------------------------------------------------------------------------
            */

            $memberLogin =
                $this->members->getMemberLogin(
                    $tenantId,
                    $id
                );

            $family = null;

            $familyMembers = [];

            if (!empty($member['family_id'])) {

                $family = $this->families->find(
                    $tenantId,
                    (int) $member['family_id']
                );

                if ($family !== null) {

                    $familyMembers =
                        $this->members->byFamily(
                            $tenantId,
                            (int) $member['family_id']
                        );
                }
            }

            $this->view(
                'Members.show',
                [
                    'title' =>
                        'Member Profile',

                    'member' =>
                        $member,

                    'memberLogin' =>
                        $memberLogin,

                    'family' =>
                        $family,

                    'familyMembers' =>
                        $familyMembers,
                ]
            );

        } catch (Throwable $e) {

            flash(
                'error',
                $e->getMessage()
            );

            redirect('/members');
        }
    }

    /**
     * GET /members/{id}/edit
     */
    public function edit(
        int $id
    ): void {
        try {

            $this->authorization->authorize(
                'members.edit'
            );

            $tenantId = (int) Auth::tenantId();

            $member = $this->members->find(
                $tenantId,
                $id
            );

            if (!$member) {
                abort(404);
            }

            $memberLogin =
                $this->members->getMemberLogin(
                    $tenantId,
                    $id
                );

            $this->view(
                'Members.edit',
                [
                    'title' =>
                        'Edit Member',

                    'member' =>
                        $member,

                    'memberLogin' =>
                        $memberLogin,
                ]
            );

        } catch (Throwable $e) {

            flash(
                'error',
                $e->getMessage()
            );

            redirect('/members');
        }
    }

    /**
     * POST /members/{id}
     */
    public function update(
        int $id
    ): void {
        $uploadedPhoto = null;

        $oldPhoto = null;

        try {

            $this->authorization->authorize(
                'members.edit'
            );

            $tenantId = (int) Auth::tenantId();

            $member = $this->members->find(
                $tenantId,
                $id
            );

            if (!$member) {
                abort(404);
            }

            $oldPhoto =
                $member['photo'] ?? null;

            $data = $_POST;

            $data['updated_by'] = Auth::id();

            /*
            |--------------------------------------------------------------------------
            | Preserve current photo when no new upload is supplied.
            |--------------------------------------------------------------------------
            */

            $data['photo'] = $oldPhoto;

            $uploadedPhoto =
                $this->preparePhotoUpload(
                    $tenantId
                );

            if ($uploadedPhoto !== null) {
                $data['photo'] = $uploadedPhoto;
            }

            $this->members->update(
                $tenantId,
                $id,
                $data
            );

            /*
            |--------------------------------------------------------------------------
            | Only remove old file after successful DB update.
            |--------------------------------------------------------------------------
            */

            if (
                $uploadedPhoto !== null
                && !empty($oldPhoto)
                && $oldPhoto !== $uploadedPhoto
            ) {
                $this->deleteStoredPhoto(
                    $oldPhoto
                );
            }

            flash(
                'success',
                'Member updated successfully.'
            );

            redirect('/members');

        } catch (Throwable $e) {

            if ($uploadedPhoto !== null) {
                $this->deleteStoredPhoto(
                    $uploadedPhoto
                );
            }

            $tenantId = (int) Auth::tenantId();

            $member = $this->members->find(
                $tenantId,
                $id
            );

            $this->view(
                'Members.edit',
                [
                    'title' =>
                        'Edit Member',

                    'member' =>
                        $member,

                    'error' =>
                        $e->getMessage(),

                    'old' =>
                        $_POST,
                ]
            );
        }
    }

    /**
     * POST /members/{id}/delete
     */
    public function delete(
        int $id
    ): void {
        try {

            $this->authorization->authorize(
                'members.delete'
            );

            $tenantId = (int) Auth::tenantId();

            $this->members->delete(
                $tenantId,
                $id
            );

            flash(
                'success',
                'Member archived successfully.'
            );

        } catch (Throwable $e) {

            flash(
                'error',
                $e->getMessage()
            );
        }

        redirect('/members');
    }

    /**
     * Validate and store an uploaded member photo.
     *
     * Returns the relative public path saved in the database,
     * or null when no file was supplied.
     */
    private function preparePhotoUpload(
        int $tenantId
    ): ?string {
        if (!isset($_FILES['photo'])) {
            return null;
        }

        $file = $_FILES['photo'];

        if (!is_array($file)) {
            throw new RuntimeException(
                'Invalid photo upload.'
            );
        }

        $error = (int) (
            $file['error']
            ?? UPLOAD_ERR_NO_FILE
        );

        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(
                $this->photoUploadErrorMessage(
                    $error
                )
            );
        }

        $tmpName = (string) (
            $file['tmp_name'] ?? ''
        );

        $size = (int) (
            $file['size'] ?? 0
        );

        if (
            $tmpName === ''
            || !is_uploaded_file($tmpName)
        ) {
            throw new RuntimeException(
                'Invalid uploaded photo.'
            );
        }

        if ($size <= 0) {
            throw new RuntimeException(
                'The uploaded photo is empty.'
            );
        }

        if ($size > self::PHOTO_MAX_BYTES) {
            throw new RuntimeException(
                'Profile photo must be 2 MB or smaller.'
            );
        }

        $finfo = new \finfo(
            FILEINFO_MIME_TYPE
        );

        $mime = $finfo->file(
            $tmpName
        );

        if (
            !isset(
                self::PHOTO_MIME_TYPES[$mime]
            )
        ) {
            throw new RuntimeException(
                'Invalid photo format. Please upload JPG, PNG, or WebP.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verify actual image.
        |--------------------------------------------------------------------------
        */

        $imageInfo = @getimagesize(
            $tmpName
        );

        if ($imageInfo === false) {
            throw new RuntimeException(
                'The uploaded photo is not a valid image.'
            );
        }

        $extension =
            self::PHOTO_MIME_TYPES[$mime];

        $directory =
            $this->photoDirectory();

        if (
            !is_dir($directory)
            && !mkdir(
                $directory,
                0755,
                true
            )
            && !is_dir($directory)
        ) {
            throw new RuntimeException(
                'Unable to create the member photo directory.'
            );
        }

        $filename = sprintf(
            'tenant_%d_%s.%s',
            $tenantId,
            bin2hex(
                random_bytes(16)
            ),
            $extension
        );

        $destination =
            $directory
            . DIRECTORY_SEPARATOR
            . $filename;

        if (
            !move_uploaded_file(
                $tmpName,
                $destination
            )
        ) {
            throw new RuntimeException(
                'Unable to save the uploaded photo.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Store web-relative path.
        |--------------------------------------------------------------------------
        */

        return 'uploads/members/' . $filename;
    }

    /**
     * Absolute filesystem directory for member photos.
     */
    private function photoDirectory(): string
    {
        return dirname(
            __DIR__,
            4
        )
            . DIRECTORY_SEPARATOR
            . 'public'
            . DIRECTORY_SEPARATOR
            . 'uploads'
            . DIRECTORY_SEPARATOR
            . 'members';
    }

    /**
     * Delete a previously stored member photo safely.
     */
    private function deleteStoredPhoto(
        ?string $relativePath
    ): void {
        if (empty($relativePath)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Only delete files from our own member-photo directory.
        |--------------------------------------------------------------------------
        */

        if (
            !str_starts_with(
                $relativePath,
                'uploads/members/'
            )
        ) {
            return;
        }

        $filename =
            basename($relativePath);

        $path =
            $this->photoDirectory()
            . DIRECTORY_SEPARATOR
            . $filename;

        if (is_file($path)) {
            @unlink($path);
        }
    }

    private function photoUploadErrorMessage(
        int $error
    ): string {
        return match ($error) {

            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE =>
                'Profile photo is too large.',

            UPLOAD_ERR_PARTIAL =>
                'Profile photo upload was incomplete.',

            UPLOAD_ERR_NO_TMP_DIR =>
                'Server upload directory is unavailable.',

            UPLOAD_ERR_CANT_WRITE =>
                'Server could not save the uploaded photo.',

            UPLOAD_ERR_EXTENSION =>
                'The photo upload was blocked by a server extension.',

            default =>
                'Unable to upload the profile photo.',
        };
    }
}