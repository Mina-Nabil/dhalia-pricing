<?php

namespace App\Providers;

use App\Exceptions\ClientManagementException;
use App\Models\AppLog;
use App\Models\Clients\Client;
use App\Models\User;
use App\Policies\ClientPolicy;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ClientServiceProvider extends ServiceProvider
{
    public function getClients($search = null, $paginate = 10, $forSelection = false)
    {
        $returnAll = false;
        if (Gate::check('view-client-any')) {
            $returnAll = true;
        }

        $clients = Client::query()->when(!$returnAll, function ($query) {
            $query->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            })->orWhere('created_by_id', Auth::id());
        })->search($search);
        if (!$forSelection) {
            AppLog::info('Clients list viewed', 'Clients loaded');
        }

        return $paginate ? $clients->paginate($paginate) : $clients->get();
    }

    public function getClient($id)
    {
        $client = Client::findOrFail($id);
        Gate::authorize('view-client', $client);
        AppLog::info('Client viewed', 'Client ' . $id . ' viewed', $client);
        return $client;
    }

    public function getClientsByIds($ids)
    {
        return Client::whereIn('id', $ids)->get();
    }

    public function checkClientNameExists($name)
    {
        return Client::where('name', $name)->exists();
    }

    public function createClient($name, $phone, $address, $email, $notes, $country_name)
    {
        Gate::authorize('create-client');
        try {

            $client = Client::create([
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'email' => $email,
                'notes' => $notes,
                'created_by_id' => Auth::id(),
                'country_name' => $country_name,
            ]);
            AppLog::info('Client created', 'Client ' . $name . ' created', $client);
            return $client;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Client creation failed', 'Client ' . $name . ' creation failed');
            throw new ClientManagementException('Client creation failed');
        }
    }

    public function getClientByName($name)
    {
        return Client::where('name', $name)->first();
    }

    public function updateClient(Client $client, $name, $phone, $address, $email, $notes, $country_name)
    {
        Gate::authorize('update-client', $client);
        try {
            $client->update([
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'email' => $email,
                'notes' => $notes,
                'country_name' => $country_name,
            ]);
            AppLog::info('Client updated', 'Client ' . $name . ' updated', $client);
            return $client;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Client update failed', 'Client ' . $name . ' update failed');
            throw new ClientManagementException('Client update failed');
        }
    }

    public function addClientInfo(Client $client, $key, $value)
    {
        Gate::authorize('update-client', $client);
        try {
            $client->infos()->updateOrCreate([
                'key' => $key,
            ], [
                'value' => $value,
            ]);
            AppLog::info('Client info updated', "Client {$client->name} info ($key => $value) updated", $client);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Client info addition failed', 'Client ' . $client->name . ' info addition failed');
            throw new ClientManagementException('Client info addition failed');
        }
    }

    public function deleteClientInfo(Client $client, $key)
    {
        Gate::authorize('update-client', $client);
        try {
            $client->infos()->where('key', $key)->delete();
            AppLog::info('Client info deleted', "Client {$client->name} info ($key) deleted", $client);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Client info deletion failed', 'Client ' . $client->name . ' info deletion failed');
            throw new ClientManagementException('Client info deletion failed');
        }
    }

    public function addUsersToClient(Client $client, array $userIds)
    {
        Gate::authorize('update-client-users', $client);
        $userIds = array_unique($userIds);
        $userIds = array_filter($userIds, function ($userId) use ($client) {
            return $userId !== $client->created_by_id;
        });
        $users = User::whereIn('id', $userIds)->get();
        try {
            $client->users()->sync($userIds);
            AppLog::info('Users added to client', "Users " . implode(', ', $users->pluck('username')->toArray()) . " added to client {$client->name}", $client);
            return $client;
        } catch (Exception $e) {
            report($e);
            $usernames = $users->pluck('username')->toArray();
            AppLog::error('User addition to client failed', 'Users ' . implode(', ', $usernames) . ' addition to client ' . $client->name . ' failed', $client);
            throw new ClientManagementException('Client addition failed');
        }
    }

    public function removeUserFromClient(Client $client, $userId)
    {
        Gate::authorize('update-client-users', $client);
        try {
            $user = User::find($userId);
            $client->users()->detach($user);
            AppLog::info('User removed from client', "User {$user->name} removed from client {$client->name}", $client);
            return $client;
        } catch (Exception $e) {
            report($e);
            AppLog::error('User removal from client failed', 'User ' . $userId . ' removal from client ' . $client->name . ' failed');
            throw new ClientManagementException('Client removal failed');
        }
    }


    public function deleteClient(Client $client)
    {
        Gate::authorize('delete-client', $client);
        try {
            $client->delete();
            AppLog::info('Client deleted', 'Client ' . $client->name . ' deleted', $client);
        } catch (Exception $e) {
            report($e);
            AppLog::error('Client deletion failed', 'Client ' . $client->name . ' deletion failed');
            throw new ClientManagementException('Client deletion failed');
        }
    }

    public function exportClientsToExcel($search = null, $filename = 'clients_export.xlsx')
    {
        Gate::authorize('view-client-any');

        try {
            // Get all clients with the same filters (no pagination)
            $clients = $this->getClients(
                search: $search,
                paginate: false // Get all data, not paginated
            );

            // Load relationships for export
            $clients->load(['createdBy', 'users', 'infos']);

            // Create new spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Clients Export');

            // Define headers
            $headers = [
                'A1' => 'Client Name',
                'B1' => 'Phone',
                'C1' => 'Email',
                'D1' => 'Address',
                'E1' => 'Country',
                'F1' => 'Notes',
                'G1' => 'Created By',
                'H1' => 'Associated Users',
                'I1' => 'Additional Info Count',
                'J1' => 'Created Date'
            ];

            // Set headers
            foreach ($headers as $cell => $header) {
                $sheet->setCellValue($cell, $header);
            }

            // Style headers
            $headerRange = 'A1:J1';
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
            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Fill data
            $row = 2;
            foreach ($clients as $client) {
                $sheet->setCellValue('A' . $row, $client->name);
                $sheet->setCellValue('B' . $row, $client->phone ?: 'N/A');
                $sheet->setCellValue('C' . $row, $client->email ?: 'N/A');
                $sheet->setCellValue('D' . $row, $client->address ?: 'N/A');
                $sheet->setCellValue('E' . $row, $client->country_name ?: 'N/A');
                $sheet->setCellValue('F' . $row, $client->notes ?: 'N/A');
                $sheet->setCellValue('G' . $row, $client->createdBy->name ?? 'N/A');

                // Get associated users names
                $associatedUsers = $client->users->pluck('name')->join(', ');
                $sheet->setCellValue('H' . $row, $associatedUsers ?: 'None');

                $sheet->setCellValue('I' . $row, $client->infos->count());
                $sheet->setCellValue('J' . $row, $client->created_at->format('Y-m-d H:i:s'));

                // Style data rows with alternating colors
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8FAFC'] // Light gray
                        ]
                    ]);
                }

                // Add borders to data rows
                $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
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

            AppLog::info('Clients exported to Excel', 'Clients exported to ' . $filename . ' with ' . $clients->count() . ' records');

            return $filePath;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Clients export failed', 'Excel export failed: ' . $e->getMessage());
            throw new ClientManagementException('Clients export failed: ' . $e->getMessage());
        }
    }

    public function importClientsFromExcel($filePath)
    {
        Gate::authorize('create-client');

        try {
            // Load spreadsheet
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Get the highest row number
            $highestRow = $sheet->getHighestRow();

            $importedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $errors = [];

            // Start from row 2 (skip header)
            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    // Read data from cells
                    $clientName = trim($sheet->getCell('A' . $row)->getCalculatedValue() ?? '');
                    $phone = trim($sheet->getCell('B' . $row)->getCalculatedValue() ?? '');
                    $email = trim($sheet->getCell('C' . $row)->getCalculatedValue() ?? '');
                    $address = trim($sheet->getCell('D' . $row)->getCalculatedValue() ?? '');
                    $countryName = trim($sheet->getCell('E' . $row)->getCalculatedValue() ?? '');
                    $notes = trim($sheet->getCell('F' . $row)->getCalculatedValue() ?? '');

                    // Skip empty rows
                    if (empty($clientName)) {
                        continue;
                    }

                    // Convert "N/A" values to null
                    $phone = ($phone === 'N/A') ? null : $phone;
                    $email = ($email === 'N/A') ? null : $email;
                    $address = ($address === 'N/A') ? null : $address;
                    $countryName = ($countryName === 'N/A') ? null : $countryName;
                    $notes = ($notes === 'N/A') ? null : $notes;

                    // Validate email format if provided
                    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Row {$row}: Invalid email format for '{$clientName}'";
                        $skippedCount++;
                        continue;
                    }

                    // Check if client exists (by name)
                    $existingClient = $this->getClientByName($clientName);

                    if ($existingClient) {
                        // Update existing client
                        Gate::authorize('update-client', $existingClient);

                        $this->updateClient(
                            $existingClient,
                            $clientName,
                            $phone,
                            $address,
                            $email,
                            $notes,
                            $countryName
                        );

                        $updatedCount++;
                        AppLog::info('Client updated via import', "Client '{$clientName}' updated from Excel import", $existingClient);
                    } else {
                        // Create new client
                        $newClient = $this->createClient(
                            $clientName,
                            $phone,
                            $address,
                            $email,
                            $notes,
                            $countryName
                        );

                        $importedCount++;
                        AppLog::info('Client created via import', "Client '{$clientName}' created from Excel import", $newClient);
                    }
                } catch (Exception $e) {
                    $errors[] = "Row {$row}: " . $e->getMessage();
                    $skippedCount++;
                    continue;
                }
            }

            $totalProcessed = $importedCount + $updatedCount;
            $summary = [
                'imported' => $importedCount,
                'updated' => $updatedCount,
                'skipped' => $skippedCount,
                'total_processed' => $totalProcessed,
                'errors' => $errors
            ];

            AppLog::info(
                'Clients import completed',
                "Import completed: {$importedCount} new, {$updatedCount} updated, {$skippedCount} skipped"
            );

            return $summary;
        } catch (Exception $e) {
            report($e);
            AppLog::error('Clients import failed', 'Excel import failed: ' . $e->getMessage());
            throw new ClientManagementException('Clients import failed: ' . $e->getMessage());
        }
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ClientServiceProvider::class, function ($app) {
            return new ClientServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-client', [ClientPolicy::class, 'view']);
        Gate::define('view-client-any', [ClientPolicy::class, 'viewAny']);
        Gate::define('create-client', [ClientPolicy::class, 'create']);
        Gate::define('update-client', [ClientPolicy::class, 'update']);
        Gate::define('update-client-users', [ClientPolicy::class, 'updateUsers']);
        Gate::define('delete-client', [ClientPolicy::class, 'delete']);
    }
}
