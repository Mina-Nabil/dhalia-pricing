<?php

namespace App\Livewire\Offers;

use App\Exceptions\OfferManagementException;
use App\Models\Offers\Offer;
use App\Providers\OfferServiceProvider;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;

class OfferShow extends Component
{
    use AlertFrontEnd;

    /** @var OfferServiceProvider */
    protected $offerService;

    public $offer_id;
    public $offer;
    public $statuses = [];

    public function boot()
    {
        $this->offerService = app(OfferServiceProvider::class);
    }

    public function mount($offer_id)
    {
        $this->offer_id = $offer_id;
        $this->offer = $this->offerService->getOffer($offer_id, true);
        $this->authorize('view', $this->offer);
        
        // Load available statuses
        $this->statuses = Offer::STATUSES;
        
        try {
        } catch (OfferManagementException $e) {
            $this->alert('error', $e->getMessage());
            return redirect()->route('offers.index');
        } catch (AuthorizationException $e) {
            $this->alert('error', 'You are not authorized to view this offer.');
            return redirect()->route('offers.index');
        } catch (Exception $e) {
            $this->alert('error', 'Offer not found.');
            return redirect()->route('offers.index');
        }
    }

    public function updateOfferStatus($newStatus)
    {
        try {            
            $this->offerService->setOfferStatus($this->offer->id, $newStatus);
            
            // Refresh the offer data
            $this->offer = $this->offerService->getOffer($this->offer_id, false);
            
            $this->alert('success', 'Offer status updated successfully!');
            
        } catch (OfferManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', $e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to update offer status: ' . $e->getMessage());
        }
    }

    public function deleteOffer()
    {
        try {
            // Use the same gate name as in the service provider
            $this->authorize('delete-offer', $this->offer);
            
            $this->offerService->deleteOffer($this->offer->id);
            
            $this->alert('success', 'Offer deleted successfully!');
            return redirect()->route('offers.index');
            
        } catch (OfferManagementException $e) {
            $this->alert('error', $e->getMessage());
        } catch (AuthorizationException $e) {
            $this->alert('error', 'You are not authorized to delete this offer.');
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to delete offer: ' . $e->getMessage());
        }
    }

    public function duplicateOffer()
    {
        try {
            $this->authorize('create-offers');
            
            return redirect()->route('offers.create', ['duplicateOfId' => $this->offer->id]);
            
        } catch (AuthorizationException $e) {
            $this->alert('error', 'You are not authorized to create offers.');
        } catch (Exception $e) {
            report($e);
            $this->alert('error', 'Failed to duplicate offer: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.offers.offer-show');
    }
}
