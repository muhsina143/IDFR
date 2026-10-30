import os
import re

dynamic_program_s4 = '''<template id="program-season-4">
      <p class="info__label">IDFR Fashion Show</p>
      <h2 class="info__title" id="infoModalTitle">{{ $currentSeason ? $currentSeason->title : 'Season 4' }}</h2>
      <p class="info__intro">
        {{ $currentSeason && $currentSeason->description ? $currentSeason->description : 'Three runway nights celebrating emerging designers, established names and the next generation of Indian modelling talent. Season 4 brings couture, resort, menswear and handloom collections to one stage in Kozhikode.' }}
      </p>
      <dl class="info__facts">
        <div>
          <dt>Dates</dt>
          <dd>{{ $currentSeason ? $currentSeason->dates : '18 – 20 Dec 2026' }}</dd>
        </div>
        <div>
          <dt>Venue</dt>
          <dd>{{ $currentSeason ? $currentSeason->location : 'IDFR Fashion House' }}</dd>
        </div>
        <div>
          <dt>Designers</dt>
          <dd>24 Labels</dd>
        </div>
        <div>
          <dt>Models</dt>
          <dd>60+ Talent</dd>
        </div>
      </dl>
      <div class="info__schedule">
        <article>
          <h3>Day 1 · Opening Night</h3>
          <p>Debut collections walked by models from the Season 4 open auditions.</p>
        </article>
        <article>
          <h3>Day 2 · Designer Showcase</h3>
          <p>Evening gowns, bridal couture and prêt lines from across South India.</p>
        </article>
        <article>
          <h3>Day 3 · Grand Finale</h3>
          <p>Couture showcases, awards and the closing walk of {{ $currentSeason ? $currentSeason->title : 'Season 4' }}.</p>
        </article>
      </div>
    </template>'''

views = [
    'resources/views/idfr/about.blade.php',
    'resources/views/idfr/models.blade.php',
    'resources/views/idfr/professionals.blade.php',
    'resources/views/idfr/gallery.blade.php',
    'resources/views/idfr/contact.blade.php',
    'resources/views/idfr/tickets.blade.php',
]

pattern = r'<template id="program-season-4">[\s\S]*?</template>'

for view_path in views:
    if not os.path.exists(view_path):
        continue
    with open(view_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if re.search(pattern, content):
        content = re.sub(pattern, dynamic_program_s4, content)
        with open(view_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated program-season-4 in {view_path}")
    else:
        print(f"Pattern not found in {view_path}")
