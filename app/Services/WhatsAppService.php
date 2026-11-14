<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $phoneNumberId;
    protected $apiUrl;

    public function __construct()
    {
        try {
            $this->token = DB::table('settings')->where('key', 'whatsapp_token')->value('value');
            $this->phoneNumberId = DB::table('settings')->where('key', 'whatsapp_phone_number_id')->value('value');
            $this->apiUrl = rtrim((string) DB::table('settings')->where('key', 'whatsapp_api_url')->value('value'), '/');
        } catch (\Throwable $e) {}
    }

    protected function isConfigured(): bool
    {
        return !empty($this->token)
            && !empty($this->phoneNumberId)
            && !empty($this->apiUrl)
            && (str_starts_with($this->apiUrl, 'http://') || str_starts_with($this->apiUrl, 'https://'));
    }

    public function sendTemplateMessage($to, $templateName = "hello_world", $lang = "en_US")
    {
        if (!$this->isConfigured() || empty($to)) {
            return false;
        }

        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => ["code" => $lang]
            ]
        ];

        try {
            $response = Http::timeout(5)->withToken($this->token)->post($url, $payload);
            return $response->json();
        } catch (\Throwable $e) {
            Log::warning('WhatsAppService sendTemplateMessage error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendWelcomeTemplate($to, $fullname, $clinicName, $email, $signature)
    {
        if (!$this->isConfigured() || empty($to)) {
            return false;
        }

        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "template",
            "template" => [
                "name" => "welcome_clinic", 
                "language" => ["code" => "en_US"],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => (string) $fullname],
                            ["type" => "text", "text" => (string) $clinicName],
                            ["type" => "text", "text" => (string) $email],
                            ["type" => "text", "text" => (string) $signature],
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::timeout(5)->withToken($this->token)->post($url, $payload);
            return $response->json();
        } catch (\Throwable $e) {
            Log::warning('WhatsAppService sendWelcomeTemplate error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendTextMessage($to, $message)
    {
        if (!$this->isConfigured() || empty($to)) {
            return false;
        }

        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";

        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "text",
            "text" => [
                "preview_url" => false,
                "body" => $message
            ]
        ];

        try {
            $response = Http::timeout(5)->withToken($this->token)->post($url, $payload);
            return $response->json();
        } catch (\Throwable $e) {
            Log::warning('WhatsAppService sendTextMessage error: ' . $e->getMessage());
            return false;
        }
    }
}

