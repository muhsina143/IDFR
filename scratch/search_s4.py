with open('resources/views/idfr/home.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

lines = text.split('\n')
for idx, line in enumerate(lines):
    if 'season' in line.lower() and ('4' in line or 'four' in line):
        print(f"Line {idx+1}: {line.strip()[:100]}")
