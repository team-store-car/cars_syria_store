<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Stelle sicher, dass die E-Mail-Verifizierung aktiviert ist
        config(['auth.verify' => true]);
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_verification_email_contains_correct_content(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, CustomVerifyEmail::class, function ($notification) use ($user) {
            $mailMessage = $notification->toMail($user);
            
            // Überprüfe den arabischen Inhalt
            $this->assertEquals('مرحباً بك في سوق السيارات السورية', $mailMessage->subject);
            $this->assertEquals('مرحباً', $mailMessage->greeting);
            $this->assertStringContainsString('تأكيد عنوان البريد الإلكتروني', $mailMessage->actionText);
            $this->assertStringContainsString('مع أطيب التحيات', $mailMessage->salutation);
            
            // Überprüfe die Button-URL
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );
            $this->assertEquals($verificationUrl, $mailMessage->actionUrl);

            return true;
        });
    }

    public function test_verification_email_template_rendering(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $notification = new CustomVerifyEmail;
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $mailMessage = $notification->toMail($user);
        
        // Überprüfe das Design-Elements
        $this->assertStringContainsString('email-container', $mailMessage->render());
        $this->assertStringContainsString('logo-container', $mailMessage->render());
        $this->assertStringContainsString('verify-button', $mailMessage->render());
        $this->assertStringContainsString('footer', $mailMessage->render());
        
        // Überprüfe den arabischen Text
        $this->assertStringContainsString('سوق السيارات السورية', $mailMessage->render());
        $this->assertStringContainsString('مع أطيب التحيات', $mailMessage->render());
        $this->assertStringContainsString('جميع الحقوق محفوظة', $mailMessage->render());
    }

    public function test_resend_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post('/email/verification-notification');

        Notification::assertSentTo($user, CustomVerifyEmail::class);
        $response->assertStatus(302);
    }
} 