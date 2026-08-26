@extends('layouts.admin')

@section('title', 'Categories Management')
@section('page-title', 'Categories Management')

@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Campaign Categories</h1>
            <p class="text-sm text-slate-500 mt-1">Organize campaigns into distinct categories with name and custom icon/image for seamless mobile app filtering.</p>
        </div>

        <button type="button" onclick="openCreateCategoryModal()" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Add Category</span>
        </button>
    </div>

    <!-- Overview Stats & Search Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Total Categories</span>
                <span class="text-xl font-extrabold text-slate-900">{{ $totalCategories }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Categorized Campaigns</span>
                <span class="text-xl font-extrabold text-slate-900">{{ $totalCampaignsCategorized }}</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center">
            <form method="GET" action="{{ route('admin.categories') }}" class="relative w-full">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
            </form>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($categories as $category)
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden flex flex-col hover:border-slate-300 hover:shadow-md transition-all shadow-xs group">
                <!-- Thumbnail Header -->
                <div class="h-36 bg-slate-100 relative overflow-hidden flex items-center justify-center border-b border-slate-100">
                    @if ($category->image)
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <!-- Active Tag / Count Badge -->
                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/90 text-slate-700 shadow-xs border border-slate-200/80 backdrop-blur-md">
                        {{ $category->campaigns_count }} {{ Str::plural('Campaign', $category->campaigns_count) }}
                    </span>

                    <!-- Quick Action Buttons -->
                    <div class="absolute top-3 right-3 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" onclick='openEditCategoryModal(@json($category))' 
                                class="p-2 rounded-xl bg-white/90 hover:bg-indigo-600 text-slate-700 hover:text-white backdrop-blur-md transition-colors shadow-md border border-slate-200" title="Edit Category">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('Are you sure you want to delete this category? Associated campaigns will remain unassigned.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-xl bg-white/90 hover:bg-red-600 text-slate-700 hover:text-white backdrop-blur-md transition-colors shadow-md border border-slate-200" title="Delete Category">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $category->name }}</h3>
                        <p class="text-xs text-slate-400 mt-1">Created {{ $category->created_at->diffForHumans() }}</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('admin.campaigns', ['category_id' => $category->id]) }}" 
                           class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-700">
                            <span>View Campaigns</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <button type="button" onclick='openEditCategoryModal(@json($category))' 
                                class="text-xs text-slate-500 hover:text-slate-900 font-semibold cursor-pointer">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white border border-slate-200/80 rounded-2xl">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">No categories found</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Create categories like "Google Maps Reviews", "App Testing", "Surveys" so users can browse tasks easily.</p>
                <button type="button" onclick="openCreateCategoryModal()" 
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create First Category</span>
                </button>
            </div>
        @endforelse
    </div>

    @if ($categories->hasPages())
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    @endif
</div>

<!-- CREATE / EDIT CATEGORY MODAL -->
<div id="categoryModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm hidden" onclick="if(event.target === this) closeCategoryModal()">
    <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
        <div class="bg-white border border-slate-200/90 rounded-3xl w-full max-w-lg shadow-2xl p-6 sm:p-8 relative my-8" onclick="event.stopPropagation()">
            <!-- Close Button -->
            <button type="button" onclick="closeCategoryModal()" class="absolute top-6 right-6 p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Modal Header -->
            <div class="pr-12 mb-6 border-b border-slate-100 pb-4">
                <h2 id="categoryModalTitle" class="text-xl font-extrabold text-slate-900 tracking-tight mb-1">Add New Category</h2>
                <p id="categoryModalSubtitle" class="text-xs text-slate-500">Provide category name and upload an icon or image banner.</p>
            </div>

            <form method="POST" action="{{ route('admin.categories.store') }}" id="categoryForm" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div id="categoryMethodContainer"></div>

                <!-- Category Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Category Name</label>
                    <input type="text" name="name" id="formCategoryName" required placeholder="e.g. Google Reviews, App Rating, Surveys" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-sm focus:outline-none focus:bg-white focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 transition-all">
                </div>

                <!-- Direct Image File Upload -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Category Image / Icon File</label>
                    <input type="file" name="image_file" id="formCategoryImageFile" accept="image/*" onchange="previewCategoryImage(this)" 
                           class="w-full text-xs font-semibold text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 cursor-pointer">
                    <p class="text-[11px] text-slate-500 mt-1">Upload a clean PNG, JPG, or SVG icon/banner (recommended square or landscape).</p>
                </div>

                <!-- Or Image URL (Optional) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Or Direct Image URL (Optional)</label>
                    <input type="url" name="image_url" id="formCategoryImageUrl" placeholder="https://example.com/icon.png" 
                           class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 text-xs focus:outline-none focus:bg-white focus:border-indigo-600">
                </div>

                <!-- Image Preview Area -->
                <div id="imagePreviewContainer" class="hidden p-3 bg-slate-50 border border-slate-200 rounded-2xl flex items-center gap-3">
                    <img id="imagePreview" src="" alt="Preview" class="w-14 h-14 rounded-xl object-cover border border-slate-200">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Current Preview</span>
                        <span class="text-[11px] text-slate-500">Image ready to be saved.</span>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeCategoryModal()" class="px-4 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold transition-colors cursor-pointer">Cancel</button>
                    <button type="submit" id="categorySubmitBtn" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm rounded-xl shadow-md shadow-indigo-600/20 transition-all cursor-pointer">
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openCreateCategoryModal() {
        document.getElementById('categoryModalTitle').innerText = 'Add New Category';
        document.getElementById('categoryModalSubtitle').innerText = 'Provide category name and upload an icon or image banner.';
        document.getElementById('categorySubmitBtn').innerText = 'Save Category';
        
        const form = document.getElementById('categoryForm');
        form.action = "{{ route('admin.categories.store') }}";
        document.getElementById('categoryMethodContainer').innerHTML = '';
        form.reset();
        
        document.getElementById('imagePreviewContainer').classList.add('hidden');
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function openEditCategoryModal(category) {
        document.getElementById('categoryModalTitle').innerText = 'Edit Category: ' + category.name;
        document.getElementById('categoryModalSubtitle').innerText = 'Update category name or replace the current image icon.';
        document.getElementById('categorySubmitBtn').innerText = 'Update Category';
        
        const form = document.getElementById('categoryForm');
        form.action = "/admin/categories/" + category.id;
        document.getElementById('categoryMethodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('formCategoryName').value = category.name || '';
        document.getElementById('formCategoryImageUrl').value = category.image || '';
        
        const previewContainer = document.getElementById('imagePreviewContainer');
        const previewImg = document.getElementById('imagePreview');

        if (category.image) {
            previewImg.src = category.image;
            previewContainer.classList.remove('hidden');
        } else {
            previewContainer.classList.add('hidden');
        }

        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }

    function previewCategoryImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreviewContainer').classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
