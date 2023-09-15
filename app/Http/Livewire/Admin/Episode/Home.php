<?php

namespace App\Http\Livewire\Admin\Episode;

use App\Http\Livewire\BaseComponent;

class Home extends BaseComponent
{
    public function render()
    {
        return view('livewire.admin.episode.home')->layout('layouts.admin-base');
    }
}
