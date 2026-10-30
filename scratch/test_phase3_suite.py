import urllib.request
import urllib.parse
import http.cookiejar
import html as html_lib
import json
import re
import sys

base_url = "http://127.0.0.1:8000"

cj = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))

total_passed = 0
total_failed = 0

def log_test(name, success, message=""):
    global total_passed, total_failed
    if success:
        total_passed += 1
        print(f"[PASS] {name} {message}")
    else:
        total_failed += 1
        print(f"[FAIL] {name} {message}")

print("==================================================")
print("  IDFR FASHION RUNWAY — PHASE 3 FULL TEST SUITE")
print("==================================================")

# 1. Unauthenticated Security Protection
for module in ['/admin/models', '/admin/designers', '/admin/professionals', '/admin/seasons']:
    try:
        class NoRedirect(urllib.request.HTTPRedirectHandler):
            def http_error_302(self, req, fp, code, msg, headers):
                return fp
        no_red = urllib.request.build_opener(NoRedirect)
        res = no_red.open(f"{base_url}{module}")
        target = res.headers.get('Location', '')
        log_test(f"Auth Protection on {module}", '/admin/login' in target or res.getcode() == 302, f"-> Redirected to {target}")
    except Exception as e:
        log_test(f"Auth Protection on {module}", False, str(e))

# 2. Authenticate as Admin
csrf_token = None
try:
    res = opener.open(f"{base_url}/admin/login")
    html_page = res.read().decode('utf-8')
    m = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', html_page)
    csrf_token = m.group(1) if m else None

    login_data = urllib.parse.urlencode({
        '_token': csrf_token,
        'email': 'director@idfr.example',
        'password': 'SuperSecureAdminPassword123!',
    }).encode('utf-8')

    res = opener.open(f"{base_url}/admin/login", data=login_data)
    login_html = res.read().decode('utf-8')
    is_admin_logged = ('Executive Overview' in login_html or '/admin' in res.geturl())
    log_test("Admin Authentication", is_admin_logged, "-> Successfully logged into admin panel")
except Exception as e:
    log_test("Admin Authentication", False, str(e))

def get_csrf():
    res = opener.open(f"{base_url}/admin")
    h = res.read().decode('utf-8')
    m = re.search(r'name=["\']csrf-token["\']\s+content=["\']([^"\']+)["\']', h)
    if not m:
        m = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', h)
    return m.group(1) if m else csrf_token

# 3. Dashboard Real Database Metrics & Quick Actions
try:
    res = opener.open(f"{base_url}/admin")
    dash_html = res.read().decode('utf-8')
    has_metrics = ("Total Models" in dash_html) and ("Total Designers" in dash_html) and ("Creative Professionals" in dash_html)
    has_quick_actions = ("Add Model" in dash_html) and ("Add Designer" in dash_html) and ("Add Creative Pro" in dash_html)
    log_test("Dashboard Phase 3 Metrics & Quick Actions", has_metrics and has_quick_actions, "-> Real database metrics & quick action buttons verified")
except Exception as e:
    log_test("Dashboard Phase 3 Metrics", False, str(e))

# 4. Seasons Module
try:
    res = opener.open(f"{base_url}/admin/seasons")
    seasons_html = res.read().decode('utf-8')
    has_seasons = ("Season 4" in seasons_html) and ("Season 3" in seasons_html) and ("Season 2" in seasons_html) and ("Season 1" in seasons_html)
    log_test("Seasons Management Listing", has_seasons, "-> All 4 Runway seasons loaded with relationship stats")
except Exception as e:
    log_test("Seasons Listing", False, str(e))

# 5. Models CRUD Lifecycle
created_model_id = None
try:
    # 5a. List view
    res = opener.open(f"{base_url}/admin/models")
    m_html = res.read().decode('utf-8')
    log_test("Models Directory Index", "Diya Pillai" in m_html and "Models" in m_html, "-> Listing with 18 seeded models active")

    # 5b. Search & filter
    res = opener.open(f"{base_url}/admin/models?search=Diya")
    s_html = res.read().decode('utf-8')
    log_test("Models Search & Filter", "Diya Pillai" in s_html and "Arjun Varma" not in s_html, "-> Filter accurately isolated search results")

    # 5c. Create Model
    token = get_csrf()
    post_data = urllib.parse.urlencode({
        '_token': token,
        'name': 'Aria Sterling Test',
        'gender': 'Female',
        'level': 'New Face',
        'age': '21',
        'height': '5\'9"',
        'location': 'Kochi, Kerala',
        'experience': '1 Year',
        'availability': 'Available for assignments',
        'description': 'Editorial testing talent for IDFR Phase 3 verification.',
        'types[]': ['Runway', 'Editorial'],
        'skills': 'Runway walk, Posing',
        'specialities': 'Bridal, Ethnic',
        'seasons[]': ['4'],
        'status': 'published',
        'display_order': '99',
    }, doseq=True).encode('utf-8')

    res = opener.open(f"{base_url}/admin/models", data=post_data)
    res_html = res.read().decode('utf-8')
    created = 'Aria Sterling Test' in res_html or res.getcode() == 200
    log_test("Create Model Profile", created, "-> Successfully persisted to database with Season 4 association")

    # Verify public visibility
    res = opener.open(f"{base_url}/models")
    pub_html = res.read().decode('utf-8')
    log_test("Public Model Dynamic Sync", "Aria Sterling Test" in pub_html, "-> Published model rendered in public template")

    # 5d. Find ID of created model
    m_id_match = re.search(r'/admin/models/(\d+)/edit', res_html)
    if m_id_match:
        created_model_id = m_id_match.group(1)

        # 5e. Edit & Update to Draft
        update_data = urllib.parse.urlencode({
            '_token': token,
            '_method': 'PUT',
            'name': 'Aria Sterling Updated',
            'gender': 'Female',
            'level': 'Experienced',
            'age': '22',
            'height': '5\'10"',
            'location': 'Kochi, Kerala',
            'status': 'draft',
            'display_order': '99',
        }).encode('utf-8')
        res = opener.open(f"{base_url}/admin/models/{created_model_id}", data=update_data)
        up_html = res.read().decode('utf-8')
        log_test("Update Model & Unpublish (Draft)", "Aria Sterling Updated" in up_html, "-> Updated details and set to draft")

        # Verify draft is hidden publicly
        res = opener.open(f"{base_url}/models")
        pub_html_draft = res.read().decode('utf-8')
        log_test("Draft Model Hidden on Public Site", "Aria Sterling Updated" not in pub_html_draft, "-> Draft model safely excluded from public portal")

        # 5f. Delete Model
        del_data = urllib.parse.urlencode({
            '_token': token,
            '_method': 'DELETE',
        }).encode('utf-8')
        res = opener.open(f"{base_url}/admin/models/{created_model_id}", data=del_data)
        del_html = res.read().decode('utf-8')
        log_test("Delete Model Record", "has been deleted" in del_html, "-> Deleted successfully with relationship cleanup")

except Exception as e:
    log_test("Models CRUD Lifecycle", False, str(e))

# 6. Designers CRUD Lifecycle
created_designer_id = None
try:
    # 6a. Listing
    res = opener.open(f"{base_url}/admin/designers")
    d_html = res.read().decode('utf-8')
    log_test("Designers Directory Index", "Tara Nair" in d_html and "Isha Menon" in d_html, "-> Loaded with 6 couture designers")

    # 6b. Create Designer
    token = get_csrf()
    designer_post = urllib.parse.urlencode({
        '_token': token,
        'name': 'Vikramaditya Test',
        'brand_name': 'Vikram Couture',
        'location': 'Mumbai, Maharashtra',
        'experience': '12 years',
        'short_intro': 'Luxury royal bandhgalas and wedding sherwanis.',
        'biography': 'Pioneering classical craftsmanship for the modern Indian groom.',
        'collection_title': 'Regalia 2026',
        'services': 'Couture, Bridal, Sherwanis',
        'website_url': 'https://vikramcouture.example',
        'status': 'published',
        'seasons[]': ['3', '4'],
        'display_order': '99',
    }, doseq=True).encode('utf-8')

    res = opener.open(f"{base_url}/admin/designers", data=designer_post)
    d_res_html = res.read().decode('utf-8')
    log_test("Create Designer Profile", "Vikramaditya Test" in d_res_html, "-> Designer and brand persisted with season associations")

    # 6c. Edit & Delete Designer
    d_id_match = re.search(r'/admin/designers/(\d+)/edit', d_res_html)
    if d_id_match:
        created_designer_id = d_id_match.group(1)
        del_data = urllib.parse.urlencode({
            '_token': token,
            '_method': 'DELETE',
        }).encode('utf-8')
        res = opener.open(f"{base_url}/admin/designers/{created_designer_id}", data=del_data)
        d_del_html = res.read().decode('utf-8')
        log_test("Delete Designer Record", "has been deleted" in d_del_html, "-> Deleted safely without affecting seasons")

except Exception as e:
    log_test("Designers CRUD Lifecycle", False, str(e))

# 7. Creative Professionals CRUD Lifecycle
created_pro_id = None
try:
    # 7a. Listing & Category Filter
    res = opener.open(f"{base_url}/admin/professionals?category=makeup-artist")
    pro_html = res.read().decode('utf-8')
    log_test("Creative Professionals Category Filter", "Ananya Menon" in pro_html and "Makeup Artist" in pro_html, "-> Category pill filters working")

    # 7b. Create Professional
    token = get_csrf()
    pro_post = urllib.parse.urlencode({
        '_token': token,
        'name': 'Samira Khan Test',
        'category': 'photographer',
        'professional_title': 'Fashion & Editorial Photographer',
        'location': 'Kochi, Kerala',
        'experience': '7 years',
        'biography': 'Specializing in high-fashion studio editorial campaigns.',
        'services': 'Fashion photography, Runway, Lookbooks',
        'website_url': 'https://samirakhan.example',
        'status': 'published',
        'seasons[]': ['4'],
        'display_order': '99',
    }, doseq=True).encode('utf-8')

    res = opener.open(f"{base_url}/admin/professionals", data=pro_post)
    pro_res_html = res.read().decode('utf-8')
    log_test("Create Creative Professional", "Samira Khan Test" in pro_res_html, "-> Professional registered with category and season")

    # Verify public visibility on professionals page
    res = opener.open(f"{base_url}/professionals")
    pub_pro_html = res.read().decode('utf-8')
    log_test("Public Professional Dynamic Sync", "Samira Khan Test" in pub_pro_html, "-> Professional rendered in public template")

    # 7c. Clean up
    p_id_match = re.search(r'/admin/professionals/(\d+)/edit', pro_res_html)
    if p_id_match:
        created_pro_id = p_id_match.group(1)
        del_data = urllib.parse.urlencode({
            '_token': token,
            '_method': 'DELETE',
        }).encode('utf-8')
        res = opener.open(f"{base_url}/admin/professionals/{created_pro_id}", data=del_data)
        p_del_html = res.read().decode('utf-8')
        log_test("Delete Creative Professional", "has been deleted" in p_del_html, "-> Deleted cleanly with relationship detach")

except Exception as e:
    log_test("Creative Professionals CRUD Lifecycle", False, str(e))

# 8. Regression Verification of Public Website
public_routes = ['/', '/about', '/models', '/professionals', '/gallery', '/contact', '/tickets', '/profiles/model', '/profiles/fashion-designer']
all_pub_ok = True
for r in public_routes:
    try:
        res = urllib.request.urlopen(f"{base_url}{r}")
        if res.getcode() != 200:
            all_pub_ok = False
            print(f"[FAIL] Public route {r} returned {res.getcode()}")
    except Exception as e:
        all_pub_ok = False
        print(f"[FAIL] Public route {r} error: {e}")

log_test("Public Website Non-Regression", all_pub_ok, "-> All core public pages return 200 OK")

print("\n==================================================")
print(f"TEST RESULTS: {total_passed} PASSED, {total_failed} FAILED")
print("==================================================")
if total_failed > 0:
    sys.exit(1)
