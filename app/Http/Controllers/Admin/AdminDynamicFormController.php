<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use App\Models\AdminActivity;
use App\Models\CategoryFormField;
use App\Models\CreativeProfessional;
use App\Models\Designer;
use App\Models\ProfessionalCategory;
use App\Models\ProfessionalFormField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminDynamicFormController extends Controller
{
    /**
     * Display categories listing & form builder overview.
     */
    public function index(Request $request): View
    {
        $categories = ProfessionalCategory::withCount('fields')
            ->orderBy('display_order')
            ->get();

        $totalFieldsCount = ProfessionalFormField::count();
        $activeFieldsCount = ProfessionalFormField::where('is_active', true)->count();

        return view('admin.dynamic-forms.index', compact('categories', 'totalFieldsCount', 'activeFieldsCount'));
    }

    /**
     * Show form for creating a new professional category.
     */
    public function createCategory(): View
    {
        $maxOrder = ProfessionalCategory::max('display_order') ?? 0;
        $nextOrder = $maxOrder + 10;

        return view('admin.dynamic-forms.create-category', compact('nextOrder'));
    }

    /**
     * Store a newly created professional category.
     */
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:professional_categories,slug', 'regex:/^[a-z0-9-]+$/'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'form_title' => ['nullable', 'string', 'max:255'],
            'form_lead' => ['nullable', 'string'],
            'default_photo' => ['nullable'],
            'banner_image' => ['nullable'],
            'display_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'slug.regex' => 'The slug must contain only lowercase alphanumeric characters and hyphens.',
            'slug.unique' => 'A category with this URL slug already exists.',
        ]);

        $name = trim($validated['name']);

        if (! empty($validated['slug'])) {
            $slug = Str::slug($validated['slug']);
        } else {
            $slug = Str::slug($name);
            if (empty($slug)) {
                $slug = 'category-'.Str::random(4);
            }
            $originalSlug = $slug;
            $counter = 1;
            while (ProfessionalCategory::where('slug', $slug)->exists()) {
                $slug = $originalSlug.'-'.$counter;
                $counter++;
            }
        }

        $uploadDir = public_path('uploads/categories');
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $defaultPhoto = null;
        if ($request->hasFile('default_photo')) {
            $f = $request->file('default_photo');
            $fn = 'cat_thumb_'.time().'_'.Str::random(6).'.'.$f->getClientOriginalExtension();
            $f->move($uploadDir, $fn);
            $defaultPhoto = 'uploads/categories/'.$fn;
        } elseif ($request->filled('default_photo') && is_string($request->input('default_photo'))) {
            $defaultPhoto = trim($request->input('default_photo'));
        }

        $bannerImage = null;
        if ($request->hasFile('banner_image')) {
            $f = $request->file('banner_image');
            $fn = 'cat_banner_'.time().'_'.Str::random(6).'.'.$f->getClientOriginalExtension();
            $f->move($uploadDir, $fn);
            $bannerImage = 'uploads/categories/'.$fn;
        } elseif ($request->filled('banner_image') && is_string($request->input('banner_image'))) {
            $bannerImage = trim($request->input('banner_image'));
        }

        $heroTitle = ! empty($validated['hero_title']) ? trim($validated['hero_title']) : $name;
        $formTitle = ! empty($validated['form_title']) ? trim($validated['form_title']) : $name.' Details';
        $formLead = ! empty($validated['form_lead']) ? trim($validated['form_lead']) : 'Fill in your experience, specialisations and contact details to publish your creative profile.';
        $displayOrder = ! empty($validated['display_order']) ? (int) $validated['display_order'] : ((ProfessionalCategory::max('display_order') ?? 0) + 10);

        $category = ProfessionalCategory::create([
            'name' => $name,
            'slug' => $slug,
            'hero_title' => $heroTitle,
            'description' => trim($validated['description'] ?? ''),
            'form_title' => $formTitle,
            'form_lead' => $formLead,
            'default_photo' => $defaultPhoto,
            'banner_image' => $bannerImage,
            'display_order' => $displayOrder,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Automatically attach core common fields to this category
        $commonFields = ProfessionalFormField::where('is_common', true)
            ->orWhereIn('field_key', ['name', 'profession', 'place', 'experience', 'phone', 'email', 'about', 'outstation'])
            ->orderBy('display_order')
            ->get();

        foreach ($commonFields as $commonField) {
            CategoryFormField::firstOrCreate(
                [
                    'category_id' => $category->id,
                    'field_id' => $commonField->id,
                ],
                [
                    'display_order' => $commonField->display_order,
                    'is_required' => $commonField->is_required,
                    'is_active' => true,
                ]
            );
        }

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'form_category_created',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Created new form category: {$category->name} ({$category->slug})",
            $request
        );

        return redirect()->route('admin.dynamic-forms.index')
            ->with('success', "Category '{$category->name}' created successfully with standard profile form fields.");
    }

    /**
     * Edit a specific category's meta and form headers.
     */
    public function editCategory(ProfessionalCategory $category): View
    {
        return view('admin.dynamic-forms.edit-category', compact('category'));
    }

    /**
     * Update a category's meta details.
     */
    public function updateCategory(Request $request, ProfessionalCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'hero_title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'form_title' => ['required', 'string', 'max:255'],
            'form_lead' => ['nullable', 'string'],
            'default_photo' => ['nullable'],
            'banner_image' => ['nullable'],
            'display_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $uploadDir = public_path('uploads/categories');
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $defaultPhoto = $category->default_photo;
        if ($request->hasFile('default_photo')) {
            $f = $request->file('default_photo');
            $fn = 'cat_thumb_'.time().'_'.Str::random(6).'.'.$f->getClientOriginalExtension();
            $f->move($uploadDir, $fn);
            $defaultPhoto = 'uploads/categories/'.$fn;
        } elseif ($request->has('default_photo') && is_string($request->input('default_photo')) && $request->filled('default_photo')) {
            $defaultPhoto = trim($request->input('default_photo'));
        }

        $bannerImage = $category->banner_image;
        if ($request->hasFile('banner_image')) {
            $f = $request->file('banner_image');
            $fn = 'cat_banner_'.time().'_'.Str::random(6).'.'.$f->getClientOriginalExtension();
            $f->move($uploadDir, $fn);
            $bannerImage = 'uploads/categories/'.$fn;
        } elseif ($request->has('banner_image') && is_string($request->input('banner_image')) && $request->filled('banner_image')) {
            $bannerImage = trim($request->input('banner_image'));
        }

        $category->update([
            'name' => trim($validated['name']),
            'hero_title' => trim($validated['hero_title']),
            'description' => trim($validated['description'] ?? ''),
            'form_title' => trim($validated['form_title']),
            'form_lead' => trim($validated['form_lead'] ?? ''),
            'default_photo' => $defaultPhoto,
            'banner_image' => $bannerImage,
            'display_order' => (int) ($validated['display_order'] ?? $category->display_order),
            'is_active' => $request->boolean('is_active'),
        ]);

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'form_category_updated',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Updated form category: {$category->name}",
            $request
        );

        return redirect()->route('admin.dynamic-forms.index')
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Delete a custom category safely.
     */
    public function destroyCategory(Request $request, ProfessionalCategory $category): RedirectResponse
    {
        $coreSlugs = [
            'makeup-artist',
            'hairstylist',
            'photographer',
            'videographer',
            'fashion-designer',
            'drone-pilot',
            'photo-video-editor',
            'jewellery-designer',
        ];

        if (in_array($category->slug, $coreSlugs, true)) {
            return redirect()->back()
                ->with('error', "Core category '{$category->name}' is protected and cannot be deleted. You can deactivate it instead.");
        }

        $profileCount = CreativeProfessional::where('category', $category->slug)->count();
        if ($profileCount > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete category '{$category->name}' because {$profileCount} professional profile(s) exist under it. You can deactivate it instead.");
        }

        $name = $category->name;
        CategoryFormField::where('category_id', $category->id)->delete();
        $category->delete();

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'form_category_deleted',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Deleted form category: {$name}",
            $request
        );

        return redirect()->route('admin.dynamic-forms.index')
            ->with('success', "Category '{$name}' deleted successfully.");
    }

    /**
     * Toggle a category's active/inactive status.
     */
    public function toggleCategory(Request $request, ProfessionalCategory $category): RedirectResponse
    {
        $category->is_active = ! $category->is_active;
        $category->save();

        $statusLabel = $category->is_active ? 'activated' : 'deactivated';

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'form_category_toggled',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Toggled category {$category->name} to {$statusLabel}",
            $request
        );

        return redirect()->back()
            ->with('success', "Category '{$category->name}' has been {$statusLabel}.");
    }

    /**
     * Manage fields assigned to a specific category.
     */
    public function categoryFields(ProfessionalCategory $category): View
    {
        $assignedPivots = CategoryFormField::with('field')
            ->where('category_id', $category->id)
            ->orderBy('display_order')
            ->get();

        $assignedFieldIds = $assignedPivots->pluck('field_id')->toArray();

        $availableFields = ProfessionalFormField::whereNotIn('id', $assignedFieldIds)
            ->where('is_active', true)
            ->orderBy('label')
            ->get();

        return view('admin.dynamic-forms.category-fields', compact('category', 'assignedPivots', 'availableFields'));
    }

    /**
     * Update order and overrides for fields in a category.
     */
    public function updateCategoryFields(Request $request, ProfessionalCategory $category): RedirectResponse
    {
        $items = $request->input('pivots', []);

        foreach ($items as $pivotId => $data) {
            $pivot = CategoryFormField::where('category_id', $category->id)->where('id', $pivotId)->first();
            if ($pivot) {
                $updateData = [
                    'display_order' => (int) ($data['display_order'] ?? $pivot->display_order),
                    'is_required' => isset($data['is_required']) ? (bool) $data['is_required'] : null,
                    'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : false,
                    'label_override' => ! empty($data['label_override']) ? trim($data['label_override']) : null,
                    'placeholder_override' => ! empty($data['placeholder_override']) ? trim($data['placeholder_override']) : null,
                    'help_text_override' => ! empty($data['help_text_override']) ? trim($data['help_text_override']) : null,
                ];

                if (array_key_exists('options_override', $data)) {
                    $raw = trim($data['options_override'] ?? '');
                    if ($raw === '') {
                        $updateData['options_override'] = null;
                    } elseif (str_starts_with($raw, '{') || str_starts_with($raw, '[')) {
                        $updateData['options_override'] = json_decode($raw, true);
                    } else {
                        $lines = array_filter(array_map('trim', explode("\n", $raw)));
                        $parsed = [];
                        foreach ($lines as $line) {
                            if (str_contains($line, ':')) {
                                [$k, $v] = explode(':', $line, 2);
                                $parsed[trim($k)] = trim($v);
                            } else {
                                $slug = Str::slug($line);
                                $parsed[$slug] = $line;
                            }
                        }
                        $updateData['options_override'] = ! empty($parsed) ? $parsed : null;
                    }
                }

                $pivot->update($updateData);
            }
        }

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'category_fields_updated',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Updated assigned form fields for category: {$category->name}",
            $request
        );

        return redirect()->back()
            ->with('success', "Form fields for '{$category->name}' updated successfully.");
    }

    /**
     * Attach an existing field to a category.
     */
    public function attachFieldToCategory(Request $request, ProfessionalCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'field_id' => ['required', 'exists:professional_form_fields,id'],
            'display_order' => ['nullable', 'integer'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $maxOrder = CategoryFormField::where('category_id', $category->id)->max('display_order') ?? 0;

        CategoryFormField::firstOrCreate(
            [
                'category_id' => $category->id,
                'field_id' => $validated['field_id'],
            ],
            [
                'display_order' => $validated['display_order'] ?? ($maxOrder + 10),
                'is_required' => $request->boolean('is_required'),
                'is_active' => true,
            ]
        );

        return redirect()->back()
            ->with('success', 'Field added to category successfully.');
    }

    /**
     * Detach a field from a category.
     */
    public function detachFieldFromCategory(Request $request, ProfessionalCategory $category, ProfessionalFormField $field): RedirectResponse
    {
        CategoryFormField::where('category_id', $category->id)
            ->where('field_id', $field->id)
            ->delete();

        return redirect()->back()
            ->with('success', "Field '{$field->label}' removed from category.");
    }

    /**
     * Fields Library: list all global form fields.
     */
    public function fields(Request $request): View
    {
        $query = ProfessionalFormField::with(['categories'])->withCount('categories');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                    ->orWhere('field_key', 'like', "%{$search}%")
                    ->orWhere('placeholder', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('field_type', $type);
        }

        if ($catFilter = $request->input('category')) {
            $query->whereHas('categories', function ($q) use ($catFilter) {
                $q->where('slug', $catFilter)->orWhere('professional_categories.id', $catFilter);
            });
        }

        $fields = $query->orderBy('display_order')->orderBy('label')->paginate(25)->withQueryString();
        $allCategories = ProfessionalCategory::orderBy('name')->get();

        return view('admin.dynamic-forms.fields', compact('fields', 'allCategories'));
    }

    /**
     * Show form for creating a new field.
     */
    public function createField(): View
    {
        $categories = ProfessionalCategory::orderBy('name')->get();

        return view('admin.dynamic-forms.create-field', compact('categories'));
    }

    /**
     * Store a newly created form field.
     */
    public function storeField(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:150'],
            'field_key' => ['required', 'string', 'max:100', 'unique:professional_form_fields,field_key', 'regex:/^[a-z0-9_]+$/'],
            'field_type' => ['required', 'string', 'in:text,number,tel,email,combobox,textarea,checkbox_group,radio_group,select,url,image,file,multiple_images'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'help_text' => ['nullable', 'string', 'max:255'],
            'default_value' => ['nullable', 'string'],
            'options' => ['nullable', 'string'],
            'validation_rules' => ['nullable', 'string', 'max:255'],
            'width' => ['required', 'in:half,full'],
            'display_order' => ['nullable', 'integer'],
            'is_required' => ['nullable', 'boolean'],
            'is_common' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:professional_categories,id'],
        ], [
            'field_key.regex' => 'Field key may only contain lowercase letters, numbers, and underscores.',
        ]);

        $options = null;
        if (! empty($validated['options'])) {
            $raw = trim($validated['options']);
            if (str_starts_with($raw, '{') || str_starts_with($raw, '[')) {
                $options = json_decode($raw, true);
            } else {
                $lines = array_filter(array_map('trim', explode("\n", $raw)));
                $options = [];
                foreach ($lines as $line) {
                    if (str_contains($line, ':')) {
                        [$k, $v] = explode(':', $line, 2);
                        $options[trim($k)] = trim($v);
                    } else {
                        $slug = Str::slug($line);
                        $options[$slug] = $line;
                    }
                }
            }
        }

        $validationRules = null;
        if (! empty($validated['validation_rules'])) {
            $normalized = ProfileController::sanitizeValidationRules(
                $validated['validation_rules'],
                $request->boolean('is_required'),
                $validated['field_type']
            );
            $validationRules = implode('|', $normalized);
        }

        $field = ProfessionalFormField::create([
            'label' => trim($validated['label']),
            'field_key' => strtolower(trim($validated['field_key'])),
            'field_type' => $validated['field_type'],
            'placeholder' => ! empty($validated['placeholder']) ? trim($validated['placeholder']) : null,
            'help_text' => ! empty($validated['help_text']) ? trim($validated['help_text']) : null,
            'default_value' => $validated['default_value'] ?? null,
            'options' => $options,
            'validation_rules' => $validationRules,
            'width' => $validated['width'],
            'display_order' => (int) ($validated['display_order'] ?? 50),
            'is_required' => $request->boolean('is_required'),
            'is_common' => $request->boolean('is_common'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Attach to selected categories
        if (! empty($validated['categories'])) {
            foreach ($validated['categories'] as $catId) {
                CategoryFormField::create([
                    'category_id' => $catId,
                    'field_id' => $field->id,
                    'display_order' => $field->display_order,
                    'is_required' => $field->is_required,
                    'is_active' => true,
                ]);
            }
        }

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'form_field_created',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Created new form field: {$field->label} ({$field->field_key})",
            $request
        );

        return redirect()->route('admin.dynamic-forms.fields.index')
            ->with('success', "Field '{$field->label}' created successfully.");
    }

    /**
     * Show edit form for a field.
     */
    public function editField(ProfessionalFormField $field): View
    {
        $categories = ProfessionalCategory::orderBy('name')->get();
        $attachedCategoryIds = $field->categories()->pluck('professional_categories.id')->toArray();

        return view('admin.dynamic-forms.edit-field', compact('field', 'categories', 'attachedCategoryIds'));
    }

    /**
     * Update an existing field.
     */
    public function updateField(Request $request, ProfessionalFormField $field): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:150'],
            'field_type' => ['required', 'string', 'in:text,number,tel,email,combobox,textarea,checkbox_group,radio_group,select,url,image,file,multiple_images'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'help_text' => ['nullable', 'string', 'max:255'],
            'default_value' => ['nullable', 'string'],
            'options' => ['nullable', 'string'],
            'validation_rules' => ['nullable', 'string', 'max:255'],
            'width' => ['required', 'in:half,full'],
            'display_order' => ['nullable', 'integer'],
            'is_required' => ['nullable', 'boolean'],
            'is_common' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:professional_categories,id'],
        ]);

        $options = null;
        if (! empty($validated['options'])) {
            $raw = trim($validated['options']);
            if (str_starts_with($raw, '{') || str_starts_with($raw, '[')) {
                $options = json_decode($raw, true);
            } else {
                $lines = array_filter(array_map('trim', explode("\n", $raw)));
                $options = [];
                foreach ($lines as $line) {
                    if (str_contains($line, ':')) {
                        [$k, $v] = explode(':', $line, 2);
                        $options[trim($k)] = trim($v);
                    } else {
                        $slug = Str::slug($line);
                        $options[$slug] = $line;
                    }
                }
            }
        }

        $validationRules = null;
        if (! empty($validated['validation_rules'])) {
            $normalized = ProfileController::sanitizeValidationRules(
                $validated['validation_rules'],
                $request->boolean('is_required'),
                $validated['field_type']
            );
            $validationRules = implode('|', $normalized);
        }

        $field->update([
            'label' => trim($validated['label']),
            'field_type' => $validated['field_type'],
            'placeholder' => ! empty($validated['placeholder']) ? trim($validated['placeholder']) : null,
            'help_text' => ! empty($validated['help_text']) ? trim($validated['help_text']) : null,
            'default_value' => $validated['default_value'] ?? null,
            'options' => $options,
            'validation_rules' => $validationRules,
            'width' => $validated['width'],
            'display_order' => (int) ($validated['display_order'] ?? 50),
            'is_required' => $request->boolean('is_required'),
            'is_common' => $request->boolean('is_common'),
            'is_active' => $request->boolean('is_active'),
        ]);

        // Sync categories
        if (isset($validated['categories'])) {
            $currentCategoryIds = $field->categories()->pluck('professional_categories.id')->toArray();
            $newCategoryIds = $validated['categories'];

            // Detach removed
            $toDetach = array_diff($currentCategoryIds, $newCategoryIds);
            CategoryFormField::where('field_id', $field->id)->whereIn('category_id', $toDetach)->delete();

            // Attach added
            $toAttach = array_diff($newCategoryIds, $currentCategoryIds);
            foreach ($toAttach as $catId) {
                CategoryFormField::create([
                    'category_id' => $catId,
                    'field_id' => $field->id,
                    'display_order' => $field->display_order,
                    'is_required' => $field->is_required,
                    'is_active' => true,
                ]);
            }
        }

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'form_field_updated',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Updated form field: {$field->label} ({$field->field_key})",
            $request
        );

        return redirect()->route('admin.dynamic-forms.fields.index')
            ->with('success', "Field '{$field->label}' updated successfully.");
    }

    /**
     * Delete a custom field. Core common fields are protected.
     */
    public function destroyField(Request $request, ProfessionalFormField $field): RedirectResponse
    {
        $coreKeys = ['name', 'profession', 'place', 'experience', 'phone', 'email', 'about', 'service', 'outstation'];
        if (in_array($field->field_key, $coreKeys, true)) {
            return redirect()->back()->with('error', "Cannot delete system core field '{$field->label}'. You can deactivate it instead.");
        }

        $label = $field->label;
        CategoryFormField::where('field_id', $field->id)->delete();
        $field->delete();

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'form_field_deleted',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Deleted form field: {$label}",
            $request
        );

        return redirect()->route('admin.dynamic-forms.fields.index')
            ->with('success', "Field '{$label}' deleted successfully.");
    }

    /**
     * Toggle active status of a field.
     */
    public function toggleField(Request $request, ProfessionalFormField $field): RedirectResponse
    {
        $field->is_active = ! $field->is_active;
        $field->save();

        $statusLabel = $field->is_active ? 'activated' : 'deactivated';

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'form_field_toggled',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Toggled field {$field->label} to {$statusLabel}",
            $request
        );

        return redirect()->back()
            ->with('success', "Field '{$field->label}' has been {$statusLabel}.");
    }

    /**
     * Live form preview for a category.
     */
    public function preview(ProfessionalCategory $category): View
    {
        $fields = $category->getActiveFormFields();
        $profile = CreativeProfessional::where('category', $category->slug)->first();
        $user = auth('admin')->user() ?? auth()->user();

        return view('admin.dynamic-forms.preview', [
            'categoryModel' => $category,
            'fields' => $fields,
            'profile' => $profile,
            'user' => $user,
        ]);
    }

    /**
     * Quick status update / approval toggle on Creative Professionals.
     */
    public function toggleApproval(Request $request, CreativeProfessional $professional): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:published,approved,pending,rejected,draft,archived'],
        ]);

        $newStatus = ($validated['status'] === 'approved') ? 'published' : $validated['status'];
        $professional->status = $newStatus;
        $professional->save();

        if ($professional->category === 'fashion-designer' && $professional->user_id) {
            Designer::where('user_id', $professional->user_id)->update(['status' => $newStatus]);
        }

        $adminUser = auth('admin')->user() ?? auth()->user();
        AdminActivity::log(
            'professional_status_updated',
            $adminUser,
            $adminUser?->email ?? 'admin@idfr.test',
            "Changed status of {$professional->name} to {$newStatus}",
            $request
        );

        $msg = match ($newStatus) {
            'published' => "Professional '{$professional->name}' has been accepted and is now published on the main website.",
            'rejected' => "Professional '{$professional->name}' has been rejected and remains hidden from the main website.",
            'pending' => "Professional '{$professional->name}' is now Pending Approval.",
            default => "Professional '{$professional->name}' status updated to {$newStatus}.",
        };

        return redirect()->back()
            ->with('success', $msg);
    }
}
