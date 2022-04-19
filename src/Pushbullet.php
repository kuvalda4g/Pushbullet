<?php

namespace Pushbullet;

class Pushbullet
{
    private $access_token;

    public function __construct(string $token)
    {
        $this->access_token = $token;
    }

    private function sendRequest(string $url, array $data = [], array $headers = []): array
    {
        $headers[] = 'Access-Token: ' . $this->access_token;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);

        $json = json_decode($response, true);
        $json['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $json;
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

    public function pushFile(string $filePath, $title = null, $body = null)
    {
        $response = $this->sendRequest('https://api.pushbullet.com/v2/upload-request', [
            'file_name' => basename($filePath),
            'file_type' => mime_content_type($filePath)
        ]);

        $response2 = $this->sendRequest(
            $response['upload_url'],
            ['file' => new \CURLFile($filePath)],
            ['Content-Type: multipart/form-data']
        );

        if ($response2['http_code'] !== 204) {
            throw new \Exception($response2);
        }

        $this->sendRequest('https://api.pushbullet.com/v2/pushes', [
            'type' => 'file',
            'title' => $title,
            'body' => $body,
            'file_name' => $response['file_name'],
            'file_type' => $response['file_type'],
            'file_url' => $response['file_url']
        ]);
    }
}
