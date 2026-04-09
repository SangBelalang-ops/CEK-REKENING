const API_URL = "https://api-rekening.lfourr.com/getBankAccount";
const API_KEY = "aFVBKbUGW15JPwN3to5L7DiyN0QY1syLI4p4jVQ5N0VnsgYHRs"; // Tempel Key dari api.co.id / GitHub

async function cekRekening() {
    const bank = document.getElementById('bankCode').value;
    const norek = document.getElementById('accNumber').value;
    const hasil = document.getElementById('result');

    hasil.innerText = "Checking...";

    try {
        const response = await fetch(`${API_URL}?bankCode=${bank}&accountNumber=${norek}`, {
            headers: { 'x-api-key': API_KEY }
        });
        
        const res = await response.json();

        // Logika: Kalau sukses tampilin nama, kalau gagal tampilin pesan error
        if (res.status === true || res.status === "success") {
            hasil.innerHTML = `NAMA: <b style="color: #00ff00">${res.data.name || res.data.account_name}</b>`;
        } else {
            hasil.innerText = "Data tidak ditemukan!";
        }
    } catch (err) {
        hasil.innerText = "Server API Error!";
    }
}
