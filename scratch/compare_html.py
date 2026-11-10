import os

ref_path = r'C:\Users\muhsi\.gemini\antigravity\brain\a685094e-c789-45c7-bc8e-7e36ccf2d859\.user_uploaded\media_1793946082086_c6d9cb60.html'
with open(ref_path, 'r', encoding='utf-8') as f:
    ref_lines = f.readlines()

print(f"Reference lines: {len(ref_lines)}")
# Check what is in the reference file
print("First 20 lines of ref:")
print("".join(ref_lines[:20]))

