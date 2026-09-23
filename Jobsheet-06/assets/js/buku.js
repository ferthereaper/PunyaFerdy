// Mengambil & menampilkan Daftar Buku secara asinkron dari data/buku.json
async function muatDaftarBuku() {
    const tbody = document.querySelector(".table-responsive table tbody");
    const loading = document.getElementById("loading-indicator");
    if (!tbody) return;

    loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        // simulasi delay jaringan agar loading indicator terlihat
        await new Promise((resolve) => setTimeout(resolve, 600));

        const res = await fetch("../data/buku.json");
        if (!res.ok) {
            throw new Error("Gagal mengambil data (status " + res.status + ")");
        }
        const daftarBuku = await res.json();

        daftarBuku.forEach(function (buku) {
            const tr = document.createElement("tr");
            tr.innerHTML =
                "<td>" + buku.judul + "</td>" +
                "<td>" + buku.pengarang + "</td>" +
                "<td>" + buku.tahun + "</td>" +
                "<td>" + buku.stok + "</td>" +
                "<td>" +
                "<button type=\"button\">Edit</button> " +
                "<button type=\"button\" class=\"btn-hapus\">Hapus</button>" +
                "</td>";
            tbody.appendChild(tr);
        });
    } catch (err) {
        tbody.innerHTML =
            "<tr><td colspan=\"5\">Gagal memuat data: " + err.message + "</td></tr>";
    } finally {
        loading.style.display = "none";
    }
}

// JS 6 latihan 1
const btnMuatUlang = document.getElementById("btn-muat-ulang");
if (btnMuatUlang) {
    btnMuatUlang.addEventListener("click", function() {
        muatDaftarBuku();
    });
}

document.addEventListener("DOMContentLoaded", muatDaftarBuku);

// JS 6 Latihan 2
muatDataTabel("../data/buku.json", "#tabel-buku tbody", function(data, tbody) {
    data.forEach(function(item) {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${item.judul}</td>
            <td>${item.pengarang}</td>
            <td>${item.tahun}</td>
            <td>${item.stok}</td>
            <td>${item.kategori}</td>
            <td>
                <button class="button-edit">Edit</button>
                <button class="button-hapus">Hapus</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
});