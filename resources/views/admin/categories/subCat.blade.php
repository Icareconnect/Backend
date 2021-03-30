@foreach($subcategories as $subcategory)
 <ul role="group">
    <li role="treeitem"  aria-expanded="false" data-cate_name="{{$subcategory->name}}" data-parent_id="{{ $subcategory->id }}" >
	  @if(count($subcategory->subcategory))
	  	<span>{{ $subcategory->name }}</span>
	    @include('admin.categories.subCat',['subcategories' => $subcategory->subcategory])
	  @else
	  	{{ $subcategory->name }}
	  @endif
    </li> 
 </ul> 
@endforeach