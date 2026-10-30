import urllib.request, http.cookiejar, urllib.parse, re

base_url = 'http://127.0.0.1:8000'
cj = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))

# Login
res = opener.open(f'{base_url}/admin/login')
m = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', res.read().decode('utf-8'))
token = m.group(1)
post = urllib.parse.urlencode({'_token': token, 'email': 'director@idfr.example', 'password': 'SuperSecureAdminPassword123!'}).encode('utf-8')
opener.open(f'{base_url}/admin/login', data=post)

# Try GET /admin/models/1/edit
try:
    r = opener.open(f'{base_url}/admin/models/1/edit')
    print('GET /admin/models/1/edit status:', r.getcode())
except Exception as e:
    print('GET /admin/models/1/edit error:', e)

# Try GET /admin/designers/1/edit
try:
    r = opener.open(f'{base_url}/admin/designers/1/edit')
    print('GET /admin/designers/1/edit status:', r.getcode())
except Exception as e:
    print('GET /admin/designers/1/edit error:', e)

# Try GET /admin/professionals/1/edit
try:
    r = opener.open(f'{base_url}/admin/professionals/1/edit')
    print('GET /admin/professionals/1/edit status:', r.getcode())
except Exception as e:
    print('GET /admin/professionals/1/edit error:', e)
