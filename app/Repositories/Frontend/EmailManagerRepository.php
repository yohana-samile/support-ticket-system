<?php

namespace App\Repositories\Frontend;

use App\Models\CpanelEmail;
use App\Models\Domain;
use App\Models\DomainSwitcher;
use App\Models\EmailManager;
use App\Repositories\BaseRepository;
use App\Services\Frontend\WHMService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class EmailManagerRepository.
 */
class EmailManagerRepository extends BaseRepository
{
    const MODEL = EmailManager::class;


    public function createNewEmailAccount(array $input) {
        return DB::transaction(function() use($input) {
            $service = new WHMService();
            $cpanelResponse = $service->createEmailAccount($input);

            if ($cpanelResponse === false) {
                return false;
            }

            $email = EmailManager::create([
                'email' => $input['email'],
                'email_password' => $input['password'],
                'send_welcome_email' => $input['send_welcome_email'] ?? 1,
                'domain' => $input['domain'],
                'txtdiskquota' => $input['txtdiskquota'] ?? 1024,
                'diskquota' => $input['txtdiskquota'] ?? 1024,
                'sent_to_cpanel' => false,
                'password_hash' => Hash::make($input['password']),
                'skip_update_db' => 0,
                'admin_id' =>  user_id(),
            ]);
            return $email;
        });
    }

    public function updateEmailAccountQouta(array $input) {
        $emailRecord = EmailManager::query()->where('uid', $input['uid'])->first();
        if (!$emailRecord) {
            return false;
        }

        $existingQuota = $emailRecord->txtdiskquota === 'unlimited' ? 'unlimited' : (int) $emailRecord->txtdiskquota;
        if ($input['quota'] !== 'unlimited' && (!is_numeric($input['quota']) || (int)$input['quota'] < 0)) {
            return false;
        }
        $newQuota = $input['quota'] === 'unlimited' ? 'unlimited' : (int) $input['quota'];
        if ($existingQuota !== 'unlimited' && $newQuota !== 'unlimited' && $newQuota < $existingQuota) {
            return false; // Cannot reduce quota
        }

        $whmService = app(WHMService::class);
        $response = $whmService->updateEmailQuota($emailRecord->email, $newQuota);
        if ($response === false) {
            return false;
        }
        $emailRecord->update(['txtdiskquota' => $input['quota']]);
        return true;
    }

    public function updateEmailAccountPassword(array $input) {
        $emailRecord = EmailManager::query()->where('uid', $input['uid'])->first();
        if (!$emailRecord) {
            return false;
        }
        if ($this->isWeakPassword($input['password'])) {
            return false;
        }
        if ($input['password'] === $emailRecord->email_password) {
            return false;
        }
        $whmService = app(WHMService::class);
        $response = $whmService->changeEmailPassword($emailRecord->email, $input['password'], $emailRecord->domain);

        if ($response === false) {
            return false;
        }
        $emailRecord->update(['email_password' => $input['password']]);
        return true;
    }

    public function deleteEmailAccount($emailManager): bool
    {
        $email = EmailManager::query()->where('uid', $emailManager)->first();
        if (!$email) {
            return false;
        }
        $emailAddress = $email->email;
        $whmService = app(WHMService::class);
        $deletedFromWHM = $whmService->deleteEmailAccount($emailAddress);

        if (!$deletedFromWHM) {
            \Log::warning("Email not deleted via WHM: $emailAddress. Proceeding with local cleanup.");
        }
        $deletedSuffix = '-(deleted)' . time();
        $cpanelEmail = CpanelEmail::query()->where('email', $emailAddress)->first();
        if ($cpanelEmail) {
            $cpanelEmail->update(['email' => $emailAddress . $deletedSuffix]);
            $cpanelEmail->delete();
        }
        $adminEmail = EmailManager::query()->where('email', $emailAddress)->first();
        if ($adminEmail) {
            $adminEmail->update(['email' => $emailAddress . $deletedSuffix]);
            $adminEmail->delete();
        }
        return true;
    }

    public function getAllForDt() {
        $domain_selected = DomainSwitcher::query()->where('admin_id', user_id())->first();
        if ($domain_selected === null) {
            return $this->query()->where('admin_id', user_id())->orderBy('created_at', 'desc')->get();
        }
        $domain = Domain::getDomainById($domain_selected->domain_id);
        return $this->query()->where('admin_id', user_id())->where('domain', $domain->name)->orderBy('created_at', 'desc')->get();
    }

    /* Function to check password strength */
    private function isWeakPassword($password) {
        $strongPasswordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?])[A-Za-z\d!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?]{12,}$/";
        return !preg_match($strongPasswordRegex, $password);
    }

}
