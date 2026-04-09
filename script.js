// Tes pake URL paling dasar sesuai dokumentasi terbaru mereka
const testUrl = `https://use.api.co.id/v1/bank/inquiry?bank_code=${bank}&account_number=${norek}`;

const response = await fetch(testUrl, {
    method: 'GET',
    headers: {
        'x-api-co-id': 'aFVBKbUGW15JPwN3to5L7DiyN0QY1syLI4p4jVQ5N0VnsgYHRs',
        'Content-Type': 'application/json'
    }
});
