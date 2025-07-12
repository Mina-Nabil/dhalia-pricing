<?php

namespace App\Providers;

use App\Exceptions\ProductManagementException;
use App\Models\AppLog;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductCost;
use App\Models\Products\Ingredient;
use App\Models\Spec;
use App\Policies\ProductPolicy;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductServiceProvider extends ServiceProvider
{
    // Category methods
    public function getCategories($search = null, $paginate = false, $forDropdown = false)
    {
        $query = ProductCategory::query()
            ->withCount('products')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->orderBy('name');
        if (!$forDropdown) {
            Gate::authorize('view-product-list');
            AppLog::info('Product categories list viewed', 'Categories loaded');
        }
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
    public function getProducts($search = null, $paginate = 10, $categoryId = null, $forDropdown = false)
    {
        $query = Product::query()
            ->with('spec', 'category')
            ->when($search, function ($query) use ($search) {
                $query->bySearch($search);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->byCategory($categoryId);
            })
            ->orderBy('product_category_id')
            ->orderBy('spec_id')
            ->orderBy('name');
        if (!$forDropdown) {
            Gate::authorize('view-product-list');
            AppLog::info('Products list viewed', 'Products loaded');
        }
        return $paginate ? $query->paginate($paginate) : $query->get();
    }

    public function getProduct($id)
    {
        $product = Product::with('spec', 'category', 'costs', 'ingredients')
            ->withSum('ingredients', 'cost')
            ->findOrFail($id);
        Gate::authorize('view-product', $product);
        AppLog::info('Product viewed', 'Product ' . $id . ' viewed', $product);
        return $product;
    }

    public function getSelectedProduct($id)
    {
        $product = Product::with('costs', 'ingredients')->findOrFail($id);
        return $product;
    }

    public function createProduct($name, $categoryId, $baseCost, $specId)
    {
        Gate::authorize('create-product');
        try {
            $product = Product::create(['name' => $name, 'product_category_id' => $categoryId, 'base_cost' => $baseCost, 'spec_id' => $specId]);
            AppLog::info('Product created', 'Product ' . $name . ' created', $product);
            return $product;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product creation failed', 'Product ' . $name . ' creation failed');
            throw new ProductManagementException('Product creation failed');
        }
    }

    public function updateProduct(Product $product, $name, $categoryId, $baseCost, $specId)
    {
        Gate::authorize('update-product', $product);
        try {
            $product->update(['name' => $name, 'product_category_id' => $categoryId, 'base_cost' => $baseCost, 'spec_id' => $specId]);
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
        Gate::authorize('update-product', $product);
        try {
            $highestCost = $product->costs()->withoutGlobalScope('sort_by_order')->orderByDesc('sort_order')->first();
            $sortOrder = $highestCost ? $highestCost->sort_order + 1 : 0;
            $product->costs()->create([
                'name' => $name,
                'cost' => $cost,
                'is_percentage' => $isPercentage,
                'sort_order' => $sortOrder
            ]);
            AppLog::info('Product cost created', 'Product cost ' . $name . ' created', $product);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product cost creation failed', 'Product cost ' . $name . ' creation failed');
            throw new ProductManagementException('Product cost creation failed');
        }
    }

    public function deleteProductCost(ProductCost $productCost)
    {
        Gate::authorize('update-product', $productCost->product);
        try {
            $productCost->delete();
            AppLog::info('Product cost deleted', 'Product cost deleted for product ' . $productCost->product->name);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product cost deletion failed', 'Product cost ' . $productCost->name . ' deletion failed', $productCost);
            throw new ProductManagementException('Product cost deletion failed');
        }
    }

    public function moveProductCostUp(ProductCost $productCost)
    {
        Gate::authorize('update-product', $productCost->product);
        try {
            $higherProductCost = $productCost->product->costs()
                ->withoutGlobalScope('sort_by_order')
                ->where('sort_order', '<', $productCost->sort_order)
                ->orderByDesc('sort_order')
                ->first();
            if ($higherProductCost) {
                $tmpOrder = $productCost->sort_order;
                $productCost->update(['sort_order' => $higherProductCost->sort_order]);
                $higherProductCost->update(['sort_order' => $tmpOrder]);
            }
            AppLog::info('Product cost moved up', 'Product cost ' . $productCost->name . ' moved up', $productCost);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product cost move up failed', 'Product cost ' . $productCost->name . ' move up failed', $productCost);
            throw new ProductManagementException('Product cost move up failed');
        }
    }

    public function moveProductCostDown(ProductCost $productCost)
    {
        Gate::authorize('update-product', $productCost->product);
        AppLog::info('Product cost moved down', 'Product cost ' . $productCost->name . ' moved down', $productCost);
        try {
            $lowerProductCost = $productCost->product->costs()->where('sort_order', '>', $productCost->sort_order)->orderBy('sort_order', 'asc')->first();
            if ($lowerProductCost) {
                $tmpOrder = $productCost->sort_order;
                $productCost->update(['sort_order' => $lowerProductCost->sort_order]);
                $lowerProductCost->update(['sort_order' => $tmpOrder]);
            }
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product cost move down failed', 'Product cost ' . $productCost->name . ' move down failed', $productCost);
            throw new ProductManagementException('Product cost move down failed');
        }
    }

    public function addProductIngredient(Product $product, $name, $cost)
    {
        Gate::authorize('update-product', $product);
        try {
            $product->ingredients()->create([
                'name' => $name,
                'cost' => $cost,
            ]);
            AppLog::info('Product ingredient created', 'Product ingredient ' . $name . ' created', $product);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product ingredient creation failed', 'Product ingredient ' . $name . ' creation failed');
            throw new ProductManagementException('Product ingredient creation failed');
        }
    }

    public function deleteProductIngredient(Ingredient $ingredient)
    {
        Gate::authorize('update-product', $ingredient->product);
        try {
            $ingredient->delete();
            AppLog::info('Product ingredient deleted', 'Product ingredient deleted for product ' . $ingredient->product->name);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product ingredient deletion failed', 'Product ingredient ' . $ingredient->name . ' deletion failed', $ingredient);
            throw new ProductManagementException('Product ingredient deletion failed');
        }
    }

    public function exportProductsToExcel($filename = 'products_export.xlsx')
    {
        Gate::authorize('view-product-list');
        
        try {
            // Create new spreadsheet
            $spreadsheet = new Spreadsheet();
            
            // Create sheets
            $productsSheet = $spreadsheet->getActiveSheet();
            $productsSheet->setTitle('Products Data');
            
            $categoriesSheet = $spreadsheet->createSheet();
            $categoriesSheet->setTitle('Categories');
            
            $specsSheet = $spreadsheet->createSheet();
            $specsSheet->setTitle('Specs');
            
            // Populate Categories sheet
            $this->populateCategoriesSheet($categoriesSheet);
            
            // Populate Specs sheet
            $this->populateSpecsSheet($specsSheet);
            
            // Populate Products sheet
            $this->populateProductsSheet($productsSheet);
            
            // Set active sheet to Products Data
            $spreadsheet->setActiveSheetIndex(0);
            
            // Save to file
            $writer = new Xlsx($spreadsheet);
            $filePath = storage_path('app/' . $filename);
            $writer->save($filePath);
            
            AppLog::info('Products exported to Excel', 'Products exported to ' . $filename);
            
            return $filePath;
            
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product export failed', 'Excel export failed');
            throw new ProductManagementException('Product export failed: ' . $e->getMessage());
        }
    }

    private function populateCategoriesSheet($sheet)
    {
        // Headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Description');
        
        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4A90E2']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
        $sheet->getRowDimension('1')->setRowHeight(25);
        
        // Get categories
        $categories = ProductCategory::get();
        
        $row = 2;
        foreach ($categories as $category) {
            $sheet->setCellValue('A' . $row, $category->id);
            $sheet->setCellValue('B' . $row, $category->name);
            $sheet->setCellValue('C' . $row, $category->description);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function populateSpecsSheet($sheet)
    {
        // Headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        
        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28A745']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);
        $sheet->getRowDimension('1')->setRowHeight(25);
        
        // Get specs
        $specs = Spec::get();
        
        $row = 2;
        foreach ($specs as $spec) {
            $sheet->setCellValue('A' . $row, $spec->id);
            $sheet->setCellValue('B' . $row, $spec->name);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function populateProductsSheet($sheet)
    {
        // Get all products with their relationships
        $products = Product::with(['category', 'spec', 'costs', 'ingredients'])->get();
        
        // Collect all unique cost names and ingredient names
        $costNames = [];
        $ingredientNames = [];
        
        foreach ($products as $product) {
            foreach ($product->costs as $cost) {
                if (!in_array($cost->name, $costNames)) {
                    $costNames[] = $cost->name;
                }
            }
            
            foreach ($product->ingredients as $ingredient) {
                if (!in_array($ingredient->name, $ingredientNames)) {
                    $ingredientNames[] = $ingredient->name;
                }
            }
        }
        
        // Sort names for consistent ordering
        sort($costNames);
        sort($ingredientNames);
        
        // Create column mapping
        $costColumnMap = [];
        $ingredientColumnMap = [];
        
        // Basic product columns (A-E)
        $currentCol = 'A';
        $basicHeaders = ['ID', 'Name', 'Category', 'Spec', 'Base Cost'];
        
        foreach ($basicHeaders as $header) {
            $sheet->setCellValue($currentCol . '1', $header);
            $currentCol++;
        }
        
        // Cost columns
        foreach ($costNames as $costName) {
            $costColumnMap[$costName] = $currentCol;
            $sheet->setCellValue($currentCol . '1', 'Cost: ' . $costName);
            $currentCol++;
        }
        
        // Ingredient columns
        foreach ($ingredientNames as $ingredientName) {
            $ingredientColumnMap[$ingredientName] = $currentCol;
            $sheet->setCellValue($currentCol . '1', 'Ingredient: ' . $ingredientName);
            $currentCol++;
        }
        
        // Style headers
        $lastColumn = $this->getColumnLetter($this->getColumnIndex($currentCol) - 1);
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);
        $sheet->getRowDimension('1')->setRowHeight(25);
        
        // Populate product data
        $row = 2;
        foreach ($products as $product) {
            // Basic product data
            $sheet->setCellValue('A' . $row, $product->id);
            $sheet->setCellValue('B' . $row, $product->name);
            $sheet->setCellValue('C' . $row, $product->category ? $product->category->name : '');
            $sheet->setCellValue('D' . $row, $product->spec ? $product->spec->name : '');
            $sheet->setCellValue('E' . $row, $product->base_cost);
            
            // Cost data
            foreach ($product->costs as $cost) {
                $column = $costColumnMap[$cost->name];
                $costValue = $cost->cost;
                if ($cost->is_percentage) {
                    $costValue .= '%';
                }
                $sheet->setCellValue($column . $row, $costValue);
            }
            
            // Ingredient data
            foreach ($product->ingredients as $ingredient) {
                $column = $ingredientColumnMap[$ingredient->name];
                $sheet->setCellValue($column . $row, $ingredient->cost);
            }
            
            $row++;
        }
        
        // Auto-size all columns
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function getColumnLetter($index)
    {
        $letters = '';
        while ($index > 0) {
            $index--;
            $letters = chr($index % 26 + ord('A')) . $letters;
            $index = intval($index / 26);
        }
        return $letters;
    }

    private function getColumnIndex($letter)
    {
        $index = 0;
        $length = strlen($letter);
        for ($i = 0; $i < $length; $i++) {
            $index = $index * 26 + (ord($letter[$i]) - ord('A') + 1);
        }
        return $index;
    }

    public function importProductsFromExcel($filePath)
    {
        Gate::authorize('create-product');
        
        try {
            // Load spreadsheet
            $spreadsheet = IOFactory::load($filePath);
            
            // Import Categories (Sheet 2)
            $categoriesSheet = $spreadsheet->getSheet(1); // Index 1 for Categories
            $this->importCategoriesFromSheet($categoriesSheet);
            
            // Import Specs (Sheet 3)
            $specsSheet = $spreadsheet->getSheet(2); // Index 2 for Specs
            $this->importSpecsFromSheet($specsSheet);
            
            // Import Products (Sheet 1)
            $productsSheet = $spreadsheet->getSheet(0); // Index 0 for Products Data
            $this->importProductsFromSheet($productsSheet);
            
            AppLog::info('Products imported from Excel', 'Products imported successfully from ' . basename($filePath));
            
            return true;
            
        } catch (Exception $e) {
            report($e);
            AppLog::error('Product import failed', 'Excel import failed: ' . $e->getMessage());
            throw new ProductManagementException('Product import failed: ' . $e->getMessage());
        }
    }

    private function importCategoriesFromSheet($sheet)
    {
        $highestRow = $sheet->getHighestRow();
        
        for ($row = 2; $row <= $highestRow; $row++) {
            $id = $sheet->getCell('A' . $row)->getValue();
            $name = $sheet->getCell('B' . $row)->getValue();
            $description = $sheet->getCell('C' . $row)->getValue();
            
            if (empty($name)) continue;
            
            $category = ProductCategory::find($id);
            if ($category) {
                // Update existing category
                $category->update([
                    'name' => $name,
                    'description' => $description
                ]);
            } else {
                // Create new category
                ProductCategory::create([
                    'name' => $name,
                    'description' => $description
                ]);
            }
        }
    }

    private function importSpecsFromSheet($sheet)
    {
        $highestRow = $sheet->getHighestRow();
        
        for ($row = 2; $row <= $highestRow; $row++) {
            $id = $sheet->getCell('A' . $row)->getValue();
            $name = $sheet->getCell('B' . $row)->getValue();
            
            if (empty($name)) continue;
            
            $spec = Spec::find($id);
            if ($spec) {
                // Update existing spec
                $spec->update([
                    'name' => $name
                ]);
            } else {
                // Create new spec
                Spec::create([
                    'name' => $name
                ]);
            }
        }
    }

    private function importProductsFromSheet($sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        // Get header row to identify cost and ingredient columns
        $headers = [];
        $costColumns = [];
        $ingredientColumns = [];
        
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $header = $sheet->getCell($col . '1')->getValue();
            $headers[$col] = $header;
            
            if (strpos($header, 'Cost: ') === 0) {
                $costColumns[$col] = str_replace('Cost: ', '', $header);
            } elseif (strpos($header, 'Ingredient: ') === 0) {
                $ingredientColumns[$col] = str_replace('Ingredient: ', '', $header);
            }
        }
        
        // Import products
        for ($row = 2; $row <= $highestRow; $row++) {
            $id = $sheet->getCell('A' . $row)->getValue();
            $name = $sheet->getCell('B' . $row)->getValue();
            $categoryName = $sheet->getCell('C' . $row)->getValue();
            $specName = $sheet->getCell('D' . $row)->getValue();
            $baseCost = $sheet->getCell('E' . $row)->getValue();
            
            if (empty($name)) continue;
            
            // Find category and spec by name
            $category = ProductCategory::where('name', $categoryName)->first();
            $spec = Spec::where('name', $specName)->first();
            
            if (!$category || !$spec) {
                AppLog::warning('Product import skipped', "Product '{$name}' skipped: Category or Spec not found");
                continue;
            }
            
            // Create or update product
            $product = Product::find($id);
            if ($product) {
                // Update existing product
                $product->update([
                    'name' => $name,
                    'product_category_id' => $category->id,
                    'spec_id' => $spec->id,
                    'base_cost' => $baseCost
                ]);
            } else {
                // Create new product
                $product = Product::create([
                    'name' => $name,
                    'product_category_id' => $category->id,
                    'spec_id' => $spec->id,
                    'base_cost' => $baseCost
                ]);
            }
            
            // Delete existing costs and ingredients
            $product->costs()->delete();
            $product->ingredients()->delete();
            
            // Import costs
            $sortOrder = 0;
            foreach ($costColumns as $col => $costName) {
                $costValue = $sheet->getCell($col . $row)->getValue();
                if (!empty($costValue)) {
                    $isPercentage = false;
                    $cleanValue = $costValue;
                    
                    // Check if it's a percentage
                    if (is_string($costValue) && strpos($costValue, '%') !== false) {
                        $isPercentage = true;
                        $cleanValue = str_replace('%', '', $costValue);
                    }
                    
                    $product->costs()->create([
                        'name' => $costName,
                        'cost' => floatval($cleanValue),
                        'is_percentage' => $isPercentage,
                        'sort_order' => $sortOrder++
                    ]);
                }
            }
            
            // Import ingredients
            foreach ($ingredientColumns as $col => $ingredientName) {
                $ingredientCost = $sheet->getCell($col . $row)->getValue();
                if (!empty($ingredientCost)) {
                    $product->ingredients()->create([
                        'name' => $ingredientName,
                        'cost' => floatval($ingredientCost)
                    ]);
                }
            }
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
