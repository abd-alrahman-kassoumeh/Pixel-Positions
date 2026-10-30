<?php

namespace App\Mail;

use App\Models\Job;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Apply extends Mailable
{
    use Queueable, SerializesModels;

    public string $cvPath;
    public Job $job;

    public User $applicant;

    /**
     * Create a new message instance.
     */
    public function __construct(Job $job , string $cvPath , User $applicant)
    {
        $this->cvPath = $cvPath;
        $this->job = $job;
        $this->applicant = $applicant;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Job Application for ' . $this->job->title,
            replyTo: [
                new Address($this->applicant->email , $this->applicant->name)
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: '',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('local' , $this->cvPath),
        ];
    }
}
