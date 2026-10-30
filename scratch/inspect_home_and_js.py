with open('resources/views/idfr/home.blade.php', 'r', encoding='utf-8') as f:
    home_text = f.read()

import re

# Check hero section
hero_match = re.search(r'<section[^>]*class="[^"]*hero[^"]*"[\s\S]*?</section>', home_text)
if hero_match:
    print("=== HERO SECTION IN HOME ===")
    print(hero_match.group(0)[:800])

# Check season 4 section in home
s4_match = re.search(r'(<!-- ============ SEASON 4 ============ -->[\s\S]*?</section>)', home_text)
if s4_match:
    print("\n=== SEASON 4 SECTION IN HOME ===")
    print(s4_match.group(1)[:800])

# Check heroSlidesData template
hero_template = re.search(r'<template id="heroSlidesData">[\s\S]*?</template>', home_text)
if hero_template:
    print("\n=== HERO SLIDES TEMPLATE ===")
    print(hero_template.group(0)[:800])

# Check program-season-4 template
p_s4 = re.search(r'<template id="program-season-4">[\s\S]*?</template>', home_text)
if p_s4:
    print("\n=== PROGRAM SEASON 4 TEMPLATE ===")
    print(p_s4.group(0)[:800])
