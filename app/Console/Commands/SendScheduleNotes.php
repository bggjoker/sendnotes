<?php

namespace App\Console\Commands;

use App\Models\Note;
use App\Jobs\SendEmail;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendScheduleNotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-schedule-notes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $notes = Note::where('is_published', true)
            ->where('send_date', $now->toDateString())
            ->get();

        $noteCount = $notes->count();
        $this->info("Sending {$noteCount} scheduled notes.");

        foreach ($notes as $note)
        {
            SendEmail::dispatch($note);
        }

    }
}
