<?php

namespace App\Mail;

use App\Model\ArchiveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChannelRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $archiveRequest;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ArchiveRequest $archiveRequest)
    {
        $this->archiveRequest = $archiveRequest;
    }

    public function makeSubject(){
        return 'Core Set Request - '.$this->archiveRequest->requestedTypeLabel();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->from($this->archiveRequest->email());
        $this->to(config('mya.administrator.address'), config('mya.administrator.name'));
        $this->cc($this->archiveRequest->email());
        $this->subject($this->makeSubject());
        if ($this->archiveRequest->hasFile()){
            $this->attach($this->archiveRequest->file->path(),[
                'as' => $this->archiveRequest->file->getClientOriginalName()
            ]);
        }
        return $this->text('emails.channel_request');
    }
}
