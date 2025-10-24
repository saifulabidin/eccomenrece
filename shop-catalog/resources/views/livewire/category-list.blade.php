<div class="row g-4">
    @foreach($categories as $category)
    <div class="col-lg-3 col-md-4 col-sm-6">
        <x-category-card :category="$category" />
    </div>
    @endforeach
</div>
