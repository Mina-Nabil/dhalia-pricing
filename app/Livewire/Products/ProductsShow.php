<?php

namespace App\Livewire\Products;

use App\Exceptions\ProductManagementException;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Providers\ProductServiceProvider;
use App\Traits\AlertFrontEnd;
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
    public $baseCost = '';
    
    // Edit mode flag
    public $editMode = false;

    public function __construct()
    {
        $this->productService = app(ProductServiceProvider::class);
    }

    public function mount($product_id)
    {
        $this->product = $this->productService->getProduct($product_id);
        $this->loadProductData();
    }

    protected function rules()
    {
        return [
            'productName' => 'required|string|max:255',
            'selectedCategoryId' => 'required|exists:product_categories,id',
            'baseCost' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ];
    }

    public function loadProductData()
    {
        $this->productName = $this->product->name;
        $this->selectedCategoryId = $this->product->product_category_id;
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

    public function updateProduct()
    {
        $this->validate();

        try {
            $this->productService->updateProduct($this->product, $this->productName, $this->selectedCategoryId, $this->baseCost);
            
            // Refresh the product data
            $this->product = $this->productService->getProduct($this->product->id);
            $this->loadProductData();
            
            $this->editMode = false;
            $this->alert('success', 'Product updated successfully');
        } catch(AuthorizationException $e){
            $this->alert('error', $e->getMessage());
        }
            catch (ProductManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function cancelEdit()
    {
        $this->editMode = false;
        $this->loadProductData();
        $this->resetValidation();
    }

    public function render()
    {
        $categories = $this->productService->getCategories();
        
        return view('livewire.products.products-show', [
            'categories' => $categories,
        ]);
    }
}
