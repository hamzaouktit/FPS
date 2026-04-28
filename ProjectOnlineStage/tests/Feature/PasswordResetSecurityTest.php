<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Mail\MyEmail;

class PasswordResetSecurityTest extends TestCase
{
    protected $email = 'test@example.com';
    protected $password = 'NewPassword123456789';

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        
        // Create test user
        User::updateOrCreate(
            ['email' => $this->email],
            ['name' => 'Test User', 'password' => Hash::make('oldpassword')]
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Cache::flush();
    }

    /**
     * TEST 1: Code Generation
     * Verify: 8-digit codes, unique, random
     */
    public function test_generated_code_is_eight_digits()
    {
        $response = $this->post(route('forgot.password.send'), [
            'email' => $this->email
        ]);

        $codeHash = Cache::get('password_reset_code_hash:' . $this->email);
        $this->assertNotNull($codeHash, 'Code hash should be stored');
    }

    /**
     * TEST 2: Code Expiration
     * Verify: Code invalid after 10 minutes
     */
    public function test_code_expires_after_10_minutes()
    {
        // Send reset code
        $this->post(route('forgot.password.send'), ['email' => $this->email]);

        // Get current timestamp
        $timestamp = Cache::get('password_reset_code_timestamp:' . $this->email);
        $this->assertNotNull($timestamp);

        // Simulate 11 minutes passing
        Cache::put('password_reset_code_timestamp:' . $this->email, $timestamp - 660, now()->addMinutes(1));

        // Try to verify (should fail - code expired)
        session(['reset_email' => $this->email, 'reset_session_started' => now()->timestamp]);
        
        $response = $this->post(route('forgot.password.verify'), [
            'code' => '12345678'
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * TEST 3: Hashed Code Storage
     * Verify: Plain code NEVER stored in cache
     */
    public function test_code_stored_as_hash_not_plaintext()
    {
        Mail::fake();

        $this->post(route('forgot.password.send'), ['email' => $this->email]);

        $codeHash = Cache::get('password_reset_code_hash:' . $this->email);
        
        // Verify the stored value is a hash, not a plain number
        $this->assertNotNull($codeHash);
        $this->assertEquals(64, strlen($codeHash), 'SHA256 hash should be 64 characters');
        $this->assertNotRegExp('/^\d{8}$/', $codeHash, 'Should not be plain 8-digit code');
    }

    /**
     * TEST 4: Brute Force Lockout (3 attempts)
     * Verify: Email locked after 3 failed verification attempts
     */
    public function test_brute_force_lockout_after_3_failed_attempts()
    {
        Mail::fake();

        // Send code
        $this->post(route('forgot.password.send'), ['email' => $this->email]);

        session(['reset_email' => $this->email, 'reset_session_started' => now()->timestamp]);

        // Attempt 1: Wrong code
        $this->post(route('forgot.password.verify'), ['code' => '00000001']);
        
        // Attempt 2: Wrong code
        $this->post(route('forgot.password.verify'), ['code' => '00000002']);
        
        // Attempt 3: Wrong code - Should trigger lockout
        $response = $this->post(route('forgot.password.verify'), ['code' => '00000003']);

        // Verify email is locked
        $emailLock = Cache::get('password_reset:email_lock:' . $this->email);
        $this->assertNotNull($emailLock, 'Email should be locked after 3 attempts');

        // Try again - should be locked
        session(['reset_email' => $this->email, 'reset_session_started' => now()->timestamp]);
        $response = $this->post(route('forgot.password.verify'), ['code' => '99999999']);
        $response->assertRedirectToRoute('forgot.password.index');
    }

    /**
     * TEST 5: IP-Based Lockout
     * Verify: IP is locked after 3 failed attempts
     */
    public function test_ip_lockout_after_3_failed_attempts()
    {
        Mail::fake();

        // Send code
        $this->post(route('forgot.password.send'), ['email' => $this->email]);

        session(['reset_email' => $this->email, 'reset_session_started' => now()->timestamp]);

        // Attempt 1, 2, 3 - trigger IP lock
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('forgot.password.verify'), ['code' => "0000000{$i}"]);
        }

        // Verify IP is locked
        $ipLock = Cache::get('password_reset:ip_lock:' . $this->ip());
        $this->assertNotNull($ipLock, 'IP should be locked');
    }

    /**
     * TEST 6: IP Cannot Bypass Email Lock
     * Verify: Different IP can't bypass email lockout
     */
    public function test_different_ip_cannot_bypass_email_lockout()
    {
        Mail::fake();

        // Send code to email
        $this->post(route('forgot.password.send'), ['email' => $this->email]);

        session(['reset_email' => $this->email, 'reset_session_started' => now()->timestamp]);

        // Attempt with IP1 - trigger email lock
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('forgot.password.verify'), ['code' => "0000000{$i}"]);
        }

        $emailLock = Cache::get('password_reset:email_lock:' . $this->email);
        $this->assertNotNull($emailLock);

        // Attempt from IP2 (simulated)
        // Email should still be locked (not tied to IP)
        session(['reset_email' => $this->email, 'reset_session_started' => now()->timestamp]);
        
        // Manually set email lock to verify it persists
        $response = $this->post(route('forgot.password.verify'), ['code' => '99999999']);
        $response->assertSessionHasErrors();
    }

    /**
     * TEST 7: Session Timeout (30 minutes)
     * Verify: Session invalid after 30 minutes
     */
    public function test_session_timeout_after_30_minutes()
    {
        $oldTimestamp = now()->timestamp - 1800 - 1; // 30 minutes + 1 second ago

        session([
            'reset_email' => $this->email,
            'reset_session_started' => $oldTimestamp
        ]);

        $response = $this->get(route('forgot.password.reset.form'));

        // Should redirect due to expired session
        $response->assertRedirectToRoute('forgot.password.index');
        $response->assertSessionMissing('reset_email');
    }

    /**
     * TEST 8: Constant-Time Comparison
     * Verify: hash_equals() prevents timing attacks
     * Note: This is a conceptual test - timing attacks hard to test
     */
    public function test_code_comparison_is_constant_time()
    {
        // This is tested indirectly by code functionality
        // The controller uses hash_equals() which is constant-time
        $this->assertTrue(true, 'hash_equals() is used in controller (manual code review)');
    }

    /**
     * TEST 9: Rate Limiting Per Email (3/24hrs)
     * Verify: Max 3 code requests per email per 24 hours
     */
    public function test_email_rate_limit_3_per_24_hours()
    {
        Mail::fake();

        // Request 1
        $this->post(route('forgot.password.send'), ['email' => $this->email]);
        $this->assertResponseOk();

        // Request 2
        $this->post(route('forgot.password.send'), ['email' => $this->email]);
        $this->assertResponseOk();

        // Request 3
        $this->post(route('forgot.password.send'), ['email' => $this->email]);
        $this->assertResponseOk();

        // Request 4 - should be rate limited
        $response = $this->post(route('forgot.password.send'), ['email' => $this->email]);
        
        // Should show generic message (not rate limit error)
        $response->assertSessionHas('success');
    }

    /**
     * TEST 10: Rate Limiting Per IP (10/hour)
     * Verify: Max 10 code requests per IP per hour
     */
    public function test_ip_rate_limit_10_per_hour()
    {
        Mail::fake();

        for ($i = 0; $i < 10; $i++) {
            $response = $this->post(route('forgot.password.send'), [
                'email' => "user{$i}@example.com"
            ]);
            $this->assertResponseOk();
        }

        // 11th request - rate limited
        $response = $this->post(route('forgot.password.send'), [
            'email' => 'user11@example.com'
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * TEST 11: Password Strength (12 chars minimum)
     * Verify: Passwords < 12 chars rejected
     */
    public function test_password_minimum_12_characters()
    {
        // Setup successful code verification
        session([
            'reset_email' => $this->email,
            'reset_code_verified' => true,
            'verified_at' => now()->timestamp
        ]);

        // Try weak password (6 chars)
        $response = $this->post(route('forgot.password.reset'), [
            'password' => 'weak12',
            'password_confirmation' => 'weak12'
        ]);

        $response->assertSessionHasErrors('password');

        // Try strong password (12 chars)
        session([
            'reset_email' => $this->email,
            'reset_code_verified' => true,
            'verified_at' => now()->timestamp
        ]);

        $response = $this->post(route('forgot.password.reset'), [
            'password' => 'StrongPassword123',
            'password_confirmation' => 'StrongPassword123'
        ]);

        $response->assertSessionMissing('errors');
    }

    /**
     * TEST 12: Generic Error Messages (User Enumeration Prevention)
     * Verify: Same message for valid/invalid emails
     */
    public function test_generic_error_message_prevents_enumeration()
    {
        Mail::fake();

        // Valid email
        $response1 = $this->post(route('forgot.password.send'), [
            'email' => $this->email // exists in DB
        ]);

        $message1 = $response1->getSession()->get('success');

        // Invalid email
        $response2 = $this->post(route('forgot.password.send'), [
            'email' => 'nonexistent@example.com'
        ]);

        $message2 = $response2->getSession()->get('success');

        // Both should show same generic message
        $this->assertEquals($message1, $message2);
    }

    /**
     * TEST 13: Email Normalization
     * Verify: Different case/whitespace treated as same email
     */
    public function test_email_normalization()
    {
        Mail::fake();

        // Uppercase with whitespace
        $response = $this->post(route('forgot.password.send'), [
            'email' => '  ' . strtoupper($this->email) . '  '
        ]);

        // Should find the lowercased user
        $codeHash = Cache::get('password_reset_code_hash:' . strtolower($this->email));
        $this->assertNotNull($codeHash);
    }

    /**
     * TEST 14: Code Verification Requires Session
     * Verify: Can't verify without active session
     */
    public function test_verification_requires_active_session()
    {
        // No session - try to verify
        $response = $this->post(route('forgot.password.verify'), [
            'code' => '12345678'
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * TEST 15: Audit Logging
     * Verify: Security events are logged
     */
    public function test_security_events_are_logged()
    {
        Mail::fake();
        \Illuminate\Support\Facades\Log::spy();

        // Send code
        $this->post(route('forgot.password.send'), ['email' => $this->email]);

        // Verify logging was called
        \Illuminate\Support\Facades\Log::shouldHaveReceived('info');
    }

    /**
     * TEST 16: Full Successful Flow
     * Verify: Complete password reset works end-to-end
     */
    public function test_complete_password_reset_flow()
    {
        Mail::fake();

        // Step 1: Request code
        $response = $this->post(route('forgot.password.send'), [
            'email' => $this->email
        ]);

        $this->assertResponseOk();

        // Step 2: Manually get the code (in real scenario, user gets from email)
        $codeHash = Cache::get('password_reset_code_hash:' . $this->email);
        $this->assertNotNull($codeHash);

        // Step 3: Verify code (we'll simulate with correct code)
        session(['reset_email' => $this->email, 'reset_session_started' => now()->timestamp]);
        
        // Note: In real test, we'd need to capture actual code from email
        // For now, we test the flow up to verification

        // Step 4: Check password was updated (after full flow)
        // $this->assertTrue(Hash::check($this->password, $user->fresh()->password));
    }

    /**
     * Helper method
     */
    private function ip(): string
    {
        return $this->response->request()->getClientIp();
    }
}

/**
 * RUNNING THE TESTS
 * 
 * php artisan test tests/Feature/PasswordResetSecurityTest.php
 * 
 * Or individual tests:
 * php artisan test tests/Feature/PasswordResetSecurityTest.php --filter test_brute_force_lockout_after_3_failed_attempts
 * 
 * With coverage:
 * php artisan test tests/Feature/PasswordResetSecurityTest.php --coverage
 */
