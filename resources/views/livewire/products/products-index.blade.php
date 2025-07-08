<div>
    {{-- In work, do what you enjoy. --}}
    <div class="flex justify-between flex-wrap items-center">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Products Management
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            @can('create', App\Models\Products\Product::class)
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
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">Name</th>
                                        {{-- <th scope="col" class="table-th">Prefix</th> --}}
                                        <th scope="col" class="table-th">Products</th>
                                        <th scope="col" class="table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($categories as $category)
                                        <tr>
                                            <td class="table-td">{{ $category->name }}</td>
                                            {{-- <td class="table-td">{{ $department->prefix_code }}</td> --}}
                                            <td class="table-td">{{ $category->products_count }}</td>
                                            <td class="table-td">
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
                                            <td colspan="4" class="table-td text-center">No categories found</td>
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
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">Name</th>
                                        <th scope="col" class="table-th">Category</th>
                                        <th scope="col" class="table-th">Spec</th>
                                        <th scope="col" class="table-th">Base Cost</th>
                                        <th scope="col" class="table-th">Actions</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse($products as $product)
                                        <tr>
                                            <td class="table-td">{{ $product->category->name }}</td>
                                            <td class="table-td">{{ $product->spec->name }}</td>
                                            <td class="table-td">{{ $product->name }}</td>
                                            <td class="table-td">{{ $product->base_cost }}</td>
                                            <td class="table-td">
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
                                                        onConfirm: () => $dispatch('deleteProduct', {{ $product->id }})
                                                    })"
                                                        class="action-btn text-danger">
                                                        <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="table-td text-center">No products found</td>
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
