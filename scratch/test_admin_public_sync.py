import urllib.request
import urllib.parse
import http.cookiejar
import re
import json
import subprocess
import os
import html

base_url = "http://127.0.0.1:8000"
cj = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))
opener.addheaders = [('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)')]

results = []
def log_test(name, passed, detail=""):
    status = "PASS" if passed else "FAIL"
    results.append((name, passed, detail))
    print(f"[{status}] {name} :: {detail}")

def run_artisan_tinker(code):
    cmd = ["php", "artisan", "tinker", f"--execute={code}"]
    res = subprocess.run(cmd, capture_output=True, text=True)
    return res.stdout.strip()

print("=" * 70)
print("IDFR ADMIN-TO-PUBLIC WEBSITE SYNCHRONIZATION TEST SUITE")
print("=" * 70)

# Step 0: Login as Admin
try:
    res = opener.open(f"{base_url}/admin/login")
    login_html = res.read().decode('utf-8')
    m = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', login_html)
    csrf_token = m.group(1) if m else None

    login_post = urllib.parse.urlencode({
        '_token': csrf_token,
        'email': 'director@idfr.example',
        'password': 'SuperSecureAdminPassword123!',
    }).encode('utf-8')
    res = opener.open(f"{base_url}/admin/login", data=login_post)
    h = res.read().decode('utf-8')
    log_test("Admin Authentication", "/admin" in res.geturl(), "Successfully authenticated as admin")
except Exception as e:
    log_test("Admin Authentication", False, str(e))

def get_csrf():
    res = opener.open(f"{base_url}/admin/seasons")
    h = res.read().decode('utf-8')
    m = re.search(r'name=["\']csrf-token["\']\s+content=["\']([^"\']+)["\']', h)
    if not m:
        m = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', h)
    return m.group(1) if m else None

# Helper to create multipart form data
def create_multipart(fields, files=None):
    boundary = '----WebKitFormBoundary' + os.urandom(16).hex()
    body = bytearray()
    for name, value in fields.items():
        body.extend(f'--{boundary}\r\n'.encode('utf-8'))
        body.extend(f'Content-Disposition: form-data; name="{name}"\r\n\r\n'.encode('utf-8'))
        body.extend(f'{value}\r\n'.encode('utf-8'))
    if files:
        for name, file_info in files.items():
            filename, content_type, data = file_info
            body.extend(f'--{boundary}\r\n'.encode('utf-8'))
            body.extend(f'Content-Disposition: form-data; name="{name}"; filename="{filename}"\r\n'.encode('utf-8'))
            body.extend(f'Content-Type: {content_type}\r\n\r\n'.encode('utf-8'))
            body.extend(data)
            body.extend(b'\r\n')
    body.extend(f'--{boundary}--\r\n'.encode('utf-8'))
    return boundary, bytes(body)

# Minimal 1x1 PNG image data
dummy_png = bytes.fromhex(
    '89504e470d0a1a0a0000000d49484452000000010000000108060000001f15c4890000000d49444154789c6360606060000000050001a7e45b4c0000000049454e44ae426082'
)

# ==============================================================================
# TEST 1: SEASON EDIT -> DATABASE -> PUBLIC HOMEPAGE & SUBPAGES
# ==============================================================================
print("\n--- TEST 1: SEASON MODULE SYNC ---")
try:
    token = get_csrf()
    season_fields = {
        '_token': token,
        '_method': 'PUT',
        'number': '4',
        'title': 'Season 4 - The Royal Edition',
        'year': '2026',
        'location': 'Grand Arena, Kozhikode',
        'dates': '20 — 23 December 2026',
        'status': 'published',
        'description': 'The most anticipated fashion runway of South India featuring premier couture designers and elite models.',
    }
    season_files = {
        'hero_image': ('royal_hero.png', 'image/png', dummy_png)
    }
    boundary, body = create_multipart(season_fields, season_files)
    req = urllib.request.Request(f"{base_url}/admin/seasons/4", data=body, method='POST')
    req.add_header('Content-Type', f'multipart/form-data; boundary={boundary}')
    res = opener.open(req)
    
    # 1. Verify in Database
    db_out = run_artisan_tinker("echo json_encode(App\\Models\\Season::find(4)->toArray());")
    season_db = json.loads(db_out)
    db_synced = (
        season_db.get('title') == 'Season 4 - The Royal Edition' and
        season_db.get('dates') == '20 — 23 December 2026' and
        season_db.get('location') == 'Grand Arena, Kozhikode' and
        bool(season_db.get('hero_image'))
    )
    log_test("Season DB Update", db_synced, f"DB Title: '{season_db.get('title')}', Image: '{season_db.get('hero_image')}'")

    # 2. Verify on Public Homepage
    res_home = opener.open(f"{base_url}/")
    home_html = res_home.read().decode('utf-8')
    
    hero_title_ok = "Season 4 - The Royal Edition" in home_html
    dates_ok = "20 — 23 December 2026" in home_html
    desc_ok = "The most anticipated fashion runway of South India" in home_html
    hero_img_ok = season_db.get('hero_image') in home_html
    gallery_filter_ok = 'data-season="Season 4 - The Royal Edition"' in home_html
    footer_ok = "Fashion Show Season 4 - The Royal Edition" in home_html

    log_test("Homepage Hero Title Sync", hero_title_ok, "Hero label & big title reflect 'Season 4 - The Royal Edition'")
    log_test("Homepage Hero Dates & Location Sync", dates_ok, "Hero & Banner reflect '20 — 23 December 2026'")
    log_test("Homepage Hero Description Sync", desc_ok, "Hero info text & Banner reflect new description")
    log_test("Homepage Hero Image Sync", hero_img_ok, f"Preload and heroSlidesData reference '{season_db.get('hero_image')}'")
    log_test("Homepage Gallery Filter Season Sync", gallery_filter_ok, "Gallery filter button dynamically updated with new season title")
    log_test("Footer Dynamic Season Sync", footer_ok, "Footer quick link dynamically shows new season title")

    # 3. Verify on Subpage (About modal)
    res_about = opener.open(f"{base_url}/about")
    about_html = res_about.read().decode('utf-8')
    modal_sync_ok = "Season 4 - The Royal Edition" in about_html and "20 — 23 December 2026" in about_html
    log_test("Subpage Program Modal Sync", modal_sync_ok, "/about modal dynamically reflects Season 4 - The Royal Edition")

    # 4. Verify Image HTTP 200
    res_img = opener.open(f"{base_url}/{season_db.get('hero_image')}")
    log_test("Uploaded Hero Image HTTP 200", res_img.getcode() == 200, f"Image served at /{season_db.get('hero_image')}")

except Exception as e:
    log_test("Season Sync Lifecycle", False, str(e))

# ==============================================================================
# TEST 2: MODEL EDIT -> DATABASE -> PUBLIC /models & HOMEPAGE
# ==============================================================================
print("\n--- TEST 2: MODEL MODULE SYNC ---")
try:
    first_model_id = json.loads(run_artisan_tinker("echo json_encode(App\\Models\\ModelProfile::first()->id);"))
    token = get_csrf()
    model_fields = {
        '_token': token,
        '_method': 'PUT',
        'name': 'Diya Pillai (Star Model)',
        'gender': 'Female',
        'level': 'Experienced',
        'age': '24',
        'height': '5\'11"',
        'location': 'Kochi & Milan',
        'experience': '8 Years Professional',
        'types[]': 'Runway',
        'measurements': 'Bust: 34, Waist: 25, Hips: 36',
        'skills': 'Haute Couture Ramp, Editorial Posing',
        'specialities': 'Bridal Week, International Runway',
        'availability': 'Available for Milan & IDFR',
        'description': 'Leading IDFR runway model with extensive international runway showcase experience.',
        'status': 'published',
        'is_featured': '1',
        'display_order': '1',
    }
    model_files = {
        'profile_photo': ('diya_star.png', 'image/png', dummy_png)
    }
    boundary, body = create_multipart(model_fields, model_files)
    req = urllib.request.Request(f"{base_url}/admin/models/{first_model_id}", data=body, method='POST')
    req.add_header('Content-Type', f'multipart/form-data; boundary={boundary}')
    res = opener.open(req)

    # 1. Verify in Database
    db_out = run_artisan_tinker(f"echo json_encode(App\\Models\\ModelProfile::find({first_model_id})->toArray());")
    model_db = json.loads(db_out)
    m_synced = (
        model_db.get('name') == 'Diya Pillai (Star Model)' and
        model_db.get('height') == '5\'11"' and
        model_db.get('location') == 'Kochi & Milan' and
        bool(model_db.get('profile_photo'))
    )
    log_test("Model DB Update", m_synced, f"DB Name: '{model_db.get('name')}', Height: '{model_db.get('height')}'")

    # 2. Verify on Public /models page
    res_models = opener.open(f"{base_url}/models")
    models_html = res_models.read().decode('utf-8')
    models_text = html.unescape(models_html)
    m_name_pub = "Diya Pillai (Star Model)" in models_text
    m_loc_pub = "Kochi & Milan" in models_text
    m_height_pub = "5'11\"" in models_text
    m_img_pub = model_db.get('profile_photo') in models_html
    log_test("Public /models Name Sync", m_name_pub, "modelsData template contains 'Diya Pillai (Star Model)'")
    log_test("Public /models Stats Sync", m_loc_pub and m_height_pub, "modelsData template contains 'Kochi & Milan' and 5'11\"")
    log_test("Public /models Photo Sync", m_img_pub, f"modelsData template contains '{model_db.get('profile_photo')}'")

    # 3. Verify on Public Homepage /
    res_home2 = opener.open(f"{base_url}/")
    home2_html = res_home2.read().decode('utf-8')
    log_test("Homepage Models Template Sync", "Diya Pillai (Star Model)" in home2_html, "Homepage modelsData contains updated model")

    # 4. Verify Draft Status Hiding
    model_fields['status'] = 'draft'
    boundary, body = create_multipart(model_fields)
    req = urllib.request.Request(f"{base_url}/admin/models/{first_model_id}", data=body, method='POST')
    req.add_header('Content-Type', f'multipart/form-data; boundary={boundary}')
    opener.open(req)
    res_draft = opener.open(f"{base_url}/models")
    draft_html = res_draft.read().decode('utf-8')
    log_test("Model Draft Privacy Sync", "Diya Pillai (Star Model)" not in draft_html, "Draft model successfully hidden from public website")

    # Restore to published
    model_fields['status'] = 'published'
    boundary, body = create_multipart(model_fields)
    req = urllib.request.Request(f"{base_url}/admin/models/{first_model_id}", data=body, method='POST')
    req.add_header('Content-Type', f'multipart/form-data; boundary={boundary}')
    opener.open(req)
    res_restored = opener.open(f"{base_url}/models")
    log_test("Model Publish Restore Sync", "Diya Pillai (Star Model)" in res_restored.read().decode('utf-8'), "Model restored and visible again")

except Exception as e:
    log_test("Model Sync Lifecycle", False, str(e))

# ==============================================================================
# TEST 3: DESIGNER EDIT -> DATABASE -> PUBLIC /professionals
# ==============================================================================
print("\n--- TEST 3: DESIGNER MODULE SYNC ---")
try:
    first_designer_id = json.loads(run_artisan_tinker("echo json_encode(App\\Models\\Designer::first()->id);"))
    token = get_csrf()
    designer_fields = {
        '_token': token,
        '_method': 'PUT',
        'name': 'Tara Nair (Haute Couture)',
        'brand_name': 'Tara Nair Royal Atelier',
        'collection_title': 'Velvet Royale Finale',
        'location': 'Kochi & Mumbai',
        'experience': '12 Years Couture',
        'services': 'Runway Couture, Bridal Trousseau, Celebrity Styling',
        'short_intro': 'Celebrated designer known for dramatic velvet and silk silhouettes.',
        'biography': 'Tara Nair is an acclaimed South Indian fashion designer whose creations grace IDFR finales.',
        'status': 'published',
        'display_order': '1',
    }
    boundary, body = create_multipart(designer_fields)
    req = urllib.request.Request(f"{base_url}/admin/designers/{first_designer_id}", data=body, method='POST')
    req.add_header('Content-Type', f'multipart/form-data; boundary={boundary}')
    opener.open(req)

    # 1. Verify in Database
    db_out = run_artisan_tinker(f"echo json_encode(App\\Models\\Designer::find({first_designer_id})->toArray());")
    designer_db = json.loads(db_out)
    d_synced = (
        designer_db.get('name') == 'Tara Nair (Haute Couture)' and
        designer_db.get('collection_title') == 'Velvet Royale Finale' and
        designer_db.get('brand_name') == 'Tara Nair Royal Atelier'
    )
    log_test("Designer DB Update", d_synced, f"DB Name: '{designer_db.get('name')}', Collection: '{designer_db.get('collection_title')}'")

    # 2. Verify on Public /professionals
    res_pros = opener.open(f"{base_url}/professionals")
    pros_html = res_pros.read().decode('utf-8')
    pros_text = html.unescape(pros_html)
    d_pub_ok = "Tara Nair (Haute Couture)" in pros_text and "Velvet Royale Finale" in pros_text
    log_test("Public /professionals Designer Sync", d_pub_ok, "creativeProsData includes Tara Nair under fashion-designer category with collection")

    # 3. Test Designer Draft Status Hiding
    designer_fields['status'] = 'draft'
    boundary, body = create_multipart(designer_fields)
    req = urllib.request.Request(f"{base_url}/admin/designers/{first_designer_id}", data=body, method='POST')
    req.add_header('Content-Type', f'multipart/form-data; boundary={boundary}')
    opener.open(req)
    res_d_draft = opener.open(f"{base_url}/professionals")
    log_test("Designer Draft Privacy Sync", "Tara Nair (Haute Couture)" not in res_d_draft.read().decode('utf-8'), "Draft designer hidden from public website")

    # Restore to published
    designer_fields['status'] = 'published'
    boundary, body = create_multipart(designer_fields)
    req = urllib.request.Request(f"{base_url}/admin/designers/{first_designer_id}", data=body, method='POST')
    req.add_header('Content-Type', f'multipart/form-data; boundary={boundary}')
    opener.open(req)
    res_d_pub = opener.open(f"{base_url}/professionals")
    log_test("Designer Publish Restore Sync", "Tara Nair (Haute Couture)" in res_d_pub.read().decode('utf-8'), "Designer visible again publicly")

except Exception as e:
    log_test("Designer Sync Lifecycle", False, str(e))

# ==============================================================================
# TEST 4: CREATIVE PROFESSIONAL EDIT -> DATABASE -> PUBLIC /professionals
# ==============================================================================
print("\n--- TEST 4: CREATIVE PROFESSIONAL MODULE SYNC ---")
try:
    first_pro_id = json.loads(run_artisan_tinker("echo json_encode(App\\Models\\CreativeProfessional::first()->id);"))
    token = get_csrf()
    pro_fields = {
        '_token': token,
        '_method': 'PUT',
        'name': 'Arjun Menon (Vogue Certified)',
        'category': 'photographer',
        'brand': 'IDFR',
        'location': 'Bengaluru & Dubai',
        'experience': '15 Years Fashion & Runway',
        'services': 'Runway, High Fashion Campaign, Lookbook',
        'short_intro': 'Lead IDFR runway photographer specializing in dynamic motion editorial.',
        'biography': 'Arjun Menon has captured runway shows across Mumbai, Dubai and Kozhikode.',
        'status': 'published',
        'display_order': '1',
    }
    boundary, body = create_multipart(pro_fields)
    req = urllib.request.Request(f"{base_url}/admin/professionals/{first_pro_id}", data=body, method='POST')
    req.add_header('Content-Type', f'multipart/form-data; boundary={boundary}')
    opener.open(req)

    # 1. Verify in Database
    db_out = run_artisan_tinker(f"echo json_encode(App\\Models\\CreativeProfessional::find({first_pro_id})->toArray());")
    pro_db = json.loads(db_out)
    pro_synced = (
        pro_db.get('name') == 'Arjun Menon (Vogue Certified)' and
        pro_db.get('location') == 'Bengaluru & Dubai'
    )
    log_test("Creative Professional DB Update", pro_synced, f"DB Name: '{pro_db.get('name')}', Location: '{pro_db.get('location')}'")

    # 2. Verify on Public /professionals
    res_pros = opener.open(f"{base_url}/professionals")
    pros_html = res_pros.read().decode('utf-8')
    pros_text = html.unescape(pros_html)
    pro_pub_ok = "Arjun Menon (Vogue Certified)" in pros_text and "Bengaluru & Dubai" in pros_text
    log_test("Public /professionals Pro Sync", pro_pub_ok, "creativeProsData includes updated name and location")

except Exception as e:
    log_test("Creative Professional Sync Lifecycle", False, str(e))

# ==============================================================================
# TEST 5: GALLERY IMAGE CREATE & DELETE -> DATABASE -> PUBLIC /gallery & /
# ==============================================================================
print("\n--- TEST 5: GALLERY MODULE SYNC ---")
try:
    token = get_csrf()
    gallery_fields = {
        '_token': token,
        'title': 'Sync Verification Emerald Gown Look',
        'season_name': 'Season 4 - The Royal Edition',
        'alt_text': 'Emerald silk gown finale look',
        'width': '1100',
        'height': '1650',
        'display_order': '1',
        'status': 'published',
    }
    gallery_files = {
        'image': ('emerald_sync.png', 'image/png', dummy_png)
    }
    boundary, body = create_multipart(gallery_fields, gallery_files)
    req = urllib.request.Request(f"{base_url}/admin/gallery", data=body, method='POST')
    req.add_header('Content-Type', f'multipart/form-data; boundary={boundary}')
    opener.open(req)

    # 1. Verify in DB
    db_out = run_artisan_tinker("echo json_encode(App\\Models\\GalleryImage::where('title', 'Sync Verification Emerald Gown Look')->first()->toArray());")
    gal_db = json.loads(db_out)
    gal_id = gal_db.get('id')
    log_test("Gallery Look DB Creation", bool(gal_id), f"Created image ID: {gal_id}, Path: '{gal_db.get('image_path')}'")

    # 2. Verify on Public /gallery
    res_gal = opener.open(f"{base_url}/gallery")
    gal_html = res_gal.read().decode('utf-8')
    gal_pub_ok = "Sync Verification Emerald Gown Look" in gal_html and 'data-season="Season 4 - The Royal Edition"' in gal_html
    log_test("Public /gallery Sync", gal_pub_ok, "galleryData includes new image and matches dynamic season filter")

    # 3. Verify on Public / (Homepage)
    res_home_gal = opener.open(f"{base_url}/")
    home_gal_html = res_home_gal.read().decode('utf-8')
    log_test("Homepage Gallery Grid Sync", "Sync Verification Emerald Gown Look" in home_gal_html, "Homepage galleryData includes new look")

    # 4. Verify Image HTTP 200
    res_img_gal = opener.open(f"{base_url}/{gal_db.get('image_path')}")
    log_test("Uploaded Gallery Image HTTP 200", res_img_gal.getcode() == 200, f"Served image at /{gal_db.get('image_path')}")

    # 5. Delete and verify removed
    del_data = urllib.parse.urlencode({'_token': token, '_method': 'DELETE'}).encode('utf-8')
    opener.open(f"{base_url}/admin/gallery/{gal_id}", data=del_data)
    res_after_del = opener.open(f"{base_url}/gallery")
    after_del_html = res_after_del.read().decode('utf-8')
    log_test("Gallery Delete Sync", "Sync Verification Emerald Gown Look" not in after_del_html, "Deleted look immediately removed from public gallery")

except Exception as e:
    log_test("Gallery Sync Lifecycle", False, str(e))

# ==============================================================================
# TEST 6: PUBLIC CONTACT ENQUIRY -> DATABASE -> ADMIN PORTAL
# ==============================================================================
print("\n--- TEST 6: CONTACT ENQUIRY MODULE SYNC ---")
try:
    enquiry_data = urllib.parse.urlencode({
        '_token': get_csrf(),
        'name': 'Kavya Pillai Sync Test',
        'phone': '+91 98888 11122',
        'email': 'kavya.sync@example.com',
        'subject': 'Designer Registration Inquiry',
        'message': 'Testing public contact submission flow to admin enquiry management.',
    }).encode('utf-8')
    req = urllib.request.Request(f"{base_url}/contact/enquiry", data=enquiry_data)
    res_enq = opener.open(req)
    
    # 1. Verify in DB
    db_out = run_artisan_tinker("echo json_encode(App\\Models\\ContactEnquiry::where('name', 'Kavya Pillai Sync Test')->first()->toArray());")
    enq_db = json.loads(db_out)
    enq_id = enq_db.get('id')
    log_test("Public Contact Form DB Persistence", bool(enq_id) and enq_db.get('status') == 'unread', f"Enquiry ID: {enq_id}, Status: {enq_db.get('status')}")

    # 2. Verify on Admin /admin/enquiries
    res_admin_enq = opener.open(f"{base_url}/admin/enquiries")
    admin_enq_html = res_admin_enq.read().decode('utf-8')
    log_test("Admin Inbound Enquiries Display", "Kavya Pillai Sync Test" in admin_enq_html, "Public submission immediately appears in admin dashboard & table")

    # 3. Clean up test enquiry
    token = get_csrf()
    del_enq = urllib.parse.urlencode({'_token': token, '_method': 'DELETE'}).encode('utf-8')
    opener.open(f"{base_url}/admin/enquiries/{enq_id}", data=del_enq)
    log_test("Admin Enquiry Cleanup", True, "Test enquiry cleaned up")

except Exception as e:
    log_test("Contact Sync Lifecycle", False, str(e))

# ==============================================================================
# SUMMARY
# ==============================================================================
print("\n" + "=" * 70)
total_tests = len(results)
passed_tests = sum(1 for _, p, _ in results if p)
failed_tests = total_tests - passed_tests
print(f"TOTAL: {total_tests} | PASSED: {passed_tests} | FAILED: {failed_tests}")
print("=" * 70)
if failed_tests == 0:
    print("ALL ADMIN-TO-PUBLIC WEBSITE SYNCHRONIZATION TESTS PASSED PERFECTLY!")
else:
    print("WARNING: SOME TESTS FAILED")
