const API_BASE = "https://api-rekening.lfourr.com";
const API_KEY = "aFVBKbUGW15JPwN3to5L7DiyN0QY1syLI4p4jVQ5N0VnsgYHRs";

async function cekRekening() {
    const bank = document.getElementById('bankCode').value;
    const norek = document.getElementById('accNumber').value;
    const hasil = document.getElementById('result');

    if (!bank || !norek) return alert("Pilih Bank/E-Wallet & Isi Nomor!");

    hasil.innerText = "Checking...";

    // --- BAGIAN UPDATE-NYA DI SINI ---
    // Kita cek dulu, yang dipilih itu E-Wallet atau Bank?
    // Di dropdown, E-wallet biasanya kodenya kayak 'dana', 'ovo', 'gopay'
    const isEwallet = ["dana", "ovo", "gopay", "linkaja", "shopeepay"].includes(bank.toLowerCase());
    
    // Tentukan URL berdasarkan jenisnya
    const endpoint = isEwallet ? "getEwalletAccount" : "getBankAccount";
    const finalUrl = `${API_BASE}/${endpoint}?bankCode=${bank}&accountNumber=${norek}`;

    try {
        const response = await fetch(finalUrl, {
            headers: { 'x-api-key': API_KEY }
        });
        
        const res = await response.json();

        if (res.status === true || res.status === "success") {
            // Kadang API e-wallet strukturnya agak beda, kita handle dua-duanya
            const nama = res.data.name || res.data.account_name || "Nama tidak terbaca";
            hasil.innerHTML = `NAMA: <b style="color: #00ff00">${nama.toUpperCase()}</b>`;
        } else {
            hasil.innerText = "Data tidak ditemukan! Cek nomor lagi.";
        }
    } catch (err) {
        hasil.innerText = "Server Error! Coba lagi nanti.";
    }
}
