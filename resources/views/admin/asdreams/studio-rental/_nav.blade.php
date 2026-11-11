@include('partials.svg-sprite')

<div class="category-tabs-bar" style="margin-bottom: 1.5rem;">
  <a href="{{ route('admin.asdreams.studio-rental.index', ['tab' => 'facilities']) }}"
     class="category-tab {{ request()->routeIs('admin.asdreams.studio-facilities*') ? 'is-active' : '' }}">
    Facilities Provided
  </a>
  <a href="{{ route('admin.asdreams.studio-rental.index', ['tab' => 'equipment']) }}"
     class="category-tab {{ request()->routeIs('admin.asdreams.studio-equipment*') ? 'is-active' : '' }}">
    Equipment Included
  </a>
  <a href="{{ route('admin.asdreams.studio-rental.index', ['tab' => 'gallery']) }}"
     class="category-tab {{ request()->routeIs('admin.asdreams.studio-gallery*') ? 'is-active' : '' }}">
    Studio Gallery
  </a>
  <a href="{{ route('admin.asdreams.studio-rental.index', ['tab' => 'settings']) }}"
     class="category-tab {{ request()->routeIs('admin.asdreams.settings*') ? 'is-active' : '' }}">
    Studio Settings
  </a>
</div>
