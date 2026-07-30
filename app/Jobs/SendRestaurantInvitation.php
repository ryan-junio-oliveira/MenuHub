<?php

namespace App\Jobs;

use App\Mail\RestaurantInvitation;
use App\Models\Restaurant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendRestaurantInvitation implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Restaurant $restaurant,
    ) {}

    public function handle(): void
    {
        $admin = $this->restaurant->adminUser();

        if (!$admin) {
            return;
        }

        try {
            $setupUrl = route('setup.show', $this->restaurant->setup_token);

            Mail::to($admin->email)->send(new RestaurantInvitation($this->restaurant, $admin, $setupUrl));

            $this->restaurant->update([
                'invitation_sent_at' => now(),
                'invitation_failed_at' => null,
            ]);
        } catch (\Throwable $e) {
            $this->restaurant->update([
                'invitation_failed_at' => now(),
            ]);

            throw $e;
        }
    }
}
