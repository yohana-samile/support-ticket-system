<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{
    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        return asset('images/default-profile.png');
    }

    /**
     * Update the user's profile photo.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function updateProfilePhoto($file)
    {
        $path = $file->store('profile-photos', 'public');

        if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
            Storage::disk('public')->delete($this->profile_photo_path);
        }

        $this->update(['profile_photo_path' => $path]);
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePhoto()
    {
        if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
            Storage::disk('public')->delete($this->profile_photo_path);
        }

        $this->update(['profile_photo_path' => null]);
    }
}
