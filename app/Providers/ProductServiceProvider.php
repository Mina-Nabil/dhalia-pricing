<?php

namespace App\Providers;

use App\Exceptions\ProductManagementException;
use App\Models\AppLog;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductCost;
use App\Policies\ProductPolicy;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    // Category methods
    public function getCategories($search = null, $paginate = false)
    {
        Gate::authorize('view-product-list');
        $query = ProductCategory::query()
            ->withCount('products')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->orderBy('name');
        
        AppLog::info('Product categories list viewed', 'Categories loaded');
        return $paginate ? $query->paginate($paginate) : $query->get();
    }

    public function getCategory($id)
    {
        $category = ProductCategory::findOrFail($id);
        Gate::authorize('view-product-list');
        AppLog::info('Product category viewed', 'Category ' . $id . ' viewed', $category);
        return $category;
    }

    public function createCategory($name, $description = null)
    {
        Gate::authorize('create-product');
        try {
            $category = ProductCategory::create(['name' => $name, 'description' => $description]);
            AppLog::info('Product category created', 'Category ' . $name . ' created', $category);
            return $category;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product category creation failed', 'Category ' . $name . ' creation failed');
            throw new ProductManagementException('Product category creation failed');
        }
    }

    public function updateCategory(ProductCategory $category, $name, $description = null)
    {
        Gate::authorize('create-product');
        try {
            $category->update(['name' => $name, 'description' => $description]);
            AppLog::info('Product category updated', 'Category ' . $category->name . ' updated', $category);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product category update failed', 'Category ' . $category->name . ' update failed');
            throw new ProductManagementException('Product category update failed');
        }
    }

    public function deleteCategory(ProductCategory $category)
    {
        Gate::authorize('delete-product', $category);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            throw new ProductManagementException('Cannot delete category with existing products');
        }   
        
        AppLog::info('Product category deleted', 'Category ' . $category->name . ' deleted', $category);
        try {
            $category->delete();
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product category deletion failed', 'Category ' . $category->name . ' deletion failed');
            throw new ProductManagementException('Product category deletion failed');
        }
    }

    // Product methods
    public function getProducts($search = null, $paginate = 10, $categoryId = null)
    {
        Gate::authorize('view-product-list');
        $query = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->bySearch($search);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->byCategory($categoryId);
            });
        AppLog::info('Products list viewed', 'Products loaded');
        return $paginate ? $query->paginate($paginate) : $query->get();
    }

    public function getProduct($id)
    {
        $product = Product::findOrFail($id);
        Gate::authorize('view-product', $product);
        AppLog::info('Product viewed', 'Product ' . $id . ' viewed', $product);
        return $product;
    }

    public function createProduct($name, $categoryId, $baseCost)
    {
        Gate::authorize('create-product');
        try {
            $product = Product::create(['name' => $name, 'product_category_id' => $categoryId, 'base_cost' => $baseCost]);
            AppLog::info('Product created', 'Product ' . $name . ' created', $product);
            return $product;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product creation failed', 'Product ' . $name . ' creation failed');
            throw new ProductManagementException('Product creation failed');
        }
    }

    public function updateProduct(Product $product, $name, $categoryId, $baseCost)
    {
        Gate::authorize('update-product', $product);
        try {
            $product->update(['name' => $name, 'product_category_id' => $categoryId, 'base_cost' => $baseCost]);
            AppLog::info('Product updated', 'Product ' . $product->name . ' updated', $product);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product update failed', 'Product ' . $product->name . ' update failed');
            throw new ProductManagementException('Product update failed');
        }
    }

    public function deleteProduct(Product $product)
    {
        Gate::authorize('delete-product', $product);
        AppLog::info('Product deleted', 'Product ' . $product->name . ' deleted', $product);
        try {
            $product->costs()->delete();
            $product->delete();
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product deletion failed', 'Product ' . $product->name . ' deletion failed');
            throw new ProductManagementException('Product deletion failed');
        }
    }

    public function addProductCost(Product $product, $name, $cost, $isPercentage)
    {
        Gate::authorize('product-update', $product);
        AppLog::info('Product cost created', 'Product cost ' . $name . ' created', $name);
        try {
            $highestCost = $product->costs()->sortByDesc('sort_order')->first();
            $sortOrder = $highestCost ? $highestCost->sort_order + 1 : 0;
            $product->costs()->create([
                'name' => $name,
                'cost' => $cost,
                'is_percentage' => $isPercentage,
                'sort_order' => $sortOrder
            ]);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product cost creation failed', 'Product cost ' . $name . ' creation failed');
            throw new ProductManagementException('Product cost creation failed');
        }
    }

    public function deleteProductCost(ProductCost $productCost)
    {
        Gate::authorize('product-update', $productCost->product);
        AppLog::info('Product cost deleted', 'Product cost deleted for product ' . $productCost->product->name, $productCost);
        try {
            $productCost->delete();
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product cost deletion failed', 'Product cost ' . $productCost->name . ' deletion failed', $productCost);
            throw new ProductManagementException('Product cost deletion failed');
        }
    }

    public function moveProductCostUp(ProductCost $productCost)
    {
        Gate::authorize('product-update', $productCost->product);
        AppLog::info('Product cost moved up', 'Product cost ' . $productCost->name . ' moved up', $productCost);
        try {
            $higherProductCost = $productCost->product->costs()->where('sort_order', '<', $productCost->sort_order)->orderBy('sort_order', 'desc')->first();
            if ($higherProductCost) {
                $productCost->update(['sort_order' => $higherProductCost->sort_order]);
                $higherProductCost->update(['sort_order' => $productCost->sort_order]);
            }
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product cost move up failed', 'Product cost ' . $productCost->name . ' move up failed', $productCost);
            throw new ProductManagementException('Product cost move up failed');
        }
    }

    public function moveProductCostDown(ProductCost $productCost)
    {
        Gate::authorize('product-update', $productCost->product);
        AppLog::info('Product cost moved down', 'Product cost ' . $productCost->name . ' moved down', $productCost);
        try {
            $lowerProductCost = $productCost->product->costs()->where('sort_order', '>', $productCost->sort_order)->orderBy('sort_order', 'asc')->first();
            if ($lowerProductCost) {
                $productCost->update(['sort_order' => $lowerProductCost->sort_order]);
                $lowerProductCost->update(['sort_order' => $productCost->sort_order]);
            }
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product cost move down failed', 'Product cost ' . $productCost->name . ' move down failed', $productCost);
            throw new ProductManagementException('Product cost move down failed');
        }
        }
    
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ProductServiceProvider::class, function ($app) {
            return new ProductServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-product', [ProductPolicy::class, 'view']);
        Gate::define('view-product-list', [ProductPolicy::class, 'viewAny']);
        Gate::define('create-product', [ProductPolicy::class, 'create']);
        Gate::define('update-product', [ProductPolicy::class, 'update']);
        Gate::define('delete-product', [ProductPolicy::class, 'delete']);
    }
}
