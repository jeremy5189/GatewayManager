<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class NotifyUserDeny extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@' . env('MAILGUN_DOMAIN'))
                    ->subject('Your request was denied')
                    ->view('emails.notify-deny', [
                        'user'    => $this->user,
                        'time'    => \Carbon\Carbon::now()->toDateTimeString()
                    ]);
    }
}
