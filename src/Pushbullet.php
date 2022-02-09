<?php

namespace Pushbullet;

class Pushbullet
{
    private $accessToken;

    public function __construct(string $token)
    {
        $this->accessToken = $token;
    }

    private function sendRequest(string $url, array $data = []): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $headers = ['Access-Token: ' . $this->accessToken];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (!$response = curl_exec($ch)) {
            throw new \Exception(curl_error($ch));
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);
        curl_close($ch);

        return json_decode($body, true);
    }

    public function pushLink(string $title, string $body, string $url)
    {
        $this->sendRequest('https://api.pushbullet.com/v2/pushes', [
            'type' => 'link',
            'title' => $title,
            'body' => $body,
            'url' => $url
        ]);
    }

    public function pushNote(string $title, string $body)
    {
        $this->sendRequest('https://api.pushbullet.com/v2/pushes', [
            'type' => 'note',
            'title' => $title,
            'body' => $body
        ]);
    }
}
