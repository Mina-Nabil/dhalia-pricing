<?php

namespace App\Livewire\Products;

use App\Exceptions\ProductManagementException;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductCost;
use App\Models\Products\Ingredient;
use App\Models\Spec;
use App\Providers\ProductServiceProvider;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;

class ProductsShow extends Component
{
    use AlertFrontEnd;

    protected $productService;

    public $product;

    // Product form properties
    public $productName = '';
    public $selectedCategoryId = '';
    public $selectedSpecId = '';
    public $baseCost = '';

    // Cost form properties
    public $costName = '';
    public $costAmount = '';
    public $isPercentage = false;
    public $isFixed = false;
    public $costType = 'per_ton'; // 'per_ton', 'percentage', 'fixed'

    // Edit cost properties
    public $showEditCostModal = false;
    public $editingCostId = null;
    public $editCostName = '';
    public $editCostAmount = '';
    public $editCostType = 'per_ton';

    // Ingredient form properties
    public $ingredientName = '';
    public $ingredientCost = '';
    public $addIngredientMode = false;

    // Edit mode flags
    public $editMode = false;
    public $addCostMode = false;

    public function boot()
    {
        $this->productService = app(ProductServiceProvider::class);
    }

    public function mount($product_id)
    {
        $this->product = $this->productService->getProduct($product_id);
        $this->loadProductData();
    }

    public function loadProductData()
    {
        $this->productName = $this->product->name;
        $this->selectedCategoryId = $this->product->product_category_id;
        $this->selectedSpecId = $this->product->spec_id;
        $this->baseCost = $this->product->base_cost;
    }

    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;

        if (!$this->editMode) {
            // Reset form data when exiting edit mode
            $this->loadProductData();
            $this->resetValidation();
        }
    }

    public function toggleAddCostMode()
    {
        $this->addCostMode = !$this->addCostMode;

        if (!$this->addCostMode) {
            // Reset cost form data when exiting add cost mode
            $this->resetCostFormFields();
            $this->resetValidation();
        }
    }

    public function updateProduct()
    {
        $this->validate([
            'productName' => 'required|string|max:255',
            'selectedCategoryId' => 'required|exists:product_categories,id',
            'selectedSpecId' => 'required|exists:specs,id',
            'baseCost' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        try {
            $this->productService->updateProduct($this->product, $this->productName, $this->selectedCategoryId, $this->baseCost, $this->selectedSpecId);

            // Refresh the product data
            $this->product = $this->productService->getProduct($this->product->id);
            $this->loadProductData();

            $this->editMode = false;
            $this->alert('success', 'Product updated successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function addProductCost()
    {
        $this->validate([
            'costName' => 'required|string|max:255',
            'costAmount' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'costType' => 'required|in:per_ton,percentage,fixed',
        ]);

        // Set boolean values based on cost type
        $this->isPercentage = ($this->costType === 'percentage');
        $this->isFixed = ($this->costType === 'fixed');

        try {
            $this->productService->addProductCost($this->product, $this->costName, $this->costAmount, $this->isPercentage, $this->isFixed);

            // Refresh the product data
            $this->product = $this->productService->getProduct($this->product->id);

            $this->addCostMode = false;
            $this->resetCostFormFields();
            $this->mount($this->product->id);

            $this->alert('success', 'Cost added successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function moveProductCostUp($costId)
    {
        try {
            $productCost = ProductCost::findOrFail($costId);
            $this->productService->moveProductCostUp($productCost);

            // Refresh the product data
            $this->product = $this->productService->getProduct($this->product->id);
            $this->mount($this->product->id);
            $this->alert('success', 'Cost moved up successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function moveProductCostDown($costId)
    {
        try {
            $productCost = ProductCost::findOrFail($costId);
            $this->productService->moveProductCostDown($productCost);

            // Refresh the product data
            $this->product = $this->productService->getProduct($this->product->id);
            $this->mount($this->product->id);

            $this->alert('success', 'Cost moved down successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function deleteProductCost($costId)
    {
        try {
            $productCost = ProductCost::findOrFail($costId);
            $this->productService->deleteProductCost($productCost);

            // Refresh the product data
            $this->product = $this->productService->getProduct($this->product->id);
            $this->mount($this->product->id);
            $this->alert('success', 'Cost deleted successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function cancelEdit()
    {
        $this->editMode = false;
        $this->loadProductData();
        $this->resetValidation();
    }

    public function cancelAddCost()
    {
        $this->addCostMode = false;
        $this->resetCostFormFields();
        $this->resetValidation();
    }

    public function openEditCostModal($costId)
    {
        $cost = ProductCost::findOrFail($costId);
        $this->editingCostId = $cost->id;
        $this->editCostName = $cost->name;
        $this->editCostAmount = $cost->cost;
        
        // Set the cost type based on the cost properties
        if ($cost->is_percentage) {
            $this->editCostType = 'percentage';
        } elseif ($cost->is_fixed) {
            $this->editCostType = 'fixed';
        } else {
            $this->editCostType = 'per_ton';
        }
        
        $this->showEditCostModal = true;
    }

    public function updateProductCost()
    {
        $this->validate([
            'editCostName' => 'required|string|max:255',
            'editCostAmount' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'editCostType' => 'required|in:per_ton,percentage,fixed',
        ]);

        try {
            $productCost = ProductCost::findOrFail($this->editingCostId);
            
            // Set boolean values based on cost type
            $isPercentage = ($this->editCostType === 'percentage');
            $isFixed = ($this->editCostType === 'fixed');
            
            $this->productService->updateProductCost($productCost, $this->editCostName, $this->editCostAmount, $isPercentage, $isFixed);

            // Refresh the product data
            $this->product = $this->productService->getProduct($this->product->id);

            $this->closeEditCostModal();
            $this->alert('success', 'Cost updated successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function closeEditCostModal()
    {
        $this->showEditCostModal = false;
        $this->editingCostId = null;
        $this->editCostName = '';
        $this->editCostAmount = '';
        $this->editCostType = 'per_ton';
        $this->resetValidation();
    }

    public function toggleAddIngredientMode()
    {
        $this->addIngredientMode = !$this->addIngredientMode;

        if (!$this->addIngredientMode) {
            // Reset ingredient form data when exiting add ingredient mode
            $this->resetIngredientFormFields();
            $this->resetValidation();
        }
    }

    public function addProductIngredient()
    {
        $this->validate([
            'ingredientName' => 'required|string|max:255',
            'ingredientCost' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        try {
            $this->productService->addProductIngredient($this->product, $this->ingredientName, $this->ingredientCost);

            // Refresh the product data
            $this->product = $this->productService->getProduct($this->product->id);

            $this->addIngredientMode = false;
            $this->resetIngredientFormFields();
            $this->mount($this->product->id);

            $this->alert('success', 'Ingredient added successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function deleteProductIngredient($ingredientId)
    {
        try {
            $ingredient = Ingredient::findOrFail($ingredientId);
            $this->productService->deleteProductIngredient($ingredient);

            // Refresh the product data
            $this->product = $this->productService->getProduct($this->product->id);
            $this->mount($this->product->id);
            $this->alert('success', 'Ingredient deleted successfully');
        } catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function cancelAddIngredient()
    {
        $this->addIngredientMode = false;
        $this->resetIngredientFormFields();
        $this->resetValidation();
    }

    private function resetCostFormFields()
    {
        $this->costName = '';
        $this->costAmount = '';
        $this->isPercentage = false;
        $this->isFixed = false;
        $this->costType = 'per_ton';
    }

    private function resetIngredientFormFields()
    {
        $this->ingredientName = '';
        $this->ingredientCost = '';
    }

    public function render()
    {
        $categories = $this->productService->getCategories();
        $specs = app(\App\Providers\SpecServiceProvider::class)->getSpecs(null, false);

        return view('livewire.products.products-show', [
            'categories' => $categories,
            'specs' => $specs,
        ]);
    }
}
