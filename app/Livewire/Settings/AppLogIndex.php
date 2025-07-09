<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AppLog;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('App Logs')]
#[Layout('components.layouts.app')]
class AppLogIndex extends Component
{
    use WithPagination;

    public $LogId;
    public $user;
    public $level;
    public $title;
    public $desc;
    public $time;
    public $numberOfPaginatorsRendered = 20;


    public $fromDate ;
    public $toDate;

    protected $listeners = ['dateRangeSelected'];

    public function dateRangeSelected($data)
    {

        $this->fromDate = $data[0];
        $this->toDate = $data[1];
        $this->resetPage();
    }

    public function showLogInfo($id)
    {
        $this->LogId = $id;
        $log = AppLog::find($this->LogId);
        $this->user = $log->user?->username;
        $this->level = $log->level;
        $this->title = $log->title;
        $this->desc = $log->desc;
        $this->time = $log->created_at;
    }

    public function closeLogInfo()
    {
        $this->LogId = null;
        $this->user = null;
        $this->level = null;
        $this->title = null;
        $this->desc = null;
        $this->time = null;
    }

    public function mount()
    {
        $this->fromDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->toDate = Carbon::now()->format('Y-m-d');
    }   

    public function render()
    {
        $fromDate = Carbon::parse($this->fromDate);
        $toDate = Carbon::parse($this->toDate);
        $logs = AppLog::with('user')->orderBy('created_at', 'desc')->fromTo($fromDate, $toDate)->paginate($this->numberOfPaginatorsRendered);
        
        return view('livewire.settings.app-log-index', [
            'logs' => $logs,
        ]);
    }
}
