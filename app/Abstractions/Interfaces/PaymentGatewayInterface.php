<?php

namespace App\Abstractions\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface PaymentGatewayInterface
{
    public function fetchKey(): string;

    public function handleWebhookPayload(array $payload = []): bool;

    public function queryAndVerifyPaymentTransaction(array $payment): bool;

    public function reachPaymentGatewayForVerification(string $url, string $reference_id, string $key): array;

}
