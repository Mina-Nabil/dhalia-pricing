<?php

namespace App\Providers;

use App\Exceptions\OfferManagementException;
use App\Models\AppLog;
use App\Models\Offers\Offer;
use App\Models\Offers\OfferItem;
use App\Policies\OfferPolicy;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OfferServiceProvider extends ServiceProvider
{

    const SORT_FIELDS = [
        'created_at',
        'total_price',
        'total_tonnage',
        'total_profit',
        'code'
    ];

    /**
     * Get offers
     * 
     * @param string|null $search
     * @param int|null $user_id
     * @param int|null $client_id
     * @param string|null $status
     * @param string|null $date_from
     * @param string|null $date_to
     * @param float|null $price_from
     * @param float|null $price_to
     * @param int $paginate
     * @param string $sort
     * @param string $sort_direction
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getOffers($search = null, $user_ids = [], $client_ids = [], $statuses = [], $date_from = null, $date_to = null, $price_from = null, $price_to = null, $profit_from = null, $profit_to = null, $paginate = 10, $sort = 'created_at', $sort_direction = 'desc', $with = [])
    {

        if (!in_array($sort, self::SORT_FIELDS)) {
            throw new OfferManagementException('Invalid sort field');
        }

        if (!in_array($sort_direction, ['asc', 'desc'])) {
            throw new OfferManagementException('Invalid sort direction');
        }

        $returnAll = false;
        if (Gate::check('view-offers-list')) {
            $returnAll = true;
        }

        $query = Offer::query()
            ->with($with)
            ->when(!$returnAll, function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($search, function ($query) use ($search) {
                $query->search($search);
            })
            ->when(count($user_ids), function ($query) use ($user_ids) {
                $query->users($user_ids);
            })
            ->when(count($client_ids), function ($query) use ($client_ids) {
                $query->clients($client_ids);
            })
            ->when(count($statuses), function ($query) use ($statuses) {
                $query->statuses($statuses);
            })
            ->when($date_from, function ($query) use ($date_from) {
                $query->dateFrom($date_from);
            })
            ->when($date_to, function ($query) use ($date_to) {
                $query->dateTo($date_to);
            })
            ->when($price_from, function ($query) use ($price_from) {
                $query->priceFrom($price_from);
            })
            ->when($price_to, function ($query) use ($price_to) {
                $query->priceTo($price_to);
            })
            ->when($profit_from, function ($query) use ($profit_from) {
                $query->profitFrom($profit_from);
            })
            ->when($profit_to, function ($query) use ($profit_to) {
                $query->profitTo($profit_to);
            })
            ->orderBy($sort, $sort_direction);

        AppLog::info('Offers list viewed', 'Offers loaded');

        return $paginate ? $query->paginate($paginate) : $query->get();
    }

    public function getOffer($id, $log = true)
    {
        Log::info('getOffer', ['id' => $id, 'log' => $log]);
        $offer = Offer::find($id);
        if (!$offer) throw new OfferManagementException('Offer not found');
        Gate::authorize('view-offer', $offer);

        $offer->load(
            'user',
            'client',
            'items.product',
            'items.product.category',
            'items.ingredients',
            'items.product.spec',
            'items.packing',
            'duplicateOf',
            'currency',
            'comments.user',
        );
        AppLog::info('Offer loaded', "Offer $offer->code loaded", $offer);
        return $offer;
    }

    public function createOffer($status, $clientId, $currencyId, $currencyRate, $offerItems, $duplicateOfId = null, $notes = null)
    {
        Gate::authorize('create-offers');
        $this->checkOfferItemsArray($offerItems);
        $code = $this->getNextOfferCode($duplicateOfId);

        $totalTonnage = 0;
        $totalPrice = 0;
        $totalBasePrice = 0;
        $totalFreightCost = 0;
        $totalPackingCost = 0;
        $totalSterilizationCost = 0;
        $totalAgentCommissionCost = 0;
        $totalInternalCost = 0;
        $totalCosts = 0;
        $totalProfit = 0;
        $profitPercentage = 0;

        foreach ($offerItems as $item) {
            $totalTonnage += ($item['quantity_in_kgs'] / 1000);
            $totalPrice += $item['price'];
            $totalBasePrice += $item['base_cost_currency'];
            $totalFreightCost += $item['freight_total_cost'];
            $totalPackingCost += $item['total_packing_cost'];
            $totalSterilizationCost += $item['sterilization_total_cost'];
            $totalAgentCommissionCost += $item['agent_commission_total_cost'];
            $totalInternalCost += $item['internal_cost'];
            $totalCosts += $item['total_costs'];
            $totalProfit += $item['total_profit'];
        }

        $profitPercentage = ($totalProfit / $totalCosts) * 100;

        $offer = new Offer([
            'user_id' => Auth::id(),
            'status' => $status,
            'duplicate_of_id' => $duplicateOfId,
            'client_id' => $clientId,
            'currency_id' => $currencyId,
            'currency_rate' => $currencyRate,
            'code' => $code,
            'total_tonnage' => $totalTonnage,
            'total_price' => $totalPrice,
            'total_base_price' => $totalBasePrice,
            'total_freight_cost' => $totalFreightCost,
            'total_packing_cost' => $totalPackingCost,
            'total_sterilization_cost' => $totalSterilizationCost,
            'total_agent_commission_cost' => $totalAgentCommissionCost,
            'total_internal_cost' => $totalInternalCost,
            'total_costs' => $totalCosts,
            'total_profit' => $totalProfit,
            'profit_percentage' => $profitPercentage,
            'notes' => $notes,
        ]);
        try {
            DB::transaction(function () use ($offer, $offerItems) {
                $offer->save();
                $offer->items()->createMany($offerItems);
                foreach ($offer->items as $key => $item) {
                    if (isset($offerItems[$key]['ingredients']) && count($offerItems[$key]['ingredients']) > 0) {
                        $item->ingredients()->createMany($offerItems[$key]['ingredients']);
                    }
                }
                $offer->refresh();
            });
        } catch (Exception $e) {
            report($e);
            throw new OfferManagementException('Failed to create offer');
        }

        return $offer;
    }

    public function setOfferStatus($id, $status)
    {
        $offer = $this->getOffer($id);
        Gate::authorize('update-offer', $offer);
        $offer->status = $status;
        try {
            $offer->save();
            AppLog::info('Offer status updated', "Offer $offer->code status updated to $status", $offer);
            return $offer;
        } catch (Exception $e) {
            report($e);
            throw new OfferManagementException('Failed to update offer status');
        }
    }

    public function updateOfferNotes($id, $notes)
    {
        $offer = $this->getOffer($id, false);
        Gate::authorize('update-offer-notes', $offer);
        $offer->notes = $notes;
        try {
            $offer->save();
            AppLog::info('Offer notes updated', "Offer $offer->code notes updated", $offer);
            return $offer;
        } catch (Exception $e) {
            report($e);
            throw new OfferManagementException('Failed to update offer notes');
        }
    }

    public function deleteOffer($id)
    {
        $offer = $this->getOffer($id);
        Gate::authorize('delete-offer', $offer);
        try {
            DB::transaction(function () use ($offer) {
                foreach ($offer->items as $item) {
                    $item->ingredients()->delete();
                    $item->delete();
                }
                $offer->comments()->delete();
                $offer->delete();
            });
        } catch (Exception $e) {
            report($e);
            throw new OfferManagementException('Failed to delete offer');
        }
        AppLog::info('Offer deleted', "Offer $offer->code deleted", $offer);
        return true;
    }

    public function exportOffersToExcel($search = null, $user_ids = [], $client_ids = [], $statuses = [], $date_from = null, $date_to = null, $price_from = null, $price_to = null, $profit_from = null, $profit_to = null, $sort = 'created_at', $sort_direction = 'desc', $filename = 'offers_export.xlsx')
    {
        Gate::authorize('can-export-offers');

        try {
            // Get all offers with the same filters (no pagination)
            $offers = $this->getOffers(
                search: $search,
                user_ids: $user_ids,
                client_ids: $client_ids,
                statuses: $statuses,
                date_from: $date_from,
                date_to: $date_to,
                price_from: $price_from,
                price_to: $price_to,
                profit_from: $profit_from,
                profit_to: $profit_to,
                paginate: false, // Get all data, not paginated
                sort: $sort,
                sort_direction: $sort_direction,
                with: ['user', 'client', 'currency', 'items.product']
            );

            // Create new spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Offers Export');
            if (Gate::check('view-offers-profit')) {
                $columnIndeces = [
                    'Offer Code' => 'A',
                    'Status' => 'B',
                    'Client Name' => 'C',
                    'User Name' => 'D',
                    'Currency' => 'E',
                    'Total Tonnage' => 'F',
                    'Total Price' => 'G',
                    'Total Base Price' => 'H',
                    'Total Costs' => 'I',
                    'Total Profit' => 'J',
                    'Profit Percentage' => 'K',
                    'Created Date' => 'L',
                    'Items Count' => 'M'
                ];
            } else {
                $columnIndeces = [
                    'Offer Code' => 'A',
                    'Status' => 'B',
                    'Client Name' => 'C',
                    'User Name' => 'D',
                    'Currency' => 'E',
                    'Total Tonnage' => 'F',
                    'Total Price' => 'G',
                    'Total Base Price' => 'H',
                    'Total Costs' => 'I',
                    'Created Date' => 'J',
                    'Items Count' => 'K'
                ];
            }

            // Define headers
            $headers = [
                $columnIndeces['Offer Code'] . '1' => 'Offer Code',
                $columnIndeces['Status'] . '1' => 'Status',
                $columnIndeces['Client Name'] . '1' => 'Client Name',
                $columnIndeces['User Name'] . '1' => 'User Name',
                $columnIndeces['Currency'] . '1' => 'Currency',
                $columnIndeces['Total Tonnage'] . '1' => 'Total Tonnage',
                $columnIndeces['Total Price'] . '1' => 'Total Price',
                $columnIndeces['Total Base Price'] . '1' => 'Total Base Price',
                $columnIndeces['Total Costs'] . '1' => 'Total Costs',
                $columnIndeces['Created Date'] . '1' => 'Created Date',
                $columnIndeces['Items Count'] . '1' => 'Items Count'
            ];

            if (Gate::check('view-offers-profit')) {
                $headers[$columnIndeces['Total Profit'] . '1'] = 'Total Profit';
                $headers[$columnIndeces['Profit Percentage'] . '1'] = 'Profit Percentage';
            }

            // Set headers
            foreach ($headers as $cell => $header) {
                $sheet->setCellValue($cell, $header);
            }

            // Style headers
            $headerRange = $columnIndeces['Offer Code'] . '1:' . $columnIndeces['Items Count'] . '1';
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'] // Blue background
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
            ]);

            // Auto-size columns
            foreach (range($columnIndeces['Offer Code'], $columnIndeces['Items Count']) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Fill data
            $row = 2;
            foreach ($offers as $offer) {
                $sheet->setCellValue($columnIndeces['Offer Code'] . $row, $offer->code);
                $sheet->setCellValue($columnIndeces['Status'] . $row, $offer->status);
                $sheet->setCellValue($columnIndeces['Client Name'] . $row, $offer->client->name ?? 'N/A');
                $sheet->setCellValue($columnIndeces['User Name'] . $row, $offer->user->name ?? 'N/A');
                $sheet->setCellValue($columnIndeces['Currency'] . $row, $offer->currency->name ?? 'N/A');
                $sheet->setCellValue($columnIndeces['Total Tonnage'] . $row, number_format($offer->total_tonnage, 2));
                $sheet->setCellValue($columnIndeces['Total Price'] . $row, number_format($offer->total_price, 2));
                $sheet->setCellValue($columnIndeces['Total Base Price'] . $row, number_format($offer->total_base_price, 2));
                $sheet->setCellValue($columnIndeces['Total Costs'] . $row, number_format($offer->total_costs, 2));
                if (Gate::check('view-offers-profit')) {
                    $sheet->setCellValue($columnIndeces['Total Profit'] . $row, number_format($offer->total_profit, 2));
                    $sheet->setCellValue($columnIndeces['Profit Percentage'] . $row, number_format($offer->profit_percentage, 2) . '%');
                }
                $sheet->setCellValue($columnIndeces['Created Date'] . $row, $offer->created_at->format('Y-m-d H:i:s'));
                $sheet->setCellValue($columnIndeces['Items Count'] . $row, $offer->items->count());

                // Style data rows with alternating colors
                if ($row % 2 == 0) {
                    $sheet->getStyle($columnIndeces['Offer Code'] . $row . ':' . $columnIndeces['Items Count'] . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8FAFC'] // Light gray
                        ]
                    ]);
                }

                // Add borders to data rows
                $sheet->getStyle($columnIndeces['Offer Code'] . $row . ':' . $columnIndeces['Items Count'] . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB']
                        ]
                    ]
                ]);

                $row++;
            }

            // Save to file
            $writer = new Xlsx($spreadsheet);
            $filePath = storage_path('app/' . $filename);
            $writer->save($filePath);

            AppLog::info('Offers exported to Excel', 'Offers exported to ' . $filename . ' with ' . $offers->count() . ' records');

            return $filePath;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Offers export failed', 'Excel export failed: ' . $e->getMessage());
            throw new OfferManagementException('Offers export failed: ' . $e->getMessage());
        }
    }

    private function checkOfferItemsArray(&$offerItems)
    {
        $requiredFields = OfferItem::REQUIRED_FIELDS;
        $i = 0;
        foreach ($offerItems as &$item) {
            foreach ($requiredFields as $field) {
                if (!isset($item[$field])) {
                    throw new OfferManagementException("Field $field is required in offer item#$i ");
                }
            }
            foreach ($item as $key => $value) {
                if (!in_array($key, $requiredFields)) {
                    unset($item[$key]);
                }
            }
            $i++;
        }
    }

    private function getNextOfferCode($duplicateOfId = null)
    {
        if ($duplicateOfId) {
            $duplicateOf = $this->getOffer($duplicateOfId);
            $duplicationCode = str_pad($this->getDuplicateOffersCount($duplicateOfId) + 1, 2, '0', STR_PAD_LEFT);
            return $duplicateOf->code . '-' . $duplicationCode;
        } else {
            $latestOfferId = $this->getLatestOfferId();
            return "OF" . str_pad($latestOfferId + 1, 5, '0', STR_PAD_LEFT);
        }
    }

    private function getDuplicateOffersCount($duplicateOfId)
    {
        return Offer::where('duplicate_of_id', $duplicateOfId)->count();
    }

    private function getLatestOfferId()
    {
        return Offer::latest()->first()?->id ?? 0;
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(OfferServiceProvider::class, function ($app) {
            return new OfferServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-offers-list', [OfferPolicy::class, 'viewAny']);
        Gate::define('view-offers-profit', [OfferPolicy::class, 'viewProfit']);
        Gate::define('view-offer', [OfferPolicy::class, 'view']);
        Gate::define('create-offers', [OfferPolicy::class, 'create']);
        Gate::define('update-offer', [OfferPolicy::class, 'update']);
        Gate::define('update-offer-notes', [OfferPolicy::class, 'updateNotes']);
        Gate::define('delete-offer', [OfferPolicy::class, 'delete']);
        Gate::define('can-export-offers', [OfferPolicy::class, 'canExport']);
    }
}
