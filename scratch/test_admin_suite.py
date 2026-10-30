import urllib.request
import urllib.parse
import http.cookiejar
import html as html_lib
import re
import sys

base_url = "http://127.0.0.1:8000"

# Setup cookie jar to simulate browser session
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
print("  IDFR FASHION RUNWAY — ADMIN TEST SUITE (PHASE 1)")
print("==================================================")

# 1. Unauthenticated /admin Access
try:
    req = urllib.request.Request(f"{base_url}/admin", headers={'User-Agent': 'Mozilla/5.0'})
    class NoRedirectHandler(urllib.request.HTTPRedirectHandler):
        def http_error_302(self, req, fp, code, msg, headers):
            return fp
        http_error_301 = http_error_302

    no_red_opener = urllib.request.build_opener(NoRedirectHandler)
    res = no_red_opener.open(req)
    redirect_target = res.headers.get('Location', '')
    if '/admin/login' in redirect_target or res.getcode() == 302:
        log_test("1. Unauthenticated Protection", True, f"-> Redirected to {redirect_target}")
    else:
        log_test("1. Unauthenticated Protection", False, f"Code: {res.getcode()}, Target: {redirect_target}")
except Exception as e:
    log_test("1. Unauthenticated Protection", False, str(e))

# 2. GET /admin/login Page Loading & CSRF Verification
csrf_token = None
try:
    req = urllib.request.Request(f"{base_url}/admin/login", headers={'User-Agent': 'Mozilla/5.0'})
    res = opener.open(req)
    html_page = res.read().decode('utf-8')
    token_match = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', html_page)
    if token_match:
        csrf_token = token_match.group(1)
        has_elements = ("Admin Portal" in html_page) and ("Executive Security Portal" in html_page) and ("password-toggle" in html_page)
        log_test("2. Admin Login View & CSRF", has_elements, f"-> Token acquired ({len(csrf_token)} chars)")
    else:
        log_test("2. Admin Login View & CSRF", False, "No CSRF token found in login form")
except Exception as e:
    log_test("2. Admin Login View & CSRF", False, str(e))

# 3. Failed Login with Invalid Credentials
try:
    post_data = urllib.parse.urlencode({
        '_token': csrf_token,
        'email': 'director@idfr.example',
        'password': 'WrongPassword123!',
    }).encode('utf-8')
    req = urllib.request.Request(f"{base_url}/admin/login", data=post_data, headers={'User-Agent': 'Mozilla/5.0'})
    res = opener.open(req)
    html_page = res.read().decode('utf-8')
    rejected = "The provided credentials do not match our administrative records." in html_page or "credentials" in html_page
    log_test("3. Reject Invalid Credentials", rejected, "-> Properly rejected invalid password")
except Exception as e:
    log_test("3. Reject Invalid Credentials", False, str(e))

# Update CSRF token from current cookies/html
token_match = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', html_page)
if token_match:
    csrf_token = token_match.group(1)

# 4. Successful Admin Login
logged_in = False
try:
    post_data = urllib.parse.urlencode({
        '_token': csrf_token,
        'email': 'director@idfr.example',
        'password': 'SuperSecureAdminPassword123!',
        'remember': '1',
    }).encode('utf-8')
    req = urllib.request.Request(f"{base_url}/admin/login", data=post_data, headers={'User-Agent': 'Mozilla/5.0'})
    res = opener.open(req)
    final_url = res.geturl()
    html_page = res.read().decode('utf-8')
    
    if '/admin' in final_url and "Executive Overview" in html_page and "IDFR Director" in html_page:
        logged_in = True
        log_test("4. Authenticated Admin Login", True, f"-> Successfully entered {final_url}")
    else:
        log_test("4. Authenticated Admin Login", False, f"URL: {final_url}, Contains director: {'IDFR Director' in html_page}")
except Exception as e:
    log_test("4. Authenticated Admin Login", False, str(e))

# 5. Verify Dashboard Metrics and Audit Log
if logged_in:
    has_metrics = ("Seasons" in html_page) and ("Total Models" in html_page) and ("Total Designers" in html_page) and ("Total Registrations" in html_page)
    has_activity = ("Audit & Security Log" in html_page) and ("LOGIN SUCCESS" in html_page)
    has_telemetry = ("System Architecture" in html_page) and ("Laravel" in html_page)
    log_test("5. Dashboard Metrics & Audit Log", has_metrics and has_activity and has_telemetry, "-> Metrics, Audit log & Telemetry present")
else:
    log_test("5. Dashboard Metrics & Audit Log", False, "Skipped due to failed login")

# 6. Verify All 8 Sub-Module Routes
modules = [
    ('/admin/seasons', 'Seasons'),
    ('/admin/gallery', 'Gallery'),
    ('/admin/models', 'Model'),
    ('/admin/designers', 'Designer'),
    ('/admin/professionals', 'Professional'),
    ('/admin/enquiries', 'Contact & Sponsorship Enquiries'),
    ('/admin/registrations', 'Member Registrations'),
    ('/admin/settings', 'Executive Site Settings'),
]

all_modules_ok = True
for path, title in modules:
    try:
        req = urllib.request.Request(f"{base_url}{path}", headers={'User-Agent': 'Mozilla/5.0'})
        res = opener.open(req)
        m_html = res.read().decode('utf-8')
        m_unescaped = html_lib.unescape(m_html)
        if res.getcode() == 200 and title in m_unescaped:
            pass
        else:
            all_modules_ok = False
            print(f"  Failed module {path}")
    except Exception as e:
        all_modules_ok = False
        print(f"  Exception on module {path}: {e}")

log_test("6. Sidebar Module Routes (No Crash)", all_modules_ok, "-> All 8 admin modules respond with 200 OK")

# 7. Test Logout Functionality
try:
    logout_token = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', html_page).group(1)
    post_data = urllib.parse.urlencode({'_token': logout_token}).encode('utf-8')
    req = urllib.request.Request(f"{base_url}/admin/logout", data=post_data, headers={'User-Agent': 'Mozilla/5.0'})
    res = opener.open(req)
    logout_html = res.read().decode('utf-8')
    
    logged_out_ok = ("logged out securely" in logout_html or "Admin Portal" in logout_html) and ('/admin/login' in res.geturl())
    log_test("7. Secure Logout & Invalidation", logged_out_ok, f"-> Redirected to {res.geturl()}")
    
    # Verify accessing /admin now fails
    req_check = urllib.request.Request(f"{base_url}/admin", headers={'User-Agent': 'Mozilla/5.0'})
    res_check = opener.open(req_check)
    session_cleared = '/admin/login' in res_check.geturl()
    log_test("8. Post-Logout Protected Verification", session_cleared, "-> /admin correctly redirected to /admin/login")
except Exception as e:
    log_test("7. Secure Logout & Invalidation", False, str(e))
    log_test("8. Post-Logout Protected Verification", False, str(e))

# 9. Verify Public Website Unbroken
public_routes = ['/', '/about', '/models', '/tickets', '/asdreams']
public_ok = True
for pr in public_routes:
    try:
        req = urllib.request.Request(f"{base_url}{pr}", headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req) as r:
            if r.getcode() != 200:
                public_ok = False
    except Exception as e:
        public_ok = False

log_test("9. Public Website Unbroken", public_ok, "-> All tested public pages return 200 OK")

print("\n==================================================")
print(f"TEST RESULTS: {total_passed} PASSED, {total_failed} FAILED")
print("==================================================")

if total_failed > 0:
    sys.exit(1)
