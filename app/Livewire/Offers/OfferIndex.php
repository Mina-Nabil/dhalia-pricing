<?php

namespace App\Livewire\Offers;

use App\Exceptions\OfferManagementException;
use App\Models\Offers\Offer;
use App\Providers\OfferServiceProvider;
use App\Providers\ClientServiceProvider;
use App\Providers\UserServiceProvider;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;
use Livewire\WithPagination;

class OfferIndex extends Component
{
    use WithPagination, AlertFrontEnd;

    /** @var OfferServiceProvider */
    protected $offerService;

    // Search and filters
    public $search = '';
    public $filterUserIds = [];
    public $filterClientIds = [];
    public $filterStatuses = [];
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterPriceFrom = '';
    public $filterPriceTo = '';
    public $filterProfitFrom = '';
    public $filterProfitTo = '';
    public $sort = 'created_at';
    public $sortDirection = 'desc';

    // UI state
    public $showFilters = false;
    public $deleteConfirmationModal = false;

    // Selected items
    public $selectedOffer;
    public $itemIdToDelete = null;

    protected $paginationTheme = 'simple-bootstrap';

    protected $listeners = ['deleteOffer', 'clientsSelected', 'usersSelected', 'statusesSelected'];

    public function boot()
    {
        $this->offerService = app(OfferServiceProvider::class);
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterUserIds()
    {
        $this->resetPage();
    }

    public function updatingFilterClientIds()
    {
        $this->resetPage();
    }

    public function updatingFilterStatuses()
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }

    public function updatingFilterPriceFrom()
    {
        $this->resetPage();
    }

    public function updatingFilterPriceTo()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function updatingSortDirection()
    {
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clientsSelected($clientIds)
    {
        $this->filterClientIds = $clientIds;
        $this->resetPage();
    }

    public function usersSelected($userIds)
    {
        $this->filterUserIds = $userIds;
        $this->resetPage();
    }

    public function statusesSelected($statuses)
    {
        $this->filterStatuses = $statuses;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterUserIds = [];
        $this->filterClientIds = [];
        $this->dispatch('clearClientsSelection');
        $this->dispatch('clearUsersSelection');
        $this->dispatch('clearStatusesSelection');
        $this->filterStatuses = [];
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->filterPriceFrom = '';
        $this->filterPriceTo = '';
        $this->filterProfitFrom = '';
        $this->filterProfitTo = '';
        $this->sort = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
        $this->render();
    }

    public function sortBy($field)
    {
        if ($this->sort === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function goToOfferShow($offerId)
    {
        return redirect()->route('offers.show', $offerId);
    }

    public function goToOfferCreate()
    {
        return redirect()->route('offers.create');
    }

    public function confirmDeleteOffer($offerId)
    {
        $this->itemIdToDelete = $offerId;
        $this->deleteConfirmationModal = true;
    }

    public function deleteOffer($offerId)
    {
        try {
            $this->offerService->deleteOffer($offerId);
            $this->alert('success', 'Offer deleted successfully');
        } catch (OfferManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'An unexpected error occurred');
        }
    }

    public function closeDeleteConfirmationModal()
    {
        $this->deleteConfirmationModal = false;
        $this->itemIdToDelete = null;
    }

    public function confirmDelete()
    {
        if ($this->itemIdToDelete) {
            $this->deleteOffer($this->itemIdToDelete);
        }

        $this->closeDeleteConfirmationModal();
    }

    public function exportOffers()
    {
        try {
            $this->authorize('canExport', Offer::class);
            
            $filePath = $this->offerService->exportOffersToExcel(
                search: $this->search ?: null,
                user_ids: $this->filterUserIds,
                client_ids: $this->filterClientIds,
                statuses: $this->filterStatuses,
                date_from: $this->filterDateFrom ?: null,
                date_to: $this->filterDateTo ?: null,
                price_from: $this->filterPriceFrom ?: null,
                price_to: $this->filterPriceTo ?: null,
                profit_from: $this->filterProfitFrom ?: null,
                profit_to: $this->filterProfitTo ?: null,
                sort: $this->sort,
                sort_direction: $this->sortDirection,
                filename: 'offers_export_' . date('Y-m-d_H-i-s') . '.xlsx'
            );
            
            $this->alert('success', 'Offers exported successfully');
            
            return response()->download($filePath)->deleteFileAfterSend(true);
            
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (OfferManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $offers = $this->offerService->getOffers(
            search: $this->search ?: null,
            user_ids: $this->filterUserIds,
            client_ids: $this->filterClientIds,
            statuses: $this->filterStatuses,
            date_from: $this->filterDateFrom ?: null,
            date_to: $this->filterDateTo ?: null,
            price_from: $this->filterPriceFrom ?: null,
            price_to: $this->filterPriceTo ?: null,
            profit_from: $this->filterProfitFrom ?: null,
            profit_to: $this->filterProfitTo ?: null,
            paginate: 10,
            sort: $this->sort,
            sort_direction: $this->sortDirection,
            with: ['user', 'client', 'items.product']
        );

        $sortFields = OfferServiceProvider::SORT_FIELDS;

        return view('livewire.offers.offer-index', [
            'offers' => $offers,
            'sortFields' => $sortFields,
        ]);
    }
}
