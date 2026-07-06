<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_student_with_parent_email()
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@presensi.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.students.store'), [
                'name' => 'Budi Handoko',
                'email' => 'budi@student.com',
                'parent_email' => 'ortu.budi@gmail.com',
                'nisn' => '0089123456',
                'department' => 'MIPA',
                'class_name' => 'XI-MIPA-2',
                'phone' => '081234567890',
                'password' => 'password123'
            ]);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('students', [
            'nisn' => '0089123456',
            'parent_email' => 'ortu.budi@gmail.com'
        ]);
    }

    public function test_it_can_update_student_parent_email()
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@presensi.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $user = User::factory()->create(['role' => 'student']);
        $student = Student::create([
            'user_id' => $user->id,
            'nisn' => '0089123456',
            'class_name' => 'XI-MIPA-2',
            'department' => 'MIPA',
            'phone' => '081234567890',
            'parent_email' => 'old.email@gmail.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.students.update', $student->id), [
                'name' => $user->name,
                'email' => $user->email,
                'parent_email' => 'new.email@gmail.com',
                'department' => 'MIPA',
                'class_name' => 'XI-MIPA-2',
                'phone' => '081234567890',
                'status' => 'active'
            ]);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'parent_email' => 'new.email@gmail.com'
        ]);
    }
}
