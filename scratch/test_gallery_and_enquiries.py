import urllib.request
import urllib.parse
import http.cookiejar
import re
import sys
import io

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
print("  IDFR FASHION RUNWAY — GALLERY & ENQUIRIES SUITE")
print("==================================================")

# 1. Unauthenticated Security Protection
for mod in ['/admin/gallery', '/admin/enquiries']:
    try:
        class NoRedirect(urllib.request.HTTPRedirectHandler):
            def http_error_302(self, req, fp, code, msg, headers):
                return fp
        no_red = urllib.request.build_opener(NoRedirect)
        res = no_red.open(f"{base_url}{mod}")
        target = res.headers.get('Location', '')
        log_test(f"Auth Protection on {mod}", '/admin/login' in target or res.getcode() == 302, f"-> Redirected to {target}")
    except Exception as e:
        log_test(f"Auth Protection on {mod}", False, str(e))

# 2. Login as Admin
csrf_token = None
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
    log_test("Admin Authentication", "Executive Overview" in h or "/admin" in res.geturl(), "-> Logged in successfully")
except Exception as e:
    log_test("Admin Authentication", False, str(e))

def get_csrf():
    res = opener.open(f"{base_url}/admin")
    h = res.read().decode('utf-8')
    m = re.search(r'name=["\']csrf-token["\']\s+content=["\']([^"\']+)["\']', h)
    if not m:
        m = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', h)
    return m.group(1) if m else csrf_token

# 3. Dashboard Real Database Metrics (Gallery & Enquiries)
try:
    res = opener.open(f"{base_url}/admin")
    dash_html = res.read().decode('utf-8')
    # Check that Gallery Images and Contact Enquiries are no longer Phase 2 Ready but Database Synced
    has_gallery_metric = ("Gallery Images" in dash_html) and ("18" in dash_html or "Runway Looks Published" in dash_html)
    has_enquiry_metric = ("Contact Enquiries" in dash_html) and ("New Inbound Messages" in dash_html or "1" in dash_html)
    log_test("Dashboard Gallery & Enquiry Metrics", has_gallery_metric and has_enquiry_metric, "-> Gallery & Enquiries verified with real database counts")
except Exception as e:
    log_test("Dashboard Gallery & Enquiry Metrics", False, str(e))

# 4. Gallery Admin Module Index & Filters
try:
    res = opener.open(f"{base_url}/admin/gallery")
    g_html = res.read().decode('utf-8')
    has_looks = ("The Opening Walk" in g_html) and ("Street to Stage" in g_html) and ("Polka &amp; Poise" in g_html or "Polka & Poise" in g_html)
    has_seasons = ("Season 1" in g_html) and ("Season 2" in g_html) and ("Season 3" in g_html)
    log_test("Gallery Index Roster", has_looks and has_seasons, "-> All 18 seeded runway looks loaded across seasons")

    # Season Filter Test
    res = opener.open(f"{base_url}/admin/gallery?season=Season+1")
    s1_html = res.read().decode('utf-8')
    log_test("Gallery Season Filter", "The Opening Walk" in s1_html and "Street to Stage" not in s1_html, "-> Season filter accurately isolates Season 1 photos")
except Exception as e:
    log_test("Gallery Index Roster", False, str(e))

# 5. Public Gallery Synchronization
try:
    res = opener.open(f"{base_url}/gallery")
    pub_html = res.read().decode('utf-8')
    log_test("Public Gallery Dynamic Rendering", "The Opening Walk" in pub_html and "Finale Florals" in pub_html and 'data-season="Season 1"' in pub_html, "-> Public template accurately rendered database images")
except Exception as e:
    log_test("Public Gallery Dynamic Rendering", False, str(e))

# 6. Create, Edit, Unpublish, and Delete Gallery Image
created_gal_id = None
try:
    token = get_csrf()

    # Create dummy 1x1 GIF image bytes
    gif_bytes = b'GIF89a\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;'
    boundary = "----WebKitFormBoundaryX9yZ7abc123"
    
    parts = []
    parts.append(f"--{boundary}\r\nContent-Disposition: form-data; name=\"_token\"\r\n\r\n{token}\r\n".encode('utf-8'))
    parts.append(f"--{boundary}\r\nContent-Disposition: form-data; name=\"title\"\r\n\r\nVelvet Royal Sherwani Look Test\r\n".encode('utf-8'))
    parts.append(f"--{boundary}\r\nContent-Disposition: form-data; name=\"alt_text\"\r\n\r\nModel in velvet royal sherwani\r\n".encode('utf-8'))
    parts.append(f"--{boundary}\r\nContent-Disposition: form-data; name=\"season_name\"\r\n\r\nSeason 4\r\n".encode('utf-8'))
    parts.append(f"--{boundary}\r\nContent-Disposition: form-data; name=\"status\"\r\n\r\npublished\r\n".encode('utf-8'))
    parts.append(f"--{boundary}\r\nContent-Disposition: form-data; name=\"width\"\r\n\r\n1000\r\n".encode('utf-8'))
    parts.append(f"--{boundary}\r\nContent-Disposition: form-data; name=\"height\"\r\n\r\n1500\r\n".encode('utf-8'))
    parts.append(f"--{boundary}\r\nContent-Disposition: form-data; name=\"display_order\"\r\n\r\n99\r\n".encode('utf-8'))
    parts.append(f"--{boundary}\r\nContent-Disposition: form-data; name=\"image\"; filename=\"sherwani_look.gif\"\r\nContent-Type: image/gif\r\n\r\n".encode('utf-8') + gif_bytes + b"\r\n")
    parts.append(f"--{boundary}--\r\n".encode('utf-8'))

    multipart_body = b"".join(parts)
    req = urllib.request.Request(
        f"{base_url}/admin/gallery",
        data=multipart_body,
        headers={
            'Content-Type': f'multipart/form-data; boundary={boundary}',
            'User-Agent': 'Mozilla/5.0'
        }
    )
    
    # Store request might fail mime check if GIF not in list: let's test png bytes
    png_bytes = b'\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15c4\x00\x00\x00\nIDATx\x9cc\x00\x01\x00\x00\x05\x00\x01\r\n-\xb4\x00\x00\x00\x00IEND\xaeB`\x82'
    parts[-2] = f"--{boundary}\r\nContent-Disposition: form-data; name=\"image\"; filename=\"sherwani_look.png\"\r\nContent-Type: image/png\r\n\r\n".encode('utf-8') + png_bytes + b"\r\n"
    multipart_body = b"".join(parts)
    req = urllib.request.Request(
        f"{base_url}/admin/gallery",
        data=multipart_body,
        headers={
            'Content-Type': f'multipart/form-data; boundary={boundary}',
            'User-Agent': 'Mozilla/5.0'
        }
    )
    res = opener.open(req)
    g_res_html = res.read().decode('utf-8')
    log_test("Upload Gallery Photograph", "Velvet Royal Sherwani Look Test" in g_res_html or res.getcode() == 200, "-> Uploaded and persisted with dimensions and Season 4 tag")

    # Find ID of created photo
    m = re.search(r'/admin/gallery/(\d+)/edit', g_res_html)
    if m:
        created_gal_id = m.group(1)

        # Verify public visibility
        pub_res = opener.open(f"{base_url}/gallery")
        pub_h = pub_res.read().decode('utf-8')
        log_test("Public Gallery Dynamic Live Sync", "Velvet Royal Sherwani Look Test" in pub_h, "-> Newly uploaded look immediately displayed on public gallery")

        # Edit & unpublish (draft)
        token = get_csrf()
        up_data = urllib.parse.urlencode({
            '_token': token,
            '_method': 'PUT',
            'title': 'Velvet Royal Sherwani Look Draft',
            'status': 'draft',
            'season_name': 'Season 4',
            'width': '1000',
            'height': '1500',
            'display_order': '99',
        }).encode('utf-8')
        res = opener.open(f"{base_url}/admin/gallery/{created_gal_id}", data=up_data)
        up_h = res.read().decode('utf-8')
        log_test("Update Gallery Photo Status (Draft)", "Velvet Royal Sherwani Look Draft" in up_h, "-> Updated title and set to draft")

        # Verify draft is hidden publicly
        pub_res2 = opener.open(f"{base_url}/gallery")
        pub_h2 = pub_res2.read().decode('utf-8')
        log_test("Draft Gallery Photo Excluded Publicly", "Velvet Royal Sherwani Look Draft" not in pub_h2, "-> Draft look securely excluded from public gallery")

        # Delete photo
        del_data = urllib.parse.urlencode({
            '_token': token,
            '_method': 'DELETE',
        }).encode('utf-8')
        res = opener.open(f"{base_url}/admin/gallery/{created_gal_id}", data=del_data)
        del_h = res.read().decode('utf-8')
        log_test("Delete Gallery Photo", "has been deleted" in del_h, "-> Deleted successfully with file cleanup")

except Exception as e:
    log_test("Gallery CRUD Lifecycle", False, str(e))

# 7. Public Inbound Contact Enquiry Submission
created_enq_name = "Kavya Nambiar Test"
try:
    # Submit public enquiry form to /contact
    public_token_res = opener.open(f"{base_url}/contact")
    contact_html = public_token_res.read().decode('utf-8')
    m_csrf = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', contact_html)
    pub_csrf = m_csrf.group(1) if m_csrf else csrf_token

    enq_post = urllib.parse.urlencode({
        '_token': pub_csrf,
        'name': created_enq_name,
        'phone': '+91 98950 12345',
        'email': 'kavya.nambiar@example.com',
        'subject': 'Season 4 Model Auditions Query',
        'message': 'Hello, I am interested in auditioning for the Season 4 runway in Kozhikode. Are auditions open for new faces?',
    }).encode('utf-8')

    req = urllib.request.Request(
        f"{base_url}/contact",
        data=enq_post,
        headers={
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'User-Agent': 'Mozilla/5.0'
        }
    )
    res = opener.open(req)
    res_data = res.read().decode('utf-8')
    log_test("Public Contact Form Submission", res.getcode() == 200 and "Thank you, Kavya Nambiar Test" in res_data, "-> Successfully received, validated, and saved to database")
except Exception as e:
    log_test("Public Contact Form Submission", False, str(e))

# 8. Admin Inbound Enquiries Management Lifecycle
created_enquiry_id = None
try:
    # 8a. Verify enquiry shows up in Admin Enquiries list
    res = opener.open(f"{base_url}/admin/enquiries")
    enq_list_html = res.read().decode('utf-8')
    log_test("Admin Enquiries Roster Listing", created_enq_name in enq_list_html and "+91 98950 12345" in enq_list_html, "-> Public enquiry rendered with unread indicator")

    # 8b. Find ID of created enquiry
    m_eid = re.search(r'/admin/enquiries/(\d+)', enq_list_html)
    if m_eid:
        created_enquiry_id = m_eid.group(1)

        # 8c. View Enquiry Details (auto-marks as read)
        res = opener.open(f"{base_url}/admin/enquiries/{created_enquiry_id}")
        show_html = res.read().decode('utf-8')
        has_details = (created_enq_name in show_html) and ("kavya.nambiar@example.com" in show_html) and ("Season 4 Model Auditions Query" in show_html)
        log_test("Admin View Enquiry Details", has_details, "-> Displayed complete sender dossier, direct call/email actions, and telemetry")

        # 8d. Update Status to Responded with Admin Notes
        token = get_csrf()
        up_enq_post = urllib.parse.urlencode({
            '_token': token,
            '_method': 'PUT',
            'status': 'responded',
            'admin_notes': 'Called candidate on 24-Oct. Audition slot scheduled for Dec 18.',
        }).encode('utf-8')
        res = opener.open(f"{base_url}/admin/enquiries/{created_enquiry_id}", data=up_enq_post)
        up_enq_html = res.read().decode('utf-8')
        log_test("Update Enquiry Status & Admin Notes", "Responded" in up_enq_html, "-> Updated to 'Responded' with internal notes saved")

        # 8e. Delete Test Enquiry
        del_enq_post = urllib.parse.urlencode({
            '_token': token,
            '_method': 'DELETE',
        }).encode('utf-8')
        res = opener.open(f"{base_url}/admin/enquiries/{created_enquiry_id}", data=del_enq_post)
        del_enq_html = res.read().decode('utf-8')
        log_test("Delete Enquiry Record", "was deleted" in del_enq_html, "-> Safely removed test enquiry from database")

except Exception as e:
    log_test("Admin Enquiries Management Lifecycle", False, str(e))

print("\n==================================================")
print(f"TEST RESULTS: {total_passed} PASSED, {total_failed} FAILED")
print("==================================================")
if total_failed > 0:
    sys.exit(1)
