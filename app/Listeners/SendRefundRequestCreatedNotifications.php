<?php

namespace App\Listeners;

use App\Events\RefundRequestCreated;
use App\Models\Refund;
use App\Notifications\NewRefundRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendRefundRequestCreatedNotifications
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RefundRequestCreated $event): void
    {
//        error_log("This is a message to the server console.");
        Notification::send($event->refund, new NewRefundRequest($event->refund));

        // foreach (Refund::where('refund_status', 1)->cursor() as $refund) {
        //     error_log("In loop.");
        //     $refund->notify(new NewRefundRequest($event->refund));
        //     Notification::send($refund, new \App\Notifications\NewRefundRequest($event->refund));
        // }
    }
}
