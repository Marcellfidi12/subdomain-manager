<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubdomainController extends Controller
{
    // Menampilkan form input dan daftar subdomain
    public function index()
    {
        // Konfigurasi API Cloudflare
        $apiToken = env('CLOUDFLARE_API_TOKEN'); // Ambil API Token dari .env
        $zoneId = env('CLOUDFLARE_ZONE_ID'); // Ambil Zone ID dari .env

        // URL API Cloudflare untuk mendapatkan daftar DNS records
        $url = "https://api.cloudflare.com/client/v4/zones/$zoneId/dns_records";

        // Inisialisasi cURL untuk melakukan request HTTP
        $ch = curl_init($url);

        // Set opsi cURL untuk melakukan GET request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiToken",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Eksekusi request dan ambil respons
        $response = curl_exec($ch);

        // Cek apakah ada kesalahan dalam request
        if (curl_errno($ch)) {
            curl_close($ch);
            return redirect()->back()->with('error', 'Error: ' . curl_error($ch));
        }

        // Parse JSON response
        $result = json_decode($response, true);
        curl_close($ch);

        // Cek apakah sukses
        if ($result['success']) {
            // Ambil daftar DNS records yang jenisnya A
            $subdomains = array_filter($result['result'], function($record) {
                return $record['type'] == 'A'; // Pastikan hanya A records yang diambil
            });

            return view('subdomain.index', compact('subdomains'));
        } else {
            return redirect()->back()->with('error', "Gagal mengambil daftar subdomain. Error: " . $result['errors'][0]['message']);
        }
    }

    // Menangani penyimpanan data (mengirim ke API Cloudflare)
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'subdomain' => 'required|string|max:255',
            'ipAddress' => 'required|ip',
        ]);

        // Ambil nilai subdomain dan IP address dari form
        $subdomain = $validated['subdomain'];
        $ipAddress = $validated['ipAddress'];

        // Konfigurasi API Cloudflare
        $apiToken = env('CLOUDFLARE_API_TOKEN'); // Ambil API Token dari .env
        $zoneId = env('CLOUDFLARE_ZONE_ID'); // Ambil Zone ID dari .env

        // URL API Cloudflare untuk menambah DNS record
        $url = "https://api.cloudflare.com/client/v4/zones/$zoneId/dns_records";

        // Data yang akan dikirim ke API untuk menambah DNS record
        $data = [
            'type' => 'A',
            'name' => $subdomain,
            'content' => $ipAddress,
            'ttl' => 3600,
            'proxied' => false,
        ];

        // Inisialisasi cURL untuk melakukan request HTTP
        $ch = curl_init($url);

        // Set opsi cURL untuk melakukan POST request dengan data JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiToken",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Eksekusi request dan ambil respons
        $response = curl_exec($ch);

        // Cek apakah ada kesalahan dalam request
        if (curl_errno($ch)) {
            return redirect()->back()->with('error', 'Error: ' . curl_error($ch));
        } else {
            $result = json_decode($response, true);

            // Menampilkan hasil
            if ($result['success']) {
                return redirect()->route('subdomain.index')->with('success', "Subdomain $subdomain berhasil ditambahkan!");
            } else {
                return redirect()->back()->with('error', "Gagal menambahkan subdomain. Error: " . $result['errors'][0]['message']);
            }
        }

        // Tutup cURL session
        curl_close($ch);
    }

    // Menampilkan halaman edit untuk subdomain
    public function edit($recordId)
    {
        // Konfigurasi API Cloudflare
        $apiToken = env('CLOUDFLARE_API_TOKEN'); // Ambil API Token dari .env
        $zoneId = env('CLOUDFLARE_ZONE_ID'); // Ambil Zone ID dari .env

        // URL API Cloudflare untuk mendapatkan DNS record spesifik
        $url = "https://api.cloudflare.com/client/v4/zones/$zoneId/dns_records/$recordId";

        // Inisialisasi cURL untuk melakukan request HTTP
        $ch = curl_init($url);

        // Set opsi cURL untuk melakukan GET request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiToken",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Eksekusi request dan ambil respons
        $response = curl_exec($ch);

        // Cek apakah ada kesalahan dalam request
        if (curl_errno($ch)) {
            curl_close($ch);
            return redirect()->back()->with('error', 'Error: ' . curl_error($ch));
        }

        // Parse JSON response
        $result = json_decode($response, true);
        curl_close($ch);

        // Cek apakah sukses
        if ($result['success']) {
            $subdomain = $result['result'];
            return view('subdomain.edit', compact('subdomain'));
        } else {
            return redirect()->back()->with('error', "Gagal mengambil data subdomain. Error: " . $result['errors'][0]['message']);
        }
    }

    // Menangani update IP address subdomain
    public function update(Request $request, $recordId)
    {
        // Validasi input
        $validated = $request->validate([
            'ipAddress' => 'required|ip',
        ]);

        // Ambil nilai IP address dari form
        $ipAddress = $validated['ipAddress'];

        // Konfigurasi API Cloudflare
        $apiToken = env('CLOUDFLARE_API_TOKEN'); // Ambil API Token dari .env
        $zoneId = env('CLOUDFLARE_ZONE_ID'); // Ambil Zone ID dari .env

        // URL API Cloudflare untuk update DNS record
        $url = "https://api.cloudflare.com/client/v4/zones/$zoneId/dns_records/$recordId";

        // Data yang akan dikirim ke API untuk update DNS record
        $data = [
            'type' => 'A',
            'content' => $ipAddress,
            'ttl' => 3600,
            'proxied' => false,
        ];

        // Inisialisasi cURL untuk melakukan request HTTP
        $ch = curl_init($url);

        // Set opsi cURL untuk melakukan PUT request dengan data JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiToken",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Eksekusi request dan ambil respons
        $response = curl_exec($ch);

        // Cek apakah ada kesalahan dalam request
        if (curl_errno($ch)) {
            curl_close($ch);
            return redirect()->back()->with('error', 'Error: ' . curl_error($ch));
        }

        // Parse JSON response
        $result = json_decode($response, true);
        curl_close($ch);

        // Menampilkan hasil
        if ($result['success']) {
            return redirect()->route('subdomain.index')->with('success', "IP address subdomain berhasil diupdate!");
        } else {
            return redirect()->back()->with('error', "Gagal mengupdate subdomain. Error: " . $result['errors'][0]['message']);
        }
    }

    // Menghapus subdomain dari Cloudflare
    public function destroy($recordId)
    {
        // Konfigurasi API Cloudflare
        $apiToken = env('CLOUDFLARE_API_TOKEN'); // Ambil API Token dari .env
        $zoneId = env('CLOUDFLARE_ZONE_ID'); // Ambil Zone ID dari .env

        // URL API Cloudflare untuk menghapus DNS record
        $url = "https://api.cloudflare.com/client/v4/zones/$zoneId/dns_records/$recordId";

        // Inisialisasi cURL untuk melakukan request HTTP
        $ch = curl_init($url);

        // Set opsi cURL untuk melakukan DELETE request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $apiToken",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        // Eksekusi request dan ambil respons
        $response = curl_exec($ch);

        // Cek apakah ada kesalahan dalam request
        if (curl_errno($ch)) {
            curl_close($ch);
            return redirect()->back()->with('error', 'Error: ' . curl_error($ch));
        }

        // Parse JSON response
        $result = json_decode($response, true);
        curl_close($ch);

        // Menampilkan hasil
        if ($result['success']) {
            return redirect()->route('subdomain.index')->with('success', "Subdomain berhasil dihapus!");
        } else {
            return redirect()->back()->with('error', "Gagal menghapus subdomain. Error: " . $result['errors'][0]['message']);
        }
    }
}
