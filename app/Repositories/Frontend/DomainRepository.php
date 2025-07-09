<?php

namespace App\Repositories\Frontend;
use App\Models\Domain;
use App\Models\DomainInfo;
use App\Models\DomainSwitcher;
use App\Models\DomainUser;
use App\Models\Token;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DomainRepository extends BaseRepository
{
    const MODEL = Domain::class;

    public function switchDomain(array $input) {
        $userId = Auth::user()->id;
        if (!$userId) {
            return false;
        }
        return DB::transaction(function() use($input, $userId) {
            if (isset($input['domain_to_use'])) {
                $domain = $this->explodeDomainName($input['domain_to_use']);
                if (!$domain) {
                    return false;
                }

                $token = Token::query()->where('domain', $domain['name'])->first();
                if (!$token) {
                    DomainSwitcher::updateOrCreate(
                        ['admin_id' => $userId],
                        [
                            'domain_id' => $domain['id'],
                            'admin_id' => $userId,
                            'token_id' =>  null
                        ]
                    );
                    return true;
                }

                $domainSwitched = DomainSwitcher::updateOrCreate(
                    ['admin_id' => $userId],
                    [
                        'domain_id' => $domain['id'],
                        'admin_id' => $userId,
                        'token_id' =>  $token->id
                    ]
                );
                $token = Token::query()->where('domain', $token->domain)->update(['is_active' => true]);
                if (!$token) {
                    DomainSwitcher::query()->where('admin_id', $userId)->forceDelete();
                }
            }
            else{
                DomainSwitcher::query()->where('admin_id', $userId)->forceDelete();
                $domainSwitched = Domain::query()->where('admin_id', $userId)->get();
            }
            return $domainSwitched;
        });
    }

    public function getAllForDt() {
        //$domainToUse = DomainSwitcher::where('admin_id', user_id())->first();
        $domainIds = DomainUser::getAdminAssignedDomains(user_id());
        return $this->query()->whereIn('domain_type', ['main', 'addon'])->whereIn('id', $domainIds)->orderBy('created_at', 'desc')->get();
    }

    public function domainInfo($domain)
    {
        return DomainInfo::query()->where('domain', $domain)->first(); //todo user domain_predefine_name to avoid conflicts
//        return DomainInfo::query()->where('domain_predefine_name', $domain)->first();
    }

    public function explodeDomainName($domainName)
    {
        $mainDomains = $this->query()->where('domain_type', 'sub')->orderBy('created_at', 'desc')->pluck('name');
        $matchedMainDomain = $mainDomains->first(function ($knownDomain) use ($domainName) {
            return str_ends_with($knownDomain, '.' . $domainName) || $knownDomain === $domainName;
        });

        if ($matchedMainDomain) {
            $domain = $this->query()
                ->where('name', $matchedMainDomain)
                ->selectRaw('DISTINCT ON (parent_domain) *')
                ->orderBy('parent_domain')
                ->orderBy('created_at', 'desc')->first();

            if ($domain) {
                $parts = explode('.', $matchedMainDomain);

                $baseDomain = implode('.', array_slice($parts, -3));
                if (strlen($parts[count($parts) - 2]) <= 3) {
                    $baseDomain = implode('.', array_slice($parts, -3));
                }
                else {
                    $baseDomain = implode('.', array_slice($parts, -2));
                }
                return ["name" => $baseDomain, "id" => $domain->id];

                // return $this->query()->where('name', $baseDomain)->first();
            }
        }
        return $this->query()->where('domain_type', 'main')->where('name', $domainName)->first();
    }
}
