<?php

namespace App\Models;

use App\Constants\AddressTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'company',
        'line1',
        'line2',
        'city',
        'county',
        'postcode',
        'country',
        'phone',
        'is_default',
        'is_validated',
        'validation_data',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_validated' => 'boolean',
        'validation_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeShipping($query)
    {
        return $query->whereIn('type', [AddressTypes::SHIPPING, AddressTypes::BOTH]);
    }

    public function scopeBilling($query)
    {
        return $query->whereIn('type', [AddressTypes::BILLING, AddressTypes::BOTH]);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->line1,
            $this->line2,
            $this->city,
            $this->county,
            $this->postcode,
            $this->getCountryName(),
        ]);

        return implode(', ', $parts);
    }

    public function getFormattedAddressAttribute(): array
    {
        return [
            'name' => $this->getFullNameAttribute(),
            'company' => $this->company,
            'line1' => $this->line1,
            'line2' => $this->line2,
            'city' => $this->city,
            'county' => $this->county,
            'postcode' => $this->postcode,
            'country' => $this->country,
            'country_name' => $this->getCountryName(),
            'phone' => $this->phone,
        ];
    }

    public function getCountryName(): string
    {
        $countries = [
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'CA' => 'Canada',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'ES' => 'Spain',
            'IT' => 'Italy',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'IE' => 'Ireland',
            'AT' => 'Austria',
            'CH' => 'Switzerland',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'PT' => 'Portugal',
            'GR' => 'Greece',
            'PL' => 'Poland',
            'CZ' => 'Czech Republic',
            'HU' => 'Hungary',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'HR' => 'Croatia',
            'BG' => 'Bulgaria',
            'RO' => 'Romania',
            'LT' => 'Lithuania',
            'LV' => 'Latvia',
            'EE' => 'Estonia',
            'MT' => 'Malta',
            'CY' => 'Cyprus',
            'LU' => 'Luxembourg',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'CN' => 'China',
            'IN' => 'India',
            'BR' => 'Brazil',
            'MX' => 'Mexico',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'PE' => 'Peru',
            'ZA' => 'South Africa',
            'EG' => 'Egypt',
            'MA' => 'Morocco',
            'NG' => 'Nigeria',
            'KE' => 'Kenya',
            'GH' => 'Ghana',
            'TZ' => 'Tanzania',
            'UG' => 'Uganda',
            'ZW' => 'Zimbabwe',
            'NZ' => 'New Zealand',
            'MY' => 'Malaysia',
            'SG' => 'Singapore',
            'TH' => 'Thailand',
            'VN' => 'Vietnam',
            'PH' => 'Philippines',
            'ID' => 'Indonesia',
            'AE' => 'United Arab Emirates',
            'SA' => 'Saudi Arabia',
            'IL' => 'Israel',
            'TR' => 'Turkey',
            'RU' => 'Russia',
            'UA' => 'Ukraine',
        ];

        return $countries[$this->country] ?? $this->country;
    }

    public function getNormalizedPostcode(): string
    {
        if ($this->country === 'GB') {
            return strtoupper(str_replace(' ', '', $this->postcode));
        }

        return strtoupper($this->postcode);
    }

    public function isUKAddress(): bool
    {
        return $this->country === 'GB';
    }

    public function isInternational(): bool
    {
        return $this->country !== 'GB';
    }

    public function getShippingZone(): ?ShippingZone
    {
        return ShippingZone::query()
            ->active()
            ->where(function ($query) {
                $query->whereJsonContains('countries', $this->country)
                    ->orWhereJsonContains('countries', '*');
            })
            ->get()
            ->first(function ($zone) {
                return $zone->coversPostcode($this->postcode);
            });
    }

    public function setAsDefault(): void
    {
        if ($this->user_id) {
            static::where('user_id', $this->user_id)
                ->where('type', $this->type)
                ->update(['is_default' => false]);

            $this->update(['is_default' => true]);
        }
    }

    public function markAsValidated(array $validationData = []): void
    {
        $this->update([
            'is_validated' => true,
            'validation_data' => $validationData,
        ]);
    }

    public function markAsInvalid(): void
    {
        $this->update([
            'is_validated' => false,
            'validation_data' => null,
        ]);
    }

    public function needsValidation(): bool
    {
        return !$this->is_validated ||
            $this->updated_at->lt(now()->subDays(30));
    }

    public function toShippoFormat(): array
    {
        return [
            'name' => $this->getFullNameAttribute(),
            'company' => $this->company ?? '',
            'street1' => $this->line1,
            'street2' => $this->line2 ?? '',
            'city' => $this->city,
            'state' => $this->county ?? '',
            'zip' => $this->postcode,
            'country' => $this->country,
            'phone' => $this->phone ?? '',
            'email' => $this->user?->email ?? '',
        ];
    }

    public function isShippingAddress(): bool
    {
        return in_array($this->type, [AddressTypes::SHIPPING, AddressTypes::BOTH]);
    }

    public function isBillingAddress(): bool
    {
        return in_array($this->type, [AddressTypes::BILLING, AddressTypes::BOTH]);
    }

    public function getTypeLabel(): string
    {
        return AddressTypes::getTypeLabel($this->type);
    }
}
