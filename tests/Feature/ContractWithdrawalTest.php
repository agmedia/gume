<?php

namespace Tests\Feature;

use App\Helpers\Helper;
use App\Mail\ContractWithdrawalAdminMail;
use App\Mail\ContractWithdrawalReceiptMail;
use App\Models\ContractWithdrawal;
use App\Models\User;
use App\Services\ContractWithdrawalSettingsService;
use Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContractWithdrawalTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'array',
            'services.recaptcha.sitekey' => '',
            'services.recaptcha.secret' => '',
            'mail.admin' => 'raskidi@example.test',
        ]);

        Helper::flushCache('settings', 'storecontract_withdrawal');
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    protected function tearDown(): void
    {
        Helper::flushCache('settings', 'storecontract_withdrawal');

        parent::tearDown();
    }

    public function test_public_form_uses_pneumax_design_and_cookie_controls(): void
    {
        $response = $this->get(route('contract-withdrawal.create'));

        $response->assertOk()
            ->assertSee('Obrazac za jednostrani raskid ugovora')
            ->assertSee('PNEU-MAX j.d.o.o., Severinska 4, 10000 Zagreb')
            ->assertSee('Raskid ugovora')
            ->assertSee('14 dana')
            ->assertSee('cookie-consent-trigger--floating', false)
            ->assertSee('fa-cookie-bite', false)
            ->assertSee(asset('assets/vendor/fontawesome-pro/css/fontawesome.min.css'), false)
            ->assertDontSee('kit.fontawesome.com', false)
            ->assertSee(asset('assets/img/payment-methods/mastercard.svg'), false);
    }

    public function test_withdrawal_is_reviewed_stored_and_emailed_to_both_parties(): void
    {
        Mail::fake();

        app(ContractWithdrawalSettingsService::class)->save([
            'admin_email' => 'raskidi@example.test',
            'return_address' => 'PNEU-MAX j.d.o.o., Severinska 4, 10000 Zagreb',
            'return_cost_policy' => 'consumer',
            'instructions' => 'U paket priložite broj narudžbe ili referencu zahtjeva.',
        ]);

        $review = $this->post(route('contract-withdrawal.review'), $this->validPayload());

        $review->assertOk()
            ->assertSee('Pregledajte izjavu o raskidu')
            ->assertSee('Potvrditi raskid ugovora')
            ->assertSee('Ovime nedvosmisleno izjavljujem');

        preg_match('/name="draft_token" value="([^"]+)"/', $review->getContent(), $matches);
        $this->assertNotEmpty($matches[1] ?? null);

        $store = $this->post(route('contract-withdrawal.store'), [
            'draft_token' => $matches[1],
        ]);

        $store->assertRedirect(route('contract-withdrawal.create'))
            ->assertSessionHas('success')
            ->assertSessionHas('withdrawal_reference');

        $withdrawal = ContractWithdrawal::query()->latest('id')->firstOrFail();

        $this->assertSame('PM-TEST-1001', $withdrawal->order_number);
        $this->assertSame(ContractWithdrawal::STATUS_RECEIVED, $withdrawal->status);
        $this->assertNotNull($withdrawal->consumer_notified_at);
        $this->assertNotNull($withdrawal->admin_notified_at);
        $this->assertNull($withdrawal->notification_error);
        $this->assertSame(
            ContractWithdrawal::snapshotHash($withdrawal->request_snapshot),
            $withdrawal->snapshot_hash
        );

        Mail::assertSent(ContractWithdrawalReceiptMail::class, function ($mail) use ($withdrawal) {
            return $mail->hasTo('kupac@example.test') && $mail->withdrawal->is($withdrawal);
        });

        Mail::assertSent(ContractWithdrawalAdminMail::class, function ($mail) use ($withdrawal) {
            return $mail->hasTo('raskidi@example.test') && $mail->withdrawal->is($withdrawal);
        });
    }

    public function test_invalid_submission_does_not_create_withdrawal(): void
    {
        $before = ContractWithdrawal::query()->count();

        $this->from(route('contract-withdrawal.create'))
            ->post(route('contract-withdrawal.review'), [
                'full_name' => '',
                'email' => 'nije-email',
            ])
            ->assertRedirect(route('contract-withdrawal.create'))
            ->assertSessionHasErrors(['full_name', 'email', 'address_line', 'order_number', 'items']);

        $this->assertSame($before, ContractWithdrawal::query()->count());
    }

    public function test_admin_can_open_settings_and_process_a_withdrawal(): void
    {
        $admin = User::factory()->create();
        Bouncer::allow($admin)->everything();
        $withdrawal = $this->makeWithdrawal();

        $this->actingAs($admin)
            ->get(route('contract-withdrawals.index'))
            ->assertOk()
            ->assertSee($withdrawal->reference);

        $this->actingAs($admin)
            ->get(route('contract-withdrawals.show', $withdrawal))
            ->assertOk()
            ->assertSee($withdrawal->declaration);

        $this->actingAs($admin)
            ->get(route('contract-withdrawal-settings.edit'))
            ->assertOk()
            ->assertSee('PNEU-MAX j.d.o.o., Severinska 4, 10000 Zagreb');

        $this->actingAs($admin)
            ->patch(route('contract-withdrawals.update', $withdrawal), [
                'status' => ContractWithdrawal::STATUS_COMPLETED,
                'internal_note' => 'Povrat je obrađen.',
            ])
            ->assertRedirect(route('contract-withdrawals.show', $withdrawal));

        $withdrawal->refresh();
        $this->assertSame(ContractWithdrawal::STATUS_COMPLETED, $withdrawal->status);
        $this->assertSame('Povrat je obrađen.', $withdrawal->internal_note);
        $this->assertSame($admin->id, $withdrawal->handled_by);
        $this->assertNotNull($withdrawal->completed_at);
    }

    private function validPayload(): array
    {
        return [
            'full_name' => 'Ana Kupac',
            'email' => 'kupac@example.test',
            'phone' => '+385 91 123 4567',
            'address_line' => 'Testna 12',
            'postal_code' => '10000',
            'city' => 'Zagreb',
            'country_code' => 'HR',
            'order_number' => 'PM-TEST-1001',
            'contract_date' => '2026-08-01',
            'received_date' => '2026-08-03',
            'items' => '4 auto gume',
            'note' => '',
            'recaptcha' => '',
        ];
    }

    private function makeWithdrawal(): ContractWithdrawal
    {
        $snapshot = [
            'version' => '2026-08-07',
            'submitted_at' => now()->toIso8601String(),
            'confirmation_channel' => 'email',
            'data' => $this->validPayload(),
            'declaration' => 'Ovime raskidam ugovor PM-TEST-1001.',
        ];

        return ContractWithdrawal::query()->create([
            'reference' => 'JR-20260807-ABC123',
            'submission_key' => hash('sha256', 'test-submission'),
            'order_number' => 'PM-TEST-1001',
            'full_name' => 'Ana Kupac',
            'email' => 'kupac@example.test',
            'phone' => '+385 91 123 4567',
            'address_line' => 'Testna 12',
            'postal_code' => '10000',
            'city' => 'Zagreb',
            'country_code' => 'HR',
            'contract_date' => '2026-08-01',
            'received_date' => '2026-08-03',
            'items' => '4 auto gume',
            'declaration' => 'Ovime raskidam ugovor PM-TEST-1001.',
            'request_snapshot' => $snapshot,
            'snapshot_hash' => ContractWithdrawal::snapshotHash($snapshot),
            'status' => ContractWithdrawal::STATUS_RECEIVED,
            'locale' => 'hr',
            'submitted_at' => now(),
        ]);
    }
}
