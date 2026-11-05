@php
    $categorySlug = $categoryModel->slug;
    $actionUrl = $saveRoute ?? route('profile.'.$categorySlug.'.save');
    $profileLabel = strtolower($categoryModel->name);
@endphp

<form
    class="profile-form"
    id="profileForm"
    method="POST"
    action="{{ $actionUrl }}"
    data-profile-kind="creative"
    data-profile-label="{{ $profileLabel }}"
    enctype="multipart/form-data"
    novalidate
>
    @csrf
    <input type="file" name="profile_photo" id="pfFile_profile_photo" class="visually-hidden" tabindex="-1" aria-hidden="true" style="display:none;" />
    <input type="file" name="portfolio_image_1" id="pfFile_portfolio_1" class="visually-hidden" tabindex="-1" aria-hidden="true" style="display:none;" />
    <input type="file" name="portfolio_image_2" id="pfFile_portfolio_2" class="visually-hidden" tabindex="-1" aria-hidden="true" style="display:none;" />
    <input type="hidden" name="category" value="{{ $categorySlug }}" />
    <input type="hidden" name="brand" value="{{ $profile->brand ?? ($profile->brand_name ?? 'IDFR') }}" />

    @foreach($fields as $field)
        @php
            $key = $field->field_key;
            $widthClass = ($field->width === 'full') ? 'profile-form__full' : '';
            $isRequired = (bool) $field->is_required;
            
            // Map legacy element IDs to preserve JS bindings
            $fieldId = 'pf_' . \Illuminate\Support\Str::snake($key);
            if ($key === 'name') $fieldId = 'pfName';
            elseif ($key === 'profession') $fieldId = 'pfProfession';
            elseif ($key === 'place') $fieldId = 'pfPlace';
            elseif ($key === 'experience') $fieldId = 'pfExperience';
            elseif ($key === 'phone') $fieldId = 'pfPhone';
            elseif ($key === 'email') $fieldId = 'pfEmail';
            elseif ($key === 'about') $fieldId = 'pfAbout';
            elseif (in_array($key, ['kit', 'equipment', 'software', 'materials', 'atelier'], true)) $fieldId = 'pfEquipment';
            elseif ($key === 'service' || str_starts_with($key, 'service_') || str_contains($key, 'service')) $fieldId = 'pfServices';

            // Resolve field value
            $val = null;
            if (old($key) !== null) {
                $val = old($key);
            } elseif ($profile) {
                if ($key === 'name') {
                    $val = $profile->name;
                } elseif ($key === 'profession') {
                    $val = $profile->professional_title ?? $categoryModel->name;
                } elseif ($key === 'place') {
                    $val = $profile->location;
                } elseif ($key === 'experience') {
                    $val = $profile->experience;
                } elseif ($key === 'phone') {
                    $val = $profile->phone;
                } elseif ($key === 'email') {
                    $val = $profile->email;
                } elseif ($key === 'about') {
                    $val = $profile->biography ?? ($profile->description ?? '');
                } elseif (in_array($key, ['kit', 'equipment', 'software', 'materials'], true)) {
                    $val = $profile->equipment ?? ($profile->atelier_notes ?? '');
                } elseif ($key === 'atelier') {
                    $val = $profile->atelier_notes ?? ($profile->equipment ?? '');
                } elseif ($key === 'service' || str_starts_with($key, 'service_') || str_contains($key, 'service')) {
                    $val = old($key, old('service', $profile->services ?? []));
                } elseif ($key === 'outstation') {
                    $val = $profile->outstation ?? 'yes';
                } else {
                    $customs = $profile->custom_fields ?? [];
                    $val = $customs[$key] ?? $field->default_value;
                }
            } else {
                if ($key === 'name') {
                    $val = $user?->name ?? '';
                } elseif ($key === 'profession') {
                    $val = $categoryModel->name;
                } elseif ($key === 'phone') {
                    $val = $user?->phone ?? '';
                } elseif ($key === 'email') {
                    $val = $user?->email ?? '';
                } elseif ($key === 'outstation') {
                    $val = $field->default_value ?? 'yes';
                } elseif ($key === 'service' || str_starts_with($key, 'service_') || str_contains($key, 'service')) {
                    $val = old($key, old('service', []));
                } else {
                    $val = $field->default_value;
                }
            }
        @endphp

        @if($field->field_type === 'text')
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}">{{ $field->label }}</label>
                <input
                    type="text"
                    id="{{ $fieldId }}"
                    name="{{ $key }}"
                    @if($key === 'profession') readonly @endif
                    @if($key === 'name') autocomplete="name" minlength="2" @endif
                    @if($isRequired && $key !== 'profession') required @endif
                    placeholder="{{ $field->placeholder }}"
                    value="{{ $val }}"
                />
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>

        @elseif($field->field_type === 'combobox')
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}" id="{{ $fieldId }}Label">{{ $field->label }}</label>
                <div class="combo" id="placeCombo">
                    <input
                        type="text"
                        id="{{ $fieldId }}"
                        name="{{ $key }}"
                        role="combobox"
                        aria-autocomplete="list"
                        aria-expanded="false"
                        aria-controls="pfPlaceList"
                        autocomplete="off"
                        spellcheck="false"
                        @if($isRequired) required @endif
                        placeholder="{{ $field->placeholder }}"
                        value="{{ $val }}"
                    />
                    <ul
                        class="combo__list"
                        id="pfPlaceList"
                        role="listbox"
                        aria-labelledby="{{ $fieldId }}Label"
                        hidden
                    ></ul>
                </div>
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>

        @elseif($field->field_type === 'number')
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}">{{ $field->label }}</label>
                <div class="unit">
                    <input
                        type="number"
                        id="{{ $fieldId }}"
                        name="{{ $key }}"
                        inputmode="decimal"
                        @if($isRequired) required @endif
                        min="0"
                        max="40"
                        step="0.5"
                        placeholder="{{ $field->placeholder }}"
                        value="{{ $val }}"
                    />
                    @if($key === 'experience')
                        <span class="unit__label" aria-hidden="true">yrs</span>
                    @endif
                </div>
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>

        @elseif($field->field_type === 'tel')
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}">{{ $field->label }}</label>
                <input
                    type="tel"
                    id="{{ $fieldId }}"
                    name="{{ $key }}"
                    autocomplete="tel"
                    @if($isRequired) required @endif
                    placeholder="{{ $field->placeholder }}"
                    value="{{ $val }}"
                />
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>

        @elseif($field->field_type === 'email')
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}">{{ $field->label }}</label>
                <input
                    type="email"
                    id="{{ $fieldId }}"
                    name="{{ $key }}"
                    autocomplete="email"
                    @if($isRequired) required @endif
                    placeholder="{{ $field->placeholder }}"
                    value="{{ $val }}"
                />
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>

        @elseif($field->field_type === 'textarea')
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}">{{ $field->label }}</label>
                <textarea
                    id="{{ $fieldId }}"
                    name="{{ $key }}"
                    rows="{{ $key === 'about' ? 4 : 2 }}"
                    @if($key === 'about') minlength="20" @endif
                    @if($isRequired) required @endif
                    placeholder="{{ $field->placeholder }}"
                >{{ $val }}</textarea>
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>

        @elseif($field->field_type === 'checkbox_group')
            @php
                $selectedArray = is_array($val) ? $val : [];
                $options = $field->options;
                if (is_string($options)) {
                    $decoded = json_decode($options, true);
                    $options = is_array($decoded) ? $decoded : [];
                }
                $options = is_array($options) ? $options : [];
                $isServiceChoice = ($key === 'service' || str_starts_with($key, 'service_') || str_contains($key, 'service'));
                $checkboxName = $isServiceChoice ? 'service[]' : "{$key}[]";
            @endphp
            <fieldset class="field {{ $widthClass }}" id="{{ $fieldId }}">
                <legend>
                    {{ $field->label }}
                    <span class="field__note">{{ $field->help_text ?? 'Select all that apply' }}</span>
                </legend>
                @if(empty($options))
                    <div style="padding: 1rem 1.25rem; border-radius: 6px; background: rgba(229,184,61,0.08); border: 1px dashed rgba(229,184,61,0.3); color: var(--gold); font-size: 0.85rem; line-height: 1.5;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span>No service options have been configured for this profession yet. Options can be added in <strong>Admin Dashboard &rarr; Dynamic Forms &rarr; Fields</strong>.</span>
                    </div>
                @else
                    <div class="choices choices--grid">
                        @foreach($options as $optKey => $optLabel)
                            <label class="choice choice--check">
                                <input
                                    type="checkbox"
                                    name="{{ $checkboxName }}"
                                    value="{{ $optKey }}"
                                    {{ (in_array($optKey, $selectedArray, true) || in_array($optLabel, $selectedArray, true)) ? 'checked' : '' }}
                                /><span>{{ $optLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
                <span class="field__error" aria-live="polite"></span>
            </fieldset>

        @elseif($field->field_type === 'radio_group')
            @php
                $selectedVal = strtolower((string) ($val ?? 'yes'));
                $options = $field->options;
                if (is_string($options)) {
                    $decoded = json_decode($options, true);
                    $options = is_array($decoded) ? $decoded : null;
                }
                $options = is_array($options) ? $options : ['yes' => 'Yes', 'no' => 'No'];
            @endphp
            <fieldset class="field {{ $widthClass }}">
                <legend>{{ $field->label }}</legend>
                <div class="choices choices--row">
                    @foreach($options as $optVal => $optLabel)
                        <label class="choice choice--radio">
                            <input
                                type="radio"
                                name="{{ $key }}"
                                value="{{ $optVal }}"
                                @if($isRequired) required @endif
                                {{ $selectedVal === strtolower((string) $optVal) ? 'checked' : '' }}
                            /><span>{{ $optLabel }}</span>
                        </label>
                    @endforeach
                </div>
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </fieldset>

        @elseif(in_array($field->field_type, ['select', 'dropdown'], true))
            @php
                $options = $field->options;
                if (is_string($options)) {
                    $decoded = json_decode($options, true);
                    $options = is_array($decoded) ? $decoded : [];
                }
                $options = is_array($options) ? $options : [];
            @endphp
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}">{{ $field->label }}</label>
                <select id="{{ $fieldId }}" name="{{ $key }}" @if($isRequired) required @endif>
                    <option value="">{{ $field->placeholder ?? 'Please select' }}</option>
                    @foreach($options as $optVal => $optLabel)
                        <option value="{{ $optVal }}" {{ (string)$val === (string)$optVal ? 'selected' : '' }}>
                            {{ $optLabel }}
                        </option>
                    @endforeach
                </select>
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>

        @elseif($field->field_type === 'url')
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}">{{ $field->label }}</label>
                <input
                    type="url"
                    id="{{ $fieldId }}"
                    name="{{ $key }}"
                    @if($isRequired) required @endif
                    placeholder="{{ $field->placeholder ?? 'https://...' }}"
                    value="{{ $val }}"
                />
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>

        @elseif(in_array($field->field_type, ['image', 'file'], true))
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}">{{ $field->label }}</label>
                <input
                    type="file"
                    id="{{ $fieldId }}"
                    name="{{ $key }}"
                    accept="image/*"
                    @if($isRequired && empty($val)) required @endif
                />
                @if(!empty($val) && is_string($val))
                    <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px;">
                        <img src="{{ str_starts_with($val, 'http') ? $val : asset(ltrim($val, '/')) }}" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid rgba(255,255,255,0.15);" />
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Current uploaded file</span>
                    </div>
                @endif
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>

        @elseif($field->field_type === 'multiple_images')
            <div class="field {{ $widthClass }}">
                <label for="{{ $fieldId }}">{{ $field->label }}</label>
                <input
                    type="file"
                    id="{{ $fieldId }}"
                    name="{{ $key }}[]"
                    multiple
                    accept="image/*"
                />
                @if(!empty($val) && is_array($val))
                    <div style="margin-top: 6px; display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($val as $mImg)
                            @php $mUrl = is_array($mImg) ? ($mImg['image'] ?? '') : $mImg; @endphp
                            @if($mUrl)
                                <img src="{{ str_starts_with($mUrl, 'http') ? $mUrl : asset(ltrim($mUrl, '/')) }}" alt="Portfolio" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid rgba(255,255,255,0.15);" />
                            @endif
                        @endforeach
                    </div>
                @endif
                @if($field->help_text)<small class="field__note">{{ $field->help_text }}</small>@endif
                <span class="field__error" aria-live="polite"></span>
            </div>
        @endif
    @endforeach

    <div class="profile-form__actions profile-form__full">
        <button class="btn btn--ghost" type="reset">
            <span>Reset</span>
        </button>
        <button class="btn btn--gold" type="submit">
            <span>Save Profile</span>
            <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right" />
            </svg>
        </button>
    </div>
</form>

