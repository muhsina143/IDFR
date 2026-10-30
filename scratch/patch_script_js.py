with open('public/js/script.js', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Update DATA.models
old_models = """    models: tplItems('modelsData').map((a) => {
        const m = {
            id: Number(a.dataset.id), name: textOf(a, 'h3'), gender: a.dataset.gender, level: a.dataset.level, types: listOf(a, '[data-types]'),
            age: Number(textOf(a, '[data-age]')), height: textOf(a, '[data-height]'), location: textOf(a, '[data-location]'),
            experience: textOf(a, '[data-experience]'), premium: a.dataset.premium === 'true',
            image: srcOf(a),
            portfolio: $$('[data-portfolio] img', a).map((img) => img.getAttribute('src')),
            measurements: Object.fromEntries($$('[data-measurements] > dt', a).map((dt) => [dt.textContent, dt.nextElementSibling.textContent])),
            skills: listOf(a, '[data-skills]'),
            specialities: listOf(a, '[data-specialities]'),
            availability: textOf(a, '[data-availability]'),
            description: textOf(a, '[data-description]')
        };
        if (a.dataset.kid === 'true') m.kid = true;
        return m;
    }),"""

new_models = """    models: tplItems('modelsData').map((a) => {
        const id = Number(a.dataset?.id);
        const m = {
            id,
            name: textOf(a, 'h3'),
            gender: a.dataset?.gender || 'Female',
            level: a.dataset?.level || 'New Face',
            types: listOf(a, '[data-types]'),
            age: Number(textOf(a, '[data-age]')) || 0,
            height: textOf(a, '[data-height]'),
            location: textOf(a, '[data-location]'),
            experience: textOf(a, '[data-experience]'),
            premium: a.dataset?.premium === 'true',
            image: srcOf(a),
            portfolio: $$('[data-portfolio] img', a).map((img) => img.getAttribute('src') || '').filter(Boolean),
            measurements: Object.fromEntries(
                $$('[data-measurements] > dt', a)
                    .map((dt) => [dt.textContent?.trim() || '', dt.nextElementSibling?.textContent?.trim() || ''])
                    .filter(([k]) => k.length > 0)
            ),
            skills: listOf(a, '[data-skills]'),
            specialities: listOf(a, '[data-specialities]'),
            availability: textOf(a, '[data-availability]'),
            description: textOf(a, '[data-description]')
        };
        if (a.dataset?.kid === 'true') m.kid = true;
        return m;
    }),"""

# Normalize CRLF for matching
content_normalized = content.replace('\r\n', '\n')
assert old_models in content_normalized, "old_models not found"
content_normalized = content_normalized.replace(old_models, new_models)

# 2. Update gallery
old_gallery = """    gallery: tplItems('galleryData').map((f) => {
        const img = $('img', f);
        const [w, h] = sizeOf(img);
        return { season: f.dataset.season, image: img.getAttribute('src'), title: textOf(f, 'figcaption'), w, h, alt: img.getAttribute('alt') };
    }),"""

new_gallery = """    gallery: tplItems('galleryData').map((f) => {
        const img = $('img', f);
        const [w, h] = sizeOf(img);
        return {
            season: f.dataset?.season || '',
            image: img?.getAttribute('src') || '',
            title: textOf(f, 'figcaption'),
            w,
            h,
            alt: img?.getAttribute('alt') || ''
        };
    }),"""

assert old_gallery in content_normalized, "old_gallery not found"
content_normalized = content_normalized.replace(old_gallery, new_gallery)

# 3. Update places
old_places = "    places: tplItems('placesData').map((o) => o.textContent),"
new_places = "    places: tplItems('placesData').map((o) => o.textContent?.trim() || ''),"
assert old_places in content_normalized, "old_places not found"
content_normalized = content_normalized.replace(old_places, new_places)

# 4. Update asdHero
old_asdHero = """    asdHero: tplItems('asdHeroData').map((f) => ({
        image: srcOf(f), title: textOf(f, 'figcaption'), meta: f.dataset.meta, alt: $('img', f).getAttribute('alt')
    })),"""

new_asdHero = """    asdHero: tplItems('asdHeroData').map((f) => ({
        image: srcOf(f),
        title: textOf(f, 'figcaption'),
        meta: f.dataset?.meta || '',
        alt: $('img', f)?.getAttribute('alt') || ''
    })),"""

assert old_asdHero in content_normalized, "old_asdHero not found"
content_normalized = content_normalized.replace(old_asdHero, new_asdHero)

# 5. Update asdCategories
old_asdCategories = """    asdCategories: tplItems('asdCategoriesData').map((s) => {
        const c = { id: s.dataset.id, name: textOf(s, 'h3') };
        if (s.dataset.tab) c.tab = s.dataset.tab;
        c.photos = $$('figure', s).map((f) => { const img = $('img', f); return [img.getAttribute('src'), textOf(f, 'figcaption'), ...sizeOf(img)]; });
        return c;
    }),"""

new_asdCategories = """    asdCategories: tplItems('asdCategoriesData').map((s) => {
        const c = { id: s.dataset?.id || '', name: textOf(s, 'h3') };
        if (s.dataset?.tab) c.tab = s.dataset.tab;
        c.photos = $$('figure', s).map((f) => {
            const img = $('img', f);
            return [img?.getAttribute('src') || '', textOf(f, 'figcaption'), ...sizeOf(img)];
        });
        return c;
    }),"""

assert old_asdCategories in content_normalized, "old_asdCategories not found"
content_normalized = content_normalized.replace(old_asdCategories, new_asdCategories)

# 6. Update creativePros
old_creativePros = """    creativePros: tplItems('creativeProsData').map((a) => ({
        id: a.dataset.id, category: a.dataset.category, brand: a.dataset.brand,
        name: textOf(a, 'h3'), image: srcOf(a), alt: $(':scope > img', a).getAttribute('alt'),
        location: textOf(a, '[data-location]'), experience: textOf(a, '[data-experience]'),
        bio: textOf(a, '[data-bio]'), services: listOf(a, '[data-services]'),
        portfolio: $$('[data-portfolio] figure', a).map((f) => {
            const img = $('img', f);
            const [w, h] = sizeOf(img);
            return { src: img.getAttribute('src'), alt: img.getAttribute('alt'), caption: textOf(f, 'figcaption'), w, h };
        })
    }))"""

new_creativePros = """    creativePros: tplItems('creativeProsData').map((a) => ({
        id: a.dataset?.id || '',
        category: a.dataset?.category || '',
        brand: a.dataset?.brand || '',
        name: textOf(a, 'h3'),
        image: srcOf(a),
        alt: ($(':scope > img', a) || $('img', a))?.getAttribute('alt') || '',
        location: textOf(a, '[data-location]'),
        experience: textOf(a, '[data-experience]'),
        bio: textOf(a, '[data-bio]'),
        services: listOf(a, '[data-services]'),
        portfolio: $$('[data-portfolio] figure', a).map((f) => {
            const img = $('img', f);
            const [w, h] = sizeOf(img);
            return {
                src: img?.getAttribute('src') || '',
                alt: img?.getAttribute('alt') || '',
                caption: textOf(f, 'figcaption'),
                w,
                h
            };
        })
    }))"""

assert old_creativePros in content_normalized, "old_creativePros not found"
content_normalized = content_normalized.replace(old_creativePros, new_creativePros)

# 7. Update MODEL_FILTERS
old_filters = """const MODEL_FILTERS = {
    all: () => true,
    female: (m) => m.gender === 'Female',
    male: (m) => m.gender === 'Male',
    new: (m) => m.level === 'New Face',
    experienced: (m) => m.level === 'Experienced',
    runway: (m) => m.types.includes('Runway'),
    editorial: (m) => m.types.includes('Editorial'),
    commercial: (m) => m.types.includes('Commercial'),
    kids: (m) => m.kid === true
};"""

new_filters = """const MODEL_FILTERS = {
    all: () => true,
    female: (m) => m.gender === 'Female',
    male: (m) => m.gender === 'Male',
    new: (m) => m.level === 'New Face',
    experienced: (m) => m.level === 'Experienced',
    runway: (m) => (m.types || []).some((t) => t.toLowerCase() === 'runway'),
    editorial: (m) => (m.types || []).some((t) => t.toLowerCase() === 'editorial'),
    commercial: (m) => (m.types || []).some((t) => t.toLowerCase() === 'commercial'),
    kids: (m) => m.kid === true
};"""

assert old_filters in content_normalized, "old_filters not found"
content_normalized = content_normalized.replace(old_filters, new_filters)

# 8. Guard types.join in modelCardHTML
old_card_types = "    const types = m.types.join(' / ');"
new_card_types = "    const types = (m.types || []).join(' / ');"
assert old_card_types in content_normalized, "old_card_types not found"
content_normalized = content_normalized.replace(old_card_types, new_card_types)

# 9. Guard #year at end of script
old_year = "    $('#year').textContent = String(new Date().getFullYear());"
new_year = """    const yearEl = $('#year');
    if (yearEl) yearEl.textContent = String(new Date().getFullYear());"""
assert old_year in content_normalized, "old_year not found"
content_normalized = content_normalized.replace(old_year, new_year)

# Write back preserving CRLF
content_final = content_normalized.replace('\n', '\r\n')
with open('public/js/script.js', 'w', encoding='utf-8') as f:
    f.write(content_final)

# Also update IDFR_LATEST/js/script.js if present
try:
    with open('IDFR_LATEST/js/script.js', 'r', encoding='utf-8') as f:
        idfr_content = f.read()
    idfr_norm = idfr_content.replace('\r\n', '\n')
    # Update textOf in IDFR_LATEST/js/script.js
    old_textOf_static = "const textOf = (el, sel) => $(sel, el).textContent;"
    new_textOf_static = """const textOf = (el, sel) => {
    if (!el) return '';
    const target = sel ? $(sel, el) : el;
    return target?.textContent?.trim() ?? '';
};"""
    if old_textOf_static in idfr_norm:
        idfr_norm = idfr_norm.replace(old_textOf_static, new_textOf_static)
        with open('IDFR_LATEST/js/script.js', 'w', encoding='utf-8') as f:
            f.write(idfr_norm.replace('\n', '\r\n'))
        print("Updated IDFR_LATEST/js/script.js as well")
except Exception as e:
    print("Could not update IDFR_LATEST/js/script.js:", e)

print("Patch applied successfully to public/js/script.js")

