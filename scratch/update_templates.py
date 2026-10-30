import re

# 1. Update auth-modals.blade.php
with open('resources/views/partials/auth-modals.blade.php', 'r', encoding='utf-8') as f:
    auth_content = f.read()

if "@include('partials.enquiry-modal-template')" not in auth_content:
    pattern = r'(id="modelModal"[\s\S]*?</div>\s*</div>)'
    match = re.search(pattern, auth_content)
    if match:
        auth_content = auth_content[:match.end()] + "\n\n    @include('partials.enquiry-modal-template')\n" + auth_content[match.end():]
        with open('resources/views/partials/auth-modals.blade.php', 'w', encoding='utf-8') as f:
            f.write(auth_content)
        print('auth-modals updated')
    else:
        print('modelModal not found in auth-modals')
else:
    print('already in auth-modals')

# 2. Update models.blade.php
with open('resources/views/idfr/models.blade.php', 'r', encoding='utf-8') as f:
    models_content = f.read()

models_pattern = r'<template id="modelEnquiryTemplate">[\s\S]*?</template>'
if re.search(models_pattern, models_content):
    models_content = re.sub(models_pattern, "@include('partials.enquiry-modal-template')", models_content, count=1)
    with open('resources/views/idfr/models.blade.php', 'w', encoding='utf-8') as f:
        f.write(models_content)
    print('models.blade.php updated')

# 3. Update home.blade.php
with open('resources/views/idfr/home.blade.php', 'r', encoding='utf-8') as f:
    home_content = f.read()

if re.search(models_pattern, home_content):
    home_content = re.sub(models_pattern, "@include('partials.enquiry-modal-template')", home_content, count=1)
    with open('resources/views/idfr/home.blade.php', 'w', encoding='utf-8') as f:
        f.write(home_content)
    print('home.blade.php updated')

