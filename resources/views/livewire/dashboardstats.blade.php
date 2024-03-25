<?php

use Livewire\Volt\Component;

new class extends Component {
    public function with()
    {
        return [
            'notesSentCount' => Auth::user()
            ->notes()
            ->where('send_date', '<', now())
            ->count(),

            'noteLovedCount' => Auth::user()->notes->sum('heart_count'),

        ];
    }
}; ?>

<div>
    <p>Notes Sent: {{ $notesSentCount }}</p>
    <p>Notes Loved: {{ $noteLovedCount }}</p>
</div>
