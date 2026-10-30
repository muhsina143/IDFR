for path in ['resources/views/idfr/home.blade.php', 'resources/views/idfr/models.blade.php']:
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()

    norm = content.replace('\r\n', '\n')

    old_stats = """          <dl data-stats>
            @if($m->age)<dt>Age</dt><dd data-age>{{ $m->age }}</dd>@endif
            @if($m->height)<dt>Height</dt><dd data-height>{{ $m->height }}</dd>@endif
            @if($m->location)<dt>Location</dt><dd data-location>{{ $m->location }}</dd>@endif
            @if($m->experience)<dt>Experience</dt><dd data-experience>{{ $m->experience }}</dd>@endif
          </dl>"""

    new_stats = """          <dl data-stats>
            <dt>Age</dt><dd data-age>{{ $m->age }}</dd>
            <dt>Height</dt><dd data-height>{{ $m->height }}</dd>
            <dt>Location</dt><dd data-location>{{ $m->location }}</dd>
            <dt>Experience</dt><dd data-experience>{{ $m->experience }}</dd>
          </dl>"""

    old_avail_desc = """          @if($m->availability)<p data-availability>{{ $m->availability }}</p>@endif
          @if($m->description)<p data-description>{{ $m->description }}</p>@endif"""

    new_avail_desc = """          <p data-availability>{{ $m->availability }}</p>
          <p data-description>{{ $m->description }}</p>"""

    assert old_stats in norm, f"old_stats not found in {path}"
    assert old_avail_desc in norm, f"old_avail_desc not found in {path}"

    norm = norm.replace(old_stats, new_stats)
    norm = norm.replace(old_avail_desc, new_avail_desc)

    with open(path, 'w', encoding='utf-8') as f:
        f.write(norm.replace('\n', '\r\n'))

    print(f"Updated {path}")

