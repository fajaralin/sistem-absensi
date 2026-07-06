<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\Contracts\FaceRecognitionServiceInterface;
use App\Mail\AttendanceNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_it_sends_email_to_parent_on_successful_check_in_and_leave()
    {
        Mail::fake();

        // 1. Create a student with a parent email
        $user = User::factory()->create([
            'name' => 'Fajar Santoso',
            'email' => 'fajar@student.com',
            'role' => 'student'
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'nisn' => '1234567890',
            'class_name' => 'XII-IPA-1',
            'department' => 'IPA',
            'phone' => '08123456789',
            'parent_email' => 'ortu.fajar@gmail.com',
            'status' => 'active',
        ]);

        // Mock the services needed by AttendanceService
        $attendanceService = app(AttendanceService::class);

        // 2. Call submitStudentLeave to simulate a student submitting leave
        $result = $attendanceService->submitStudentLeave($student->id, 'sakit', 'Sakit demam tinggi', null);

        $this->assertTrue($result['success']);

        // Assert that the email was sent for leave
        Mail::assertQueued(AttendanceNotification::class, function ($mail) use ($student) {
            return $mail->hasTo('ortu.fajar@gmail.com') &&
                   $mail->student->id === $student->id &&
                   $mail->status === 'sakit' &&
                   is_null($mail->time);
        });

        // 3. Test that no email is sent if student doesn't have parent_email
        $userNoEmail = User::factory()->create([
            'name' => 'Budi No Email',
            'email' => 'budi_noemail@student.com',
            'role' => 'student'
        ]);

        $studentNoEmail = Student::create([
            'user_id' => $userNoEmail->id,
            'nisn' => '0987654321',
            'class_name' => 'XII-IPA-1',
            'department' => 'IPA',
            'phone' => '08123456789',
            'parent_email' => null, // empty
            'status' => 'active',
        ]);

        // Reset fake mail
        Mail::fake();

        $resultNoEmail = $attendanceService->submitStudentLeave($studentNoEmail->id, 'izin', 'Izin acara keluarga', null);
        $this->assertTrue($resultNoEmail['success']);

        // Assert no mails were sent
        Mail::assertNothingQueued();
    }

    public function test_it_sends_email_on_successful_face_scan_by_nisn()
    {
        Mail::fake();

        // 1. Create a student with a parent email
        $user = User::factory()->create([
            'name' => 'Siti Aminah',
            'email' => 'siti@student.com',
            'role' => 'student'
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'nisn' => '0089123457',
            'class_name' => 'XII-IPA-1',
            'department' => 'IPA',
            'phone' => '08123456789',
            'parent_email' => 'ortu.siti@gmail.com',
            'status' => 'active',
        ]);

        // Mock FaceRecognitionServiceInterface
        $this->mock(FaceRecognitionServiceInterface::class, function ($mock) use ($student) {
            $mock->shouldReceive('recognize')
                 ->once()
                 ->with('dummy_base64_image', $student->nisn)
                 ->andReturn([
                     'success' => true,
                     'name' => $student->nisn,
                     'confidence' => 0.92
                 ]);
        });

        // Mock settings to ensure attendance gate is open and status is 'hadir' (not 'telat')
        // to satisfy SQLite enum/check constraints in test environment.
        $settingService = app(\App\Services\SettingService::class);
        $settingService->updateSettings([
            'attendance_start_time' => '00:00',
            'attendance_late_threshold' => '23:59'
        ]);

        $attendanceService = app(AttendanceService::class);

        $result = $attendanceService->scanFacePresenceByNisn($student->nisn, 'dummy_base64_image');

        $this->assertTrue($result['success'], $result['message'] ?? 'Check-in failed');

        // Assert that the email was sent for attendance
        Mail::assertQueued(AttendanceNotification::class, function ($mail) use ($student) {
            return $mail->hasTo('ortu.siti@gmail.com') &&
                   $mail->student->id === $student->id &&
                   $mail->status === 'hadir' &&
                   !is_null($mail->time);
        });
    }

    public function test_it_sends_email_on_successful_face_scan_auto()
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Budi Handoko',
            'email' => 'budi@student.com',
            'role' => 'student'
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'nisn' => '0089123456',
            'class_name' => 'XI-MIPA-2',
            'department' => 'MIPA',
            'phone' => '081234567890',
            'parent_email' => 'ortu.budi@gmail.com',
            'status' => 'active',
        ]);

        // Mock FaceRecognitionServiceInterface for 1-to-many identification
        $this->mock(FaceRecognitionServiceInterface::class, function ($mock) use ($student) {
            $mock->shouldReceive('identify')
                 ->once()
                 ->with('dummy_base64_image')
                 ->andReturn([
                     'success' => true,
                     'nisn' => $student->nisn,
                     'confidence' => 0.95
                 ]);
        });

        $settingService = app(\App\Services\SettingService::class);
        $settingService->updateSettings([
            'attendance_start_time' => '00:00',
            'attendance_late_threshold' => '23:59'
        ]);

        $attendanceService = app(AttendanceService::class);

        $result = $attendanceService->scanFacePresenceAuto('dummy_base64_image');

        $this->assertTrue($result['success'], $result['message'] ?? 'Check-in failed');

        Mail::assertQueued(AttendanceNotification::class, function ($mail) use ($student) {
            return $mail->hasTo('ortu.budi@gmail.com') &&
                   $mail->student->id === $student->id &&
                   $mail->status === 'hadir' &&
                   !is_null($mail->time);
        });
    }

    public function test_it_sends_email_on_successful_self_scan_face_presence()
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Siti Aminah',
            'email' => 'siti@student.com',
            'role' => 'student'
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'nisn' => '0089123457',
            'class_name' => 'XII-IPA-1',
            'department' => 'IPA',
            'phone' => '08123456789',
            'parent_email' => 'ortu.siti@gmail.com',
            'status' => 'active',
        ]);

        // Mock FaceRecognitionServiceInterface
        $this->mock(FaceRecognitionServiceInterface::class, function ($mock) use ($student) {
            $mock->shouldReceive('recognize')
                 ->once()
                 ->with('dummy_base64_image')
                 ->andReturn([
                     'success' => true,
                     'name' => $student->nisn,
                     'confidence' => 0.90
                 ]);
        });

        $settingService = app(\App\Services\SettingService::class);
        $settingService->updateSettings([
            'attendance_start_time' => '00:00',
            'attendance_late_threshold' => '23:59'
        ]);

        $attendanceService = app(AttendanceService::class);

        $result = $attendanceService->scanFacePresence($user->id, 'dummy_base64_image');

        $this->assertTrue($result['success'], $result['message'] ?? 'Check-in failed');

        Mail::assertQueued(AttendanceNotification::class, function ($mail) use ($student) {
            return $mail->hasTo('ortu.siti@gmail.com') &&
                   $mail->student->id === $student->id &&
                   $mail->status === 'hadir' &&
                   !is_null($mail->time);
        });
    }
}
