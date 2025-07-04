<?php

namespace App\Livewire\Settings;

use App\Exceptions\CurrencyManagementException;
use App\Models\Currency;
use App\Providers\CurrencyServiceProvider;
use App\Traits\AlertFrontEnd;
use Livewire\Component;
use Livewire\WithPagination;

class CurrenciesIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    protected $currencyService;

    public $search = '';
    public $setCurrencySec = false;
    public $editMode = false;
    public $selectedCurrency;

    // Form properties
    public $name = '';
    public $code = '';
    public $rate = '';

    protected $paginationTheme = 'simple-bootstrap';

    protected $listeners = ['deleteCurrency'];

    public function __construct()
    {
        $this->authorize('viewAny', Currency::class);
        $this->currencyService = app(CurrencyServiceProvider::class);
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10|unique:currencies,code',
            'rate' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
        ];

        // For updates, exclude current record from unique validation
        if ($this->editMode && $this->selectedCurrency) {
            $rules['code'] = 'nullable|string|max:10|unique:currencies,code,' . $this->selectedCurrency->id;
        }

        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openNewCurrencySec()
    {
        $this->resetFormFields();
        $this->editMode = false;
        $this->setCurrencySec = true;
    }

    public function updateThisCurrency($currencyId)
    {
        try {
            $currency = $this->currencyService->getCurrency($currencyId);
            
            $this->selectedCurrency = $currency;
            $this->name = $currency->name;
            $this->code = $currency->code;
            $this->rate = $currency->rate;
            $this->editMode = true;
            $this->setCurrencySec = true;
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to load currency data');
        }
    }

    public function addNewCurrency()
    {
        $this->validate();

        try {
            $currencyService = app(CurrencyServiceProvider::class);
            
            if ($this->editMode) {
                $currencyService->updateCurrency($this->selectedCurrency, $this->name, $this->code, $this->rate);
                $this->alert('success', 'Currency updated successfully');
            } else {
                $currencyService->createCurrency($this->name, $this->rate, $this->code);
                $this->alert('success', 'Currency created successfully');
            }

            $this->resetFormFields();
            $this->setCurrencySec = false;
        } catch (CurrencyManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function deleteCurrency($currencyId)
    {
        try {
            $currency = $this->currencyService->getCurrency($currencyId);
            $this->currencyService->deleteCurrency($currency);
            $this->alert('success', 'Currency deleted successfully');
        } catch (CurrencyManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    private function resetFormFields()
    {
        $this->name = '';
        $this->code = '';
        $this->rate = '';
        $this->selectedCurrency = null;
        $this->resetValidation();
    }

    public function render()
    {
        $currencyService = app(CurrencyServiceProvider::class);
        
        $currencies = Currency::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.currencies-index', [
            'currencies' => $currencies
        ]);
    }
}
