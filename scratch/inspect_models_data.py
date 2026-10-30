import urllib.request
import re

url = 'http://127.0.0.1:8000/'
req = urllib.request.urlopen(url)
html = req.read().decode('utf-8')

match = re.search(r'<template id="modelsData">(.*?)</template>', html, re.DOTALL)
if match:
    content = match.group(1).strip()
    print("Found modelsData template, length:", len(content))
    articles = re.findall(r'<article.*?</article>', content, re.DOTALL)
    print(f"Total articles in modelsData: {len(articles)}")
    for i, a in enumerate(articles):
        print(f"\n--- Article {i} ---")
        print(a)
else:
    print("modelsData NOT FOUND")

