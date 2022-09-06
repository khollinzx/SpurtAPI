<?php

namespace App\Abstractions\Interfaces;

/**
 * This declares all the functions needed for all email providers
 */
interface EmailProviderInterface
{
    public function getKey(): string;

    public function initialize(array $data = []);

    public function sendMail(array $emailConfig, string $bladeTemplate, array $bladeData = [], string $subject = ''): bool;
}
