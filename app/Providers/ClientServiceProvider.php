<?php

namespace App\Providers;

use App\Exceptions\ClientManagementException;
use App\Models\AppLog;
use App\Models\Clients\Client;
use App\Policies\ClientPolicy;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class ClientServiceProvider extends ServiceProvider
{
    public function getClients($search = null, $paginate = 10)
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
        AppLog::info('Clients list viewed', 'Clients loaded');

        return $paginate ? $clients->paginate($paginate) : $clients->get();
    }

    public function getClient($id)
    {
        $client = Client::findOrFail($id);
        Gate::authorize('view-client', $client);
        AppLog::info('Client viewed', 'Client ' . $id . ' viewed', $client);
        return $client;
    }

    public function checkClientNameExists($name)
    {
        return Client::where('name', $name)->exists();
    }

    public function createClient($name, $phone, $address, $email, $notes)
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

    public function updateClient(Client $client, $name, $phone, $address, $email, $notes)
    {
        Gate::authorize('update-client', $client);
        try {
            $client->update([
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'email' => $email,
                'notes' => $notes,
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
        Gate::define('delete-client', [ClientPolicy::class, 'delete']);
    }
}
