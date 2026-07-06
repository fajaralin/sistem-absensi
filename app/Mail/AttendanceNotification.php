<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttendanceNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $student;
    public $status;
    public $time;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $status, $time)
    {
        $this->student = $student;
        $this->status = $status;
        $this->time = $time;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Laporan Presensi Harian Siswa - SMAN 1 Utama')
                    ->view('emails.attendance_notification');
    }
}
