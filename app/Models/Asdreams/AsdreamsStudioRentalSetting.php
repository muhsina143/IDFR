<?php

namespace App\Models\Asdreams;

use Illuminate\Support\Collection;

class AsdreamsStudioRentalSetting
{
    /**
     * Supported currency configurations.
     *
     * @var array<string, array{label: string, symbol: string, space_after: bool}>
     */
    public const CURRENCIES = [
        'INR' => [
            'label' => 'INR (₹)',
            'symbol' => '₹',
            'space_after' => false,
        ],
        'AED' => [
            'label' => 'AED (د.إ)',
            'symbol' => 'د.إ',
            'space_after' => true,
        ],
        'USD' => [
            'label' => 'USD ($)',
            'symbol' => '$',
            'space_after' => false,
        ],
    ];

    /**
     * Supported rental unit configurations.
     *
     * @var array<string, array{label: string, display: string}>
     */
    public const UNITS = [
        'Per Hour' => [
            'label' => 'Per Hour',
            'display' => '/ Hour',
        ],
        'Per Day' => [
            'label' => 'Per Day',
            'display' => '/ Day',
        ],
        'Per Session' => [
            'label' => 'Per Session',
            'display' => '/ Session',
        ],
    ];

    /**
     * Default Studio Rental pricing & information configuration values.
     *
     * @var array<string, string>
     */
    public const DEFAULTS = [
        'studio_rental_title' => 'Studio Rental',
        'studio_rental_area' => '4,000 sq. ft.',
        'studio_rental_short_description' => 'Professional photography and creative studio space available for your projects.',
        'studio_rental_booking_info' => '',
        'studio_rental_additional_note' => '',
        'studio_rental_price_amount' => '1000',
        'studio_rental_currency' => 'INR',
        'studio_rental_unit' => 'Per Day',
        'studio_rental_pricing_label' => 'Starting From',
        'studio_rental_price_description' => 'Final pricing may vary depending on rental duration, studio requirements and additional services.',
        'studio_rental_price_enabled' => '1',
    ];

    /**
     * Seed default settings if they do not exist yet in the database.
     */
    public static function ensureDefaults(): void
    {
        foreach (self::DEFAULTS as $key => $value) {
            if (AsdreamsWebsiteSetting::where('key', $key)->doesntExist()) {
                AsdreamsWebsiteSetting::set($key, $value, 'studio_rental');
            }
        }
    }

    /**
     * Resolve typed, formatted Studio Rental settings from the database or preloaded map.
     *
     * @param  Collection<string, string|null>|array<string, string|null>|null  $settingsMap
     * @return array{
     *     title: string,
     *     studio_area: string,
     *     short_description: string,
     *     booking_info: string,
     *     additional_note: string,
     *     price_amount: string,
     *     formatted_number: string,
     *     currency: string,
     *     currency_symbol: string,
     *     rental_unit: string,
     *     unit_display: string,
     *     pricing_label: string,
     *     price_description: string,
     *     price_enabled: bool,
     *     formatted_price: string,
     * }
     */
    public static function getConfig(Collection|array|null $settingsMap = null): array
    {
        $map = match (true) {
            $settingsMap instanceof Collection => $settingsMap->all(),
            is_array($settingsMap) => $settingsMap,
            default => AsdreamsWebsiteSetting::pluck('value', 'key')->all(),
        };

        $getValue = static function (string $key) use ($map): string {
            if (array_key_exists($key, $map) && $map[$key] !== null) {
                return (string) $map[$key];
            }

            return self::DEFAULTS[$key] ?? '';
        };

        $currency = $getValue('studio_rental_currency');
        if (! array_key_exists($currency, self::CURRENCIES)) {
            $currency = self::DEFAULTS['studio_rental_currency'];
        }

        $unit = $getValue('studio_rental_unit');
        if (! array_key_exists($unit, self::UNITS)) {
            $unit = self::DEFAULTS['studio_rental_unit'];
        }

        $rawAmount = trim($getValue('studio_rental_price_amount'));
        $numericClean = str_replace([',', ' '], '', $rawAmount);
        $amountFloat = is_numeric($numericClean) ? (float) $numericClean : 1000.0;
        $decimals = (abs($amountFloat - floor($amountFloat)) > 0.0001) ? 2 : 0;
        $formattedNumber = number_format($amountFloat, $decimals);

        $currencyMeta = self::CURRENCIES[$currency];
        $formattedPrice = $currencyMeta['symbol']
            .($currencyMeta['space_after'] ? ' ' : '')
            .$formattedNumber;

        $priceEnabledRaw = $getValue('studio_rental_price_enabled');
        $priceEnabled = in_array(strtolower($priceEnabledRaw), ['1', 'true', 'yes', 'on'], true);

        return [
            'title' => $getValue('studio_rental_title') ?: self::DEFAULTS['studio_rental_title'],
            'studio_area' => $getValue('studio_rental_area') ?: self::DEFAULTS['studio_rental_area'],
            'short_description' => $getValue('studio_rental_short_description'),
            'booking_info' => $getValue('studio_rental_booking_info'),
            'additional_note' => $getValue('studio_rental_additional_note'),
            'price_amount' => is_numeric($numericClean) ? $numericClean : self::DEFAULTS['studio_rental_price_amount'],
            'formatted_number' => $formattedNumber,
            'currency' => $currency,
            'currency_symbol' => $currencyMeta['symbol'],
            'rental_unit' => $unit,
            'unit_display' => self::UNITS[$unit]['display'],
            'pricing_label' => $getValue('studio_rental_pricing_label'),
            'price_description' => $getValue('studio_rental_price_description'),
            'price_enabled' => $priceEnabled,
            'formatted_price' => $formattedPrice,
        ];
    }

    /**
     * Persist validated Studio Rental pricing & information fields without duplicating keys.
     *
     * @param  array<string, mixed>  $data
     */
    public static function saveConfig(array $data): void
    {
        $mapping = [
            'studio_rental_title' => trim((string) ($data['title'] ?? self::DEFAULTS['studio_rental_title'])),
            'studio_rental_area' => trim((string) ($data['studio_area'] ?? '')) ?: self::DEFAULTS['studio_rental_area'],
            'studio_rental_short_description' => trim((string) ($data['short_description'] ?? '')),
            'studio_rental_booking_info' => trim((string) ($data['booking_info'] ?? '')),
            'studio_rental_additional_note' => trim((string) ($data['additional_note'] ?? '')),
            'studio_rental_price_amount' => (string) ($data['price_amount'] ?? self::DEFAULTS['studio_rental_price_amount']),
            'studio_rental_currency' => (string) ($data['currency'] ?? self::DEFAULTS['studio_rental_currency']),
            'studio_rental_unit' => (string) ($data['rental_unit'] ?? self::DEFAULTS['studio_rental_unit']),
            'studio_rental_pricing_label' => trim((string) ($data['pricing_label'] ?? '')),
            'studio_rental_price_description' => trim((string) ($data['price_description'] ?? '')),
            'studio_rental_price_enabled' => ! empty($data['price_enabled']) ? '1' : '0',
        ];

        foreach ($mapping as $key => $value) {
            AsdreamsWebsiteSetting::set($key, $value, 'studio_rental');
        }
    }
}
