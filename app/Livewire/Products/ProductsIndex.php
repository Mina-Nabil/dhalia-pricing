<?php

namespace App\Livewire\Products;

use App\Exceptions\ProductManagementException;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Providers\ProductServiceProvider;
use App\Providers\SpecServiceProvider;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ProductsIndex extends Component
{
    use WithPagination, AlertFrontEnd, WithFileUploads;

    /**
     * @var ProductServiceProvider
     */
    protected $productService;

    /**
     * @var SpecServiceProvider
     */
    protected $specService;

    public $search = '';
    
    // File upload for import
    public $importFile;
    
    // Modal states
    public $newCategoryModal = false;
    public $editCategoryModal = false;
    public $newProductModal = false;
    public $editProductModal = false;
    public $deleteConfirmationModal = false;
    
    // Edit mode flags
    public $categoryEditMode = false;
    public $productEditMode = false;
    
    // Selected items
    public $selectedCategory;
    public $selectedProduct;
    
    // Category form properties
    public $categoryName = '';
    public $categoryDescription = '';
    
    // Product form properties
    public $productName = '';
    public $selectedCategoryId = '';
    public $selectedSpecId = '';
    public $baseCost = '';
    
    // Delete confirmation
    public $itemTypeToDelete = '';
    public $itemIdToDelete = null;

    protected $paginationTheme = 'simple-bootstrap';

    protected $listeners = ['deleteProduct', 'deleteCategory'];

    public function boot()
    {
        $this->productService = app(ProductServiceProvider::class);
        $this->specService = app(SpecServiceProvider::class);
    }

    public function mount()
    {
        $this->authorize('viewAny', Product::class);
    }

    protected function rules()
    {
        $rules = [];

        // Category validation rules
        if ($this->newCategoryModal || $this->editCategoryModal) {
            $rules = array_merge($rules, [
                'categoryName' => 'required|string|max:255',
                'categoryDescription' => 'nullable|string|max:500',
            ]);
        }

        // Product validation rules
        if ($this->newProductModal || $this->editProductModal) {
            $rules = array_merge($rules, [
                'productName' => 'required|string|max:255',
                'selectedCategoryId' => 'required|exists:product_categories,id',
                'baseCost' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
                'selectedSpecId' => 'required|exists:specs,id',
            ]);
        }

        // Import file validation
        if ($this->importFile) {
            $rules = array_merge($rules, [
                'importFile' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            ]);
        }

        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Category methods
    public function openNewCategory()
    {
        $this->resetCategoryFormFields();
        $this->categoryEditMode = false;
        $this->newCategoryModal = true;
    }

    public function openEditCategorySec($categoryId)
    {
        try {
            $category = $this->productService->getCategory($categoryId);
            
            $this->selectedCategory = $category;
            $this->categoryName = $category->name;
            $this->categoryDescription = $category->description;
            $this->categoryEditMode = true;
            $this->editCategoryModal = true;
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to load category data');
        }
    }

    public function addNewCategory()
    {
        $this->validate();

        try {
            if ($this->categoryEditMode) {
                $this->productService->updateCategory($this->selectedCategory, $this->categoryName, $this->categoryDescription);
                $this->alert('success', 'Category updated successfully');
            } else {
                $this->productService->createCategory($this->categoryName, $this->categoryDescription);
                $this->alert('success', 'Category created successfully');
            }

            $this->resetCategoryFormFields();
            $this->closeNewCategorySec();
            $this->closeEditCategorySec();
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function confirmDeleteCategory($categoryId)
    {
        $this->itemTypeToDelete = 'category';
        $this->itemIdToDelete = $categoryId;
        $this->deleteConfirmationModal = true;
    }

    public function deleteCategory($categoryId)
    {
        try {
            $category = $this->productService->getCategory($categoryId);
            $this->productService->deleteCategory($category);
            $this->alert('success', 'Category deleted successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    // Product methods
    public function goToProductShow($productId)
    {
        return redirect()->route('products.show', $productId);
    }

    public function openNewProduct()
    {
        $this->resetProductFormFields();
        $this->productEditMode = false;
        $this->newProductModal = true;
    }

    public function openProductShow($productId)
    {
        // Redirect to product show page or modal
        return redirect()->route('products.show', $productId);
    }

    public function updateThisProduct($productId)
    {
        try {
            $product = $this->productService->getProduct($productId);
            
            $this->selectedProduct = $product;
            $this->productName = $product->name;
            $this->selectedCategoryId = $product->product_category_id;
            $this->baseCost = $product->base_cost;
            $this->selectedSpecId = $product->spec_id;
            $this->productEditMode = true;
            $this->editProductModal = true;
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to load product data');
        }
    }

    public function addNewProduct()
    {
        $this->validate();

        try {
            if ($this->productEditMode) {
                $this->productService->updateProduct($this->selectedProduct, $this->productName, $this->selectedCategoryId, $this->baseCost, $this->selectedSpecId);
                $this->alert('success', 'Product updated successfully');
            } else {
                $this->productService->createProduct($this->productName, $this->selectedCategoryId, $this->baseCost, $this->selectedSpecId);
                $this->alert('success', 'Product created successfully');
            }

            $this->resetProductFormFields();
            $this->closeNewProductSec();
            $this->closeEditProductSec();
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function deleteProduct($productId)
    {
        try {
            $product = $this->productService->getProduct($productId);
            $this->productService->deleteProduct($product);
            $this->alert('success', 'Product deleted successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    // Export and Import methods
    public function exportProducts()
    {
        try {
            $this->authorize('viewAny', Product::class);
            
            $filePath = $this->productService->exportProductsToExcel();
            
            $this->alert('success', 'Products exported successfully');
            
            return response()->download($filePath)->deleteFileAfterSend(true);
            
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function importProducts()
    {
        try {
            $this->authorize('create', Product::class);
            
            // Validate the uploaded file
            $this->validate([
                'importFile' => 'required|file|mimes:xlsx,xls|max:10240'
            ]);
            
            if (!$this->importFile) {
                $this->alert('error', 'Please select a file to import');
                return;
            }
            
            // Get the temporary file path
            $filePath = $this->importFile->getRealPath();
            
            // Import the products
            $this->productService->importProductsFromExcel($filePath);
            
            $this->alert('success', 'Products imported successfully');
            
            // Reset the file input
            $this->importFile = null;
            
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // Modal control methods
    public function closeNewCategorySec()
    {
        $this->newCategoryModal = false;
        $this->resetCategoryFormFields();
    }

    public function closeEditCategorySec()
    {
        $this->editCategoryModal = false;
        $this->resetCategoryFormFields();
    }

    public function closeNewProductSec()
    {
        $this->newProductModal = false;
        $this->resetProductFormFields();
    }

    public function closeEditProductSec()
    {
        $this->editProductModal = false;
        $this->resetProductFormFields();
    }

    public function closeDeleteConfirmationModal()
    {
        $this->deleteConfirmationModal = false;
        $this->itemTypeToDelete = '';
        $this->itemIdToDelete = null;
    }

    public function confirmDelete()
    {
        if ($this->itemTypeToDelete === 'category') {
            $this->deleteCategory($this->itemIdToDelete);
        } elseif ($this->itemTypeToDelete === 'product') {
            $this->deleteProduct($this->itemIdToDelete);
        }
        
        $this->closeDeleteConfirmationModal();
    }

    // Form reset methods
    private function resetCategoryFormFields()
    {
        $this->categoryName = '';
        $this->categoryDescription = '';
        $this->selectedCategory = null;
        $this->resetValidation();
    }

    private function resetProductFormFields()
    {
        $this->productName = '';
        $this->selectedCategoryId = '';
        $this->baseCost = '';
        $this->selectedProduct = null;
        $this->resetValidation();
    }

    public function render()
    {
        $categories = $this->productService->getCategories($this->search);
        $products = $this->productService->getProducts($this->search, 10);
        $allCategories = $this->productService->getCategories(forDropdown: true);
        $allSpecs = $this->specService->getSpecs(forDropdown: true);


        return view('livewire.products.products-index', [
            'categories' => $categories,
            'products' => $products,
            'allCategories' => $allCategories,
            'allSpecs' => $allSpecs,
        ]);
    }
}
