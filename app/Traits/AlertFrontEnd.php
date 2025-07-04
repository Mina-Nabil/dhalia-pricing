<?php

namespace App\Traits;

use Illuminate\Validation\ValidationException;

trait AlertFrontEnd
{
    public function alertSuccess($message)
    {
        $this->dispatch('toastalert', [
            'message' => $message,
            'type' => 'success',
        ]);
    }

    public function alertError($message = 'Server error')
    {
        $this->dispatch('toastalert', [
            'message' => $message, // Use the default if no message provided
            'type' => 'failed',
        ]);
    }

    public function alertInfo($message)
    {
        $this->dispatch('toastalert', [
            'message' => $message,
            'type' => 'info',
        ]);
    }

    public function alert($type, $message)
    {
        $this->dispatch('toastalert', [
            'message' => $message,
            'type' => $type,
        ]);
    }

    public function throwError($property, $message)
    {
        throw ValidationException::withMessages([
            $property => $message,
        ]);
    }
}
