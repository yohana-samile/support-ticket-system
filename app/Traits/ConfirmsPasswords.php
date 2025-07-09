<?php

namespace App\Traits;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use function Livewire\store;

trait ConfirmsPasswords
{
    public $confirmingPassword = false;
    public $confirmableId = null;
    public $confirmablePassword = '';
    public  $success = false;

    public function startConfirmingPassword(string $confirmableId)
    {
        $this->resetErrorBag();
        if (!user()) {
            \Log::debug('User not logged in or session expired.');
            return;
        }

        if ($this->passwordIsConfirmed()) {
            \Log::debug("Password already confirmed for confirmableId: {$confirmableId}");

            return dispatch('password-confirmed', ['id' => $confirmableId]);
        }

        $this->confirmingPassword = true;
        $this->confirmableId = $confirmableId;
        $this->confirmablePassword = '';
        $this->dispatch('confirming-password');
        \Log::debug("Password confirmation started for confirmableId: {$confirmableId}");
    }


    /**
     * Stop confirming the user's password.
     *
     * @return void
     */
    public function stopConfirmingPassword()
    {
        $this->confirmingPassword = false;
        $this->confirmableId = null;
        $this->confirmablePassword = '';
    }

    /**
     * Confirm the user's password.
     *
     * @return void
     */
    public function confirmPassword()
    {
        $user = user();

        $this->validate([
            'confirmablePassword' => 'required|string',
        ]);

        if (! Hash::check($this->confirmablePassword, $user->password)) {
            throw ValidationException::withMessages([
                'confirmable_password' => [__('This password does not match our records.')],
            ]);
        }
        session(['auth.password_confirmed_at' => time()]);
        dispatch('password-confirmed', ['id' => $this->confirmableId]);
        $this->stopConfirmingPassword();
    }

    /**
     * Ensure that the user's password has been recently confirmed.
     *
     * @param  int|null  $maximumSecondsSinceConfirmation
     * @return void
     */
    protected function ensurePasswordIsConfirmed($maximumSecondsSinceConfirmation = null)
    {
        $maximumSecondsSinceConfirmation = $maximumSecondsSinceConfirmation ?: config('auth.password_timeout', 900);

        $this->passwordIsConfirmed($maximumSecondsSinceConfirmation) ? null : abort(403);
    }

    /**
     * Determine if the user's password has been recently confirmed.
     *
     * @param  int|null  $maximumSecondsSinceConfirmation
     * @return bool
     */
    protected function passwordIsConfirmed($maximumSecondsSinceConfirmation = null)
    {
        $maximumSecondsSinceConfirmation = $maximumSecondsSinceConfirmation ?: config('auth.password_timeout', 900);

        return (time() - session('auth.password_confirmed_at', 0)) < $maximumSecondsSinceConfirmation;
    }

    public function resetErrorBag($field = null)
    {
        $fields = (array) $field;

        if (empty($fields)) {
            $errorBag = new MessageBag;

            $this->setErrorBag($errorBag);

            return $errorBag;
        }

        $this->setErrorBag(
            $this->errorBagExcept($fields)
        );
    }

    public function setErrorBag($bag)
    {
        return store($this)->set('errorBag', $bag instanceof MessageBag
            ? $bag
            : new MessageBag($bag)
        );
    }

    public function errorBagExcept($field)
    {
        $fields = (array) $field;

        return new MessageBag(
            collect($this->getErrorBag())
                ->reject(function ($messages, $messageKey) use ($fields) {
                    return collect($fields)->some(function ($field) use ($messageKey) {
                        return str($messageKey)->is($field);
                    });
                })
                ->toArray()
        );
    }

    public function getErrorBag()
    {
        if (! store($this)->has('errorBag')) {
            $previouslySharedErrors = app('view')->getShared()['errors'] ?? new ViewErrorBag;
            $this->setErrorBag($previouslySharedErrors->getMessages());
        }

        return store($this)->get('errorBag');
    }
}

