<div>
    {{-- In work, do what you enjoy. --}}
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Products Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create-product')
                <button wire:click="exportProducts"
                    class="btn inline-flex justify-center btn-success dark:bg-green-600 dark:text-slate-300 m-1"
                    wire:loading.attr="disabled" wire:target="exportProducts">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:download-bold" wire:loading.remove wire:target="exportProducts"></iconify-icon>
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2 animate-spin" icon="ph:spinner-bold" wire:loading wire:target="exportProducts"></iconify-icon>
                    <span wire:loading.remove wire:target="exportProducts">Export Products</span>
                    <span wire:loading wire:target="exportProducts">Exporting...</span>
                </button>
            @endcan
            @can('create-product')
                <div class="flex items-center space-x-2">
                    <input type="file" wire:model="importFile" accept=".xlsx,.xls" class="hidden" id="importFileInput">
                    <button onclick="document.getElementById('importFileInput').click()"
                        class="btn inline-flex justify-center btn-warning dark:bg-yellow-600 dark:text-slate-300 m-1">
                        <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:upload-bold"></iconify-icon>
                        Import Products
                    </button>
                    @if($importFile)
                        <button wire:click="importProducts"
                            class="btn inline-flex justify-center btn-info dark:bg-blue-600 dark:text-slate-300 m-1"
                            wire:loading.attr="disabled" wire:target="importProducts">
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:check-bold" wire:loading.remove wire:target="importProducts"></iconify-icon>
                            <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2 animate-spin" icon="ph:spinner-bold" wire:loading wire:target="importProducts"></iconify-icon>
                            <span wire:loading.remove wire:target="importProducts">Import Products</span>
                            <span wire:loading wire:target="importProducts">Importing...</span>
                        </button>
                    @endif
                </div>
                <button wire:click="openNewCategory"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Category
                </button>
                <button wire:click="openNewProduct"
                    class="btn inline-flex justify-center btn-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                    <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="ph:plus-bold"></iconify-icon>
                    Create Product
                </button>
            @endcan
        </div>
    </div>

    <!-- Import File Information -->
    @if($importFile)
        <div class="mb-4">
            <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                <div class="flex items-center">
                    <iconify-icon class="text-blue-600 text-xl mr-2" icon="ph:file-bold"></iconify-icon>
                    <span class="text-blue-800">Selected file: {{ $importFile->getClientOriginalName() }}</span>
                </div>
                @error('importFile')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>
    @endif

    <!-- File Upload Progress -->
    <div wire:loading wire:target="importFile" class="mb-4">
        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
            <div class="flex items-center">
                <iconify-icon class="text-yellow-600 text-xl mr-2 animate-spin" icon="ph:spinner-bold"></iconify-icon>
                <span class="text-yellow-800">Uploading file...</span>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="flex flex-wrap sm:flex-nowrap justify-between space-x-3 rtl:space-x-reverse mb-6">
        <div class="flex-0 w-full sm:w-auto mb-3 sm:mb-0">
            <div class="relative">
                <input type="text" class="form-control pl-10" placeholder="Search by category or product..."
                    wire:model.live.debounce.300ms="search">
                <span class="absolute right-0 top-0 w-9 h-full flex items-center justify-center text-slate-400">
                    <iconify-icon icon="heroicons-solid:search"></iconify-icon>
                </span>
            </div>
        </div>
    </div>

    <!-- Grid Layout for Tables -->
    <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-5 mb-5 text-wrap">
        <!-- Departments Table -->
        <div class="card mb-6">
            <header class="card-header noborder">
                <h4 class="card-title">Categories</h4>
            </header>
            <div class="card-body px-6 pb-6">
                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block align-middle" style="min-width: 100%;">
                        <div class="overflow-hidden">
                            <table class="w-full divide-y divide-slate-100 dark:divide-slate-700" style="min-width: 400px; table-layout: auto;">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th whitespace-nowrap">Name</th>
                                        {{-- <th scope="col" class="table-th">Prefix</th> --}}
                                        <th scope="col" class="table-th whitespace-nowrap">Products</th>
                                        <th scope="col" class="table-th whitespace-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($categories as $category)
                                        <tr>
                                            <td class="table-td whitespace-nowrap">{{ $category->name }}</td>
                                            {{-- <td class="table-td">{{ $department->prefix_code }}</td> --}}
                                            <td class="table-td whitespace-nowrap">{{ $category->products_count }}</td>
                                            <td class="table-td whitespace-nowrap">
                                                <div class="flex space-x-3 rtl:space-x-reverse">
                                                    <button wire:click="openEditCategorySec({{ $category->id }})"
                                                        class="action-btn text-primary">
                                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                    </button>
                                                    <button wire:click="confirmDeleteCategory({{ $category->id }})"
                                                        class="action-btn text-danger">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="table-td text-center whitespace-nowrap">No categories found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Positions Table -->
        <div class="card col-span-2">
            <header class="card-header noborder">
                <h4 class="card-title">Products</h4>
            </header>
            <div class="card-body px-6 pb-6">
                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block align-middle" style="min-width: 100%;">
                        <div class="overflow-hidden">
                            <table class="w-full divide-y divide-slate-100 dark:divide-slate-700" style="min-width: 700px; table-layout: auto;">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th whitespace-nowrap">Name</th>
                                        <th scope="col" class="table-th whitespace-nowrap">Category</th>
                                        <th scope="col" class="table-th whitespace-nowrap">Spec</th>
                                        @can('view-product-costs')
                                            <th scope="col" class="table-th whitespace-nowrap">Base Cost</th>
                                        @endcan
                                        <th scope="col" class="table-th whitespace-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($products as $product)
                                        <tr>
                                            <td class="table-td whitespace-nowrap hover:cursor-pointer hover:underline" wire:click="goToProductShow({{ $product->id }})">{{ $product->name }}</td>
                                            <td class="table-td whitespace-nowrap">{{ $product->category->name }}</td>
                                            <td class="table-td whitespace-nowrap">{{ $product->spec->name }}</td>
                                            @can('view-product-costs')
                                                <td class="table-td whitespace-nowrap">{{ number_format($product->base_cost, 2) }}</td>
                                            @endcan
                                            <td class="table-td whitespace-nowrap">
                                                <div class="flex space-x-3 rtl:space-x-reverse">
                                                    <button wire:click="goToProductShow({{ $product->id }})"
                                                        class="action-btn text-primary">
                                                        <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                    </button>
                                                    <button wire:click="updateThisProduct({{ $product->id }})"
                                                        class="action-btn text-primary">
                                                        <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                    </button>
                                                    <button wire:click="$dispatch('showConfirmationModal', {
                                                        title: 'Delete Product',
                                                        message: 'Are you sure you want to delete this product?',
                                                        callback: 'deleteProduct',
                                                        params: {{ $product->id }}
                                                    })"
                                                        class="action-btn text-danger">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="table-td text-center whitespace-nowrap">No products found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    {{ $products->links('vendor.livewire.simple-bootstrap') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    @if ($newCategoryModal)
        <x-modal wire:model="newCategoryModal">
            <x-slot name="title">
                Create New Category
            </x-slot>
            <x-slot name="content">
                <x-text-input wire:model="categoryName" label="Category Name" 
                    errorMessage="{{ $errors->first('categoryName') }}" />
                
                                 <div class="form-group">
                     <label for="categoryDescription" class="form-label">Description (Optional)</label>
                     <textarea id="categoryDescription" 
                         class="form-control @error('categoryDescription') !border-danger-500 @enderror"
                         wire:model="categoryDescription" rows="3"></textarea>
                     @error('categoryDescription')
                         <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                     @enderror
                 </div>
            </x-slot>
            <x-slot name="footer">
                <x-primary-button wire:click.prevent="addNewCategory" loadingFunction="addNewCategory">
                    Create Category
                </x-primary-button>
            </x-slot>
        </x-modal>
    @endif

    <!-- Edit Category Modal -->
    @if ($editCategoryModal)
        <x-modal wire:model="editCategoryModal">
            <x-slot name="title">
                Edit Category
            </x-slot>
            <x-slot name="content">
                <x-text-input wire:model="categoryName" label="Category Name" 
                    errorMessage="{{ $errors->first('categoryName') }}" />
                
                <div class="form-group">
                    <label for="categoryDescription" class="form-label">Description (Optional)</label>
                    <textarea id="categoryDescription" 
                        class="form-control @error('categoryDescription') !border-danger-500 @enderror"
                        wire:model="categoryDescription" rows="3"></textarea>
                    @error('categoryDescription')
                        <span class="font-Inter text-sm text-danger-500 pt-2 inline-block">{{ $message }}</span>
                    @enderror
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-primary-button wire:click.prevent="addNewCategory" loadingFunction="addNewCategory">
                    Update Category
                </x-primary-button>
            </x-slot>
        </x-modal>
    @endif

    <!-- New Product Modal -->
    @if ($newProductModal)
        <x-modal wire:model="newProductModal">
            <x-slot name="title">
                Create New Product
            </x-slot>
            <x-slot name="content">
                <x-text-input wire:model="productName" label="Product Name" 
                    errorMessage="{{ $errors->first('productName') }}" />
                
                <x-select wire:model="selectedCategoryId" label="Category" 
                    errorMessage="{{ $errors->first('selectedCategoryId') }}">
                    <option value="">Select a category</option>
                    @foreach ($allCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-select>

                <x-select wire:model="selectedSpecId" label="Spec" 
                    errorMessage="{{ $errors->first('selectedSpecId') }}">
                    <option value="">Select a spec</option>
                    @foreach ($allSpecs as $spec)
                        <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                    @endforeach
                </x-select>

                <x-text-input wire:model="baseCost" label="Base Cost" type="number" step="0.01" 
                    errorMessage="{{ $errors->first('baseCost') }}" placeholder="e.g., 100.00" />
            </x-slot>
            <x-slot name="footer">
                <x-primary-button wire:click.prevent="addNewProduct" loadingFunction="addNewProduct">
                    Create Product
                </x-primary-button>
            </x-slot>
        </x-modal>
    @endif

    <!-- Edit Product Modal -->
    @if ($editProductModal)
        <x-modal wire:model="editProductModal">
            <x-slot name="title">
                Edit Product
            </x-slot>
            <x-slot name="content">
                <x-text-input wire:model="productName" label="Product Name" 
                    errorMessage="{{ $errors->first('productName') }}" />
                
                <x-select wire:model="selectedCategoryId" label="Category" 
                    errorMessage="{{ $errors->first('selectedCategoryId') }}">
                    <option value="">Select a category</option>
                    @foreach ($allCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-select>

                <x-select wire:model="selectedSpecId" label="Spec" 
                    errorMessage="{{ $errors->first('selectedSpecId') }}">
                    <option value="">Select a spec</option>
                    @foreach ($allSpecs as $spec)
                        <option value="{{ $spec->id }}">{{ $spec->name }}</option>
                    @endforeach
                </x-select>

                <x-text-input wire:model="baseCost" label="Base Cost" type="number" step="0.01" 
                    errorMessage="{{ $errors->first('baseCost') }}" placeholder="e.g., 100.00" />
            </x-slot>
            <x-slot name="footer">
                <x-primary-button wire:click.prevent="addNewProduct" loadingFunction="addNewProduct">
                    Update Product
                </x-primary-button>
            </x-slot>
        </x-modal>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($deleteConfirmationModal)
        <x-modal wire:model="deleteConfirmationModal">
            <x-slot name="title">
                Confirm Delete
            </x-slot>
            <x-slot name="content">
                <p>Are you sure you want to delete this {{ $itemTypeToDelete }}? This action cannot be undone.</p>

                @if ($itemTypeToDelete === 'category')
                    <p class="text-danger-500">Warning: If this category has associated products, it cannot be deleted.</p>
                @endif

                @if ($itemTypeToDelete === 'product')
                    <div class="text-danger-500">
                        <p>Warning: Deleting this product will also remove all associated costs.</p>
                    </div>
                @endif
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="closeDeleteConfirmationModal">
                    Cancel
                </x-secondary-button>
                <x-danger-button wire:click.prevent="confirmDelete" loadingFunction="confirmDelete">
                    Delete
                </x-danger-button>
            </x-slot>
        </x-modal>
    @endif
</div>
