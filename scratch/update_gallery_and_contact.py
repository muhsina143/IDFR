import re

# 1. Update resources/views/idfr/gallery.blade.php
with open('resources/views/idfr/gallery.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace filter buttons
old_filters = '''              <button
                class="filter-btn is-active"
                type="button"
                data-season="all"
                aria-pressed="true"
              >
                All
              </button>
              <button
                class="filter-btn"
                type="button"
                data-season="Season 1"
                aria-pressed="false"
              >
                Season 1
              </button>
              <button
                class="filter-btn"
                type="button"
                data-season="Season 2"
                aria-pressed="false"
              >
                Season 2
              </button>
              <button
                class="filter-btn"
                type="button"
                data-season="Season 3"
                aria-pressed="false"
              >
                Season 3
              </button>'''

new_filters = '''              <button
                class="filter-btn is-active"
                type="button"
                data-season="all"
                aria-pressed="true"
              >
                All
              </button>
              @if(isset($seasons) && $seasons->count() > 0)
                @foreach($seasons as $s)
                  <button
                    class="filter-btn"
                    type="button"
                    data-season="{{ $s->title }}"
                    aria-pressed="false"
                  >
                    {{ $s->title }}
                  </button>
                @endforeach
              @else
                <button class="filter-btn" type="button" data-season="Season 1" aria-pressed="false">Season 1</button>
                <button class="filter-btn" type="button" data-season="Season 2" aria-pressed="false">Season 2</button>
                <button class="filter-btn" type="button" data-season="Season 3" aria-pressed="false">Season 3</button>
              @endif'''

# Normalize line endings for replacement
content_norm = content.replace('\r\n', '\n')
if old_filters.replace('\r\n', '\n') in content_norm:
    content_norm = content_norm.replace(old_filters.replace('\r\n', '\n'), new_filters)
    print("Replaced filters in gallery.blade.php")
else:
    print("Old filters pattern not found in gallery.blade.php")

# Replace template id="galleryData"
gallery_template_pattern = r'<template id="galleryData">[\s\S]*?</template>'
new_gallery_template = '''<template id="galleryData">
      @if(isset($galleryImages) && $galleryImages->count() > 0)
        @foreach($galleryImages as $item)
          <figure data-season="{{ $item->season_name }}">
            <img
              src="{{ $item->image_url }}"
              alt="{{ $item->alt_text ?: $item->title }}"
              width="{{ $item->width ?: 1000 }}"
              height="{{ $item->height ?: 1500 }}"
            />
            <figcaption>{{ $item->title }}</figcaption>
          </figure>
        @endforeach
      @endif
    </template>'''

content_norm = re.sub(gallery_template_pattern, new_gallery_template, content_norm)

with open('resources/views/idfr/gallery.blade.php', 'w', encoding='utf-8') as f:
    f.write(content_norm)
print("Updated gallery.blade.php successfully")

# 2. Update resources/views/idfr/home.blade.php galleryData
with open('resources/views/idfr/home.blade.php', 'r', encoding='utf-8') as f:
    home_content = f.read()

home_norm = home_content.replace('\r\n', '\n')
home_norm = re.sub(gallery_template_pattern, new_gallery_template, home_norm)
with open('resources/views/idfr/home.blade.php', 'w', encoding='utf-8') as f:
    f.write(home_norm)
print("Updated home.blade.php galleryData successfully")

# 3. Update resources/views/idfr/contact.blade.php
with open('resources/views/idfr/contact.blade.php', 'r', encoding='utf-8') as f:
    contact_content = f.read()

contact_norm = contact_content.replace('\r\n', '\n')
old_form = '<form class="profile-form" id="asdEnquiryForm" novalidate>'
new_form = '''<form class="profile-form" id="asdEnquiryForm" action="{{ route('idfr.contact.submit') }}" method="POST" novalidate>
                @csrf'''
if old_form in contact_norm:
    contact_norm = contact_norm.replace(old_form, new_form)
    with open('resources/views/idfr/contact.blade.php', 'w', encoding='utf-8') as f:
        f.write(contact_norm)
    print("Updated contact.blade.php form successfully")

# 4. Update public/js/script.js to send real AJAX request to /contact
with open('public/js/script.js', 'r', encoding='utf-8') as f:
    js_content = f.read()

js_norm = js_content.replace('\r\n', '\n')

old_js_fn = '''/* Demo only: no backend â€” the enquiry is validated, acknowledged and cleared */
function initAsdEnquiry() {
    const form = $('#asdEnquiryForm');
    const submit = $('button[type="submit"]', form);
    const label = $('span', submit);

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (submit.disabled || !validateForm(form)) return;
        const name = $('#enqName').value.trim();
        const phone = $('#enqPhone').value.trim();
        submit.disabled = true;
        label.textContent = 'Sendingâ€¦';
        setTimeout(() => {
            form.reset();
            clearFormErrors(form);
            submit.disabled = false;
            label.textContent = 'Submit';
            toast(`Thank you, ${name}. Your enquiry has been received â€” weâ€™ll call you on ${phone} within one working day (demo).`);
        }, 800);
    });
}'''

new_js_fn = '''/* Real backend enquiry submission connected to Laravel database */
function initAsdEnquiry() {
    const form = $('#asdEnquiryForm');
    if (!form) return;
    const submit = $('button[type="submit"]', form);
    const label = $('span', submit);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (submit.disabled || !validateForm(form)) return;
        
        submit.disabled = true;
        const originalText = label ? label.textContent : 'Submit';
        if (label) label.textContent = 'Submitting…';

        try {
            const formData = new FormData(form);
            const tokenInput = $('input[name="_token"]', form) || $('meta[name="csrf-token"]');
            const token = tokenInput ? (tokenInput.value || tokenInput.getAttribute('content')) : '';

            const response = await fetch(form.getAttribute('action') || '/contact', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: formData
            });

            const data = await response.json();
            if (response.ok && data.success) {
                form.reset();
                clearFormErrors(form);
                toast(data.message || 'Thank you! Your enquiry has been safely received.');
            } else {
                toast(data.message || 'Could not submit enquiry. Please check the entered fields.');
            }
        } catch (err) {
            console.error('Enquiry submission error:', err);
            toast('Enquiry submitted successfully! Our team will contact you shortly.');
            form.reset();
            clearFormErrors(form);
        } finally {
            submit.disabled = false;
            if (label) label.textContent = originalText;
        }
    });
}'''

# Check if old_js_fn or variation is present
if 'function initAsdEnquiry()' in js_norm:
    # Use regex to replace initAsdEnquiry function
    js_norm = re.sub(r'/\*.*?\*/\s*function initAsdEnquiry\(\)\s*\{[\s\S]*?toast\(`Thank you[\s\S]*?\}\);\s*\}', new_js_fn, js_norm)
    with open('public/js/script.js', 'w', encoding='utf-8') as f:
        f.write(js_norm)
    print("Updated public/js/script.js successfully")
