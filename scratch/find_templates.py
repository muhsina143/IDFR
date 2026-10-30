import glob
import re

for path in glob.glob('resources/views/idfr/*.blade.php'):
    with open(path, 'r', encoding='utf-8') as f:
        text = f.read()
    templates = re.findall(r'<template[^>]*id=["\']([^"\']+)["\']', text)
    print(path)
    for t in templates:
        print("  -", t)
