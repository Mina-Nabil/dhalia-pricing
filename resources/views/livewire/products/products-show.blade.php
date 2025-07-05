<div>
    <div class="flex justify-between flex-wrap items-center mb-6">
        <div class="md:mb-6 mb-4 flex space-x-3 rtl:space-x-reverse">
            <h4 class="font-medium lg:text-2xl text-xl capitalize text-slate-900 inline-block ltr:pr-4 rtl:pl-4">
                Product Details
            </h4>
        </div>
        <div class="flex sm:space-x-4 space-x-2 sm:justify-end items-center md:mb-6 mb-4 rtl:space-x-reverse">
            <a href="{{ route('products.index') }}"
                class="btn inline-flex justify-center btn-outline-dark dark:bg-slate-700 dark:text-slate-300 m-1">
                <iconify-icon class="text-xl ltr:mr-2 rtl:ml-2" icon="heroicons:arrow-left"></iconify-icon>
                Back to Products
            </a>
        </div>
    </div>

    <!-- Three Cards Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Product Info and Total Cost Cards -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Product Information Card -->
            <x-card title="Product Information">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Product
                            Name</label>
                        <p class="text-slate-900 dark:text-white text-lg font-medium mt-1">{{ $product->name }}</p>
                    </div>
                    <div>
                        <label
                            class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Category</label>
                        <p class="text-slate-900 dark:text-white text-lg font-medium mt-1">
                            {{ $product->category->name }}</p>
                    </div>
                    <div>
                        <label
                            class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Base
                            Cost</label>
                        <p class="text-slate-900 dark:text-white text-lg font-semibold mt-1">
                            ${{ number_format($product->base_cost, 2) }}</p>
                    </div>
                    <div>
                        <label
                            class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Product
                            ID</label>
                        <p class="text-slate-500 dark:text-slate-400 text-lg mt-1">#{{ $product->id }}</p>
                    </div>
                </div>

                @if ($product->category->description)
                    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <label
                            class="form-label font-medium text-slate-600 dark:text-slate-400 text-sm uppercase tracking-wide">Category
                            Description</label>
                        <p class="text-slate-600 dark:text-slate-400 mt-2 text-base">
                            {{ $product->category->description }}</p>
                    </div>
                @endif

            </x-card>

            <!-- Total Cost Card -->
            <x-card title="Cost Breakdown">
                <x-slot name="tools">
                    @if (!$addCostMode)
                        <button wire:click="toggleAddCostMode"
                            class="btn btn-sm text-white hover:bg-white hover:text-slate-800">
                            <iconify-icon class="text-lg" icon="heroicons:plus" />
                        </button>
                    @endif
                </x-slot>

                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">Base Cost:</span>
                        <span
                            class="font-semibold text-slate-900 dark:text-white text-lg">${{ number_format($product->base_cost, 2) }}</span>
                    </div>

                    @if ($product->costs->count() > 0)
                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                            <p
                                class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-4 uppercase tracking-wide">
                                Additional Costs:</p>
                            @foreach ($product->costs as $index => $cost)
                                <div class="flex justify-between items-center py-2 group hover:bg-slate-100 dark:hover:bg-slate-800">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-slate-600 dark:text-slate-400">{{ $cost->name }}:</span>
                                        <span class="text-slate-900 dark:text-white font-medium">
                                            @if ($cost->is_percentage)
                                                {{ $cost->cost }}%
                                            @else
                                                ${{ number_format($cost->cost, 2) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <!-- Move Up Button -->
                                        @if ($index > 0)
                                            <button wire:click="moveProductCostUp({{ $cost->id }})"
                                                class="action-btn text-slate-500 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400"
                                                title="Move Up">
                                                <iconify-icon icon="heroicons:chevron-up" class="text-sm"></iconify-icon>
                                            </button>
                                        @endif
                                        
                                        <!-- Move Down Button -->
                                        @if ($index < $product->costs->count() - 1)
                                            <button wire:click="moveProductCostDown({{ $cost->id }})"
                                                class="action-btn text-slate-500 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400"
                                                title="Move Down">
                                                <iconify-icon icon="heroicons:chevron-down" class="text-sm"></iconify-icon>
                                            </button>
                                        @endif
                                        
                                        <!-- Delete Button -->
                                        <button wire:click="deleteProductCost({{ $cost->id }})"
                                            class="action-btn text-slate-500 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400"
                                            title="Delete Cost">
                                            <iconify-icon icon="heroicons:trash" class="text-sm"></iconify-icon>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add Cost Form -->
                    @if ($addCostMode)
                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                            <form wire:submit.prevent="addProductCost">
                                <div class="space-y-4">
                                    <x-text-input wire:model="costName" 
                                                  label="Cost Name" 
                                                  errorMessage="{{ $errors->first('costName') }}" 
                                                  placeholder="e.g., Shipping, Tax, etc." />
                                    
                                    <x-text-input wire:model="costAmount" 
                                                  label="Cost Amount" 
                                                  type="number" 
                                                  step="0.01" 
                                                  errorMessage="{{ $errors->first('costAmount') }}" 
                                                  placeholder="100.00" />
                                    
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" 
                                               wire:model="isPercentage" 
                                               id="isPercentage" 
                                               class="form-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                        <label for="isPercentage" class="text-sm text-slate-600 dark:text-slate-400">
                                            Is this a percentage?
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-3 mt-6">
                                    <x-primary-button type="submit" 
                                                      loadingFunction="addProductCost">
                                        Add Cost
                                    </x-primary-button>
                                    <x-secondary-button wire:click="cancelAddCost" 
                                                        type="button">
                                        Cancel
                                    </x-secondary-button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                        <div class="flex justify-between items-center py-2">
                            <span class="text-xl font-bold text-slate-900 dark:text-white">Total Cost:</span>
                            <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                ${{ number_format($product->total_cost, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right Column: Update Form Card -->
        <div class="lg:col-span-1">
            <x-card title="Update Product">
                <x-slot name="tools">
                    @if (!$editMode)
                        <button wire:click="toggleEditMode"
                            class="btn btn-sm text-white  hover:bg-white hover:text-slate-800">
                            <iconify-icon class="text-lg" icon="heroicons:pencil-square" />
                        </button>
                    @endif
                </x-slot>

                @if ($editMode)
                    <form wire:submit.prevent="updateProduct">
                        <div class="space-y-6">
                            <x-text-input wire:model="productName" label="Product Name"
                                errorMessage="{{ $errors->first('productName') }}" />

                            <x-select wire:model="selectedCategoryId" label="Category"
                                errorMessage="{{ $errors->first('selectedCategoryId') }}">
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-select>

                            <x-text-input wire:model="baseCost" label="Base Cost" type="number" step="0.01"
                                errorMessage="{{ $errors->first('baseCost') }}" placeholder="e.g., 100.00" />
                        </div>

                        <div class="flex space-x-3 mt-8">
                            <x-primary-button type="submit" loadingFunction="updateProduct">
                                Update Product
                            </x-primary-button>
                            <x-secondary-button wire:click="cancelEdit" type="button">
                                Cancel
                            </x-secondary-button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-12">
                        <div class="mb-6">
                            <iconify-icon class="text-6xl text-slate-400 dark:text-slate-600"
                                icon="heroicons:document-text"></iconify-icon>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 mb-4 text-base">
                            Click the "Edit" button to update this product's information.
                        </p>
                        <div class="text-sm text-slate-400 dark:text-slate-500">
                            <p>Current values are displayed in the information card.</p>
                        </div>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
