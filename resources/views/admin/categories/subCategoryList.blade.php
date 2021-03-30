<ul class="list-group mt-3">
@foreach ($subcategories as $subcategory)
  <li class="list-group-item mt-3">
    <div class="d-flex justify-content-between">
      <span style="color:{{ $subcategory->color_code }}"> {{ $subcategory->name }}</span>
      <a href="{{ route('categories.edit', $subcategory->id) }}" class="btn btn-sm btn-primary mr-1 edit-category" >Edit</a>
      <!-- <div class="button-group d-flex">
        <form action="{{ route('categories.destroy', $subcategory->id) }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
      </div> -->
    </div>
    @if(count($subcategory->subcategory))
            @include('admin.categories.subCategoryList',['subcategories' => $subcategory->subcategory, 'dataParent' => $subcategory->id, 'dataLevel' => $dataLevel ])
        @endif
  </li>
@endforeach
</ul>