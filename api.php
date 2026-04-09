<?php
// api.php
header('Content-Type: application/json');

// --- 1. MASUKKAN KEY KAMU DI SINI ---
$API_KEY_COID   = "xy9sQ9AcQwFZcNeiSTSrwZoEDGh9dsdW7e8IJ2GqAlgsb72oqX"; 
$API_KEY_GEMINI = "AIzaSyDoC3xsK5VY8lLKlWgGkCKW7rooQvylORo";

$bank    = $_GET['bank'] ?? '';
$account = $_GET['account'] ?? '';

if (empty($bank) || empty($account)) {
    echo json_encode(['status' => 'error', 'message' => 'Input tidak lengkap']);
    exit;
}

// --- 2. CEK DOKUMENTASI API.CO.ID ---
// Beberapa provider menggunakan endpoint berbeda. Coba cek dashboard kamu, 
// biasanya pilih salah satu dari ini:
// A. https://api.co.id/v1/check/bank
// B. https://api.co.id/api/v1/bank
$url_coid = "https://api.co.id/v1/check/bank?bank=$bank&account=$account";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url_coid);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Mematikan cek SSL jika di localhost
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $API_KEY_COID",
    "Accept: application/json"
]);

$response_coid = curl_exec($ch);
$httpCode = curl_getinfo($ch, HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// DEBUG: Jika ingin melihat mentahannya, aktifkan baris bawah ini:
// die($response_coid); 

if ($curlError) {
    echo json_encode(['status' => 'error', 'message' => 'CURL Error: ' . $curlError]);
    exit;
}

$data_coid = json_decode($response_coid, true);

// --- 3. LOGIKA PEMBACAAN HASIL ---
// Perhatikan struktur JSON dari api.co.id. 
// Kadang menggunakan 'status' => 'success', kadang 'status' => 200, atau 'status' => true
$isSuccess = false;
$nama_pemilik = "";

if (isset($data_coid['status']) && ($data_coid['status'] == "success" || $data_coid['status'] == 200 || $data_coid['status'] === true)) {
    $isSuccess = true;
    // Cek nama field-nya: apakah 'name', 'account_name', atau 'customer_name'?
    $nama_pemilik = $data_coid['data']['name'] ?? $data_coid['data']['account_name'] ?? "User Found";
}

// --- 4. ANALISIS AI (GEMINI) ---
$ai_message = "Gunakan transaksi aman.";
if ($isSuccess && !empty($API_KEY_GEMINI)) {
    $url_gemini = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $API_KEY_GEMINI;
    $prompt = ["contents" => [["parts" => [["text" => "Berikan tips singkat keamanan transfer ke $bank atas nama $nama_pemilik dalam 15 kata."]]]]];

    $ch_ai = curl_init($url_gemini);
    curl_setopt($ch_ai, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_ai, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_ai, CURLOPT_POST, true);
    curl_setopt($ch_ai, CURLOPT_POSTFIELDS, json_encode($prompt));
    curl_setopt($ch_ai, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response_ai = curl_exec($ch_ai);
    curl_close($ch_ai);
    
    $resAi = json_decode($response_ai, true);
    $ai_message = $resAi['candidates'][0]['content']['parts'][0]['text'] ?? "Rekening valid, silakan bertransaksi.";
}

echo json_encode([
    'debug_http_code' => $httpCode, // Untuk cek apakah koneksi sukses (200)
    'bank_data' => $data_coid,
    'is_success' => $isSuccess,
    'nama_pemilik' => $nama_pemilik,
    'ai_analysis' => $ai_message
]);
